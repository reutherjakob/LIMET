<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();
require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";
include "_pdf_createBericht_utils.php";
include "../utils/_format.php";

$_SESSION["PDFTITEL"] = "Medizintechnische Loseinteilung";
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4_queer", "Medizintechnische Loseinteilung" );

// Daten laden
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern, tabelle_varianten.Variante, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`
FROM tabelle_elemente INNER JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_varianten ON tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_varianten.idtabelle_Varianten) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
WHERE (((tabelle_räume_has_tabelle_elemente.Anzahl)>0) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."));";

$result2 = $mysqli->query($sql);
$elemente = array();
$elementeCounter = 0;
while ($row = $result2->fetch_assoc()) { 
    $elemente[$elementeCounter]['elementID'] = $row['TABELLE_Elemente_idTABELLE_Elemente'];
    $elemente[$elementeCounter]['variantenID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $elemente[$elementeCounter]['Stk'] = $row['Anzahl'];
    $elemente[$elementeCounter]['ID'] = $row['ElementID'];
    $elemente[$elementeCounter]['Bezeichnung'] = $row['Bezeichnung'];
    $elemente[$elementeCounter]['Variante'] = $row['Variante'];
    $elemente[$elementeCounter]['Neu/Bestand'] = $row['Neu/Bestand'];
    $elemente[$elementeCounter]['raumNr'] = $row['Raumnr'];
    $elemente[$elementeCounter]['raum'] = $row['Raumbezeichnung'];
    $elemente[$elementeCounter]['Raumbereich'] = $row['Raumbereich Nutzer'];
    $elemente[$elementeCounter]['lotID'] = $row['tabelle_Lose_Extern_idtabelle_Lose_Extern'];
    
    $elementeCounter = $elementeCounter + 1;
}

//Kopfzeile
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0);
$rowHeightFirstLine = $pdf->getStringHeight(30,"Auftragnehmer",false,true,'',1);
$pdf->MultiCell(20, $rowHeightFirstLine, "LosNr",'B', 'L', 0, 0);
$pdf->MultiCell(40, $rowHeightFirstLine, "Bezeichnung",'B', 'L', 0, 0);
$pdf->MultiCell(30, $rowHeightFirstLine, "Verfahren",'B', 'L', 0, 0);
$pdf->MultiCell(30, $rowHeightFirstLine, "Bearbeiter",'B', 'L', 0, 0);
$pdf->MultiCell(35, $rowHeightFirstLine, "Schätzsumme-Neu",'B', 'R', 0, 0);
$pdf->MultiCell(40, $rowHeightFirstLine, "Schätzsumme-Bestand",'B', 'R', 0, 0);
$pdf->MultiCell(30, $rowHeightFirstLine, "Vergabesumme",'B', 'R', 0, 0);
$pdf->MultiCell(40, $rowHeightFirstLine, "Auftragnehmer",'B', 'R', 0, 0);
$pdf->Ln();
$fill = 0;
$pdf->SetFillColor(244, 244, 244);


$sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, 
                tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.Verfahren, tabelle_lose_extern.mkf_von_los,
                tabelle_lose_extern.Bearbeiter, tabelle_lose_extern.Vergabesumme, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Notiz, tabelle_lieferant.Lieferant, tabelle_lieferant.idTABELLE_Lieferant,
                losschaetzsumme.Summe As schaetzsumme,
                losbestandschaetzsumme.SummeBestand,
                losschaetzsumme.id,
                losbestandschaetzsumme.id
        FROM tabelle_lieferant 
        RIGHT JOIN tabelle_lose_extern 
        ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
        LEFT JOIN
                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS Summe
                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
            AS losschaetzsumme
            ON (tabelle_lose_extern.idtabelle_Lose_Extern = losschaetzsumme.id)
        LEFT JOIN 
                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS SummeBestand
                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
            AS losbestandschaetzsumme
            ON (tabelle_lose_extern.idtabelle_Lose_Extern = losbestandschaetzsumme.id)
        WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
        ORDER BY LosNr_Extern;";

$result3 = $mysqli->query($sql);
setlocale(LC_MONETARY,"de_DE");
$summe = 0;
$summebestand = 0;
$summeVergeben = 0;

while ($row = $result3->fetch_assoc()) {    
        $fill=!$fill;                                          
        $y = $pdf->GetY(); 
        
        $rowHeight1 = $pdf->getStringHeight(40,$row['Lieferant'],false,true,'',1);
        $rowHeight2 = $pdf->getStringHeight(40,$row['LosBezeichnung_Extern'],false,true,'',1);
        $rowHeight3 = $pdf->getStringHeight(20,$row['LosNr_Extern'],false,true,'',1);
        
        if($rowHeight1 > $rowHeight2){
            $rowHeight = $rowHeight1;
            if($rowHeight1 > $rowHeight3){
                $rowHeight = $rowHeight1;
            }
            else{
                $rowHeight = $rowHeight3;
            }
        }
        else{
            if($rowHeight2 > $rowHeight3){
                $rowHeight = $rowHeight2;
            }
            else{
                $rowHeight = $rowHeight3;
            }
            //$rowHeight = $rowHeight2;
        }
        
        if (($y + $rowHeightFirstLine) >= 180) {
            $pdf->AddPage();
        } 
        $pdf->SetFont('helvetica', '', 8);           
        $pdf->MultiCell(20, $rowHeight, $row['LosNr_Extern'],0, 'L', $fill, 0);
        $pdf->MultiCell(40, $rowHeight, $row['LosBezeichnung_Extern'],0, 'L', $fill, 0);
        $pdf->MultiCell(30, $rowHeight, $row['Verfahren'],0, 'L', $fill, 0);
        $pdf->MultiCell(30, $rowHeight, $row['Bearbeiter'],0, 'L', $fill, 0);
        $pdf->MultiCell(35, $rowHeight, format_money_report($row['schaetzsumme']),0, 'R', $fill, 0);
        $pdf->MultiCell(40, $rowHeight, format_money_report($row['SummeBestand']),0, 'R', $fill, 0);
        $pdf->MultiCell(30, $rowHeight, format_money_report($row['Vergabesumme']),0, 'R', $fill, 0);
        $pdf->MultiCell(40, $rowHeight, $row['Lieferant'],0, 'R', $fill, 0);
        $summe = $summe + $row['schaetzsumme'];
        $summebestand = $summebestand + $row['SummeBestand'];
        $summeVergeben = $summeVergeben + $row['Vergabesumme'];
        
        
               
    $pdf->Ln();                                    
}
$fill=!$fill; 
$pdf->MultiCell(20, $rowHeight, "",'T', 'L', $fill, 0);
$pdf->MultiCell(40, $rowHeight, "",'T', 'L', $fill, 0);
$pdf->MultiCell(30, $rowHeight, "",'T', 'L', $fill, 0);
$pdf->MultiCell(30, $rowHeight, "",'T', 'L', $fill, 0);
$pdf->MultiCell(35, $rowHeight, format_money_report($summe),'T', 'R', $fill, 0);
$pdf->MultiCell(40, $rowHeight, format_money_report($summebestand),'T', 'R', $fill, 0);
$pdf->MultiCell(30, $rowHeight, format_money_report($summeVergeben),'T', 'R', $fill, 0);
$pdf->MultiCell(40, $rowHeight, "",'T', 'R', $fill, 0);

 

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Loseinteilung'), 'I');
//============================================================+
// END OF FILE
//============================================================+

