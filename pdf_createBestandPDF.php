
<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";
include "_pdf_createBericht_utils.php";

$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$_SESSION["PDFTITEL"] = "Medizintechnik Umsiedlungsliste";
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4_queer", "Umsiedlungsliste-MT");
$mysqli = utils_connect_sql();
// Abfrage der Bestandselemente
//
$sql = "SELECT tabelle_elemente.ElementID,
       tabelle_elemente.Bezeichnung,
       tabelle_räume_has_tabelle_elemente.id,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung,
       tabelle_bestandsdaten.Inventarnummer,
       tabelle_bestandsdaten.Seriennummer,
       tabelle_bestandsdaten.Anschaffungsjahr,
       tabelle_bestandsdaten.`Aktueller Ort`,
       tabelle_geraete.Typ,
       tabelle_hersteller.Hersteller,
       tabelle_räume.Raumnr,
       tabelle_räume.Raumbezeichnung,
       tabelle_räume.`Raumbereich Nutzer`
FROM tabelle_hersteller
         RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten INNER JOIN (tabelle_elemente INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente
                                                                                                                ON tabelle_räume.idTABELLE_Räume =
                                                                                                                   tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                                                                   ON tabelle_elemente.idTABELLE_Elemente =
                                                                                      tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
                                                 ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id =
                                                    tabelle_räume_has_tabelle_elemente.id)
                     ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete)
                    ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = ".$_SESSION["projectID"].") AND
       ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`) = 0) AND ((tabelle_räume_has_tabelle_elemente.Standort) = 1))
ORDER BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr;";
$result = $mysqli->query($sql);


$fill = 0;
$pdf->SetFillColor(244, 244, 244);
$raumbereich= "";
// Ausgabe
while ($row = $result->fetch_assoc()) {
    if($raumbereich != $row['Raumbereich Nutzer']){            
        if($raumbereichCounter > 0){
            $pdf->MultiCell(270, 8, "",'T', 'L', 0, 0);
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(0);
        $pdf->MultiCell(270, 8, $row['Raumbereich Nutzer'],1, 'L', 0, 0);
        $pdf->Ln();            
        $rowHeight = $pdf->getStringHeight(40,"Standort vor Siedlung",false,true,'',1);
        $pdf->SetFont('helvetica', '', 8);            
        $fill = 0;
        $pdf->MultiCell(2, $rowHeight, "",'L', 'L', 0, 0);
        $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(0, 0, 0)));
        $pdf->MultiCell(30, $rowHeight, "Raum",'B', 'L', 0, 0);
        $pdf->MultiCell(20, $rowHeight, "Element-ID",'B', 'L', 0, 0);
        $pdf->MultiCell(45, $rowHeight, "Element",'B', 'L', 0, 0);
        $pdf->MultiCell(35, $rowHeight, "Gerät",'B', 'L', 0, 0);
        $pdf->MultiCell(35, $rowHeight, "Inventarnummer",'B', 'L', 0, 0);
        $pdf->MultiCell(35, $rowHeight, "Seriennummer",'B', 'L', 0, 0);
        $pdf->MultiCell(26, $rowHeight, "Anschaffungsjahr",'B', 'L', 0, 0);
        $pdf->MultiCell(40, $rowHeight, "Standort vor Siedlung",'B', 'L', 0, 0);
        $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->MultiCell(2, $rowHeight, "",'R', 'L', 0, 0);
        $pdf->Ln();
        $raumbereich = $row['Raumbereich Nutzer'];
        $raumbereichCounter++;
    }

    $rowHeight = $pdf->getStringHeight(20,$row['Raumnr']."-".$row['Raumbezeichnung'],false,true,'',1);
    $rowHeight1 = $pdf->getStringHeight(35,$row['Hersteller']."-".$row['Typ'],false,true,'',1);
    $rowHeight2 = $pdf->getStringHeight(45,$row['Bezeichnung'],false,true,'',1);

    $y = $pdf->GetY();
    if($rowHeight > $rowHeight1 && $rowHeight > $rowHeight2){
        $rowHeightFinal = $rowHeight;
    }
    else{
        $rowHeightFinal = max($rowHeight1, $rowHeight2);
    }
    $rowHeightFinal = $rowHeightFinal + 1;

    if (($y + $rowHeightFinal) >= 180) {
        $pdf->AddPage();
    } 
    $pdf->MultiCell(2, $rowHeightFinal, "",'L', 'L', 0, 0);
    $pdf->MultiCell(30, $rowHeightFinal, $row['Raumnr']."-".$row['Raumbezeichnung'],'', 'L', $fill, 0);
    $pdf->MultiCell(20, $rowHeightFinal, $row['ElementID'],'', 'L', $fill, 0);
    $pdf->MultiCell(45, $rowHeightFinal, $row['Bezeichnung'],'', 'L', $fill, 0);
    $pdf->MultiCell(35, $rowHeightFinal, $row['Hersteller']."-".$row['Typ'],'', 'L', $fill, 0);
    $pdf->MultiCell(35, $rowHeightFinal, $row['Inventarnummer'],'', 'L', $fill, 0);
    $pdf->MultiCell(35, $rowHeightFinal, $row['Seriennummer'],'', 'L', $fill, 0);
    $pdf->MultiCell(26, $rowHeightFinal, $row['Anschaffungsjahr'],'', 'L', $fill, 0);
    $pdf->MultiCell(40, $rowHeightFinal, $row['Aktueller Ort'],'', 'L',$fill, 0);       
    $pdf->MultiCell(2, $rowHeightFinal, "",'R', 'L', 0, 0);

    $fill=!$fill; 
    $pdf->Ln();                                    
}

// Umrandung für Raumbereich beenden
$pdf->MultiCell(270, 8, "",'T', 'L', 0, 0);


$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Umsiedlungsliste'), 'I');
$_SESSION["PDFHeaderSubtext"]="";
//============================================================+
// END OF FILE
//============================================================+

