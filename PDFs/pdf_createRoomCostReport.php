<?php
// pdf_createRoomCostReport.php
require_once '../utils/_utils.php';
include "../utils/_format.php";
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";

require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";

check_login();

// Get room ID from GET
$roomID = isset($_GET['roomID']) ? intval($_GET['roomID']) : 0;
if ($roomID <= 0) {
    die("Ungültige Raum-ID");
}

$mysqli = utils_connect_sql();

// Set PDF title based on project phase
if ($_SESSION["projectPlanungsphase"] == "Vorentwurf") {
    $_SESSION["PDFTITEL"] = "Medizintechnische Kostenschätzung";
} else {
    $_SESSION["PDFTITEL"] = "Medizintechnische Kostenberechnung";
}

// Initialize PDF
$marginTop = 22;
$marginBottom = 10;
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBottom, "A4_queer", "Raumkostenbericht");
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// === Fetch project info ===
$sql_project = "SELECT tabelle_projekte.Projektname, tabelle_projekte.Preisbasis, tabelle_planungsphasen.Bezeichnung
    FROM tabelle_projekte
    INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen
    WHERE tabelle_projekte.idTABELLE_Projekte = ?";
$stmt = $mysqli->prepare($sql_project);
$stmt->bind_param('i', $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

// --- Print project header on the top left ---
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(PDF_MARGIN_LEFT, $marginTop);
$pdf->MultiCell(0, 6, "Projekt: " . $project['Projektname'], 0, 'L');
$pdf->MultiCell(0, 6, "Projektphase: " . $project['Bezeichnung'], 0, 'L');
$pdf->MultiCell(0, 6, "Preisbasis: " . $project['Preisbasis'], 0, 'L');

$ytemp = $pdf->GetY();

// === Fetch Gewerke legend ===
$sql_gewerke = "SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.Bezeichnung
FROM tabelle_auftraggeber_gewerke
INNER JOIN tabelle_auftraggeber_codes ON tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes
WHERE tabelle_auftraggeber_codes.idTABELLE_Auftraggeber_Codes = 
    (SELECT TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes FROM tabelle_projekte WHERE idTABELLE_Projekte = ?)
ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";
$stmt = $mysqli->prepare($sql_gewerke);
$stmt->bind_param('i', $_SESSION["projectID"]);
$stmt->execute();
$resultGewerke = $stmt->get_result();

// --- Print Gewerke legend top right ---
$xRight = 297 - 15 - 70;
$yTop = $marginTop;
$pdf->SetXY($xRight, $yTop);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(70, 6, "Gewerke:", 0, 'R');

$pdf->SetFont('helvetica', 'I', 7);
$currentY = $pdf->GetY();
while ($row = $resultGewerke->fetch_assoc()) {
    $pdf->SetXY($xRight, $currentY);
    $pdf->MultiCell(70, 5, $row['Gewerke_Nr'] . " - " . $row['Bezeichnung'], 0, 'R');
    $currentY = $pdf->GetY();
}
$stmt->close();

// Start new section below headers, leaving space for Gewerke legend height
$startingY = max($pdf->GetY(), $marginTop + 20);
$pdf->SetY($startingY);

// === Functions to calculate costs ===
function calculateCosts($mysqli, $sql, $roomID, $projectID)
{
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $roomID, $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    $sum = 0;
    $costs = ['ortsfest' => 0, 'ortsveränderlich' => 0];

    while ($row = $result->fetch_assoc()) {
        $summe = isset($row["Summe_Neu"]) ? (float)$row["Summe_Neu"] : (float)$row["Summe_Bestand"];
        $sum += $summe;
        if (preg_match('/^[1345]/', $row["ElementID"] ?? '')) {
            $costs['ortsfest'] += $summe;
        } else {
            $costs['ortsveränderlich'] += $summe;
        }
    }
    $stmt->close();
    return ['sum' => $sum, 'costs' => $costs];
}

// SQL for new elements costs
$sql_new = "SELECT 
    SUM(traeume_elem.Anzahl * proj_kosten.Kosten) AS Summe_Neu,
    elem.ElementID
FROM tabelle_räume_has_tabelle_elemente AS traeume_elem
INNER JOIN tabelle_projekt_varianten_kosten AS proj_kosten ON 
    proj_kosten.tabelle_elemente_idTABELLE_Elemente = traeume_elem.TABELLE_Elemente_idTABELLE_Elemente AND
    proj_kosten.tabelle_Varianten_idtabelle_Varianten = traeume_elem.tabelle_Varianten_idtabelle_Varianten
INNER JOIN tabelle_elemente AS elem ON elem.idTABELLE_Elemente = traeume_elem.TABELLE_Elemente_idTABELLE_Elemente
WHERE traeume_elem.TABELLE_Räume_idTABELLE_Räume = ? AND traeume_elem.Standort = 1 AND proj_kosten.tabelle_projekte_idTABELLE_Projekte = ? AND traeume_elem.`Neu/Bestand` = 1  
GROUP BY elem.ElementID
";

// SQL for existing elements costs
$sql_existing = "SELECT 
    SUM(traeume_elem.Anzahl * proj_kosten.Kosten) AS Summe_Bestand,
    elem.ElementID
FROM tabelle_räume_has_tabelle_elemente AS traeume_elem
INNER JOIN tabelle_projekt_varianten_kosten AS proj_kosten ON 
    proj_kosten.tabelle_elemente_idTABELLE_Elemente = traeume_elem.TABELLE_Elemente_idTABELLE_Elemente AND
    proj_kosten.tabelle_Varianten_idtabelle_Varianten = traeume_elem.tabelle_Varianten_idtabelle_Varianten
INNER JOIN tabelle_elemente AS elem ON elem.idTABELLE_Elemente = traeume_elem.TABELLE_Elemente_idTABELLE_Elemente
WHERE traeume_elem.TABELLE_Räume_idTABELLE_Räume = ? AND traeume_elem.Standort = 1 AND proj_kosten.tabelle_projekte_idTABELLE_Projekte = ? AND traeume_elem.`Neu/Bestand` = 0 
GROUP BY elem.ElementID
";

// Calculate costs
$new_costs = calculateCosts($mysqli, $sql_new, $roomID, $_SESSION["projectID"]);
$existing_costs = calculateCosts($mysqli, $sql_existing, $roomID, $_SESSION["projectID"]);

$SummeNeu = $new_costs['sum'];
$SummeBestand = $existing_costs['sum'];
$SummeGesamt = $SummeNeu + $SummeBestand;
$Kosten_ortsfest = $new_costs['costs']['ortsfest'] + $existing_costs['costs']['ortsfest'];
$Kosten_ortsveraenderlich = $new_costs['costs']['ortsveränderlich'] + $existing_costs['costs']['ortsveränderlich'];

// Format money values
$formattedNumberGesamt = format_money_report($SummeGesamt);
$formattedNumberNeu = format_money_report($SummeNeu);
$formattedNumberBestand = format_money_report($SummeBestand);
$formattedKostenOrtsfest = format_money_report($Kosten_ortsfest);
$formattedKostenOrtsveraenderlich = format_money_report($Kosten_ortsveraenderlich);

// === Output key room cost summary as a table ===
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$labelWidth = 55;
$valueWidth = 35;
$cellHeight = 8;


// Define a helper to print label/value row with stripes
function printSummaryRow($pdf, $label, $value, $fill)
{
    global $labelWidth, $valueWidth, $cellHeight;
    $pdf->SetFillColor($fill ? 230 : 255);
    $pdf->Cell($labelWidth, $cellHeight, $label, 1, 0, 'L', $fill);
    $pdf->Cell($valueWidth, $cellHeight, $value, 1, 1, 'R', $fill);
}


$pdf->SetY($ytemp + 10);
$fill = true;
printSummaryRow($pdf, "Raumkosten (Gesamt)", $formattedNumberGesamt, $fill);
$fill = !$fill;
printSummaryRow($pdf, "Neu", $formattedNumberNeu, $fill);
$fill = !$fill;
printSummaryRow($pdf, "Bestand", $formattedNumberBestand, $fill);
$fill = !$fill;
printSummaryRow($pdf, "Ortsfest", $formattedKostenOrtsfest, $fill);
$fill = !$fill;
printSummaryRow($pdf, "Ortsveränderlich", $formattedKostenOrtsveraenderlich, $fill);

$pdf->Ln(10);
// === Fetch room elements with variant cost and gewerk info (LEFT JOIN to allow missing cost or gewerk) ===
$sql_room_elements = "
SELECT 
    ele_me.id,
    ele_me.Anzahl,
    ele.ElementID,
    ele.Bezeichnung,
    var.Variante,
    ele_me.`Neu/Bestand`,
    kosten.Kosten AS VariantenKosten,
    agg.Gewerke_Nr,
    agg.Bezeichnung AS GewerkeBezeichnung
FROM tabelle_räume_has_tabelle_elemente ele_me
INNER JOIN tabelle_elemente ele ON ele_me.TABELLE_Elemente_idTABELLE_Elemente = ele.idTABELLE_Elemente
INNER JOIN tabelle_varianten var ON ele_me.tabelle_Varianten_idtabelle_Varianten = var.idtabelle_Varianten
LEFT JOIN tabelle_projekt_varianten_kosten kosten ON 
    kosten.tabelle_elemente_idTABELLE_Elemente = ele_me.TABELLE_Elemente_idTABELLE_Elemente AND
    kosten.tabelle_Varianten_idtabelle_Varianten = ele_me.tabelle_Varianten_idtabelle_Varianten AND
    kosten.tabelle_projekte_idTABELLE_Projekte = ?
LEFT JOIN tabelle_projekt_element_gewerk peg ON 
    peg.tabelle_elemente_idTABELLE_Elemente = ele_me.TABELLE_Elemente_idTABELLE_Elemente AND
    peg.tabelle_projekte_idTABELLE_Projekte = ?
LEFT JOIN tabelle_auftraggeber_gewerke agg ON
    agg.idTABELLE_Auftraggeber_Gewerke = peg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
WHERE ele_me.TABELLE_Räume_idTABELLE_Räume = ? AND ele_me.Anzahl <> 0 
ORDER BY ele.ElementID ASC;
";

$stmt = $mysqli->prepare($sql_room_elements);
$stmt->bind_param('iii', $_SESSION["projectID"], $_SESSION["projectID"], $roomID);
$stmt->execute();
$result_room_elements = $stmt->get_result();

// === Render elements table ===
$columnWidths = [20, 30, 90, 20, 20, 20, 30];
$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetFillColor(200, 200, 200);


$pdf->Cell($columnWidths[1], 8, "Element ID", 1, 0, 'L', true);
$pdf->Cell($columnWidths[2], 8, "Bezeichnung", 1, 0, 'L', true);
$pdf->Cell($columnWidths[3], 8, "Variante", 1, 0, 'C', true);
$pdf->Cell($columnWidths[4], 8, "Anzahl", 1, 0, 'C', true);
$pdf->Cell($columnWidths[5], 8, "Neu/Bestand", 1, 0, 'C', true);
$pdf->Cell($columnWidths[0], 8, "Gewerk", 1, 0, 'C', true);
$pdf->Cell($columnWidths[6], 8, "Gesamtkosten (€)", 1, 1, 'R', true);

$pdf->SetFont('helvetica', '', 8);
$fill = false;
while ($row = $result_room_elements->fetch_assoc()) {

    if ($pdf->GetY() > 180) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(200, 200, 200);

        $pdf->Cell($columnWidths[1], 8, "Element ID", 1, 0, 'L', true);
        $pdf->Cell($columnWidths[2], 8, "Bezeichnung", 1, 0, 'L', true);
        $pdf->Cell($columnWidths[3], 8, "Variante", 1, 0, 'C', true);
        $pdf->Cell($columnWidths[4], 8, "Anzahl", 1, 0, 'C', true);
        $pdf->Cell($columnWidths[5], 8, "Neu/Bestand", 1, 0, 'C', true);
        $pdf->Cell($columnWidths[0], 8, "Gewerk", 1, 0, 'C', true);
        $pdf->Cell($columnWidths[6], 8, "Gesamtkosten (€)", 1, 1, 'R', true);
        $pdf->SetFont('helvetica', '', 8);
    }

    $gewerk = !empty($row['Gewerke_Nr']) ? trim($row['Gewerke_Nr']) : '';
    $elementID = $row['ElementID'] ?? '';
    $bezeichnung = $row['Bezeichnung'] ?? '';
    $variante = $row['Variante'] ?? '';
    $anzahl = (int)$row['Anzahl'];
    $neuBestand = isset($row['Neu/Bestand']) ? ($row['Neu/Bestand'] == 1 ? "Neu" : "Bestand") : '';
    $costSingle = isset($row['VariantenKosten']) ? (float)$row['VariantenKosten'] : null;
    $totalCost = ($costSingle !== null) ? $anzahl * $costSingle : null;
    $formattedTotalCost = ($totalCost !== null) ? format_money_report($totalCost) : '';

    $pdf->SetFillColor($fill ? 245 : 255);

    $pdf->Cell($columnWidths[1], 7, $elementID, 1, 0, 'L', $fill);
    $pdf->Cell($columnWidths[2], 7, $bezeichnung, 1, 0, 'L', $fill);
    $pdf->Cell($columnWidths[3], 7, $variante, 1, 0, 'C', $fill);
    $pdf->Cell($columnWidths[4], 7, $anzahl, 1, 0, 'C', $fill);
    $pdf->Cell($columnWidths[5], 7, $neuBestand, 1, 0, 'C', $fill);
    $pdf->Cell($columnWidths[0], 7, $gewerk, 1, 0, 'C', $fill);
    $pdf->Cell($columnWidths[6], 7, $formattedTotalCost, 1, 1, 'R', $fill);

    $fill = !$fill;
}


$stmt->close();
$mysqli->close();
ob_end_clean();
$pdf->Output("Raumkostenbericht_Raum{$roomID}.pdf", 'I');
exit();