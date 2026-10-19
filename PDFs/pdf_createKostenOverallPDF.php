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
$istProjekt80 = ($projectID === 80);

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, 20, 10, "A4_queer", "Medizintechnische Gesamt Kosten");
$mysqli = utils_connect_sql();

// ==================== KOPF + LEGENDEN ====================
$kopf = kb_write_projektkopf($pdf, kb_projekt_info($mysqli, $projectID), $inkl, 200);
kb_write_gewerke_legende($pdf, kb_gewerke_katalog($mysqli, $projectID), $kopf['x'], $kopf['y'], 1, 80);

if ($istProjekt80) {
    $pdf->Ln();
    kb_write_legende($pdf, "Bereiche", [
        "ZAE   = Haus C/D",
        "ZAE*  = U/Bs 1-6 ",
        "ZAE** = Zubau Haus B",
    ], $kopf['x'], $pdf->GetY(), 1, 80);
}
$pdf->SetY(max($pdf->GetY(), $kopf['yEnde']) + 4);

// ==================== DATEN ====================
$gewerkeNr = kb_gewerke_im_projekt($mysqli, $projectID, $inkl);

$bereiche = kb_query($mysqli, "
    SELECT r.`Raumbereich Nutzer` AS Raumbereich, r.Geschoss, MAX(r.Bauabschnitt) AS Bauabschnitt
    " . KB_FROM_BASIS . "
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
    $budgetWhere
    GROUP BY r.`Raumbereich Nutzer`, r.Geschoss
    ORDER BY r.Geschoss, r.`Raumbereich Nutzer`", 'i', [$projectID]);

$kosten = kb_kosten_je_gewerk($mysqli, $projectID, $inkl, ['r.`Raumbereich Nutzer`', 'r.Geschoss']);
$mysqli->close();

// ==================== TABELLE ====================
kb_write_tabellenkopf($pdf, "Bereich", "Geschoss", $gewerkeNr);
$pdf->SetFillColor(244, 244, 244);

$summen = [];
$fill = false;
foreach ($bereiche as $b) {
    if (kb_seitenumbruch_noetig($pdf, 12)) {
        $pdf->AddPage();
        kb_write_tabellenkopf($pdf, "Bereich", "Geschoss", $gewerkeNr);
    }
    $geschoss = $istProjekt80
        ? $b['Geschoss'] . " (Haus " . $b['Bauabschnitt'] . ")"
        : (string)$b['Geschoss'];

    kb_write_kosten_block($pdf, (string)$b['Raumbereich'], $geschoss, $gewerkeNr,
        $kosten[kb_key($b['Raumbereich'], $b['Geschoss'])] ?? [], $summen, $fill);
    $fill = !$fill;
}

if (kb_seitenumbruch_noetig($pdf, 12)) {
    $pdf->AddPage();
    kb_write_tabellenkopf($pdf, "Bereich", "Geschoss", $gewerkeNr);
}
kb_write_summen_block($pdf, 'Gesamt', $gewerkeNr, $summen);

kb_pdf_ausgeben($pdf, "KostenGesamt");
