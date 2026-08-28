<?php
/**
 * pdf_createRaumtypenKatalog.php
 * ---------------------------------------------------------------------------
 * Raumtypenblätter als PDF (TCPDF).
 *
 *  - Titelblatt ("Raumtypenblätter" + Projektname + Stand: Datum)
 *  - EINE A4-Seite je Raumtyp
 *  - Kopfzeile mit Lime-Logo, Vorgabe-Fußzeile (Datum + Seite X von Y)
 *  - Parameter thematisch gegliedert (Architektur · Klima/Lüftung/HLK ·
 *    Elektrotechnik · Medien · Abzüge/Absaugung · Sicherheit)
 *  - Rendert DIREKT aus $labortypen (Nutzerumfrage/raumtypen.php),
 *    d. h. Änderungen an den Raumtypen erscheinen automatisch im PDF.
 *
 *  Aufruf:  .../pdf_createRaumtypenKatalog.php          (Inline)
 *           .../pdf_createRaumtypenKatalog.php?dl=1      (Download)
 * ---------------------------------------------------------------------------
 */

/* ═══════════════════════════════════════════════════════════════════════════
   KONFIG  –  bei Bedarf an eure Ordnerstruktur anpassen
   ═══════════════════════════════════════════════════════════════════════════ */
$RK_TCPDF_PATH = __DIR__ . '/../TCPDF-main/TCPDF-main/tcpdf.php';
$RK_UTILS_PATH = __DIR__ . '/../utils/_utils.php';
$RK_LOGO_PATH = __DIR__ . '/pdf_createBericht_LOGO.php';   // definiert get_header_logo()/get_titelblatt_logo()
$RK_LOGO_IMG = '';   // optional: Pfad zu einer Logo-Grafik (png/jpg/svg); hat Vorrang
$RK_DATA_CANDIDATES = [
    __DIR__ . '/../Nutzerumfrage/raumtypen.php',
    __DIR__ . '/Nutzerumfrage/raumtypen.php',
    __DIR__ . '/raumtypen.php',
];

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

/* ── Login / Utils (wie in den anderen pdf_*-Skripten, aber optional) ─────── */
if (is_file($RK_UTILS_PATH)) {
    require_once $RK_UTILS_PATH;
    if (function_exists('check_login')) {
        check_login();
    }
}

/* ── TCPDF + Logo-Funktionen laden ────────────────────────────────────────── */
if (!is_file($RK_TCPDF_PATH)) {
    die('Fehler: TCPDF nicht gefunden. Bitte $RK_TCPDF_PATH anpassen.');
}
require_once $RK_TCPDF_PATH;
if (is_file($RK_LOGO_PATH)) {
    include_once $RK_LOGO_PATH;
}

/* ── Datenquelle laden: $labortypen ───────────────────────────────────────── */
if (!isset($labortypen) || !is_array($labortypen)) {
    foreach ($RK_DATA_CANDIDATES as $cand) {
        if (is_file($cand)) {
            require_once $cand;
            break;
        }
    }
}
if (!isset($labortypen) || !is_array($labortypen) || count($labortypen) === 0) {
    die('Fehler: $labortypen nicht gefunden. Bitte Pfad in $RK_DATA_CANDIDATES anpassen.');
}

/* ── Projekt / Datum ──────────────────────────────────────────────────────── */
$RK_PROJEKT = (isset($_SESSION['projectName']) && $_SESSION['projectName'] !== '')
    ? $_SESSION['projectName'] : 'Projekt';
$RK_DATUM = date('d.m.Y');

/* ═══════════════════════════════════════════════════════════════════════════
   FARB-PALETTE (Lime / Grün – aus den bestehenden Berichten)
   ═══════════════════════════════════════════════════════════════════════════ */
$RK = [
    'primary' => [110, 150, 80],
    'badge' => [74, 103, 53],
    'labelbg' => [220, 235, 190],
    'blockbg' => [198, 220, 168],   // Themenblock-Überschrift
    'zebra' => [240, 244, 233],
    'white' => [255, 255, 255],
    'border' => [205, 215, 190],
    'valtext' => [15, 42, 7],
    'labeltext' => [60, 82, 40],
    'muted' => [90, 110, 70],
    'title' => [255, 255, 255],
];

/* ═══════════════════════════════════════════════════════════════════════════
   FELD-TYPEN  ·  'B' = Ja/Nein · 'T' = Text · 'N' = Zahl
   ═══════════════════════════════════════════════════════════════════════════ */
const RK_B = 'B';
const RK_T = 'T';
const RK_N = 'N';
const RK_C = 'C';   // kombiniert / berechnet (Callback)

/* Parameter in Themenblöcke gegliedert, verteilt auf linke / rechte Spalte. */
$RK_BLOCKS_LEFT = [
    ['Allgemeines / Architektur', [
        ['achsen', 'Achsen', RK_T],
        [fn($rec) => rk_range($rec, 'flaeche_min', 'flaeche_max'), 'Fläche (min–max) [m2]', RK_C],
        [fn($rec) => rk_range($rec, 'raumhoehe_bestand', 'raumhoehe_neubau'), 'Raumhöhe (Bestand - Neubau) [m]', RK_C],
        // ['raumhoehe_neubau', 'Raumhöhe Neubau [m]', RK_N],
        // ['raumhoehe_bestand', 'Raumhöhe Bestand [m]', RK_N],
        ['decke', 'Decke', RK_T],
        ['tuere_min', 'Türenbreite [m]', RK_N],
        ['akustik', 'erhöhte Akustik Anforderung', RK_B],
        ['tagelicht_notwendig', 'Tageslicht notwendig', RK_B],
        // ['tagelicht_nicht_notwendig','Tageslicht nicht notw.',     RK_B],
        ['blendschutz', 'Blendschutz', RK_B],
        ['verdunkelung', 'Verdunkelung', RK_B],
    ]],
    ['Klima, Lüftung & HLK', [
        ['waermeabgabe', 'Wärmeabgabe [W/m2]', RK_N],
        ['heizung', 'Heizung & Kühlung', RK_B],
        //  ['kuehlung',              'Kühlung',                       RK_B],
        [fn($rec) => rk_temp($rec), 'Temperatur (min–max) [°C]', RK_C],
        ['temp_schwankung', 'Temp.-Schwankungstoleranz [°C]', RK_T],
        ['luftfeuchtigkeit', 'Luftfeuchtigkeit [%]', RK_T],
        ['luftfeuchtigkeit_schwankungstoleranz', 'LF-Toleranz [%]', RK_T],
        ['luftwechsel', 'Luftwechsel', RK_T],
        //  ['luftwechsel_rate_m3_je_m2h','LW-Rate (m3/m2h)',          RK_N],
        //  ['luftwechsel_norm',      'Luftwechsel-Norm',              RK_T],
        ['luftwechsel_filter', 'Zu-/Abluftfilter', RK_T],
        ['druckregelung', 'Druckregelung/Schleuse', RK_T],
        //['druckregelung_typ',     'Druck-Typ',                     RK_T],
        // ['druckregelung_schleuse','Schleuse',                      RK_B],
        ['sonderabluft', 'Sonderabluft', RK_T],
    ]],

    ['Medien', [
        ['kaltwasser', 'Kalt- & Warmwasser', RK_B],
        //   ['warmwasser', 'Warmwasser', RK_B],
        ['ve_wasser', 'VE-Wasser', RK_B],
        ['n2', 'Stickstoff (N2)', RK_B],
        ['dl', 'Druckluft (DL)', RK_B],
        ['sondergase', 'Sondergase', RK_T],
    ]],

    ['Elektrotechnik', [
        ['elektro_230v', '230 V', RK_B],
        ['elektro_400v_cee', '400 V CEE', RK_B],
        ['elektro_edv', 'EDV', RK_B],
        [fn($rec) => rk_notstrom($rec), 'Notstrom', RK_C],
        ['anschlussleistung', 'Anschlussleistung [W/m2]', RK_N],
        ['AV_quotient', 'AV/SV-Quotient', RK_N],
    ]],

];

$RK_BLOCKS_RIGHT = [

    ['Abzüge & Absaugung', [
        ['abzuege', 'Abzüge', RK_T],
        ['abzuege_anzahl_min', 'Abzüge Anzahl min.', RK_N],
        ['abzuege_anzahl_max', 'Abzüge Anzahl max.', RK_N],
        ['abzuege_notstrom', 'Abzüge notstromversorgt', RK_B],
        ['abzuege_unterflurabfall', 'Unterflurabfallsystem', RK_B],
        ['abzuege_abluftwaescher', 'Abluftwäscher', RK_B],
        ['abzuege_sicherheitswerkbank_klasse', 'Sicherheitswerkbank', RK_T],
        ['punktabsaugungen', 'Punktabsaugungen', RK_T],
        // ['punktabsaugungen_min',  'Punktabsaug. min.',             RK_N],
        //['punktabsaugungen_max',  'Punktabsaug. max.',             RK_N],
    ]],
    ['Sicherheit & Ausstattung', [
        ['labormoebel', 'Labormöbel', RK_T],
        ['sicherheitsschraenke', 'Sicherheitsschränke', RK_T],
        ['sicherheitsschrank_saeure_lauge', 'Schrank Säure/Lauge', RK_B],
        ['sicherheitsschrank_brennbar', 'Schrank brennbar', RK_B],
        ['sicherheitsausstattung', 'Sicherheitsausstattung', RK_T],
        ['sicherheit_notdusche', 'Notdusche', RK_B],
        ['sicherheit_augendusche', 'Augendusche', RK_B],
        ['sicherheit_notruf', 'Notruf', RK_B],
        ['sicherheit_erstehilfe', 'Erste Hilfe', RK_B],
        [fn($rec) => rk_bsl($rec), 'Bio Safety Level', RK_C],
        ['sonstige_anforderungen', 'Sonstige Anforderungen', RK_T],
        ['anmerkungen', 'Anmerkungen', RK_T],

    ]],
];

/* ═══════════════════════════════════════════════════════════════════════════
   WERT-FORMATIERUNG
   ═══════════════════════════════════════════════════════════════════════════ */
function rk_fmt($val, string $type): string
{
    if ($val === null) return '–';
    $s = trim((string)$val);
    if ($s === '') return '–';
    if ($type === RK_B) {
        $l = strtolower($s);
        if ($s === '1' || $l === 'true' || $l === 'ja') return 'Ja';
        if ($s === '0' || $l === 'false' || $l === 'nein') return 'Nein';
        if ($l === 'nach_erfordernis') return 'nach Erfordernis';
        return $s;
    }
    if ($type === RK_T) {
        return ($s === '0') ? '–' : $s;
    }
    return $s;
}

function rk_val($rec, string $key): ?string
{
    if (!isset($rec[$key])) return null;
    $s = trim((string)$rec[$key]);
    return ($s === '') ? null : $s;
}

function rk_range($rec, string $minKey, string $maxKey): string
{
    $min = rk_val($rec, $minKey);
    $max = rk_val($rec, $maxKey);
    if ($min !== null && $max !== null) return ($min === $max) ? $min : ($min . '–' . $max);
    if ($min !== null) return $min;
    if ($max !== null) return $max;
    return '–';
}

function rk_temp($rec): string
{
    if (rk_val($rec, 'temp_min') !== null || rk_val($rec, 'temp_max') !== null) {
        return rk_range($rec, 'temp_min', 'temp_max');
    }
    $ne = strtolower((string)($rec['temp_nach_erfordernis'] ?? ''));
    if (in_array($ne, ['1', 'ja', 'true', 'nach_erfordernis'], true)) return 'nach Erfordernis';
    return '–';
}/* Notstrom: Nein | Ja, ohne USV | Ja, mit USV  (USV hat Vorrang) */
function rk_notstrom($rec): string
{
    $usv = strtolower(trim((string)($rec['elektro_notstrom_usv'] ?? '')));
    $ohne = strtolower(trim((string)($rec['elektro_notstrom'] ?? '')));
    $yes = ['1', 'ja', 'true'];
    if (in_array($usv, $yes, true)) return 'Ja, mit USV';
    if (in_array($ohne, $yes, true)) return 'Ja, ohne USV';
    return 'Nein';
}

function rk_bsl($rec): string
{
    $levels = [];
    foreach (['1', '2', '3'] as $lvl) {
        $v = strtolower(trim((string)($rec['bsl' . $lvl] ?? '')));
        if (in_array($v, ['1', 'ja', 'true'], true)) {
            $levels[] = $lvl;
        }
    }
    return $levels ? implode('/', $levels) : '–';
}

/* ═══════════════════════════════════════════════════════════════════════════
   LOGO (Bilddatei → Projekt-Logofunktion → Lime-Wortmarke als Fallback)
   ═══════════════════════════════════════════════════════════════════════════ */
function rk_logo(TCPDF $pdf, float $x, float $y, float $sq, float $fontpt, array $P, bool $titel = false): void
{
    global $RK_LOGO_IMG;
    if ($RK_LOGO_IMG !== '' && is_file($RK_LOGO_IMG)) {
        $pdf->Image($RK_LOGO_IMG, $x, $y, 0, $sq, '', '', 'T', true, 300, 'L');
        return;
    }
    if ($titel && function_exists('get_titelblatt_logo')) {
        get_titelblatt_logo($pdf);
        return;
    }
    if (!$titel && function_exists('get_header_logo')) {
        get_header_logo($pdf);
        return;
    }

    // Fallback-Wortmarke
    $pdf->SetFillColorArray($P['primary']);
    $pdf->SetLineStyle(['width' => 0, 'color' => $P['primary']]);
    $pdf->RoundedRect($x, $y, $sq, $sq, $sq * 0.14, '1111', 'F');
    $m = $sq * 0.24;
    $pdf->SetDrawColorArray($P['white']);
    $pdf->SetLineStyle(['width' => max(0.3, $sq * 0.05), 'color' => $P['white']]);
    $pdf->Rect($x + $m, $y + $m, $sq - 2 * $m, $sq - 2 * $m, 'D');
    $pdf->Line($x + $sq / 2, $y + $m, $x + $sq / 2, $y + $sq - $m);
    $pdf->Line($x + $m, $y + $sq / 2, $x + $sq - $m, $y + $sq / 2);
    $pdf->SetTextColorArray($P['valtext']);
    $pdf->SetFont('helvetica', 'B', $fontpt);
    $pdf->SetXY($x + $sq + 2.5, $y);
    $pdf->Cell(0, $sq, 'Raumbuch', 0, 0, 'L', false, '', 0, false, 'T', 'M');
}

/* ═══════════════════════════════════════════════════════════════════════════
   PDF-KLASSE  (Kopfzeile mit Logo + Raumtyp-Balken · Vorgabe-Fußzeile)
   ═══════════════════════════════════════════════════════════════════════════ */

class RaumtypenKatalogPDF extends TCPDF
{
    public $rec = null, $idx = 0, $total = 0, $palette = [], $contentY = 48.0;
    public $projekt = 'Projekt', $datum = '';

    public function Header()
    {
        $P = $this->palette;
        $mL = 12;
        $w = $this->getPageWidth() - 2 * $mL;

        // ── Kopfzeile: Logo links, Kontext rechts, feine Trennlinie ───
        rk_logo($this, $mL, 7.5, 8, 12, $P, false);
        $this->SetTextColorArray($P['muted']);
        $this->SetFont('helvetica', '', 8);
        $this->SetXY($mL, 8.0);
        $this->Cell($w, 3.6, 'Raumtypenblätter', 0, 0, 'R');
        $this->SetXY($mL, 11.6);
        $this->Cell($w, 3.6, $this->projekt, 0, 0, 'R');
        $this->SetDrawColorArray($P['border']);
        $this->SetLineStyle(['width' => 0.2, 'color' => $P['border']]);
        $this->Line($mL, 17.6, $mL + $w, 17.6);

        // ── Raumtyp-Balken ────────────────────────────────────────────
        $barY = 19.6;
        $barH = 14;
        $r = 2;
        $this->SetFillColorArray($P['primary']);
        $this->SetLineStyle(['width' => 0, 'color' => $P['primary']]);
        $this->RoundedRect($mL, $barY, $w, $barH, $r, '1111', 'F');

        $bW = 10;
        $bX = $mL + 2;
        $bY = $barY + ($barH - $bW) / 2;
        $this->SetFillColorArray($P['badge']);
        $this->RoundedRect($bX, $bY, $bW, $bW, 1.3, '1111', 'F');
        $this->SetTextColorArray($P['white']);
        $this->SetFont('helvetica', 'B', 13);
        $this->SetXY($bX, $bY);
        $this->Cell($bW, $bW, (string)($this->rec['id'] ?? $this->idx), 0, 0, 'C', false, '', 0, false, 'T', 'M');

        $tX = $bX + $bW + 4;
        $this->SetFont('helvetica', 'B', 6.5);
        $this->SetXY($tX, $barY + 2.0);
        $this->Cell(0, 3, 'RAUMTYP', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 13);
        $this->SetXY($tX, $barY + 4.7);
        $this->Cell($w - ($tX - $mL) - 4, 7, (string)($this->rec['bezeichnung'] ?? ''), 0, 0, 'L', false, '', 0, false, 'T', 'M');

        // ── Beschreibung ──────────────────────────────────────────────
        $descY = $barY + $barH + 2.2;
        $this->SetXY($mL, $descY);
        $this->SetTextColorArray($P['muted']);
        $this->SetFont('helvetica', 'I', 8.5);
        $desc = trim((string)($this->rec['beschreibung'] ?? ''));
        if ($desc !== '') {
            $this->MultiCell($w, 0, $desc, 0, 'L', false, 1, $mL, $descY, true, 0, false, true, 0, 'T');
            $this->contentY = $this->GetY() + 2.2;
        } else {
            $this->contentY = $descY + 0.5;
        }
    }

    public function Footer()
    {
        if ($this->PageNo() <= 1) {
            return;
        }   // kein Standard-Footer auf dem Titelblatt
        $P = $this->palette;
        $this->SetY(-15);
        $this->SetDrawColorArray($P['border']);
        $this->SetLineStyle(['width' => 0.2, 'color' => $P['border']]);
        $this->Cell(0, 0, '', 'T', 0, 'L');
        $this->Ln(1.2);
        $this->SetTextColorArray($P['muted']);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 0, $this->datum, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . ($this->PageNo() - 1) . ' von ' . $this->total, 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

/* ═══════════════════════════════════════════════════════════════════════════
   EINE gegliederte Doppelspalte zeichnen (Themenblöcke + Parameter/Wert)
   ═══════════════════════════════════════════════════════════════════════════ */
function rk_draw_blocks(TCPDF $pdf, array $rec, array $blocks, float $x, float $y0,
                        float $wLabel, float $wVal, array $P): void
{
    $padH = 1.2;
    $padV = 0.7;
    $lineH = 3.15;
    $minH = 4.7;
    $colW = $wLabel + $wVal;
    $pdf->setCellPaddings($padH, $padV, $padH, $padV);

    $y = $y0;
    $firstBlock = true;
    foreach ($blocks as [$blockTitle, $fields]) {
        // kleine Leerzeile zwischen Themenblöcken
        if (!$firstBlock) {
            $y += 1.6;
        }
        $firstBlock = false;

        // Block-Überschrift
        $pdf->SetFillColorArray($P['blockbg']);
        $pdf->SetDrawColorArray($P['blockbg']);
        $pdf->SetTextColorArray($P['valtext']);
        $pdf->SetFont('helvetica', 'B', 7.6);
        $pdf->MultiCell($colW, 4.6, $blockTitle, 0, 'L', true, 1, $x, $y, true, 0, false, true, 4.6, 'M');
        $y += 4.6;

        // Parameterzeilen mit Zebra
        $i = 0;
        $pdf->SetLineStyle(['width' => 0.15, 'color' => $P['border']]);
        foreach ($fields as [$key, $label, $type]) {
            $val = ($type === RK_C && is_callable($key)) ? $key($rec) : rk_fmt($rec[$key] ?? null, $type);

            $pdf->SetFont('helvetica', '', 7.5);
            $nL = $pdf->getNumLines($label, $wLabel - 2 * $padH);
            $nV = $pdf->getNumLines($val, $wVal - 2 * $padH);
            $lines = max($nL, $nV, 1);
            $h = $lines * $lineH + 2 * $padV;
            if ($h < $minH) $h = $minH;

            $pdf->SetFillColorArray($P['labelbg']);
            $pdf->SetTextColorArray($P['labeltext']);
            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->MultiCell($wLabel, $h, $label, 1, 'L', true, 0, $x, $y, true, 0, false, true, $h, 'M');

            $pdf->SetFillColorArray(($i % 2 === 0) ? $P['white'] : $P['zebra']);
            $pdf->SetTextColorArray($P['valtext']);
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->MultiCell($wVal, $h, $val, 1, 'L', true, 1, $x + $wLabel, $y, true, 0, false, true, $h, 'M');

            $y += $h;
            $i++;
        }
    }
}

/* ═══════════════════════════════════════════════════════════════════════════
   PDF AUFBAUEN
   ═══════════════════════════════════════════════════════════════════════════ */
$pdf = new RaumtypenKatalogPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->palette = $RK;
$pdf->total = count($labortypen);
$pdf->projekt = $RK_PROJEKT;
$pdf->datum = $RK_DATUM;

$pdf->SetCreator('Raumbuch');
$pdf->SetAuthor('Raumbuch');
$pdf->SetTitle('Raumtypenblätter – ' . $RK_PROJEKT);
$pdf->SetMargins(12, 40, 12);
$pdf->SetAutoPageBreak(false, 12);
$pdf->setImageScale(1.25);

/* ── TITELBLATT ───────────────────────────────────────────────────────────── */
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$mL = 12;
$wFull = $pdf->getPageWidth() - 2 * $mL;
rk_logo($pdf, $mL, 24, 18, 24, $RK, true);

// Titelblock
$pdf->SetXY($mL, 104);
$pdf->SetFillColorArray($RK['primary']);
$pdf->SetLineStyle(['width' => 0, 'color' => $RK['primary']]);
$pdf->RoundedRect($mL, 104, 3, 22, 0.6, '1111', 'F');       // Lime-Akzent
$pdf->SetXY($mL + 7, 104);
$pdf->SetTextColorArray($RK['valtext']);
$pdf->SetFont('helvetica', 'B', 30);
$pdf->Cell(0, 14, 'Raumtypenblätter', 0, 2, 'L');
$pdf->SetFont('helvetica', '', 15);
$pdf->SetTextColorArray($RK['muted']);
$pdf->Cell(0, 9, 'Raumtypen-Katalog Labor', 0, 2, 'L');

// Metazeilen
$pdf->SetXY($mL + 7, 140);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColorArray($RK['valtext']);
$pdf->Cell(0, 8, trim($RK_PROJEKT), 0, 2, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColorArray($RK['muted']);
$pdf->Cell(0, 7, 'Stand: ' . $RK_DATUM, 0, 2, 'L');
$pdf->Cell(0, 7, $pdf->total . ' Raumtypen', 0, 2, 'L');

// dezente Fußlinie am Titelblatt
$pdf->SetDrawColorArray($RK['border']);
$pdf->SetLineStyle(['width' => 0.2, 'color' => $RK['border']]);
$pdf->Line($mL, 276, $mL + $wFull, 276);
$pdf->SetXY($mL, 278);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColorArray($RK['muted']);
$pdf->Cell($wFull, 4, 'Raumtypen-Katalog Labor', 0, 0, 'L');
$pdf->SetXY($mL, 278);
$pdf->Cell($wFull, 4, $RK_DATUM, 0, 0, 'R');

/* ── RAUMTYP-SEITEN ───────────────────────────────────────────────────────── */
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);

$usable = $pdf->getPageWidth() - 2 * $mL;   // 186 mm
$gap = 6;
$colW = ($usable - $gap) / 2;             // 90 mm
$wLabel = $colW * 0.54;
$wVal = $colW - $wLabel;

$i = 0;
foreach ($labortypen as $rec) {
    $i++;
    $pdf->rec = $rec;
    $pdf->idx = $i;
    $pdf->AddPage();
    $y0 = $pdf->contentY;
    rk_draw_blocks($pdf, $rec, $RK_BLOCKS_LEFT, $mL, $y0, $wLabel, $wVal, $RK);
    rk_draw_blocks($pdf, $rec, $RK_BLOCKS_RIGHT, $mL + $colW + $gap, $y0, $wLabel, $wVal, $RK);
}

/* ── Ausgabe ──────────────────────────────────────────────────────────────── */
$dest = (isset($_GET['dl']) && $_GET['dl']) ? 'D' : 'I';
$pdf->Output('Raumtypenblaetter.pdf', $dest);