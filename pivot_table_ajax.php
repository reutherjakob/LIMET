<?php
include "utils/_utils.php";
check_login();
$projectID = $_SESSION["projectID"] ?? 75;

header("Content-Type: text/html; charset=UTF-8");

// Accept multiple raumbereiche as array
$raumbereiche = $_POST['raumbereich'] ?? [];
error_log(print_r($_POST['raumbereich'], true));
if (!is_array($raumbereiche)) $raumbereiche = [$raumbereiche];

$mtRelevant = isset($_POST["mtRelevant"]) ? intval($_POST["mtRelevant"]) : 0;
$entfallen = isset($_POST["entfallen"]) ? intval($_POST["entfallen"]) : 0;
$nurMitElementen = isset($_POST["nurMitElementen"]) ? intval($_POST["nurMitElementen"]) : 0;
$ohneLeereElemente = isset($_POST["ohneLeereElemente"]) ? intval($_POST["ohneLeereElemente"]) : 1;
$transponiert = isset($_POST["transponiert"]) ? intval($_POST["transponiert"]) : 0;

if (empty($raumbereiche)) {
    echo '<div class="alert alert-warning">Kein Raumbereich gewählt.</div>';
    exit;
}

$conn = utils_connect_sql();

// --- 1. Räume im Filterbereich und Projekt ermitteln, Filter anwenden ---
$bereichPlaceholders = implode(',', array_fill(0, count($raumbereiche), '?'));
$sqlRooms = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung
             FROM tabelle_räume
             WHERE tabelle_projekte_idTABELLE_Projekte = ?
               AND `Raumbereich Nutzer` IN ($bereichPlaceholders)";

$params = array_merge([$projectID], $raumbereiche);
$types = str_repeat('i', 1) . str_repeat('s', count($raumbereiche));

if ($mtRelevant) {
    $sqlRooms .= " AND `MT-relevant` = 1";
}
if ($entfallen) {
    $sqlRooms .= " AND (`Entfallen` IS NULL OR `Entfallen` = 0)";
}
$sqlRooms .= " ORDER BY Raumnr";

error_log("Types: $types");
error_log("Params: " . print_r($params, true));

$stmt = $conn->prepare($sqlRooms);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resRooms = $stmt->get_result();

$rooms = [];
while ($row = $resRooms->fetch_assoc()) {
    $rooms[$row["idTABELLE_Räume"]] = $row["Raumnr"] . " " . $row["Raumbezeichnung"];
}
$stmt->close();

// Optional: Nur Räume mit Elementen anzeigen
if ($nurMitElementen) {
    $sql = "SELECT DISTINCT TABELLE_Räume_idTABELLE_Räume FROM tabelle_räume_has_tabelle_elemente WHERE Standort = 1";
    $result = $conn->query($sql);
    $roomIDsWithElements = [];
    while ($row = $result->fetch_assoc()) {
        $roomIDsWithElements[] = $row['TABELLE_Räume_idTABELLE_Räume'];
    }
    $rooms = array_filter($rooms, function($id) use ($roomIDsWithElements) {
        return in_array($id, $roomIDsWithElements);
    }, ARRAY_FILTER_USE_KEY);
}

if (empty($rooms)) {
    echo '<div class="alert alert-info">Keine Räume im gewählten Bereich gefunden.</div>';
    exit;
}

// --- 2. Pivot-Daten abfragen ---
$roomIDs = array_keys($rooms);
if (empty($roomIDs)) {
    echo '<div class="alert alert-info">Keine Räume gefunden.</div>';
    exit;
}
$bereichPlaceholders = implode(',', array_fill(0, count($raumbereiche), '?'));
$roomPlaceholders = implode(',', array_fill(0, count($roomIDs), '?'));

$sql = "
    SELECT
        e.ElementID,
        e.Bezeichnung,
        r.idTABELLE_Räume,
        SUM(re.Anzahl) AS Summe
    FROM tabelle_elemente e
    JOIN tabelle_räume_has_tabelle_elemente re ON e.idTABELLE_Elemente = re.TABELLE_Elemente_idTABELLE_Elemente
    JOIN tabelle_räume r ON re.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
      AND r.`Raumbereich Nutzer` IN ($bereichPlaceholders)
      AND re.Standort = 1
      AND r.idTABELLE_Räume IN ($roomPlaceholders)
    GROUP BY e.ElementID, e.Bezeichnung, r.idTABELLE_Räume
    ORDER BY e.ElementID 
";

// Build parameter types and values for the pivot query
$allParams = array_merge([$projectID], $raumbereiche, $roomIDs);
$allTypes = str_repeat('i', 1) . str_repeat('s', count($raumbereiche)) . str_repeat('i', count($roomIDs));

$stmt = $conn->prepare($sql);
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$res = $stmt->get_result();

$pivot = [];
while ($row = $res->fetch_assoc()) {
    $eid = $row["ElementID"];
    $bez = $row["Bezeichnung"];
    $rid = $row["idTABELLE_Räume"];
    $sum = $row["Summe"];
    if (!isset($pivot[$eid])) {
        $pivot[$eid] = [
            "Bezeichnung" => $bez,
            "Räume" => []
        ];
    }
    $pivot[$eid]["Räume"][$rid] = $sum;
}
$stmt->close();

// --- 3. Filter: Elemente mit nur Nullen/leer ausblenden ---
if ($ohneLeereElemente) {
    foreach ($pivot as $eid => $data) {
        $alleNull = true;
        foreach ($rooms as $rid => $rlabel) {
            $val = $data["Räume"][$rid] ?? null;
            if (!empty($val) && intval($val) > 0) {
                $alleNull = false;
                break;
            }
        }
        if ($alleNull) {
            unset($pivot[$eid]);
        }
    }
}

// --- 4. Ausgabe: Pivot-Tabelle ---
echo '<table class="table compact table-striped table-hover" id="pivotTable">';

if ($transponiert) {
    echo '<thead><tr><th>Raum</th><th>Summe</th>';
    foreach ($pivot as $eid => $data) {
        echo '<th class="rotate"><div> ' . htmlspecialchars($eid . ' ' . $data["Bezeichnung"]) . '</div></th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($rooms as $rid => $rlabel) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($rlabel) . '</td>';

        // Summe berechnen
        $sum = 0;
        foreach ($pivot as $eid => $data) {
            $val = $data["Räume"][$rid] ?? 0;
            $sum += intval($val);
        }
        echo '<td>' . $sum . '</td>';

        foreach ($pivot as $eid => $data) {
            $val = $data["Räume"][$rid] ?? "";
            echo '<td>' . htmlspecialchars($val) . '</td>';
        }
        echo '</tr>';
    }

} else {
    echo '<thead><tr><th>Element</th><th>Summe</th>';
    foreach ($rooms as $rid => $rlabel) {
        echo '<th class="rotate"><div>' . htmlspecialchars($rlabel) . '</div></th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($pivot as $eid => $data) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($eid . ' ' . $data["Bezeichnung"]) . '</td>';

        // Summe berechnen
        $sum = 0;
        foreach ($rooms as $rid => $rlabel) {
            $val = $data["Räume"][$rid] ?? 0;
            $sum += intval($val);
        }
        echo '<td>' . $sum . '</td>';

        foreach ($rooms as $rid => $rlabel) {
            $val = $data["Räume"][$rid] ?? "";
            echo '<td>' . htmlspecialchars($val) . '</td>';
        }
        echo '</tr>';
    }

}
echo '</tbody></table>';
?>
