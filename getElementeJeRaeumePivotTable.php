<?php
// 25 FX
include_once "utils/_utils.php";

// --- AUTH and INIT ---
check_login();
$projectID = (int)$_SESSION["projectID"];
header("Content-Type: text/html; charset=UTF-8");

// 1. POST Inputs holen
$raumbereiche = $_POST['raumbereich'] ?? [];
if (!is_array($raumbereiche)) $raumbereiche = [$raumbereiche];
$zusatzRaeume = $_POST['zusatzRaeume'] ?? [];
if (!is_array($zusatzRaeume)) $zusatzRaeume = [$zusatzRaeume];
$zusatzElemente = $_POST['zusatzElemente'] ?? [];
if (!is_array($zusatzElemente)) $zusatzElemente = [$zusatzElemente];
// error_log('ZusatzElemente: ' . print_r($zusatzElemente, true));


$raumbereiche = (array)($raumbereiche);  // No intval!
$zusatzRaeume = array_map('intval', (array)($zusatzRaeume));  // IDs only
$zusatzElemente = array_map('intval', (array)($zusatzElemente));  // IDs only

$mtRelevant = isset($_POST["mtRelevant"]) ? intval($_POST["mtRelevant"]) : 0;
$entfallen = isset($_POST["entfallen"]) ? intval($_POST["entfallen"]) : 0;
$nurMitElementen = isset($_POST["nurMitElementen"]) ? intval($_POST["nurMitElementen"]) : 0;
$ohneLeereElemente = isset($_POST["ohneLeereElemente"]) ? intval($_POST["ohneLeereElemente"]) : 1;
$transponiert = isset($_POST["transponiert"]) ? intval($_POST["transponiert"]) : 0;

// --- 1. Räume im Filterbereich ermitteln ---
$conn = utils_connect_sql();
$rooms = [];
if (!empty($raumbereiche)) {
    $bereichPlaceholders = implode(',', array_fill(0, count($raumbereiche), '?'));
    $sqlRooms = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung FROM tabelle_räume WHERE tabelle_projekte_idTABELLE_Projekte = ?
        AND `Raumbereich Nutzer` IN ($bereichPlaceholders)";
    $params = array_merge([$projectID], $raumbereiche);
    $types = str_repeat('i', 1) . str_repeat('s', count($raumbereiche));
    if ($mtRelevant) $sqlRooms .= " AND `MT-relevant` = 1";
    if ($entfallen) $sqlRooms .= " AND (`Entfallen` IS NULL OR `Entfallen` = 0)";
    $sqlRooms .= " ORDER BY Raumnr";
    $stmt = $conn->prepare($sqlRooms);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $resRooms = $stmt->get_result();
    while ($row = $resRooms->fetch_assoc()) {
        $rooms[$row["idTABELLE_Räume"]] = $row["Raumnr"] . " " . $row["Raumbezeichnung"];
    }
    $stmt->close();
}
// --- 1b. Zusätzlich selektierte Räume einfügen (keine Duplikate) ---
if (!empty($zusatzRaeume)) {
    $roomPlaceholder = implode(',', array_fill(0, count($zusatzRaeume), '?'));
    $sql = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung FROM tabelle_räume WHERE idTABELLE_Räume IN ($roomPlaceholder)";
    if ($mtRelevant) $sql .= " AND `MT-relevant` = 1";
    if ($entfallen) $sql .= " AND (`Entfallen` IS NULL OR `Entfallen` = 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($zusatzRaeume)), ...$zusatzRaeume);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rooms[$row["idTABELLE_Räume"]] = $row["Raumnr"] . " " . $row["Raumbezeichnung"];
    }
    $stmt->close();
}

if ($nurMitElementen) {
    $sql = "SELECT DISTINCT TABELLE_Räume_idTABELLE_Räume FROM tabelle_räume_has_tabelle_elemente WHERE Standort = 1";
    $result = $conn->query($sql);
    $roomIDsWithElements = [];
    while ($row = $result->fetch_assoc()) {
        $roomIDsWithElements[] = $row['TABELLE_Räume_idTABELLE_Räume'];
    }
    $rooms = array_filter($rooms, function ($id) use ($roomIDsWithElements) {
        return in_array($id, $roomIDsWithElements);
    }, ARRAY_FILTER_USE_KEY);
}
if (empty($rooms)) {
    echo '<div class="alert alert-info">Keine Räume im gewählten Bereich oder unter den Zusatzräumen gefunden.</div>';
    exit;
}

// --- 2. Relevante Element-IDs (mit Varianten) aus den Räumen holen ---
$roomIDs = array_keys($rooms);
if (empty($roomIDs)) {
    echo '<div class="alert alert-info">Keine Räume gefunden.</div>';
    exit;
}
$roomPlaceholders = implode(',', array_fill(0, count($roomIDs), '?'));
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
$stmt->bind_param($elemTypes, ...$elemParams);
$stmt->execute();
$resElemIDs = $stmt->get_result();
$foundElementIDs = [];
$existingVariantsByElement = [];
while ($row = $resElemIDs->fetch_assoc()) {
    $eid = intval($row["idTABELLE_Elemente"]);
    $var = $row["tabelle_Varianten_idtabelle_Varianten"] === null ? null : intval($row["tabelle_Varianten_idtabelle_Varianten"]);
    $foundElementIDs[] = ['id' => $eid, 'variante' => $var];

    if (!isset($existingVariantsByElement[$eid])) {
        $existingVariantsByElement[$eid] = [];
    }
    $existingVariantsByElement[$eid][] = $var;
}
$stmt->close();

// Add extra elements from zusatzElemente with variant=null
foreach ($zusatzElemente as $zusatzEIDraw) {
    $foundElementIDs[] = ['id' => intval($zusatzEIDraw), 'variante' => null];
}

// --- 3. Pivot-Daten (mit Varianten) abfragen ---
$filterElemente = !empty($foundElementIDs);
$elementFilterSQL = "";
$elementFilterArr = [];
if ($filterElemente) {
    foreach ($foundElementIDs as $feid) {
        if (!isset($feid['id'])) continue;
        $eid = intval($feid['id']);
        if ($feid['variante'] === null) {
            $elementFilterArr[] = "(e.idTABELLE_Elemente = $eid)";
        } else {
            $variante = intval($feid['variante']);
            $elementFilterArr[] = "(e.idTABELLE_Elemente = $eid AND 
                                   (re.tabelle_Varianten_idtabelle_Varianten = $variante OR re.tabelle_Varianten_idtabelle_Varianten IS NULL))";
        }
    }
    if (count($elementFilterArr)) {
        $elementFilterSQL = " AND (" . implode(" OR ", $elementFilterArr) . ")";
    }
}
// error_log("Element Filter SQL: " . $elementFilterSQL);

$sql = "
    SELECT
        e.ElementID,#
        e.Bezeichnung,
        e.idTABELLE_Elemente,
        COALESCE(v.idtabelle_Varianten, 0) AS idtabelle_Varianten,
        COALESCE(v.Variante, '-') AS VarianteName,
        r.idTABELLE_Räume,
        SUM(re.Anzahl) AS Summe
    FROM tabelle_elemente e
    JOIN tabelle_räume_has_tabelle_elemente re 
        ON e.idTABELLE_Elemente = re.TABELLE_Elemente_idTABELLE_Elemente
    JOIN tabelle_räume r 
        ON re.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
    LEFT JOIN tabelle_varianten v
        ON re.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
      AND re.Standort = 1
      AND r.idTABELLE_Räume IN ($roomPlaceholders)
      $elementFilterSQL
    GROUP BY e.ElementID, e.Bezeichnung, e.idTABELLE_Elemente, v.idtabelle_Varianten, r.idTABELLE_Räume
    ORDER BY e.ElementID 
";
$allParams = array_merge([$projectID], $roomIDs);
$allTypes = str_repeat('i', 1 + count($roomIDs));
$stmt = $conn->prepare($sql);
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$res = $stmt->get_result();

// --- PIVOT DATA PREP
$pivot = [];
while ($row = $res->fetch_assoc()) {
    // error_log("Pivot row: " . print_r($row, true));
    $pivotKey = $row["idTABELLE_Elemente"] . "_" . $row["idtabelle_Varianten"];
    $label = $row["ElementID"] . ' ' . $row["Bezeichnung"] . ' (' . $row["VarianteName"] . ')';
    if (!isset($pivot[$pivotKey])) {
        $pivot[$pivotKey] = [
            "ElementLabel" => $label,
            "Räume" => []
        ];
    }
    $pivot[$pivotKey]["Räume"][$row["idTABELLE_Räume"]] = intval($row["Summe"]);
}

// --- Helper function to get label for element + variant ---
function getElementLabel($conn, int $elementID, ?int $variantID): string {
    $sqlElem = "SELECT ElementID, Bezeichnung FROM tabelle_elemente WHERE idTABELLE_Elemente = ?";
    $stmt = $conn->prepare($sqlElem);
    $stmt->bind_param("i", $elementID);
    $stmt->execute();
    $res = $stmt->get_result();
    $elem = $res->fetch_assoc();
    $stmt->close();

    if (!$elem) return "Unbekanntes Element #$elementID";

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
}

// --- Add missing zusatzElemente to pivot with zero sums ---
$existingKeys = array_keys($pivot);
foreach ($foundElementIDs as $fe) {
    $elementID = intval($fe['id']);
    $variantID = $fe['variante'] ?? 0;
    $key = $elementID . "_" . $variantID;
    if (!in_array($key, $existingKeys)) {
        $label = getElementLabel($conn, $elementID, $variantID);

        // zero sums for all rooms
        $raumSums = [];
        foreach ($rooms as $roomID => $roomLabel) {
            $raumSums[$roomID] = 0;
        }

        $pivot[$key] = ["ElementLabel" => $label, "Räume" => $raumSums];
    }
}

// --- 4. Filter: Elemente mit nur Nullen/leer ausblenden (falls gewünscht)
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

// --- 5. Ausgabe: Pivot-Tabelle inkl. Varianten ---
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
?>
