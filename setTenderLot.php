<?php
session_start();

if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }

	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	if ($mysqli ->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
        
        if($_GET["lotID"] != ""){
            $_SESSION["lotID"]=$_GET["lotID"];
        }
            
            if(filter_input(INPUT_GET, 'mkf')==0){
                if(strlen($_GET["lotSum"]) > 0){	
                    if(filter_input(INPUT_GET, 'lotAuftragnehmer')==0){
                        $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
                                        SET
                                        `LosNr_Extern` = '".$_GET["losNr"]."',
                                        `LosBezeichnung_Extern` = '".$_GET["losName"]."',
                                        `Ausführungsbeginn` = '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                        `Vergabesumme`= '".$_GET["lotSum"]."',
                                        `Vergabe_abgeschlossen`='".$_GET["lotVergabe"]."',
                                        `Versand_LV`='".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                        `Verfahren`='".$_GET["lotVerfahren"]."',
                                        `Bearbeiter`='".$_GET["lotLVBearbeiter"]."',
                                        `Notiz` = '".$_GET["lotNotice"]."',
                                        `Kostenanschlag` = '".$_GET["kostenanschlag"]."',
                                        `Budget` = '".$_GET["budget"]."'
                                        WHERE `idtabelle_Lose_Extern` = ".$_SESSION["lotID"].";";
                    }
                    else{
                        $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
                                        SET
                                        `LosNr_Extern` = '".$_GET["losNr"]."',
                                        `LosBezeichnung_Extern` = '".$_GET["losName"]."',
                                        `Ausführungsbeginn` = '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                        `Vergabesumme`= '".$_GET["lotSum"]."',
                                        `Vergabe_abgeschlossen`='".$_GET["lotVergabe"]."',
                                        `Versand_LV`='".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                        `Verfahren`='".$_GET["lotVerfahren"]."',
                                        `Bearbeiter`='".$_GET["lotLVBearbeiter"]."',
                                        `Notiz` = '".$_GET["lotNotice"]."',
                                        `tabelle_lieferant_idTABELLE_Lieferant` = ".filter_input(INPUT_GET, 'lotAuftragnehmer').",
                                        `Kostenanschlag` = '".$_GET["kostenanschlag"]."',
                                        `Budget` = '".$_GET["budget"]."'
                                        WHERE `idtabelle_Lose_Extern` = ".$_SESSION["lotID"].";";
                    }
                }
                else{
                    if(filter_input(INPUT_GET, 'lotAuftragnehmer')==0){
                        $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
                                        SET
                                        `LosNr_Extern` = '".$_GET["losNr"]."',
                                        `LosBezeichnung_Extern` = '".$_GET["losName"]."',
                                        `Ausführungsbeginn` = '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                        `Vergabe_abgeschlossen`='".$_GET["lotVergabe"]."',
                                        `Versand_LV`='".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                        `Verfahren`='".$_GET["lotVerfahren"]."',
                                        `Bearbeiter`='".$_GET["lotLVBearbeiter"]."',
                                        `Notiz` = '".$_GET["lotNotice"]."',
                                        `Kostenanschlag` = '".$_GET["kostenanschlag"]."',
                                        `Budget` = '".$_GET["budget"]."'
                                        WHERE `idtabelle_Lose_Extern` = ".$_SESSION["lotID"].";";
                    }
                    else{
                        $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
                                        SET
                                        `LosNr_Extern` = '".$_GET["losNr"]."',
                                        `LosBezeichnung_Extern` = '".$_GET["losName"]."',
                                        `Ausführungsbeginn` = '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                        `Vergabe_abgeschlossen`='".$_GET["lotVergabe"]."',
                                        `Versand_LV`='".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                        `Verfahren`='".$_GET["lotVerfahren"]."',
                                        `Bearbeiter`='".$_GET["lotLVBearbeiter"]."',
                                        `Notiz` = '".$_GET["lotNotice"]."',                                    
                                        `tabelle_lieferant_idTABELLE_Lieferant` = ".filter_input(INPUT_GET, 'lotAuftragnehmer').",
                                        `Kostenanschlag` = '".$_GET["kostenanschlag"]."',
                                        `Budget` = '".$_GET["budget"]."'
                                        WHERE `idtabelle_Lose_Extern` = ".$_SESSION["lotID"].";";
                    }
                }
        }
        else{
                if(strlen($_GET["lotSum"]) > 0){	
                    if(filter_input(INPUT_GET, 'lotAuftragnehmer')==0){
                        $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
                                        SET
                                        `Ausführungsbeginn` = '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                        `Vergabesumme`= '".$_GET["lotSum"]."',
                                        `Vergabe_abgeschlossen`='".$_GET["lotVergabe"]."',
                                        `Versand_LV`='".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                        `Bearbeiter`='".$_GET["lotLVBearbeiter"]."',
                                        `Notiz` = '".$_GET["lotNotice"]."',
                                        `Kostenanschlag` = '".$_GET["kostenanschlag"]."',
                                        `Budget` = '".$_GET["budget"]."'
                                        WHERE `idtabelle_Lose_Extern` = ".$_SESSION["lotID"].";";
                    }
                    else{
                        $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
                                        SET
                                        `Ausführungsbeginn` = '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                        `Vergabesumme`= '".$_GET["lotSum"]."',
                                        `Vergabe_abgeschlossen`='".$_GET["lotVergabe"]."',
                                        `Versand_LV`='".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                        `Bearbeiter`='".$_GET["lotLVBearbeiter"]."',
                                        `Notiz` = '".$_GET["lotNotice"]."',
                                        `tabelle_lieferant_idTABELLE_Lieferant` = ".filter_input(INPUT_GET, 'lotAuftragnehmer').",
                                        `Kostenanschlag` = '".$_GET["kostenanschlag"]."',
                                        `Budget` = '".$_GET["budget"]."'
                                        WHERE `idtabelle_Lose_Extern` = ".$_SESSION["lotID"].";";
                    }
                }
                else{
                    if(filter_input(INPUT_GET, 'lotAuftragnehmer')==0){
                        $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
                                        SET
                                        `Ausführungsbeginn` = '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                        `Vergabe_abgeschlossen`='".$_GET["lotVergabe"]."',
                                        `Versand_LV`='".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                        `Bearbeiter`='".$_GET["lotLVBearbeiter"]."',
                                        `Notiz` = '".$_GET["lotNotice"]."',
                                        `Kostenanschlag` = '".$_GET["kostenanschlag"]."',
                                        `Budget` = '".$_GET["budget"]."'
                                        WHERE `idtabelle_Lose_Extern` = ".$_SESSION["lotID"].";";
                    }
                    else{
                        $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
                                        SET
                                        `Ausführungsbeginn` = '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                        `Vergabe_abgeschlossen`='".$_GET["lotVergabe"]."',
                                        `Versand_LV`='".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                        `Bearbeiter`='".$_GET["lotLVBearbeiter"]."',
                                        `Notiz` = '".$_GET["lotNotice"]."',                                    
                                        `tabelle_lieferant_idTABELLE_Lieferant` = ".filter_input(INPUT_GET, 'lotAuftragnehmer').",
                                        `Kostenanschlag` = '".$_GET["kostenanschlag"]."',
                                        `Budget` = '".$_GET["budget"]."'
                                        WHERE `idtabelle_Lose_Extern` = ".$_SESSION["lotID"].";";
                    }
                }
            
        }


			
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Los erfolgreich aktualisiert!";
            
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();					
?>
