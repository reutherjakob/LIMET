<?php
include "_utils.php";
check_login();
$projectID = $_SESSION["projectID"] ?? 75;

header("Content-Type: text/html; charset=UTF-8");

$raumbereich = $_POST["raumbereich"] ?? "";
$mtRelevant = isset($_POST["mtRelevant"]) ? intval($_POST["mtRelevant"]) : 0;
$entfallen = isset($_POST["entfallen"]) ? intval($_POST["entfallen"]) : 0;
$nurMitElementen = isset($_POST["nurMitElementen"]) ? intval($_POST["nurMitElementen"]) : 0;
$ohneLeereElemente = isset($_POST["ohneLeereElemente"]) ? intval($_POST["ohneLeereElemente"]) : 1;
$transponiert = isset($_POST["transponiert"]) ? intval($_POST["transponiert"]) : 0;

if (empty($raumbereich)) {
    echo '<div class="alert alert-warning">Kein Raumbereich gewählt.</div>';
    exit;
}

$conn = utils_connect_sql();

// Räume im Filterbereich und Projekt ermitteln, Filter anwenden
$sqlRooms = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung
             FROM tabelle_räume
             WHERE tabelle_projekte_idTABELLE_Projekte = ?
               AND `Raumbereich Nutzer` = ?";

$params = [$projectID, $raumbereich];
$types = "is";

if ($mtRelevant) {
    $sqlRooms .= " AND `MT-relevant` = 1";
}
if ($entfallen) {
    $sqlRooms .= " AND (`Entfallen` IS NULL OR `Entfallen` = 0)";
}
$sqlRooms .= " ORDER BY Raumnr";

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

// Pivot-Daten abfragen
$roomIDs = implode(",", array_map("intval", array_keys($rooms)));
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
      AND r.`Raumbereich Nutzer` = ?
      AND re.Standort = 1
      AND r.idTABELLE_Räume IN ($roomIDs)
    GROUP BY e.ElementID, e.Bezeichnung, r.idTABELLE_Räume
    ORDER BY e.ElementID, r.Raumnr
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $projectID, $raumbereich);
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

if ($ohneLeereElemente) {
// Elemente mit nur Nullen/leer ausblenden, falls Filter aktiv
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



if ($transponiert) {
    echo '<table class="table table-sm table-striped table-hover border border-light border-5" id="pivotTable">';
    echo '<thead><tr><th>Raum</th>';
    foreach ($pivot as $eid => $data) {
        // ElementID und Bezeichnung in einer Spalte
        echo '<th>' . htmlspecialchars($eid . ' ' . $data["Bezeichnung"]) . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($rooms as $rid => $rlabel) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($rlabel) . '</td>';
        foreach ($pivot as $eid => $data) {
            $val = $data["Räume"][$rid] ?? "";
            echo '<td>' . htmlspecialchars($val) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';

} else {

    echo '<table class="table table-sm table-striped table-hover border border-light border-5" id="pivotTable">';
    echo '<thead><tr><th>Element</th>';
    foreach ($rooms as $rid => $rlabel) {
        echo '<th>' . htmlspecialchars($rlabel) . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($pivot as $eid => $data) {
        echo '<tr>';
        // ElementID und Bezeichnung in einer Spalte
        echo '<td>' . htmlspecialchars($eid . ' ' . $data["Bezeichnung"]) . '</td>';
        foreach ($rooms as $rid => $rlabel) {
            $val = $data["Räume"][$rid] ?? "";
            echo '<td>' . htmlspecialchars($val) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';


}


?>
