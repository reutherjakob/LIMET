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
	    die("Connection failed: " . $conn->connect_error);
	}
	$mysqli->query("SET NAMES 'utf8'");
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
	
        
	if(filter_input(INPUT_GET, 'gereatID') != 0){
		$sql = "INSERT INTO `LIMET_RB`.`tabelle_bestandsdaten`
			(`Inventarnummer`,
			`Seriennummer`,
			`Anschaffungsjahr`,
			`tabelle_räume_has_tabelle_elemente_id`,
			`tabelle_geraete_idTABELLE_Geraete`,
                        `Aktueller Ort`)
			VALUES
			('".filter_input(INPUT_GET, 'inventarNr')."',
			'".filter_input(INPUT_GET, 'serienNr')."',
			'".filter_input(INPUT_GET, 'anschaffungsJahr')."',
			".$_SESSION["roombookID"].",
			".filter_input(INPUT_GET, 'gereatID').",
                        '".filter_input(INPUT_GET, 'currentPlace')."');";
	}
	else{
		$sql = "INSERT INTO `LIMET_RB`.`tabelle_bestandsdaten`
			(`Inventarnummer`,
			`Seriennummer`,
			`Anschaffungsjahr`,
			`tabelle_räume_has_tabelle_elemente_id`,
			`tabelle_geraete_idTABELLE_Geraete`,
                        `Aktueller Ort`)
			VALUES
			('".filter_input(INPUT_GET, 'inventarNr')."',
			'".filter_input(INPUT_GET, 'serienNr')."',
			'".filter_input(INPUT_GET, 'anschaffungsJahr')."',
			".$_SESSION["roombookID"].",
			NULL,
                        '".filter_input(INPUT_GET, 'currentPlace')."');";
	}	
	
	
			
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Bestand hinzugefügt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	
	$mysqli ->close();	
					
?>