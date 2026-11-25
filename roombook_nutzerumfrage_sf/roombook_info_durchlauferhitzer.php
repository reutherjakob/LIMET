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

// Physik: Q [l/min] = P [kW] * 14,33 / Δt [K]
const DLE_FAKTOR = 14.33;

// Bezugswerte für die allgemeine Aussage
const T_KALT_REF = 10;   // °C Kaltwasser
const T_WARM_REF = 40;   // °C Auslauf

// Nur Geräte in diesem Leistungsbereich werden angeführt
const KW_MIN = 10;
const KW_MAX = 15;

/* ---------------------------------------------------------------------
 * Produkte
 * status: "bestaetigt" = unter Laborspüle verbaut (Projektangabe / Referenz)
 *         "unsicher"   = Untertisch-tauglich, Einsatz unter Laborspüle nicht belegt
 * lpm/dt = Herstellerangabe Warmwasserleistung bei Temperaturerhöhung dt
 * max    = max. Durchfluss / Begrenzer [l/min] (null = keine Angabe)
 * tmin/tmax = einstellbarer Auslauf-Temperaturbereich
 * --------------------------------------------------------------------- */
$PRODUKTE = [
    // ===== bestätigt =====
    ["status" => "bestaetigt", "hersteller" => "Clage", "modell" => "DCX 13 Next", "kw" => "11",
        "lpm" => 5.6, "dt" => 28, "max" => 5.0, "tmin" => 35, "tmax" => 55, "absich" => "16 A",
        "einsatz" => "unter Laborspüle durch Labormöbelhersteller verbaut (Projektangabe)",
        "url" => "https://heinze.de/m2/80/61680/etc/90/48103490.pdf", "quelle" => "Produktinformation S. 2"],
    ["status" => "bestaetigt", "hersteller" => "Clage", "modell" => "DCX 13 Next", "kw" => "13.5",
        "lpm" => 6.9, "dt" => 28, "max" => 5.0, "tmin" => 35, "tmax" => 55, "absich" => "20 A",
        "einsatz" => "unter Laborspüle durch Labormöbelhersteller verbaut (Projektangabe)",
        "url" => "https://heinze.de/m2/80/61680/etc/90/48103490.pdf", "quelle" => "Produktinformation S. 2"],
    ["status" => "bestaetigt", "hersteller" => "AEG", "modell" => "DDLE Kompakt 11/13", "kw" => "11",
        "lpm" => 3.7, "dt" => 43, "max" => 4.0, "tmin" => 20, "tmax" => 60, "absich" => "16 A",
        "einsatz" => "unter Laborspülen verbaut (u. a. TU Braunschweig, Projektangabe)",
        "url" => "https://www.wolf-online-shop.de/media/products/814001988_DB_d.pdf", "quelle" => "Datenblatt"],
    ["status" => "bestaetigt", "hersteller" => "AEG", "modell" => "DDLE Kompakt 11/13", "kw" => "13.5",
        "lpm" => 4.5, "dt" => 43, "max" => 4.0, "tmin" => 20, "tmax" => 60, "absich" => "20 A",
        "einsatz" => "unter Laborspülen verbaut (u. a. TU Braunschweig, Projektangabe)",
        "url" => "https://www.wolf-online-shop.de/media/products/814001988_DB_d.pdf", "quelle" => "Datenblatt"],

    // ===== unsicher =====
    ["status" => "unsicher", "hersteller" => "Clage", "modell" => "CEX-U", "kw" => "11",
        "lpm" => 4.8, "dt" => 33, "max" => 5.0, "tmin" => 20, "tmax" => 60, "absich" => "16 A",
        "einsatz" => "Untertischgerät für Küchenspüle, Laboreinsatz nicht belegt",
        "url" => "https://media.hornbach.lu/hb/eek_productdatasheet/as.47396275.pdf", "quelle" => "Produktinformation S. 2"],
    ["status" => "unsicher", "hersteller" => "Clage", "modell" => "CEX-U", "kw" => "13.5",
        "lpm" => 5.8, "dt" => 33, "max" => 5.0, "tmin" => 20, "tmax" => 60, "absich" => "20 A",
        "einsatz" => "Untertischgerät für Küchenspüle, Laboreinsatz nicht belegt",
        "url" => "https://media.hornbach.lu/hb/eek_productdatasheet/as.47396275.pdf", "quelle" => "Produktinformation S. 2"],
    ["status" => "unsicher", "hersteller" => "Stiebel Eltron", "modell" => "DCE 11/13", "kw" => "11",
        "lpm" => 3.7, "dt" => 43, "max" => 4.0, "tmin" => 20, "tmax" => 60, "absich" => "16 A",
        "einsatz" => "Schwestermodell des AEG DDLE Kompakt (gleiche Daten), Laboreinsatz selbst nicht belegt",
        "url" => "https://www.stiebel-eltron.de/de/fachkunden/produkte/230770.techdata.pdf", "quelle" => "Datenblatt"],
    ["status" => "unsicher", "hersteller" => "Stiebel Eltron", "modell" => "DCE 11/13", "kw" => "13.5",
        "lpm" => 4.5, "dt" => 43, "max" => 4.0, "tmin" => 20, "tmax" => 60, "absich" => "20 A",
        "einsatz" => "Schwestermodell des AEG DDLE Kompakt (gleiche Daten), Laboreinsatz selbst nicht belegt",
        "url" => "https://www.stiebel-eltron.de/de/fachkunden/produkte/230770.techdata.pdf", "quelle" => "Datenblatt"],
    ["status" => "unsicher", "hersteller" => "Vaillant", "modell" => "electronicVED lite U", "kw" => "11",
        "lpm" => 4.0, "dt" => 30, "max" => null, "tmin" => 20, "tmax" => 60, "absich" => "16 A",
        "einsatz" => "Untertischgerät für Küchenspüle, Laboreinsatz nicht belegt; Leistungswert gilt für 11–13,5 kW",
        "url" => "https://documents.unidomo.de/de/vadb0010044428_datenblatt.pdf", "quelle" => "Datenblatt"],
];

// Leistungsfilter: nur KW_MIN – KW_MAX anführen
$PRODUKTE = array_values(array_filter(
    $PRODUKTE,
    fn($p) => (float)$p["kw"] >= KW_MIN && (float)$p["kw"] <= KW_MAX
));
// Produktnummern je Modell (Reihenfolge wie in $PRODUKTE) – für neutrale Darstellung
$NR = [];
foreach ($PRODUKTE as $i => $p) {
    $key = $p["hersteller"] . "|" . $p["modell"];
    if (!isset($NR[$key])) $NR[$key] = count($NR) + 1;
    $PRODUKTE[$i]["nr"] = $NR[$key];
}
/* ---------------------------------------------------------------------
 * Auswertung
 * --------------------------------------------------------------------- */
function peff(array $p): float            // effektive Leistung aus Herstellerangabe
{
    return $p["lpm"] * $p["dt"] / DLE_FAKTOR;
}

function lpmBei(array $p, float $tKalt, float $tWarm): ?float
{
    $dt = $tWarm - $tKalt;
    if ($dt <= 0 || $tWarm > $p["tmax"]) return null;
    $q = peff($p) * DLE_FAKTOR / $dt;
    if ($p["max"] !== null) $q = min($q, $p["max"]);
    return floor($q * 10) / 10;
}

// Minimum je Leistungsstufe; $nurBestaetigt steuert, welche Produkte zählen
function minimum(array $produkte, bool $nurBestaetigt): array
{
    $min = [];
    foreach ($produkte as $p) {
        if ($nurBestaetigt && $p["status"] !== "bestaetigt") continue;
        $q = lpmBei($p, T_KALT_REF, T_WARM_REF);
        if ($q === null) continue;
        $k = $p["kw"];
        if (!isset($min[$k])) $min[$k] = ["wert" => $q, "n" => 0, "hersteller" => [], "absich" => $p["absich"], "tmax" => $p["tmax"]];
        $min[$k]["wert"] = min($min[$k]["wert"], $q);
        $min[$k]["tmax"] = min($min[$k]["tmax"], $p["tmax"]);
        $min[$k]["hersteller"][$p["hersteller"]] = true;
        $min[$k]["n"]++;
    }
    ksort($min, SORT_NUMERIC);
    return $min;
}

$MIN_BEST = minimum($PRODUKTE, true);
$MIN_ALLE = minimum($PRODUKTE, false);

function kwFmt(string $kw): string
{
    return str_replace('.', ',', $kw) . " kW";
}

function lpmFmt(float $v): string
{
    return number_format($v, 1, ',', '') . " l/min";
}

 $chartData = [];
foreach ($PRODUKTE as $p) {
    if (!$SHOW_HERSTELLER && $p["status"] !== "bestaetigt") continue;
    $chartData[] = [
        "label"  => "Produkt " . $p["nr"] . " – " . kwFmt($p["kw"]),
        "peff"   => round(peff($p), 3),
        "max"    => $p["max"],
        "tmax"   => $p["tmax"],
        "status" => $p["status"],
        "kw"     => (float)$p["kw"],
    ];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Info: Durchlauferhitzer unter Laborspülen</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        .wert-gross {
            font-size: 1.25rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .is-min {
            background-color: #d1e7dd !important;
            font-weight: 700;
        }

        .nowrap {
            white-space: nowrap;
        }

        .chart-box {
            position: relative;
            height: 420px;
        }
    </style>
</head>

<div id="limet-navbar"></div>
<?php require_once "../roombook_nutzerumfrage_sf/_utils.php"; ?>

<body>
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-inline-flex justify-content-center">
            <h1>Durchlauferhitzer unter Laborspülen</h1>
        </div>
        <div class="card-body">

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Berücksichtigt werden nur Durchlauferhitzer (<?= KW_MIN ?>–<?= KW_MAX ?> kW), die im
                <strong>Unterbau von Laborspülen</strong> eingebaut werden. Die Werte sind <strong>Mindestwerte</strong>
                (niedrigster Wert der vergleichbaren Geräte), umgerechnet auf
                <?= T_KALT_REF ?> °C Kaltwasser → <?= T_WARM_REF ?> °C Warmwasser.
                <br>Bitte prüfen Sie, ob diese Warmwassermenge und Temperatur für Ihre Spüle ausreichen.
            </div>

            <!-- ===== Allgemeine Aussage (nur bestätigte Geräte) ===== -->
            <div class="card mb-3" style="border-left: 6px solid #0d6efd;">
                <div class="card-header">
                    <h4 class="mt-2 mb-1">Durchlauferhitzer im Unterbau der Laborspüle</h4>
                    <div class="small text-muted">400 V, 3-phasig (3/PE), Festanschluss · elektronisch geregelt</div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm mb-2">
                        <thead class="table-light">
                        <tr>
                            <th>Leistung</th>
                            <th>Absicherung</th>
                            <th>Mindest-Warmwassermenge</th>
                            <th>bei</th>
                            <th>max. Auslauftemperatur</th>
                            <th>Basis</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($MIN_BEST as $kw => $m):
                            $nH = count($m["hersteller"]); ?>
                            <tr>
                                <td class="wert-gross"><?= kwFmt((string)$kw) ?></td>
                                <td><?= htmlspecialchars($m["absich"]) ?></td>
                                <td class="wert-gross text-success"><?= lpmFmt($m["wert"]) ?></td>
                                <td><?= T_WARM_REF ?> °C (Kaltwasser <?= T_KALT_REF ?> °C)</td>
                                <td>mind. <?= $m["tmax"] ?> °C</td>
                                <td class="small">
                                    <?= $nH ?> Hersteller
                                    <?php if ($nH < 3): ?>
                                        <i class="fas fa-exclamation-triangle text-warning"
                                           title="weniger als 3 vergleichbare Hersteller – Wert vorläufig"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="small mb-0">
                        <i class="fas fa-lightbulb text-warning"></i>
                        Beide Leistungsstufen liefern dieselbe Mindestmenge, weil die Geräte den Durchfluss begrenzen.
                        Die höhere Leistung bringt bei gleicher Menge eine höhere Temperatur bzw. bei kaltem Zulauf
                        im Winter mehr Reserve.
                    </p>
                </div>
            </div>

            <!-- ===== Diagramm ===== -->
            <div class="card mb-3">
                <div class="card-header d-inline-flex justify-content-center">
                    <h4 class="mt-3">Warmwassermenge über Auslauftemperatur</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end mb-2">
                        <div class="col-md-3">
                            <label class="form-label" for="tKalt">Kaltwassertemperatur [°C]</label>
                            <input type="number" class="form-control" id="tKalt" value="<?= T_KALT_REF ?>" min="2"
                                   max="25">
                        </div>
                        <?php if ($SHOW_HERSTELLER): ?>
                            <div class="col-md-5">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="zeigeUnsicher">
                                    <label class="form-check-label" for="zeigeUnsicher">nicht bestätigte Geräte
                                        einblenden (gestrichelt)</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="chart-box">
                        <canvas id="dleChart"></canvas>
                    </div>
                    <p class="small text-muted mt-2 mb-0">
                        Berechnung aus den Herstellerangaben: Menge = Leistung × 14,3 ÷ (Auslauf − Kaltwasser),
                        begrenzt auf den max. Durchfluss des Geräts und die max. einstellbare Temperatur.
                        Horizontale Abschnitte = Durchflussbegrenzung.
                    </p>
                </div>
            </div>

            <?php if ($SHOW_HERSTELLER): ?>
                <!-- ===== Herstellerdaten (nur intern) ===== -->
                <div class="card border-warning">
                    <div class="card-header d-inline-flex justify-content-center align-items-center bg-warning-subtle">
                        <h4 class="mt-3">Herstellerdaten</h4>
                        <span class="badge bg-warning text-dark ms-3">nur intern sichtbar</span>
                    </div>
                    <div class="card-body">
                        <?php foreach (["bestaetigt" => "Bestätigt: unter Laborspüle verbaut",
                                           "unsicher" => "Nicht bestätigt – fließt nicht in die Mindestwerte oben ein"] as $st => $titel): ?>
                            <h6 class="mt-2"><?= htmlspecialchars($titel) ?></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-3">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Nr.</th>
                                        <th>Hersteller</th>

                                        <th>Modell</th>
                                        <th>Leistung</th>
                                        <th>Herstellerangabe</th>
                                        <th>max. Durchfluss</th>
                                        <th>bei <?= T_WARM_REF ?> °C</th>
                                        <th>Temp.-Bereich</th>
                                        <th>Einsatz / Begründung</th>
                                        <th>Quelle</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($PRODUKTE as $p):
                                        if ($p["status"] !== $st) continue;
                                        $q = lpmBei($p, T_KALT_REF, T_WARM_REF);
                                        $isMin = $st === "bestaetigt" && $q !== null
                                            && abs($q - $MIN_BEST[$p["kw"]]["wert"]) < 0.001; ?>
                                        <tr>
                                            <td class="fw-bold nowrap">Produkt <?= $p["nr"] ?></td>
                                            <td><?= htmlspecialchars($p["hersteller"]) ?></td>
                                            <td><?= htmlspecialchars($p["modell"]) ?></td>
                                            <td class="nowrap"><?= kwFmt($p["kw"]) ?></td>
                                            <td class="nowrap"><?= lpmFmt($p["lpm"]) ?> bei Δt <?= $p["dt"] ?> K</td>
                                            <td class="nowrap"><?= $p["max"] !== null ? lpmFmt($p["max"]) : "–" ?></td>
                                            <td class="nowrap <?= $isMin ? 'is-min' : '' ?>"><?= $q !== null ? lpmFmt($q) : "–" ?></td>
                                            <td class="nowrap"><?= $p["tmin"] ?>–<?= $p["tmax"] ?> °C</td>
                                            <td class="small"><?= htmlspecialchars($p["einsatz"]) ?></td>
                                            <td>
                                                <a href="<?= htmlspecialchars($p["url"]) ?>" target="_blank"
                                                   rel="noopener">
                                                    <i class="fas fa-file-pdf text-danger"></i> <?= htmlspecialchars($p["quelle"]) ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>

                        <h6>Zum Vergleich: Minimum inkl. nicht bestätigter Geräte</h6>
                        <ul class="small">
                            <?php foreach ($MIN_ALLE as $kw => $m): ?>
                                <li><?= kwFmt((string)$kw) ?>: <?= lpmFmt($m["wert"]) ?> bei <?= T_WARM_REF ?> °C
                                    (<?= $m["n"] ?> Geräte)
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <h6>Recherche-Stand</h6>
                        <ul class="small mb-0">
                            <li>Wesemann DELTA 30, Waldner SCALA (Technischer Katalog) und Waldner XPRESS nennen
                                unter Laborspülen keinen Durchlauferhitzer.
                            </li>
                            <li>In Labormöbel-Ausschreibungen tauchen Untertisch-DLE mit 11 kW als Position im
                                Laborspülen-Unterbau auf, jedoch ohne Modellangabe.
                            </li>
                            <li>Angeführt werden nur Geräte zwischen <?= KW_MIN ?> und <?= KW_MAX ?> kW.</li>
                            <li>Stiebel DCE 11/13 ist das Schwestermodell des bestätigten AEG DDLE Kompakt
                                (Konzernmarken)
                                und bleibt daher unten, damit dasselbe Gerät nicht doppelt zählt.
                            </li>
                            <li>Sobald weitere Geräte bestätigt sind: <code>status</code> auf <code>bestaetigt</code>
                                setzen, die Mindestwerte oben werden automatisch neu berechnet.
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    const FAKTOR = <?= DLE_FAKTOR ?>;
    const GERAETE = <?= json_encode($chartData, JSON_UNESCAPED_UNICODE) ?>;
    const FARBEN = ['#0d6efd', '#dc3545', '#198754', '#fd7e14', '#6f42c1', '#20c997',
        '#d63384', '#0dcaf0', '#6c757d', '#ffc107', '#795548', '#3f51b5', '#8bc34a'];
    const T_AXIS = Array.from({length: 36}, (_, i) => 25 + i);   // 25 … 60 °C

    function kurve(g, tKalt) {
        return T_AXIS.map(t => {
            const dt = t - tKalt;
            if (dt <= 0 || t > g.tmax) return null;
            let q = g.peff * FAKTOR / dt;
            if (g.max !== null) q = Math.min(q, g.max);
            return Math.round(q * 100) / 100;
        });
    }

    let chart;

    function zeichne() {
        const tKalt = parseFloat($('#tKalt').val()) || 10;
        const zeigeUnsicher = $('#zeigeUnsicher').length ? $('#zeigeUnsicher').is(':checked') : false;

        const datasets = GERAETE
            .map((g, i) => ({g, i}))
            .filter(o => o.g.status === 'bestaetigt' || zeigeUnsicher)
            .map(({g, i}) => ({
                label: g.label + (g.status === 'unsicher' ? ' – nicht bestätigt' : ''),
                data: kurve(g, tKalt),
                borderColor: FARBEN[i % FARBEN.length],
                backgroundColor: FARBEN[i % FARBEN.length],
                borderWidth: g.status === 'bestaetigt' ? 3 : 1.5,
                borderDash: g.status === 'bestaetigt' ? [] : [6, 4],
                pointRadius: 0,
                tension: 0.2,
                spanGaps: false
            }));

        const cfg = {
            type: 'line',
            data: {labels: T_AXIS, datasets},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {mode: 'index', intersect: false},
                scales: {
                    x: {title: {display: true, text: 'Auslauftemperatur [°C]'}},
                    y: {title: {display: true, text: 'Warmwassermenge [l/min]'}, beginAtZero: true, suggestedMax: 7}
                },
                plugins: {
                    legend: {position: 'bottom', labels: {boxWidth: 18}},
                    tooltip: {
                        callbacks: {
                            title: items => items[0].label + ' °C',
                            label: c => c.parsed.y === null ? null
                                : c.dataset.label + ': ' + c.parsed.y.toFixed(1).replace('.', ',') + ' l/min'
                        }
                    }
                }
            }
        };

        if (chart) {
            chart.data = cfg.data;
            chart.update();
        } else {
            chart = new Chart(document.getElementById('dleChart'), cfg);
        }
    }

    $(function () {
        $('#tKalt, #zeigeUnsicher').on('input change', zeichne);
        zeichne();
    });
</script>
</body>
</html>