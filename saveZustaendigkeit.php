<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$name = getPostString('name');
$name = preg_replace("/[^a-zA-ZäöüÄÖÜß \-']/u", '', $name);
$name = preg_replace('/\s+/', ' ', $name);
$name = mb_substr($name, 0, 100);
if ($name === '') {
    echo json_encode(['success' => false, 'error' => 'Kein Name angegeben.']);
    $mysqli->close();
    exit;
}
$lowerName = mb_strtolower($name, 'UTF-8');
$stmt = $mysqli->prepare("SELECT idTABELLE_Projektzuständigkeiten, Zuständigkeit FROM tabelle_projektzuständigkeiten WHERE Zuständigkeit = ?");
$stmt->bind_param("s", $lowerName);
$stmt->execute();
$stmt->bind_result($existingId, $existingName);
if ($stmt->fetch()) {
    echo json_encode(['success' => true, 'id' => $existingId, 'name' => $existingName]);
    $stmt->close();
    $mysqli->close();
    exit;
}
$stmt->close();
$stmt = $mysqli->prepare(" SELECT idTABELLE_Projektzuständigkeiten, Zuständigkeit FROM tabelle_projektzuständigkeiten WHERE SOUNDEX(Zuständigkeit) = SOUNDEX(?) OR Zuständigkeit LIKE CONCAT('%', ?, '%') ");
$stmt->bind_param("ss", $name, $name);
$stmt->execute();
$stmt->bind_result($fuzzyId, $fuzzyName);
$similar = [];
while ($stmt->fetch()) {
    similar_text(mb_strtolower($fuzzyName, 'UTF-8'), $lowerName, $percent);
    if ($percent > 75) {
        $similar[] = ['id' => $fuzzyId, 'name' => $fuzzyName, 'similarity' => round($percent)];
    }
}
if (!empty($similar)) {
    usort($similar, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
    echo json_encode(['success' => false, 'error' => 'Mögliche ähnliche Zuständigkeit gefunden. Kontaktieren sie 1 Developer.', 'suggestions' => $similar]);
    $stmt->close();
    $mysqli->close();
    exit;
}
$stmt = $mysqli->prepare("INSERT INTO tabelle_projektzuständigkeiten (Zuständigkeit) VALUES (?)");
$stmt->bind_param("s", $name);
if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    echo json_encode(['success' => true, 'id' => $newId, 'name' => $name]);
} else {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Einfügen.']);
}
$stmt->close();
$mysqli->close();
