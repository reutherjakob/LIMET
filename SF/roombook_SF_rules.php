<?php

// ─── Pfade zu den Definitions-Quellen — ggf. an euer Projekt anpassen ──────
$PFAD_DB_CONFIG = '../IFCQuest/import_2_db_config.php';   // ELEMENT_MAPPING, PARAMETER_MAPPING, Konstanten
$PFAD_RAUMTYPEN = '../Nutzerumfrage/raumtypen.php';            // $labortypen

  if (!function_exists('check_login')) { include "../utils/_utils.php"; }
 check_login();

// ─── Quellen einlesen (defensiv – keine Fatals bei falschem Pfad) ──────────
$quellen_fehler = [];

if (is_file($PFAD_DB_CONFIG)) {
    require_once $PFAD_DB_CONFIG;
} else {
    $quellen_fehler[] = "Konfiguration nicht gefunden: <code>" . htmlspecialchars($PFAD_DB_CONFIG) . "</code>";
}
if (is_file($PFAD_RAUMTYPEN)) {
    include $PFAD_RAUMTYPEN; // definiert $labortypen
} else {
    $quellen_fehler[] = "Raumtypen nicht gefunden: <code>" . htmlspecialchars($PFAD_RAUMTYPEN) . "</code>";
}

$labortypen = $labortypen ?? [];
$element_mapping = defined('ELEMENT_MAPPING') ? ELEMENT_MAPPING : [];
$parameter_mapping = defined('PARAMETER_MAPPING') ? PARAMETER_MAPPING : [];
$bestand_codes = defined('BESTAND_ELEMENT_CODES') ? BESTAND_ELEMENT_CODES : [];
$mz_warn_diff = defined('MZ_LAENGE_WARN_DIFF_CM') ? MZ_LAENGE_WARN_DIFF_CM : null;

// ══════════════════════════════════════════════════════════════════════════
// Werkzeug-Links (rechte Spalte)
// ══════════════════════════════════════════════════════════════════════════
$werkzeug_links = [
    ['icon' => 'fa-dragon',    'href' => '/SF/roombook_SF_rules.php',                 'label' => 'RULES'],
    ['icon' => 'fa-cat',       'href' => '/IFCQuest/import_excel.php',                'label' => 'Import Model Excel'],
    ['icon' => 'fa-spider',    'href' => '/SF/roombook_Raumtypen_zu_Raumbuch.php',    'label' => 'RT übernehmen'],
    ['icon' => 'fa-dog',       'href' => '/SF/roombook_RB_RT_Abgleich.php',           'label' => 'Abgleich RT - RB'],
    ['icon' => 'fa-frog',      'href' => '/SF/pdf_raumtypen_sf.php',                  'label' => 'RT PDF'],
    ['icon' => 'fa-kiwi-bird', 'href' => '/SF/roombook_sf_av_sv.php',                 'label' => 'AV/SV Aufteilung'],
    ['icon' => 'fa-kiwi-bird', 'href' => '/SF/roombook_sf_flaechen_je_raumtyp.php',   'label' => 'Flächen je Raumtyp'],
    ['icon' => 'fa-dove',      'href' => '/SF/roombook_elementsInProject_delete0.php','label' => 'Nuller Vars entfernen'],
];

// ══════════════════════════════════════════════════════════════════════════
// EBENE 1a — IMPORT-LOGIK (kuratiert)
// ══════════════════════════════════════════════════════════════════════════
$import_logik = [
    [
        'titel' => 'Parameter Mapping',
        'kategorie' => 'Grundprinzip',
        'text' => '   <strong> Auswahl-Parameter vs. Varianten-Parameter: </strong>   
                    <small> <code>element_params</code> bestimmen, welches Element gewählt wird 
                    (z.&nbsp;B. Breite×Tiefe beim Labortisch). <code>variante_params</code> bilden den
                     <strong>Varianten-Fingerabdruck</strong> und werden als Elementparameter hinterlegt.</small>
                     <br><br>
                   <strong>  Variante&nbsp;A ist Parameterlos.</strong><small> Elemente <em>mit</em> Parametern nehmen nie Var&nbsp;A: per Fingerprint wird 
                    eine passende Variante (id&nbsp;&gt;&nbsp;1) gesucht, sonst die erste freie Variante
                    belegt. Ab <code>Var_id&nbsp;≥&nbsp;30</code> löst Warnung aus.</small> 
                    <br><br>
                    <strong>Sondermaß-Fallback & Toleranz</strong><small>
                    Passt eine Breite zu keiner hinterlegten Standardlänge, greift das <code>sondermass</code>-Element 
                    des Typs. Abweichungen über der Toleranz werden im Abgleich gewarnt (Wert siehe Konfig-Leiste).</small> 
                    <br><br>
                    <strong> Nicht gemappte Parameter & „not managed" </strong> <small>
                    DB-Parameter, die weder Auswahl- noch Varianten-Param sind (z.&nbsp;B. Netzart SV/AV), werden 
                    ignoriert – nicht gelöscht, nicht überschrieben. Sie können aber „<strong>ambiguous</strong>" 
                    auslösen, wenn sich DB-Varianten nur dadurch unterscheiden. Elemente, deren ElementID in keinem 
                    Mapping als Ziel vorkommt, gelten als „<strong>not_managed</strong>" und werden nie angepasst.</small> ',
        'referenz' => 'import_2_db_config.php',
        'status' => 'regel',
    ],



    [
        'titel' => 'Elelemt Gruppen & Begleit Elemente',
        'kategorie' => 'Begleit-Element',
        'text' => '<strong>Digestorium</strong> wird derzeit mit einem zusätzlichen Sicherheitsunterbauschrank importiert.
                    <br><br> 
                    <strong> Spülenverbau ist eine Gruppe</strong> aus Spülbecken, 
                   Unterbau und Arbeitsplatte. <strong>Leitfamilie</strong> ist das Spülbecken: Anzahl Spülen = 
                   Anzahl Spülbecken. Pro Becken wird aus jedem Pool (Unterbau, Arbeitsplatte) max.&nbsp;1&nbsp;Stück 
                   nach geringster Breitendifferenz konsumiert. Überzählige Unterbauten/Arbeitsplatten fallen auf ihr Fallback-Element zurück.
                                       <br><br> 
                    <strong>RDGs</strong> werden als Bestand importiert.',
        'referenz' => 'ELEMENT_MAPPING → „…9-30-45-3 Digestorium - Standard" → begleit_elemente',
        'status' => 'zu_praezisieren',
        'status_text' => 'Zukünftig zu präzisieren - Unterbauten Typ aus Digestor Parameter zu extrahieren',
    ],


];

// ══════════════════════════════════════════════════════════════════════════
// EBENE 1b — RAUMTYPEN-REGELN (kuratiert) — im gleichen konsolidierten Stil
// ══════════════════════════════════════════════════════════════════════════
$raumtyp_regeln = [
    [
        'titel' => 'Element-Anzahlen je Raumtyp',
        'kategorie' => 'Element-Anzahl',
        'text' => '
                    <strong>Medien je Typ:</strong>
                    <small> Kalt-/Warm-/VE-Wasser, N₂ und DL sowie Sondergase sind je Raumtyp definiert. Die Matrix
                    zeigt pro Typ die geforderten Medien – Grundlage für die Anschlüsse im Raum.</small>
                    <br><br>
                    <strong>Anzahlen aus der Typ-Matrix:</strong>
                    <small> Abzüge, Punktabsaugungen und Sicherheitsschränke sind je Raumtyp als Min/Max bzw. Ja/Nein
                    hinterlegt (siehe Matrix unten) – die Sollvorgabe für die Möblierung.</small>',
        'referenz' => 'raumtypen.php',
        'status' => 'regel',
    ],
    [
        'titel' => 'Raumparameter je Raumtyp',
        'kategorie' => 'Raumparameter',
        'text' => '<strong>Raumabluft je Digestor und Unterbau:</strong>
                    <small> Für jeden Digestor <u>und</u> seinen Sicherheitsunterbau wird am Raum der Parameter
                    <strong>Abluft</strong> gesetzt. Ein Digestor erzeugt damit zwei Abluft-Beiträge
                    (Abzug&nbsp;+&nbsp;Unterbau), die in die Raumbilanz einfließen.</small>',
        'referenz' => 'raumtypen.php',
        'status' => 'regel',
        //'status_text' => '',
    ],
];

// ══════════════════════════════════════════════════════════════════════════
// EBENE 1c — ZEICHENANWEISUNGEN (kuratiert) — hier frei ergänzen
// ══════════════════════════════════════════════════════════════════════════
// 'scope' steuert nur die Badge-Farbe: 'element' | 'raumtyp' | 'allgemein'
$zeichenanweisungen = [
    [
        'titel' => 'Spülen',
        'scope' => 'element',
        'gilt_fuer' => 'Laborspüle',
        'text' => '<strong>Spülen-Anzahl nach Achsen:</strong>
                    <small> Grundausstattung Labor: Medienzellen samt Labortischen, Oberschränke entlang der
                    Medienzellen. <strong>1× Laborspüle für 3-Achser</strong>, <strong>ab 6-Achser 2× Laborspüle</strong>.</small>
                    <br><br>
                    Laborspülen werden im Bereich der <strong>Raumtür</strong> platziert (kurze Wege, Zugang zu '
            . 'Wasser/Abfluss beim Betreten).',
    ],
    [
        'titel' => 'Wägetische immer hinten im Eck',
        'scope' => 'element',
        'gilt_fuer' => 'Wägetisch (9.30.35.2)',
        'text' => 'Wägetische kommen in eine <strong>hintere Raumecke</strong> – möglichst erschütterungs- und '
            . 'zugluftarm, abseits von Verkehrswegen und Türen.',
    ],
    [
        'titel' => 'Nicht möbelierte Raumtypen',
        'scope' => 'raumtyp',
        'gilt_fuer' => 'mehrere Typen',
        'text' => 'Bestimmte Raumtypen (Büros, Lager u.&nbsp;a.) erhalten <strong>keine Laboreinrichtung</strong>. '
            . 'Die betroffenen Typen werden unten automatisch aus <code>labormoebel&nbsp;=&nbsp;0</code> abgeleitet.',
        'auto' => 'nicht_moebeliert',
    ],
];

// Auto-Ableitung: Raumtypen ohne Möblierung
$nicht_moebeliert = [];
foreach ($labortypen as $t) {
    $lm = trim((string)($t['labormoebel'] ?? ''));
    if ($lm === '' || $lm === '0') {
        $nicht_moebeliert[] = $t;
    }
}

// ══════════════════════════════════════════════════════════════════════════
// Render-Helfer
// ══════════════════════════════════════════════════════════════════════════
function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function yn($v): string
{
    $on = ($v === '1' || $v === 1 || $v === true);
    return $on
        ? '<i class="bi bi-check-lg text-success" title="ja"></i>'
        : '<span class="text-body-tertiary" title="nein">–</span>';
}

function status_badge(?string $status): string
{
    return match ($status) {
        'zu_praezisieren' => '<span class="badge text-bg-warning"><i class="bi bi-hourglass-split me-1"></i>zu präzisieren</span>',
        'regel' => '<span class="badge text-bg-success-subtle text-success-emphasis border border-success-subtle">fixe Regel</span>',
        default => '',
    };
}

function scope_badge(string $scope, string $label): string
{
    $c = match ($scope) {
        'element' => 'primary',
        'raumtyp' => 'info',
        default => 'secondary',
    };
    $icon = match ($scope) {
        'element' => 'bi-box',
        'raumtyp' => 'bi-door-open',
        default => 'bi-asterisk',
    };
    return '<span class="badge text-bg-' . $c . '"><i class="bi ' . $icon . ' me-1"></i>' . e($label) . '</span>';
}

/**
 * Einklappbaren Section-Kopf ausgeben.
 * $target = id des Collapse-Containers (muss unten geöffnet/geschlossen werden)
 */
function section_header(string $target, string $bi_icon, string $titel): void
{
    ?>
    <h2 class="h4 border-bottom pb-2 mb-3">
        <a class="text-reset text-decoration-none d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse" href="#<?= e($target) ?>" role="button" aria-expanded="true"
           aria-controls="<?= e($target) ?>">
            <span><i class="bi <?= e($bi_icon) ?> text-primary me-2"></i><?= e($titel) ?></span>
            <i class="bi bi-chevron-expand fs-6 text-body-tertiary"></i>
        </a>
    </h2>
    <?php
}

/** Karte für eine kuratierte Definition rendern. */
function render_karte(array $k): void
{
    ?>
    <div class="col-12 col-xl-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <h3 class="h6 card-title mb-0"><?= e($k['titel']) ?></h3>
                    <span class="badge text-bg-primary-subtle text-primary-emphasis text-nowrap"><?= e($k['kategorie'] ?? '') ?></span>
                </div>
                <p class="card-text small mb-2"><?= $k['text'] /* enthält bewusst HTML */ ?></p>
                <?php if (!empty($k['status_text'])): ?>
                    <div class="alert alert-warning py-1 px-2 small mb-2 mt-2">
                        <i class="bi bi-hourglass-split me-1"></i><?= e($k['status_text']) ?>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-center">
                    <?php if (!empty($k['referenz'])): ?>
                        <span class="text-body-tertiary" style="font-size:.72rem"><i
                                    class="bi bi-link-45deg"></i> <?= e($k['referenz']) ?></span>
                    <?php else: ?><span></span><?php endif; ?>
                    <?= status_badge($k['status'] ?? null) ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/** Ziel-Element(e) eines Mapping-Eintrags als Kurzfassung + optionale Detail-Zeilen. */
function mapping_targets(array $cfg): array
{
    $summary = '';
    $detail = [];
    if (isset($cfg['element_id'])) $summary = '<code>' . e($cfg['element_id']) . '</code>';
    if (!empty($cfg['laengen'])) {
        $n = count($cfg['laengen']);
        $summary = $n . '&nbsp;Länge' . ($n !== 1 ? 'n' : '');
        foreach ($cfg['laengen'] as $cm => $code) $detail[$cm . '&nbsp;cm'] = $code;
    }
    if (!empty($cfg['breite_tiefe'])) {
        $n = count($cfg['breite_tiefe']);
        $summary = $n . '&nbsp;Maße';
        foreach ($cfg['breite_tiefe'] as $bt => $code) $detail[$bt] = $code;
    }
    if (($cfg['typ'] ?? '') === 'gruppe') $summary = '<code>' . e($cfg['element_id'] ?? '?') . '</code> <span class="text-body-secondary">(Gruppe)</span>';
    if (isset($cfg['sondermass'])) $detail['Sondermaß'] = $cfg['sondermass'];
    return [$summary ?: '—', $detail];
}

function params_badges(array $params): string
{
    if (empty($params)) return '<span class="text-body-tertiary small">—</span>';
    $out = [];
    foreach ($params as $p) {
        $short = str_replace(['MT_LIMET_Anzahl ', 'MT_LIMET_Sichtbarkeit ', 'MT_LIMET_'], ['', 'Sicht. ', ''], $p);
        $out[] = '<span class="badge text-bg-light border me-1 mb-1 fw-normal" title="' . e($p) . '">' . e($short) . '</span>';
    }
    return implode('', $out);
}

?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Definitionen &amp; Regelwerk — Modell-Import / Raumbuch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">

<nav class="navbar navbar-expand bg-body border-bottom sticky-top">
    <div class="container-xxl">
        <span class="navbar-brand mb-0 h1">
            <i class="bi bi-journal-code me-2 text-primary"></i>Definitionen &amp; Regelwerk
        </span>
        <span class="navbar-text small d-none d-md-inline">
            Import-Logik &nbsp;·&nbsp; Raumtypen &nbsp;·&nbsp; Mapping &nbsp;·&nbsp; Zeichnung
        </span>
        <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Drucken
        </button>
    </div>
</nav>

<div class="container-xxl my-4">

    <?php if ($quellen_fehler): ?>
        <div class="alert alert-warning">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-1"></i>Quellen nicht vollständig geladen
            </h6>
            <ul class="mb-1 small">
                <?php foreach ($quellen_fehler as $f): ?>
                    <li><?= $f ?></li><?php endforeach; ?>
            </ul>
            <span class="small text-body-secondary">Bitte die Pfade oben in <code>definitionen.php</code> anpassen.</span>
        </div>
    <?php endif; ?>

    <p class="text-body-secondary">
        Fachliche Definitionen, die dem Modell-Import und der Raumbuch-Ableitung zugrunde liegen.
        Kuratierte Regeln (Import-Logik, Raumtyp-Regeln, Zeichenanweisungen) werden von Hand gepflegt;
        Raumtypen-Matrix und Mapping werden direkt aus den Quelldateien gelesen und bleiben so aktuell.
    </p>

    <div class="row g-4">
        <!-- ── Inhaltsverzeichnis + Werkzeuge ────────────────────────────── -->
        <aside class="col-lg-3 order-lg-2">
            <div class="position-sticky" style="top: 5rem">
                <div class="list-group shadow-sm">
                    <a href="#import" class="list-group-item list-group-item-action"><i
                                class="bi bi-arrow-repeat me-2"></i>Import-Logik <span
                                class="badge text-bg-secondary rounded-pill float-end"><?= count($import_logik) ?></span></a>
                    <a href="#raumtypen" class="list-group-item list-group-item-action"><i
                                class="bi bi-door-open me-2"></i>Raumtypen-Definitionen <span
                                class="badge text-bg-secondary rounded-pill float-end"><?= count($labortypen) ?></span></a>
                    <a href="#mapping" class="list-group-item list-group-item-action"><i
                                class="bi bi-diagram-3 me-2"></i>Revit- &amp; Parameter-Mapping</a>
                    <a href="#zeichnung" class="list-group-item list-group-item-action"><i
                                class="bi bi-pencil-square me-2"></i>Zeichenanweisungen <span
                                class="badge text-bg-secondary rounded-pill float-end"><?= count($zeichenanweisungen) ?></span></a>
                </div>

                <!-- ── Werkzeug-Links ───────────────────────────────────── -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header py-2 small fw-semibold text-body-secondary">
                        <i class="fas fa-toolbox me-1"></i> Weitere Websites
                    </div>
                    <div class="list-group list-group-flush small">
                        <?php foreach ($werkzeug_links as $l): ?>
                            <a class="list-group-item list-group-item-action" href="<?= e($l['href']) ?>">
                                <i class="fas <?= e($l['icon']) ?> fa-fw me-2 text-secondary"></i><?= e($l['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ── Inhalt ────────────────────────────────────────────────────── -->
        <main class="col-lg-9 order-lg-1">

            <!-- ═══ 1. Import-Logik ═══ -->
            <section id="import" class="mb-4">
                <?php section_header('sec-import', 'bi-arrow-repeat', 'Import-Logik'); ?>
                <div class="collapse show" id="sec-import">
                    <div class="row g-3">
                        <?php foreach ($import_logik as $k) render_karte($k); ?>
                    </div>

                    <!-- Konfig-Leiste: die harten Werte direkt aus import_2_db_config.php -->
                    <div class="card border-primary-subtle mt-3">
                        <div class="card-body py-2">
                            <div class="row g-3 align-items-center small">
                                <div class="col-md-8">
                                    <span class="text-uppercase text-body-tertiary fw-semibold" style="font-size:.68rem">Bestandsgeräte-Codes (RDG → Neu/Bestand = 0)</span><br>
                                    <?php foreach ($bestand_codes as $c): ?><span class="badge text-bg-light border me-1">
                                        <code><?= e($c) ?></code></span><?php endforeach; ?>
                                    <?php if (empty($bestand_codes)): ?><span
                                            class="text-body-tertiary">—</span><?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-uppercase text-body-tertiary fw-semibold" style="font-size:.68rem">Längen-Warntoleranz</span><br>
                                    <?= $mz_warn_diff !== null ? '<span class="badge text-bg-light border">&gt; ' . e($mz_warn_diff) . '&nbsp;cm → Warnung</span>' : '<span class="text-body-tertiary">—</span>' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══ 2. Raumtypen-Definitionen ═══ -->
            <section id="raumtypen" class="mb-4">
                <?php section_header('sec-raumtypen', 'bi-door-open', 'Raumtypen-Definitionen'); ?>
                <div class="collapse show" id="sec-raumtypen">
                    <div class="row g-3 mb-3">
                        <?php foreach ($raumtyp_regeln as $k) render_karte($k); ?>
                    </div>

                    <h3 class="h6 text-body-secondary mb-2"><i class="bi bi-table me-1"></i>Typ-Matrix — Element-Anzahlen
                        &amp; Raumparameter</h3>
                    <p class="small text-body-secondary">Aus <code>raumtypen.php</code>. Zeile aufklappen für Möbel- und
                        Sicherheitsdetails.</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle small">
                            <thead class="table-light">
                            <tr>
                                <th style="width:2.5rem">#</th>
                                <th>Bezeichnung</th>
                                <th class="text-center" title="Abzüge min–max">Abzüge</th>
                                <th class="text-center">Punktabs.</th>
                                <th class="text-center" title="Sicherheitsschrank Säure/Lauge">SiSchr S/L</th>
                                <th class="text-center" title="Sicherheitsschrank brennbar">SiSchr brenn.</th>
                                <th class="text-center" title="Kaltwasser">KW</th>
                                <th class="text-center" title="Warmwasser">WW</th>
                                <th class="text-center" title="VE-Wasser">VE</th>
                                <th class="text-center" title="Stickstoff">N₂</th>
                                <th class="text-center" title="Druckluft">DL</th>
                                <th style="width:2rem"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($labortypen as $t):
                                $rid = 'rt-' . e($t['id'] ?? '');
                                $abz_min = $t['abzuege_anzahl_min'] ?? '0';
                                $abz_max = $t['abzuege_anzahl_max'] ?? '0';
                                $abz = ($abz_min === $abz_max) ? $abz_min : "$abz_min – $abz_max";
                                ?>
                                <tr>
                                    <td class="text-body-secondary"><?= e($t['id'] ?? '') ?></td>
                                    <td class="fw-semibold"><?= e($t['bezeichnung'] ?? '') ?></td>
                                    <td class="text-center"><?= ($abz === '0') ? '<span class="text-body-tertiary">–</span>' : e($abz) ?></td>
                                    <td class="text-center small"><?php
                                        $pa = trim((string)($t['punktabsaugungen'] ?? '0'));
                                        echo ($pa === '0' || $pa === '') ? '<span class="text-body-tertiary">–</span>' : '<span title="' . e($pa) . '"><i class="bi bi-check-lg text-success"></i></span>';
                                        ?></td>
                                    <td class="text-center"><?= yn($t['sicherheitsschrank_saeure_lauge'] ?? '0') ?></td>
                                    <td class="text-center"><?= yn($t['sicherheitsschrank_brennbar'] ?? '0') ?></td>
                                    <td class="text-center"><?= yn($t['kaltwasser'] ?? '0') ?></td>
                                    <td class="text-center"><?= yn($t['warmwasser'] ?? '0') ?></td>
                                    <td class="text-center"><?= yn($t['ve_wasser'] ?? '0') ?></td>
                                    <td class="text-center"><?= yn($t['n2'] ?? '0') ?></td>
                                    <td class="text-center"><?= yn($t['dl'] ?? '0') ?></td>
                                    <td class="text-center">
                                        <a data-bs-toggle="collapse" href="#<?= $rid ?>" role="button"
                                           class="text-decoration-none" title="Details"><i
                                                    class="bi bi-chevron-down"></i></a>
                                    </td>
                                </tr>
                                <tr class="collapse" id="<?= $rid ?>">
                                    <td colspan="12" class="bg-body-tertiary">
                                        <div class="row g-3 py-2 px-1">
                                            <div class="col-md-6">
                                                <div class="text-uppercase text-body-tertiary fw-semibold"
                                                     style="font-size:.68rem">Labormöbel
                                                </div>
                                                <div><?= e($t['labormoebel'] ?? '—') ?: '—' ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-uppercase text-body-tertiary fw-semibold"
                                                     style="font-size:.68rem">Sicherheitsausstattung
                                                </div>
                                                <div><?= e($t['sicherheitsausstattung'] ?? '—') ?: '—' ?></div>
                                            </div>
                                            <div class="col-12">
                                                <div class="text-uppercase text-body-tertiary fw-semibold"
                                                     style="font-size:.68rem">Medien / Sonstiges
                                                </div>
                                                <div class="small text-body-secondary">
                                                    Sondergase: <?= e($t['sondergase'] ?? '—') ?> ·
                                                    Luftwechsel: <?= e($t['luftwechsel'] ?? '—') ?> ·
                                                    BSL: <?= (($t['bsl3'] ?? '0') === '1' ? '3' : (($t['bsl2'] ?? '0') === '1' ? '2' : (($t['bsl1'] ?? '0') === '1' ? '1' : '–'))) ?>
                                                    <?php if (!empty($t['beschreibung'])): ?><br>
                                                        <em><?= e($t['beschreibung']) ?></em><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ═══ 3. Revit- & Parameter-Mapping ═══ -->
            <section id="mapping" class="mb-4">
                <?php section_header('sec-mapping', 'bi-diagram-3', 'Revit- & Parameter-Mapping'); ?>
                <div class="collapse show" id="sec-mapping">
                    <p class="small text-body-secondary">Revit-Familie → DB-Element (aus <code>ELEMENT_MAPPING</code>).
                        <span class="badge text-bg-light border">prefix</span> = Name enthält Schlüssel ·
                        <span class="badge text-bg-light border">exact</span> = exakter Name ·
                        <span class="badge text-bg-light border">gruppe</span> = mehrere Familien = 1 Element ·
                        <span class="badge text-bg-light border">fallback</span> = nur intern referenziert.
                    </p>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm align-middle small">
                            <thead class="table-light">
                            <tr>
                                <th>Familie / Schlüssel</th>
                                <th>Match</th>
                                <th>Typ</th>
                                <th>Ziel-Element</th>
                                <th>Auswahl-Params</th>
                                <th>Varianten-Params</th>
                                <th>Besonderheit</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0;
                            foreach ($element_mapping as $key => $cfg):
                                $i++;
                                [$sum, $detail] = mapping_targets($cfg);
                                $matchColors = ['prefix' => 'info', 'exact' => 'primary', 'gruppe' => 'success', 'fallback' => 'secondary'];
                                $mc = $matchColors[$cfg['match'] ?? ''] ?? 'secondary';
                                $detailId = 'map-' . $i;
                                $besonderheit = [];
                                if (!empty($cfg['begleit_elemente'])) $besonderheit[] = '<span class="badge text-bg-warning" title="Begleit-Element(e): ' . e(implode(', ', $cfg['begleit_elemente'])) . '"><i class="bi bi-plus-circle me-1"></i>Begleiter</span>';
                                if (!empty($cfg['leitfamilien'])) $besonderheit[] = '<span class="badge text-bg-success-subtle text-success-emphasis border border-success-subtle">Leitfamilie</span>';
                                if (($cfg['match'] ?? '') === 'fallback') $besonderheit[] = '<span class="badge text-bg-secondary">nur intern</span>';
                                if (!empty($cfg['laenge_fallback'])) $besonderheit[] = '<span class="badge text-bg-light border" title="Längen-Fallback: ' . e($cfg['laenge_fallback']) . '">Längen-FB</span>';
                                ?>
                                <tr>
                                    <td class="fw-semibold text-break" style="max-width:22rem"><?= e($key) ?></td>
                                    <td><span class="badge text-bg-<?= $mc ?>"><?= e($cfg['match'] ?? '?') ?></span></td>
                                    <td><span class="text-body-secondary"><?= e($cfg['typ'] ?? '—') ?></span></td>
                                    <td>
                                        <?= $sum ?>
                                        <?php if ($detail): ?><a data-bs-toggle="collapse" href="#<?= $detailId ?>"
                                                                 class="text-decoration-none ms-1"
                                                                 title="Zuordnung anzeigen"><i
                                                        class="bi bi-chevron-expand"></i></a><?php endif; ?>
                                    </td>
                                    <td><?= params_badges($cfg['element_params'] ?? []) ?></td>
                                    <td><?= params_badges($cfg['variante_params'] ?? []) ?></td>
                                    <td><?= $besonderheit ? implode(' ', $besonderheit) : '<span class="text-body-tertiary">—</span>' ?></td>
                                </tr>
                                <?php if ($detail): ?>
                                <tr class="collapse" id="<?= $detailId ?>">
                                    <td colspan="7" class="bg-body-tertiary">
                                        <div class="d-flex flex-wrap gap-2 py-1">
                                            <?php foreach ($detail as $label => $code): ?>
                                                <span class="badge text-bg-light border fw-normal"><?= $label ?> → <code><?= e($code) ?></code></span>
                                            <?php endforeach; ?>
                                            <?php if (($cfg['typ'] ?? '') === 'gruppe' && !empty($cfg['familien'])): ?>
                                                <div class="w-100 small text-body-secondary mt-1">
                                                    <strong>Familien der Gruppe:</strong>
                                                    <ul class="mb-0"><?php foreach ($cfg['familien'] as $f): ?>
                                                            <li><?= e($f) ?><?= in_array($f, $cfg['leitfamilien'] ?? [], true) ? ' <span class="badge text-bg-success-subtle text-success-emphasis border border-success-subtle">Leit</span>' : '' ?></li><?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($cfg['begleit_elemente'])): ?>
                                                <div class="w-100 small text-warning-emphasis mt-1"><i
                                                            class="bi bi-plus-circle me-1"></i>Begleit-Element je Instanz:
                                                    <code><?= e(implode('</code>, <code>', $cfg['begleit_elemente'])) ?></code>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="h6 text-body-secondary mb-2"><i class="bi bi-sliders me-1"></i>Parameter-Mapping</h3>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle small">
                            <thead class="table-light">
                            <tr>
                                <th>Revit-Parameter</th>
                                <th>Bezeichnung</th>
                                <th class="text-center">DB-ID</th>
                                <th class="text-center">Einheit</th>
                                <th>Quell-Spalte</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($parameter_mapping as $revit => $p): ?>
                                <tr>
                                    <td><code><?= e($revit) ?></code></td>
                                    <td><?= e($p['bezeichnung'] ?? '') ?></td>
                                    <td class="text-center"><span
                                                class="badge text-bg-secondary"><?= e($p['id'] ?? '?') ?></span></td>
                                    <td class="text-center"><?= e($p['einheit'] ?? '') ?: '<span class="text-body-tertiary">–</span>' ?></td>
                                    <td><?= !empty($p['source_col']) ? '<code>' . e($p['source_col']) . '</code>' : '<span class="text-body-tertiary">–</span>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ═══ 4. Zeichenanweisungen ═══ -->
            <section id="zeichnung" class="mb-4">
                <?php section_header('sec-zeichnung', 'bi-pencil-square', 'Zeichenanweisungen'); ?>
                <div class="collapse show" id="sec-zeichnung">
                    <p class="small text-body-secondary">Platzierungs- und Möblierungsregeln fürs Modell. Frei
                        erweiterbar im Array <code>$zeichenanweisungen</code>.</p>
                    <div class="row g-3">
                        <?php foreach ($zeichenanweisungen as $z): ?>
                            <div class="col-12 col-xl-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <h3 class="h6 card-title mb-0"><i
                                                        class="bi bi-geo-alt text-primary me-1"></i><?= e($z['titel']) ?>
                                            </h3>
                                            <?= scope_badge($z['scope'] ?? 'allgemein', $z['gilt_fuer'] ?? '') ?>
                                        </div>
                                        <p class="card-text small mb-2"><?= $z['text'] ?></p>

                                        <?php if (($z['auto'] ?? '') === 'nicht_moebeliert'): ?>
                                            <div class="border-top pt-2 mt-2">
                                                <span class="text-uppercase text-body-tertiary fw-semibold"
                                                      style="font-size:.68rem">Automatisch abgeleitet (labormoebel = 0)</span>
                                                <div class="mt-1">
                                                    <?php if ($nicht_moebeliert): foreach ($nicht_moebeliert as $t): ?>
                                                        <span class="badge text-bg-light border me-1 mb-1 fw-normal"
                                                              title="<?= e($t['bezeichnung'] ?? '') ?>">
                                                            <?= e($t['id'] ?? '') ?> · <?= e($t['bezeichnung'] ?? '') ?>
                                                        </span>
                                                    <?php endforeach; else: ?>
                                                        <span class="text-body-tertiary small">— keine gefunden —</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <footer class="text-body-tertiary small border-top pt-3">
                Auto-Render aus <code>raumtypen.php</code> und <code>import_2_db_config.php</code>.
                Kuratierte Regeln in <code>definitionen.php</code>: <code>$import_logik</code>,
                <code>$raumtyp_regeln</code>, <code>$zeichenanweisungen</code>.
            </footer>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>