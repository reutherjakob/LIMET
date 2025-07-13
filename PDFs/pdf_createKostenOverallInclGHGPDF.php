<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
include "../utils/_format.php";
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";

require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";

if ($_SESSION["projectPlanungsphase"] == "Vorentwurf") {
    $_SESSION["PDFTITEL"] = "Medizintechnische Kostenschätzung";
} else {
    $_SESSION["PDFTITEL"] = "Medizintechnische Kostenberechnung";
}
$marginTop = 17;
$marginBTM = 10;
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Medizintechnische Gesamt Kosten");
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_projekte.Projektname, tabelle_projekte.Preisbasis,  tabelle_planungsphasen.Bezeichnung
    FROM tabelle_projekte INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen
    WHERE (((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."));";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

//TITEL einfügen
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetY(19);
$pdf->MultiCell(180, 6, "Kostenberechnung nach Gewerken und GHG", 'TLR', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(180/2, 6, "Projekt: ".$row['Projektname'],'L', 'L', 0, 0);
$pdf->MultiCell(180/2, 6, "Preisbasis: ".$row['Preisbasis'],'R', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(180, 6, "Projektphase: ".$row['Bezeichnung'],'BRL', 'L', 0, 0);
$pdf->Ln();           
$pdf->Ln();
$pdf->Ln();



// Gewerke und GHG laden ----------------------------------
$sql = "SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, Sum(`Kosten`*`Anzahl`) AS PP, tabelle_auftraggeber_gewerke.Bezeichnung AS GewerkBezeichnung , tabelle_auftraggeber_ghg.Bezeichnung AS GHGBezeichnung
FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
GROUP BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG
ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG;";

$result = $mysqli->query($sql);

$raumbereichData = array(); // Array zum Zwischenspeichern der Gewerke/GHG/Summen
    
$j = 0;
while ($row = $result->fetch_assoc()) { 
    $raumbereichData[$j]['Gewerke_Nr']= $row["Gewerke_Nr"];
    $raumbereichData[$j]['Gewerke_Bezeichnung']= $row["GewerkBezeichnung"];
    $raumbereichData[$j]['GHG']= $row["GHG"];
    $raumbereichData[$j]['GHG_Bezeichnung']= $row["GHGBezeichnung"];
    $raumbereichData[$j]['PP']= $row["PP"];
    $j++;
}


setlocale(LC_MONETARY,"de_DE");
$pdf->SetFillColor(244, 244, 244);
$sumGewerk = 0;
$sumGesamt =0;
$sumGesamtNeu =0;
$sumGesamtBestand =0;
$sumGewerkBestand = 0; 
$sumGewerkNeu = 0; 
$i = 0;
$gewerk = "";
$ghg = "";
$fill = 0;

foreach($raumbereichData as $rowData2) {  
    if($rowData2["Gewerke_Nr"] !== $gewerk){
        $pdf->SetFont('helvetica', 'B', 10);    
        if($i > 0){
            $pdf->MultiCell(120, 4,  format_money_report( $sumGewerk), 'T', 'R', 0, 0);
            $pdf->MultiCell(30, 4,  format_money_report( $sumGewerkNeu), 'T', 'R', 0, 0);
            $pdf->MultiCell(30, 4,  format_money_report( $sumGewerkBestand), 'T', 'R', 0, 0);
            $sumGesamt = $sumGesamt + $sumGewerk;
            $sumGesamtNeu = $sumGesamtNeu + $sumGewerkNeu;
            $sumGesamtBestand = $sumGesamtBestand + $sumGewerkBestand;
            $sumGewerk = 0;
            $sumGewerkBestand = 0;  
            $sumGewerkNeu = 0;
            $pdf->Ln();
            $pdf->Ln();
        }
        $pdf->MultiCell(70, 6, "Gewerk ".$rowData2["Gewerke_Nr"]." ".$rowData2["Gewerke_Bezeichnung"],'B', 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(50, 6, "Kosten",'B', 'R', 0, 0);
        $pdf->MultiCell(30, 6, "davon Neu",'B', 'R', 0, 0);
        $pdf->MultiCell(30, 6, "davon Bestand",'B', 'R', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->MultiCell(20, 6, "",'', 'L', 0, 0);
        if(empty($rowData2["GHG"])){
            $pdf->MultiCell(50, 6, "ohne Zuteilung: ",'', 'L', 0, 0);
        }
        else{
            $pdf->MultiCell(50, 6, "GHG: ".$rowData2["GHG"]." ".$rowData2["GHG_Bezeichnung"],'', 'L', 0, 0);
        }
        $pdf->MultiCell(50, 4,  format_money_report( $rowData2["PP"]), 0, 'R', 0, 0);                
    }
    else{
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->MultiCell(20, 6, "",'', 'L', 0, 0);
        if(empty($rowData2["GHG"])){
            $pdf->MultiCell(50, 6, "ohne Zuteilung: ",'', 'L', 0, 0);
        }
        else{
            $pdf->MultiCell(50, 6, "GHG: ".$rowData2["GHG"]." ".$rowData2["GHG_Bezeichnung"],'', 'L', 0, 0);
        }
        $pdf->MultiCell(50, 4,  format_money_report( $rowData2["PP"]), 0, 'R', 0, 0);
    }       
    
    // Neusumme ermitteln ----------------------------------
    if($rowData2["GHG"] == ""){
            $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_neu
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='".$rowData2["Gewerke_Nr"]."' AND tabelle_auftraggeber_ghg.GHG IS NULL);";
    }
    else{
        $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_neu
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='".$rowData2["Gewerke_Nr"]."' AND tabelle_auftraggeber_ghg.GHG='".$rowData2["GHG"]."');";
    }
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    if ( null != ($row['PP_neu'])){
        $pdf->MultiCell(30, 4,  format_money_report( $row["PP_neu"]), 0, 'R', $fill, 0);
        //$sumRaumbereichBestand = $sumRaumbereichBestand + $row['PP'];
    }
    else{
        $pdf->MultiCell(30, 4,  format_money_report( 0), 0, 'R', $fill, 0);
    }
    $sumGewerkNeu = $sumGewerkNeu + $row["PP_neu"];    
    //--------------------------------------------------------------    
    
    // Bestandssumme ermitteln ----------------------------------
    if($rowData2["GHG"] == ""){
            $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='".$rowData2["Gewerke_Nr"]."' AND tabelle_auftraggeber_ghg.GHG IS NULL);";
    }
    else{
        $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='".$rowData2["Gewerke_Nr"]."' AND tabelle_auftraggeber_ghg.GHG='".$rowData2["GHG"]."');";
    }
    
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    if ( null != ($row['PP'])  ){
        $pdf->MultiCell(30, 4,  format_money_report( $row["PP"]), 0, 'R', $fill, 0);
        //$sumRaumbereichBestand = $sumRaumbereichBestand + $row['PP'];
    }
    else{
        $pdf->MultiCell(30, 4,  format_money_report( 0), 0, 'R', $fill, 0);
    }
    //--------------------------------------------------------------
    
    $i++;
    $gewerk = $rowData2["Gewerke_Nr"];
    $sumGewerk = $sumGewerk + $rowData2["PP"];
    $sumGewerkBestand = $sumGewerkBestand + $row["PP"];
    
    $pdf->Ln();    
}

// Letzte Gesamtsumme bilden
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(120, 4,  format_money_report( $sumGewerk), 'T', 'R', 0, 0);
$pdf->MultiCell(30, 4,  format_money_report( $sumGewerkNeu), 'T', 'R', 0, 0);
$pdf->MultiCell(30, 4,  format_money_report( $sumGewerkBestand), 'T', 'R', 0, 0);
$sumGesamt = $sumGesamt + $sumGewerk;
$sumGesamtNeu = $sumGesamtNeu + $sumGewerkNeu;
$sumGesamtBestand = $sumGesamtBestand + $sumGewerkBestand;
$pdf->Ln();
$pdf->Ln(); 
$pdf->MultiCell(90, 4, "GESAMT: ", 'T', 'L', 0, 0);
$pdf->MultiCell(30, 4,  format_money_report( $sumGesamt), 'T', 'R', 0, 0);
$pdf->MultiCell(30, 4,  format_money_report( $sumGesamtNeu), 'T', 'R', 0, 0);
$pdf->MultiCell(30, 4,  format_money_report( $sumGesamtBestand), 'T', 'R', 0, 0);

// ---------------------------------------------------------
$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName("Kosten_Gewerke-GHG"), 'I');


