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
	$_SESSION["elementID"]=$_GET["elementID"];

	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	
	
	$sql = "SELECT `tabelle_elemente`.`Bezeichnung`,
			    `tabelle_elemente`.`ElementID`
				FROM `LIMET_RB`.`tabelle_elemente`
				WHERE `tabelle_elemente`.`idTABELLE_Elemente`= ".$_GET["elementID"].";";	
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	echo $row["ElementID"]." ".$row["Bezeichnung"];

	$mysqli ->close();
	?>