<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html>
<head>
</head>
<body>

<?php
require_once 'utils/_utils.php';
init_page_serversides();

$mysqli = utils_connect_sql();

$projectID = $_SESSION["projectID"] ?? null;
$projectID = intval($projectID);

if (!$projectID) {
    echo "<div class='alert alert-danger'>Projekt-ID fehlt oder ungültig.</div>";
    exit;
}

// Abfrage: Gewerke zugewiesen
$stmt = $mysqli->prepare("
    SELECT reh.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_räume r
    INNER JOIN tabelle_räume_has_tabelle_elemente reh ON r.idTABELLE_Räume = reh.TABELLE_Räume_idTABELLE_Räume
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
      AND reh.TABELLE_Elemente_idTABELLE_Elemente NOT IN (
          SELECT tabelle_elemente_idTABELLE_Elemente
          FROM tabelle_projekt_element_gewerk
          WHERE tabelle_projekte_idTABELLE_Projekte = ?
      )
    GROUP BY reh.TABELLE_Elemente_idTABELLE_Elemente
");

$stmt->bind_param("ii", $projectID, $projectID);
$stmt->execute();
$result = $stmt->get_result();

echo "<div class='m-1 row' id='checkGewerke'>";
if ($result->num_rows > 0) {
    echo "<span class='badge bg-danger'>Gewerke zugeteilt</span>";
} else {
    echo "<span class='badge bg-success'>Gewerke zugeteilt</span>";
}
echo "</div>";
$stmt->close();


// Abfrage: Kosten zugewiesen
$stmt = $mysqli->prepare("
    SELECT reh.TABELLE_Elemente_idTABELLE_Elemente, reh.tabelle_Varianten_idtabelle_Varianten
    FROM tabelle_räume r
    INNER JOIN tabelle_räume_has_tabelle_elemente reh ON r.idTABELLE_Räume = reh.TABELLE_Räume_idTABELLE_Räume
    INNER JOIN tabelle_projekt_varianten_kosten pk ON 
        pk.tabelle_Varianten_idtabelle_Varianten = reh.tabelle_Varianten_idtabelle_Varianten
        AND pk.tabelle_elemente_idTABELLE_Elemente = reh.TABELLE_Elemente_idTABELLE_Elemente
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
      AND pk.Kosten = '0'
      AND pk.tabelle_projekte_idTABELLE_Projekte = ?
    GROUP BY reh.TABELLE_Elemente_idTABELLE_Elemente, reh.tabelle_Varianten_idtabelle_Varianten
");

$stmt->bind_param("ii", $projectID, $projectID);
$stmt->execute();
$result = $stmt->get_result();

echo "<div class='m-1 row' id='checkCosts'>";
if ($result->num_rows > 0) {
    echo "<span class='badge bg-danger'>Kosten zugeordnet</span>";
} else {
    echo "<span class='badge bg-success'>Kosten zugeordnet</span>";
}
echo "</div>";
$stmt->close();


// Abfrage: Offene Protokollpunkte
$stmt = $mysqli->prepare("
    SELECT v.idtabelle_Vermerke
    FROM tabelle_Vermerkuntergruppe ug
    INNER JOIN tabelle_Vermerkgruppe g ON ug.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = g.idtabelle_Vermerkgruppe
    INNER JOIN tabelle_Vermerke v ON ug.idtabelle_Vermerkuntergruppe = v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe
    WHERE g.tabelle_projekte_idTABELLE_Projekte = ?
      AND v.Vermerkart = 'Bearbeitung'
      AND v.Bearbeitungsstatus = 0
");

$stmt->bind_param("i", $projectID);
$stmt->execute();
$result = $stmt->get_result();

echo "<div class='m-1 row' id='checkProtocols'>";
if ($result->num_rows > 0) {
    echo "<span class='badge bg-danger'>Offene Protokollpunkte</span>";
} else {
    echo "<span class='badge bg-success'>Offene Protokollpunkte</span>";
}
echo "</div>";
$stmt->close();


// Abfrage: Lose Zuordnung
$stmt = $mysqli->prepare("
    SELECT reh.tabelle_Lose_Extern_idtabelle_Lose_Extern
    FROM tabelle_räume r
    INNER JOIN tabelle_räume_has_tabelle_elemente reh ON r.idTABELLE_Räume = reh.TABELLE_Räume_idTABELLE_Räume
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
      AND reh.tabelle_Lose_Extern_idtabelle_Lose_Extern IS NULL
");
$stmt->bind_param("i", $projectID);
$stmt->execute();
$result = $stmt->get_result();

echo "<div class='m-1 row' id='checkLots'>";
if ($result->num_rows > 0) {
    echo "<span class='badge bg-danger'>Elemente Losen zugeordnet</span>";
} else {
    echo "<span class='badge bg-success'>Elemente Losen zugeordnet</span>";
}
echo "</div>";
$stmt->close();
$mysqli->close();

?>

<script>
</script>
</body>
</html>
