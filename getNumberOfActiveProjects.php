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
	$_SESSION["variantenID"]=$_GET["variantenID"];

	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	
	$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=".$_GET["variantenID"].") AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=".$_SESSION["elementID"].") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."));";		    
	
        $sql = "SELECT Count(view_Projekte.idTABELLE_Projekte) AS Anzahl
                FROM view_Projekte
                WHERE (((view_Projekte.Aktiv)=1));";
        
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	echo $row["Anzahl"];

	$mysqli ->close();
?>