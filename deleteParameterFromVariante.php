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
		
	$sql = " DELETE FROM `LIMET_RB`.`tabelle_projekt_elementparameter`
			WHERE `tabelle_projekte_idTABELLE_Projekte`=".$_SESSION["projectID"]."
			AND `tabelle_elemente_idTABELLE_Elemente`=".$_SESSION["elementID"]."
			AND `tabelle_parameter_idTABELLE_Parameter`=".$_GET["parameterID"]."
			AND `tabelle_Varianten_idtabelle_Varianten`=".$_GET["variantenID"].";";
	
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Parameter entfernt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();	
					
?>
