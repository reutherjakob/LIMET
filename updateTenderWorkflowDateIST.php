<?php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$date = getPostDate('date');         
$lotID = getPostInt('lotID');        
$workflowID = getPostInt('workflowID'); 
$workflowTeilID = getPostInt('workflowTeilID'); 

if ($lotID > 0 && $date) {
    $sql = "UPDATE LIMET_RB.tabelle_lot_workflow SET Timestamp_Ist=? 
          WHERE tabelle_lose_extern_idtabelle_Lose_Extern=? 
          AND tabelle_workflow_idtabelle_workflow=? 
          AND tabelle_wofklowteil_idtabelle_wofklowteil=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("siii", $date, $lotID, $workflowID, $workflowTeilID);
    $ausgabe = $stmt->execute() ? "Ist-Datum erfolgreich aktualisiert!" : "Error: " . $stmt->error;
    $stmt->close();
} else $ausgabe = "Fehlende Daten: lotID=$lotID, date='$date'";
echo $ausgabe;
$mysqli->close();
?>
