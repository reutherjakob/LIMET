<?php
require_once 'utils/_utils.php';
init_page_serversides("x");
$mysqli = utils_connect_sql();
$projekt_id = 95;

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

// Fläche je Trakt (Bauabschnitt) und Raumtyp
$stmt = $mysqli->prepare(
    "SELECT
        Bauabschnitt,
        `Raumtyp BH`,
        COUNT(*) AS anzahl_räume,
        SUM(Nutzfläche) AS gesamt_nf
     FROM tabelle_räume
     WHERE tabelle_projekte_idTABELLE_Projekte = ?
       AND `Raumtyp BH` IS NOT NULL
       AND Bauabschnitt IS NOT NULL
     GROUP BY Bauabschnitt, `Raumtyp BH`
     ORDER BY Bauabschnitt, `Raumtyp BH`"
);
$stmt->bind_param('i', $projekt_id);
$stmt->execute();
$result = $stmt->get_result();

// Daten strukturieren: $data[trakt][raumtyp_id] = ['anzahl' => x, 'nf' => y]
$data = [];
$trakte = [];
while ($row = $result->fetch_assoc()) {
    $trakt = $row['Bauabschnitt'];
    $typ   = (int)$row['Raumtyp BH'];
    if (!in_array($trakt, $trakte)) $trakte[] = $trakt;
    $data[$trakt][$typ] = [
        'anzahl' => (int)$row['anzahl_räume'],
        'nf'     => (float)$row['gesamt_nf'],
    ];
}
$stmt->close();
sort($trakte);

// Summen je Trakt
$summen = [];
foreach ($trakte as $trakt) {
    $summen[$trakt] = ['anzahl' => 0, 'nf' => 0.0];
    foreach ($data[$trakt] ?? [] as $d) {
        $summen[$trakt]['anzahl'] += $d['anzahl'];
        $summen[$trakt]['nf']     += $d['nf'];
    }
}

// Gesamtsumme
$gesamt_anzahl = array_sum(array_column($summen, 'anzahl'));
$gesamt_nf     = array_sum(array_column($summen, 'nf'));

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Fläche je Trakt – Projekt 95</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid mt-3">

    <!-- Summary Cards je Trakt -->
    <div class="row mb-3 g-2">
        <?php foreach ($trakte as $trakt): ?>
            <div class="col-auto">
                <div class="card text-center shadow-sm" style="min-width:140px">
                    <div class="card-header fw-bold bg-dark text-white py-1">
                        Trakt <?= htmlspecialchars($trakt) ?>
                    </div>
                    <div class="card-body py-2 px-3">
                        <div class="fs-5 fw-bold"><?= number_format($summen[$trakt]['nf'], 1, ',', '.') ?> m²</div>
                        <small class="text-muted"><?= $summen[$trakt]['anzahl'] ?> Räume</small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="col-auto">
            <div class="card text-center shadow-sm border-primary" style="min-width:140px">
                <div class="card-header fw-bold bg-primary text-white py-1">Gesamt</div>
                <div class="card-body py-2 px-3">
                    <div class="fs-5 fw-bold"><?= number_format($gesamt_nf, 1, ',', '.') ?> m²</div>
                    <small class="text-muted"><?= $gesamt_anzahl ?> Räume</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailtabelle -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong><i class="fas fa-th me-2"></i>Fläche je Trakt &amp; Raumtyp</strong>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" id="tabellen-suche" class="form-control form-control-sm"
                       placeholder="🔍 Suchen…" style="width:200px">
                <button id="excel-btn" class="btn btn-sm btn-success fw-bold">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: calc(100vh - 230px); overflow-y: auto;">
                <table id="trakt-table" class="table table-sm table-bordered table-hover mb-0">
                    <thead class="table-dark"
                           style="position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.4);">
                    <tr>
                        <th style="width:30px">#</th>
                        <th>Raumtyp</th>
                        <?php foreach ($trakte as $trakt): ?>
                            <th class="text-center" colspan="2">
                                Trakt <?= htmlspecialchars($trakt) ?>
                            </th>
                        <?php endforeach; ?>
                        <th class="text-center" colspan="2">Gesamt</th>
                    </tr>
                    <tr class="table-secondary" style="position: sticky; top: 37px; z-index: 9;">
                        <th></th>
                        <th></th>
                        <?php foreach ($trakte as $trakt): ?>
                            <th class="text-center small text-muted">Räume</th>
                            <th class="text-center small text-muted">NF [m²]</th>
                        <?php endforeach; ?>
                        <th class="text-center small text-muted">Räume</th>
                        <th class="text-center small text-muted">NF [m²]</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($raumtypen as $typ_id => $typ_name):
                        // Prüfen ob dieser Raumtyp irgendwo vorkommt
                        $hat_daten = false;
                        $row_anzahl = 0;
                        $row_nf = 0.0;
                        foreach ($trakte as $trakt) {
                            if (!empty($data[$trakt][$typ_id])) {
                                $hat_daten = true;
                                $row_anzahl += $data[$trakt][$typ_id]['anzahl'];
                                $row_nf     += $data[$trakt][$typ_id]['nf'];
                            }
                        }
                        if (!$hat_daten) continue; // Raumtypen ohne Räume ausblenden
                    ?>
                        <tr>
                            <td class="text-center text-muted"><?= $typ_id ?></td>
                            <td><?= htmlspecialchars($typ_name) ?></td>
                            <?php foreach ($trakte as $trakt): ?>
                                <?php $d = $data[$trakt][$typ_id] ?? null; ?>
                                <td class="text-center"><?= $d ? $d['anzahl'] : '—' ?></td>
                                <td class="text-end"><?= $d ? number_format($d['nf'], 1, ',', '.') : '—' ?></td>
                            <?php endforeach; ?>
                            <td class="text-center fw-semibold"><?= $row_anzahl ?></td>
                            <td class="text-end fw-semibold"><?= number_format($row_nf, 1, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr class="table-secondary fw-bold">
                        <td colspan="2" class="text-end">Σ</td>
                        <?php foreach ($trakte as $trakt): ?>
                            <td class="text-center"><?= $summen[$trakt]['anzahl'] ?></td>
                            <td class="text-end"><?= number_format($summen[$trakt]['nf'], 1, ',', '.') ?></td>
                        <?php endforeach; ?>
                        <td class="text-center"><?= $gesamt_anzahl ?></td>
                        <td class="text-end"><?= number_format($gesamt_nf, 1, ',', '.') ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var colCount = <?= count($trakte) * 2 + 4 ?>;
        var exportCols = [];
        for (var i = 0; i < colCount; i++) exportCols.push(i);

        var table = $('#trakt-table').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
            dom: 't',
            buttons: [{
                extend: 'excelHtml5',
                title: 'Fläche je Trakt und Raumtyp',
                exportOptions: {
                    columns: exportCols,
                    modifier: {
                        page: 'all',
                        search: 'applied',
                        order: 'applied'
                    },
                    format: {
                        body: function (data, row, column, node) {
                            if (column < 2) return data;
                            if (!data || data.trim() === '—' || data.trim() === '') return '';
                            var clean = data.replace(/\./g, '').replace(',', '.').replace(/\s*m²/, '').trim();
                            var num = parseFloat(clean);
                            return isNaN(num) ? data : num;
                        },
                        footer: function (data, column, node) {
                            if (column < 2) return data;
                            if (!data || data.trim() === '—' || data.trim() === '') return '';
                            var clean = data.replace(/\./g, '').replace(',', '.').replace(/\s*m²/, '').trim();
                            var num = parseFloat(clean);
                            return isNaN(num) ? data : num;
                        }
                    }
                }
            }],
            language: {
                zeroRecords: 'Keine Einträge gefunden',
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
            }
        });

        $('#tabellen-suche').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('#excel-btn').on('click', function () {
            table.button(0).trigger();
        });
    });
</script>
</body>
</html>