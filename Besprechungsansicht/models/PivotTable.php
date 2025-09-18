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

        // === 2. Find element+variant+bestand combos assigned to these rooms ===
        $sqlElem = "SELECT  e.idTABELLE_Elemente, re.tabelle_Varianten_idtabelle_Varianten, re.`Neu/Bestand` as Bestand 
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
                'variantId' => $row['tabelle_Varianten_idtabelle_Varianten'] === null ? 0 : (int)$row['tabelle_Varianten_idtabelle_Varianten'],
                'Bestand' => $row['Bestand']
            ];
        }
        $stmt->close();

        foreach ($zusatzElemente as $ae) {
            $elementVariants[] = ['elementId' => (int)$ae, 'variantId' => 0, 'Bestand' => 1];
        }

        $filters = [];
        foreach ($elementVariants as $ev) {
            $e = $ev['elementId'];
            $v = $ev['variantId'];
            $bestand = $ev['Bestand'];

            if ($v === 0) {
                $filters[] = "(e.idTABELLE_Elemente=$e AND re.`Neu/Bestand` = $bestand)";
            } else {
                $filters[] = "(e.idTABELLE_Elemente=$e AND (re.tabelle_Varianten_idtabelle_Varianten=$v OR re.tabelle_Varianten_idtabelle_Varianten IS NULL) AND re.`Neu/Bestand` = $bestand)";
            }
        }

        $elemFilter = $filters ? "AND (" . implode(' OR ', $filters) . ")" : '';
        // === 3. Query all entry rows (no aggregation) ===
        $sqlPivot = "SELECT 
                    re.id AS relationId,
                    re.status,
                    re.`Neu/Bestand` as Bestand,
                    e.idTABELLE_Elemente AS elementId,
                    re.tabelle_Varianten_idtabelle_Varianten AS variantId,
                    e.ElementID,
                    e.Bezeichnung,
                    COALESCE(v.Variante, 'A') AS variantName,
                    r.idTABELLE_Räume AS roomId,
                    re.Anzahl AS total
                FROM tabelle_elemente e
                JOIN tabelle_räume_has_tabelle_elemente re ON e.idTABELLE_Elemente=re.TABELLE_Elemente_idTABELLE_Elemente
                JOIN tabelle_räume r ON re.TABELLE_Räume_idTABELLE_Räume=r.idTABELLE_Räume
                LEFT JOIN tabelle_varianten v ON re.tabelle_Varianten_idtabelle_Varianten=v.idtabelle_Varianten
                WHERE r.tabelle_projekte_idTABELLE_Projekte=?
                  AND re.Standort=1
                  AND r.idTABELLE_Räume IN ($roomPlaceholders)
                  $elemFilter
                ORDER BY e.ElementID, variantName ASC, r.Raumnr";

        $params = array_merge([$projectID], $roomIds);
        $types = 'i' . str_repeat('i', count($roomIds));
        $stmt = $conn->prepare($sqlPivot);
        if (!$stmt) throw new Exception("Prepare failed pivot: " . $conn->error);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();

        // Step 1: Group entries by element+variant+bestand+room
        $groupsByRoom = [];
        while ($row = $res->fetch_assoc()) {
            $key = $row['elementId'] . '_' . $row['variantId'] . '_' . $row['Bestand'] . '_' . $row['roomId'];
            $groupsByRoom[$key][] = $row;
        }
        $stmt->close();

        // Step 2: Calculate max entries per element+variant+bestand
        $maxEntriesPerElement = [];
        foreach ($groupsByRoom as $roomKey => $rows) {
            list($elementId, $variantId, $bestand, $roomId) = explode('_', $roomKey);
            $elemKey = $elementId . '_' . $variantId . '_' . $bestand;
            $maxEntriesPerElement[$elemKey] = max($maxEntriesPerElement[$elemKey] ?? 0, count($rows));
        }

        // Step 3: Initialize empty $pivot structure keyed by elemKey and row-index
        $pivot = [];
        foreach ($maxEntriesPerElement as $elemKey => $maxCount) {
            for ($i = 0; $i < $maxCount; $i++) {
                $pivot[$elemKey][$i] = [
                    'label' => '',
                    'elementId' => null,
                    'variantId' => null,
                    'values' => [],    // roomId => value sum for that row
                    'relationIds' => [],
                    'statuses' => [],
                    'Bestand' => null
                ];
            }
        }

        // Step 4: Distribute each room's entries across the pivot rows for that elem
        foreach ($groupsByRoom as $roomKey => $rows) {
            list($elementId, $variantId, $bestand, $roomId) = explode('_', $roomKey);
            $elemKey = $elementId . '_' . $variantId . '_' . $bestand;

            // Sort rows stably by relationId
            usort($rows, fn($a, $b) => $a['relationId'] <=> $b['relationId']);

            foreach ($rows as $index => $r) {
                // Initialize label and other info only if empty
                if ($pivot[$elemKey][$index]['label'] === '') {
                    $pivot[$elemKey][$index]['label'] = $r['ElementID'] . ' ' . $r['Bezeichnung'] . ' (' . ($r['variantName'] ?: '-') . '' . ($r['Bestand'] === 0 ? "; Best)" : ")");
                    $pivot[$elemKey][$index]['elementId'] = (int)$r['elementId'];
                    $pivot[$elemKey][$index]['variantId'] = $r['variantId'] ?? 0;
                    $pivot[$elemKey][$index]['Bestand'] = $r['Bestand'];
                }
                // Sum values per room per row
                $pivot[$elemKey][$index]['values'][$roomId] = ($pivot[$elemKey][$index]['values'][$roomId] ?? 0) + (int)$r['total'];
                $pivot[$elemKey][$index]['relationIds'][$roomId] = $r['relationId']; // last relationId per room,col
                $pivot[$elemKey][$index]['statuses'][$roomId] = $r['status'];
            }
        }

        // Add missing elements with zeros for rooms
        foreach ($elementVariants as $ev) {
            $key = $ev['elementId'] . '_' . $ev['variantId'] . '_' . $ev['Bestand'];
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
                $label = ($elem ? $elem['ElementID'] . ' ' . $elem['Bezeichnung'] : 'Unbekannt') . ' (' . $variantName . ')(' . $ev['Bestand'] . ')';
                $pivot[$key] = [[
                    'label' => $label,
                    'elementId' => $ev['elementId'],
                    'variantId' => $ev['variantId'],
                    'values' => array_fill_keys($roomIds, 0),
                    'relationIds' => [],
                    'Bestand' => $ev['Bestand'],
                    'statuses' => []
                ]];
            }
        }

        // Flatten $pivot to single dimension list for output
        $flatPivot = [];
        foreach ($pivot as $elemKey => $rows) {
            if (isset($rows[0]) && is_array($rows[0])) {
                foreach ($rows as $row) {
                    $flatPivot[] = $row;
                }
            } else {
                $flatPivot[] = $rows;
            }
        }

        // Filter out empty sums if requested
        if ($ohneLeere) {
            $flatPivot = array_filter($flatPivot, fn($data) => array_sum($data['values']) > 0);
        }

        // === 4. Generate HTML ===
        ob_start();
        echo '<table id="pivotTable" class="table table-bordered table-sm table-striped">';
        echo '<thead><tr><th>Element (Variante)</th><th>Summe</th>';
        foreach ($rooms as $rId => $rLabel) {
            echo '<th>' . htmlspecialchars($rLabel) . '</th>';
        }
        echo '</tr></thead><tbody>';
        foreach ($flatPivot as $data) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($data['label']) . '</td>';
            $total = array_sum($data['values']);
            echo '<td>' . $total . '</td>';
            foreach ($rooms as $rId => $rLabel) {
                $val = $data['values'][$rId] ?? 0;
                $relId = $data['relationIds'][$rId] ?? '';
                $status = $data['statuses'][$rId] ?? null;

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
                        break;
                }
                echo '<td class="' . $class . '" tabindex="0" ' .
                    'data-room-id="' . htmlspecialchars($rId) . '" ' .
                    'data-element-id="' . htmlspecialchars($data['elementId']) . '" ' .
                    'data-variant-id="' . htmlspecialchars($data['variantId'] ?? '1') . '" ' .
                    'data-bestand="' . htmlspecialchars($data['Bestand']) . '" ' .
                    'data-relation-id="' . htmlspecialchars($relId ?: '0') . '">' .
                    htmlspecialchars($val) .
                    '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table>';

        return ob_get_clean();
    }
}