<?php
global $mysqli;
require_once "../roombook_nutzerlogin/_utils.php";

if (!function_exists('loadEnv')) {
    include "../roombook_nutzerlogin/db.php";
}
$role = init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_admin", "spargelfeld_view"]);

// Gemeinsame Auswahloptionen für Filterklassen (Formular + Infoseite)

$FILTERKLASSEN_STAUB = [
    "G3_G4"     => ["label" => "G3 / G4",   "iso" => "ISO Coarse",            "info" => "Grobstaub, z. B. Vorfilter, Küchenabluft, Schutz von Lüftungsgeräten"],
    "M5"        => ["label" => "M5",        "iso" => "ePM10 ≥ 50 %",          "info" => "Feinstaub, geringe Anforderungen (Werkstatt, Lager)"],
    "M6"        => ["label" => "M6",        "iso" => "ePM2,5 ≥ 50 %",         "info" => "Feinstaub, Vor- und Umluftfilter"],
    "F7"        => ["label" => "F7",        "iso" => "ePM1 50 – 65 %",        "info" => "Feinstaub, Endfilter für Büros und Produktionsräume"],
    "F8_F9"     => ["label" => "F8 / F9",   "iso" => "ePM1 ≥ 65 %",           "info" => "Feinstaub fein, Endfilter Labor/Krankenhaus, Vorfilter für HEPA"],
    "unbekannt" => ["label" => "unbekannt", "iso" => "",                      "info" => "Klasse nicht bekannt"],
];

$FILTERKLASSEN_HEPA = [
    "E10_E12"   => ["label" => "E10 – E12 (EPA)", "iso" => "≥ 85 – 99,5 %",    "info" => "Kein echter HEPA. Oft als „HEPA H10–H12“ verkauft"],
    "H13"       => ["label" => "H13 (HEPA)",      "iso" => "≥ 99,95 %",        "info" => "Reinraum ISO 7/8, OP, Sicherheitswerkbank, Abluft BSL/Radiochemie"],
    "H14"       => ["label" => "H14 (HEPA)",      "iso" => "≥ 99,995 %",       "info" => "Reinraum ISO 5, GMP A/B"],
    "U15plus"   => ["label" => "U15 – U17 (ULPA)","iso" => "≥ 99,9995 %",      "info" => "Reinraum ISO 4 und besser (Halbleiter, Isolatoren)"],
    "unbekannt" => ["label" => "unbekannt",       "iso" => "",                 "info" => "Klasse nicht bekannt"],
];

// Für das Formular: nur key => label
function filterklassen_select_options(array $list): array {
    return array_map(fn($o) => $o["label"], $list);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Info: Filterklassen</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        .filter-table td, .filter-table th {
            vertical-align: top;
        }

        .filter-table ul {
            margin-bottom: 0;
            padding-left: 1.1rem;
        }

        .klasse {
            white-space: nowrap;
            font-weight: 600;
        }

        .grp-grob {
            background-color: #f6f1e7 !important;
        }

        .grp-fein {
            background-color: #eaf2f8 !important;
        }

        .grp-schweb {
            background-color: #eef6ee !important;
        }
    </style>
</head>

<div id="limet-navbar"></div>
<?php require_once "../roombook_nutzerumfrage_sf/_utils.php"; ?>

<body>
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-inline-flex justify-content-center">
            <h1>Filterklassen – Übersicht</h1>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Die Filterklasse steht meist <strong>auf dem Filterrahmen, dem Typenschild des Geräts</strong>
                oder im Datenblatt / Wartungsprotokoll. Wenn Sie sie nicht kennen, geben Sie bitte
                <em>„unbekannt“</em> an – eine grobe Einschätzung anhand der Anwendung unten hilft uns trotzdem weiter.
            </div>

            <!-- ===== Auswahlmöglichkeiten im Fragebogen ===== -->
            <div class="card mb-3">
                <div class="card-header d-inline-flex justify-content-center">
                    <h4 class="mt-3">Auswahlmöglichkeiten im Fragebogen</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-lg-6">
                            <h5><span class="badge grp-fein text-dark border">Staubfilter</span></h5>
                            <p class="small text-muted mb-2">
                                Für Grob- und Feinstaub (Partikel &gt; 1 µm). Wählbar bei
                                <em>„Besondere Anforderungen an die Raumabluft?“ → Staubfilter</em>.
                            </p>
                            <table class="table table-bordered table-sm filter-table">
                                <thead class="table-light">
                                <tr>
                                    <th>Option</th>
                                    <th>ISO 16890 (ca.)</th>
                                    <th>Wofür typisch</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($FILTERKLASSEN_STAUB as $o): ?>
                                    <tr>
                                        <td class="klasse"><?= htmlspecialchars($o["label"]) ?></td>
                                        <td><?= htmlspecialchars($o["iso"]) ?></td>
                                        <td><?= htmlspecialchars($o["info"]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-lg-6">
                            <h5><span class="badge grp-schweb text-dark border">HEPA-Filter</span></h5>
                            <p class="small text-muted mb-2">
                                Für Schwebstoffe, Keime und Aerosole (Partikel &lt; 1 µm). Wählbar bei Raumabluft
                                <em>→ HEPA Filter</em> und bei <em>„Filterung der Zuluft erforderlich?“ → HEPA
                                    Filter</em>.
                            </p>
                            <table class="table table-bordered table-sm filter-table">
                                <thead class="table-light">
                                <tr>
                                    <th>Option</th>
                                    <th>Abscheidegrad (MPPS)</th>
                                    <th>Wofür typisch</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($FILTERKLASSEN_HEPA as $o): ?>
                                    <tr>
                                        <td class="klasse"><?= htmlspecialchars($o["label"]) ?></td>
                                        <td><?= htmlspecialchars($o["iso"]) ?></td>
                                        <td><?= htmlspecialchars($o["info"]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <p class="small mb-0">
                        <i class="fas fa-lightbulb text-warning"></i>
                        <strong>Faustregel:</strong> Steht auf dem Filter G, M oder F, ist es ein
                        <strong>Staubfilter</strong>.
                        Steht dort E, H oder U, ist es ein <strong>Schwebstofffilter</strong>; „HEPA“ im eigentlichen
                        Sinn sind nur H13 und H14.
                    </p>
                </div>
            </div>

            <!-- ===== Übersicht nach Filterart ===== -->
            <div class="card mb-3">
                <div class="card-header d-inline-flex justify-content-center">
                    <h4 class="mt-3">Übersicht nach Filterart</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm filter-table">
                            <thead class="table-light">
                            <tr>
                                <th style="width:16%">Filterart / Partikelgröße</th>
                                <th style="width:20%">Typische Partikel</th>
                                <th style="width:9%">Klasse</th>
                                <th>Anwendungsbeispiele</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Grobstaub -->
                            <tr class="grp-grob">
                                <td rowspan="2"><strong>Grobstaubfilter</strong><br>Partikel &gt; 10 µm</td>
                                <td rowspan="2">
                                    <ul>
                                        <li>Insekten</li>
                                        <li>Textilfasern, Haare</li>
                                        <li>Sand, Flugasche</li>
                                        <li>Blütenstaub, Pollen, Sporen</li>
                                        <li>Zementstaub</li>
                                    </ul>
                                </td>
                                <td class="klasse">G1 – G2</td>
                                <td>
                                    <ul>
                                        <li>Einfache Anwendungen, z.&nbsp;B. Insektenschutz in Kompaktgeräten</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="grp-grob">
                                <td class="klasse">G3 – G4</td>
                                <td>
                                    <ul>
                                        <li>Vor- und Umluftfilter für Zivilschutzanlagen</li>
                                        <li>Abluft Farbspritzkabinen, Küchenabluft</li>
                                        <li>Verschmutzungsschutz für Klima- und Kompaktgeräte (z.&nbsp;B.
                                            Fensterklimageräte, Ventilatoren)
                                        </li>
                                        <li>Vorfilter für M6 – F8</li>
                                    </ul>
                                </td>
                            </tr>

                            <!-- Feinstaub -->
                            <tr class="grp-fein">
                                <td rowspan="3"><strong>Feinstaubfilter</strong><br>Partikel 1 – 10 µm</td>
                                <td rowspan="2">
                                    <ul>
                                        <li>Pollen, Sporen</li>
                                        <li>Zementstaub, Flugasche</li>
                                        <li>Keime, Bakterien</li>
                                    </ul>
                                </td>
                                <td class="klasse">M5</td>
                                <td>
                                    <ul>
                                        <li>Außenluftfilter für Räume mit geringen Anforderungen (Werkhallen, Lager,
                                            Garagen)
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="grp-fein">
                                <td class="klasse">M6 – F7</td>
                                <td>
                                    <ul>
                                        <li>Vor- und Umluftfiltrierung in Lüftungszentralen</li>
                                        <li>Endfilter in Klimaanlagen für Verkaufsräume, Büros, gewisse
                                            Produktionsräume
                                        </li>
                                        <li>Vorfilter für F9 – E11</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="grp-fein">
                                <td>
                                    <ul>
                                        <li>Ölrauch, agglomerierter Ruß</li>
                                        <li>Tabakrauch</li>
                                        <li>Metalloxidrauch</li>
                                    </ul>
                                </td>
                                <td class="klasse">F7 – F9</td>
                                <td>
                                    <ul>
                                        <li>Endfilter in Klimaanlagen für Büros, Produktionsräume, Schaltzentralen,
                                            Krankenhäuser, EDV-Räume
                                        </li>
                                        <li>Vorfilter für E11 – H13 sowie für Aktivkohle</li>
                                    </ul>
                                </td>
                            </tr>

                            <!-- Schwebstoff -->
                            <tr class="grp-schweb">
                                <td rowspan="3"><strong>Schwebstofffilter</strong><br>(EPA / HEPA / ULPA)<br>Partikel
                                    &lt; 1 µm
                                </td>
                                <td>
                                    <ul>
                                        <li>Keime, Bakterien, Viren</li>
                                        <li>Tabak- und Metalloxidrauch</li>
                                    </ul>
                                </td>
                                <td class="klasse">E10 – E12<br><small class="text-muted">EPA</small></td>
                                <td>
                                    <ul>
                                        <li>Endfilter für Räume mit hohen Anforderungen (Labore, Lebensmittel-, Pharma-,
                                            Optik- und Elektronikproduktion, Medizin)
                                        </li>
                                        <li>E11: Endfilter für Reinräume (ca. ISO 8)</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="grp-schweb">
                                <td>
                                    <ul>
                                        <li>Öldunst, Ruß im Entstehungszustand</li>
                                        <li>Radioaktive Schwebstoffe</li>
                                    </ul>
                                </td>
                                <td class="klasse">H13<br><small class="text-muted">HEPA</small></td>
                                <td>
                                    <ul>
                                        <li>Endfilter für Reinräume ISO 7 / ISO 8, OP-Säle, mikrobiologische
                                            Sicherheitswerkbänke
                                        </li>
                                        <li>Endfilter in Zivilschutzanlagen, Abluftfilter in kerntechnischen Anlagen
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="grp-schweb">
                                <td>
                                    <ul>
                                        <li>Aerosole</li>
                                    </ul>
                                </td>
                                <td class="klasse">H14<br><small class="text-muted">HEPA</small><br>U15 – U17<br><small
                                            class="text-muted">ULPA</small></td>
                                <td>
                                    <ul>
                                        <li>H14: Reinräume ISO 5, GMP-Klasse A/B</li>
                                        <li>U15 – U17: Reinräume ISO 4 und besser (Halbleiter, Isolatoren)</li>
                                    </ul>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0">
                        Hinweis: Die Bezeichnungen G/M/F stammen aus der EN 779, die 2018 durch die ISO 16890 ersetzt
                        wurde (Klassen ISO Coarse, ePM10, ePM2,5, ePM1). Auf älteren Anlagen und Filtern findet man aber
                        meist noch G/M/F.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-inline-flex justify-content-center">
                    <h4 class="mt-3">Schwebstofffilter nach EN 1822 im Detail</h4>
                </div>
                <div class="card-body">
                    <p>
                        Schwebstofffilter werden an der <strong>MPPS</strong> (Most Penetrating Particle Size) geprüft,
                        also an der Partikelgröße, die der Filter am schlechtesten zurückhält (typisch 0,1 – 0,3 µm).
                        Jede Klasse höher lässt <strong>rund zehnmal weniger</strong> Partikel durch.
                        <strong>HEPA beginnt erst bei H13</strong>: Ein als „HEPA H12“ bezeichneter Filter ist nach
                        EN 1822 ein EPA-Filter (E12).
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-striped mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Klasse (EN 1822)</th>
                                <th>Abscheidegrad MPPS</th>
                                <th>ISO 29463</th>
                                <th>Typische Anwendung</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="klasse">E10 (EPA)</td>
                                <td>≥ 85 %</td>
                                <td>–</td>
                                <td>Allgemeine Raumlufttechnik, Vorfilter für Reinräume</td>
                            </tr>
                            <tr>
                                <td class="klasse">E11 (EPA)</td>
                                <td>≥ 95 %</td>
                                <td>ISO 15 E</td>
                                <td>Hochwertige Gebäudetechnik, Lebensmittelindustrie, Serverräume</td>
                            </tr>
                            <tr>
                                <td class="klasse">E12 (EPA)</td>
                                <td>≥ 99,5 %</td>
                                <td>ISO 25 E</td>
                                <td>Sensible Vorfiltration, Krankenhausräume</td>
                            </tr>
                            <tr class="table-success">
                                <td class="klasse">H13 (HEPA)</td>
                                <td>≥ 99,95 %</td>
                                <td>ISO 35 H</td>
                                <td>ISO 7 / ISO 8, OP-Säle, Sicherheitswerkbänke</td>
                            </tr>
                            <tr class="table-success">
                                <td class="klasse">H14 (HEPA)</td>
                                <td>≥ 99,995 %</td>
                                <td>ISO 45 H</td>
                                <td>ISO 5, GMP-Klasse A/B</td>
                            </tr>
                            <tr>
                                <td class="klasse">U15 (ULPA)</td>
                                <td>≥ 99,9995 %</td>
                                <td>ISO 55 U</td>
                                <td>ISO 4 / ISO 3, Halbleiterfertigung</td>
                            </tr>
                            <tr>
                                <td class="klasse">U16 (ULPA)</td>
                                <td>≥ 99,99995 %</td>
                                <td>ISO 65 U</td>
                                <td>ISO 2 / ISO 1, Isolatoren</td>
                            </tr>
                            <tr>
                                <td class="klasse">U17 (ULPA)</td>
                                <td>≥ 99,999995 %</td>
                                <td>ISO 75 U</td>
                                <td>Höchste Reinheitsanforderungen</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>