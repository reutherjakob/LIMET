<?php
#2025done
require_once '../_utils/_utils.php';
check_login();

require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";
include "_pdf_createBericht_utils.php";
include "../_utils/_format_money.php";
include_once "_pdf_kosten_utils.php";

$_SESSION["PDFTITEL"] = ($_SESSION["projectPlanungsphase"] == "Vorentwurf")
    ? "Medizintechnische Kostenschätzung"
    : "Medizintechnische Kostenberechnung";

$projectID = (int)$_SESSION["projectID"];
$inkl = kb_inkl_nicht_freigegeben();
$budgetWhere = kb_budget_where($inkl);

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, 20, 10, "A4", "Gesamt-Kosten");
$mysqli = utils_connect_sql();

$projekt = kb_projekt_info($mysqli, $projectID);

// ==================== DATEN ====================
$bereiche = kb_query($mysqli, "
    SELECT r.`Raumbereich Nutzer` AS Raumbereich, r.Geschoss
    " . KB_FROM_BASIS . "
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
    $budgetWhere 
    GROUP BY r.`Raumbereich Nutzer`, r.Geschoss
    ORDER BY r.Geschoss, r.`Raumbereich Nutzer`", 'i', [$projectID]);

// Alle Gewerk/GHG-Summen aller Raumbereiche in einer Abfrage
$kostenRows = kb_query($mysqli, "
    SELECT r.`Raumbereich Nutzer` AS Raumbereich, r.Geschoss,
           g.Gewerke_Nr, ghg.GHG,
           MAX(g.Bezeichnung) AS GewerkBezeichnung,
           MAX(ghg.Bezeichnung) AS GHGBezeichnung,
           " . KB_SELECT_KOSTEN . "
    " . KB_FROM_BASIS . KB_JOIN_KOSTEN . "
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
      AND rhe.Standort = 1
      $budgetWhere
    GROUP BY r.`Raumbereich Nutzer`, r.Geschoss, g.Gewerke_Nr, ghg.GHG
    ORDER BY g.Gewerke_Nr, ghg.GHG", 'i', [$projectID]);
$mysqli->close();

$kostenJeBereich = [];
foreach ($kostenRows as $row) {
    $kostenJeBereich[kb_key($row['Raumbereich'], $row['Geschoss'])][] = $row;
}

// ==================== AUSGABE: eine Seite je Raumbereich/Geschoss ====================
$erste = true;
foreach ($bereiche as $b) {
    if (!$erste) {
        $pdf->AddPage('P', 'A4');
    }
    $erste = false;

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(100, 6, "Kostenberechnung nach Gewerken und GHG", 'TL', 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Preisbasis: " . $projekt['Preisbasis'], 'TR', 'L', 0, 0);
    $pdf->Ln();
    $pdf->MultiCell(100, 6, "Bereich: " . $b['Raumbereich'], 'L', 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Projekt: " . $projekt['Projektname'], 'R', 'L', 0, 0);
    $pdf->Ln();
    $pdf->MultiCell(100, 6, "Geschoss: " . $b['Geschoss'], 'BL', 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Projektphase: " . $projekt['Bezeichnung'], 'BR', 'L', 0, 0);
    $pdf->Ln();
    kb_write_budget_hinweis($pdf, $inkl, 180);
    $pdf->Ln();

    kb_write_ghg_bericht($pdf, $kostenJeBereich[kb_key($b['Raumbereich'], $b['Geschoss'])] ?? [], 90, 30, 0);
}

kb_pdf_ausgeben($pdf, 'Kosten_Gewerke-GHG');
