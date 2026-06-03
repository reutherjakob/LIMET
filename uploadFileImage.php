<?php
ob_start();

require_once "utils/_utils.php";
check_login();

header('Content-Type: application/json');

$image     = $_POST['fileUpload'] ?? null;
$vermerkID = getPostInt('vermerkID', 0);
$projectID = (int)($_SESSION["projectID"] ?? 0);

if (!$image || !$projectID) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Fehlende Daten.']);
    exit;
}

// Strip base64 prefix
$image = preg_replace('/^data:image\/[a-zA-Z+]+;base64,/', '', $image);
$image = str_replace(' ', '+', $image);

// Validieren: muss valides base64 sein
if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $image)) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Bilddaten (kein base64).']);
    exit;
}

$data = base64_decode($image, true);
if ($data === false || strlen($data) < 100) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Bilddaten konnten nicht dekodiert werden.']);
    exit;
}

// Zieldatei
$unique   = uniqid('', true);
$filename = "Image_{$projectID}_{$unique}.jpeg";
$file     = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Images/{$filename}";

$written = file_put_contents($file, $data);
if ($written === false || $written < 100) {
    // Datei aufräumen falls halb geschrieben
    if (file_exists($file)) unlink($file);
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Datei konnte nicht gespeichert werden.']);
    exit;
}

// INSERT tabelle_Files
$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("
    INSERT INTO `LIMET_RB`.`tabelle_Files`
        (`tabelle_projekte_idTABELLE_Projekte`, `tabelle_filetype_id`, `Timestamp`, `Name`)
    VALUES (?, 1, NOW(), ?)
");
$stmt->bind_param("is", $projectID, $filename);

if (!$stmt->execute()) {
    $err = $stmt->error;
    $stmt->close();
    $mysqli->close();
    // Datei wieder löschen wenn DB-Eintrag fehlschlägt
    if (file_exists($file)) unlink($file);
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'DB-Fehler: ' . $err]);
    exit;
}

$fileID = $mysqli->insert_id;
$stmt->close();

// Optional: Vermerk verknüpfen
if ($vermerkID > 0) {
    $stmt2 = $mysqli->prepare("
        INSERT INTO `LIMET_RB`.`tabelle_Files_has_tabelle_Vermerke`
            (`tabelle_Files_idtabelle_Files`, `tabelle_Vermerke_idtabelle_Vermerke`)
        VALUES (?, ?)
    ");
    $stmt2->bind_param("ii", $fileID, $vermerkID);
    if (!$stmt2->execute()) {
        $err = $stmt2->error;
        $stmt2->close();
        $mysqli->close();
        ob_end_clean();
        echo json_encode(['status' => 'error', 'msg' => 'Bild gespeichert, Verknüpfung fehlgeschlagen: ' . $err]);
        exit;
    }
    $stmt2->close();
}

$mysqli->close();
ob_end_clean();
// linked_vermerk = true → JS kann Galerie + Vermerk-Thumbnails neu laden
echo json_encode([
    'status'         => 'ok',
    'msg'            => 'Bild hochgeladen.',
    'linked_vermerk' => $vermerkID > 0,
    'file_id'        => $fileID
]);
?>