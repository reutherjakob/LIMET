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
init_pdf_attributes($pdf, PDF_MARGIN_LEFT, 20, 10, "A4_queer", "Gesamtkosten-BA&Budget");
$mysqli = utils_connect_sql();

/**
 * Liste in n Spalten über die volle Seitenbreite.
 * Die Zeilenhöhe richtet sich nach dem längsten Eintrag der Zeile -> keine Überlappungen.
 */
function schreibeSpaltenListe($pdf, string $titel, array $eintraege, int $spalten = 3, string $fussnote = ''): void
{
    $m = $pdf->getMargins();
    $breite = $pdf->getPageWidth() - $m['left'] - $m['right'];
    $wSpalte = $breite / $spalten;

    if (kb_seitenumbruch_noetig($pdf, 12)) {
        $pdf->AddPage();
    }
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->MultiCell($breite, 5, $titel, 'B', 'L', 0, 1);
    $pdf->Ln(1);

    $pdf->SetFont('helvetica', '', 8);
    foreach (array_chunk($eintraege, $spalten) as $zeile) {
        $h = 0;
        foreach ($zeile as $text) {
            $h = max($h, $pdf->getStringHeight($wSpalte, $text, false, true, '', 0));
        }
        if (kb_seitenumbruch_noetig($pdf, $h)) {
            $pdf->AddPage();
        }
        foreach ($zeile as $text) {
            $pdf->MultiCell($wSpalte, $h, $text, 0, 'L', 0, 0);
        }
        $pdf->Ln($h);
    }

    if ($fussnote !== '') {
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->MultiCell($breite, 4, $fussnote, 0, 'L', 0, 1);
    }
    $pdf->Ln(4);
}

// ==================== SEITE 1: PROJEKT, GEWERKE, BUDGETS ====================
$m = $pdf->getMargins();
$seitenBreite = $pdf->getPageWidth() - $m['left'] - $m['right'];

kb_write_projektkopf($pdf, kb_projekt_info($mysqli, $projectID), $inkl, $seitenBreite);
$pdf->Ln(4);

$gewerkeEintraege = array_map(
    static fn($g) => $g['Gewerke_Nr'] . " - " . $g['Bezeichnung'],
    kb_gewerke_katalog($mysqli, $projectID)
);
schreibeSpaltenListe($pdf, "Gewerke", $gewerkeEintraege, 3);

$budgets = kb_query($mysqli, "
    SELECT idtabelle_projektbudgets, Budgetnummer, Budgetname, status
    FROM tabelle_projektbudgets
    WHERE tabelle_projekte_idTABELLE_Projekte = ?
    ORDER BY Budgetnummer", 'i', [$projectID]);

$budgetEintraege = [];
$gibtOffene = false;
foreach ($budgets as $b) {
    $status = $b['status'] === null ? null : (int)$b['status'];
    if ($status === KB_STATUS_ABGELEHNT) {
        continue; // abgelehnte Budgets werden nie ausgegeben
    }
    $offen = ($status !== KB_STATUS_FREIGEGEBEN);
    $gibtOffene = $gibtOffene || $offen;
    $budgetEintraege[] = $b['Budgetnummer'] . " - " . $b['Budgetname'] . ($offen ? ' *' : '');
}
$budgetEintraege[] = "NZ - nicht zugeordnet";
$fussnote = $gibtOffene
    ? ($inkl ? '* noch nicht freigegeben (in den Kosten enthalten)' : '* noch nicht freigegeben (in den Kosten nicht enthalten)')
    : '';
schreibeSpaltenListe($pdf, "Budgets", $budgetEintraege, 3, $fussnote);

// ==================== DATEN ====================
$gewerkeNr = kb_gewerke_im_projekt($mysqli, $projectID, $inkl);

$abschnitte = kb_query($mysqli, "
    SELECT r.Bauabschnitt
    " . KB_FROM_BASIS . "
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
    $budgetWhere
    GROUP BY r.Bauabschnitt
    ORDER BY r.Bauabschnitt", 'i', [$projectID]);

$kostenRows = kb_query($mysqli, "
    SELECT r.Bauabschnitt,
           rhe.tabelle_projektbudgets_idtabelle_projektbudgets AS BudgetID,
           MAX(pb.Budgetnummer) AS BudgetNr,
           g.Gewerke_Nr,
           " . KB_SELECT_KOSTEN . "
    " . KB_FROM_BASIS . KB_JOIN_KOSTEN . "
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
      AND rhe.Standort = 1
      AND g.Gewerke_Nr IS NOT NULL
      $budgetWhere
    GROUP BY r.Bauabschnitt, rhe.tabelle_projektbudgets_idtabelle_projektbudgets, g.Gewerke_Nr
    ORDER BY r.Bauabschnitt, BudgetNr", 'i', [$projectID]);
$mysqli->close();

// [Bauabschnitt][BudgetID] => ['label' => .., 'kosten' => [Gewerke_Nr => PP/PPNeu/PPBestand]]
$daten = [];
foreach ($kostenRows as $row) {
    $baKey = kb_key($row['Bauabschnitt']);
    $budgetKey = (string)((int)($row['BudgetID'] ?? 0));
    if (!isset($daten[$baKey][$budgetKey])) {
        $daten[$baKey][$budgetKey] = [
            'label' => ($budgetKey === '0') ? 'NZ' : (string)($row['BudgetNr'] ?? '?'),
            'kosten' => [],
        ];
    }
    $nr = (string)$row['Gewerke_Nr'];
    foreach (KB_KOSTENARTEN as $art) {
        $daten[$baKey][$budgetKey]['kosten'][$nr][$art] =
            ($daten[$baKey][$budgetKey]['kosten'][$nr][$art] ?? 0) + (float)$row[$art];
    }
}

// ==================== TABELLE (ab Seite 2) ====================
$w1 = 50;
$w2 = 20;
$w3 = 20;
// Gewerkespalten max. 25 mm, bei vielen Gewerken schmaler, damit die Tabelle auf die Seite passt
$wCol = min(25, ($seitenBreite - $w1 - $w2 - $w3) / (count($gewerkeNr) + 1));
$hBlock = 10;   // 3 Zeilen à 4 mm (Budget bzw. Summe)
$hBA = 4;       // Bauabschnitt-Zeile
$tabellenBreite = $w1 + $w2 + $w3 + $wCol * (count($gewerkeNr) + 1);
$linieNormal = ['width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$linieSumme = ['width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => [255, 0, 0]];

$schreibeKopf = static function () use ($pdf, $w1, $w2, $w3, $wCol, $gewerkeNr, $linieNormal) {
    $pdf->SetLineStyle($linieNormal);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell($w1, 6, "Bauabschnitt", 'B', 'L', 0, 0);
    $pdf->MultiCell($w2, 6, "Budget", 'B', 'C', 0, 0);
    $pdf->MultiCell($w3, 6, "", 'B', 'C', 0, 0);
    foreach ($gewerkeNr as $nr) {
        $pdf->MultiCell($wCol, 6, $nr, 'B', 'R', 0, 0);
    }
    $pdf->MultiCell($wCol, 6, "Gesamt", 'B', 'R', 0, 0);
    $pdf->Ln();
};

$schreibeBAZeile = static function (string $label) use ($pdf, $w1, $hBA, $tabellenBreite, $linieNormal) {
    $pdf->SetLineStyle($linieNormal);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell($w1, $hBA, $label, 'T', 'L', 1, 0);
    $pdf->MultiCell($tabellenBreite - $w1, $hBA, "", 'T', 'L', 1, 0);
    $pdf->Ln();
};

$neueTabellenSeite = static function () use ($pdf, $schreibeKopf) {
    $pdf->AddPage();
    $schreibeKopf();
};

$pdf->SetFillColor(244, 244, 244);
$neueTabellenSeite();

// verfügbare Höhe einer ganzen Tabellenseite (unter dem Kopf)
$hSeite = ($pdf->getPageHeight() - $pdf->getBreakMargin()) - $pdf->GetY();
$aufSeiteLeer = true;

foreach ($abschnitte as $a) {
    $baLabel = (string)$a['Bauabschnitt'];
    $budgetsBA = $daten[kb_key($a['Bauabschnitt'])] ?? [];
    $hGesamt = $hBA + $hBlock * count($budgetsBA) + $hBlock + 2;

    // Passt der ganze Bauabschnitt auf eine leere Seite, aber nicht mehr auf diese -> neue Seite.
    // Ist er länger als eine Seite, beginnt er hier, sobald zumindest BA-Zeile + erster Block passen.
    if (!$aufSeiteLeer) {
        $passtGanz = !kb_seitenumbruch_noetig($pdf, $hGesamt);
        $passtAnfang = !kb_seitenumbruch_noetig($pdf, $hBA + $hBlock);
        if ((!$passtGanz && $hGesamt <= $hSeite) || !$passtAnfang) {
            $neueTabellenSeite();
        }
    }

    $schreibeBAZeile($baLabel);
    $aufSeiteLeer = false;

    // Budgets
    $summenBA = [];
    foreach ($budgetsBA as $budget) {
        if (kb_seitenumbruch_noetig($pdf, $hBlock)) {
            $neueTabellenSeite();
            $schreibeBAZeile($baLabel . " (Fortsetzung)");
        }
        kb_write_kosten_zeilen($pdf, [
            [[$w1, '', 'L'], [$w2, $budget['label'], 'C'], [$w3, '', 'L']],
            [[$w1, '', 'L'], [$w2, '', 'L'], [$w3, 'davon Neu', 'L']],
            [[$w1, '', 'L'], [$w2, '', 'L'], [$w3, 'davon Bestand', 'L']],
        ], $gewerkeNr, $budget['kosten'], $summenBA, false, ['', 'I', 'I'], [6, 6, 6], [0, 0, 0], $wCol);
    }

    // Summe Bauabschnitt
    if (kb_seitenumbruch_noetig($pdf, $hBlock)) {
        $neueTabellenSeite();
        $schreibeBAZeile($baLabel . " (Fortsetzung)");
    }
    $pdf->SetLineStyle($linieSumme);
    $keine = null;
    kb_write_kosten_zeilen($pdf, [
        [[$w1, 'Summe', 'R'], [$w2, '', 'R'], [$w3, '', 'R']],
        [[$w1, '', 'R'], [$w2, '', 'R'], [$w3, 'davon Neu', 'L']],
        [[$w1, '', 'R'], [$w2, '', 'R'], [$w3, 'davon Bestand', 'L']],
    ], $gewerkeNr, $summenBA, $keine, false, ['BI', 'BI', 'BI'], [6, 6, 6], ['T', 0, 0], $wCol);
    $pdf->SetLineStyle($linieNormal);
    $pdf->Ln(2);
}

kb_pdf_ausgeben($pdf, "Gesamtkosten_BA&Budget");