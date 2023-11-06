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
        
        $sql = "UPDATE `LIMET_RB`.`tabelle_projekte`
                SET
                `Bettenanzahl` = '".filter_input(INPUT_GET, 'betten')."',
                `BGF` = '".filter_input(INPUT_GET, 'bgf')."',
                `NF` = '".filter_input(INPUT_GET, 'nf')."',
                `Aktiv` = '".filter_input(INPUT_GET, 'active')."',
                `Neubau` = '".filter_input(INPUT_GET, 'neubau')."',
                `TABELLE_Planungsphasen_idTABELLE_Planungsphasen` = ".filter_input(INPUT_GET, 'planungsphase').",
                `Ausfuehrung` = '".filter_input(INPUT_GET, 'bearbeitung')."'
                WHERE `idTABELLE_Projekte` = ".$_SESSION["projectID"].";";
        
	if ($mysqli->query($sql) === TRUE) {
            echo "Projekt aktualisiert!";
	} 
	else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
