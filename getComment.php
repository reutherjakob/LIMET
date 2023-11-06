<?php
session_start();
?>
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
   
   function br2nl($string){
	$return= str_replace(array("<br/>"), "\n", $string);
	return $return;
	}

?>
<?php
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	mysqli_query($mysqli, "SET NAMES 'utf8'");
	
	if($_GET["commentID"] != ""){
		$_SESSION["roombookID"]=$_GET["commentID"];
	}
	
	$sql = "SELECT tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
			FROM tabelle_räume_has_tabelle_elemente
			WHERE (((tabelle_räume_has_tabelle_elemente.id)=".$_GET["commentID"]."));";
	
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	echo br2nl($row["Kurzbeschreibung"]);
	$mysqli ->close();
?>			
