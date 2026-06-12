<?php
// getImageMeta.php – Metadaten eines Bildes (Timestamp, Projekt, Räume, Vermerke)
require_once 'utils/_utils.php';
check_login();

header('Content-Type: application/json');

$imageID = filter_input(INPUT_POST, 'imageID', FILTER_VALIDATE_INT);
$projectID = (int)($_SESSION['projectID'] ?? 0);

if (!$imageID || !$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter']);
    exit;
}

$mysqli = utils_connect_sql();

// ── Basisdaten: Timestamp + Projektname ──────────────────────────────────────
$stmt = $mysqli->prepare("
    SELECT f.Timestamp, f.Name, p.Projektname
    FROM tabelle_Files f
    INNER JOIN tabelle_projekte p
        ON f.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte
    WHERE f.idtabelle_Files = ?
      AND f.tabelle_projekte_idTABELLE_Projekte = ?
");
$stmt->bind_param('ii', $imageID, $projectID);
$stmt->execute();
$base = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$base) {
    $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Bild nicht gefunden']);
    exit;
}

// ── Räume ────────────────────────────────────────────────────────────────────
$stmt = $mysqli->prepare("
SELECT r.idTABELLE_Räume AS raumID, r.Raumnr, r.Raumbezeichnung, r.`Raumbereich Nutzer`
    FROM tabelle_Files_has_tabelle_Raeume fhr
    INNER JOIN tabelle_räume r ON fhr.tabelle_idRaeume = r.idTABELLE_Räume
    WHERE fhr.tabelle_idfFile = ?
");
$stmt->bind_param('i', $imageID);
$stmt->execute();
$raeume = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Vermerke ─────────────────────────────────────────────────────────────────
$stmt = $mysqli->prepare("
    SELECT v.idtabelle_Vermerke, LEFT(v.Vermerktext, 80) AS Kurztext,
           vg.Gruppenname, vg.Datum
    FROM tabelle_Files_has_tabelle_Vermerke fhv
    INNER JOIN tabelle_Vermerke v
        ON fhv.tabelle_Vermerke_idtabelle_Vermerke = v.idtabelle_Vermerke
    INNER JOIN tabelle_Vermerkuntergruppe vu
        ON v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = vu.idtabelle_Vermerkuntergruppe
    INNER JOIN tabelle_Vermerkgruppe vg
        ON vu.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = vg.idtabelle_Vermerkgruppe
    WHERE fhv.tabelle_Files_idtabelle_Files = ?
");
$stmt->bind_param('i', $imageID);
$stmt->execute();
$vermerke = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$mysqli->close();

echo json_encode([
    'status' => 'ok',
    'id' => $imageID,
    //  'name' => $base['Name'],
    'timestamp' => $base['Timestamp'],
    'projekt' => $base['Projektname'],
    'raeume' => $raeume,
    'vermerke' => $vermerke,
]);
?>