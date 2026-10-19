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
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, 17, 10, "A4_queer", "Medizintechnische Kostenberechnung");
$mysqli = utils_connect_sql();

// ==================== KOPF + LEGENDE ====================
$pdf->Ln(2);
$kopf = kb_write_projektkopf($pdf, kb_projekt_info($mysqli, $projectID), $inkl, 177);
$yLegende = kb_write_gewerke_legende($pdf, kb_gewerke_katalog($mysqli, $projectID), $kopf['x'], $kopf['y'], 2, 50);
$pdf->SetY(max($yLegende, $kopf['yEnde']) + 6);

// ==================== DATEN ====================
$gewerkeNr = kb_gewerke_im_projekt($mysqli, $projectID, $inkl);

$abschnitte = kb_query($mysqli, "
    SELECT r.Bauabschnitt
    " . KB_FROM_BASIS . "
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
    $budgetWhere
    GROUP BY r.Bauabschnitt
    ORDER BY r.Bauabschnitt", 'i', [$projectID]);

$kosten = kb_kosten_je_gewerk($mysqli, $projectID, $inkl, ['r.Bauabschnitt']);
$mysqli->close();

// ==================== TABELLE ====================
kb_write_tabellenkopf($pdf, "Bauabschnitt", "", $gewerkeNr);
$pdf->SetFillColor(244, 244, 244);

$summen = [];
$fill = false;
foreach ($abschnitte as $a) {
    if (kb_seitenumbruch_noetig($pdf, 12)) {
        $pdf->AddPage();
        kb_write_tabellenkopf($pdf, "Bauabschnitt", "", $gewerkeNr);
    }
    kb_write_kosten_block($pdf, (string)$a['Bauabschnitt'], '', $gewerkeNr,
        $kosten[kb_key($a['Bauabschnitt'])] ?? [], $summen, $fill);
    $fill = !$fill;
}

if (kb_seitenumbruch_noetig($pdf, 12)) {
    $pdf->AddPage();
    kb_write_tabellenkopf($pdf, "Bauabschnitt", "", $gewerkeNr);
}
kb_write_summen_block($pdf, 'Gesamt', $gewerkeNr, $summen);

kb_pdf_ausgeben($pdf, 'MT-Kosten-Bauabschnitt');
