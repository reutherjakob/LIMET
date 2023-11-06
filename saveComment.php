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
	    die("Connection failed: " . $conn->connect_error);
	}
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
	
	$sql = "UPDATE view_Raeume_has_Elemente SET view_Raeume_has_Elemente.Kurzbeschreibung = '".$_GET["comment"]."', view_Raeume_has_Elemente.Anzahl = '".$_GET["amount"]."' WHERE (((view_Raeume_has_Elemente.id)=".$_GET["id"]."))";
	
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();
	
	
					
?>
