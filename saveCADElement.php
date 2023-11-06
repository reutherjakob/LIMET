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

	if($_GET["id"] != "" && $_GET["selectCAD_notwendig"] != "" && $_GET["selectCAD_dwg_vorhanden"] != "" && $_GET["selectCAD_dwg_kontrolliert"] != "" && $_GET["selectCAD_familie_vorhanden"] != "" && $_GET["selectCAD_familie_kontrolliert"] != ""){
		
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
		else{
			
			if($_GET["selectCAD_notwendig"]=="Ja"){
				$cadnotwendig = 1;
			}
			else{
				$cadnotwendig = 0;
			}
			if($_GET["selectCAD_dwg_vorhanden"]=="Ja"){
				$dwgvorhanden = 1;
			}
			else{
				$dwgvorhanden = 0;
			}

			if($_GET["selectCAD_dwg_kontrolliert"]=="Nicht geprüft"){
				$dwgkontrollliert = 0;
			}
			else{
				if($_GET["selectCAD_dwg_kontrolliert"]=="Freigegeben"){
					$dwgkontrollliert = 1;
				}
				else{
					$dwgkontrollliert = 2;
				}
			}
			if($_GET["selectCAD_familie_vorhanden"]=="Ja"){
				$familievorhanden = 1;
			}
			else{
				$familievorhanden = 0;
			}
			
			if($_GET["selectCAD_familie_kontrolliert"]=="Nicht geprüft"){
				$familiekontrolliert = 0;
			}
			else{
				if($_GET["selectCAD_familie_kontrolliert"]=="Freigegeben"){
					$familiekontrolliert = 1;
				}
				else{
					$familiekontrolliert = 2;
				}
			}
			
			
			$sql = "UPDATE `LIMET_RB`.`tabelle_elemente` 
			SET `CAD_notwendig` = '".$cadnotwendig."',
			`CAD_dwg_vorhanden` = '".$dwgvorhanden."',
			`CAD_dwg_kontrolliert` = '".$dwgkontrollliert."',
			`CAD_familie_vorhanden` = '".$familievorhanden."',
			`CAD_familie_kontrolliert` = '".$familiekontrolliert."',
			`CAD_Kommentar` = '".$_GET["CADcomment"]."' 
			WHERE `idTABELLE_Elemente` = ".$_GET["id"];	
				
			if ($mysqli ->query($sql) === TRUE) {
			    echo "Erfolgreich aktualisiert!";
			} else {
			    echo "Error: " . $sql . "<br>" . $mysqli->error;
			}
			
		}
		
		$mysqli ->close();
	}
	else{
		echo "Keine korrekten Werte übergeben!";
	}
?>
