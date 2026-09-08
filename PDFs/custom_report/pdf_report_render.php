<?php
/**
 * ============================================================================
 *  RENDERING  –  ALLE DARSTELLUNGSFUNKTIONEN DES CUSTOM-BERICHTS
 * ============================================================================
 *
 *  Hier liegt die komplette VISUELLE Logik. Wer das Aussehen ändern will,
 *  arbeitet hier (Maße/Farben kommen aus pdf_report_layout.php).
 *
 *  Gliederung:
 *    A) NIEDRIGSTUFE  – einzelne Zellen / Symbole (Häkchen, Zahl, Text, Anm.)
 *    B) STRUKTUR      – Block-Label, Trennbalken, Seitenumbruch
 *    C) WERT-RENDERER – ein Parameter -> richtige Zell-Darstellung (render-Typ)
 *    D) KOMPOSITION   – Raumkopf, Parameter-Raster, ganzer Parameter-Block
 *    E) MED.-TECH.    – Orchestrierung des MT-Blocks (Tabelle liegt in
 *                       pdf_createMTTabelle_custom.php)
 *
 *  Abhängigkeiten: $L (Layout-Array), REPORT_STYLE, REPORT_MT,
 *                  generische Helfer (kify, clean_string, ...) aus den Utils.
 * ============================================================================
 */


// ============================================================================
//  A) NIEDRIGSTUFE – EINZELNE ZELLEN / SYMBOLE
// ============================================================================

function text_black_bg_white($pdf): void
{
    $c = REPORT_STYLE;
    $pdf->SetTextColor(...$c['color_text']);
    $pdf->SetFillColor(...$c['color_white']);
}

/**
 * Häkchen (✔) / Kreuz (✘) im A3-Stil (einfarbig schwarz).
 */
function hackerlA3($pdf, $schriftgr, $cellsize, $param, $comp_true): void
{
    $orig = $pdf->getFontSizePt();
    $pdf->SetFont('zapfdingbats', '', 10);
    $isTrue = ($param == $comp_true || $param === "Ja" || $param === "ja" || (is_numeric($param) && $param > 0) || $param === "1");
    $pdf->MultiCell($cellsize, $schriftgr, TCPDF_FONTS::unichr($isTrue ? 52 : 54), 0, 'L', 1, 0);
    $pdf->SetFont(REPORT_STYLE['font_family'], '', $orig);
    $pdf->SetFillColor(...REPORT_STYLE['color_white']);
}

/**
 * Strahlenanwendung: 0/leer -> Kreuz, 1 -> Häkchen, sonst -> "Quasi stationär".
 */
function strahlenanw($pdf, $param, $cellsize, $gr): void
{
    $orig = $pdf->getFontSizePt();
    $pdf->SetFont('zapfdingbats', '', 10);
    if ($param === '0' || empty($param)) {
        $pdf->MultiCell($cellsize, $gr, TCPDF_FONTS::unichr(54), 0, 'L', 1, 0);
    } elseif ($param === '1') {
        $pdf->MultiCell($cellsize, $gr, TCPDF_FONTS::unichr(52), 0, 'L', 1, 0);
    } else {
        $pdf->SetFont(REPORT_STYLE['font_family'], '', 10);
        $pdf->MultiCell($cellsize, $gr, "Quasi stationär", 0, 'L', 1, 0);
    }
    text_black_bg_white($pdf);
    $pdf->SetFont(REPORT_STYLE['font_family'], '', $orig);
}

/**
 * Text-Zelle mit optionaler Änderungs-Hervorhebung (grüner Hintergrund).
 */
function multicell_text_hightlight($pdf, $breite, $font_size, $param_key, $pdf_text, array $changes, $side = "L"): void
{
    if (trim($pdf_text ?? "") === "") {
        $pdf->SetFillColor(...REPORT_STYLE['color_white']);
    } elseif (!empty($changes)) {
        $pdf->SetFillColor(...(in_array($param_key, $changes, true) ? REPORT_STYLE['color_change_bg'] : REPORT_STYLE['color_white']));
    }
    $pdf->MultiCell($breite, $font_size, $pdf_text, 0, $side, true, 0);
}

/**
 * Zahl + Einheit; "-" wenn 0/leer.
 */
function multicell_with_nr($pdf, $NR, $unit, $schriftgr, $einzug, $align = "L"): void
{
    $orig = $pdf->getFontSizePt();
    $pdf->MultiCell($einzug, $schriftgr, ($NR > 0) ? $NR . $unit : "-", 0, $align, 1, 0);
    $pdf->SetFontSize($orig);
    $pdf->SetFillColor(...REPORT_STYLE['color_white']);
}

/**
 * Text + Einheit; "-" wenn null.
 */
function multicell_with_str($pdf, $STR, $einzug, $unit, $schriftgr = 6, $side = 'L'): void
{
    $orig = $pdf->getFontSizePt();
    $pdf->MultiCell($einzug, $schriftgr, ($STR !== null) ? $STR . " " . $unit : "-", 0, $side, 1, 0);
    $pdf->SetFontSize($orig);
    $pdf->SetFillColor(...REPORT_STYLE['color_white']);
}

/**
 * Anmerkungsblock über volle Breite (Label links + Text rechts).
 * $label kommt aus der Parameter-Definition (z. B. "BO-Beschreibung").
 * Fällt auf "Anmerkung:" zurück, wenn keine Beschriftung übergeben wird.
 */
function anmA3($pdf, $inp_text, $SB, $block_header_w, $label = "Anmerkung:"): bool
{
    if (!anm_has_content($inp_text)) {
        return false;
    }
    $outstr = (is_not_no_comment($inp_text) && trim($inp_text) !== "")
        ? format_text(clean_string(br2nl($inp_text)))
        : "Keine Anmerkung";

    $h = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
    $pdf->MultiCell($block_header_w, $h, $label, 0, 'R', 0, 0);
    $pdf->MultiCell($SB - $block_header_w, $h, $outstr, 0, 'L', 0, 1);
    return true;
}

/**
 * Höhe, die ein Anmerkungstext einnehmen würde (für die Umbruchschätzung).
 */
function getAnmHeight($pdf, $inp_text, $SB)
{
    if ($inp_text !== "keine Angaben MT" && $inp_text !== "") {
        $outstr = "Anm.: " . format_text(clean_string(br2nl($inp_text)));
        if (!is_not_no_comment($outstr)) {
            $outstr = "Keine Anmerkung";
        }
        return $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
    }
    return 0;
}


// ============================================================================
//  B) STRUKTUR – BLOCK-LABEL, TRENNBALKEN, SEITENUMBRUCH
// ============================================================================

/**
 * Trennbalken (grauer Streifen) zwischen Räumen/Abschnitten.
 */
function balken($pdf, $horizontalSpacerLN, $SB): void
{
    $pdf->SetFillColor(...REPORT_STYLE['color_balken']);
    $pdf->SetFont(REPORT_STYLE['font_family'], '', 1);
    $pdf->MultiCell($SB, 2, "", "BT", 'L', 1, 1);
    $pdf->Ln($horizontalSpacerLN);
    $pdf->SetFillColor(...REPORT_STYLE['color_white']);
    $pdf->SetFont(REPORT_STYLE['font_family'], '', REPORT_STYLE['font_size_header']);
}

/**
 * Seitenumbruch, wenn der nächste Block nicht mehr auf die Seite passt.
 */
function newpageA3($pdf, $next_block_size, $SH): void
{
    if (($pdf->GetY() + $next_block_size) >= $SH) {
        $pdf->AddPage();
    }
}

/**
 * Block-Beschriftung links + Oberkante-Trennlinie. Macht ggf. vorher einen
 * Seitenumbruch (außer beim Med.-tech.-Block mit Sonderregel).
 */
function block_label_queer($block_header_w, $pdf, $block_label, $upcoming_block_size, $block_height = 12, $SB = 390): void
{
    $isMT = ($block_label === "Med.-tech.");
    $requires_newpage = !$isMT || ($upcoming_block_size < 275 && $pdf->getY() > 130);
    if ($requires_newpage) {
        newpageA3($pdf, $upcoming_block_size, 275);
    }
    $pdf->SetFont(REPORT_STYLE['font_family'], 'B', $block_height);
    $pdf->MultiCell($SB, 1, "", 'T', 'L', 0, 0);   // Trennlinie oben
    $pdf->Ln(1);
    $pdf->MultiCell($block_header_w, $block_height, $block_label, 0, 'L', 0, 0);
    $pdf->SetFont(REPORT_STYLE['font_family'], '', REPORT_STYLE['font_size_header']);
}


// ============================================================================
//  C) WERT-RENDERER – EIN PARAMETER -> RICHTIGE DARSTELLUNG
// ============================================================================

/**
 * Gibt den Wert EINES Parameters in die Wert-Zelle aus (gem. render-Typ).
 * Neue render-Typen werden hier ergänzt – dann sind sie überall verfügbar.
 */
function cb_render_value($pdf, array $p, array $row, float $valW, float $font_size): void
{
    $val = $row[$p['key']] ?? '';
    $unit = $p['unit'] ?? '';

    switch ($p['render']) {
        case 'nr':
            multicell_with_nr($pdf, $val, $unit, $font_size, $valW);
            break;
        case 'kify':
            $out = ($val !== '' && $val !== null && $val != "0") ? kify($val) . $unit : "-";
            multicell_with_str($pdf, $out, $valW, "");
            break;
        case 'kify_ka':
            $isEmpty = ($val === '' || $val === null || $val === "0" || $val == 0 || $val === "-");
            multicell_with_str($pdf, $isEmpty ? "keine Angabe" : kify($val) . $unit, $valW, "");
            break;
        case 'hackerl':
            hackerlA3($pdf, $font_size, $valW, $val, $p['true_val'] ?? 'JA');
            break;
        case 'strahlen':
            strahlenanw($pdf, $val, $valW, $font_size);
            break;
        case 'janein':
            multicell_with_str($pdf, translate_1_to_yes($val), $valW, "");
            break;
        case 'str':
        default:
            multicell_with_str($pdf, ($val === '' || $val === null) ? " - " : $val, $valW, $unit);
            break;
    }
}

/**
 * Näherung des dargestellten Wert-Textes – NUR zur Höhenmessung.
 * Symbol-Renderer (Häkchen/Strahlung) gelten als einzeilig ('').
 */
function cb_value_text(array $p, array $row): string
{
    $val = $row[$p['key']] ?? '';
    $unit = $p['unit'] ?? '';
    switch ($p['render']) {
        case 'hackerl':
        case 'strahlen':
            return '';
        case 'janein':
            return 'Nein';
        case 'nr':
        case 'kify':
        case 'kify_ka':
            return trim((string)$val . $unit);
        case 'str':
        default:
            return trim((string)$val . ' ' . $unit);
    }
}


// ============================================================================
//  D) KOMPOSITION – RAUMKOPF, RASTER, GANZER BLOCK
// ============================================================================

/**
 * Ersetzt {Spalte}-Platzhalter durch Werte aus der Raumzeile.
 */
function cb_fill_template(string $tpl, array $row): string
{
    return preg_replace_callback('/\{([^}]+)\}/', function ($m) use ($row) {
        $k = trim($m[1]);
        return array_key_exists($k, $row) ? (string)$row[$k] : '';
    }, $tpl);
}

/**
 * Zeichnet den konfigurierbaren Raum-Header (Felder/Labels aus Projekt-Config).
 */
function cb_render_room_header($pdf, array $row, array $headerFields, int $perRow, array $L): void
{
    // A3: ggf. neue Seite, wenn weit unten (A4 startet je Raum ohnehin frisch)
    if ($L['format'] !== 'A4' && $pdf->GetY() >= 180) {
        $pdf->AddPage();
    }
    // Trennbalken oben (außer ganz am Seitenanfang)
    if ($pdf->GetY() >= 15) {
        balken($pdf, 1, $L['SB']);
    } else {
        $pdf->Ln(1);
    }

    $pdf->SetFont(REPORT_STYLE['font_family'], 'B', REPORT_STYLE['font_size_header']);
    $lineH = $L['ln2'];
    $cellW = $L['SB'] / max(1, $perRow);

    $texts = [];
    foreach ($headerFields as $f) {
        $val = cb_fill_template($f['value'] ?? '', $row);
        $label = trim((string)($f['label'] ?? ''));
        $texts[] = ($label !== '') ? ($label . ': ' . $val) : $val;
    }

    foreach (array_chunk($texts, $perRow) as $chunk) {
        $maxLines = 1;
        foreach ($chunk as $t) {
            $maxLines = max($maxLines, $pdf->getNumLines($t, $cellW));
        }
        foreach ($chunk as $t) {
            $pdf->MultiCell($cellW, $lineH, $t, 0, 'L', 0, 0);
        }
        $pdf->Ln(max($lineH, $maxLines * $lineH));
    }

    $pdf->SetFont(REPORT_STYLE['font_family'], '', REPORT_STYLE['font_size_header']);
    $pdf->Ln(2);
}

/**
 * Breite EINER Grundspalte (span = 1): nutzbare Breite / perRow.
 */
function cb_col_width(int $perRow, array $L): float
{
    return ($L['SB'] - $L['block_header_w']) / max(1, $perRow);
}

/**
 * Wie viele Grundspalten belegt ein Parameter? ('span', Default 1, max perRow)
 * So bekommt ein langer Wert (z. B. "Hygieneklasse: Richtlinie MA LL 14") mehr
 * Platz, während kurze Werte ("AV: x") kompakt bleiben.
 */
function cb_param_span(array $p, int $perRow): int
{
    return max(1, min($perRow, (int)($p['span'] ?? 1)));
}

/**
 * Label-/Wert-Breite EINES Parameters.
 *   - 'span' vergrößert die WERT-Zelle (das Label bleibt für saubere Spalten
 *     gleich breit); span=1 verhält sich exakt wie bisher.
 *   - 'cell'/'str_cell' (in e_C) sind absolute Overrides und schlagen 'span'.
 */
function cb_param_widths(array $p, float $colW, int $perRow, array $L): array
{
    $span = cb_param_span($p, $perRow);
    $lblW = isset($p['cell'])
        ? $p['cell'] * $L['e_C']
        : $colW * REPORT_STYLE['grid_label_ratio'];
    $valW = isset($p['str_cell'])
        ? $p['str_cell'] * $L['e_C']
        : $colW * REPORT_STYLE['grid_value_ratio'] + ($span - 1) * $colW;
    return [$lblW, $valW];
}

/**
 * Teilt die Parameter in Zeilen auf (Fluss-Layout): summiert die span-Werte und
 * bricht um, sobald perRow überschritten würde. Ersetzt das starre array_chunk.
 *
 * Manueller Umbruch: ein Parameter mit 'ln_after' => true beendet die Zeile
 * direkt nach sich (wie im A3queer-Bericht). So lässt sich das Raster exakt
 * steuern – unabhängig von perRow/span.
 */
function cb_flow_rows(array $params, int $perRow): array
{
    $rows = [];
    $cur = [];
    $used = 0;
    foreach ($params as $p) {
        $span = cb_param_span($p, $perRow);
        // Automatischer Umbruch, wenn die Zeile sonst zu voll würde
        if (!empty($cur) && $used + $span > $perRow) {
            $rows[] = $cur;
            $cur = [];
            $used = 0;
        }
        $cur[] = $p;
        $used += $span;
        // Manueller Umbruch nach diesem Parameter
        if (!empty($p['ln_after'])) {
            $rows[] = $cur;
            $cur = [];
            $used = 0;
        }
    }
    if (!empty($cur)) {
        $rows[] = $cur;
    }
    return $rows;
}

/**
 * Benötigte Zeilenanzahl einer Rasterzeile (Label UND Wert je Zelle).
 */
function cb_row_lines($pdf, array $rowParams, array $row, float $colW, int $perRow, array $L): int
{
    $maxLines = 1;
    foreach ($rowParams as $p) {
        [$thisLbl, $thisVal] = cb_param_widths($p, $colW, $perRow, $L);
        $valText = cb_value_text($p, $row);
        $maxLines = max(
            $maxLines,
            $pdf->getNumLines((string)$p['label'], $thisLbl),
            ($valText === '') ? 1 : $pdf->getNumLines($valText, $thisVal)
        );
    }
    return $maxLines;
}

/**
 * Rendert die Inline-Parameter eines Blocks als Fluss-Raster.
 * Breite je Parameter über 'span' (bzw. 'cell'/'str_cell') steuerbar.
 */
function cb_render_inline_grid($pdf, array $params, array $row, array $changes, int $perRow, array $L): void
{
    if (empty($params)) {
        return;
    }
    $colW = cb_col_width($perRow, $L);
    $lineH = $L['font_size'];

    foreach (cb_flow_rows($params, $perRow) as $ri => $rowParams) {
        if ($ri > 0) {
            $pdf->MultiCell($L['block_header_w'], $lineH, "", 0, 'L', 0, 0);
        }
        $maxLines = cb_row_lines($pdf, $rowParams, $row, $colW, $perRow, $L);

        foreach ($rowParams as $p) {
            [$thisLbl, $thisVal] = cb_param_widths($p, $colW, $perRow, $L);
            multicell_text_hightlight($pdf, $thisLbl, $L['font_size'], $p['key'], $p['label'], $changes);
            cb_render_value($pdf, $p, $row, $thisVal, $L['font_size']);
        }
        // Vorschub an höchste Zelle anpassen
        $pdf->Ln(max($L['ln2'], $maxLines * $lineH));
    }
}

/**
 * Schätzt die Blockhöhe (für den Seitenumbruch in block_label_queer).
 */
function cb_estimate_block_height($pdf, array $inline, array $anms, array $row, int $perRow, array $L): float
{
    $colW = cb_col_width($perRow, $L);
    $lineH = $L['font_size'];

    $h = $L['block_header_height'];
    foreach (cb_flow_rows($inline, $perRow) as $rowParams) {
        $h += max($L['ln2'], cb_row_lines($pdf, $rowParams, $row, $colW, $perRow, $L) * $lineH);
    }
    foreach ($anms as $a) {
        $h += $L['ln'] + (float)getAnmHeight($pdf, $row[$a['key']] ?? '', $L['SB']);
    }
    return $h + $L['ln'];
}

/**
 * Rendert EINEN kompletten Parameter-Block (Label + Raster + Anmerkungen).
 * Kapselt die früher im Hauptskript verstreute Block-Logik.
 *
 * @param array $group Gruppe aus den paramDefinitions (label, perRow, params)
 */
function cb_render_param_block($pdf, array $group, array $selectedKeys, array $row, array $changes, array $L): void
{
    $selectedInGroup = array_values(array_filter(
        $group['params'],
        fn($p) => in_array($p['key'], $selectedKeys, true)
    ));
    if (empty($selectedInGroup)) {
        return;
    }

    // Inline-Parameter / Spezialblöcke (z. B. Leistung inkl. GLZ) / Anmerkungen trennen
    $inline = [];
    $specials = [];
    $anms = [];
    foreach ($selectedInGroup as $p) {
        switch ($p['render']) {
            case 'anm':
                $anms[] = $p;
                break;
            case 'power_glz':
                $specials[] = $p;
                break;
            default:
                $inline[] = $p;
                break;
        }
    }// Leere Anmerkungen ("Keine Anmerkung") gar nicht erst mitnehmen
    $anms = array_values(array_filter(
        $anms,
        fn($a) => anm_has_content($row[$a['key']] ?? null)
    ));

    // Nichts Darstellbares übrig -> ganzen Block (Label + Trennlinie) weglassen
    if (empty($inline) && empty($specials) && empty($anms)) {
        return;
    }

    $perRow = $group['perRow'] ?? 3;
    // Höhe der Spezialblöcke grob mitschätzen (je eine Datenzeile)
    $blockEst = cb_estimate_block_height($pdf, $inline, $anms, $row, $perRow, $L)
        + count($specials) * ($L['font_size'] * 2 + $L['ln']);

    block_label_queer($L['block_header_w'], $pdf, $group['label'], $blockEst, $L['block_header_height'], $L['SB']);
    cb_render_inline_grid($pdf, $inline, $row, $changes, $perRow, $L);

    // Spezialblöcke (Leistung inkl. GLZ). Die Werte hat das Hauptskript bereits
    // per report_fetch_room_power_glz() ermittelt und unter dem Param-Key in
    // $row injiziert – diese Funktion macht reine Darstellung.
    foreach ($specials as $p) {
        if ($p['render'] === 'power_glz') {
            cb_render_power_glz_block($pdf, (array)($row[$p['key']] ?? []), $L, $changes);
            $pdf->Ln($L['ln2']);
        }
    }

    // Stand vor der Anmerkung nichts (kein Inline-Parameter UND kein Spezialblock),
    // dient bereits der Block-Header als linke Beschriftung. Das Label der ERSTEN
    // Anmerkung wird dann unterdrückt, sonst überlappt es mit dem Block-Header.
    $nothingBefore = empty($inline) && empty($specials);
    if ($nothingBefore && !empty($anms)) {
        $pdf->Ln(1);
    }
    foreach ($anms as $i => $a) {
        $label = $a['label'] ?? 'Anmerkung:';
        if ($i === 0 && $nothingBefore) {
            $label = '';   // Block-Header ist bereits die Beschriftung
        }
        anmA3($pdf, $row[$a['key']] ?? '', $L['SB'], $L['block_header_w'], $label);
        //   $pdf->Ln($L['ln2']);
    }
}


// ============================================================================
//  D2) RAUMLEISTUNG INKL. GLEICHZEITIGKEIT (GLZ)
//  Reine Darstellung. Die Werte stammen aus report_fetch_room_power_glz()
//  (Daten-Layer) und werden vom Hauptskript unter dem Param-Key in $row
//  injiziert. Ausgabe als eine horizontale Zeile: AV / SV / ZSV / USV / Ohne NA.
// ============================================================================
/**
 * @param array $leistungen ['AV'=>W,'SV'=>W,'ZSV'=>W,'USV'=>W,'Unbekannt'=>W]
 */
function cb_render_power_glz_block($pdf, array $leistungen, array $L, array $changes): void
{
    $fs = $L['font_size'];
    $eC = $L['e_C'];
    $e3 = $L['e_C_3rd'];
    $e23 = $L['e_C_2_3rd'];
    $val = fn($k) => round((float)($leistungen[$k] ?? 0), 0);

    multicell_text_hightlight($pdf, $eC * 2 + 10, $fs, 'MT Leistung AV',
        "Leistungen nach Netzart inkl. Gleichzeitigkeit -  AV: ", $changes);
    multicell_with_nr($pdf, $val('AV'), "W", $fs, $e3 + 10);

    multicell_text_hightlight($pdf, $e23, $fs, ' SV', "SV: ", $changes);
    multicell_with_nr($pdf, $val('SV'), "W", $fs, $e3 + 10);

    multicell_text_hightlight($pdf, $e23, $fs, 'MT Leistung ZSV', "ZSV: ", $changes);
    multicell_with_nr($pdf, $val('ZSV'), "W", $fs, $e3 + 10);

    multicell_text_hightlight($pdf, $e23, $fs, 'MT Leistung USV', "USV: ", $changes);
    multicell_with_nr($pdf, $val('USV'), "W", $fs, $e3 + 10);

    multicell_text_hightlight($pdf, $e23, $fs, 'MT Leistung ohne Zuordnung', "Ohne NA: ", $changes);
    multicell_with_nr($pdf, $val('Unbekannt'), "W", $fs, $e3 + 10);
}


// ============================================================================
//  E) MED.-TECH.-BLOCK (Orchestrierung)
//  Die eigentlichen Tabellen (Detail/Liste) liegen in
//  pdf_createMTTabelle_custom.php. Datenbeschaffung in pdf_report_data.php.
//  Rückgabe: resultierende Seitenausrichtung ('P'|'L').
// ============================================================================
function cb_render_mt_block($pdf, $mysqli, int $roomID, int $projectID, ?string $sinceDate,
                            array $L, string $mt_mode, string $format, string $curOrient): string
{
    $block_header_w = $L['block_header_w'];
    $block_header_h = $L['block_header_height'];
    $style_normal = report_line_style_normal();
    $style_dashed = report_line_style_dashed();

    // Elemente im Raum laden (Daten-Layer)
    $mt = report_fetch_mt_elements($mysqli, $roomID);
    $result = $mt['result'];
    $count = $mt['count'];

    // ---- DETAILS: Elemente + Parameter-Tabelle ----
    if ($mt_mode === 'details' && $count > 0) {
        // A4: breite Tabelle auf eigene Querformat-Seite
        if ($format === 'A4') {
            $pdf->AddPage('L', 'A4');
            $curOrient = 'L';
            $SB_mt = 297 - 2 * $L['margin'];
            $SH_mt = 210 - $L['marginTop'] - $L['marginBTM'];
        } else {
            $SB_mt = $L['SB'];
            $SH_mt = $L['SH'];
        }

        $pd = report_fetch_mt_param_data($mysqli, $projectID, $sinceDate);

        block_label_queer($block_header_w, $pdf, "Med.-tech.", 10 + $count * 5, $block_header_h, $SB_mt);
        make_MT_details_table($pdf, $result, $pd['defs'], $pd['values'], $SB_mt, $SH_mt, $pd['changes']);

        return $curOrient;
    }

    // ---- LIST: nur Auflistung der Elemente ----
    if ($count > 0) {
        $SB_use = $L['SB'];
        block_label_queer($block_header_w, $pdf, "Med.-tech.", 10 + $count / 2 * 5, $block_header_h, $SB_use);
        $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB_use + 15, $pdf->GetY(), $style_dashed);
        make_MT_list($pdf, $SB_use, $block_header_w, $count, $result, $style_normal, $style_dashed);
        return $curOrient;
    }

    // ---- keine Ausstattung ----
    $SB_use = $L['SB'];
    $pdf->Line(15, $pdf->GetY(), $SB_use + 15, $pdf->GetY(), $style_normal);
    block_label_queer($block_header_w, $pdf, "Med.-tech.", 15, $block_header_h, $SB_use);
    $pdf->Multicell(0, 0, "Keine medizin-/labortechnische Ausstattung.", "", "L", 0, 0);
    $pdf->Ln();

    return $curOrient;
}