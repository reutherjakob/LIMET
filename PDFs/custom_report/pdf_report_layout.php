 <?php
/**
 * ============================================================================
 *  LAYOUT & STYLE  –  ZENTRALE OPTISCHE KONFIGURATION DES CUSTOM-BERICHTS
 * ============================================================================
 *
 *  >>> HIER liegt ALLES Optische an EINER Stelle. <<<
 *  Wenn du Abstände, Schriftgrößen, Spaltenbreiten, Farben oder Proportionen
 *  ändern willst, machst du das ausschließlich in dieser Datei – die Render-
 *  Funktionen (pdf_report_render.php) lesen ihre Maße nur aus dem $L-Array
 *  bzw. den $REPORT_STYLE-/REPORT_*-Konstanten hier.
 *
 *  Aufbau:
 *    1) REPORT_STYLE      – Farben, Linienstile, feste Designwerte
 *    2) report_build_layout($format) – baut das format-abhängige $L-Array
 *                           (Geometrie, Breiten, Zeilenhöhen) für A3 quer / A4 hoch
 *    3) Hilfskonstanten für die Med.-tech.-Tabellen
 *
 *  Das $L-Array wird vom Hauptskript einmal gebaut und an alle Render-
 *  Funktionen durchgereicht. So gibt es keine "magischen Zahlen" mehr verstreut
 *  im Code.
 * ============================================================================
 */

// ----------------------------------------------------------------------------
//  1) GLOBALE DESIGN-KONSTANTEN  (Farben, Linien, feste Maße)
// ----------------------------------------------------------------------------
const REPORT_STYLE = [
    // Farben (RGB)
    'color_line'        => [110, 150, 80],   // Trennlinien / Block-Rahmen (grün)
    'color_balken'      => [200, 210, 200],  // Trennbalken zwischen Räumen
    'color_change_bg'   => [220, 235, 190],  // Hintergrund für markierte Änderungen
    'color_white'       => [255, 255, 255],
    'color_text'        => [0, 0, 0],
    'color_table_head'  => [244, 244, 244],  // Kopfzeilen-Füllung Tabellen
    'color_zebra'       => [240, 240, 235],  // Zebra-Streifen in Tabellen

    // Schrift
    'font_family'       => 'helvetica',
    'font_size_body'    => 6,    // Parameter-Raster
    'font_size_header'  => 10,   // Raumkopf / Standardtext
    'block_header_size' => 10,   // Block-Beschriftung (fett)

    // Zeilenabstände (vertikaler Vorschub in mm)
    'ln'                => 4,
    'ln2'               => 5,
    'ln3'               => 8,

    // Block-Beschriftung
    'block_header_w'    => 25,   // Breite der linken Label-Spalte je Block
    'block_header_h'    => 10,

    // Raster (Anteil der Paarbreite für Label- bzw. Wert-Zelle)
    'grid_label_ratio'  => 0.60,
    'grid_value_ratio'  => 0.34,

    // Seitenränder (oben/unten); links/rechts kommt aus PDF_MARGIN_LEFT
    'margin_top'        => 17,
    'margin_btm'        => 10,
];

// ----------------------------------------------------------------------------
//  Linienstile (TCPDF-Format) – aus REPORT_STYLE abgeleitet
// ----------------------------------------------------------------------------
function report_line_style_normal(): array
{
    return ['width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => REPORT_STYLE['color_line']];
}

function report_line_style_dashed(): array
{
    return ['width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => REPORT_STYLE['color_line']];
}


// ----------------------------------------------------------------------------
//  2) FORMAT-ABHÄNGIGES LAYOUT-ARRAY $L
// ----------------------------------------------------------------------------
/**
 * Baut das komplette Geometrie-/Maß-Array für ein Format.
 *
 *  A3 -> quer  (Breite 420), A4 -> hoch (Breite 210). Höhe ist in beiden Fällen
 *  gleich (297 minus Ränder), damit der Seitenumbruch identisch funktioniert.
 *
 * @param string $format 'A3'|'A4'
 * @return array          Layout-Maße für die Render-Funktionen
 */
function report_build_layout(string $format): array
{
    /** @noinspection PhpUndefinedConstantInspection */
    $margin    = PDF_MARGIN_LEFT;
    $marginTop = REPORT_STYLE['margin_top'];
    $marginBTM = REPORT_STYLE['margin_btm'];

    if ($format === 'A4') {
        $orient = 'P';
        $SB     = 210 - 2 * $margin;   // A4 hoch
    } else {
        $orient = 'L';
        $SB     = 420 - 2 * $margin;   // A3 quer
    }
    $SH = 297 - $marginTop - $marginBTM;

    $eC = $SB / 8;   // "Einheits-Spalte" – Bezugsbreite für cell/str_cell-Faktoren

    return [
        'format'              => $format,
        'orient'              => $orient,
        'margin'              => $margin,
        'marginTop'           => $marginTop,
        'marginBTM'           => $marginBTM,

        'SB'                  => $SB,    // nutzbare Breite
        'SH'                  => $SH,    // nutzbare Höhe

        'e_C'                 => $eC,
        'e_C_3rd'             => $eC / 3,
        'e_C_2_3rd'           => $eC - ($eC / 3),

        'font_size'           => REPORT_STYLE['font_size_body'],
        'block_header_w'      => REPORT_STYLE['block_header_w'],
        'block_header_height' => REPORT_STYLE['block_header_h'],

        'ln'                  => REPORT_STYLE['ln'],
        'ln2'                 => REPORT_STYLE['ln2'],
        'ln3'                 => REPORT_STYLE['ln3'],

        // Wieviele Header-Felder pro Zeile (kann per Projekt-Option überschrieben werden)
        'header_per_row'      => ($format === 'A4') ? 2 : 5,
    ];
}


// ----------------------------------------------------------------------------
//  3) MED.-TECH.-TABELLE – feste Maße (Detail-Tabelle)
// ----------------------------------------------------------------------------
const REPORT_MT = [
    // feste Spaltenbreiten der Detail-Tabelle: ID, Element, Var, Stk, Bestand, Ort, Verw.
    'detail_cols'      => [15, 42, 7, 7, 11, 7, 9],
    'detail_row_h'     => 5,
    'detail_main_h'    => 7,
    'detail_font'      => 6,

    // Auflistung (Liste) – Spaltenproportionen: ID, Var, Stk, Bestand, Element
    'list_props'       => [0.16, 0.07, 0.07, 0.10, 0.60],
];
