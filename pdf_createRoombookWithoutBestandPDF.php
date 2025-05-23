<?php
# 2025done
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
include "_pdf_createBericht_utils.php";

$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$_SESSION["PDFTITEL"] = "Medizintechnisches Raumbuch";
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Raumbuch-MT");
$mysqli = utils_connect_sql();


// Variantenparameter Info laden
//$sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente
//FROM tabelle_projekt_elementparameter
//WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)=14));";

$sql ="SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
ORDER BY tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie;";

$result1 = $mysqli->query($sql);
$variantenInfos = array();
$variantenInfosCounter = 0;
while ($row = $result1->fetch_assoc()) { 
    $variantenInfos[$variantenInfosCounter]['VarID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $variantenInfos[$variantenInfosCounter]['elementID'] = $row['tabelle_elemente_idTABELLE_Elemente'];
    $variantenInfos[$variantenInfosCounter]['Wert'] = $row['Wert'];
    $variantenInfos[$variantenInfosCounter]['Einheit'] = $row['Einheit'];
    $variantenInfos[$variantenInfosCounter]['Kategorie'] = $row['Kategorie'];
    $variantenInfos[$variantenInfosCounter]['Bezeichnung'] = $row['Bezeichnung'];
    $variantenInfosCounter = $variantenInfosCounter + 1;
}

// RaumIDs laden über GET
$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);
foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');
    // Raumdaten laden ----------------------------------
    $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung,  tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.Geschoss, `tabelle_räume`.`Anmerkung FunktionBO`
                FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
                WHERE (((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."));";

    $result2 = $mysqli->query($sql);
    while ($row = $result2->fetch_assoc()) { 
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(100, 6, "Raum: ".$row['Raumbezeichnung'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: ".$row['Raumnr'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 6, "Bereich: ".$row['Raumbereich Nutzer'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Geschoss: ".$row['Geschoss'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Raumfläche: ".$row['Nutzfläche']." m2",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projekt: ".$row['Projektname'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: ".$row['Bauabschnitt'],0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projektstatus: ".$row['Bezeichnung'],'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauetappe: ".$row['Bauetappe'],'B', 'L', 0, 0);
        
        $pdf->SetFont('helvetica', '', 8);
        if( null != ($row['Anmerkung FunktionBO'])){
            $pdf->Ln();
            $rowHeightComment = $pdf->getStringHeight(150,br2nl($row['Anmerkung FunktionBO']),false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightComment) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(30, $rowHeightComment, "Funktion BO:",'B', 'L', 0, 0);
            $pdf->MultiCell(150, $rowHeightComment, br2nl($row['Anmerkung FunktionBO']),'B', 'L', 0, 0);           
        }
    }

    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0);
    $rowHeightFirstLine = $pdf->getStringHeight(50,"ElementID",false,true,'',1);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Gewerk",'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "GHG",'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "ElementID",'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Var",'B', 'C', 0, 0);
    $pdf->MultiCell(50, $rowHeightFirstLine, "Element",'B', 'L', 0, 0);
    $pdf->MultiCell(15, $rowHeightFirstLine, "Stk",'B', 'C', 0, 0);
    $pdf->MultiCell(15, $rowHeightFirstLine, "",'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "",'B', 'C', 0, 0);
    $pdf->Ln();

    // Elemente im Raum laden
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."))
    ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

    $result = $mysqli->query($sql);


    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    $idRoombookEntry = 0;
    $bestandsCounter = 1;
    while ($row = $result->fetch_assoc()) {

        if($idRoombookEntry != $row['id']){
            $fill=!$fill;
            $bestandsCounter = 1;
            $pdf->SetFont('helvetica', '', 8);
            $rowHeightMainLine = $pdf->getStringHeight(50,$row['Bezeichnung'],false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();    
            if (($y + $rowHeightMainLine) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Gewerke_Nr'],0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['GHG'],0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['ElementID'],0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Variante'],0, 'C', $fill, 0);
            $pdf->MultiCell(50, $rowHeightMainLine, $row['Bezeichnung'],0, 'L', $fill, 0);
            $pdf->MultiCell(15, $rowHeightMainLine, $row['Anzahl'],0, 'C', $fill, 0);
            if($row['Neu/Bestand']==1){
                $pdf->MultiCell(15, $rowHeightMainLine, "",0, 'C', $fill, 0);
            }
            else{
                $pdf->MultiCell(15, $rowHeightMainLine, "",0, 'C', $fill, 0);
            }
            $pdf->MultiCell(20, $rowHeightMainLine, $row[''],0, 'R', $fill, 0);
            
            $additionalRoombookData = "";
            foreach($variantenInfos as $array) { 
                
                /* ---------------Ausgabe der Varianten-Parameter---------------------------
                if($array['elementID']===$row['TABELLE_Elemente_idTABELLE_Elemente']){                 
                    if($array['VarID']===$row['tabelle_Varianten_idtabelle_Varianten']){                     
                        $additionalRoombookData = $additionalRoombookData."\n".$array['Kategorie']."-".$array['Bezeichnung'].": ".$array['Wert']." ".$array['Einheit'];
                    }               
                }
                 * */
                 
            }
            if($row['Standort']==1){
                $additionalRoombookData = $additionalRoombookData."\nStandort: Ja";
            }
            else{
                $additionalRoombookData = $additionalRoombookData."\nStandort: Nein";
            }
            if($row['Verwendung']==1){
                $additionalRoombookData = $additionalRoombookData."\nVerwendung: Ja";
            }
            else{
                $additionalRoombookData = $additionalRoombookData."\nVerwendung: Nein";
            }
            if( null != ($row['Kurzbeschreibung'])){
                $additionalRoombookData = $additionalRoombookData."\nKommentar: ".$row['Kurzbeschreibung'];
            }
            if( null != ($row['Inventarnummer'])){
                $additionalRoombookData = $additionalRoombookData."\nBestandsgerät ".$bestandsCounter.":\n     Inventarnummer: ".$row['Inventarnummer'];
            }
            if( null != ($row['Seriennummer'])){
                $additionalRoombookData = $additionalRoombookData."\n     Seriennummer: ".$row['Seriennummer'];
            }
            if( null != ($row['Anschaffungsjahr'])){
                $additionalRoombookData = $additionalRoombookData."\n     Anschaffungsjahr: ".$row['Anschaffungsjahr'];
            }
            if( null != ($row['Hersteller'])){
                $additionalRoombookData = $additionalRoombookData."\n     Gerät: ".$row['Hersteller']." ".$row['Typ'];
            }            

            if( null != ($additionalRoombookData)>0){
                $pdf->Ln();

                $pdf->SetFont('helvetica', 'I', 6);
                $rowHeight = $pdf->getStringHeight(50,$additionalRoombookData,false,true,'',1);
                // Wenn Seitenende? Überprüfen und neue Seite anfangen
                $y = $pdf->GetY();
                if (($y + $rowHeight) >= 270) {
                    $pdf->AddPage();
                }
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(50, $rowHeight, $additionalRoombookData,0, 'L', $fill, 0);
                $pdf->MultiCell(15, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(15, $rowHeight, "",0, 'C', $fill, 0);
                $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
                $bestandsCounter++;
            }
            $idRoombookEntry = $row['id'];
        }
        else{
            $pdf->SetFont('helvetica', 'I', 6);

            $additionalRoombookExtraData = "";
            if(null !=($row['Inventarnummer'])){
                $additionalRoombookExtraData = $additionalRoombookExtraData."Bestandsgerät ".$bestandsCounter.":\n     Inventarnummer: ".$row['Inventarnummer'];
            }
            if(null !=($row['Seriennummer'])){
                $additionalRoombookExtraData = $additionalRoombookExtraData."\n     Seriennummer: ".$row['Seriennummer'];
            }
            if(null !=($row['Anschaffungsjahr'])){
                $additionalRoombookExtraData = $additionalRoombookExtraData."\n     Anschaffungsjahr: ".$row['Anschaffungsjahr'];
            }
            if(null !=($row['Hersteller'])){
                $additionalRoombookExtraData = $additionalRoombookExtraData."\n     Gerät: ".$row['Hersteller']." ".$row['Typ'];
            }
            $rowHeight = $pdf->getStringHeight(50,$additionalRoombookExtraData,false,true,'',1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeight) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(50, $rowHeight, $additionalRoombookExtraData,0, 'L', $fill, 0);
            $pdf->MultiCell(20, $rowHeight, "",0, 'C', $fill, 0);
            $pdf->MultiCell(30, $rowHeight, "",0, 'C', $fill, 0);
            $bestandsCounter++;
        }
        $pdf->Ln();                  
    }
}


//$pdf->MultiCell(50, 6, "Bereich",'B', 'L', 0, 0);
//$pdf->MultiCell(20, 6, "Geschoss",'B', 'C', 0, 0);

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Raumbuch'), 'I');


//============================================================+
// END OF FILE
//============================================================+

