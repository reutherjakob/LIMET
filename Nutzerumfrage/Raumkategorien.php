<?php

global $mysqli, $labortypen;
require_once "../Nutzerlogin/_utils.php";

if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}

$role = init_page(["internal_rb_user", "spargelfeld_ext_users", "spargefeld_admin"]);

require_once "../Nutzerumfrage/raumtypen.php"; // lädt $labortypen


// =========================================================
// COLUMN-KONFIGURATION
// 'array_key' => ['label' => 'Tabellenüberschrift', 'suffix' => 'Einheit (optional)', 'hidden' => bool]
// =========================================================
$column_config = [
    'id' => ['label' => 'ID', 'suffix' => '', 'hidden' => false, 'initially_hidden' => true],
    'bezeichnung' => ['label' => 'Raumbez.', 'suffix' => '', 'hidden' => false],
    'beschreibung' => ['label' => 'Tätigkeiten', 'suffix' => '', 'hidden' => false],
    'achsen' => ['label' => 'Layout [Achsen]', 'suffix' => '', 'hidden' => false],
    'flaeche_min' => ['label' => 'Mind. Flächenbedarf [m²]', 'suffix' => '', 'hidden' => false, 'initially_hidden' => true],
    'flaeche_max' => ['label' => 'Max. Flächenbedarf [m²]', 'suffix' => '', 'hidden' => false, 'initially_hidden' => true],
    'raumhoehe' => ['label' => 'Mind. Lichte Raumhöhe (Neubau/Bestand) [m]', 'suffix' => '', 'hidden' => false, 'initially_hidden' => true],
    'raumhoehe_rohbau' => ['label' => 'Mind. Raumhöhe Bestand [m]', 'suffix' => '', 'hidden' => false],
    'raumhoehe_neubau' => ['label' => 'Mind. Raumhöhe Neubau [m]', 'suffix' => '', 'hidden' => false],
    'decke' => ['label' => 'Decke', 'suffix' => '', 'hidden' => false],
    'tuere_min' => ['label' => 'Türbreiten [m]', 'suffix' => '', 'hidden' => false],
    'temp_min' => ['label' => 'Min. Temp.[°C]', 'suffix' => '', 'hidden' => false],
    'temp_max' => ['label' => 'Max. Temp.[°C]', 'suffix' => '', 'hidden' => false],
    'temp_schwankung' => ['label' => 'Temp. Schwankungstoleranz  [K]', 'suffix' => '', 'hidden' => false],

    'luftfeuchtigkeit' => ['label' => 'Anford. Luftfeuchtigkeit [%]', 'suffix' => '', 'hidden' => false],
    'luftfeuchtigkeit_schwankungstoleranz' => ['label' => 'Luftfeuchtigkeit Schwankungstoleranz [%]', 'suffix' => '', 'hidden' => false],

    'akustik' => ['label' => 'Anforderungen Akustik/Schallschutz > Norm ', 'suffix' => '', 'hidden' => false],

    'tagelicht_notwendig' => ['label' => 'Tageslicht erforderlich', 'suffix' => '', 'hidden' => false],
    'blendschutz' => ['label' => 'Steuerbarer Blendschutz/Sonnenschutz', 'suffix' => '', 'hidden' => false],
    'verdunkelung' => ['label' => 'Verdunkelung erforderlich', 'suffix' => '', 'hidden' => false],

    'luftwechsel' => ['label' => 'Luftwechselrate', 'suffix' => '', 'hidden' => false, 'initially_hidden' => true],
    'luftwechsel_rate_m3_je_m2h' => ['label' => 'Luftwechselrate', 'suffix' => '', 'hidden' => false],
    'luftwechsel_typ' => ['label' => 'Luftwechsel Typ', 'suffix' => '', 'hidden' => false],
    'luftwechsel_norm' => ['label' => 'Lüftungsnorm', 'suffix' => '', 'hidden' => false],
    'luftwechsel_abluft_filter' => ['label' => 'Luft Filter', 'suffix' => '', 'hidden' => false],

    'druckregelung' => ['label' => 'Über-/Unterdruckregelung', 'suffix' => '', 'hidden' => false, 'initially_hidden' => true],
    'druckregelung_typ' => ['label' => 'Druckregelung Typ', 'suffix' => '', 'hidden' => false],
    'druckregelung_schleuse' => ['label' => 'Schleuse', 'suffix' => '', 'hidden' => false],

    'heizung_kuehlung' => ['label' => 'Zentrale Heizung und Kühlung', 'suffix' => '', 'hidden' => false, 'initially_hidden' => true],
    'kuehlung' => ['label' => 'Zentrale Kühlung', 'suffix' => '', 'hidden' => false],
    'heizung' => ['label' => 'Zentrale Heizung', 'suffix' => '', 'hidden' => false],

    'elektro' => ['label' => 'Elektroversorgung', 'suffix' => '', 'hidden' => false, 'initially_hidden' => true],
    'elektro_230v' => ['label' => '230V', 'suffix' => '', 'hidden' => false],
    'elektro_400v_cee' => ['label' => '400V CEE', 'suffix' => '', 'hidden' => false],
    'elektro_edv' => ['label' => 'EDV', 'suffix' => '', 'hidden' => false],
    'elektro_notstrom' => ['label' => 'Notstrom ohne USV', 'suffix' => '', 'hidden' => false],
    'elektro_notstrom_usv' => ['label' => 'Notstrom mit USV', 'suffix' => '', 'hidden' => false],

    'anschlussleistung' => ['label' => 'Elektrische Anschlussleistung [ca. W/m2]', 'suffix' => '', 'hidden' => false],
    'waermeabgabe' => ['label' => 'Wärmeabgabe durch Geräte [ca. W/m2]', 'suffix' => '', 'hidden' => false],


    'kaltwasser' => ['label' => 'Kaltwasser', 'suffix' => '', 'hidden' => false],
    'warmwasser' => ['label' => 'Warmwasser (Durchlauferhitzer)', 'suffix' => '', 'hidden' => false],
    've_wasser' => ['label' => 'VE Wasser', 'suffix' => '', 'hidden' => false],

    'n2' => ['label' => 'N2 (zentral versorgt)', 'suffix' => '', 'hidden' => false],
    'dl' => ['label' => 'DL (zentral versorgt)', 'suffix' => '', 'hidden' => false],
    'sondergase' => ['label' => 'Sondergase (dezentral versorgt)', 'suffix' => '', 'hidden' => false],

    'abzuege' => ['label' => 'Abzüge / Digestorien', 'suffix' => '', 'hidden' => false],
    // 'abzuege_anzahl_min' => ['label' => 'Abzüge Anzahl Min.', 'suffix' => '', 'hidden' => false],
    // 'abzuege_anzahl_max' => ['label' => 'Abzüge Anzahl Max.', 'suffix' => '', 'hidden' => false],
    // 'abzuege_notstrom' => ['label' => 'Abzüge notstromversorgt', 'suffix' => '', 'hidden' => false],
    // 'abzuege_unterflurabfall' => ['label' => 'Unterflurabfallsammelsystem', 'suffix' => '', 'hidden' => false],
    // 'abzuege_abluftwaescher' => ['label' => 'Abluftwäscher', 'suffix' => '', 'hidden' => false],
    // 'abzuege_sicherheitswerkbank_klasse' => ['label' => 'Sicherheitswerkbank Klasse', 'suffix' => '', 'hidden' => false],

    'sonderabluft' => ['label' => 'Sonderabluft', 'suffix' => '', 'hidden' => false],

    'punktabsaugungen' => ['label' => 'Punktabsaugungen', 'suffix' => '', 'hidden' => false],
    //  'punktabsaugungen_min' => ['label' => 'Punktabsaugungen Min.', 'suffix' => '', 'hidden' => false],
    //  'punktabsaugungen_max' => ['label' => 'Punktabsaugungen Max.', 'suffix' => '', 'hidden' => false],
    //  'punktabsaugungen_pro_einheit' => ['label' => 'Punktabsaugungen Bezugsgröße', 'suffix' => '', 'hidden' => false],
    //  'punktabsaugungen_nach_geraeten' => ['label' => 'Punktabsaugungen nach Geräteanzahl', 'suffix' => '', 'hidden' => false],

    'labormoebel' => ['label' => 'Labormöbel', 'suffix' => '', 'hidden' => false],
    'sicherheitsschraenke' => ['label' => 'Sicherheitsschränke', 'suffix' => '', 'hidden' => false],
    'sicherheitsschrank_saeure_lauge' => ['label' => 'Säure/Laugen-Schrank (mit Abluft)', 'suffix' => '', 'hidden' => false],
    'sicherheitsschrank_brennbar' => ['label' => 'Sicherheitsschrank brennbare Fl.', 'suffix' => '', 'hidden' => false],


    'sicherheitsausstattung' => ['label' => 'Sicherheitsausstattung', 'suffix' => '', 'hidden' => false],
    'sicherheit_notdusche' => ['label' => 'Notdusche', 'suffix' => '', 'hidden' => false],
    'sicherheit_augendusche' => ['label' => 'Augendusche (leitungsgebunden)', 'suffix' => '', 'hidden' => false],
    'sicherheit_notruf' => ['label' => 'Notrufmöglichkeit', 'suffix' => '', 'hidden' => false],
    'sicherheit_erstehilfe' => ['label' => 'Erste-Hilfe-Koffer im Raum', 'suffix' => '', 'hidden' => false],


    'sonstige_anforderungen' => ['label' => 'Sonstige Anforderungen', 'suffix' => '', 'hidden' => false],
    'anmerkungen' => ['label' => 'Anmerkungen', 'suffix' => '', 'hidden' => false],
];
// =========================================================

$columns = array_keys($column_config);
$hidden_indices = [];
$visible_index = 0;
foreach ($columns as $col) {
    if (!empty($column_config[$col]['hidden'])) {
        continue;
    }
    if (!empty($column_config[$col]['initially_hidden'])) {
        $hidden_indices[] = $visible_index;
    }
    $visible_index++;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title> Raumkategorien </title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
</head>

<style>
    #raeumTypenTable td {
        border-right: 1px solid #dee2e6;
    }
</style>

<div id="limet-navbar"></div>
<?php require_once "../Nutzerumfrage/_utils.php"; ?>

<body>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-3"> Raum Kategorien</div>
                <div class="col-9 d-flex justify-content-end" id="BtnArea"></div>
            </div>


        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover table-striped-columns" id="raeumTypenTable">
                <thead class="table-light">
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <?php if (empty($column_config[$col]['hidden'])): ?>
                            <th><?= htmlspecialchars($column_config[$col]['label'] ?? $col) ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($labortypen as $typ): ?>
                    <tr data-id="<?= htmlspecialchars($typ['id']) ?>"
                        data-name="<?= htmlspecialchars($typ['bezeichnung']) ?>">
                        <?php foreach ($columns as $col): ?>
                            <?php if (empty($column_config[$col]['hidden'])): ?>
                                <td>
                                    <?= htmlspecialchars($typ[$col] ?? '') ?>
                                    <?php if (!empty($typ[$col]) && !empty($column_config[$col]['suffix'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($column_config[$col]['suffix']) ?></small>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const table = $('#raeumTypenTable').DataTable({
            pageLength: 33,
            scrollX: true,
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: null
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.2.1/i18n/de-DE.json'
            },
            buttons: [
                {extend: 'colvis', text: '<i class="fas fa-columns"></i> Spaltensichtbarkeit'},
                {
                    extend: 'colvisRestore',
                    text: '<i class="fas fa-undo"></i> Spalten zurücksetzen',
                    action: function (e, dt) {
                        dt.state.clear();
                        window.location.reload();
                    }
                },
                {extend: 'searchBuilder', text: '<i class="fas fa-filter"></i> Filter'},
                {extend: 'excel', text: '<i class="fas fa-file-excel"></i> Download als Excel'},
                {extend: 'print', text: '<i class="fas fa-print"></i> Drucken'},
            ],
            stateSave: true,
            orderCellsTop: true,
            fixedHeader: true,
            responsive: false,
            columnDefs: [
                {targets: '_all', defaultContent: '–'},
                {targets: <?= json_encode($hidden_indices) ?>, visible: false},
            ],
            initComplete: function () {
                const btnArea = $('#BtnArea');
                table.buttons().container().appendTo(btnArea);
                const searchInput = $('<input type="search" class="form-control form-control-sm ms-2" placeholder="Suchen…" style="width:200px;">')
                    .on('input', function () {
                        table.search(this.value).draw();
                    });
                btnArea.append(searchInput);
            },
        });
    });
</script>

</body>
</html>