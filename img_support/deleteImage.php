<?php
// Kein Output vor dem JSON — wichtig!
ob_start();

require_once '../utils/_utils.php';
check_login();

header('Content-Type: application/json');

$mysqli  = utils_connect_sql();
$imageID = getPostInt('imageID', 0);
$confirm = getPostInt('confirm', 0);   // 0 = nur prüfen (Dry-Run), 1 = wirklich löschen

if ($imageID === 0) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Bild-ID.']);
    exit;
}

// Dateiname holen
$stmt = $mysqli->prepare("SELECT `Name` FROM `LIMET_RB`.`tabelle_Files` WHERE `idtabelle_Files` = ?");
$stmt->bind_param('i', $imageID);
$stmt->execute();
$stmt->bind_result($imageName);
if (!$stmt->fetch()) {
    $stmt->close();
    $mysqli->close();
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Bild nicht gefunden.']);
    exit;
}
$stmt->close();

// ── Sperre 1: Vermerk-Verknüpfungen ──────────────────────────────────────────
$stmtV = $mysqli->prepare(
    "SELECT COUNT(*) FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Vermerke` WHERE `tabelle_Files_idtabelle_Files` = ?"
);
$stmtV->bind_param('i', $imageID);
$stmtV->execute();
$stmtV->bind_result($vermerkCount);
$stmtV->fetch();
$stmtV->close();

// ── Sperre 2: Zuordnung zu ANDEREN Projekten ─────────────────────────────────
// (tabelle_Files_has_tabelle_Projekte enthält nur Zusatz-Projekte, nie das Ursprungsprojekt)
$stmtP = $mysqli->prepare(
    "SELECT COUNT(*) FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Projekte` WHERE `tabelle_Files_idtabelle_Files` = ?"
);
$stmtP->bind_param('i', $imageID);
$stmtP->execute();
$stmtP->bind_result($projektCount);
$stmtP->fetch();
$stmtP->close();

if ($vermerkCount > 0 || $projektCount > 0) {
    $mysqli->close();
    ob_end_clean();

    $teile = [];
    if ($projektCount > 0) $teile[] = (int)$projektCount . " weiteren Projekt" . ($projektCount > 1 ? 'en' : '');
    if ($vermerkCount > 0) $teile[] = (int)$vermerkCount . " Vermerk" . ($vermerkCount > 1 ? 'en' : '');

    echo json_encode([
        'status'       => 'linked',
        'projektCount' => (int)$projektCount,
        'vermerkCount' => (int)$vermerkCount,
        'msg'          => 'Bild ist noch in ' . implode(' und ', $teile)
            . ' verknüpft und kann nicht gelöscht werden. Bitte zuerst die Zuordnungen entfernen.'
    ]);
    exit;
}

// ── Nur prüfen? Dann hier stoppen (noch NICHT löschen) ───────────────────────
if ($confirm !== 1) {
    $mysqli->close();
    ob_end_clean();
    echo json_encode(['status' => 'confirm', 'msg' => 'Bild kann gelöscht werden.']);
    exit;
}

// ── Ab hier: wirklich löschen ────────────────────────────────────────────────
$baseDir    = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Images/";
$targetFile = $baseDir . basename($imageName);

if (file_exists($targetFile) && !unlink($targetFile)) {
    $mysqli->close();
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Datei konnte nicht gelöscht werden.']);
    exit;
}

// Verbleibende Verknüpfungen (Räume / Geräte) aufräumen
$stmtR = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Raeume` WHERE `tabelle_idfFile` = ?");
$stmtR->bind_param('i', $imageID);
$stmtR->execute();
$stmtR->close();

$stmtG = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Geraete` WHERE `tabelle_idFile` = ?");
$stmtG->bind_param('i', $imageID);
$stmtG->execute();
$stmtG->close();

// DB-Eintrag löschen
$stmtDel = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_Files` WHERE `idtabelle_Files` = ?");
$stmtDel->bind_param('i', $imageID);
$ok  = $stmtDel->execute();
$err = $stmtDel->error;
$stmtDel->close();
$mysqli->close();

ob_end_clean();
echo json_encode($ok
    ? ['status' => 'ok',    'msg' => 'Bild gelöscht.']
    : ['status' => 'error', 'msg' => 'DB-Fehler: ' . $err]);
?>