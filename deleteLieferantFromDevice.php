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
		
	
		$sql = "DELETE FROM `LIMET_RB`.`tabelle_geraete_has_tabelle_lieferant`
				WHERE `tabelle_geraete_idTABELLE_Geraete` = ".$_SESSION["deviceID"]." AND `tabelle_lieferant_idTABELLE_Lieferant` = ".$_GET["lieferantID"].";";
				
		if ($mysqli->query($sql) === TRUE) {
		    echo "Lieferant von Gerät entfernt!"; 
		} 
		else {
		    echo "Error1: " . $sql . "<br>" . $mysqli->error;
		}
			
		$mysqli ->close();
?>
