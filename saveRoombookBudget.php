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
        
        if(filter_input(INPUT_GET, 'budgetID') === '0'){
            $sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
                    SET
                    `tabelle_projektbudgets_idtabelle_projektbudgets` = NULL 
                    WHERE `id` =  ".filter_input(INPUT_GET, 'roombookID').";";
        }
        else{
            $sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
                    SET
                    `tabelle_projektbudgets_idtabelle_projektbudgets` = ".filter_input(INPUT_GET, 'budgetID')." 
                    WHERE `id` =  ".filter_input(INPUT_GET, 'roombookID').";";
        }

	if ($mysqli ->query($sql) === TRUE) {
	    echo "Erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();
	
	
					
?>
