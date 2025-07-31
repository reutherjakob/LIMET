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
     * @param bool $mtRelevant Filter MT-relevant rooms
     * @param bool $entfallen Filter out "entfallen" rooms
     * @param bool $nurMitElementen Only show rooms that have elements
     * @param bool $ohneLeereElemente Filter out elements with all zero sum
     * @param bool $transponiert Whether to transpose rows/columns
     * @return string HTML
     * @throws Exception on SQL errors
     */

// Inside your PivotTable.php model class

    public function getElementeJeRaeumePivotTable(
        array $raumbereiche,
        array $zusatzRaeume,
        array $zusatzElemente,
        bool  $mtRelevant,
        bool  $entfallen,
        bool  $nurMitElementen,
        bool  $ohneLeereElemente,
        bool  $transponiert
    ): string
    {
        $conn = $this->conn;
        $projectID = $this->projectID;

        // --- 1. Rooms filter ---
        $rooms = [];
        if (!empty($raumbereiche)) {
            $bereichPlaceholders = implode(',', array_fill(0, count($raumbereiche), '?'));
            $sqlRooms = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung
                     FROM tabelle_räume
                     WHERE tabelle_projekte_idTABELLE_Projekte = ? 
                       AND `Raumbereich Nutzer` IN ($bereichPlaceholders)";
            if ($mtRelevant) $sqlRooms .= " AND `MT-relevant` = 1";
            if ($entfallen) $sqlRooms .= " AND (`Entfallen` IS NULL OR `Entfallen` = 0)";
            $sqlRooms .= " ORDER BY Raumnr";

            $params = array_merge([$projectID], $raumbereiche);
            $types = 'i' . str_repeat('s', count($raumbereiche));
            $stmt = $conn->prepare($sqlRooms);
            if (!$stmt) throw new Exception("Prepare failed rooms: " . $conn->error);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $resRooms = $stmt->get_result();
            while ($row = $resRooms->fetch_assoc()) {
                $rooms[$row["idTABELLE_Räume"]] = $row["Raumnr"] . " " . $row["Raumbezeichnung"];
            }
            $stmt->close();
        }

        // Add zusatz rooms not already in rooms
        if (!empty($zusatzRaeume)) {
            $toAdd = array_diff($zusatzRaeume, array_keys($rooms));
            if (!empty($toAdd)) {
                $placeholders = implode(',', array_fill(0, count($toAdd), '?'));
                $sqlAdd = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung FROM tabelle_räume WHERE idTABELLE_Räume IN ($placeholders)";
                if ($mtRelevant) $sqlAdd .= " AND `MT-relevant` = 1";
                if ($entfallen) $sqlAdd .= " AND (`Entfallen` IS NULL OR `Entfallen` = 0)";
                $stmt2 = $conn->prepare($sqlAdd);
                if (!$stmt2) throw new Exception("Prepare failed zusatz rooms: " . $conn->error);
                $types2 = str_repeat('i', count($toAdd));
                $stmt2->bind_param($types2, ...$toAdd);
                $stmt2->execute();
                $res2 = $stmt2->get_result();
                while ($row = $res2->fetch_assoc()) {
                    $rooms[$row["idTABELLE_Räume"]] = $row["Raumnr"] . " " . $row["Raumbezeichnung"];
                }
                $stmt2->close();
            }
        }

        // Filter rooms with elements only if requested
        if ($nurMitElementen) {
            $sqlRmWithElem = "SELECT DISTINCT TABELLE_Räume_idTABELLE_Räume FROM tabelle_räume_has_tabelle_elemente WHERE Standort = 1";
            $resRm = $conn->query($sqlRmWithElem);
            $roomIDsWithElements = [];
            while ($row = $resRm->fetch_assoc()) {
                $roomIDsWithElements[] = $row['TABELLE_Räume_idTABELLE_Räume'];
            }
            $rooms = array_filter($rooms, function ($id) use ($roomIDsWithElements) {
                return in_array($id, $roomIDsWithElements);
            }, ARRAY_FILTER_USE_KEY);
        }

        if (empty($rooms)) {
            return '<div class="alert alert-info">Keine Räume im gewählten Bereich oder unter den Zusatzräumen gefunden.</div>';
        }

        $roomIDs = array_keys($rooms);
        $roomPlaceholders = implode(',', array_fill(0, count($roomIDs), '?'));

        // --- 2. Find elements with variants assigned in rooms ---
        $sqlElemIDs = "
        SELECT DISTINCT e.idTABELLE_Elemente, re.tabelle_Varianten_idtabelle_Varianten
        FROM tabelle_elemente e
        JOIN tabelle_räume_has_tabelle_elemente re ON e.idTABELLE_Elemente = re.TABELLE_Elemente_idTABELLE_Elemente
        JOIN tabelle_räume r ON re.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
          AND re.Standort = 1
          AND r.idTABELLE_Räume IN ($roomPlaceholders)";

        $elemParams = array_merge([$projectID], $roomIDs);
        $elemTypes = str_repeat('i', 1 + count($roomIDs));
        $stmt = $conn->prepare($sqlElemIDs);
        if (!$stmt) throw new Exception("Prepare failed elemIDs: " . $conn->error);
        $stmt->bind_param($elemTypes, ...$elemParams);
        $stmt->execute();
        $resElemIDs = $stmt->get_result();
        $foundElementIDs = [];
        while ($row = $resElemIDs->fetch_assoc()) {
            $eid = intval($row["idTABELLE_Elemente"]);
            $var = $row["tabelle_Varianten_idtabelle_Varianten"] === null ? null : intval($row["tabelle_Varianten_idtabelle_Varianten"]);
            $foundElementIDs[] = ['id' => $eid, 'variante' => $var];
        }
        $stmt->close();

        // Add zusatz elements with variante=null
        foreach ($zusatzElemente as $zusatzEIDraw) {
            $foundElementIDs[] = ['id' => intval($zusatzEIDraw), 'variante' => null];
        }

        // --- 3. Build filter for pivot query ---
        $elementFilterArr = [];
        if (!empty($foundElementIDs)) {
            foreach ($foundElementIDs as $feid) {
                if (!isset($feid['id'])) continue;
                $eid = intval($feid['id']);
                if ($feid['variante'] === null) {
                    $elementFilterArr[] = "(e.idTABELLE_Elemente = $eid)";
                } else {
                    $variante = intval($feid['variante']);
                    $elementFilterArr[] = "(e.idTABELLE_Elemente = $eid AND (re.tabelle_Varianten_idtabelle_Varianten = $variante OR re.tabelle_Varianten_idtabelle_Varianten IS NULL))";
                }
            }
        }
        $elementFilterSQL = count($elementFilterArr) ? " AND (" . implode(" OR ", $elementFilterArr) . ")" : "";

        // --- 4. Pivot data query ---
        $sqlPivot = "
        SELECT
            e.ElementID,
            e.Bezeichnung,
            e.idTABELLE_Elemente,
            COALESCE(v.idtabelle_Varianten, 0) AS idtabelle_Varianten,
            COALESCE(v.Variante, '-') AS VarianteName,
            r.idTABELLE_Räume,
            SUM(re.Anzahl) AS Summe
        FROM tabelle_elemente e
        JOIN tabelle_räume_has_tabelle_elemente re ON e.idTABELLE_Elemente = re.TABELLE_Elemente_idTABELLE_Elemente
        JOIN tabelle_räume r ON re.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
        LEFT JOIN tabelle_varianten v ON re.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
          AND re.Standort = 1
          AND r.idTABELLE_Räume IN ($roomPlaceholders)
          $elementFilterSQL
        GROUP BY e.ElementID, e.Bezeichnung, e.idTABELLE_Elemente, v.idtabelle_Varianten, r.idTABELLE_Räume
        ORDER BY e.ElementID
    ";
        $allParams = array_merge([$projectID], $roomIDs);
        $allTypes = str_repeat('i', 1 + count($roomIDs));
        $stmt = $conn->prepare($sqlPivot);
        if (!$stmt) throw new Exception("Prepare failed pivot: " . $conn->error);
        $stmt->bind_param($allTypes, ...$allParams);
        $stmt->execute();
        $resPivot = $stmt->get_result();

        $pivot = [];
        while ($row = $resPivot->fetch_assoc()) {
            $key = $row["idTABELLE_Elemente"] . "_" . $row["idtabelle_Varianten"];
            $label = $row["ElementID"] . ' ' . $row["Bezeichnung"] . ' (' . $row["VarianteName"] . ')';
            if (!isset($pivot[$key])) {
                $pivot[$key] = [
                    "ElementLabel" => $label,
                    "Räume" => []
                ];
            }
            $pivot[$key]["Räume"][$row["idTABELLE_Räume"]] = intval($row["Summe"]);
        }
        $stmt->close();

        // --- 5. Helper: get element + variant label ---
        $getElementLabel = function (int $elementID, ?int $variantID) use ($conn): string {
            $sqlElem = "SELECT ElementID, Bezeichnung FROM tabelle_elemente WHERE idTABELLE_Elemente = ?";
            $stmt = $conn->prepare($sqlElem);
            $stmt->bind_param("i", $elementID);
            $stmt->execute();
            $res = $stmt->get_result();
            $elem = $res->fetch_assoc();
            $stmt->close();

            if (!$elem) {
                return "Unbekanntes Element #$elementID";
            }

            $variantName = '-';
            if ($variantID !== null && $variantID !== 0) {
                $sqlVar = "SELECT Variante FROM tabelle_varianten WHERE idtabelle_Varianten = ?";
                $stmt = $conn->prepare($sqlVar);
                $stmt->bind_param("i", $variantID);
                $stmt->execute();
                $res = $stmt->get_result();
                $varRow = $res->fetch_assoc();
                $stmt->close();
                if ($varRow && !empty($varRow['Variante'])) {
                    $variantName = $varRow['Variante'];
                }
            }

            return $elem['ElementID'] . ' ' . $elem['Bezeichnung'] . ' (' . $variantName . ')';
        };

        // --- 6. Add missing zusatzElemente to pivot with zero sums ---
        $existingKeys = array_keys($pivot);
        foreach ($foundElementIDs as $fe) {
            $elementID = intval($fe['id']);
            $variantID = $fe['variante'] ?? 0;
            $key = $elementID . "_" . $variantID;
            if (!in_array($key, $existingKeys, true)) {
                $label = $getElementLabel($elementID, $variantID);

                $raumSums = [];
                foreach ($rooms as $roomID => $roomLabel) {
                    $raumSums[$roomID] = 0;
                }
                $pivot[$key] = ["ElementLabel" => $label, "Räume" => $raumSums];
            }
        }

        // --- 7. Filter empty elements if requested ---
        if ($ohneLeereElemente) {
            foreach ($pivot as $key => $data) {
                $alleNull = true;
                foreach ($rooms as $rid => $rlabel) {
                    $val = $data["Räume"][$rid] ?? null;
                    if (!empty($val) && intval($val) > 0) {
                        $alleNull = false;
                        break;
                    }
                }
                if ($alleNull) unset($pivot[$key]);
            }
        }

        // --- 8. Generate HTML output ---
        ob_start();

        echo '<table class="table compact table-striped table-hover" id="pivotTable">';
        if ($transponiert) {
            echo '<thead><tr><th>Raum</th><th>Summe</th>';
            foreach ($pivot as $key => $data) {
                echo '<th>' . htmlspecialchars($data["ElementLabel"]) . '</th>';
            }
            echo '</tr></thead><tbody>';
            foreach ($rooms as $rid => $rlabel) {
                echo '<tr><td>' . htmlspecialchars($rlabel) . '</td>';
                $sum = 0;
                foreach ($pivot as $data) {
                    $sum += $data["Räume"][$rid] ?? 0;
                }
                echo "<td>$sum</td>";
                foreach ($pivot as $data) {
                    $val = $data["Räume"][$rid] ?? "";
                    echo '<td>' . htmlspecialchars($val) . '</td>';
                }
                echo '</tr>';
            }
        } else {
            echo '<thead><tr><th>Element (Variante)</th><th>Summe</th>';
            foreach ($rooms as $rid => $rlabel) {
                echo '<th>' . htmlspecialchars($rlabel) . '</th>';
            }
            echo '</tr></thead><tbody>';
            foreach ($pivot as $key => $data) {
                echo '<tr><td>' . htmlspecialchars($data["ElementLabel"]) . '</td>';
                $sum = 0;
                foreach ($rooms as $rid => $rlabel) {
                    $sum += $data["Räume"][$rid] ?? 0;
                }
                echo "<td>$sum</td>";
                foreach ($rooms as $rid => $rlabel) {
                    $val = $data["Räume"][$rid] ?? "";
                    echo '<td>' . htmlspecialchars($val) . '</td>';
                }
                echo '</tr>';
            }
        }
        echo '</tbody></table>';

        return ob_get_clean();
    }
}
