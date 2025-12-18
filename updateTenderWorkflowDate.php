<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$lotID = getPostInt($input['lotID'] ?? 0);
$date = getPostDate($input['date'] ?? '');
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
    $ausgabe = "Soll-Datum erfolgreich aktualisiert!";
} else {
    $ausgabe = "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
echo $ausgabe;
?>
