<?php
session_start();

if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }

	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	if ($mysqli ->connect_error) {
	    die("Connection failed: " . $mysqli->connect_error);
	}
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
	
	$sql = "UPDATE tabelle_räume SET tabelle_räume.`".filter_input(INPUT_GET, 'column')."` = '".filter_input(INPUT_GET, 'value')."' WHERE (((tabelle_räume.idTABELLE_Räume)=".filter_input(INPUT_GET, 'roomID')."))";
	
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();
							
?>
