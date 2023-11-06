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
		
	$sql = "INSERT INTO `tabelle_projekte_has_tabelle_ansprechpersonen` (`TABELLE_Projekte_idTABELLE_Projekte`,`TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen`,`TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten`,`tabelle_organisation_idtabelle_organisation`)
			VALUES (".$_SESSION["projectID"].",".$_GET["personID"].",".$_GET["zustaendigkeit"].",".$_GET["organisation"].");";
			
	if ($mysqli->query($sql) === TRUE) {
		echo "Person zu Projekt hinzugefügt!";
	} 
	else {
    	echo "Error2: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
