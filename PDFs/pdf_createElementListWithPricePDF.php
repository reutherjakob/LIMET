<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";
include "_pdf_createBericht_utils.php";

$_SESSION["PDFTITEL"] = "Medizintechnische Elementliste";
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4_queer", "Elementliste-MT");

$mysqli = utils_connect_sql();


// Variantenparameter Info laden
$sql ="SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";

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

// Räume mit Element laden
$sql ="SELECT tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung
FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Anzahl)>0));";

$result2 = $mysqli->query($sql);
$raeumeMitElement = array();
$raeumeMitElementCounter = 0;
while ($row = $result2->fetch_assoc()) { 
    $raeumeMitElement[$raeumeMitElementCounter]['elementID'] = $row['TABELLE_Elemente_idTABELLE_Elemente'];
    $raeumeMitElement[$raeumeMitElementCounter]['variantenID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $raeumeMitElement[$raeumeMitElementCounter]['Stk'] = $row['Anzahl'];
    $raeumeMitElement[$raeumeMitElementCounter]['raumNr'] = $row['Raumnr'];
    $raeumeMitElement[$raeumeMitElementCounter]['raum'] = $row['Raumbezeichnung'];
    $raeumeMitElementCounter = $raeumeMitElementCounter + 1;
}

//Gewerk und Schätzkosten für alle Elemente und Varianten laden
$sql = "SELECT tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente, tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_varianten_kosten.Kosten, tabelle_auftraggeber_gewerke.Gewerke_Nr
FROM tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN tabelle_projekt_varianten_kosten ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
WHERE (((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."));";

$result3 = $mysqli->query($sql);
$elementKostenGewerke = array();
$elementKostenGewerkeCounter = 0;
while ($row = $result3->fetch_assoc()) { 
    $elementKostenGewerke[$elementKostenGewerkeCounter]['elementID'] = $row['tabelle_elemente_idTABELLE_Elemente'];
    $elementKostenGewerke[$elementKostenGewerkeCounter]['variantenID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $elementKostenGewerke[$elementKostenGewerkeCounter]['Kosten'] = $row['Kosten'];
    $elementKostenGewerke[$elementKostenGewerkeCounter]['Gewerke_Nr'] = $row['Gewerke_Nr'];
    $elementKostenGewerkeCounter = $elementKostenGewerkeCounter + 1;
}



//Kopfzeil
$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(0);
$rowHeightFirstLine = $pdf->getStringHeight(50,"ElementID",false,true,'',1);
$pdf->MultiCell(8, $rowHeightFirstLine, "Stk",'B', 'C', 0, 0);
$pdf->MultiCell(20, $rowHeightFirstLine, "ElementID",'B', 'C', 0, 0);
$pdf->MultiCell(50, $rowHeightFirstLine, "Element",'B', 'C', 0, 0);
$pdf->MultiCell(13, $rowHeightFirstLine, "Variante",'B', 'L', 0, 0);
$pdf->MultiCell(13, $rowHeightFirstLine, "Bestand",'B', 'C', 0, 0);
$pdf->MultiCell(80, $rowHeightFirstLine, "Räume",'B', 'C', 0, 0);
$pdf->MultiCell(50, $rowHeightFirstLine, "Varianteninfo",'B', 'C', 0, 0);
$pdf->MultiCell(15, $rowHeightFirstLine, "Gewerk",'B', 'C', 0, 0);
$pdf->MultiCell(17, $rowHeightFirstLine, "EP",'B', 'C', 0, 0);
$pdf->Ln();


$fill = 0;
$pdf->SetFillColor(244, 244, 244);

// Element im Projekt laden
$sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            FROM tabelle_elemente INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND tabelle_räume_has_tabelle_elemente.Anzahl>0)
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            ORDER BY tabelle_elemente.ElementID;";

$result3 = $mysqli->query($sql);
$pdf->SetFont('helvetica', 'I', 6);
setlocale(LC_MONETARY,"de_DE");
while ($row = $result3->fetch_assoc()) {    
        $fill=!$fill;        
              
        $raeume = "";        
        foreach($raeumeMitElement as $array) { 
            if($array['elementID']===$row['TABELLE_Elemente_idTABELLE_Elemente']){                 
                if($array['variantenID']===$row['idtabelle_Varianten']){                     
                    $raeume = $raeume."\n".$array['raumNr']."-".$array['raum'].": ".$array['Stk']." Stk";
                }               
            }
        }
        
        $varInfo = "";        
        foreach($variantenInfos as $array1) { 
            if($array1['elementID']===$row['TABELLE_Elemente_idTABELLE_Elemente']){                 
                if($array1['VarID']===$row['idtabelle_Varianten']){                     
                    $varInfo = $varInfo."\n".$array1['Kategorie']."-".$array1['Bezeichnung'].": ".$array1['Wert']." ".$array1['Einheit'];
                }               
            }
        }
                   
        $rowHeight = $pdf->getStringHeight(80,$raeume,false,true,'',1);
        $rowHeight1 = $pdf->getStringHeight(50,$varInfo,false,true,'',1);
        
        $rowHeightFinal = 0;

        $y = $pdf->GetY();
        if($rowHeight > $rowHeight1){
            $rowHeightFinal = $rowHeight;
        }
        else{
            $rowHeightFinal = $rowHeight1;
        }
        
        if (($y + $rowHeightFinal) >= 180) {
            $pdf->AddPage();
        } 
        

        $pdf->MultiCell(8, $rowHeightFinal, $row['SummevonAnzahl'],0, 'C', $fill, 0);
        $pdf->MultiCell(20, $rowHeightFinal, $row['ElementID'],0, 'C', $fill, 0);
        $pdf->MultiCell(50, $rowHeightFinal, $row['Bezeichnung'],0, 'C', $fill, 0);
        $pdf->MultiCell(13, $rowHeightFinal, $row['Variante'],0, 'C', $fill, 0);
        if($row['Neu/Bestand']=='1'){
            $pdf->MultiCell(13, $rowHeightFinal, "Nein",0, 'C', $fill, 0);
        }
        else{
            $pdf->MultiCell(13, $rowHeightFinal, "Ja",0, 'C', $fill, 0);
        }
        $pdf->MultiCell(80, $rowHeightFinal, $raeume,0, 'L', $fill, 0);            
        $pdf->MultiCell(50, $rowHeightFinal, $varInfo,0, 'L', $fill, 0); 
        
        foreach($elementKostenGewerke as $array1) { 
            if($array1['elementID']===$row['TABELLE_Elemente_idTABELLE_Elemente']){                 
                if($array1['variantenID']===$row['idtabelle_Varianten']){                     
                    $pdf->MultiCell(15, $rowHeightFinal, $array1['Gewerke_Nr'],0, 'C', $fill, 0);
                    $pdf->MultiCell(17, $rowHeightFinal, sprintf('%01.2f',$array1['Kosten']),0, 'R', $fill, 0);
                }               
            }
        }
    $pdf->Ln();                                    
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Elementeliste'), 'I');
