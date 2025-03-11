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
		
        $sql = "DELETE FROM `LIMET_RB`.`tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen`
                WHERE `tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe`= ".filter_input(INPUT_GET, 'groupID')."
                AND `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen`=".filter_input(INPUT_GET, 'ansprechpersonenID').";";
        
        $sql = "DELETE FROM `LIMET_RB`.`tabelle_Vermerke_has_tabelle_ansprechpersonen`
                WHERE `tabelle_Vermerke_idtabelle_Vermerke`= ".filter_input(INPUT_GET, 'vermerkID')." AND `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen`=".filter_input(INPUT_GET, 'ansprechpersonenID').";";
        
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Zustaendigkeit entfernt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();	
					
?>
