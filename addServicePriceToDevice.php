<?php
session_start();

function br2nl($string){
$return= str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br/>", $string);
return $return;
}

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
	    die("Connection failed: " . $conn->connect_error);
	}
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
	
        $projectID = filter_input(INPUT_GET, 'project');
        if ($projectID == 0){
            $projectID = "NULL";
        }
        
        $sql = "INSERT INTO `LIMET_RB`.`tabelle_wartungspreise`
                (
                    `WartungspreisProJahr`,
                    `Menge`,
                    `Wartungsart`,
                    `Info`,
                    `Datum`,
                    `tabelle_geraete_idTABELLE_Geraete`,
                    `tabelle_lieferant_idTABELLE_Lieferant`,
                    `tabelle_projekte_idTABELLE_Projekte`)
                VALUES
                (
                '".filter_input(INPUT_GET, 'wartungspreis')."',
                '".filter_input(INPUT_GET, 'menge')."',
                '".filter_input(INPUT_GET, 'wartungsart')."',
                '".filter_input(INPUT_GET, 'info')."',
                '".date("Y-m-d", strtotime(filter_input(INPUT_GET, 'date')))."',
                ".$_SESSION["deviceID"].",
                '".filter_input(INPUT_GET, 'lieferant')."',
                ".$projectID.");";
					
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Wartungspreis zu Gerät hinzugefügt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
		
	$mysqli ->close();	
					
?>
