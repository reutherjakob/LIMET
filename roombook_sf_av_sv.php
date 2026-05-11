<?php

require_once 'utils/_utils.php';
init_page_serversides();
$mysqli = utils_connect_sql();
$projekt_id = 95;

define('GLZ', 0.7); // Fixe Gleichzeitigkeit

// Raumtyp-Definitionen
$raumtypen = [
    1 => 'Probenvorbereitung/-homogenisierung ohne Abluft',
    2 => 'Probenvorbereitung/-homogenisierung mit Abluft',
    3 => 'Labor chemische Aufarbeitung, max. 2 Digestoren',
    4 => 'Labor chemische Aufarbeitung, >2 Digestoren',
    5 => 'Labor molekularbiologisch (Unterdruck ohne Schleuse)',
    6 => 'Labor molekularbiologisch (Unterdruck mit Schleuse)',
    7 => 'Labor biologische Aufarbeitung',
    8 => 'Labor mikrobiologisch, BSL2',
    9 => 'Labor mikrobiologisch, BSL3',
    10 => 'Labor mikrobiologisch Quarantäne',
    11 => 'Mikroskopieraum',
    12 => 'Wägeraum',
    13 => 'Klimakammer (Temperatur, Luftfeuchte, Licht)',
    14 => 'Labor radiochemische Aufarbeitung, C-Labor',
    15 => 'Labor radiochemische Aufarbeitung, B-Labor',
    16 => 'Sonstige Arbeitsräume',
    17 => 'Messraum molekularbiologisch (Überdruck)',
    18 => 'Messraum biologisch',
    19 => 'Messraum chemisch, ohne Sondergase',
    20 => 'Messraum chemisch, mit Sondergasen',
    21 => 'Messraum, radiochemisch',
    22 => 'Laborgeschirrreinigung (Waschküche)',
    23 => 'Raum für Brutschränke/Klimaschränke',
    24 => 'Raum für Kühl-/Tiefkühlschränke',
    25 => 'Beschreibungs-/Sensorikraum',
    26 => 'Lagerraum, Proben Raumtemperatur',
    27 => 'Lagerraum Proben, gekühlt',
    28 => 'Lagerraum Proben, tiefgekühlt',
    29 => 'Lagerraum, Chemikalien',
    30 => 'Lagerraum, Arbeitsmittel/Laborbedarf',
    31 => 'Lagerraum, Radiochemie',
    32 => 'Archiv, Dokumente und Altgeräte',
    33 => 'Archiv, Proben',
    34 => 'Büro Führungskraft / Fachexperte / Gutachter',
    35 => 'Büro Analytiker (Auswerteraum)',
];

$message = '';
$message_type = '';

// POST: Quotient speichern & AV/SV neu berechnen
// AV  = Gesamt_W × GLZ × Quotient
// SV  = Gesamt_W × GLZ × (1 – Quotient)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quotienten'])) {
    $errors = 0;
    foreach ($_POST['quotienten'] as $typ_id => $quot_raw) {
        $typ_id = (int)$typ_id;
        $quot = floatval(str_replace(',', '.', $quot_raw));
        if ($quot < 0 || $quot > 1) {
            $errors++;
            continue;
        }

        // 1) Quotient in Mindestgrösse speichern
        $stmt = $mysqli->prepare(
            "UPDATE tabelle_räume 
             SET Mindestgrösse = ?
             WHERE tabelle_projekte_idTABELLE_Projekte = ?
               AND `Raumtyp BH` = ?"
        );
        $quot_str = number_format($quot, 2, '.', '');
        $stmt->bind_param('sii', $quot_str, $projekt_id, $typ_id);
        $stmt->execute();
        $stmt->close();

        // 2) AV und SV neu berechnen (auf Basis GLZ-Leistung)
        $glz = GLZ;
        $stmt2 = $mysqli->prepare(
            "UPDATE tabelle_räume
             SET ET_Anschlussleistung_AV_W = ET_Anschlussleistung_W * ? * ?,
                 ET_Anschlussleistung_SV_W = ET_Anschlussleistung_W * ? * (1 - ?)
             WHERE tabelle_projekte_idTABELLE_Projekte = ?
               AND `Raumtyp BH` = ?
               AND ET_Anschlussleistung_W IS NOT NULL"
        );
        $stmt2->bind_param('ddddii', $glz, $quot, $glz, $quot, $projekt_id, $typ_id);
        $stmt2->execute();
        $stmt2->close();
    }

    if ($errors === 0) {
        $message = 'Quotienten gespeichert und AV/SV erfolgreich neu berechnet (GLZ = ' . GLZ . ').';
        $message_type = 'success';
    } else {
        $message = "Speichern abgeschlossen, aber $errors Wert(e) waren ungültig (müssen zwischen 0 und 1 liegen).";
        $message_type = 'warning';
    }
}

// Daten laden: Summen je Raumtyp + aktueller Quotient aus Mindestgrösse
$glz = GLZ;
$stmt = $mysqli->prepare(
    "SELECT 
        `Raumtyp BH`,
        EL_Leistungsbedarf_W_pro_m2,
        sum(Nutzfläche) as Fläche,
        COUNT(*) AS anzahl_räume,
        SUM(ET_Anschlussleistung_W)            AS gesamt_W,
        SUM(ET_Anschlussleistung_W * ?)        AS gesamt_W_glz,
        SUM(ET_Anschlussleistung_AV_W)         AS gesamt_AV,
        SUM(ET_Anschlussleistung_SV_W)         AS gesamt_SV,
        MAX(Mindestgrösse)                     AS quotient_gespeichert
     FROM tabelle_räume
     WHERE tabelle_projekte_idTABELLE_Projekte = ?
       AND `Raumtyp BH` IS NOT NULL
     GROUP BY `Raumtyp BH`
     ORDER BY `Raumtyp BH`"
);
$stmt->bind_param('di', $glz, $projekt_id);
$stmt->execute();
$result = $stmt->get_result();
$zeilen = [];
while ($row = $result->fetch_assoc()) {
    $zeilen[$row['Raumtyp BH']] = $row;
}
$stmt->close();


$stmt_sum = $mysqli->prepare(
    "SELECT 
        SUM(ET_Anschlussleistung_W)        AS total_W,
        SUM(ET_Anschlussleistung_W * ?)    AS total_W_glz,
        SUM(ET_Anschlussleistung_AV_W )     AS total_AV,
        SUM(ET_Anschlussleistung_SV_W  )     AS total_SV
     FROM tabelle_räume
     WHERE tabelle_projekte_idTABELLE_Projekte = ?
       AND `Raumtyp BH` IS NOT NULL"
);
$stmt_sum->bind_param('di', $glz, $projekt_id);
$stmt_sum->execute();
$totals = $stmt_sum->get_result()->fetch_assoc();
$stmt_sum->close();

function fmt_kw($watt)
{
    if ($watt === null) return '—';
    return number_format($watt / 1000, 2, ',', '.');
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>EL – AV/SV Quotienten</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"/>
</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid mt-3">
    <form method="POST">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>
                    ⚡ EL – AV/SV Quotienten
                    <span class="text-muted fw-normal small">
            &nbsp;|&nbsp; Gleichzeitigkeit: <strong><?= GLZ ?></strong>
            &nbsp;|&nbsp; Quotient = AV-Anteil der GLZ-Leistung. Rest = SV.
        </span>
                </strong>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="tabellen-suche" class="form-control form-control-sm"
                           placeholder="🔍 Suchen..." style="width: 180px">
                    <button type="button" id="excel-btn" class="btn btn-sm btn-success fw-bold">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold">
                        <i class="fas fa-save me-1"></i> Speichern &amp; AV/SV neu berechnen
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-2" style="max-height: calc(100vh - 130px); overflow-y: auto;">

                    <table id="quotienten-table" class="table table-bordered table-hover table-sm mb-0">
                        <thead class="table-dark"
                               style="position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.4);">
                        <tr>
                            <th style="width:30px">#</th>
                            <th>Raumtyp</th>
                            <th class="text-center">Anzahl Räume</th>
                            <th class="text-center">Gesammtfläche</th>
                            <th class="text-center">kW je m2 Benchmark</th>

                            <th class="text-center">Gesamt [kW]</th>
                            <th class="text-center text-warning">
                                Inkl. GLZ<br>
                                <small class="fw-normal">(×<?= GLZ ?>)</small>
                            </th>
                            <th class="text-center text-info">AV [kW] inkl. GLZ <br>
                                <small class="fw-normal">(×<?= GLZ ?>)</small></th>
                            <th class="text-center text-danger">SV [kW] inkl. GLZ<br>
                                <small class="fw-normal">(×<?= GLZ ?>)</small></th>
                            <th class="text-center" style="min-width:110px">
                                AV-Quotient<br>
                                <small class="text-muted fw-normal">(0.00 – 1.00)</small>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($raumtypen as $typ_id => $typ_name):
                            $z = $zeilen[$typ_id] ?? null;
                            $anzahl = $z ? (int)$z['anzahl_räume'] : 0;
                            $gesamt = $z ? $z['gesamt_W'] : null;
                            $gesamt_glz = $z ? $z['gesamt_W_glz'] : null;
                            $av = $z ? $z['gesamt_AV'] : null;
                            $sv = $z ? $z['gesamt_SV'] : null;
                            $quot_val = $z && $z['quotient_gespeichert'] !== null
                                ? $z['quotient_gespeichert']
                                : '';
                            $has_data = $anzahl > 0;
                            ?>
                            <tr <?= !$has_data ? 'class="table-light text-muted"' : '' ?>>
                                <td class="text-center"><?= $typ_id ?></td>
                                <td><?= htmlspecialchars($typ_name) ?></td>
                                <td class="text-center"><?= $has_data ? $anzahl : '—' ?></td>
                                <td class="text-end"><?= $has_data ? number_format($z['Fläche'], 1, ',', '.') . ' m²' : '—' ?></td>
                                <td class="text-end"><?= $has_data && $z['EL_Leistungsbedarf_W_pro_m2'] !== null
                                        ? number_format($z['EL_Leistungsbedarf_W_pro_m2'] / 1000, 3, ',', '.')
                                        : '—' ?></td>
                                <td class="text-end"><?= fmt_kw($gesamt) ?></td>
                                <td class="text-end text-warning fw-semibold"><?= fmt_kw($gesamt_glz) ?></td>
                                <td class="text-end text-info"><?= fmt_kw($av) ?></td>
                                <td class="text-end text-danger"><?= fmt_kw($sv) ?></td>
                                <td class="text-center">
                                    <input type="number"
                                           class="form-control form-control-sm d-inline-block"
                                           style="width:80px"
                                           name="quotienten[<?= $typ_id ?>]"
                                           value="<?= htmlspecialchars($quot_val) ?>"
                                           min="0" max="1" step="0.05"
                                           placeholder="0.00"
                                        <?= !$has_data ? 'disabled' : '' ?>>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr class="table-secondary fw-bold">
                            <td colspan="5" class="text-end">Σ Gesamt:</td>
                            <td class="text-end"><?= fmt_kw($totals['total_W']) ?></td>
                            <td class="text-end text-warning"><?= fmt_kw($totals['total_W_glz']) ?></td>
                            <td class="text-end text-info"><?= fmt_kw($totals['total_AV']) ?></td>
                            <td class="text-end text-danger"><?= fmt_kw($totals['total_SV']) ?></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
</body>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        var table = $('#quotienten-table').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
            dom: 't',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'EL – AV/SV Quotienten',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                        format: {
                            body: function (data, row, column, node) {
                                // Spalte 9 = AV-Quotient
                                if (column === 9) {
                                    var input = $(node).find('input');
                                    if (input.length) {
                                        var val = input.val();
                                        return val !== '' ? parseFloat(val) : '';
                                    }
                                }
                                // Spalte 3 = Fläche (m²): "1.234,5 m²" → Zahl
                                if (column === 3) {
                                    if (!data || data.trim() === '—') return '';
                                    var num = parseFloat(data.replace(/\./g, '').replace(',', '.').replace(/\s*m²/, ''));
                                    return isNaN(num) ? '' : num;
                                }
                                // Spalte 4 = Benchmark (kW/m²): Komma → Punkt
                                if (column === 4) {
                                    if (!data || data.trim() === '—') return '';
                                    var num = parseFloat(data.replace(/\./g, '').replace(',', '.'));
                                    return isNaN(num) ? '' : num;
                                }
                                // Spalten 5–8 = kW-Werte
                                if (column >= 5 && column <= 8) {
                                    if (!data || data.trim() === '—') return '';
                                    var num = parseFloat(data.replace(/\./g, '').replace(',', '.'));
                                    return isNaN(num) ? '' : num;
                                }
                                return data;
                            }
                        }
                    }
                }
            ],

            columnDefs: [
                {orderable: false, targets: 7}
            ],
            language: {
                zeroRecords: 'Keine Einträge gefunden'
            }
        });

        // Suche verdrahten
        $('#tabellen-suche').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Excel-Button verdrahten
        $('#excel-btn').on('click', function () {
            table.button(0).trigger();
        });
    });
</script>
</html>