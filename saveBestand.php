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
	$mysqli->query("SET NAMES 'utf8'");
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
        
	
	if(filter_input(INPUT_GET, 'geraeteID') != 0){
		$sql = "UPDATE `LIMET_RB`.`tabelle_bestandsdaten`
			SET
			`Inventarnummer` = '".filter_input(INPUT_GET, 'inventarNr')."',
			`Seriennummer` = '".filter_input(INPUT_GET, 'serienNr')."',
			`Anschaffungsjahr` = '".filter_input(INPUT_GET, 'anschaffungsJahr')."',
                        `Aktueller Ort` =  '".filter_input(INPUT_GET, 'currentPlace')."',       
			`tabelle_geraete_idTABELLE_Geraete` = ".filter_input(INPUT_GET, 'geraeteID')."
			WHERE `idtabelle_bestandsdaten` = ".filter_input(INPUT_GET, 'bestandID').";";	
	}
	else{
		$sql = "UPDATE `LIMET_RB`.`tabelle_bestandsdaten`
			SET
			`Inventarnummer` = '".filter_input(INPUT_GET, 'inventarNr')."',
			`Seriennummer` = '".filter_input(INPUT_GET, 'serienNr')."',
			`Anschaffungsjahr` = '".filter_input(INPUT_GET, 'anschaffungsJahr')."',
                        `Aktueller Ort` =  '".filter_input(INPUT_GET, 'currentPlace')."' 
			WHERE `idtabelle_bestandsdaten` = ".filter_input(INPUT_GET, 'bestandID').";";	
		
	}
		
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Bestand ge�ndert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	
	$mysqli ->close();	
					
?>