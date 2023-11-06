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
		/* change character set to utf8 */
		if (!$mysqli->set_charset("utf8")) {
		    printf("Error loading character set utf8: %s\n", $mysqli->error);
		    exit();
		} 
		
		// Check connection
		if ($mysqli->connect_error) {
		    die("Connection failed: " . $mysqli->connect_error);
		}
		
	
		$sql = "DELETE FROM `LIMET_RB`.`tabelle_projekte_has_tabelle_ansprechpersonen`
				WHERE `TABELLE_Projekte_idTABELLE_Projekte` = ".$_SESSION["projectID"]." AND `TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen` = ".$_GET["personID"].";";
				
		if ($mysqli->query($sql) === TRUE) {
		    echo "Person erfolgreich von Projekt entfernt!"; 
		} 
		else {
		    echo "Error1: " . $sql . "<br>" . $mysqli->error;
		}
			
		$mysqli ->close();
?>
