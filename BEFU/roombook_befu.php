<?php
/**
 * raumbuch_mt_word.php
 * -----------------------------------------------------------------------------
 * MT-Raumbuch als Word-Dokument.
 *
 * Zwei Modi in einer Datei:
 *   1) Normalaufruf  -> Bootstrap-Seite mit Vorschau + Download-Button
 *   2) ?export=word  -> streamt das Word-Dokument (.doc) und beendet.
 *
 * Darstellung der Elemente je Raum:
 *   - mode=tabelle  (Default): Elemente in EINER Tabelle (Kat + Element)
 *   - mode=liste             : Elemente aufgezählt, je Gewerk eine Subüberschrift
 *
 * Das <style> im Word-Export ist Dokumentformatierung fuer Word.
 * -----------------------------------------------------------------------------
 */

require_once '../utils/_utils.php';
init_page_serversides();   // Session, Login-Check, Navbar-Setup (wie documentationV2.php)

$projectID = (int)($_SESSION['projectID'] ?? 0);

// =============================================================================
//  KONFIGURATION
// =============================================================================

/**
 * Ordnet eine ElementID einer MT-Kategorie zu (anhand der ersten Ziffer).
 *   1 -> Ortsfest | 2 -> Ortsveränderlich | 5 -> MedGas | 3,4 -> Mobiliar | sonst -> Weiteres
 */
function mt_kategorie_fuer_elementid(string $elementId): string
{
    switch (substr(ltrim($elementId), 0, 1)) {
        case '1':
            return 'Ortsfest';
        case '2':
            return 'Ortsveränderlich';
        case '5':
            return 'MedGas';
        case '3':
        case '4':
            return 'Mobiliar';
        default:
            return 'Weiteres';
    }
}

/** Kurzkürzel je Kategorie fuer die Tabellenspalte. */
function mt_kat_kurz(string $kat): string
{
    static $map = [
        'Ortsfest' => '_of',
        'Ortsveränderlich' => '_ov',
        'MedGas' => '_m_gas',
        'Mobiliar' => '_möb',
        'Unkategorisiert' => '_etc',
    ];
    return $map[$kat] ?? $kat;
}

/** Reihenfolge der Gewerke in der Auflistungs-Darstellung. */
function mt_gewerk_reihenfolge(): array
{
    return ['Ortsfest', 'Ortsveränderlich', 'Mobiliar', 'MedGas', 'Weiteres'];
}

/** Legenden-Text (of = Ortsfest usw.). */
function mt_legende_paare(): array
{
    return [
        'of' => 'Ortsfest',
        'ov' => 'Ortsveränderlich',
        'gas' => 'MedGas',
        'möb' => 'Mobiliar',
        'w' => 'Weiteres',
    ];
}

/** Natürliche Sortierung von ElementIDs ("1.9" vor "1.10"). */
function mt_cmp_elementid(string $a, string $b): int
{
    $pa = array_map('intval', explode('.', $a));
    $pb = array_map('intval', explode('.', $b));
    $n = max(count($pa), count($pb));
    for ($i = 0; $i < $n; $i++) {
        $x = $pa[$i] ?? -1;
        $y = $pb[$i] ?? -1;
        if ($x !== $y) return $x <=> $y;
    }
    return 0;
}


// =============================================================================
//  DATEN LADEN
// =============================================================================

/**
 * Liest alle MT-relevanten Räume + ihre aggregierten Elemente aus der DB.
 * Rückgabe: [ 'uebersicht' => [...], 'bereiche' => [...] ]
 */
function raumbuch_daten_laden(mysqli $mysqli, int $projectID): array
{
    // ---- 1) Räume -----------------------------------------------------------
    $sqlRooms = "SELECT idTABELLE_Räume AS RaumID,
                        Raumnr,
                        Raumbezeichnung,
                        `Raumbereich Nutzer` AS Raumbereich,
                        Geschoss,
                        Nutzfläche,
                        Anwendungsgruppe,
                        H6020
                 FROM tabelle_räume
                 WHERE tabelle_projekte_idTABELLE_Projekte = $projectID
                   AND `MT-relevant` = 1
                   AND Entfallen = 0
                 ORDER BY `Raumbereich Nutzer`, Anwendungsgruppe, Raumnr";

    $resRooms = $mysqli->query($sqlRooms);
    $uebersicht = [];
    $rooms = [];
    if ($resRooms) {
        while ($r = $resRooms->fetch_assoc()) {
            $r['RaumID'] = (int)$r['RaumID'];
            $rooms[$r['RaumID']] = $r;
            $uebersicht[] = $r;
        }
    }

    // ---- 2) Elemente je Raum (Anzahl über Varianten summiert) ---------------
    $elementsByRoom = [];
    if (!empty($rooms)) {
        $sqlEls = "SELECT rhe.TABELLE_Räume_idTABELLE_Räume AS RaumID,
                          e.ElementID,
                          e.Bezeichnung,
                          SUM(rhe.Anzahl) AS Anzahl
                   FROM tabelle_räume_has_tabelle_elemente rhe
                   INNER JOIN tabelle_elemente e
                           ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
                   INNER JOIN tabelle_räume r
                           ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
                   WHERE r.tabelle_projekte_idTABELLE_Projekte = $projectID
                     AND r.`MT-relevant` = 1
                     AND r.Entfallen = 0
                   GROUP BY rhe.TABELLE_Räume_idTABELLE_Räume, e.ElementID, e.Bezeichnung
                   HAVING SUM(rhe.Anzahl) <> 0";

        $resEls = $mysqli->query($sqlEls);
        if ($resEls) {
            while ($e = $resEls->fetch_assoc()) {
                $rid = (int)$e['RaumID'];
                $kat = mt_kategorie_fuer_elementid($e['ElementID']);
                $elementsByRoom[$rid][] = [
                    'ElementID' => $e['ElementID'],
                    'Bezeichnung' => $e['Bezeichnung'],
                    'Anzahl' => (int)$e['Anzahl'],
                    'Kat' => mt_kat_kurz($kat),
                    'KatVoll' => $kat,          // Klartext-Gewerk fuer Auflistung
                ];
            }
        }
        foreach ($elementsByRoom as &$list) {
            usort($list, fn($a, $b) => mt_cmp_elementid($a['ElementID'], $b['ElementID']));
        }
        unset($list);
    }

    // ---- 3) Detail: nach Raumbereich gruppieren, gleich ausgestattete -------
    //         Räume zusammenfassen
    $bereiche = [];
    foreach ($rooms as $rid => $room) {
        $bereich = trim((string)$room['Raumbereich']);
        if ($bereich === '') {
            $bereich = '(ohne Raumbereich)';
        }
        if (!isset($bereiche[$bereich])) {
            $bereiche[$bereich] = ['gruppen' => []];
        }

        $els = $elementsByRoom[$rid] ?? [];

        // Signatur = Menge der Elemente inkl. Anzahl. Leere Räume: eindeutig.
        if ($els === []) {
            $sig = '__leer__' . $rid;
        } else {
            $parts = [];
            foreach ($els as $e) {
                $parts[] = $e['ElementID'] . '#' . $e['Anzahl'];
            }
            $sig = implode('|', $parts);
        }

        if (!isset($bereiche[$bereich]['gruppen'][$sig])) {
            $bereiche[$bereich]['gruppen'][$sig] = ['rooms' => [], 'elemente' => $els];
        }
        $bereiche[$bereich]['gruppen'][$sig]['rooms'][] = [
            'Raumnr' => $room['Raumnr'],
            'Raumbezeichnung' => $room['Raumbezeichnung'],
        ];
    }

    // ---- 4) je Gruppe: Überschrift bauen ------------------------------------
    foreach ($bereiche as $bereich => &$binfo) {
        foreach ($binfo['gruppen'] as $sig => &$gruppe) {
            $namen = array_map(fn($r) => mb_strtolower(trim($r['Raumbezeichnung'])), $gruppe['rooms']);
            $gruppe['sameName'] = (count(array_unique($namen)) === 1);

            if (count($gruppe['rooms']) === 1) {
                $r0 = $gruppe['rooms'][0];
                $gruppe['heading'] = trim($r0['Raumnr'] . ' - ' . $r0['Raumbezeichnung']);
            } elseif ($gruppe['sameName']) {
                $nummern = implode(', ', array_map(fn($r) => $r['Raumnr'], $gruppe['rooms']));
                $gruppe['heading'] = $nummern . ' - ' . $gruppe['rooms'][0]['Raumbezeichnung'];
            } else {
                $einzeln = array_map(fn($r) => trim($r['Raumnr'] . ' - ' . $r['Raumbezeichnung']), $gruppe['rooms']);
                $gruppe['heading'] = implode('  /  ', $einzeln);
            }
        }
        unset($gruppe);
        $binfo['gruppen'] = array_values($binfo['gruppen']);
    }
    unset($binfo);

    return ['uebersicht' => $uebersicht, 'bereiche' => $bereiche];
}


// =============================================================================
//  RENDERING-HELFER
// =============================================================================

function h(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Zellinhalt fuer Word in einen Null-Abstand-Absatz packen (Web: unverändert). */
function rb_cell(string $inner, bool $forWord): string
{
    return $forWord ? '<p class="cp">' . $inner . '</p>' : $inner;
}

function fmt_flaeche($v): string
{
    if ($v === null || $v === '') return '';
    return number_format((float)$v, 2, ',', '.') . ' m²';
}

/** Legende (of = Ortsfest ...) einzeilig. */
function render_legende(bool $forWord): string
{
    $paare = [];
    foreach (mt_legende_paare() as $kurz => $lang) {
        $paare[] = '<b>' . h($kurz) . '</b> = ' . h($lang);
    }
    $inner = implode(' &nbsp;·&nbsp; ', $paare);
    return $forWord
        ? '<p class="legende"><b>Legende Kat:</b> &nbsp;' . $inner . '</p>'
        : '<p class="text-muted small mb-3"><b>Legende Kat:</b> ' . $inner . '</p>';
}


function render_uebersicht(array $uebersicht, bool $forWord): string
{
    if ($forWord) {
        $html = '<table class="rb-table">';
        $html .= '<colgroup>'
            . '<col style="width:10%">
               <col style="width:26%">
               <col style="width:24%">'
            . '<col style="width:10%">
               <col style="width:10%">
               <col style="width:10%">
               <col style="width:10%">
               </colgroup>';
    } else {
        $html = '<table class="table table-sm table-bordered table-striped align-middle rb-tight">';
    }

    $html .= '<thead><tr>
            <th>Raumnr</th>
            <th>Raumbezeichnung</th>
            <th>Raumbereich</th>
            <th>Geschoss</th>
            <th>Nutzfläche</th>
            <th>RG</th>
            <th>H6020</th>
                </tr></thead><tbody>';

    foreach ($uebersicht as $r) {
        $html .= '<tr>'
            . '<td>' . rb_cell(h($r['Raumnr']), $forWord) . '</td>'
            . '<td>' . rb_cell(h($r['Raumbezeichnung']), $forWord) . '</td>'
            . '<td>' . rb_cell(h($r['Raumbereich']), $forWord) . '</td>'
            . '<td>' . rb_cell(h($r['Geschoss']), $forWord) . '</td>'
            . '<td>' . rb_cell(h(fmt_flaeche($r['Nutzfläche'])), $forWord) . '</td>'
            . '<td>' . rb_cell(h($r['Anwendungsgruppe']), $forWord) . '</td>'
            . '<td>' . rb_cell(h($r['H6020']), $forWord) . '</td>'
            . '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/** Elemente je Raum als Tabelle (Kat + Element). */
function render_raum_tabelle(array $elemente, bool $forWord): string
{
    if ($forWord) {
        $html = '<table class="rb-table">';
        $html .= '<colgroup>'
            . '<col style="width:7%"><col style="width:18%">'
            . '<col style="width:65%"><col style="width:10%">'
            . '</colgroup>';
    } else {
        $html = '<table class="table table-sm table-bordered align-middle rb-tight">';
    }

    $html .= '<thead><tr> 
            <th>Kat</th>
            <!--th>ElementID</th-->
            <th>Element</th>
         <!--th class="text-center">Anzahl</th-->
            </tr></thead><tbody>';

    foreach ($elemente as $e) {
        $html .= '<tr>'
            . '<td>' . rb_cell(h($e['Kat']), $forWord) . '</td>'
            . '<td>' . rb_cell(h($e['Bezeichnung']), $forWord) . '</td>'
            . '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/**
 * Elemente je Raum als Auflistung: je Gewerk eine Subüberschrift, darunter
 * die Elemente aufgezählt.
 */
function render_raum_liste(array $elemente, bool $forWord): string
{
    // nach Gewerk (Klartext) gruppieren
    $byGewerk = [];
    foreach ($elemente as $e) {
        $g = $e['KatVoll'] ?? 'Weiteres';
        $byGewerk[$g][] = $e;
    }

    $html = '';
    foreach (mt_gewerk_reihenfolge() as $gewerk) {
        if (empty($byGewerk[$gewerk])) {
            continue;
        }

        if ($forWord) {
            $html .= '<p class="gewerk">' . h($gewerk) . '</p>';
            $html .= '<ul class="rb-liste">';
        } else {
            $html .= '<h6 class="fw-bold text-secondary mt-2 mb-1">' . h($gewerk) . '</h6>';
            $html .= '<ul class="mb-2">';
        }

        foreach ($byGewerk[$gewerk] as $e) {
            $html .= '<li>' . h($e['Bezeichnung']) . '</li>';
        }
        $html .= '</ul>';
    }
    return $html;
}

/**
 * Detailteil. Überschriften:
 *   Word: h1 (Raumbereich) / h2 (Raum)  -> Navigationsbereich
 *   Web : h3 / h5
 *
 * $mode: 'tabelle' (Default) oder 'liste'
 */
function render_detail(array $bereiche, bool $forWord, string $mode = 'tabelle'): string
{
    $hBereich = $forWord ? 'h1' : 'h3';
    $hRaum = $forWord ? 'h2' : 'h5';

    $html = render_legende($forWord);

    foreach ($bereiche as $bereich => $binfo) {
        $cls = $forWord ? '' : ' class="mt-4 pt-2 border-top"';
        $html .= "<$hBereich$cls>" . h($bereich) . "</$hBereich>";

        foreach ($binfo['gruppen'] as $gruppe) {
            $cls = $forWord ? '' : ' class="mt-3"';
            $html .= "<$hRaum$cls>" . h($gruppe['heading']) . "</$hRaum>";

            if (empty($gruppe['elemente'])) {
                $html .= $forWord
                    ? '<p><i>Keine Elemente hinterlegt.</i></p>'
                    : '<p class="text-muted fst-italic">Keine Elemente hinterlegt.</p>';
                continue;
            }

            $html .= ($mode === 'liste')
                ? render_raum_liste($gruppe['elemente'], $forWord)
                : render_raum_tabelle($gruppe['elemente'], $forWord);
        }
    }
    return $html;
}


// =============================================================================
//  MODUS 2:  WORD-EXPORT
// =============================================================================

if (($_GET['export'] ?? '') === 'word') {

    if ($projectID <= 0) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Kein Projekt ausgewählt.';
        exit;
    }

    $mode = (($_GET['mode'] ?? '') === 'liste') ? 'liste' : 'tabelle';

    $mysqli = utils_connect_sql();
    if (method_exists($mysqli, 'set_charset')) {
        @$mysqli->set_charset('utf8mb4');
    }
    $daten = raumbuch_daten_laden($mysqli, $projectID);
    $mysqli->close();

    $projektName = trim($_SESSION['projectName'] ?? '');
    $planungphase = trim($_SESSION['projectPlanungsphase'] ?? '');
    $datum = $_SESSION['PDFdatum'] ?? date('d.m.Y');

    $safeName = preg_replace('/[^\p{L}\p{N}_\- ]/u', '', $projektName);
    $fileName = trim($safeName . '_GPMT_MT-Raumbuch_' . date('Y-m-d')) . '.doc';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $uebersichtHtml = render_uebersicht($daten['uebersicht'], true);
    $detailHtml = render_detail($daten['bereiche'], true, $mode);

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" '
        . 'xmlns:w="urn:schemas-microsoft-com:office:word" '
        . 'xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="utf-8">';
    echo '<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View>'
        . '<w:Zoom>100</w:Zoom></w:WordDocument></xml><![endif]-->';
    // Dokumentformatierung fuer Word: schmale Ränder, feste Tabellenbreite,
    // enger Zeilenabstand (line-height 1), minimales Zellpadding.
    echo '<style>
        @page { size: 21.0cm 29.7cm; margin: 1.4cm 1.4cm; mso-page-orientation: portrait; }
        body  { font-family:"Calibri",sans-serif; font-size:10pt; line-height:1.0; color:#000; }
        p     { margin:0 0 4pt; line-height:1.0; mso-line-height-rule:exactly; }
        h1 { font-size:15pt; color:#1f3864; border-bottom:1pt solid #1f3864; margin:14pt 0 5pt; line-height:1.1; }
        h2 { font-size:12pt; color:#2e4a7d; margin:9pt 0 3pt; line-height:1.1; }
        table.rb-table { border-collapse:collapse; width:100%; table-layout:fixed;
                         margin:2pt 0 8pt; mso-table-lspace:0pt; mso-table-rspace:0pt; }
        table.rb-table th, table.rb-table td {
            border:0.5pt solid #999; padding:0.4pt 3pt; font-size:9pt;
            line-height:1.0; mso-line-height-rule:exactly; vertical-align:top;
                   mso-para-margin-top:0; mso-para-margin-bottom:0;   
            word-wrap:break-word; overflow-wrap:break-word; }
        table.rb-table th { background:#e7edf5; text-align:left; }
        .title   { font-size:18pt; font-weight:bold; color:#1f3864; }
        .subtle  { color:#666; font-size:9.5pt; }
        .legende { font-size:9pt; color:#333; }
        .text-center { text-align:center; }

        p.cp { margin:0; mso-para-margin-top:0; mso-para-margin-bottom:0;
               line-height:1.0; mso-line-height-rule:exactly; }
        p.MsoNormal, table.rb-table td p, table.rb-table th p {
               margin:0; mso-para-margin-top:0; mso-para-margin-bottom:0;
               line-height:1.0; mso-line-height-rule:exactly; }

        /* Auflistungs-Darstellung */
        p.gewerk { font-weight:bold; color:#2e4a7d; font-size:10pt;
                   margin:6pt 0 2pt; line-height:1.1; }
        ul.rb-liste { margin:0 0 6pt; padding-left:16pt; }
        ul.rb-liste li { font-size:9.5pt; margin:0;
                         line-height:1.0; mso-line-height-rule:exactly; }
    </style></head><body>';

    echo '<div class="title">Medizintechnisches Raumbuch</div>';
    echo '<p class="subtle">Projekt: ' . h($projektName)
        . ($planungphase !== '' ? ' &nbsp;–&nbsp; PPH: ' . h($planungphase) : '')
        . '<br>Stand: ' . h($datum) . '</p>';

    echo '<h1>Übersicht der MT-relevanten Räume</h1>';
    echo $uebersichtHtml;
    echo '<p class="subtle">RG = Raumgruppe Ö-Norm 8101</p>';
    echo '<br clear="all" style="mso-special-character:line-break;page-break-before:always">';

    echo $detailHtml;

    echo '</body></html>';
    exit;
}


// =============================================================================
//  MODUS 1:  BOOTSTRAP-SEITE (Vorschau + Download)
// =============================================================================

$mysqli = utils_connect_sql();
if (method_exists($mysqli, 'set_charset')) {
    @$mysqli->set_charset('utf8mb4');
}
$daten = ($projectID > 0)
    ? raumbuch_daten_laden($mysqli, $projectID)
    : ['uebersicht' => [], 'bereiche' => []];
$mysqli->close();

$anzahlRaeume = count($daten['uebersicht']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>BEFU Rohling</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../Logo/iphone_favicon.png">
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<div class="container-fluid bg-light pb-5">
    <div id="limet-navbar"></div>
    <div class="row justify-content-center">
        <div class="col-xxl-12">
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <b>BEFU </b>
                        - Übersicht der <?php echo $anzahlRaeume; ?> MT-relevante Räume.
                        <p class="text-muted small"> (RG = Raum/-Anwendungsgruppe)</p>

                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="modeSwitch">
                            <label class="form-check-label" for="modeSwitch" id="modeSwitchLabel">Tabelle</label>
                        </div>
                        <a href="?export=word&amp;mode=tabelle" id="downloadBtn" class="btn btn-sm btn-primary">
                            <i class="fas fa-file-word"></i> Word-Dokument herunterladen
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="card col-6">
                            <div class="card-body">
                                <?php if ($anzahlRaeume === 0): ?>
                                    <div class="alert alert-info mb-0">
                                        Für dieses Projekt wurden keine MT-relevanten Räume gefunden.
                                    </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <?php echo render_uebersicht($daten['uebersicht'], false); ?>
                                </div>
                            </div>
                        </div>
                        <div class="card col-6">
                            <div class="card-body">
                                <div id="detail-tabelle">
                                    <?php echo render_detail($daten['bereiche'], false, 'tabelle'); ?>
                                </div>
                                <div id="detail-liste" class="d-none">
                                    <?php echo render_detail($daten['bereiche'], false, 'liste'); ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sw = document.getElementById('modeSwitch');
        if (!sw) return;
        var lbl = document.getElementById('modeSwitchLabel');
        var btn = document.getElementById('downloadBtn');
        var tab = document.getElementById('detail-tabelle');
        var lst = document.getElementById('detail-liste');

        function apply() {
            var liste = sw.checked;
            if (lbl) lbl.textContent = liste ? 'Auflistung' : 'Tabelle';
            if (btn) btn.href = '?export=word&mode=' + (liste ? 'liste' : 'tabelle');
            if (tab) tab.classList.toggle('d-none', liste);
            if (lst) lst.classList.toggle('d-none', !liste);
        }

        sw.addEventListener('change', apply);
        apply();
    });
</script>
</body>
</html>