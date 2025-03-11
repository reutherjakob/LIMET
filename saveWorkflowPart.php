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
	
        $sql = "UPDATE `LIMET_RB`.`tabelle_lot_workflow`
                    SET
                    `Timestamp_Ist` = '".filter_input(INPUT_GET, 'dateIs')."',
                    `Timestamp_Soll` = '".filter_input(INPUT_GET, 'dateShould')."',
                    `Abgeschlossen` = '".filter_input(INPUT_GET, 'status')."',
                    `user` = '".$_SESSION["username"]."',
                    `Kommentar` = '".filter_input(INPUT_GET, 'comment')."'"
                    . " WHERE `tabelle_lose_extern_idtabelle_Lose_Extern`= ".$_SESSION["lotID"]."
                        AND `tabelle_wofklowteil_idtabelle_wofklowteil`= ".filter_input(INPUT_GET, 'workflowID').";";
        
	
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Workflow erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();
	
	
					
?>
