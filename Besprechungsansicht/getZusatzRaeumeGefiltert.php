<?php
include "../utils/_utils.php";
check_login();

$projectID = intval($_GET['projectID'] ?? $_POST['projectID'] ?? 0);
$mtRelevant = intval($_GET['mtRelevant'] ?? $_POST['mtRelevant'] ?? 1);
$ohneEntfallen = intval($_GET['entfallen'] ?? $_POST['entfallen'] ?? 1);

header('Content-Type: application/json; charset=UTF-8');

if (!$projectID) {
    echo json_encode([]);
    exit;
}

$conn = utils_connect_sql();

$sql = "SELECT idTABELLE_Räume AS id, 
               CONCAT(Raumnr, ' - ', Raumbezeichnung, ' - ', `Raumbereich Nutzer`) AS text
        FROM tabelle_räume
        WHERE tabelle_projekte_idTABELLE_Projekte = ?";
$params = [$projectID];
$types = 'i';

if ($mtRelevant) {
    $sql .= " AND `MT-relevant` = 1";
}
if ($ohneEntfallen) {
    $sql .= " AND (`Entfallen` IS NULL OR `Entfallen` = 0)";
}
$sql .= " ORDER BY Raumnr";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$raeume = [];
while ($row = $result->fetch_assoc()) {
    $raeume[] = $row;
}
$stmt->close();

echo json_encode(['results' => $raeume]);
?>
