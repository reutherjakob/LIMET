<?php
/**
 * ============================================================================
 *  GENERISCHE HELFER  –  NICHT-VISUELLE UTILITIES FÜR DEN CUSTOM-BERICHT
 * ============================================================================
 *
 *  Hier liegen nur allgemeine, layout-unabhängige Helfer:
 *    - Zahlen-/Textformatierung (kify, clean_string, format_text)
 *    - kleine Übersetzer (Ja/Nein, Bestand)
 *    - Datei-/Datums-Helfer
 *    - PDF-Grundinitialisierung (Header/Footer/Margins)
 *
 *  VISUELLE Render-Funktionen liegen in pdf_report_render.php,
 *  DATEN-Funktionen in pdf_report_data.php.
 *
 *  Hinweis: br2nl() und filter_old_equal_new() kommen aus den gemeinsamen
 *  Bericht-Utils bzw. constDefinitions und werden hier vorausgesetzt.
 * ============================================================================
 */


// ----------------------------------------------------------------------------
//  ZAHL- / TEXTFORMATIERUNG
// ----------------------------------------------------------------------------

/**
 * "k"-Formatierung: >=1000 -> Tausender mit " k", sonst Zahl mit Dezimalkomma.
 */
function kify($input)
{
    if (!is_numeric($input)) {
        return $input;
    }
    if ($input >= 1000) {
        $val = number_format(round($input / 1000, 2), 2, ',', '');
        return rtrim(rtrim($val, '0'), ',') . ' k';
    }
    $val = number_format($input, 2, ',', '');
    return rtrim(rtrim($val, '0'), ',') . " ";
}

/**
 * Erlaubt nur definierte Zeichen (Umlaute, °, Zeilenumbruch, ASCII).
 */
function clean_string($dirty_str)
{
    return preg_replace('/[^äüößÄÜÖ°\n(\x20-\x7F)]*/u', '', $dirty_str);
}

/**
 * Entfernt Leerzeichen vor Zeilenumbrüchen.
 */
function format_text($string)
{
    return preg_replace("/\s+\n/", "\n", $string);
}

/**
 * Prüft, ob ein Text eine echte Anmerkung ist (kein Leer-/Platzhaltertext).
 */
function is_not_no_comment($str): bool
{
    return !($str === "keine Anmerkung" || $str === "keine Angaben" || $str === "");
}

/**
 * True, wenn eine Anmerkung echten Inhalt hat (also NICHT als
 * "Keine Anmerkung"-Platzhalter dargestellt würde).
 */
function anm_has_content($text): bool
{
    return $text !== null
        && is_not_no_comment($text)
        && trim((string)$text) !== "";
}
// ----------------------------------------------------------------------------
//  KLEINE ÜBERSETZER
// ----------------------------------------------------------------------------

function translateBestand($value): string
{
    return ($value == 0) ? 'Ja' : 'Nein';
}

function translate_1_to_yes($value): string
{
    return ($value == 1) ? 'Ja' : 'Nein';
}


// ----------------------------------------------------------------------------
//  DATEI / DATUM
// ----------------------------------------------------------------------------

function getFileName($topic): string
{
    $date        = $_SESSION["PDFdatum"] ?? date('Y-m-d');
    $projectname = trim($_SESSION['projectName'] ?? '');
    return $projectname . "_GPMT_" . $topic . "_" . $date . ".pdf";
}

function getValidatedDateFromURL(): ?string
{
    return isset($_GET['date'])
        ? filter_var($_GET['date'], FILTER_SANITIZE_SPECIAL_CHARS)
        : null;
}


// ----------------------------------------------------------------------------
//  SEITENUMBRUCH-HELFER (generisch, ohne Layout-Abhängigkeit)
// ----------------------------------------------------------------------------

/**
 * Bricht um, wenn die aktuelle Y-Position + Höhe das Seitenende erreicht.
 */
function check_4_new_page($pdf, $height, $format = ""): bool
{
    $pagelength = ($format === "A3") ? 290 : 270;
    if (($pdf->GetY() + $height) >= $pagelength) {
        $pdf->AddPage();
        return true;
    }
    return false;
}


// ----------------------------------------------------------------------------
//  PDF-GRUNDINITIALISIERUNG (Metadaten, Header/Footer, Margins, erste Seite)
// ----------------------------------------------------------------------------
function init_pdf_attributes($pdf, $einzugLR, $marginTop, $marginBTM, $format = "", $label = "")
{
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
    $pdf->SetTitle($label);
    $pdf->SetSubject($label);
    $pdf->SetKeywords($label);
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins($einzugLR, $marginTop, $einzugLR);
    $pdf->SetHeaderMargin($marginTop);
    $pdf->SetFooterMargin($marginBTM);
    $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->SetFont('helvetica', '', 10);

    if ($format === "A3") {
        $pdf->AddPage('L', 'A3');
    } elseif ($format === "A4") {
        $pdf->AddPage('P', 'A4');
    } elseif ($format === "A4_queer") {
        $pdf->AddPage('L', 'A4');
    }
    return $pdf;
}
