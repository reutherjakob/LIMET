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
	

	//echo $_GET["Notiz"]." ".date('Y-m-d')." ".$_SESSION["username"]." ".$_GET["Kategorie"]." ".$_GET["roomID"];
	
	if($_SESSION["deviceID"] != "" && $_GET["lieferantenID"] != ""){
		
		
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
		
                
		$sql = "INSERT INTO `LIMET_RB`.`tabelle_geraete_has_tabelle_lieferant`
                        (`tabelle_geraete_idTABELLE_Geraete`,
                        `tabelle_lieferant_idTABELLE_Lieferant`)
                        VALUES
                        (".$_SESSION["deviceID"].",
                        ".$_GET["lieferantenID"].");";
                
		if ($mysqli->query($sql) === TRUE) {
		    echo "Lieferant zu Gerät hinzugefügt!";		
		} 
		else {
		    echo "Error1: " . $sql . "<br>" . $mysqli->error;
		}
						
		$mysqli ->close();
	}
	else{
		echo "Fehler bei der Verbindung";
	}
?>
