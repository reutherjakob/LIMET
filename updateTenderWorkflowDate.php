<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();


$mysqli = utils_connect_sql();

$lotID = getPostInt('lotID');
$date = getPostDate('date');
$workflowID = getPostInt('workflowID');
$workflowTeilID = getPostInt('workflowTeilID');

$sql1 = "UPDATE `LIMET_RB`.`tabelle_lot_workflow`
    SET `Timestamp_Soll` = ?
    WHERE `tabelle_lose_extern_idtabelle_Lose_Extern` = ?
    AND `tabelle_workflow_idtabelle_workflow` = ?
    AND `tabelle_wofklowteil_idtabelle_wofklowteil` = ?";

$stmt = $mysqli->prepare($sql1);
$stmt->bind_param("siii", $date, $lotID, $workflowID, $workflowTeilID);

if ($stmt->execute()) {
    $ausgabe = "Soll-Datum erfolgreich aktualisiert!"; //  \n LosId: " . $lotID . ", Datum: " . $date . ", Workflow ID" . $workflowID;
} else {
    $ausgabe = "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
echo $ausgabe;
?>
