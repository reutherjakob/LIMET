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
	
	if($_GET["Name"] != "" && $_GET["Vorname"] != "" && $_GET["Tel"] != ""){
		
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
		
	
		$sql = "UPDATE `LIMET_RB`.`tabelle_ansprechpersonen`
			SET
			`Name` = '".$_GET["Name"]."',
			`Vorname` = '".$_GET["Vorname"]."',
			`Tel` = '".$_GET["Tel"]."',
			`Adresse` = '".$_GET["Adresse"]."',
			`PLZ` = '".$_GET["PLZ"]."',
			`Ort` = '".$_GET["Ort"]."',
			`Land` = '".$_GET["Land"]."',
			`Mail` = '".$_GET["Email"]."',
                        `Raumnr` = '".$_GET["Raumnr"]."'
			WHERE `idTABELLE_Ansprechpersonen` = ".$_GET["personID"].";";
		
		if ($mysqli->query($sql) === TRUE) {
		    echo "Personendaten gespeichert "; 
		} 
		else {
		    echo "Error1: " . $sql . "<br>" . $mysqli->error;
		}
			
		$sql = "UPDATE `LIMET_RB`.`tabelle_projekte_has_tabelle_ansprechpersonen`
				SET
				`TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten` = ".$_GET["zustaendigkeit"].",
				`tabelle_organisation_idtabelle_organisation` = ".$_GET["organisation"]."
				WHERE `TABELLE_Projekte_idTABELLE_Projekte` = ".$_SESSION["projectID"]." AND `TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen` = ".$_GET["personID"].";";
				
		if ($mysqli->query($sql) === TRUE) {
			echo "und Organisation bzw. Zuständigkeit gespeichert!";
		} 
		else {
	    	echo "Error2: " . $sql . "<br>" . $mysqli->error;
		}
		
		$mysqli ->close();
	}
	else{
		echo "Fehler bei der Verbindung";
	}
?>
