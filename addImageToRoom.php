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
        
        $sql = "INSERT INTO `LIMET_RB`.`tabelle_Files_has_tabelle_Raeume`
                (`tabelle_idfFile`,
                `tabelle_idRaeume`)
                VALUES
                (".filter_input(INPUT_GET, 'imageID').",
                ".filter_input(INPUT_GET, 'roomID').");";

	if ($mysqli->query($sql) === TRUE) {
            echo "Bild zu Raum hinzugefügt!";
	} 
	else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
