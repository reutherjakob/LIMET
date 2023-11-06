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
	
	
	$sql = "SELECT `tabelle_elemente`.`ElementID`
				FROM `LIMET_RB`.`tabelle_elemente`
				WHERE `tabelle_elemente`.`idTABELLE_Elemente` = ".$_SESSION["elementID"].";";
	
	$result = $mysqli->query($sql);
	while($row = $result->fetch_assoc()) {
		$elementID = $row["ElementID"];
	}
	
	
	$sql = "SELECT MAX(`tabelle_geraete`.`Laufende_Nr`)
			FROM `LIMET_RB`.`tabelle_geraete`
			WHERE `tabelle_geraete`.`TABELLE_Elemente_idTABELLE_Elemente` = ".$_SESSION["elementID"].";";
	
	$result = $mysqli->query($sql);
	while($row = $result->fetch_assoc()) {
		$laufendeNr = $row["MAX(`tabelle_geraete`.`Laufende_Nr`)"];
		$laufendeNr = $laufendeNr+1;
	}
	if($laufendeNr == ""){
		$laufendeNr = 1;
	}

			
	$sql = "INSERT INTO `LIMET_RB`.`tabelle_geraete`
			(`GeraeteID`,
			`Typ`,
			`Kurzbeschreibung`,
			`Änderung`,
			`TABELLE_Elemente_idTABELLE_Elemente`,
			`Laufende_Nr`,
			`tabelle_hersteller_idtabelle_hersteller`)
			VALUES
			('".$elementID.".".$laufendeNr."',
			'".$_GET["type"]."',
			'".$_GET["kurzbeschreibung"]."',
			'".date('Y-m-d')."',
			".$_SESSION["elementID"].",
			'".$laufendeNr."',
			".$_GET["hersteller"].");";
				
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Gerät hinzugefügt! ".date('Y-m-d');
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	
	$mysqli ->close();	
					
?>
