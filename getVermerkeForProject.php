<?php
// getVermerkeForProject.php
// Gibt alle Vermerke des Projekts zurück, gruppiert nach Vermerkgruppe und Untergruppe.
// Wird im Vermerk-Zuordnungs-Modal als Picker verwendet.
require_once 'utils/_utils.php';
check_login();

header('Content-Type: application/json');

$projectID = (int)($_SESSION['projectID'] ?? 0);
if (!$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Kein Projekt.']);
    exit;
}

$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare("
    SELECT
        v.idtabelle_Vermerke,
        LEFT(v.Vermerktext, 80)                          AS Kurztext,
        v.Vermerkart,
        vu.Untergruppenname,
        vu.Untergruppennummer,
        vg.idtabelle_Vermerkgruppe,
        vg.Gruppenname,
        vg.Datum
    FROM tabelle_Vermerke v
    INNER JOIN tabelle_Vermerkuntergruppe vu
        ON v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = vu.idtabelle_Vermerkuntergruppe
    INNER JOIN tabelle_Vermerkgruppe vg
        ON vu.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = vg.idtabelle_Vermerkgruppe
    WHERE vg.tabelle_projekte_idTABELLE_Projekte = ?
    ORDER BY vg.Datum DESC, vg.idtabelle_Vermerkgruppe, vu.Untergruppennummer, v.idtabelle_Vermerke
");
$stmt->bind_param('i', $projectID);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();

echo json_encode(['status' => 'ok', 'vermerke' => $rows]);
?>