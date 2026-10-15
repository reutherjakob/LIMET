<?php
/**
 * ============================================================================
 *  MED.-TECH.-TABELLEN  –  RENDERING (Detail-Tabelle & Auflistung)
 * ============================================================================
 *
 *  Reines Rendering. Die Datensätze (Elemente, Parameter-Definitionen,
 *  Parameter-Werte, Änderungen) werden vom Daten-Layer (pdf_report_data.php)
 *  vorbereitet und hier nur dargestellt. Maße kommen aus REPORT_MT / REPORT_STYLE
 *  (pdf_report_layout.php).
 *
 *  Öffentliche Funktionen:
 *    make_MT_details_table(...)  – breite Tabelle mit Elementen + Parametern
 *    make_MT_list(...)           – kompakte Auflistung der Elemente
 * ============================================================================
 */

require_once '../../_utils/_utils.php';
check_login();


// ----------------------------------------------------------------------------
//  KLEINE HELFER
// ----------------------------------------------------------------------------

/**
 * Prüft, ob für Element/Parameter ein Änderungseintrag vorliegt.
 */
function checkEntry($jsonArray, $elementId, $parameterId): bool
{
    foreach ($jsonArray as $entry) {
        if ($entry['element'] == $elementId && $entry['parameter'] == $parameterId) {
            return true;
        }
    }
    return false;
}

/**
 * Korrigiert problematische Einheiten-Strings.
 */
function checkAndManipulateString($input)
{
    if ($input === "\'\'" || $input === '\"') {
        return '"';
    }
    if (str_contains($input, '/min')) {
        $input = ' ' . $input;
    }
    return $input;
}

/**
 * Abkürzungs-Legende unter der Tabelle ("Abk-Bezeichnung; ...").
 */
function abk_vz($paramInfos, $pdf, $f_size): void
{
    if (empty($paramInfos)) {
        return;
    }
    $pdf->MultiCell(20, $f_size, "Abkürzungen: ", 0, 'L', 0, 0, '', '', true, 0, false, false, 0);
    foreach ($paramInfos as $array) {
        $w = $pdf->GetStringWidth($array['Bezeichnung'] . "-", 'courier', 'B', $f_size);
        if (($pdf->GetX() + $w) >= 400) {
            $pdf->Ln($f_size / 2);
        }
        $pdf->SetFont('courier', 'B', $f_size);
        $pdf->MultiCell($w + 3, $f_size, $array['Bezeichnung'] . "-", 0, 'R', 0, 0, '', '', true, 0, false, false, 0);

        $w = $pdf->GetStringWidth($array['Bezeichnung'] . ";", 'courier', '', $f_size);
        if (($pdf->GetX() + $w) >= 400) {
            $pdf->Ln($f_size / 2);
        }
        $pdf->SetFont('courier', '', $f_size);
        $pdf->MultiCell($w + 3, $f_size, $array['Bezeichnung'] . ";", 0, 'L', 0, 0, '', '', true, 0, false, false, 0);
    }
    $pdf->SetFont('courier', 'B', $f_size);
}


// ----------------------------------------------------------------------------
//  DETAIL-TABELLE
// ----------------------------------------------------------------------------

/**
 * Zeichnet den zweizeiligen Tabellenkopf (fixe Spalten + Parameter-Spalten,
 * darüber retrospektiv die Kategorie-Überschriften). Wird beim Start und nach
 * jedem Seitenumbruch identisch verwendet.
 */
function mt_draw_detail_header($pdf, array $paramInfos, float $text_width): void
{
    $cols = REPORT_MT['detail_cols'];
    $rh   = REPORT_MT['detail_row_h'];

    $pdf->SetFont('courier', 'B', REPORT_MT['detail_font']);
    $hX = $pdf->GetX();     // Position der OBEREN Kopfzeile (Kategorien)
    $hY = $pdf->GetY();
    $lastCategory = "";
    $pdf->Ln($rh);          // untere Kopfzeile beginnt darunter

    // fixe Spalten
    $labels = ["ID", "Element", "Var", "Stk", "Bestand", "Ort", "Verw."];
    foreach ($labels as $i => $lbl) {
        $pdf->MultiCell($cols[$i], $rh, $lbl, 1, 'C', 0, 0);
    }

    // Parameter-Spalten + Kategorie-Überschriften (oben, retrospektiv spannend)
    foreach ($paramInfos as $array) {
        if ($lastCategory != $array['Kategorie']) {
            $curX = $pdf->GetX();
            $curY = $pdf->GetY();
            $pdf->SetXY($hX, $hY);
            $pdf->MultiCell($curX - $hX, $rh, "MT " . $lastCategory, 1, 'C', 0, 0);
            $hX = $pdf->GetX();
            $hY = $pdf->GetY();
            $lastCategory = $array['Kategorie'];
            $pdf->SetXY($curX, $curY);
        }
        $pdf->MultiCell($text_width, $rh, $array['Bezeichnung'], 1, 'C', 0, 0);
    }

    // letzte Kategorie-Überschrift
    $curX = $pdf->GetX();
    $curY = $pdf->GetY();
    $pdf->SetXY($hX, $hY);
    $pdf->MultiCell($curX - $hX, $rh, $lastCategory, 1, 'C', 0, 0);
    $pdf->SetXY($curX, $curY);
    $pdf->Ln($rh);
}

/**
 * Volle Detail-Tabelle: je Element eine Zeile, je Parameter eine Wertspalte.
 *
 * @param mysqli_result $result   Elemente im Raum
 * @param mysqli_result $defs     Parameter-Definitionen (Abkürzung/Kategorie)
 * @param mysqli_result $values   Parameter-Werte je Element/Variante
 */
function make_MT_details_table($pdf, $result, $defs, $values, $SB, $SH, $dataChanges): void
{
    // ---- Parameter-Werte einlesen ----
    $elementParamInfos = [];
    while ($row = $values->fetch_assoc()) {
        $elementParamInfos[] = [
            'KategorieID' => $row['idTABELLE_Parameter_Kategorie'],
            'ParamID'     => $row['idTABELLE_Parameter'],
            'elementID'   => $row['tabelle_elemente_idTABELLE_Elemente'],
            'variantenID' => $row['tabelle_Varianten_idtabelle_Varianten'],
            'Wert'        => $row['Wert'],
            'Einheit'     => $row['Einheit'],
        ];
    }

    // ---- Parameter-Definitionen einlesen ----
    $paramInfos = [];
    while ($row = $defs->fetch_assoc()) {
        $paramInfos[$row['idTABELLE_Parameter']] = [
            'ParamID'     => $row['idTABELLE_Parameter'],
            'KategorieID' => $row['idTABELLE_Parameter_Kategorie'],
            'Bezeichnung' => $row['Abkuerzung'],
            'Kategorie'   => $row['Kategorie'],
        ];
    }

    // ---- nur Parameter behalten, die in diesem Raum tatsächlich Werte haben ----
    $roomElementIDs = [];
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        $roomElementIDs[] = $row['TABELLE_Elemente_idTABELLE_Elemente'];
    }

    $roomParamValues = array_filter($elementParamInfos,
        fn($ep) => in_array($ep['elementID'], $roomElementIDs, true));

    $validParamIDs = [];
    foreach ($roomParamValues as $ep) {
        if (trim((string)$ep['Wert']) !== '') {
            $validParamIDs[$ep['ParamID']] = true;
        }
    }
    $paramInfos = array_values(array_filter($paramInfos,
        fn($param) => isset($validParamIDs[$param['ParamID']])));

    // ---- Maße ----
    $cols          = REPORT_MT['detail_cols'];
    $rowHeight     = REPORT_MT['detail_row_h'];
    $rowHeightMain = REPORT_MT['detail_main_h'];
    $f_size        = REPORT_MT['detail_font'];
    $paramCount    = count($paramInfos);
    $text_width    = $paramCount > 0 ? ($SB - array_sum($cols)) / $paramCount : 0;

    $pdf->SetFillColor(...REPORT_STYLE['color_table_head']);
    $pdf->SetTextColor(0, 5, 0);
    mt_draw_detail_header($pdf, $paramInfos, $text_width);

    // ---- Datenzeilen ----
    $is_even_row = 0;
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {

        // Seitenumbruch?
        if ($pdf->GetY() >= $SH - 5) {
            abk_vz($paramInfos, $pdf, $f_size);
            $pdf->AddPage('L', 'A3');
            $pdf->SetFont('courier', 'B', $f_size);
            mt_draw_detail_header($pdf, $paramInfos, $text_width);
            $pdf->Ln(0.2);
        }

        // Zebra-Streifen
        $is_even_row = ($is_even_row + 1) % 2;
        $zebra = ($is_even_row % 2) === 0;
        $pdf->SetFillColor(...($zebra ? REPORT_STYLE['color_zebra'] : REPORT_STYLE['color_white']));

        // fixe Spalten
        $rowHeightMain = REPORT_MT['detail_main_h'];
        $pdf->MultiCell($cols[0], $rowHeightMain, $row['ElementID'], 1, 'C', true, 0);
        $pdf->MultiCell($cols[1], $rowHeightMain, $row['Bezeichnung'], 1, 'C', true, 0);
        $pdf->MultiCell($cols[2], $rowHeightMain, $row['Variante'], 1, 'C', true, 0);
        $pdf->MultiCell($cols[3], $rowHeightMain, $row['SummevonAnzahl'], 1, 'C', true, 0);
        $pdf->MultiCell($cols[4], $rowHeightMain, ($row['Neu/Bestand'] == 1) ? "Nein" : "Ja", 1, 'C', true, 0);
        $pdf->MultiCell($cols[5], $rowHeightMain, $row['Standort'], 1, 'C', true, 0);
        $pdf->MultiCell($cols[6], $rowHeightMain, $row['Verwendung'], 1, 'C', true, 0);

        // Parameter-Werte
        $extra = 0;   // zusätzliche Zellbreite, falls Text zu breit
        foreach ($paramInfos as $array) {
            $outputValue = "";
            foreach ($elementParamInfos as $ep) {
                if ($ep['ParamID'] == $array['ParamID']
                    && $ep['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente']
                    && $ep['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']) {

                    if (checkEntry($dataChanges, $ep['elementID'], $ep['ParamID'])) {
                        $pdf->SetFillColor(...REPORT_STYLE['color_change_bg']);
                    }
                    $outputValue = $ep['Wert'] . checkAndManipulateString($ep['Einheit']);
                    while ($pdf->getStringHeight($text_width + $extra, $outputValue, false, false, '', 1) > $rowHeightMain) {
                        $extra += 1;
                    }
                }
            }

            $pdf->MultiCell($text_width + $extra, $rowHeightMain, $outputValue, 1, 'C', true, 0);
            $pdf->SetFillColor(...($zebra ? REPORT_STYLE['color_zebra'] : REPORT_STYLE['color_white']));
            $extra = ($extra > 0) ? -$extra : 0;
        }
        $pdf->Ln();
    }
    abk_vz($paramInfos, $pdf, $f_size);
    $pdf->Ln();
}


// ----------------------------------------------------------------------------
//  AUFLISTUNG (kompakt)
// ----------------------------------------------------------------------------

/**
 * Kompakte Elementliste. Bei A3-Breite zweispaltig, sonst einspaltig.
 */
function make_MT_list($pdf, $SB, $block_header_w, $rowcounter, $resultX, $style_normal, $style_dashed): void
{
    $a3Width = 420 - 2 * PDF_MARGIN_LEFT;
    $twoUp   = ($rowcounter > 1) && ($SB >= $a3Width - 1);

    $pdf->SetLineStyle($style_dashed);

    // Spaltenbreiten (zweispaltig -> halbe Breite je Tabelle)
    $widthFactor = $twoUp ? 0.5 : 1.0;
    $spaces = [];
    foreach (REPORT_MT['list_props'] as $prop) {
        $spaces[] = ($SB - $block_header_w) * $widthFactor * $prop;
    }
    $fill = 0;
    $pdf->SetFillColor(...REPORT_STYLE['color_table_head']);

    // ---- Kopfzeile ----
    $hh = $pdf->getStringHeight(50, "ID", false, true, '', 1);
    $drawHead = function () use ($pdf, $spaces, $hh, $twoUp) {
        $pdf->MultiCell($spaces[0], $hh, "ID", 'LB', 'C', 0, 0);
        $pdf->MultiCell($spaces[1], $hh, "Var", 'B', 'C', 0, 0);
        $pdf->MultiCell($spaces[2], $hh, "Stk", 'B', 'C', 0, 0);
        $pdf->MultiCell($spaces[3], $hh, "Bestand", 'B', 'C', 0, 0);
        $pdf->MultiCell($spaces[4], $hh, "Element", $twoUp ? 'B' : 'BR', 'L', 0, 0);
    };
    $drawHead();
    if ($twoUp) {
        $drawHead();
    }
    $pdf->Ln();

    // ---- Datenzeilen ----
    $c_even = 0;
    while ($row = $resultX->fetch_assoc()) {
        $pdf->SetFont(REPORT_STYLE['font_family'], '', REPORT_STYLE['font_size_header']);
        $rh = $pdf->getStringHeight($spaces[4], $row['Bezeichnung'], false, true, '', 1) + 1;
        check_4_new_page($pdf, $rh, "A3");

        // Einrückung: einspaltig vor jeder Zeile, zweispaltig nur am Paaranfang
        if (!$twoUp || ($c_even % 2 == 0)) {
            $pdf->MultiCell($block_header_w, $rh, "", "", 'R', "", 0);
        }
        $c_even++;

        $pdf->MultiCell($spaces[0], $rh, $row['ElementID'], 'LT', 'C', $fill, 0);
        $pdf->MultiCell($spaces[1], $rh, $row['Variante'], 'T', 'C', $fill, 0);
        $pdf->MultiCell($spaces[2], $rh, $row['SummevonAnzahl'], 'T', 'C', $fill, 0);
        $pdf->MultiCell($spaces[3], $rh, translateBestand($row['Neu/Bestand']), 'T', 'C', $fill, 0);
        $pdf->MultiCell($spaces[4], $rh, $row['Bezeichnung'], 'RT', 'L', $fill, 0);

        // Zeilenumbruch: einspaltig nach jeder Zeile, zweispaltig nach jedem Paar
        if (!$twoUp || ($c_even % 2 == 0)) {
            $pdf->Ln();
            $fill = !$fill;
        }
    }
    // offenes Paar im Zwei-Spalten-Layout schließen
    if ($twoUp && $c_even % 2 === 1) {
        $pdf->Ln();
    }

    $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_dashed);
    $pdf->SetLineStyle($style_normal);
    $pdf->Ln(2);
    $pdf->Line(15, $pdf->GetY() + 1, $SB + 15, $pdf->GetY() + 1, $style_normal);
}
