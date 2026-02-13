<?php
#2025done
require_once '../utils/_utils.php';
check_login();

require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";
include "_pdf_createBericht_utils.php";
include "../utils/_format.php";

$_SESSION["PDFTITEL"] = ($_SESSION["projectPlanungsphase"] == "Vorentwurf")
    ? "Medizintechnische Kostenschätzung"
    : "Medizintechnische Kostenberechnung";
$_SESSION["PDFHeaderSubtext"] = "";

$marginTop = 20;
$marginBTM = 10;
$pageHeight = 180;
$w = array(45, 10);


$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4_queer", "Gesamt-Kosten");
$mysqli = utils_connect_sql();

// Get URL parameters
$roomBereiche = urldecode(filter_input(INPUT_GET, 'roomBereiche') ?? '');
$roomBereichGeschosse = urldecode(filter_input(INPUT_GET, 'roomBereichGeschosse') ?? '');
$selectedBereiche = $roomBereiche !== '' ? explode(',', $roomBereiche) : [];
$selectedGeschosse = $roomBereichGeschosse !== '' ? explode(',', $roomBereichGeschosse) : [];

// ==================== LOAD GEWERKE ====================
$stmt = $mysqli->prepare("
    SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
    FROM tabelle_auftraggeberg_gug 
    RIGHT JOIN (tabelle_auftraggeber_ghg 
        RIGHT JOIN (tabelle_auftraggeber_gewerke 
            RIGHT JOIN ((tabelle_räume 
                INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
                INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                    AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) 
            ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) 
        ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) 
    ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
    WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
    GROUP BY tabelle_auftraggeber_gewerke.Gewerke_Nr
    ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr
");

$stmt->bind_param('i', $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

$gewerkeInProject = array();
while ($row = $result->fetch_assoc()) {
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']] = [
        'Gewerke_Nr' => $row['Gewerke_Nr'],
        'GewerkeSummeGesamt' => 0,
        'GewerkeSummeGesamtNeu' => 0,
        'GewerkeSummeGesamtBestand' => 0
    ];
}
$stmt->close();

// ==================== LOAD PROJECT INFO ====================
$stmt = $mysqli->prepare("
    SELECT tabelle_projekte.Projektname, tabelle_projekte.Preisbasis, tabelle_planungsphasen.Bezeichnung
    FROM tabelle_projekte 
    INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen
    WHERE tabelle_projekte.idTABELLE_Projekte = ?
");

$stmt->bind_param('i', $_SESSION["projectID"]);
$stmt->execute();
$projectInfo = $stmt->fetch_assoc();
$stmt->close();

// ==================== PDF HEADER ====================
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(2);
$pdf->MultiCell(177, 6, "Projekt: " . $projectInfo['Projektname'], '', 'L', 0, 0);
$xPosition = $pdf->getX();
$yPosition = $pdf->getY();

$pdf->Ln();
$pdf->MultiCell(177, 6, "Projektphase: " . $projectInfo['Bezeichnung'], '', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(177, 6, "Preisbasis: " . $projectInfo['Preisbasis'], '', 'L', 0, 0);
$pdf->Ln(2);
$pdf->Ln();

// ==================== LOAD AND DISPLAY GEWERKE LIST ====================
$stmt = $mysqli->prepare("
    SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.Bezeichnung
    FROM tabelle_auftraggeber_gewerke 
    INNER JOIN (tabelle_projekte 
        INNER JOIN tabelle_auftraggeber_codes ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes) 
    ON tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes
    WHERE tabelle_projekte.idTABELLE_Projekte = ?
    ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr
");

$stmt->bind_param('i', $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

$pdf->SetFont('helvetica', 'B', 7);
$pdf->setXY($xPosition, $yPosition);
$pdf->MultiCell(80, '', "Gewerke: ", '', 'L', 0, 0);
$pdf->Ln();
$pdf->SetFont('helvetica', 'I', 7);

$modvar = 0;
while ($row = $result->fetch_assoc()) {
    $modvar = ($modvar + 1) % 2;
    if ($modvar === 1) {
        $pdf->MultiCell(177, '', "", '', 'L', 0, 0);
    }
    $pdf->MultiCell(50, '', $row['Gewerke_Nr'] . "-" . $row['Bezeichnung'], '', 'L', 0, 0);
    if ($modvar === 0) {
        $pdf->Ln();
    }
}
$stmt->close();

$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 10);

// ==================== TABLE HEADER ====================
$pdf->MultiCell($w[0] - 5, 6, "Bereich", 'B', 'L', 0, 0);
$pdf->MultiCell($w[1] + 10, 6, "Geschoss", 'B', 'C', 0, 0);
$abzug = -5;
foreach ($gewerkeInProject as $gewerk) {
    $pdf->MultiCell(25 + $abzug, 6, $gewerk['Gewerke_Nr'], 'B', 'R', 0, 0);
    $abzug = 0;
}
$pdf->MultiCell(25, 6, "Gesamt", 'B', 'R', 0, 0);
$pdf->Ln();

// ==================== LOAD ALL RAUMBEREICHE ====================
$stmt = $mysqli->prepare("
    SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
    FROM tabelle_auftraggeberg_gug 
    RIGHT JOIN (tabelle_auftraggeber_ghg 
        RIGHT JOIN (tabelle_auftraggeber_gewerke 
            RIGHT JOIN ((tabelle_räume 
                INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
                INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                    AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) 
            ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) 
        ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) 
    ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
    WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
    GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
    ORDER BY tabelle_räume.Geschoss
");

$stmt->bind_param('i', $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

$raumbereicheInProject = array();
while ($row = $result->fetch_assoc()) {
    $raumbereicheInProject[] = [
        'Raumbereich Nutzer' => $row['Raumbereich Nutzer'],
        'Geschoss' => $row['Geschoss']
    ];
}
$stmt->close();

// ==================== PREPARE COST QUERY STATEMENTS ====================
// Prepare statement for total costs
$stmtTotal = $mysqli->prepare("
    SELECT Sum(`Kosten`*`Anzahl`) AS PP
    FROM tabelle_projekt_varianten_kosten 
    INNER JOIN (tabelle_auftraggeber_gewerke 
        RIGHT JOIN ((tabelle_räume 
            INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
            INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) 
        ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) 
    ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
        AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) 
        AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
    WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
        AND tabelle_räume.`Raumbereich Nutzer` = ? 
        AND tabelle_räume.Geschoss = ? 
        AND tabelle_auftraggeber_gewerke.Gewerke_Nr = ? 
        AND tabelle_räume_has_tabelle_elemente.Standort = 1
");

// Prepare statement for Neu costs
$stmtNeu = $mysqli->prepare("
    SELECT Sum(`Kosten`*`Anzahl`) AS PP_neu
    FROM tabelle_projekt_varianten_kosten 
    INNER JOIN (tabelle_auftraggeber_gewerke 
        RIGHT JOIN ((tabelle_räume 
            INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
            INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) 
        ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) 
    ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
        AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) 
        AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
    WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
        AND tabelle_räume.`Raumbereich Nutzer` = ? 
        AND tabelle_räume.Geschoss = ? 
        AND tabelle_auftraggeber_gewerke.Gewerke_Nr = ? 
        AND tabelle_räume_has_tabelle_elemente.Standort = 1 
        AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 1
");

// Prepare statement for Bestand costs
$stmtBestand = $mysqli->prepare("
    SELECT Sum(`Kosten`*`Anzahl`) AS PP
    FROM tabelle_projekt_varianten_kosten 
    INNER JOIN (tabelle_auftraggeber_gewerke 
        RIGHT JOIN ((tabelle_räume 
            INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
            INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) 
        ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) 
    ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
        AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) 
        AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
    WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
        AND tabelle_räume.`Raumbereich Nutzer` = ? 
        AND tabelle_räume.Geschoss = ? 
        AND tabelle_auftraggeber_gewerke.Gewerke_Nr = ? 
        AND tabelle_räume_has_tabelle_elemente.Standort = 1 
        AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0
");

// ==================== HELPER FUNCTION ====================
function getCostForGewerk($stmt, $projectID, $raumbereich, $geschoss, $gewerkeNr) {
    $stmt->bind_param('isss', $projectID, $raumbereich, $geschoss, $gewerkeNr);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['PP'] ?? $row['PP_neu'] ?? 0;
}

function outputCostRow($pdf, $w, $gewerkeInProject, $stmt, $projectID, $raumbereich, $geschoss, $fill, $label = '', $fontSize = 8, $fontStyle = '') {
    global $stmtTotal, $stmtNeu, $stmtBestand;

    if ($label) {
        $pdf->SetFont('helvetica', $fontStyle, 6);
        $pdf->MultiCell($w[0], 4, $label, 0, 'R', $fill, 0);
        $pdf->MultiCell($w[1], 4, '', 0, 'C', $fill, 0);
    } else {
        $pdf->SetFont('helvetica', '', $fontSize);
        $pdf->MultiCell($w[0], 4, $raumbereich, 0, 'L', $fill, 0);
        $pdf->MultiCell($w[1], 4, $geschoss, 0, 'C', $fill, 0);
    }

    $sumRow = 0;
    foreach ($gewerkeInProject as $key => $gewerk) {
        $cost = getCostForGewerk($stmt, $projectID, $raumbereich, $geschoss, $gewerk['Gewerke_Nr']);
        $pdf->MultiCell(25, 4, format_money_report($cost), 0, 'R', $fill, 0);
        $sumRow += $cost;

        // Update totals
        if ($stmt === $stmtTotal) {
            $gewerkeInProject[$key]['GewerkeSummeGesamt'] += $cost;
        } elseif ($stmt === $stmtNeu) {
            $gewerkeInProject[$key]['GewerkeSummeGesamtNeu'] += $cost;
        } elseif ($stmt === $stmtBestand) {
            $gewerkeInProject[$key]['GewerkeSummeGesamtBestand'] += $cost;
        }
    }

    $pdf->MultiCell(25, 4, format_money_report($sumRow), 0, 'R', $fill, 0);
    $pdf->Ln();

    return $sumRow;
}

// ==================== GENERATE PDF CONTENT ====================
$pdf->SetFont('helvetica', '', 8);
$pdf->SetFillColor(244, 244, 244);
setlocale(LC_MONETARY, "de_DE");
$fill = 0;

foreach ($selectedBereiche as $idx => $selectedBereich) {
    $selectedGeschoss = $selectedGeschosse[$idx] ?? null;

    foreach ($raumbereicheInProject as $raumData) {
        if (trim($raumData['Raumbereich Nutzer']) === trim($selectedBereich) &&
            $selectedGeschoss !== null &&
            trim($raumData['Geschoss']) === trim($selectedGeschoss)) {

            // Check page break
            if ($pdf->GetY() >= $pageHeight) {
                $pdf->AddPage();
            }

            // Total costs
            outputCostRow($pdf, $w, $gewerkeInProject, $stmtTotal, $_SESSION["projectID"],
                $raumData['Raumbereich Nutzer'], $raumData['Geschoss'], $fill);

            // Neu costs
            outputCostRow($pdf, $w, $gewerkeInProject, $stmtNeu, $_SESSION["projectID"],
                $raumData['Raumbereich Nutzer'], $raumData['Geschoss'], $fill, 'davon Neu', 6, 'I');

            // Bestand costs
            outputCostRow($pdf, $w, $gewerkeInProject, $stmtBestand, $_SESSION["projectID"],
                $raumData['Raumbereich Nutzer'], $raumData['Geschoss'], $fill, 'davon Bestand', 6, 'I');

            $fill = !$fill;
        }
    }
}

// Close prepared statements
$stmtTotal->close();
$stmtNeu->close();
$stmtBestand->close();

// ==================== TOTALS SECTION ====================
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell($w[0], 4, 'Gesamt', 'T', 'L', 0, 0);
$pdf->MultiCell($w[1], 4, '', 'T', 'R', 0, 0);
$sumGesamt = 0;
foreach ($gewerkeInProject as $gewerk) {
    $pdf->MultiCell(25, 4, format_money_report($gewerk['GewerkeSummeGesamt']), 'T', 'R', 0, 0);
    $sumGesamt += $gewerk['GewerkeSummeGesamt'];
}
$pdf->MultiCell(25, 4, format_money_report($sumGesamt), 'T', 'R', 0, 0);
$pdf->Ln();

// Neu total
$pdf->SetFont('helvetica', 'BI', 6);
$pdf->MultiCell($w[0], 4, 'davon Neu', 0, 'R', 0, 0);
$pdf->MultiCell($w[1], 4, '', 0, 'L', 0, 0);
$sumGesamtNeu = 0;
foreach ($gewerkeInProject as $gewerk) {
    $pdf->MultiCell(25, 4, format_money_report($gewerk['GewerkeSummeGesamtNeu']), 0, 'R', 0, 0);
    $sumGesamtNeu += $gewerk['GewerkeSummeGesamtNeu'];
}
$pdf->MultiCell(25, 4, format_money_report($sumGesamtNeu), 0, 'R', 0, 0);
$pdf->Ln();

// Bestand total
$pdf->MultiCell($w[0], 4, 'davon Bestand', 0, 'R', 0, 0);
$pdf->MultiCell($w[1], 4, '', 0, 'L', 0, 0);
$sumGesamtBestand = 0;
foreach ($gewerkeInProject as $gewerk) {
    $pdf->MultiCell(25, 4, format_money_report($gewerk['GewerkeSummeGesamtBestand']), 0, 'R', 0, 0);
    $sumGesamtBestand += $gewerk['GewerkeSummeGesamtBestand'];
}
$pdf->MultiCell(25, 4, format_money_report($sumGesamtBestand), 0, 'R', 0, 0);

// ==================== OUTPUT PDF ====================
$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Kosten-je-Raumbereich'), 'I');