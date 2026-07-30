<?php
// getImageMeta.php – Metadaten eines Bildes (Timestamp, Projekte, Räume, Vermerke, Geräte)
require_once '../utils/_utils.php';
check_login();

header('Content-Type: application/json');

$imageID   = filter_input(INPUT_POST, 'imageID', FILTER_VALIDATE_INT);
$projectID = (int)($_SESSION['projectID'] ?? 0);

if (!$imageID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter']);
    exit;
}

$mysqli = utils_connect_sql();

// ── Basisdaten: Timestamp + Ursprungsprojekt ─────────────────────────────────
// Hinweis: Projekt-Restriktion entfällt bewusst, damit die Info auch für
// Bilder aus anderen Projekten (Karte "Fotos anderer Projekte") funktioniert.
$stmt = $mysqli->prepare("
    SELECT f.Timestamp, f.Name,
           f.tabelle_projekte_idTABELLE_Projekte AS originID,
           p.Projektname AS originProjekt
    FROM tabelle_Files f
    LEFT JOIN tabelle_projekte p
        ON f.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte
    WHERE f.idtabelle_Files = ?
");
$stmt->bind_param('i', $imageID);
$stmt->execute();
$base = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$base) {
    $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Bild nicht gefunden']);
    exit;
}

// ── Alle Projekte (Ursprung + zusätzliche Zuordnungen) ───────────────────────
$projekte = [];
$originID = (int)$base['originID'];
$projekte[] = [
    'projektID' => $originID,
    'name'      => $base['originProjekt'] ?? ('Projekt #' . $originID),
    'origin'    => true,
    'current'   => ($originID === $projectID),
];
$stmt = $mysqli->prepare("
    SELECT p.idTABELLE_Projekte AS projektID, p.Projektname
    FROM tabelle_Files_has_tabelle_Projekte fp
    INNER JOIN tabelle_projekte p ON p.idTABELLE_Projekte = fp.tabelle_projekte_idTABELLE_Projekte
    WHERE fp.tabelle_Files_idtabelle_Files = ?
    ORDER BY p.Projektname
");
$stmt->bind_param('i', $imageID);
$stmt->execute();
foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $p) {
    $pid = (int)$p['projektID'];
    $projekte[] = [
        'projektID' => $pid,
        'name'      => $p['Projektname'] ?? ('Projekt #' . $pid),
        'origin'    => false,
        'current'   => ($pid === $projectID),
    ];
}
$stmt->close();

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

// ── Geräte ───────────────────────────────────────────────────────────────────
$stmt = $mysqli->prepare("
    SELECT g.idTABELLE_Geraete AS geraetID, g.Typ, h.Hersteller
    FROM tabelle_Files_has_tabelle_Geraete fhg
    INNER JOIN tabelle_geraete g ON fhg.tabelle_idGeraet = g.idTABELLE_Geraete
    LEFT JOIN tabelle_hersteller h
        ON h.idtabelle_hersteller = g.tabelle_hersteller_idtabelle_hersteller
    WHERE fhg.tabelle_idFile = ?
    ORDER BY h.Hersteller, g.Typ
");
$stmt->bind_param('i', $imageID);
$stmt->execute();
$geraete = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$mysqli->close();

echo json_encode([
    'status'    => 'ok',
    'id'        => $imageID,
    'timestamp' => $base['Timestamp'],
    'projekt'   => $base['originProjekt'],   // Rückwärtskompatibel: Ursprungsprojekt-Name
    'projekte'  => $projekte,                // NEU: alle zugeordneten Projekte
    'raeume'    => $raeume,
    'vermerke'  => $vermerke,
    'geraete'   => $geraete,                 // NEU
]);
?>
