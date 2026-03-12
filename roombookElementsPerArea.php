<?php
// 25 FX
require_once 'utils/_utils.php';
init_page_serversides();
include "utils/_format.php";

$mysqli = utils_connect_sql();
$projectID = (int)($_SESSION["projectID"] ?? 0);

if (!$projectID) {
    die("Ungültige Projekt-ID");
}

// Raumbereiche für das aktuelle Projekt laden
$stmt = $mysqli->prepare("
    SELECT DISTINCT `Raumbereich Nutzer`
    FROM tabelle_räume
    WHERE tabelle_projekte_idTABELLE_Projekte = ?
      AND `Raumbereich Nutzer` IS NOT NULL
      AND `Raumbereich Nutzer` != ''
    ORDER BY `Raumbereich Nutzer`
");
$stmt->bind_param("i", $projectID);
$stmt->execute();
$raumbereiche = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Elemente je Raumbereich</title>
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
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
          rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
<div class="container-fluid">
    <div id="limet-navbar"></div>

    <div class="mt-4 card">
        <div class="card-header">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    <span class="fw-semibold">Elemente je Raumbereich</span>
                </div>
                <div class="col-xxl-5 col-lg-6 col-12">
                    <select id="select_raumbereiche" class="form-select form-select-sm" multiple>
                        <?php foreach ($raumbereiche as $rb): ?>
                            <option value="<?= htmlspecialchars($rb['Raumbereich Nutzer'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($rb['Raumbereich Nutzer'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <div class="form-check form-switch mb-0 d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="chk_distinct" checked>
                        <label class="form-check-label text-nowrap" for="chk_distinct">Distinct</label>
                    </div>
                </div>
                <div class="col-auto">

                    <button class="btn btn-sm btn-outline-dark" id="btn_alle_auswaehlen" type="button">
                        <i class="fas fa-check-double me-1"></i>Alle
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ms-1" id="btn_auswahl_leeren" type="button">
                        <i class="fas fa-times me-1"></i>Leeren
                    </button>

                </div>
                <div class="col-auto ms-auto" id="cardHeaderButtons">

                </div>
            </div>
        </div>

        <div class="card-body p-2">
            <div id="placeholder_text" class="text-center text-muted py-5">
                <i class="fas fa-hand-point-up fa-2x mb-2 d-block"></i>
                Bitte einen oder mehrere Raumbereiche auswählen.
            </div>
            <div id="table_wrapper" style="display:none;">
                <table id="tableElementeRaumbereich"
                       class="table table-sm table-striped table-hover table-bordered border border-light border-5 w-100">
                    <thead>
                    <tr>
                        <th>ElementID</th>
                        <th>Bezeichnung</th>
                        <th>Raumbereich</th>
                        <th>Raumnr</th>
                        <th>Raumbezeichnung</th>
                        <th>Geschoss</th>
                        <th>Anzahl</th>
                        <th>Neu/Bestand</th>
                        <th>Los</th>
                    </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    var table = null;
    var projectID = <?= $projectID ?>;

    function destroyTable() {
        if (table) {
            table.destroy();
            table = null;
        }
        // DataTables-DOM-Elemente aus dem Card-Header entfernen
        $('#cardHeaderButtons').empty();
        // Tabellen-Body leeren
        $('#tableBody').empty();
    }

    $(document).ready(function () {

        $('#select_raumbereiche').select2({
            theme: 'bootstrap-5',
            placeholder: 'Raumbereich(e) wählen...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false
        });

        $('#btn_alle_auswaehlen').on('click', function () {
            $('#select_raumbereiche option').prop('selected', true);
            $('#select_raumbereiche').trigger('change');
        });

        $('#btn_auswahl_leeren').on('click', function () {
            $('#select_raumbereiche').val(null).trigger('change');
        });

        $('#select_raumbereiche').on('change', function () {
            var selected = $(this).val();
            if (!selected || selected.length === 0) {
                $('#table_wrapper').hide();
                $('#placeholder_text').show();
                if (table) {
                    table.clear().draw();
                }
                return;
            }
            destroyTable();
            loadData(selected);
        });
        $('#chk_distinct').on('change', function () {
            var selected = $('#select_raumbereiche').val();
            if (selected && selected.length > 0) {
                if (table) {
                    table.destroy();
                    table = null;
                    $('#tableBody').empty();
                }
                destroyTable();
                loadData(selected);
            }
        });
    });

    function loadData(raumbereiche) {
        var distinct = $('#chk_distinct').is(':checked') ? 1 : 0;  // NEU

        $.ajax({
            url: 'getElementeJeRaumbereich.php',
            type: 'POST',
            data: {
                raumbereiche: raumbereiche,
                projectID: projectID,
                distinct: distinct
            },
            dataType: 'json',
            beforeSend: function () {
                $('#placeholder_text').hide();
                $('#table_wrapper').show();
            },
            success: function (response) {
                if (table) {
                    table.clear().rows.add(response.data).draw();
                } else {
                    initTable(response.data);
                }
            },
            error: function (response) {
                makeToaster(response, false);
            }
        });
    }

    function initTable(data) {
        var isDistinct = $('#chk_distinct').is(':checked');
        table = new DataTable('#tableElementeRaumbereich', {
            data: data,
            columns: [
                {data: 'ElementID'},
                {data: 'Bezeichnung'},
                // Raumbereich-Spalte nur im Nicht-Distinct-Modus sinnvoll
                {data: 'Raumbereich', visible: !isDistinct},
                {data: 'Raumnr', visible: !isDistinct},
                {data: 'Raumbezeichnung', visible: !isDistinct},
                {data: 'Geschoss', visible: !isDistinct},
                {data: 'Anzahl', className: 'text-end'},
                {
                    data: 'NeuBestand', visible: !isDistinct,
                    render: function (data) {
                        return data === 1
                            ? '<span class="badge bg-primary">Neu</span>'
                            : '<span class="badge bg-secondary">Bestand</span>';
                    }
                },
                {data: 'LosBezeichnung', visible: !isDistinct, defaultContent: '<span class="text-muted">–</span>'}
            ],
            order: [[1, 'asc'], [2, 'asc']],
            paging: true,
            pageLength: 25,
            lengthChange: true,
            searching: true,
            info: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: '',
                searchPlaceholder: 'Suche...',
                info: "_TOTAL_ Einträge"
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'colvis',
                            text: 'Spalten',
                            className: 'btn btn-outline-dark bg-white btn-sm'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel"></i>',
                            className: 'btn btn-outline-success bg-white btn-sm ms-1',
                            title: 'Elemente je Raumbereich'
                        }
                    ]
                },
                topEnd: null,
                bottomStart: ['info', 'pageLength'],
                bottomEnd: ['search', 'paging']
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search .xxx').remove();
                $('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .addClass('btn btn-sm btn-outline-dark xxx')
                    .appendTo('#cardHeaderButtons');
                $('.dt-buttons').addClass('btn-group btn-group-sm ms-1').appendTo('#cardHeaderButtons');
            }
        });
    }

</script>
</body>
</html>