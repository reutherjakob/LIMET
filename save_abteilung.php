<?php
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();
$abteilung = getPostString("abteilung");
if ($abteilung !="" ) {
    $check = $mysqli->prepare("SELECT idtabelle_abteilung FROM tabelle_abteilung WHERE Abteilung = ?");
    $check->bind_param("s", $abteilung);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Abteilung existiert bereits']);
        exit;
    }
    $stmt = $mysqli->prepare("INSERT INTO tabelle_abteilung (Abteilung) VALUES (?)");
    $stmt->bind_param("s", $abteilung);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $mysqli->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $mysqli->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Kein Name angegeben']);
}