<?php
global $mysqli;
require_once "../roombook_nutzerlogin/_utils.php";

if (!function_exists('loadEnv')) {
    include "../roombook_nutzerlogin/db.php";
}
$role = init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_admin", "spargelfeld_view"]);

/* =====================================================================
 * KONFIGURATION
 * Herstellerangaben nur für interne RB-User, alle anderen: produktneutral
 * ===================================================================== */
$SHOW_HERSTELLER = (($role ?? '') === 'internal_rb_user');

// Schranktypen (produktneutral)
$TYPEN = [
    "B"  => ["kurz" => "Typ 90", "lang" => "Sicherheitsunterbau Typ 90 (brennbare Flüssigkeiten)", "css" => "typ-b"],
    "SL" => ["kurz" => "S/L",    "lang" => "Sicherheitsunterbau Säuren / Laugen (belüftet)",      "css" => "typ-sl"],
];

// Verfügbare Unterbaubreiten [mm] – herstellerübergreifend
// '*' = nicht bei allen Herstellern verfügbar
$BREITEN = [
    "B"  => ["590 / 600", "890", "1100", "1400*"],
    "SL" => ["450*", "600", "900", "1100*", "1200*", "1400*"],
];

// Mögliche Kombinationen je Abzugsbreite (Rastermaß)
// '*' = nicht bei allen Herstellern verfügbar
$KOMBIS = [
    1200 => ["max" => 2, "hinweis" => "",
        "kombis" => [
            [["B", 600], ["SL", 600]],
            [["B", 600], ["B", 600]],
            [["SL", 600], ["SL", 600]],
            [["B", 1100]],
            [["SL", "1100*"]],
        ]],
    1500 => ["max" => 2, "hinweis" => "",
        "kombis" => [
            [["B", 600], ["SL", 900]],
            [["B", 890], ["SL", 600]],
            [["B", 600], ["B", 890]],
            [["B", "1400*"]],
            [["SL", "1400*"]],
        ]],
    1800 => ["max" => 3, "hinweis" => "Ab 1800 mm sind drei Unterbauten möglich.",
        "kombis" => [
            [["B", 600], ["SL", 600], ["SL", 600]],
            [["B", 600], ["B", 600], ["SL", 600]],
            [["B", 1100], ["SL", 600]],
            [["B", 890], ["SL", 900]],
            [["B", 890], ["B", 890]],
            [["B", 600], ["SL", "1100*"]],
        ]],
    2000 => ["max" => 3, "hinweis" => "Breite nicht bei allen Herstellern erhältlich.",
        "kombis" => [
            [["B", 600], ["B", 600], ["SL", 600]],
            [["B", 890], ["SL", "1100*"]],
            [["B", "1400*"], ["SL", 600]],
        ]],
    2100 => ["max" => 3, "hinweis" => "",
        "kombis" => [
            [["B", 600], ["B", 600], ["SL", 900]],
            [["B", 890], ["B", 600], ["SL", 600]],
            [["B", 1100], ["SL", 900]],
            [["B", "1400*"], ["SL", 600]],
        ]],
    2400 => ["max" => 4, "hinweis" => "Nur bei einzelnen Abzugstypen (z. B. mit Seiteninstallation, begehbar).",
        "kombis" => [
            [["B", 600], ["B", 600], ["SL", 600], ["SL", 600]],
            [["B", 1100], ["SL", 600], ["SL", 600]],
            [["B", 1100], ["B", 1100]],
        ]],
];

// Herstellerquellen – nur für interne RB-User
// Zeile: [Produkt, Breiten, Seite (Text), PDF-Seite für Sprunglink oder null]
$HERSTELLER = [
    [
        "name"  => "Wesemann – DELTA 30 (2017)",
        "url"   => "https://www.wesemann.com/dokumente/Kataloge_Broschueren/DELTA-30-2017_s-.pdf",
        "pdf"   => true,
        "zeilen" => [
            ["Abzugsbreiten", "1200 / 1500 / 1800 / 2100", "S. 24–29, 32, 38–39", 24],
            ["Unterbau-Gefahrstoffschrank (unter Tischabzügen)", "600 / 890 / 1100 / 1400", "S. 130–131", 130],
            ["Säure-/Laugenschrank", "600", "S. 133", 133],
            ["Typ-90-Sicherheitsschrank", "600 / 900 / 1200", "S. 132–133", 132],
        ],
    ],
    [
        "name"  => "Waldner – Technischer Katalog SCALA",
        "url"   => "https://www.waldner.de/fileadmin/Produkte/Laboreinrichtung/Waldner_TechnischerKatalog_DE.pdf",
        "pdf"   => true,
        "zeilen" => [
            ["Tischabzug / Secuflow", "1200 / 1500 / 1800 / 2100", "S. 12–23", 12],
            ["Abzüge mit Seiteninstallation, begehbar, Tiefabzug", "bis 2400", "S. 24–41", 24],
            ["Eingeschobener Unterbau für Abzüge", "siehe XPRESS", "S. 144", 144],
            ["Sicherheitsunterbau Säuren/Laugen", "siehe XPRESS", "S. 161", 161],
            ["FWF 90-Sicherheitsunterbau für Abzüge", "siehe XPRESS", "S. 163", 163],
        ],
    ],
    [
        "name"  => "Waldner – SCALA XPRESS (06/2026)",
        "url"   => "https://www.waldner.de/fileadmin/Waldner_Relaunch/05_Lab_Space_Solutions/Leistungen/XPRESS_Laboreinrichtung/WaldnerRS2187__XPRESS_06_2026_DE_einzeln_web.pdf",
        "pdf"   => true,
        "zeilen" => [
            ["Tischabzüge", "1200 / 1500 / 1800", "S. 3–6", 3],
            ["Eingeschobener Unterbau für Abzüge", "545 / 600 / 845 / 900 / 1145 / 1200", "S. 48–49", 48],
            ["Sicherheitsunterbau Säuren/Laugen (für Abzüge)", "545 / 600 / 845 / 900", "S. 65–66", 65],
            ["FWF 90-Sicherheitsunterbau für Abzüge", "600 / 890 / 1100", "S. 67–68", 67],
        ],
    ],
    [
        "name"  => "Köttermann – EXPLORIS Abzüge",
        "url"   => "https://pdf.directindustry.com/pdf/koettermann-gmbh-co-kg/koettermann-fume-cupboards/104169-887777.html",
        "pdf"   => false,
        "zeilen" => [
            ["Tischabzug (hoch / niedrig), EcoPlus", "1200 / 1500 / 1800 / 2000 / 2100", "S. 6–11", null],
            ["Begehbarer Abzug / Destillationsabzug", "–", "S. 12–15", null],
            ["Unterbauten und Zubehör", "–", "S. 28", null],
        ],
    ],
    [
        "name"  => "Köttermann – Gefahrstoffschränke (Stand 08/2018)",
        "url"   => "https://pdf.directindustry.com/pdf/koettermann-gmbh-co-kg/koettermann-cabinets-hazardous-substances/104169-887753.html",
        "pdf"   => false,
        "zeilen" => [
            ["Sicherheitsunterbau brennbare Flüssigkeiten (H 710, T 574)", "590 / 890 / 1100 / 1400", "S. 6–7", null],
            ["Unterbau Säuren / Laugen, PP (H 720, T 515)", "450 / 600 / 900 / 1100 / 1200 / 1400", "S. 15–16", null],
            ["Unterbau Chemikalien (nicht brennbar, nicht ätzend)", "–", "S. 18–19", null],
        ],
    ],
];

function chip(array $teil, array $TYPEN): string
{
    [$typ, $breite] = $teil;
    $t = $TYPEN[$typ];
    $stern = str_contains((string)$breite, '*');
    $b = rtrim((string)$breite, '*');
    return '<span class="chip ' . $t["css"] . '" title="' . htmlspecialchars($t["lang"]) . '">'
        . htmlspecialchars($t["kurz"]) . ' ' . htmlspecialchars($b) . ($stern ? '<sup>*</sup>' : '') . '</span>';
}

function kombiJson(array $kombi): string
{
    $out = [];
    foreach ($kombi as [$typ, $breite]) {
        $out[] = ["typ" => $typ, "b" => (int)rtrim((string)$breite, '*')];
    }
    return htmlspecialchars(json_encode($out), ENT_QUOTES);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Info: Sicherheitsunterbauten unter Abzügen</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        .chip {
            display: inline-block;
            padding: .15rem .55rem;
            margin: .1rem .15rem;
            border-radius: .35rem;
            font-size: .85rem;
            font-weight: 600;
            white-space: nowrap;
            border: 1px solid rgba(0, 0, 0, .15);
        }

        .typ-b {
            background-color: #fde2c8;
        }

        /* brennbar */
        .typ-sl {
            background-color: #d9ecf7;
        }

        /* Säure/Lauge */

        .kombi-row td {
            vertical-align: middle;
        }

        .breite-cell {
            font-size: 1.2rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .max-badge {
            font-size: 1rem;
        }

        .kombi {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            padding: .15rem 0;
            border-bottom: 1px dashed #e3e3e3;
        }

        .kombi:last-child {
            border-bottom: none;
        }

        .show-kombi {
            color: #6c757d;
        }

        .show-kombi:hover {
            color: #0d6efd;
        }

        #kombiSvg svg {
            width: 100%;
            height: auto;
            max-height: 70vh;
        }
    </style>
</head>

<div id="limet-navbar"></div>
<?php require_once "../roombook_nutzerumfrage_sf/_utils.php"; ?>

<body>
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-inline-flex justify-content-center">
            <h1>Sicherheitsunterbauten unter Abzügen</h1>
        </div>
        <div class="card-body">

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Unter einem Abzug können Sicherheitsunterbauten für <strong>brennbare Flüssigkeiten (Typ 90)</strong>
                und für <strong>Säuren / Laugen</strong> eingebaut werden. Wie viele es sind, hängt von der
                <strong>Breite des Abzugs</strong> ab. Die Übersicht zeigt übliche Kombinationen. Die genaue
                Belegung wird in der Planung festgelegt.
                Mit <i class="fas fa-image"></i> können Sie eine Skizze jeder Kombination öffnen.
            </div>

            <!-- ===== Legende ===== -->
            <div class="card mb-3">
                <div class="card-header d-inline-flex justify-content-center">
                    <h4 class="mt-3">Unterbautypen und Breiten</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm mb-2">
                        <thead class="table-light">
                        <tr>
                            <th>Typ</th>
                            <th>Beschreibung</th>
                            <th>Übliche Breiten [mm]</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($TYPEN as $key => $t): ?>
                            <tr>
                                <td><span class="chip <?= $t["css"] ?>"><?= htmlspecialchars($t["kurz"]) ?></span></td>
                                <td><?= htmlspecialchars($t["lang"]) ?></td>
                                <td>
                                    <?= implode(" / ", array_map(
                                        fn($b) => str_replace('*', '<sup>*</sup>', htmlspecialchars($b)),
                                        $BREITEN[$key]
                                    )) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="small text-muted mb-0">
                        <sup>*</sup> nicht bei allen Herstellern erhältlich.
                        Bei Abzügen mit Seiteninstallation gibt es zum Teil etwas schmälere Varianten (z.&nbsp;B. 545
                        statt 600 mm bzw. 845 statt 900 mm).
                    </p>
                </div>
            </div>

            <!-- ===== Kombinationen je Abzugsbreite ===== -->
            <div class="card mb-3">
                <div class="card-header d-inline-flex justify-content-center">
                    <h4 class="mt-3">Mögliche Kombinationen je Abzugsbreite</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                            <tr>
                                <th style="width:12%">Abzugsbreite</th>
                                <th style="width:10%">max. Anzahl</th>
                                <th>Beispielkombinationen (Breiten in mm)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($KOMBIS as $breite => $k): ?>
                                <tr class="kombi-row">
                                    <td class="breite-cell"><?= $breite ?> mm</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary max-badge"><?= $k["max"] ?></span>
                                    </td>
                                    <td>
                                        <?php foreach ($k["kombis"] as $kombi): ?>
                                            <span class="kombi">
                                                <?php foreach ($kombi as $teil) echo chip($teil, $TYPEN); ?>
                                                <button type="button" class="btn btn-sm btn-link p-0 ms-2 show-kombi"
                                                        title="Skizze anzeigen"
                                                        data-breite="<?= $breite ?>"
                                                        data-parts="<?= kombiJson($kombi) ?>">
                                                    <i class="fas fa-image"></i>
                                                </button>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if ($k["hinweis"]): ?>
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-angle-right"></i> <?= htmlspecialchars($k["hinweis"]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="small mb-0">
                        <i class="fas fa-lightbulb text-warning"></i>
                        <strong>Faustregel:</strong> Unter Abzügen bis 1500 mm passen <strong>2</strong>
                        Sicherheitsunterbauten, ab 1800 mm <strong>3</strong>, bei 2400 mm bis zu
                        <strong>4</strong>. Die Kombinationen sind Richtwerte. Medienanschlüsse oder Abluftleitungen
                        können im Einzelfall Platz kosten.
                    </p>
                </div>
            </div>

            <?php if ($SHOW_HERSTELLER): ?>
                <!-- ===== Herstellerquellen (nur interne RB-User) ===== -->
                <div class="card border-warning">
                    <div class="card-header d-inline-flex justify-content-center align-items-center bg-warning-subtle">
                        <h4 class="mt-3">Quellen (Herstellerkataloge)</h4>
                        <span class="badge bg-warning text-dark ms-3">nur intern sichtbar</span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($HERSTELLER as $h): ?>
                            <h6 class="mt-2">
                                <a href="<?= htmlspecialchars($h["url"]) ?>" target="_blank" rel="noopener">
                                    <i class="fas fa-file-pdf text-danger"></i> <?= htmlspecialchars($h["name"]) ?>
                                </a>
                            </h6>
                            <table class="table table-bordered table-sm mb-3">
                                <thead class="table-light">
                                <tr>
                                    <th>Produkt</th>
                                    <th>Breiten [mm]</th>
                                    <th style="width:18%">Seite</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($h["zeilen"] as [$p, $b, $s, $pg]): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p) ?></td>
                                        <td><?= htmlspecialchars($b) ?></td>
                                        <td>
                                            <?php if ($h["pdf"] && $pg): ?>
                                                <a href="<?= htmlspecialchars($h["url"]) ?>#page=<?= (int)$pg ?>"
                                                   target="_blank" rel="noopener" title="Katalog auf dieser Seite öffnen">
                                                    <?= htmlspecialchars($s) ?> <i class="fas fa-external-link-alt small"></i>
                                                </a>
                                            <?php else: ?>
                                                <?= htmlspecialchars($s) ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                        <p class="small text-muted mb-0">
                            Kein Katalog nennt direkt, wie viele Unterbauten unter welche Abzugsbreite passen. Die
                            Kombinationen oben sind aus den angegebenen Breiten abgeleitet.
                            Die Seitenlinks springen auf die PDF-Seite; diese kann von der gedruckten Seitenzahl um
                            einige Seiten abweichen.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ===== Modal: Skizze ===== -->
<div class="modal fade" id="kombiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kombiTitle">Skizze</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                <div id="kombiSvg"></div>
                <p class="small text-muted mt-2 mb-0">
                    Schematische Ansicht von vorne, Breiten maßstäblich. Höhen, Türen und Details sind vereinfacht.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    const TYPEN_JS = {
        B: {label: "Typ 90", sub: "brennbar", fill: "#fde2c8", stroke: "#c77a2e"},
        SL: {label: "S/L", sub: "Säure/Lauge", fill: "#d9ecf7", stroke: "#3c7fae"}
    };

    function drawKombi(W, parts) {
        const pad = 160, topY = 120, wtY = 1700, wtH = 45, floorY = 2600, plinth = 100;
        const cabTop = wtY + wtH, cabBottom = floorY - plinth;
        const vbW = W + 2 * pad, vbH = 2850;
        let s = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${vbW} ${vbH}" font-family="sans-serif">`;

        s += `<defs><pattern id="hatch" width="60" height="60" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
                <line x1="0" y1="0" x2="0" y2="60" stroke="#bbb" stroke-width="10"/></pattern></defs>`;

        // Boden
        s += `<line x1="0" y1="${floorY}" x2="${vbW}" y2="${floorY}" stroke="#444" stroke-width="10"/>`;

        // Abzug (oberer Teil)
        s += `<rect x="${pad}" y="${topY}" width="${W}" height="${wtY - topY}" fill="#f1f3f5" stroke="#555" stroke-width="14" rx="10"/>`;
        // Frontschieber (Glas)
        s += `<rect x="${pad + 70}" y="${topY + 380}" width="${W - 140}" height="${wtY - topY - 440}" fill="#dbeefa" fill-opacity="0.7" stroke="#7aa6c2" stroke-width="10"/>`;
        // Traverse / Bedienteil
        s += `<rect x="${pad + 70}" y="${topY + 120}" width="${W - 140}" height="180" fill="#e2e6ea" stroke="#999" stroke-width="6"/>`;
        s += `<text x="${pad + W / 2}" y="${topY + 240}" font-size="80" text-anchor="middle" fill="#666">Abzug ${W} mm</text>`;

        // Arbeitsplatte
        s += `<rect x="${pad - 20}" y="${wtY}" width="${W + 40}" height="${wtH}" fill="#777"/>`;

        // Unterbauten
        let x = pad, sum = 0;
        parts.forEach(p => {
            const t = TYPEN_JS[p.typ];
            const w = p.b;
            s += `<rect x="${x + 4}" y="${cabTop}" width="${w - 8}" height="${cabBottom - cabTop}" fill="${t.fill}" stroke="${t.stroke}" stroke-width="10"/>`;
            // Türfuge bei breiten Schränken
            if (w >= 850) {
                s += `<line x1="${x + w / 2}" y1="${cabTop + 30}" x2="${x + w / 2}" y2="${cabBottom - 30}" stroke="${t.stroke}" stroke-width="6"/>`;
            }
            // Sockel
            s += `<rect x="${x + 4}" y="${cabBottom}" width="${w - 8}" height="${plinth}" fill="#555"/>`;
            // Beschriftung
            const cx = x + w / 2, cy = (cabTop + cabBottom) / 2;
            s += `<text x="${cx}" y="${cy - 40}" font-size="${w < 500 ? 60 : 85}" font-weight="bold" text-anchor="middle" fill="#333">${t.label}</text>`;
            s += `<text x="${cx}" y="${cy + 50}" font-size="${w < 500 ? 50 : 65}" text-anchor="middle" fill="#333">${t.sub}</text>`;
            s += `<text x="${cx}" y="${cy + 140}" font-size="${w < 500 ? 50 : 65}" text-anchor="middle" fill="#333">${w} mm</text>`;
            x += w;
            sum += w;
        });

        // Restbreite / Blende
        const rest = W - sum;
        if (rest > 0) {
            s += `<rect x="${x + 4}" y="${cabTop}" width="${rest - 8}" height="${floorY - cabTop}" fill="url(#hatch)" stroke="#bbb" stroke-width="6"/>`;
            if (rest >= 150) {
                s += `<text x="${x + rest / 2}" y="${(cabTop + cabBottom) / 2}" font-size="55" text-anchor="middle" fill="#777">Rest</text>`;
                s += `<text x="${x + rest / 2}" y="${(cabTop + cabBottom) / 2 + 70}" font-size="55" text-anchor="middle" fill="#777">${rest} mm</text>`;
            }
        }

        // Bemaßung Gesamtbreite
        const dimY = floorY + 140;
        s += `<line x1="${pad}" y1="${dimY}" x2="${pad + W}" y2="${dimY}" stroke="#333" stroke-width="6"/>`;
        s += `<line x1="${pad}" y1="${dimY - 40}" x2="${pad}" y2="${dimY + 40}" stroke="#333" stroke-width="6"/>`;
        s += `<line x1="${pad + W}" y1="${dimY - 40}" x2="${pad + W}" y2="${dimY + 40}" stroke="#333" stroke-width="6"/>`;
        s += `<text x="${pad + W / 2}" y="${dimY + 100}" font-size="75" text-anchor="middle" fill="#333">${W} mm</text>`;

        s += `</svg>`;
        return s;
    }

    $(function () {
        const modal = new bootstrap.Modal(document.getElementById('kombiModal'));
        $(document).on('click', '.show-kombi', function () {
            const W = parseInt($(this).data('breite'), 10);
            const parts = $(this).data('parts');   // jQuery parst JSON automatisch
            const txt = parts.map(p => TYPEN_JS[p.typ].label + ' ' + p.b).join(' + ');
            $('#kombiTitle').text('Abzug ' + W + ' mm: ' + txt);
            $('#kombiSvg').html(drawKombi(W, parts));
            modal.show();
        });
    });
</script>
</body>
</html>