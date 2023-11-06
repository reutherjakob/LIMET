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
		
		$sql = "INSERT INTO `tabelle_ansprechpersonen`
				(`Name`,
				`Vorname`,
				`Tel`,
				`Adresse`,
				`PLZ`,
				`Ort`,
				`Land`,
				`Mail`,
                                `Gebietsbereich`,
                                `tabelle_abteilung_idtabelle_abteilung`,
                                `tabelle_lieferant_idTABELLE_Lieferant`)
				VALUES
				('".$_GET["Name"]."',
				'".$_GET["Vorname"]."',
				'".$_GET["Tel"]."',
				'".$_GET["Adresse"]."',
				'".$_GET["PLZ"]."',
				'".$_GET["Ort"]."',
				'".$_GET["Land"]."',
				'".$_GET["Email"]."',
                                '".$_GET["gebiet"]."',"
                                .$_GET["abteilung"]."," 
                                .$_GET["lieferant"].");";
		
		if ($mysqli->query($sql) === TRUE) {
		    echo "Kontaktperson hinzugefügt!";
		    $id = $mysqli->insert_id; 
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
