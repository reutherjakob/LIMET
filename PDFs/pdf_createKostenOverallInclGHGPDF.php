<?php
#2025done
require_once '../_utils/_utils.php';
include "../_utils/_format_money.php";
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";
require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";
include_once "_pdf_kosten_utils.php";
check_login();

$_SESSION["PDFTITEL"] = ($_SESSION["projectPlanungsphase"] == "Vorentwurf")
    ? "Medizintechnische Kostenschätzung"
    : "Medizintechnische Kostenberechnung";

$projectID = (int)$_SESSION["projectID"];
$inkl = kb_inkl_nicht_freigegeben();
$budgetWhere = kb_budget_where($inkl);

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, 17, 10, "A4", "Medizintechnische Gesamt Kosten");
$mysqli = utils_connect_sql();

$projekt = kb_projekt_info($mysqli, $projectID);

// ==================== KOPFBOX ====================
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetY(19);
$pdf->MultiCell(180, 6, "Kostenberechnung nach Gewerken und GHG", 'TLR', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(90, 6, "Projekt: " . $projekt['Projektname'], 'L', 'L', 0, 0);
$pdf->MultiCell(90, 6, "Preisbasis: " . $projekt['Preisbasis'], 'R', 'L', 0, 0);
$pdf->Ln();
$pdf->MultiCell(180, 6, "Projektphase: " . $projekt['Bezeichnung'], 'BRL', 'L', 0, 0);
$pdf->Ln();
kb_write_budget_hinweis($pdf, $inkl, 180);
$pdf->Ln();

// ==================== DATEN (eine Abfrage) ====================
$zeilen = kb_query($mysqli, "
    SELECT g.Gewerke_Nr, ghg.GHG,
           MAX(g.Bezeichnung) AS GewerkBezeichnung,
           MAX(ghg.Bezeichnung) AS GHGBezeichnung,
           " . KB_SELECT_KOSTEN . "
    " . KB_FROM_BASIS . KB_JOIN_KOSTEN . "
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
      AND rhe.Standort = 1
      $budgetWhere
    GROUP BY g.Gewerke_Nr, ghg.GHG
    ORDER BY g.Gewerke_Nr, ghg.GHG", 'i', [$projectID]);
$mysqli->close();

// ==================== AUSGABE ====================
kb_write_ghg_bericht($pdf, $zeilen, 70, 50, 20);

kb_pdf_ausgeben($pdf, "Kosten_Gewerke-GHG");
