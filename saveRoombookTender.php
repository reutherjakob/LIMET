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
	    die("Connection failed: " . $mysqli->connect_error);
	}
	mysqli_query($mysqli, "SET NAMES 'utf8'");
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
	
	if($_GET["losExtern"] == "0"){
		$sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
				SET
                                `Standort` ='".$_GET["standort"]."',
                                `Verwendung`='".$_GET["verwendung"]."',
				`Neu/Bestand` = '".$_GET["bestand"]."',
				`Anzahl` = '".$_GET["amount"]."',
				`Kurzbeschreibung` = '".$_GET["comment"]."',
				`tabelle_Lose_Extern_idtabelle_Lose_Extern` = NULL,
				`Timestamp` = '".date("Y-m-d H:i:s")."'
				WHERE `id` = ".$_GET["roombookID"].";";
	}
	else{
		$sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
				SET
                                `Standort` ='".$_GET["standort"]."',
                                `Verwendung`='".$_GET["verwendung"]."',
				`Neu/Bestand` = '".$_GET["bestand"]."',
				`Anzahl` = '".$_GET["amount"]."',
				`Kurzbeschreibung` = '".$_GET["comment"]."',
				`tabelle_Lose_Extern_idtabelle_Lose_Extern` = ".$_GET["losExtern"].",
				`Timestamp` = '".date("Y-m-d H:i:s")."'
				WHERE `id` = ".$_GET["roombookID"].";";
	}
	
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Erfolgreich aktualisiert!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	$mysqli ->close();

?>
