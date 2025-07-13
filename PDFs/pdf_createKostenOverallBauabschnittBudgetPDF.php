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
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4_queer", "Gesamtkosten-BA&Budget");
check_login();
$mysqli = utils_connect_sql();

// TITELZEILE MIT PROJEKTINFOS--------------------------------------------------
$sql = "SELECT tabelle_projekte.Projektname, tabelle_projekte.Preisbasis,  tabelle_planungsphasen.Bezeichnung
    FROM tabelle_projekte INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen
    WHERE (((tabelle_projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";
$result = $mysqli->query($sql);

$row = $result->fetch_assoc();

//TITEL einfügen
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(5);
$pdf->MultiCell(150, 6, "Projekt: " . $row['Projektname'], '', 'L', 0, 0);
$xPosition = $pdf->getX();
$yPosition = $pdf->getY();
$pdf->Ln();
$pdf->MultiCell(150, 6, "Projektphase: " . $row['Bezeichnung'], '', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(150, 6, "Preisbasis: " . $row['Preisbasis'], '', 'L', 0, 0);
$pdf->Ln();

$sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.Bezeichnung
FROM tabelle_auftraggeber_gewerke INNER JOIN (tabelle_projekte INNER JOIN tabelle_auftraggeber_codes ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes) ON tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes
WHERE (((tabelle_projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";

$pdf->SetFont('helvetica', 'B', 7);
$pdf->setXY($xPosition, $yPosition);
$pdf->MultiCell(50, '', "Gewerke: ", '', 'L', 0, 0);
$pdf->Ln();
$pdf->SetFont('helvetica', 'I', 7);
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
    $pdf->MultiCell(150, '', "", '', 'L', 0, 0);
    $pdf->MultiCell(50, '', $row['Gewerke_Nr'] . "-" . $row['Bezeichnung'], '', 'L', 0, 0);
    $pdf->Ln();
}
$yPosition1 = $pdf->getY();

// data loading for header ----------------------------------
$sql = "SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
        FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
        GROUP BY tabelle_auftraggeber_gewerke.Gewerke_Nr
        ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";

$result = $mysqli->query($sql);
$gewerkeInProject = array();
while ($row = $result->fetch_assoc()) {
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['idTABELLE_Auftraggeber_Gewerke'] = $row['idTABELLE_Auftraggeber_Gewerke'];
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['Gewerke_Nr'] = $row['Gewerke_Nr'];
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamt'] = 0;
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtNeu'] = 0;
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtBestand'] = 0;
}
//--------------------------------------------------------------

$pdf->SetFont('helvetica', 'B', 7);
$pdf->setXY($xPosition, $yPosition);
$pdf->MultiCell(50, '', "", '', 'L', 0, 0);
$pdf->MultiCell(50, '', "Budgets", '', 'L', 0, 0);
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 7);
// Projektbudgets laden ----------------------------------
$sql = "SELECT tabelle_projektbudgets.tabelle_projekte_idTABELLE_Projekte, tabelle_projektbudgets.Budgetnummer, tabelle_projektbudgets.Budgetname, tabelle_projektbudgets.idtabelle_projektbudgets
        FROM tabelle_projektbudgets
        WHERE (((tabelle_projektbudgets.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
        ORDER BY tabelle_projektbudgets.Budgetnummer;";

$result = $mysqli->query($sql);
$budgetsInProject = array();
while ($row = $result->fetch_assoc()) {
    $budgetsInProject[$row['idtabelle_projektbudgets']]['idtabelle_projektbudgets'] = $row['idtabelle_projektbudgets'];
    $budgetsInProject[$row['idtabelle_projektbudgets']]['Budgetnummer'] = $row['Budgetnummer'];
    $budgetsInProject[$row['idtabelle_projektbudgets']]['Budgetname'] = $row['Budgetname'];

    $pdf->MultiCell(200, '', "", '', 'L', 0, 0);
    $pdf->MultiCell(50, '', $row['Budgetnummer'] . "-" . $row['Budgetname'], '', 'L', 0, 0);
    $pdf->Ln();
}
$yPosition2 = $pdf->getY();
//------------------------------------------------------------

if ($yPosition2 > $yPosition1) {
    $pdf->setY($yPosition2);
} else {
    $pdf->setY($yPosition1);
}

$pdf->Ln();
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(50, 6, "Bauabschnitt", 'B', 'L', 0, 0);
$pdf->MultiCell(20, 6, "Budget", 'B', 'C', 0, 0);
$pdf->MultiCell(20, 6, "", 'B', 'C', 0, 0);

foreach ($gewerkeInProject as $key => $rowData) {
    $gewerkeInProject[$key]['Position'] = $pdf->getX();
    //$rowData['Position'] = $pdf->getX();
    $pdf->MultiCell(25, 6, $rowData['Gewerke_Nr'], 'B', 'R', 0, 0);
}
$pdf->MultiCell(25, 6, "Gesamt", 'B', 'R', 0, 0);
$pdf->Ln();
// ---------------------------------------------------------

// data loading Raumbereiche ----------------------------------
$pdf->SetFont('helvetica', '', 8);
$pdf->SetFillColor(244, 244, 244);

$sql = "SELECT tabelle_räume.Bauabschnitt
FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
GROUP BY tabelle_räume.Bauabschnitt
ORDER BY tabelle_räume.Bauabschnitt;";

$result = $mysqli->query($sql);
$raumbereicheInProject = array();
$i = 1;
while ($row = $result->fetch_assoc()) {
    $raumbereicheInProject[$i]['Bauabschnitt'] = $row['Bauabschnitt'];
    $i++;
}
$totalRows = $result->num_rows;
$currentRow = 0; // Initialize a counter for rows
foreach ($raumbereicheInProject as $rowData) {
    $currentRow++;
    $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(50, 4, $rowData['Bauabschnitt'], 'T', 'L', 1, 0);
    $pdf->MultiCell(215, 4, "", 'T', 'L', 1, 0);
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 6);
    // ------------------------------------Budgets-----------------------------
    $sql1 = "SELECT tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets, tabelle_projektbudgets.Budgetnummer, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke, Sum(`Kosten`*`Anzahl`) AS PP, Sum(If(`Neu/Bestand`=1,`Kosten`*`Anzahl`,0)) AS PPNeu, Sum(If(`Neu/Bestand`=0,`Kosten`*`Anzahl`,0)) AS PPBestand
            FROM tabelle_projektbudgets RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) INNER JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON tabelle_projektbudgets.idtabelle_projektbudgets = tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets
            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.Bauabschnitt)='" . $rowData['Bauabschnitt'] . "') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
            GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets, tabelle_projektbudgets.Budgetnummer, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
            ORDER BY tabelle_projektbudgets.Budgetnummer;";

    $result1 = $mysqli->query($sql1);


    $oldBudget = -1; // Start
    $oldY = -1;
    $oldY1 = -1;
    $oldY2 = -1;


    while ($row1 = $result1->fetch_assoc()) {

        $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamt'] = 0;
        $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtNeu'] = 0;
        $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtBestand'] = 0;
        if (null != ($row1["tabelle_projektbudgets_idtabelle_projektbudgets"])) {
            $budget = $row1["tabelle_projektbudgets_idtabelle_projektbudgets"];
        } else {
            $budget = 0;
        }

        $xPosition = $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['Position'];

        if ($oldBudget !== $budget) {
            if ($row1["tabelle_projektbudgets_idtabelle_projektbudgets"] != 0) {
                $pdf->MultiCell(50, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, $budgetsInProject[$row1['tabelle_projektbudgets_idtabelle_projektbudgets']]['Budgetnummer'], 0, 'C', 0, 0);
                $pdf->setX($xPosition);
                $pdf->MultiCell(25, 4, format_money_report($row1["PP"]), 0, 'R', 0, 0);
                $oldY = $pdf->getY();
                $pdf->Ln();
                $pdf->SetFont('helvetica', 'I', 6);
                $pdf->MultiCell(50, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "davon Neu", 0, 'L', 0, 0);
                $pdf->setX($xPosition);
                $pdf->MultiCell(25, 4, format_money_report($row1["PPNeu"]), 0, 'R', 0, 0);
                $oldY1 = $pdf->getY();
                $pdf->Ln();
                $pdf->MultiCell(50, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "davon Bestand", 0, 'L', 0, 0);
                $pdf->setX($xPosition);
                $pdf->MultiCell(25, 4, format_money_report($row1["PPBestand"]), 0, 'R', 0, 0);
                $oldY2 = $pdf->getY();
                $pdf->Ln();
            } else {
                $pdf->MultiCell(50, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "NZ", 0, 'C', 0, 0);
                $pdf->setX($xPosition);
                $pdf->MultiCell(25, 4, format_money_report($row1["PP"]), 0, 'R', 0, 0);
                $oldY = $pdf->getY();
                $pdf->Ln();
                $pdf->SetFont('helvetica', 'I', 6);
                $pdf->MultiCell(50, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "davon Neu", 0, 'L', 0, 0);
                $pdf->setX($xPosition);
                $pdf->MultiCell(25, 4, format_money_report($row1["PPNeu"]), 0, 'R', 0, 0);
                $oldY1 = $pdf->getY();
                $pdf->Ln();
                $pdf->MultiCell(50, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "", 0, 'L', 0, 0);
                $pdf->MultiCell(20, 4, "davon Bestand", 0, 'L', 0, 0);
                $pdf->setX($xPosition);
                $pdf->MultiCell(25, 4, format_money_report($row1["PPBestand"]), 0, 'R', 0, 0);
                $oldY2 = $pdf->getY();
                $pdf->Ln();
            }
        } else {
            $pdf->setXY($xPosition, $oldY);
            $pdf->MultiCell(25, 4, format_money_report($row1["PP"]), 0, 'R', 0, 0);
            $pdf->setXY($xPosition, $oldY1);
            $pdf->MultiCell(25, 4, format_money_report($row1["PPNeu"]), 0, 'R', 0, 0);
            $pdf->setXY($xPosition, $oldY2);
            $pdf->MultiCell(25, 4, format_money_report($row1["PPBestand"]), 0, 'R', 0, 0);
            $pdf->Ln();
        }

        $oldBudget = $budget;
        $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamt'] = $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamt'] + $row1['PP'];
        $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtNeu'] = $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtNeu'] + $row1['PPNeu'];
        $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtBestand'] = $gewerkeInProject[$row1['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtBestand'] + $row1['PPBestand'];
    }
    $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 0, 0)));
    $pdf->SetFont('helvetica', 'BI', 6);
    $pdf->MultiCell(50, 4, 'Summe', 'T', 'R', 0, 0);
    $pdf->MultiCell(20, 4, '', 'T', 'R', 0, 0);
    $pdf->MultiCell(20, 4, '', 'T', 'R', 0, 0);
    $sumGesamt = 0;
    foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
        $pdf->MultiCell(25, 4, format_money_report($rowDataGewerkeInProject['GewerkeSummeGesamt']), 'T', 'R', 0, 0);
        $sumGesamt = $sumGesamt + $rowDataGewerkeInProject['GewerkeSummeGesamt'];
    }
    $pdf->MultiCell(25, 4, format_money_report($sumGesamt), 'T', 'R', 0, 0);
    // Neusumme----------------------------------------------------
    $pdf->Ln();
    $pdf->MultiCell(50, 4, '', 0, 'R', 0, 0);
    $pdf->MultiCell(20, 4, '', 0, 'R', 0, 0);
    $pdf->MultiCell(20, 4, 'davon Neu', 0, 'L', 0, 0);
    $sumGesamtNeu = 0;
    foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
        $pdf->MultiCell(25, 4, format_money_report($rowDataGewerkeInProject['GewerkeSummeGesamtNeu']), 0, 'R', 0, 0);
        $sumGesamtNeu = $sumGesamtNeu + $rowDataGewerkeInProject['GewerkeSummeGesamtNeu'];
    }
    $pdf->MultiCell(25, 4, format_money_report($sumGesamtNeu), 0, 'R', 0, 0);
    // Bestand von gesamtSumme-------------------------------------
    $pdf->Ln();
    $pdf->MultiCell(50, 4, '', 0, 'R', 0, 0);
    $pdf->MultiCell(20, 4, '', 0, 'R', 0, 0);
    $pdf->MultiCell(20, 4, 'davon Bestand', 0, 'L', 0, 0);
    $sumGesamtBestand = 0;
    foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
        $pdf->MultiCell(25, 4, format_money_report($rowDataGewerkeInProject['GewerkeSummeGesamtBestand']), 0, 'R', 0, 0);
        $sumGesamtBestand = $sumGesamtBestand + $rowDataGewerkeInProject['GewerkeSummeGesamtBestand'];
    }
    $pdf->MultiCell(25, 4, format_money_report($sumGesamtBestand), 0, 'R', 0, 0);

    $pdf->Ln();
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    if ((($y + 4) >= 180) && ($currentRow < $totalRows)) {
        $pdf->AddPage();
    }
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName("Gesamtkosten_BA&Budget"), 'I');

