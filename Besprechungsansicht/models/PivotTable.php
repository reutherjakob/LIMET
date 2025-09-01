<?php
// models/PivotTable.php

class PivotTable
{
    private \mysqli $conn;
    private int $projectID;

    public function __construct(\mysqli $conn, int $projectID)
    {
        $this->conn = $conn;
        $this->projectID = $projectID;
    }

    /**
     * Returns the HTML for the Elemente-je-Raeume Pivot Table with variants and summations
     *
     * @param array $raumbereiche Array of raumbereich strings/filter text
     * @param array $zusatzRaeume Additional room IDs (ints)
     * @param array $zusatzElemente Additional element IDs (ints)
     * @param bool $mtRelevant Filter MT-relevant
     * @param bool $entfallen Filter out "entfallen"
     * @param bool $nurMitElementen Only show rooms with elements
     * @param bool $ohneLeere Filter out elements with zero total
     * @param bool $transponiert Whether to transpose rows/columns
     * @return string HTML of pivot table
     * @throws Exception on SQL errors
     */
    public function getElementeJeRaeume(
        array $raumbereiche,
        array $zusatzRaeume,
        array $zusatzElemente,
        bool  $mtRelevant,
        bool  $entfallen,
        bool  $nurMitElementen,
        bool  $ohneLeere,
        bool  $transponiert
    ): string
    {
        $conn = $this->conn;
        $projectID = $this->projectID;

        // === 1. Load rooms with filters ===
        $rooms = [];
        if (!empty($raumbereiche)) {
            $placeholders = implode(',', array_fill(0, count($raumbereiche), '?'));
            $sql = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung 
                    FROM tabelle_räume 
                    WHERE tabelle_projekte_idTABELLE_Projekte = ? 
                      AND `Raumbereich Nutzer` IN ($placeholders)";
            if ($mtRelevant) $sql .= " AND `MT-relevant`=1";
            if ($entfallen) $sql .= " AND (Entfallen IS NULL OR Entfallen=0)";
            $sql .= " ORDER BY Raumnr";

            $params = array_merge([$projectID], $raumbereiche);
            $types = 'i' . str_repeat('s', count($raumbereiche));
            $stmt = $conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed rooms: " . $conn->error);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) {
                $rooms[$r['idTABELLE_Räume']] = $r['Raumnr'] . ' ' . $r['Raumbezeichnung'];
            }
            $stmt->close();
        }
        // Add additional rooms by IDs
        if (!empty($zusatzRaeume)) {
            $toAdd = array_diff($zusatzRaeume, array_keys($rooms));
            if (!empty($toAdd)) {
                $placeholders = implode(',', array_fill(0, count($toAdd), '?'));
                $sql = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung FROM tabelle_räume WHERE idTABELLE_Räume IN ($placeholders)";
                if ($mtRelevant) $sql .= " AND `MT-relevant`=1";
                if ($entfallen) $sql .= " AND (Entfallen IS NULL OR Entfallen=0)";
                $stmt = $conn->prepare($sql);
                if (!$stmt) throw new Exception("Prepare failed rooms add: " . $conn->error);
                $types = str_repeat('i', count($toAdd));
                $stmt->bind_param($types, ...$toAdd);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($r = $res->fetch_assoc()) {
                    $rooms[$r['idTABELLE_Räume']] = $r['Raumnr'] . ' ' . $r['Raumbezeichnung'];
                }
                $stmt->close();
            }
        }
        // Filter rooms by element presence if requested
        if ($nurMitElementen) {
            $sql = "SELECT DISTINCT TABELLE_Räume_idTABELLE_Räume FROM tabelle_räume_has_tabelle_elemente WHERE Standort=1";
            $res = $conn->query($sql);
            $validRooms = [];
            while ($row = $res->fetch_assoc()) {
                $validRooms[] = $row['TABELLE_Räume_idTABELLE_Räume'];
            }
            $rooms = array_filter($rooms, fn($k) => in_array($k, $validRooms), ARRAY_FILTER_USE_KEY);
        }

        if (empty($rooms)) {
            return '<div class="alert alert-info">Keine Räume gefunden</div>';
        }

        $roomIds = array_keys($rooms);
        $roomPlaceholders = implode(',', array_fill(0, count($roomIds), '?'));

        // === 2. Find element+variant combos assigned to these rooms ===
        $sqlElem = "SELECT DISTINCT e.idTABELLE_Elemente, re.tabelle_Varianten_idtabelle_Varianten
                    FROM tabelle_elemente e
                    JOIN tabelle_räume_has_tabelle_elemente re ON e.idTABELLE_Elemente=re.TABELLE_Elemente_idTABELLE_Elemente
                    JOIN tabelle_räume r ON re.TABELLE_Räume_idTABELLE_Räume=r.idTABELLE_Räume
                    WHERE r.tabelle_projekte_idTABELLE_Projekte=?
                      AND re.Standort=1
                      AND r.idTABELLE_Räume IN ($roomPlaceholders)";

        $params = array_merge([$projectID], $roomIds);
        $types = 'i' . str_repeat('i', count($roomIds));
        $stmt = $conn->prepare($sqlElem);
        if (!$stmt) throw new Exception("Prepare failed elem lookup: " . $conn->error);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $elementVariants = [];
        while ($row = $res->fetch_assoc()) {
            $elementVariants[] = [
                'elementId' => (int)$row['idTABELLE_Elemente'],
                'variantId' => $row['tabelle_Varianten_idtabelle_Varianten'] === null ? 0 : (int)$row['tabelle_Varianten_idtabelle_Varianten']
            ];
        }
        // file_put_contents(__DIR__ . '/pivot_debug.log', print_r($elementVariants, true) );
        $stmt->close();

        // Add additional elements without variant
        foreach ($zusatzElemente as $ae) {
            $elementVariants[] = ['elementId' => (int)$ae, 'variantId' => 0];
        }

        // file_put_contents(__DIR__ . '/pivot_debug.log', print_r($elementVariants, true), FILE_APPEND );
        // Build filter for element + variant combos
        $filters = [];
        foreach ($elementVariants as $ev) {
            $e = $ev['elementId'];
            $v = $ev['variantId'];
            if ($v === 0) {
                $filters[] = "(e.idTABELLE_Elemente=$e)";
            } else {
                $filters[] = "(e.idTABELLE_Elemente=$e AND (re.tabelle_Varianten_idtabelle_Varianten=$v OR re.tabelle_Varianten_idtabelle_Varianten IS NULL))";
            }
        }
        $elemFilter = $filters ? "AND (" . implode(' OR ', $filters) . ")" : '';
        // === 3. Query sums per element+variant per room + relation ID ===
        $sqlPivot = "SELECT 
                        re.id AS relationId,
                        re.status,
                        e.idTABELLE_Elemente AS elementId,
                        re.tabelle_Varianten_idtabelle_Varianten AS variantId,
                        e.ElementID,
                        e.Bezeichnung,
                        COALESCE(v.Variante, 'A') AS variantName,
                        r.idTABELLE_Räume AS roomId,
                        SUM(re.Anzahl) AS total
                    FROM tabelle_elemente e
                    JOIN tabelle_räume_has_tabelle_elemente re ON e.idTABELLE_Elemente=re.TABELLE_Elemente_idTABELLE_Elemente
                    JOIN tabelle_räume r ON re.TABELLE_Räume_idTABELLE_Räume=r.idTABELLE_Räume
                    LEFT JOIN tabelle_varianten v ON re.tabelle_Varianten_idtabelle_Varianten=v.idtabelle_Varianten
                    WHERE r.tabelle_projekte_idTABELLE_Projekte=?
                      AND re.Standort=1
                      AND r.idTABELLE_Räume IN ($roomPlaceholders)
                      $elemFilter
                    GROUP BY e.idTABELLE_Elemente, re.tabelle_Varianten_idtabelle_Varianten, r.idTABELLE_Räume
                    ORDER BY e.ElementID, variantName ASC, r.Raumnr";

        $params = array_merge([$projectID], $roomIds);
        $types = 'i' . str_repeat('i', count($roomIds));
        $stmt = $conn->prepare($sqlPivot);
        if (!$stmt) throw new Exception("Prepare failed pivot: " . $conn->error);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();

        // file_put_contents(__DIR__ . '/pivot_debug.log', print_r($sqlPivot, true),FILE_APPEND );
        // file_put_contents(__DIR__ . '/pivot_debug.log', print_r($res, true),FILE_APPEND );

        // Aggregate pivot data
        $pivot = [];
        while ($row = $res->fetch_assoc()) {
            $key = $row['elementId'] . '_' . ($row['variantId'] ?? 0);
            if (!isset($pivot[$key])) {
                $label = $row['ElementID'] . ' ' . $row['Bezeichnung'] . ' (' . ($row['variantName'] ?: '-') . ')';
                $pivot[$key] = [
                    'label' => $label,
                    'elementId' => (int)$row['elementId'],
                    'variantId' => $row['variantId'] ?? 0,
                    'values' => array_fill_keys($roomIds, 0),
                    'relationIds' => []
                ];
            }
            $pivot[$key]['values'][(int)$row['roomId']] = (int)$row['total'];
            $pivot[$key]['relationIds'][(int)$row['roomId']] = $row['relationId']; // include relation ID per room+element
            $pivot[$key]['statuses'][(int)$row['roomId']] = $row['status'];

        }
        $stmt->close();

        // file_put_contents(__DIR__ . '/pivot_debug.log', print_r($elementVariants, true), FILE_APPEND);
        // Add missing elements from $elementVariants with zeros
        foreach ($elementVariants as $ev) {
            $key = $ev['elementId'] . '_' . $ev['variantId'];
            if (!isset($pivot[$key])) {
                $stmtE = $conn->prepare("SELECT ElementID, Bezeichnung FROM tabelle_elemente WHERE idTABELLE_Elemente=?");
                $stmtE->bind_param('i', $ev['elementId']);
                $stmtE->execute();
                $elem = $stmtE->get_result()->fetch_assoc() ?: null;
                $stmtE->close();

                $variantName = '-';
                if ($ev['variantId'] && $ev['variantId'] !== 0) {
                    $stmtV = $conn->prepare("SELECT Variante FROM tabelle_varianten WHERE idtabelle_Varianten=?");
                    $stmtV->bind_param('i', $ev['variantId']);
                    $stmtV->execute();
                    $vres = $stmtV->get_result()->fetch_assoc() ?: null;
                    $stmtV->close();
                    if ($vres && !empty($vres['Variante'])) {
                        $variantName = $vres['Variante'];
                    }
                }

                $label = ($elem ? $elem['ElementID'] . ' ' . $elem['Bezeichnung'] : 'Unbekannt') . ' (' . $variantName . ')';

                $pivot[$key] = [
                    'label' => $label,
                    'elementId' => $ev['elementId'],
                    'variantId' => $ev['variantId'],
                    'values' => array_fill_keys($roomIds, 0),
                    'relationIds' => []
                ];
            }
        }

        // Filter elements with zero sums if needed
        if ($ohneLeere) {
            foreach ($pivot as $key => $data) {
                if (array_sum($data['values']) === 0) {
                    unset($pivot[$key]);
                }
            }
        }
        // file_put_contents(__DIR__ . '/pivot_debug.log', print_r($pivot, true), FILE_APPEND);

        // === 4. Generate HTML ===
        ob_start();
        echo '<table id="pivotTable" class="table table-bordered table-sm table-striped">';

        // Header
        echo '<thead><tr><th>Element (Variante)</th><th>Summe</th>';
        foreach ($rooms as $rId => $rLabel) {
            echo '<th>' . htmlspecialchars($rLabel) . '</th>';
        }
        echo '</tr></thead><tbody>';

        foreach ($pivot as $key => $data) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($data['label']) . '</td>';
            $total = array_sum($data['values']);
            echo '<td>' . $total . '</td>';
            foreach ($rooms as $rId => $rLabel) {
                $val = $data['values'][$rId] ?? 0;
                $relId = $data['relationIds'][$rId] ?? '';
                $status = $data['statuses'][$rId] ?? null;
                file_put_contents(__DIR__ . '/pivot_debug.log', print_r($status, true), FILE_APPEND);

                $class = 'editable-cell';
                switch ($status) {
                    case 1:
                        $class .= ' status-green';
                        break;
                    case 2:
                        $class .= ' status-blue';
                        break;
                    case 3:
                        $class .= ' status-yellow';
                        break;
                    case 4:
                        $class .= ' status-red';
                        break;
                    default:
                        // keine zusätzliche Klasse, Standard-Ausgabe
                }
                echo '<td class="' . $class . '" tabindex="0" ' .
                    'data-room-id="' . htmlspecialchars($rId) . '" ' .
                    'data-element-id="' . htmlspecialchars($data['elementId']) . '" ' .
                    'data-variant-id="' . htmlspecialchars($data['variantId'] ?? '1') . '" ' .
                    'data-relation-id="' . htmlspecialchars($data['relationIds'][$rId] ?? '0') . '">' .
                    htmlspecialchars($val) .
                    '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody></table>';

        return ob_get_clean();
    }
}
