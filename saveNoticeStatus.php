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

	$sql = "UPDATE `LIMET_RB`.`tabelle_notizen`
			SET
			`Notiz_bearbeitet` = '".$_GET["status"]."'
			WHERE idtabelle_notizen = ".$_SESSION["noticeID"].";";
		
	// Query ausführen
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Notizstatus erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>