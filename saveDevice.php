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
	$mysqli->query("SET NAMES 'utf8'");
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 		
        
        $sql = "UPDATE `LIMET_RB`.`tabelle_geraete`
                    SET
                    `Typ` = '".filter_input(INPUT_GET, 'type')."',
                    `Kurzbeschreibung` = '".filter_input(INPUT_GET, 'kurzbeschreibung')."',
                    `Änderung` = '".date('Y-m-d')."',
                    `tabelle_hersteller_idtabelle_hersteller` = ".filter_input(INPUT_GET, 'hersteller')."
                    WHERE `idTABELLE_Geraete` = ".filter_input(INPUT_GET, 'deviceID').";";
			
				
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Gerät gespeichert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	
	$mysqli ->close();	
					
?>
