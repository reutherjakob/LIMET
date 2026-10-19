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
$_SESSION["PDFHeaderSubtext"] = "";

$projectID = (int)$_SESSION["projectID"];
$inkl = kb_inkl_nicht_freigegeben();

// ==================== AUSWAHL (GET, JSON) ====================
// Erwartet: auswahl=[{"bereich": "...", "geschoss": "..."}, ...]
$auswahl = [];
$auswahlRoh = json_decode((string)filter_input(INPUT_GET, 'auswahl'), true);
if (is_array($auswahlRoh)) {
    foreach ($auswahlRoh as $eintrag) {
        if (!is_array($eintrag)) {
            continue;
        }
        $bereich = $eintrag['bereich'] ?? null;
        $geschoss = $eintrag['geschoss'] ?? null;
        if (!is_scalar($bereich) || !(is_scalar($geschoss) || $geschoss === null)) {
            continue;
        }
        $auswahl[kb_key($bereich, $geschoss)] = ['bereich' => (string)$bereich, 'geschoss' => (string)$geschoss];
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, 20, 10, "A4_queer", "Gesamt-Kosten");
$mysqli = utils_connect_sql();

// ==================== KOPF + LEGENDE ====================
$pdf->Ln(2);
$kopf = kb_write_projektkopf($pdf, kb_projekt_info($mysqli, $projectID), $inkl, 177);
$yLegende = kb_write_gewerke_legende($pdf, kb_gewerke_katalog($mysqli, $projectID), $kopf['x'], $kopf['y'], 2, 50);
$pdf->SetY(max($yLegende, $kopf['yEnde']) + 6);

// ==================== DATEN ====================
$gewerkeNr = kb_gewerke_im_projekt($mysqli, $projectID, $inkl);
$kosten = kb_kosten_je_gewerk($mysqli, $projectID, $inkl, ['r.`Raumbereich Nutzer`', 'r.Geschoss']);
$mysqli->close();

// ==================== TABELLE ====================
kb_write_tabellenkopf($pdf, "Bereich", "Geschoss", $gewerkeNr);
$pdf->SetFillColor(244, 244, 244);

if (empty($auswahl)) {
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->MultiCell(0, 6, "Kein Raumbereich ausgewählt.", 0, 'L', 0, 1);
}

$summen = [];
$fill = false;
foreach ($auswahl as $key => $a) {
    if (kb_seitenumbruch_noetig($pdf, 12)) {
        $pdf->AddPage();
        kb_write_tabellenkopf($pdf, "Bereich", "Geschoss", $gewerkeNr);
    }
    kb_write_kosten_block($pdf, $a['bereich'], $a['geschoss'], $gewerkeNr, $kosten[$key] ?? [], $summen, $fill);
    $fill = !$fill;
}

if (kb_seitenumbruch_noetig($pdf, 12)) {
    $pdf->AddPage();
    kb_write_tabellenkopf($pdf, "Bereich", "Geschoss", $gewerkeNr);
}
kb_write_summen_block($pdf, 'Gesamt', $gewerkeNr, $summen);

kb_pdf_ausgeben($pdf, 'Kosten-je-Raumbereich');
