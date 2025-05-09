<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
include "_format.php";
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";

require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";

if ($_SESSION["projectPlanungsphase"] == "Vorentwurf") {
    $_SESSION["PDFTITEL"] = "Medizintechnische Kostenschätzung";
} else {
    $_SESSION["PDFTITEL"] = "Medizintechnische Kostenberechnung";
}
$marginTop = 20;
$marginBTM = 10;
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4_queer", "Medizintechnische Gesamt Kosten");
check_login();
$mysqli = utils_connect_sql();
// --------------------- TITELZEILE MIT PROJEKT INFOS -----------------------------
$sql = "SELECT tabelle_projekte.Projektname, tabelle_projekte.Preisbasis, tabelle_planungsphasen.Bezeichnung
    FROM tabelle_projekte INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen
    WHERE (((tabelle_projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

//TITEL einfügen
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(200, 6, "Projekt: " . $row['Projektname'], '', 'L', 0, 0);
$xPosition = $pdf->getX();
$yPosition = $pdf->getY();
$pdf->Ln();
$pdf->MultiCell(200, 6, "Projektphase: " . $row['Bezeichnung'], '', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(200, 6, "Preisbasis: " . $row['Preisbasis'], '', 'L', 0, 0);
$pdf->Ln();

$sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.Bezeichnung
FROM tabelle_auftraggeber_gewerke INNER JOIN (tabelle_projekte INNER JOIN tabelle_auftraggeber_codes ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes) ON tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes
WHERE (((tabelle_projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";

$pdf->SetFont('helvetica', 'B', 7);
$pdf->setXY($xPosition, $yPosition);
$pdf->MultiCell(80, '', "Gewerke: ", '', 'L', 0, 0);
$pdf->Ln();
$pdf->SetFont('helvetica', 'I', 7);
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
    $pdf->MultiCell(200, '', "", '', 'L', 0, 0);
    $pdf->MultiCell(80, '', $row['Gewerke_Nr'] . "-" . $row['Bezeichnung'], '', 'L', 0, 0);
    $pdf->Ln();
}
$pdf->Ln();
$pdf->SetFont('helvetica', 'B', 10);
// data loading for header ----------------------------------
$sql = "SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
        FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
        GROUP BY tabelle_auftraggeber_gewerke.Gewerke_Nr
        ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";

$result = $mysqli->query($sql);
$gewerkeInProject = array();
while ($row = $result->fetch_assoc()) {
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['Gewerke_Nr'] = $row['Gewerke_Nr'];
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamt'] = 0;
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtBestand'] = 0;
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtNeu'] = 0;
}

$pdf->MultiCell(50, 6, "Bereich", 'B', 'L', 0, 0);
$pdf->MultiCell(20, 6, "Geschoss", 'B', 'C', 0, 0);

foreach ($gewerkeInProject as $rowData) {
    $pdf->MultiCell(25, 6, $rowData['Gewerke_Nr'], 'B', 'R', 0, 0);
}
$pdf->MultiCell(25, 6, "Gesamt", 'B', 'R', 0, 0);
$pdf->Ln();
// ---------------------------------------------------------

// data loading Raumbereiche ----------------------------------
$pdf->SetFont('helvetica', '', 8);
$pdf->SetFillColor(244, 244, 244);

$sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
ORDER BY tabelle_räume.Geschoss, tabelle_räume.`Raumbereich Nutzer`;";

$result = $mysqli->query($sql);
$raumbereicheInProject = array();
$i = 1;
while ($row = $result->fetch_assoc()) {
    $raumbereicheInProject[$i]['Raumbereich Nutzer'] = $row['Raumbereich Nutzer'];
    $raumbereicheInProject[$i]['Geschoss'] = $row['Geschoss'];
    $i++;
}
setlocale(LC_MONETARY, "de_DE");
$sumRaumbereich = 0;
$sumRaumbereichNeu = 0;
$sumRaumbereichBestand = 0;

$fill = 0;
foreach ($raumbereicheInProject as $rowData) {
    $sumRaumbereich = 0;
    $sumRaumbereichNeu = 0;
    $sumRaumbereichBestand = 0;
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(50, 4, $rowData['Raumbereich Nutzer'], 0, 'L', $fill, 0);
    $pdf->MultiCell(20, 4, $rowData['Geschoss'], 0, 'C', $fill, 0);
    foreach ($gewerkeInProject as $key => $rowDataGewerkeInProject) {
        $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
            FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.`Raumbereich Nutzer`)='" . $rowData['Raumbereich Nutzer'] . "') AND ((tabelle_räume.Geschoss)='" . $rowData['Geschoss'] . "') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='" . $rowDataGewerkeInProject['Gewerke_Nr'] . "') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1));";

        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        if (($row['PP']) != null) {
            $pdf->MultiCell(25, 4, format_money_report($row["PP"]), 0, 'R', $fill, 0);
            $sumRaumbereich = $sumRaumbereich + $row['PP'];
        } else {
            $pdf->MultiCell(25, 4, format_money_report(0), 0, 'R', $fill, 0);
        }
        $gewerkeInProject[$key]['GewerkeSummeGesamt'] = $gewerkeInProject[$key]['GewerkeSummeGesamt'] + $row['PP'];
    }
    $pdf->MultiCell(25, 4, format_money_report($sumRaumbereich), 0, 'R', $fill, 0);
    $pdf->Ln();
    $pdf->SetFont('helvetica', 'I', 6);

    // ------------------------------------Neu --------------------------------------------------
    $pdf->MultiCell(50, 4, 'davon Neu', 0, 'R', $fill, 0);
    $pdf->MultiCell(20, 4, '', 0, 'C', $fill, 0);

    foreach ($gewerkeInProject as $key => $rowDataGewerkeInProject) {
        $sql1 = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_Neu
                FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.`Raumbereich Nutzer`)='" . $rowData['Raumbereich Nutzer'] . "') AND ((tabelle_räume.Geschoss)='" . $rowData['Geschoss'] . "') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='" . $rowDataGewerkeInProject['Gewerke_Nr'] . "') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1);";
        $result1 = $mysqli->query($sql1);
        $row1 = $result1->fetch_assoc();
        if (null != ($row1['PP_Neu'])) {
            $pdf->MultiCell(25, 4, format_money_report($row1["PP_Neu"]), 0, 'R', $fill, 0);
            $sumRaumbereichNeu = $sumRaumbereichNeu + $row1['PP_Neu'];
        } else {
            $pdf->MultiCell(25, 4, format_money_report(0), 0, 'R', $fill, 0);
        }
        $gewerkeInProject[$key]['GewerkeSummeGesamtNeu'] += $row1['PP_Neu'];
    }
    $pdf->MultiCell(25, 4, format_money_report($sumRaumbereichNeu), 0, 'R', $fill, 0);
    $pdf->Ln();

    // ------------------------------------Bestand ----------------------------------------------
    $pdf->MultiCell(50, 4, 'davon Bestand', 0, 'R', $fill, 0);
    $pdf->MultiCell(20, 4, '', 0, 'C', $fill, 0);
    foreach ($gewerkeInProject as $key => $rowDataGewerkeInProject) {
        $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
                FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.`Raumbereich Nutzer`)='" . $rowData['Raumbereich Nutzer'] . "') AND ((tabelle_räume.Geschoss)='" . $rowData['Geschoss'] . "') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='" . $rowDataGewerkeInProject['Gewerke_Nr'] . "') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0);";
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        if (null != ($row['PP'])) {
            $pdf->MultiCell(25, 4, format_money_report($row["PP"]), 0, 'R', $fill, 0);
            $sumRaumbereichBestand = $sumRaumbereichBestand + $row['PP'];
        } else {
            $pdf->MultiCell(25, 4, format_money_report(0), 0, 'R', $fill, 0);
        }
        $gewerkeInProject[$key]['GewerkeSummeGesamtBestand'] +=  $row['PP'];
    }
    $pdf->MultiCell(25, 4, format_money_report($sumRaumbereichBestand), 0, 'R', $fill, 0);
    $pdf->Ln();
    $fill = !$fill;
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    if (($y + 4) >= 180) {
        $pdf->AddPage();
    }
}
// ---------------------------------------------------------
// Gesamtsumme ausgeben
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(50, 4, 'Gesamt', 'T', 'L', 0, 0);
$pdf->MultiCell(20, 4, '', 'T', 'R', 0, 0);
$sumGesamt = 0;
foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
    $pdf->MultiCell(25, 4, format_money_report($rowDataGewerkeInProject['GewerkeSummeGesamt']), 'T', 'R', 0, 0);
    $sumGesamt = $sumGesamt + $rowDataGewerkeInProject['GewerkeSummeGesamt'];
}
$pdf->MultiCell(25, 4, format_money_report($sumGesamt), 'T', 'R', 0, 0);

// Neusumme----------------------------------------------------
$pdf->Ln();
$pdf->SetFont('helvetica', 'BI', 6);
$pdf->MultiCell(50, 4, 'davon Neu', 0, 'R', 0, 0);
$pdf->MultiCell(20, 4, '', 0, 'L', 0, 0);
$sumGesamtNeu = 0;
foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
    $pdf->MultiCell(25, 4, format_money_report($rowDataGewerkeInProject['GewerkeSummeGesamtNeu']), 0, 'R', 0, 0);
    $sumGesamtNeu = $sumGesamtNeu + $rowDataGewerkeInProject['GewerkeSummeGesamtNeu'];
}
$pdf->MultiCell(25, 4, format_money_report($sumGesamtNeu), 0, 'R', 0, 0);

// Bestand von gesamtSumme-------------------------------------
$pdf->Ln();
$pdf->SetFont('helvetica', 'BI', 6);
$pdf->MultiCell(50, 4, 'davon Bestand', 0, 'R', 0, 0);
$pdf->MultiCell(20, 4, '', 0, 'L', 0, 0);
$sumGesamtBestand = 0;
foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
    $pdf->MultiCell(25, 4, format_money_report($rowDataGewerkeInProject['GewerkeSummeGesamtBestand']), 0, 'R', 0, 0);
    $sumGesamtBestand = $sumGesamtBestand + $rowDataGewerkeInProject['GewerkeSummeGesamtBestand'];
}
$pdf->MultiCell(25, 4, format_money_report($sumGesamtBestand), 0, 'R', 0, 0);

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName("KostenGesamt"), 'I');

