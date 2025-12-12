<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare("UPDATE `LIMET_RB`.`tabelle_lot_workflow`
                    SET
                    `Timestamp_Ist` = ?,
                    `Timestamp_Soll` = ?,
                    `Abgeschlossen` = ?,
                    `user` = ?,
                    `Kommentar` = ?
                    WHERE `tabelle_lose_extern_idtabelle_Lose_Extern` = ?
                    AND `tabelle_wofklowteil_idtabelle_wofklowteil` = ?");

$dateIs = getPostDate('dateIs');
$dateShould = getPostDate('dateShould');
$status = getPostString('status');
$username = $_SESSION["username"];
$comment = getPostString('comment');
$lotID = $_SESSION["lotID"];
$workflowID = getPostInt('workflowID');

$stmt->bind_param("sssssii", $dateIs, $dateShould, $status, $username, $comment, $lotID, $workflowID);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Workflow erfolgreich aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}
$mysqli->close();
