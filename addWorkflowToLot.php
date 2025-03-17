<?php
session_start();
?>

<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
?>

<?php
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	if ($mysqli ->connect_error) {
	    die("Connection failed: " . $mysqli->connect_error);
	}
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
	
        $sql = "INSERT INTO tabelle_lot_workflow ( tabelle_wofklowteil_idtabelle_wofklowteil, tabelle_lose_extern_idtabelle_Lose_Extern, tabelle_workflow_idtabelle_workflow )
                SELECT tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil, ".$_SESSION["lotID"].", tabelle_workflow.idtabelle_workflow
                FROM tabelle_workflow INNER JOIN tabelle_workflow_has_tabelle_wofklowteil ON tabelle_workflow.idtabelle_workflow = tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow
                WHERE (((tabelle_workflow.idtabelle_workflow)=".filter_input(INPUT_GET, 'workflowID')."));";
        
        
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Erfolg!";//  "Workflow erfolgreich zu Los hinzugefügt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();
	
	
					
?>
