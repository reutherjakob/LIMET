<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";
include "_pdf_createBericht_utils.php";

$_SESSION["PDFTITEL"] = "Medizintechnische Los-Workflow-Liste";
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4_queer", "Medizintechnische Los-Workflow-Liste" );

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, DATE_FORMAT(tabelle_lot_workflow.Timestamp_Ist, '%d.%m.%Y') AS Timestamp_Ist, DATE_FORMAT(tabelle_lot_workflow.Timestamp_Soll, '%d.%m.%Y') AS Timestamp_Soll, tabelle_lot_workflow.Abgeschlossen, tabelle_workflowteil.aufgabe, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lot_workflow.Kommentar
        FROM tabelle_workflowteil INNER JOIN (tabelle_workflow_has_tabelle_wofklowteil INNER JOIN (tabelle_lose_extern INNER JOIN tabelle_lot_workflow ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern) ON (tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil) AND (tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow = tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow)) ON tabelle_workflowteil.idtabelle_wofklowteil = tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil
        WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
        ORDER BY tabelle_lose_extern.LosNr_Extern, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer;";
$result = $mysqli->query($sql);

$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(0);


$lotID = 0;
$fill = 0;
$pdf->SetFillColor(244, 244, 244);

$data = array();
$columnCounter = array();
$i = 0;
$k = 0;

while ($row = $result->fetch_assoc()) {          
    if($i > 0 && $lotID !== $row['idtabelle_Lose_Extern']){        
        $i = 0;
    }       
    $data[$k]['idtabelle_Lose_Extern'] = $row['idtabelle_Lose_Extern'];
    $data[$k]['LosNr_Extern'] = $row['LosNr_Extern'];
    $data[$k]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];   
    $data[$k]['data'] = $row['Reihenfolgennummer']." ".$row['aufgabe'];        
    $data[$k]['Abgeschlossen'] = $row['Abgeschlossen'];
    $data[$k]['Kommentar'] = $row['Kommentar'];
    $data[$k]['Timestamp_Ist'] = $row['Timestamp_Ist'];    
    $data[$k]['Timestamp_Soll'] = $row['Timestamp_Soll']; 
    $i++; 
    $k++;            
    $lotID = $row['idtabelle_Lose_Extern'];
    $columnCounter[$row['idtabelle_Lose_Extern']]['length'] = $i;
}

$width = 220/6;
$rowHeight = 10;
$info = 1; 
$columncounter = 1;
$lotID1 = 0;
$i = 0;
$x_start = $pdf->GetX();
$y_start = $pdf->GetY();

while( $i < sizeof($data)) {    
    
    if($lotID1 !== $data[$i]['idtabelle_Lose_Extern']){
        $pdf->Ln();     
        $pdf->Ln();         
        $y = $pdf->GetY(); 
        if (($y + $rowHeight) >= 160) {
            $pdf->AddPage();
        }
        $x_start = $pdf->GetX();
        $y_start = $pdf->GetY();
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->MultiCell($width, $rowHeight, $data[$i]['LosNr_Extern']." - ".$data[$i]['LosBezeichnung_Extern'],1, 'L', 0, 1, '', '', true, 0);        
        //$pdf->MultiCell($width, $rowHeight, $data[$i]['LosBezeichnung_Extern'],0, 'L', $fill, 0, '', '', true, 0);
        $pdf->SetFont('helvetica', '', 8);
        if($data[$i]['Abgeschlossen']==1){
            //grün
            $pdf->SetFillColor(217, 252, 182);
        }
        else{
            //rot
            $pdf->SetFillColor(252, 182, 182);
        }
        $pdf->MultiCell($width, $rowHeight, $data[$i]['data'],1, 'L', 1, 2, $x_start+$width, $y_start, true, 0);
        $pdf->MultiCell($width, $rowHeight, $data[$i]['Timestamp_Soll']."/".$data[$i]['Timestamp_Ist']."/".$data[$i]['Kommentar'],1, 'L', 1, 0, $x_start+$width, '', true, 0);                                
        $lotID1 = $data[$i]['idtabelle_Lose_Extern'];
        $columncounter = 2;
    }    
    else{
        if($columncounter == 6){
            $pdf->Ln();             
            $y = $pdf->GetY(); 
            if (($y + $rowHeight) >= 160) {
                $pdf->AddPage();
            }
            $columncounter = 1;
            $x_start = $pdf->GetX();
            $y_start = $pdf->GetY();
        }
        if($data[$i]['Abgeschlossen']==1){
            //grün
            $pdf->SetFillColor(217, 252, 182);
        }
        else{
            //rot
            $pdf->SetFillColor(252, 182, 182);
        }
        $pdf->MultiCell($width, $rowHeight, $data[$i]['data'],1, 'L', 1, 2, $x_start + ($width * $columncounter), $y_start, true, 0);
        $pdf->MultiCell($width, $rowHeight, $data[$i]['Timestamp_Soll']."/".$data[$i]['Timestamp_Ist']."/".$data[$i]['Kommentar'],1, 'L', 1, 0, $x_start + ($width * $columncounter), '', true, 0);  
        $columncounter++;
    }
    $i++;
}



$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Losworkflow'), 'I');


//============================================================+
// END OF FILE
//============================================================+

