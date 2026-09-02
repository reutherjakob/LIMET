<?php
/**
 * pdf_nutzerabfrage_sf.php
 * ---------------------------------------------------------------------------
 * Bericht zur Nutzerabfrage als PDF (TCPDF) – im Stil von pdf_raumtypen_sf.php.
 *
 *  - Titelblatt ("Bericht zur Nutzerabfrage" + Projekt + Stand: Datum)
 *  - Seite 1: Erörterung
 *       1. Raumtypen steuern, welche Fragen gestellt werden (eigener Bericht)
 *       2. Abstimmung des Fragenkatalogs mit Fachplanern und Nutzern
 *  - danach: Ergebnisse je Raum als Karte (Lime-Kopf + Label/Wert-Zeilen)
 *  - Kopfzeile mit Lime-Logo, Fußzeile (Datum + Seite X von Y)
 *  - Datenbasis identisch zu ergebnisse.php (inkl. "nicht abgefragt"-Logik)
 *
 *  Aufruf:  .../pdf_nutzerabfrage_sf.php          (Inline)
 *           .../pdf_nutzerabfrage_sf.php?dl=1      (Download)
 * ---------------------------------------------------------------------------
 */

/* Puffer als Schutz gegen versehentliche Ausgabe vor der PDF-Erzeugung */
ob_start();

/* ═══════════════════════════════════════════════════════════════════════════
   KONFIG  –  Pfade wie in pdf_raumtypen_sf.php
   ═══════════════════════════════════════════════════════════════════════════ */
$NB_TCPDF_PATH = __DIR__ . '/../TCPDF-main/TCPDF-main/tcpdf.php';
$NB_LOGO_PATH = __DIR__ . '/../PDFs/pdf_createBericht_LOGO.php'; // get_header_logo()/get_titelblatt_logo()
$NB_LOGO_IMG = '';   // optional: Pfad zu einer Logo-Grafik; hat Vorrang

/* ── Login / DB / Daten (wie in ergebnisse.php, gleicher Ordner) ──────────── */
require_once "../Nutzerlogin/_utils.php";
if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}
global $mysqli;

$role = init_page(["internal_rb_user", "spargelfeld_admin", "spargelfeld_view"]);
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

$projektid = 95;

require_once "form_fields_forNutzergruppe1.php"; // $formFields (Basis)
require_once "raumtyp_resolver.php";             // getRaumtypById / applyRaumtypOverrides
require_once "../Nutzerumfrage/raumtypen.php";   // lädt $labortypen

/* ── TCPDF + Logo laden ───────────────────────────────────────────────────── */
if (!is_file($NB_TCPDF_PATH)) {
    die('Fehler: TCPDF nicht gefunden. Bitte $NB_TCPDF_PATH anpassen.');
}
require_once $NB_TCPDF_PATH;
if (is_file($NB_LOGO_PATH)) {
    include_once $NB_LOGO_PATH;
}

/* ── Projekt / Datum ──────────────────────────────────────────────────────── */
$NB_PROJEKT = (isset($_SESSION['projectName']) && $_SESSION['projectName'] !== '')
    ? $_SESSION['projectName'] : 'Projekt-ID ' . $projektid;
$NB_DATUM =    $_SESSION["PDFdatum"] ?? date('d.m.Y');

/* ═══════════════════════════════════════════════════════════════════════════
   FARB-PALETTE (identisch zum Raumtypen-Report)
   ═══════════════════════════════════════════════════════════════════════════ */
$NB = [
    'primary' => [110, 150, 80],
    'badge' => [74, 103, 53],
    'labelbg' => [220, 235, 190],
    'blockbg' => [198, 220, 168],
    'zebra' => [240, 244, 233],
    'white' => [255, 255, 255],
    'border' => [205, 215, 190],
    'valtext' => [15, 42, 7],
    'labeltext' => [60, 82, 40],
    'muted' => [90, 110, 70],
    'title' => [255, 255, 255],
];

/* Hex-Varianten für die HTML-Ausgabe (writeHTML) */
$HEX = [
    'primary' => '#6E9650',
    'labelbg' => '#DCEBBE',
    'zebra' => '#F0F4E9',
    'white' => '#FFFFFF',
    'border' => '#CDD7BE',
    'valtext' => '#0F2A07',
    'labeltext' => '#3C5228',
    'muted' => '#5A6E46',
];

/* ═══════════════════════════════════════════════════════════════════════════
   ERGEBNIS-FELDER  ·  [feld, Label, Typ(B|N|T), Kommentarfeld|null]
   Reihenfolge/Bezeichnungen abgeleitet aus form_fields_forNutzergruppe1.php
   ═══════════════════════════════════════════════════════════════════════════ */
$NB_RESULT_FIELDS = [
    ['doppelfluegeltuer', 'Doppelflügeltür (1,8 m) erforderlich', 'B', null],
    ['vibrationsempfindlich_bodenstehend', 'Vibrationsempfindliche, bodenstehende Geräte', 'B', 'vibrationsempfindlich_bodenstehend_kommentar'],
    ['explosionsschutz', 'Explosionsschutz (gesamter Raum)', 'B', null],
    ['abluftwaescher', 'Abzüge mit Säurewäscher (Anzahl)', 'N', 'abluftwaescher_kommentar'],
    ['spezialgas', 'Dezentrale Sondergase', 'T', 'spezialgas_kommentar'],
    ['raumabluft_besonders', 'Besondere Anforderung Raumabluft', 'T', null],
    ['raumzuluft_besonders', 'Filterung der Zuluft', 'T', null],
    ['nutzwasser', 'Nutzwasser erforderlich', 'B', 'nutzwasser_kommentar'],
    ['spezialabwasser', 'Abwasser mit Spezialbehandlung', 'T', 'spezialabwasser_kommentar'],
    ['DL', 'Druckluft erforderlich', 'B', null],
    ['N2', 'Stickstoff (N2) erforderlich', 'B', null],
    ['Vakuum', 'Vakuumversorgung erforderlich', 'B', null],
    ['kuehlwasser', 'Kühlwasser für Geräte', 'B', 'kuehlwasser_kommentar'],
    ['raumtemp', 'Besondere Raumtemperatur', 'B', 'raumtemp_kommentar'],
    ['luftf', 'Besondere Luftfeuchtigkeit', 'B', 'luftf_kommentar'],
    ['allgemeiner_kommentar', 'Kommentar zum Raum', 'T', null],
];

/* ── "nicht abgefragt"-Logik (identisch zu ergebnisse.php) ────────────────── */
$baseFormFields = $formFields;
$baseHidden = [];
foreach ($baseFormFields as $f) {
    if (($f['type'] ?? '') === 'texthidden' && isset($f['name'])) {
        $baseHidden[$f['name']] = true;
    }
}
$hiddenCache = [];
function hiddenFieldsForRaumtyp($raumtypId): array
{
    global $hiddenCache, $labortypen, $baseFormFields, $baseHidden;
    $key = (string)$raumtypId;
    if (isset($hiddenCache[$key])) return $hiddenCache[$key];

    $rt = getRaumtypById($labortypen, $key);
    $resolved = applyRaumtypOverrides($baseFormFields, $rt);

    $hidden = [];
    foreach ($resolved as $f) {
        $name = $f['name'] ?? null;
        if (!$name) continue;
        if (($f['type'] ?? '') === 'texthidden' && empty($baseHidden[$name])) {
            $hidden[$name] = true;
        }
    }
    $hiddenCache[$key] = $hidden;
    return $hidden;
}

/* ── Ergebnisse laden (gleiche Abfrage wie ergebnisse.php) ────────────────── */
$sql = "SELECT t.*,
               r.Geschoss,
               r.Bauabschnitt,
               r.`Raumtyp BH` AS raumtyp_id,
               r.`Raumbereich Nutzer` AS rb
        FROM tabelle_room_requirements_from_user t
        JOIN tabelle_räume r ON t.roomID = r.idTABELLE_Räume
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
            AND  r.`Raumtyp BH` <> 34
            AND  r.`Raumtyp BH` <> 35
        ORDER BY  t.raumnr, Bauabschnitt ";

$rows = [];
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $projektid);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
}

/* ═══════════════════════════════════════════════════════════════════════════
   WERT-FORMATIERUNG  ·  liefert [Anzeigetext, istPositiv]
   ═══════════════════════════════════════════════════════════════════════════ */
function nb_fmt($val, string $type): array
{
    $s = ($val === null) ? '' : trim((string)$val);
    if ($type === 'B') {
        if ($s === '') return ['—', false];
        return ($s === '1' || strtolower($s) === 'ja') ? ['Ja', true] : ['Nein', false];
    }
    if ($type === 'N') {
        if ($s === '') return ['—', false];
        $pos = (is_numeric($s) && (float)$s > 0);
        return [$s, $pos];
    }
    // Text
    if ($s === '') return ['—', false];
    $l = strtolower($s);
    if ($s === '0' || $l === 'nein') return ['Nein', false];
    return [$s, true];
}

/* ═══════════════════════════════════════════════════════════════════════════
   LOGO  (wie im Raumtypen-Report)
   ═══════════════════════════════════════════════════════════════════════════ */
function nb_logo(TCPDF $pdf, float $x, float $y, float $sq, float $fontpt, array $P, bool $titel = false): void
{
    global $NB_LOGO_IMG;
    if ($NB_LOGO_IMG !== '' && is_file($NB_LOGO_IMG)) {
        $pdf->Image($NB_LOGO_IMG, $x, $y, 0, $sq, '', '', 'T', true, 300, 'L');
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
   PDF-KLASSE  (Kopfzeile Logo + Kontext · Fußzeile Datum + Seite X von Y)
   ═══════════════════════════════════════════════════════════════════════════ */

class NutzerabfrageBerichtPDF extends TCPDF
{
    public $palette = [], $projekt = 'Projekt', $datum = '';
    public $headerTitle = 'Abfrage Sonderausstattung';

    public function Header()
    {
        $P = $this->palette;
        $mL = 12;
        $w = $this->getPageWidth() - 2 * $mL;

        nb_logo($this, $mL, 7.5, 8, 12, $P, false);
        $this->SetTextColorArray($P['muted']);
        $this->SetFont('helvetica', '', 8);
        $this->SetXY($mL, 8.0);
        $this->Cell($w, 3.6, $this->headerTitle, 0, 0, 'R');
        $this->SetXY($mL, 11.6);
        $this->Cell($w, 3.6, $this->projekt, 0, 0, 'R');
        $this->SetDrawColorArray($P['border']);
        $this->SetLineStyle(['width' => 0.2, 'color' => $P['border']]);
        $this->Line($mL, 16, $mL + $w, 16);
        $this->SetXY($mL, 16);
    }

    public function Footer()
    {
        $P = $this->palette;
        $this->SetY(-15);
        $this->SetDrawColorArray($P['border']);
        $this->SetLineStyle(['width' => 0.2, 'color' => $P['border']]);
        $this->Cell(0, 0, '', 'T', 0, 'L');
        $this->Ln(1.2);
        $this->SetTextColorArray($P['muted']);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 0, $this->datum, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getPageNumGroupAlias() . ' von ' . $this->getPageGroupAlias(),
            0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

/* ═══════════════════════════════════════════════════════════════════════════
   PDF AUFBAUEN
   ═══════════════════════════════════════════════════════════════════════════ */
$pdf = new NutzerabfrageBerichtPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->palette = $NB;
$pdf->projekt = $NB_PROJEKT;
$pdf->datum = $NB_DATUM;

$pdf->SetCreator('Raumbuch');
$pdf->SetAuthor('Raumbuch');
$pdf->SetTitle('Bericht zur Nutzerabfrage Sonderausstattungen – ' . $NB_PROJEKT);
$pdf->SetMargins(12, 18, 12);
$pdf->SetAutoPageBreak(true, 16);
$pdf->setImageScale(1.25);

$mL = 12;
$wFull = $pdf->getPageWidth() - 2 * $mL;

/* ── TITELBLATT ───────────────────────────────────────────────────────────── */
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

nb_logo($pdf, $mL, 24, 18, 24, $NB, true);

$pdf->SetXY($mL, 104);
$pdf->SetFillColorArray($NB['primary']);
$pdf->SetLineStyle(['width' => 0, 'color' => $NB['primary']]);
$pdf->RoundedRect($mL, 104, 3, 22, 0.6, '1111', 'F');   // Lime-Akzent
$pdf->SetXY($mL + 7, 104);
$pdf->SetTextColorArray($NB['valtext']);
$pdf->SetFont('helvetica', 'B', 30);
$pdf->Cell(0, 14, 'Bericht zur Nutzerabfrage', 0, 2, 'L');
$pdf->SetFont('helvetica', '', 15);
$pdf->SetTextColorArray($NB['muted']);
$pdf->Cell(0, 9, 'Erhebung der raumbezogenen Sonderanforderungen', 0, 2, 'L');

$pdf->SetXY($mL + 7, 140);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColorArray($NB['valtext']);
$pdf->Cell(0, 8, trim($NB_PROJEKT), 0, 2, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColorArray($NB['muted']);
$pdf->Cell(0, 7, 'Stand: ' . $NB_DATUM, 0, 2, 'L');
$pdf->Cell(0, 7, count($rows) . ' ausgewertete Räume', 0, 2, 'L');


/* ── AB HIER: Kopf-/Fußzeile + Seitengruppe (Seitenzählung ohne Titelblatt) ── */
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->startPageGroup();

/* ── SEITE 1: ERÖRTERUNG ──────────────────────────────────────────────────── */
$pdf->AddPage();

$css = '<style>
    h2 { color:' . $HEX['valtext'] . '; font-size:13pt; }
    p  { color:' . $HEX['valtext'] . '; font-size:10pt; line-height:1.5; text-align:justify; }
    ul { color:' . $HEX['valtext'] . '; font-size:10pt; }
    li { color:' . $HEX['valtext'] . '; font-size:10pt; line-height:1.5; }
    .lead { color:' . $HEX['muted'] . '; font-size:10pt; }
</style>';

$intro = $css . '
<h2>Grundlage: Standardausstattung laut Raumtypen</h2>
<p>Jedem labortechnisch relevanten Raum ist ein Raumtyp (gemäß RUF) zugeordnet.
Die Raumtypen legen grundlegende bauliche und technische Standards für die Laborräume fest &ndash; etwa die Medienversorgung,
 die Anforderungen an Lüftung und Klima, sowie die (Sicherheits-)Ausstattung. Diese
sind in einem eigenen Bericht ausführlich dokumentiert; dervorliegende Bericht baut auf dieser Systematik auf.</p>

<p>Der Raumtyp bestimmte, welche Fragen zu welchem Raum gestellt
wurden. Anforderungen, die durch den Raumtyp bereits eindeutig vorgegeben sind &ndash; 
beispielsweise die zentrale Stickstoff- und Druckluftversorgung oder feste Temperaturvorgaben
&ndash;, wurden nicht erneut abgefragt, sondern direkt aus dem Raumtyp übernommen. 
In der Ergebnisdarstellung werden für einen Raum nur die abgefragten Anforderungen aufgeführt.</p>

<h2>Erarbeiten des Fragenkatalogs</h2>
<p>Der Fragenkatalog (inkl. der standartisierten and standard Antworten je Frage) wurde mit den weiteren Fachplanern (u.a. HKLS, Elektrotechnik, Architektur, Statik) sowie mit den Nutzenden präzise abgestimmt.
Fachlich bereits geklärte oder durch den Raumtyp abgedeckte Punkte wurden bewusst ausgeklammert, um die Abfrage für die Nutzer schlank und fokussiert zu halten.
Zielsetzung war es, sicherzustellen, dass  </p>
<ul>
<li>die Fragen unmissverständlich formuliert und mit vordefinierten Standard-Antworten versehen sind. </li>
<li>die für den Vorentwurf essenziellen und fehlenden Angaben für die weiteren Fachplaner erhoben werden.</li>
<li>der Aufwand beim Ausfüllen für die Nutzenden minimiert bleibt und dennoch möglichst viele Angben gemacht werden können. </li>
</ul>

<h2>Nutzeraccounts</h2>
<p>Um die Integrität und Nachvollziehbarkeit der Abfrage, sowie auch eine simple Abwicklung und die Anonymität der Nutzenden, zu grantieren, 
wurden je AGES Abteilung/Instiut/Organistionseinheit Zugangsdaten erstellt und Nutzerintern verteilt. 
Je Account konnten nur die entsprechenden Räume bearbeitet werden. </p>

<h2>Nutzerabstimmung & Disclaimer </h2>
<p>Die aufgenommenen Nutzerwünsche bedeuten nicht automatisch, dass diese Sonderausstattungsangaben unhinterfragt umgesetzt werden können. 
Entsprechend wurden die Angaben in Abstimmung mit den Nutzergruppen nochmals reflektiert. Das Ergebiss der Abstimmung ist in den Reultaten auf diesem Bericht inkludiert.  
</p>
 
<p>Die im Folgenden dargestellten Ergebnisse bilden damit die von den Nutzern bestätigten raumspezifischen Zusatzanforderungen ab.</p>';

$pdf->writeHTML($intro, true, false, true, false, '');


/* ── FRAGENKATALOG (dynamisch aus $formFields) ────────────────────────────── */
$pdf->AddPage();

$catHead = $css . '<h2>Fragenkatalog der Nutzerabfrage</h2>'
    . '<p class="lead"><small>Übersicht aller Fragen mit den möglichen Antwortoptionen und der jeweils '
    . 'vorbelegten Standard-Antwort. Je nach Raumtyp werden einzelne Fragen nicht gestellt.</small></p>';
$pdf->writeHTML($catHead, true, false, true, false, '');

$skipTypes = ['texthidden', 'text_non_editable'];

$catalog = '<table border="0" cellpadding="3" cellspacing="0" style="width:100%; border:0.2mm solid ' . $HEX['border'] . ';">'
    . '<tr nobr="true">'
    . '<td width="7%"  bgcolor="' . $HEX['primary'] . '" style="color:#FFFFFF; font-size:8pt;"><b>Nr.</b></td>'
    . '<td width="50%" bgcolor="' . $HEX['primary'] . '" style="color:#FFFFFF; font-size:8pt;"><b>Frage</b></td>'
    . '<td width="28%" bgcolor="' . $HEX['primary'] . '" style="color:#FFFFFF; font-size:8pt;"><b>Antwortoptionen</b></td>'
    . '<td width="15%" bgcolor="' . $HEX['primary'] . '" style="color:#FFFFFF; font-size:8pt;"><b>Standard</b></td>'
    . '</tr>';

$nr = 0;
foreach ($formFields as $f) {
    $type = $f['type'] ?? '';
    if (in_array($type, $skipTypes, true) || !isset($f['label'])) continue;
    $nr++;

    // Frage-Label von HTML befreien
    $label = trim(preg_replace('/\s+/', ' ',
        strip_tags(str_replace(['<br>', '<br/>', '<br />'], ' ', $f['label']))));
    $info = trim((string)($f['info'] ?? ''));

    // Antwortoptionen
    if (!empty($f['options']) && is_array($f['options'])) {
        $opts = htmlspecialchars(implode(', ', array_values($f['options'])));
    } elseif ($type === 'textarea' || $type === 'text') {
        $opts = 'Freitext';
    } else {
        $opts = '–';
    }
    if ($type === 'multiselect') $opts .= ' <i>(Mehrfachauswahl)</i>';

    // Standard-Antwort
    if (array_key_exists('default_value', $f)) {
        $dv = $f['default_value'];
        if (!empty($f['options']) && is_array($f['options']) && array_key_exists($dv, $f['options'])) {
            $def = htmlspecialchars((string)$f['options'][$dv]);
        } elseif ($dv === '' || $dv === null) {
            $def = '(leer)';
        } else {
            $def = htmlspecialchars((string)$dv);
        }
    } elseif ($type === 'multiselect') {
        $def = '(keine)';
    } elseif ($type === 'textarea') {
        $def = '(leer)';
    } else {
        $def = '–';
    }

    $bg = ($nr % 2 === 0) ? $HEX['zebra'] : $HEX['white'];

    $frageCell = '<b>' . htmlspecialchars($label) . '</b>';
    if ($info !== '') {
        $frageCell .= '<br><span style="color:' . $HEX['muted'] . '; font-size:7pt;">'
            . htmlspecialchars($info) . '</span>';
    }

    $catalog .= '<tr nobr="true">'
        . '<td width="7%"  bgcolor="' . $bg . '" style="color:' . $HEX['muted'] . '; font-size:8pt;">' . $nr . '</td>'
        . '<td width="50%" bgcolor="' . $bg . '" style="color:' . $HEX['valtext'] . '; font-size:8pt;">' . $frageCell . '</td>'
        . '<td width="28%" bgcolor="' . $bg . '" style="color:' . $HEX['valtext'] . '; font-size:8pt;">' . $opts . '</td>'
        . '<td width="15%" bgcolor="' . $bg . '" style="color:' . $HEX['labeltext'] . '; font-size:8pt;"><b>' . $def . '</b></td>'
        . '</tr>';
}
$catalog .= '</table>';
$pdf->writeHTML($catalog, true, false, true, false, '');




/* ── ERGEBNISSE ───────────────────────────────────────────────────────────── */
$pdf->AddPage();

$head = $css . '<h2>Ergebnisse der Nutzerabfrage</h2>'
    . '<p class="lead"><small>' . count($rows) . ' ausgewertete Räume · &bdquo;Ja&ldquo;/&bdquo;Nein&ldquo; = Nutzerangabe · '
    . '&bdquo;&mdash;&ldquo; = keine Angabe. Nicht abgefragte Anforderungen sind je Raum nicht gelistet.</small>></p>';
$pdf->writeHTML($head, true, false, true, false, '');

if (empty($rows)) {
    $pdf->writeHTML('<p style="color:' . $HEX['muted'] . ';">Für dieses Projekt liegen noch keine Abfrageergebnisse vor.</p>',
        true, false, true, false, '');
} else {
    foreach ($rows as $row) {
        $hidden = hiddenFieldsForRaumtyp($row['raumtyp_id'] ?? '');

        $raumnr = htmlspecialchars((string)($row['raumnr'] ?? ''));
        $kat = htmlspecialchars((string)($row['raumkategorieAbfrage'] ?? ($row['roomname'] ?? '')));
        $rb = htmlspecialchars((string)($row['rb'] ?? ''));
        $trakt = htmlspecialchars((string)($row['Bauabschnitt'] ?? ''));
        $ebene = htmlspecialchars((string)($row['Geschoss'] ?? ''));
        $nf = htmlspecialchars((string)($row['nf'] ?? ''));

        // Karte je Raum – nobr hält kurze Karten zusammen
        $html = '<table nobr="true" border="0" cellpadding="3" cellspacing="0" '
            . 'style="width:100%; border:0.2mm solid ' . $HEX['border'] . ';">';

        // Kopfbalken
        $meta = 'Bereich: ' . ($rb !== '' ? $rb : '–')
            . ' &nbsp;·&nbsp; Trakt: ' . ($trakt !== '' ? $trakt : '–')
            . ' &nbsp;·&nbsp; Ebene: ' . ($ebene !== '' ? $ebene : '–')
            . ' &nbsp;·&nbsp; NF: ' . ($nf !== '' ? $nf . ' m²' : '–');
        $html .= '<tr><td colspan="2" bgcolor="' . $HEX['primary'] . '" style="color:#FFFFFF;">'
            . '<b>Raum ' . ($raumnr !== '' ? $raumnr : '—') . '</b>'
            . ($kat !== '' ? ' &nbsp;&nbsp; ' . $kat : '')
            . '<br><span style="font-size:7.5pt;">' . $meta . '</span>'
            . '</td></tr>';

        // Wert-Zeilen (nur abgefragte Felder)
        $i = 0;
        foreach ($NB_RESULT_FIELDS as [$field, $label, $type, $commentField]) {
            if (isset($hidden[$field])) continue;

            // Allg. Kommentar nur zeigen, wenn befüllt
            $raw = $row[$field] ?? null;
            if ($field === 'allgemeiner_kommentar' && trim((string)$raw) === '') continue;

            [$disp, $pos] = nb_fmt($raw, $type);

            // Kommentar anhängen, falls vorhanden
            if ($commentField !== null) {
                $c = trim((string)($row[$commentField] ?? ''));
                if ($c !== '') {
                    $disp .= ' <span style="color:' . $HEX['muted'] . '; font-style:italic;">('
                        . htmlspecialchars($c) . ')</span>';
                } else {
                    $disp = htmlspecialchars($disp);
                }
            } else {
                $disp = htmlspecialchars($disp);
            }

            $valColor = $pos ? $HEX['valtext'] : $HEX['muted'];
            $valInner = $pos ? ('<b>' . $disp . '</b>') : $disp;
            $rowBg = ($i % 2 === 0) ? $HEX['white'] : $HEX['zebra'];

            $html .= '<tr>'
                . '<td width="45%" bgcolor="' . $HEX['labelbg'] . '" style="color:' . $HEX['labeltext'] . '; font-size:8pt;">'
                . htmlspecialchars($label) . '</td>'
                . '<td width="55%" bgcolor="' . $rowBg . '" style="color:' . $valColor . '; font-size:8pt;">'
                . $valInner . '</td>'
                . '</tr>';
            $i++;
        }

        $html .= '</table><span style="font-size:3pt;"><br></span>';
        $pdf->writeHTML($html, true, false, true, false, '');
    }
}

/* ── Ausgabe ──────────────────────────────────────────────────────────────── */
if ($mysqli) {
    $mysqli->close();
}
ob_end_clean(); // evtl. Ausgabepuffer verwerfen (Schutz vor "data already output")

$dest = (isset($_GET['dl']) && $_GET['dl']) ? 'D' : 'I';
$pdf->Output('Bericht_Nutzerabfrage.pdf', $dest);