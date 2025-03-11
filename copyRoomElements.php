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
	    die("Connection failed: " . $mysqli->connect_error);
	}
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
		
	
	$roomIDs = $_GET["rooms"];
	$ausgabe = "";
	foreach ($roomIDs as $valueOfRoomID) {
            $sql = "INSERT INTO `tabelle_räume_has_tabelle_elemente`
                        (`TABELLE_Räume_idTABELLE_Räume`,
                        `TABELLE_Elemente_idTABELLE_Elemente`,
                        `Neu/Bestand`,
                        `Anzahl`,
                        `Standort`,
                        `Verwendung`,
                        `Timestamp`,
                        `tabelle_Varianten_idtabelle_Varianten`)
                        SELECT ".$valueOfRoomID.",
                            `tabelle_räume_has_tabelle_elemente`.`TABELLE_Elemente_idTABELLE_Elemente`,
                            `tabelle_räume_has_tabelle_elemente`.`Neu/Bestand`,
                            `tabelle_räume_has_tabelle_elemente`.`Anzahl`,
                            `tabelle_räume_has_tabelle_elemente`.`Standort`,
                            `tabelle_räume_has_tabelle_elemente`.`Verwendung`,
                            '".date("Y-m-d H:i:s")."',    
                            `tabelle_räume_has_tabelle_elemente`.`tabelle_Varianten_idtabelle_Varianten`
                        FROM `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
                        WHERE `tabelle_räume_has_tabelle_elemente`.`TABELLE_Räume_idTABELLE_Räume` = ".$_SESSION["roomID"].";";                    	
	
		if ($mysqli ->query($sql) === TRUE) {
		    $ausgabe = $ausgabe . "Raum ".$valueOfRoomID." erfolgreich aktualisiert! \n";
		} else {
		    $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
		}
	}
	$mysqli ->close();	
	
	echo $ausgabe;
	
					
?>
