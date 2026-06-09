<?php
// Kein Output vor dem JSON — wichtig!
ob_start();

require_once '../utils/_utils.php';
check_login();

header('Content-Type: application/json');

$mysqli  = utils_connect_sql();
$imageID = getPostInt('imageID', 0);

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

// Wie viele Vermerke sind noch verknüpft?
$stmtCheck = $mysqli->prepare(
    "SELECT COUNT(*) FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Vermerke` WHERE `tabelle_Files_idtabelle_Files` = ?"
);
$stmtCheck->bind_param('i', $imageID);
$stmtCheck->execute();
$stmtCheck->bind_result($vermerkCount);
$stmtCheck->fetch();
$stmtCheck->close();

if ($vermerkCount > 0) {
    $mysqli->close();
    ob_end_clean();
    echo json_encode([
        'status'       => 'linked',
        'vermerkCount' => (int)$vermerkCount,
        'msg'          => "Bild ist noch in " . (int)$vermerkCount
            . " Vermerk" . ($vermerkCount > 1 ? 'en' : '')
            . " verknüpft. Bitte zuerst alle Verknüpfungen entfernen."
    ]);
    exit;
}

// Datei löschen
$baseDir    = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Images/";
$targetFile = $baseDir . basename($imageName);

if (file_exists($targetFile) && !unlink($targetFile)) {
    $mysqli->close();
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Datei konnte nicht gelöscht werden.']);
    exit;
}

// DB-Eintrag löschen
$stmtDel = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_Files` WHERE `idtabelle_Files` = ?");
$stmtDel->bind_param('i', $imageID);
$ok = $stmtDel->execute();
$err = $stmtDel->error;
$stmtDel->close();
$mysqli->close();

ob_end_clean();
if ($ok) {
    echo json_encode(['status' => 'ok', 'msg' => 'Bild gelöscht.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'DB-Fehler: ' . $err]);
}
?>