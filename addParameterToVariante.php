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
		

	$sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_elementparameter`
			(`tabelle_projekte_idTABELLE_Projekte`,
			`tabelle_elemente_idTABELLE_Elemente`,
			`tabelle_parameter_idTABELLE_Parameter`,
			`tabelle_Varianten_idtabelle_Varianten`,
			`Wert`,
			`Einheit`,
			`tabelle_planungsphasen_idTABELLE_Planungsphasen`)
			VALUES
			(".$_SESSION["projectID"].",
			".$_SESSION["elementID"].",
			".$_GET["parameterID"].",
			".$_GET["variantenID"].",
			'',
			'',
			1);";

	if ($mysqli ->query($sql) === TRUE) {
	    echo "Parameter hinzugefügt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();	
					
?>
