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
	

	//echo $_GET["Notiz"]." ".date('Y-m-d')." ".$_SESSION["username"]." ".$_GET["Kategorie"]." ".$_GET["roomID"];
	
	if($_GET["roomID"] != "" && $_GET["Notiz"] != "" && $_GET["Kategorie"] != ""){
		
		
		$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
		/* change character set to utf8 */
		if (!$mysqli->set_charset("utf8")) {
		    printf("Error loading character set utf8: %s\n", $mysqli->error);
		    exit();
		} 
		
		// Check connection
		if ($mysqli->connect_error) {
		    die("Connection failed: " . $mysqli->connect_error);
		}
		
		$sql = "INSERT INTO tabelle_notizen (Notiz,Datum,User,Kategorie,tabelle_räume_idTABELLE_Räume) Values('".$_GET["Notiz"]."','".date('Y-m-d')."','".$_SESSION["username"]."','".$_GET["Kategorie"]."',".$_GET["roomID"].")";		
		if ($mysqli->query($sql) === TRUE) {
		    echo "Erfolgreich gespeichert!";
		} else {
		    echo "Error: " . $sql . "<br>" . $mysqli->error;
		}
		
		$mysqli ->close();
	}
	else{
		echo "test";
	}
?>
