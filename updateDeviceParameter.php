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
        
        $sql = "UPDATE `LIMET_RB`.`tabelle_geraete_has_tabelle_parameter`
                    SET
                    `Wert` = '".$_GET["wert"]."',
                    `Einheit` = '".$_GET["einheit"]."'
                    WHERE `TABELLE_Geraete_idTABELLE_Geraete` = ".$_SESSION["deviceID"]." AND `TABELLE_Parameter_idTABELLE_Parameter` = ".$_GET["parameterID"].";";

	if ($mysqli ->query($sql) === TRUE) {
	    echo "Parameter erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();	
					
?>
