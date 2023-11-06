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
        
        if(filter_input(INPUT_GET, 'lotMKFOf')==0){        
            if(strlen($_GET["lotSum"])	> 0){
                if(filter_input(INPUT_GET, 'lotAuftragnehmer')==0){
                    $sql= "INSERT INTO `LIMET_RB`.`tabelle_lose_extern`
                                    (`LosNr_Extern`,
                                    `LosBezeichnung_Extern`,
                                    `Ausführungsbeginn`,
                                    `tabelle_projekte_idTABELLE_Projekte`,
                                    `Vergabesumme`,
                                    `Vergabe_abgeschlossen`,
                                    `Versand_LV`,
                                    `Verfahren`,
                                    `Bearbeiter`,
                                    `Notiz`,
                                    `Kostenanschlag`,
                                    `Budget`)
                                    VALUES
                                    ('".$_GET["losNr"]."',
                                    '".$_GET["losName"]."',
                                    '".$_GET["losDatum"]."',
                                    ".$_SESSION["projectID"].",
                                    '".$_GET["lotSum"]."',
                                    '".$_GET["lotVergabe"]."',
                                    '".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                    '".$_GET["lotVerfahren"]."',
                                    '".$_GET["lotLVBearbeiter"]."',
                                    '".$_GET["lotNotice"]."',
                                    '".$_GET["kostenanschlag"]."',
                                    '".$_GET["budget"]."');";
                }
                else{
                    $sql= "INSERT INTO `LIMET_RB`.`tabelle_lose_extern`
                                    (`LosNr_Extern`,
                                    `LosBezeichnung_Extern`,
                                    `Ausführungsbeginn`,
                                    `tabelle_projekte_idTABELLE_Projekte`,
                                    `Vergabesumme`,
                                    `Vergabe_abgeschlossen`,
                                    `Versand_LV`,
                                    `Verfahren`,
                                    `Bearbeiter`,
                                    `Notiz`,
                                    `tabelle_lieferant_idTABELLE_Lieferant`,
                                    `Kostenanschlag`,
                                    `Budget`)
                                    VALUES
                                    ('".$_GET["losNr"]."',
                                    '".$_GET["losName"]."',
                                    '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                    ".$_SESSION["projectID"].",
                                    '".$_GET["lotSum"]."',
                                    '".$_GET["lotVergabe"]."',
                                    '".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                    '".$_GET["lotVerfahren"]."',
                                    '".$_GET["lotLVBearbeiter"]."',
                                    '".$_GET["lotNotice"]."',
                                    ".filter_input(INPUT_GET, 'lotAuftragnehmer').",
                                    '".$_GET["kostenanschlag"]."',
                                    '".$_GET["budget"]."');";               
                }

            }
            else{
                if(filter_input(INPUT_GET, 'lotAuftragnehmer')==0){
                    $sql= "INSERT INTO `LIMET_RB`.`tabelle_lose_extern`
                                    (`LosNr_Extern`,
                                    `LosBezeichnung_Extern`,
                                    `Ausführungsbeginn`,
                                    `tabelle_projekte_idTABELLE_Projekte`,
                                    `Vergabe_abgeschlossen`,
                                    `Versand_LV`,
                                    `Verfahren`,
                                    `Bearbeiter`,
                                    `Notiz`,
                                    `Kostenanschlag`,
                                    `Budget`)
                                    VALUES
                                    ('".$_GET["losNr"]."',
                                    '".$_GET["losName"]."',
                                    '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                    ".$_SESSION["projectID"].",
                                    '".$_GET["lotVergabe"]."',
                                    '".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                    '".$_GET["lotVerfahren"]."',
                                    '".$_GET["lotLVBearbeiter"]."',
                                    '".$_GET["lotNotice"]."',
                                    '".$_GET["kostenanschlag"]."',
                                    '".$_GET["budget"]."');";
                }
                else{
                    $sql= "INSERT INTO `LIMET_RB`.`tabelle_lose_extern`
                                    (`LosNr_Extern`,
                                    `LosBezeichnung_Extern`,
                                    `Ausführungsbeginn`,
                                    `tabelle_projekte_idTABELLE_Projekte`,
                                    `Vergabe_abgeschlossen`,
                                    `Versand_LV`,
                                    `Verfahren`,
                                    `Bearbeiter`,
                                    `Notiz`,
                                    `tabelle_lieferant_idTABELLE_Lieferant`,
                                    `Kostenanschlag`,
                                    `Budget`)
                                    VALUES
                                    ('".$_GET["losNr"]."',
                                    '".$_GET["losName"]."',
                                    '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                    ".$_SESSION["projectID"].",
                                    '".$_GET["lotVergabe"]."',
                                    '".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                    '".$_GET["lotVerfahren"]."',
                                    '".$_GET["lotLVBearbeiter"]."',
                                    '".$_GET["lotNotice"]."',
                                    ".filter_input(INPUT_GET, 'lotAuftragnehmer').",
                                    '".$_GET["kostenanschlag"]."',
                                    '".$_GET["budget"]."');";               
                }
            }
        }
        else{
            // MKF anlegen
            // Abfragen laufenden Nr            
            $sqlMKF = "SELECT Max(tabelle_lose_extern.mkf_nr) AS Maxvonmkf_nr
                        FROM tabelle_lose_extern
                        WHERE (((tabelle_lose_extern.mkf_von_los)=".filter_input(INPUT_GET, 'lotMKFOf')."));";
            
            $resultMKFNr = $mysqli->query($sqlMKF);
            while($row = $resultMKFNr->fetch_assoc()) {
                    $laufendeNr = $row["Maxvonmkf_nr"];
                    $laufendeNr = $laufendeNr+1;
            }
            if($laufendeNr == ""){
                    $laufendeNr = 1;
            }
            
            $sqlLosDaten = "SELECT tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern
                            FROM tabelle_lose_extern
                            WHERE (((tabelle_lose_extern.idtabelle_Lose_Extern)=".filter_input(INPUT_GET, 'lotMKFOf')."));";
            
            $resultLosDaten = $mysqli->query($sqlLosDaten);
            while($row = $resultLosDaten->fetch_assoc()) {
                    $mkfLosNr = $row["LosNr_Extern"].".".$laufendeNr;
                    $mkfLosBezeichnung = $row["LosBezeichnung_Extern"];
            }
            
            if(strlen($_GET["lotSum"])	> 0){
                if(filter_input(INPUT_GET, 'lotAuftragnehmer')==0){
                    $sql= "INSERT INTO `LIMET_RB`.`tabelle_lose_extern`
                                    (`LosNr_Extern`,
                                    `LosBezeichnung_Extern`,
                                    `Ausführungsbeginn`,
                                    `tabelle_projekte_idTABELLE_Projekte`,
                                    `Vergabesumme`,
                                    `Vergabe_abgeschlossen`,
                                    `Versand_LV`,
                                    `Verfahren`,
                                    `Bearbeiter`,
                                    `Notiz`,
                                    `mkf_von_los`,
                                    `mkf_nr`,
                                    `Kostenanschlag`,
                                    `Budget`)
                                    VALUES
                                    ('".$mkfLosNr."',
                                    '".$mkfLosBezeichnung."',
                                    '".$_GET["losDatum"]."',
                                    ".$_SESSION["projectID"].",
                                    '".$_GET["lotSum"]."',
                                    '".$_GET["lotVergabe"]."',
                                    '".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                    '".$_GET["lotVerfahren"]."',
                                    '".$_GET["lotLVBearbeiter"]."',
                                    '".$_GET["lotNotice"]."',
                                    '".filter_input(INPUT_GET, 'lotMKFOf')."',
                                    '".$laufendeNr."',
                                    '".$_GET["kostenanschlag"]."',
                                    '".$_GET["budget"]."');";
                }
                else{
                    $sql= "INSERT INTO `LIMET_RB`.`tabelle_lose_extern`
                                    (`LosNr_Extern`,
                                    `LosBezeichnung_Extern`,
                                    `Ausführungsbeginn`,
                                    `tabelle_projekte_idTABELLE_Projekte`,
                                    `Vergabesumme`,
                                    `Vergabe_abgeschlossen`,
                                    `Versand_LV`,
                                    `Verfahren`,
                                    `Bearbeiter`,
                                    `Notiz`,
                                    `tabelle_lieferant_idTABELLE_Lieferant`,
                                    `mkf_von_los`,
                                    `mkf_nr`,
                                    `Kostenanschlag`,
                                    `Budget`)
                                    VALUES
                                    ('".$mkfLosNr."',
                                    '".$mkfLosBezeichnung."',
                                    '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                    ".$_SESSION["projectID"].",
                                    '".$_GET["lotSum"]."',
                                    '".$_GET["lotVergabe"]."',
                                    '".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                    '".$_GET["lotVerfahren"]."',
                                    '".$_GET["lotLVBearbeiter"]."',
                                    '".$_GET["lotNotice"]."',
                                    ".filter_input(INPUT_GET, 'lotAuftragnehmer').",
                                    '".filter_input(INPUT_GET, 'lotMKFOf')."',
                                    '".$laufendeNr."',
                                    '".$_GET["kostenanschlag"]."',
                                    '".$_GET["budget"]."');";              
                }

            }
            else{
                if(filter_input(INPUT_GET, 'lotAuftragnehmer')==0){
                    $sql= "INSERT INTO `LIMET_RB`.`tabelle_lose_extern`
                                    (`LosNr_Extern`,
                                    `LosBezeichnung_Extern`,
                                    `Ausführungsbeginn`,
                                    `tabelle_projekte_idTABELLE_Projekte`,
                                    `Vergabe_abgeschlossen`,
                                    `Versand_LV`,
                                    `Verfahren`,
                                    `Bearbeiter`,
                                    `Notiz`,
                                    `mkf_von_los`,
                                    `mkf_nr`,
                                    `Kostenanschlag`,
                                    `Budget`)
                                    VALUES
                                    ('".$mkfLosNr."',
                                    '".$mkfLosBezeichnung."',
                                    '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                    ".$_SESSION["projectID"].",
                                    '".$_GET["lotVergabe"]."',
                                    '".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                    '".$_GET["lotVerfahren"]."',
                                    '".$_GET["lotLVBearbeiter"]."',
                                    '".$_GET["lotNotice"]."',
                                    '".filter_input(INPUT_GET, 'lotMKFOf')."',
                                    '".$laufendeNr."',
                                    '".$_GET["kostenanschlag"]."',
                                    '".$_GET["budget"]."');";
                }
                else{
                    $sql= "INSERT INTO `LIMET_RB`.`tabelle_lose_extern`
                                    (`LosNr_Extern`,
                                    `LosBezeichnung_Extern`,
                                    `Ausführungsbeginn`,
                                    `tabelle_projekte_idTABELLE_Projekte`,
                                    `Vergabe_abgeschlossen`,
                                    `Versand_LV`,
                                    `Verfahren`,
                                    `Bearbeiter`,
                                    `Notiz`,
                                    `tabelle_lieferant_idTABELLE_Lieferant`,
                                    `mkf_von_los`,
                                    `mkf_nr`,
                                    `Kostenanschlag`,
                                    `Budget`)
                                    VALUES
                                    ('".$mkfLosNr."',
                                    '".$mkfLosBezeichnung."',
                                    '".date("Y-m-d", strtotime($_GET["losDatum"]))."',
                                    ".$_SESSION["projectID"].",
                                    '".$_GET["lotVergabe"]."',
                                    '".date("Y-m-d", strtotime($_GET["lotLVSend"]))."',
                                    '".$_GET["lotVerfahren"]."',
                                    '".$_GET["lotLVBearbeiter"]."',
                                    '".$_GET["lotNotice"]."',
                                    ".filter_input(INPUT_GET, 'lotAuftragnehmer').",
                                    '".filter_input(INPUT_GET, 'lotMKFOf')."',
                                    '".$laufendeNr."',
                                    '".$_GET["kostenanschlag"]."',
                                    '".$_GET["budget"]."');";               
                }
            }

            
        }
			
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Los zu Projekt hinzugefügt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	
	$mysqli ->close();	
					
?>
