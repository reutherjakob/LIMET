<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();

require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
include "_pdf_createBericht_utils.php";

$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$_SESSION["PDFTITEL"] = "Medizintechnik Bedarfserhebung";
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Raumbuch-MT");
$mysqli = utils_connect_sql();


$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);


foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');
    // Raumdaten laden ----------------------------------
   // $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung,  tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.Geschoss
    //            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
     //           WHERE (((tabelle_räume.idTABELLE_Räume)=".$valueOfRoomID."));";
    
    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, 
            tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Nutzfläche_Soll, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, 
            tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
            tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O,  
            tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung
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
        $pdf->MultiCell(100, 6, "Raumfläche Ist: ".$row['Nutzfläche']." m2",0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Raumfläche Soll: ".$row['Nutzfläche_Soll']." m2",0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projekt: ".$row['Projektname'],0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: ".$row['Bauabschnitt'],0, 'L', 0, 0);        
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projektstatus: ".$row['Bezeichnung'],'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauetappe: ".$row['Bauetappe'],'B', 'L', 0, 0);            
        $pdf->Ln();     
        $rowHeightComment = $pdf->getStringHeight(140,br2nl($row['Anmerkung FunktionBO']),false,true,'',1);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(40, $rowHeightComment, "Betriebsorganisation:",0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "RFP: ",0, 'R', 0, 0);
        $pdf->MultiCell(140, $rowHeightComment, br2nl($row['Anmerkung FunktionBO']),0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "Tätigkeit Arzt: ",0, 'R', 0, 0);
        $pdf->MultiCell(140, 35, "",1, 'R', 0, 0);
        $pdf->Ln();
        $pdf->Ln(6);
        $pdf->MultiCell(40, 6, "Tätigkeit Pflege: ",0, 'R', 0, 0);
        $pdf->MultiCell(140, 35, "",1, 'R', 0, 0);
        $pdf->Ln();
        $pdf->Ln(6);        
        $pdf->MultiCell(40, 6, "Patientenwege: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, "Gehend: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "Rollstuhl: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "Liege: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(10, 6, "Bett: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Ln();           
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(40,8, "Ausstattung: ",'T', 'L', 0, 0);
        $pdf->MultiCell(140,8, "",'T', 'L', 0, 0);
        $pdf->Ln();        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Gase: ",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, "O2: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);  
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "DL: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);  
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "VA: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);  
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "",0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, "CO2: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "N2O: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "NGA: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(20, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);   
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Geräte: ",0, 'R', 0, 0);
        $pdf->MultiCell(140, 70, "",1, 'R', 0, 0);
        $pdf->Ln();
        $pdf->Ln(6);        
        $pdf->SetTextColor(0);
        $pdf->MultiCell(40, 6, "Strahlenanwendung: ",0, 'R', 0, 0);
        if($row['Strahlenanwendung']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        }
        else{
            if($row['Strahlenanwendung']==='1'){
                $pdf->SetFont('zapfdingbats', '', 10);
                //grün
                $pdf->SetTextColor(0, 255, 0); 
                $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
            }
            else{
                $pdf->MultiCell(15, 6, "Quasi stationär",0, 'L', 0, 0);
            }
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);    
        $pdf->MultiCell(31, 6, "Laseranwendung: ",0, 'R', 0, 0);
        if($row['Laseranwendung']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0);            
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(30, 6, "Abdunkelbarkeit: ",0, 'R', 0, 0);
        if($row['Abdunkelbarkeit']==='0'){
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "Sterilgut: ",0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(15, 6, TCPDF_FONTS::unichr(114),0, 'L', 0, 0);               
        $pdf->Ln();
    }
    
    
}

// MYSQL-Verbindung schließen
$mysqli ->close();
ob_end_clean();
$pdf->Output(getFileName(  'Formular-Nutzer'), 'I');

//============================================================+
// END OF FILE
//============================================================+
