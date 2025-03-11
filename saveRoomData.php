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
	$sql = "UPDATE `LIMET_RB`.`tabelle_räume`
			SET
			`Raumnr` = '".$_GET["raumnummer"]."',
			`Raumbezeichnung` = '".$_GET["raumbezeichnung"]."',
			`Raumbereich Nutzer` = '".$_GET["raumbereich"]."',
			`Geschoss` = '".$_GET["geschoss"]."',
			`Bauetappe` = '".$_GET["bauetappe"]."',
			`Bauabschnitt` = '".$_GET["bauteil"]."',
			`Nutzfläche` = '".$_GET["nutzflaeche"]."',
                        `TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen` = ".$_GET["funktionsteilstelle"].",   
                        `MT-relevant` = '".$_GET["MTrelevant"]."'
			WHERE `idTABELLE_Räume` = ".$_GET["ID"].";";

	if ($mysqli ->query($sql) === TRUE) {
	    echo "Raum erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();
	
	
					
?>
