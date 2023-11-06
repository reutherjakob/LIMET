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
	
	
	$sql = "INSERT INTO `LIMET_RB`.`tabelle_BO_Taetigkeiten`
			(`Taetigkeit_Deutsch`,
			`tabelle_BO_Untergruppe_idtabelle_BO_Untergruppe`)
			VALUES
			('".$_GET["boTaetigkeit"]."',
			".$_GET["boGruppe"].");";
	

	if ($mysqli ->query($sql) === TRUE) {
	    echo "Tätigkeit hinzugefügt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();	
					
?>
