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
                        `Gebietsbereich` = '".$_GET["gebiet"]."',
                        `tabelle_abteilung_idtabelle_abteilung` = ".$_GET["abteilung"].",
                        `tabelle_lieferant_idTABELLE_Lieferant` = ".$_GET["lieferant"]."
                        WHERE `idTABELLE_Ansprechpersonen` = ".$_GET["ansprechID"].";";
		
		if ($mysqli->query($sql) === TRUE) {
		    echo "Kontaktperson gespeichert!";
		    $id = $mysqli->insert_id; 
		} 
		else {
		    echo "Error1: " . $sql . "<br>" . $mysqli->error;
		}
						
		$mysqli ->close();
	}
	else{
		echo "Fehler bei der Übertragung der Parameter";
	}
?>
