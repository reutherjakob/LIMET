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
	mysqli_query($mysqli, "SET NAMES 'utf8'");
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
	
	$sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
			SET
			`Kurzbeschreibung` = '".br2nl($_GET["comment"])."',
			`Timestamp` = '".date("Y-m-d H:i:s")."'
			WHERE `id` = ".$_SESSION["roombookID"].";";
	
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();
				
?>
