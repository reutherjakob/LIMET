<?php
// 25 FX
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();

$workflowID = getPostInt('workflowID', 0);
$lotID = $_SESSION["lotID"] ?? 0; // Ensure session value is available and trusted


$sql = "INSERT INTO tabelle_lot_workflow (
            tabelle_wofklowteil_idtabelle_wofklowteil,
            tabelle_lose_extern_idtabelle_Lose_Extern,
            tabelle_workflow_idtabelle_workflow
        )
        SELECT 
            tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil,
            ?, 
            tabelle_workflow.idtabelle_workflow
        FROM 
            tabelle_workflow 
            INNER JOIN tabelle_workflow_has_tabelle_wofklowteil 
            ON tabelle_workflow.idtabelle_workflow = tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow
        WHERE tabelle_workflow.idtabelle_workflow = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $lotID, $workflowID);



if ($stmt->execute()) {
    echo "Erfolg!";//  "Workflow erfolgreich zu Los hinzugefügt!";
} else {
    echo "Error: " . $sql ." ". $workflowID. "<br>" . $mysqli->error;
}

$mysqli->close();
?>
