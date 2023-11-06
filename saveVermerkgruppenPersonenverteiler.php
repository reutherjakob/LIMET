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
               
        
        $sql = "UPDATE `LIMET_RB`.`tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen`
                SET
                `Verteiler` = ".filter_input(INPUT_GET, 'verteiler')."
                WHERE `tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe` = ".filter_input(INPUT_GET, 'groupID')." AND `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen` = ".filter_input(INPUT_GET, 'ansprechpersonenID').";";
        
	if ($mysqli->query($sql) === TRUE) {
            echo "Verteiler aktualisiert!";
	} 
	else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
