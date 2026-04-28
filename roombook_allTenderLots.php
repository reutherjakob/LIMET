<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Losverwaltung</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png">
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <!--DATEPICKER -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <!--Bootstrap Toggle -->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
</head>


<body id="bodyTenderLots">
<?php
require_once 'utils/_utils.php';
init_page_serversides("x");
?>

<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class='row'>
        <div class='col-xxl-12' id="mainCardColumn">
            <div class="mt-4 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 d-flex align-items-top justify-content-start" id="LoseCardHeaderSub0">
                            <span><strong>ÜBERSICHT ALLE LOSE</strong> &emsp;</span>

                        </div>
                        <div class="col-6 d-flex align-items-top justify-content-end" id="LoseCardHeaderSub">
                            <div class="me-2">
                                <label for="dateSelect" class="visually-hidden">
                                    Änderungsdatum
                                </label>
                                <input type="date" id="dateSelect" name="dateSelect"
                                       class="form-control form-control-sm w-auto"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Ab welchem Versanddatum Lose laden?"/>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#helpModal" title="Hilfe">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>


                    </div>
                </div>

                <div class="card-body p-1 py-1 m-1" id="projectLots">
                    <table id="tableTenderLots"
                           class="table table-sm table-responsive table-striped compact border border-light border-1 w-100">
                        <thead>
                        <tr>
                            <th>ID Los</th>
                            <th>ID Projekt</th>
                            <th>Projekt</th>
                            <th>LosNummer</th>
                            <th>Bezeichnung</th>
                            <th>Versand</th>
                            <th>Liefertermin</th>
                            <th>Verfahren</th>
                            <th>Status</th>
                            <th>Vergabesumme</th>
                            <th>Vergabesumme</th>
                            <th>Auftragnehmer</th>
                            <th>MKF-von_Los</th>
                            <th>
                                <div class='d-flex justify-content-center align-items-center'
                                     data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="Los Workflow"><i
                                            class="fas fa-code-branch"></i>
                                </div>
                                <span class="visually-hidden">Workflow</span>
                            </th>
                            <th>
                                <div class='d-flex justify-content-center align-items-center'
                                     data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="Elemente im Los">
                                    <i class="fas fa-briefcase-medical"></i>
                                </div>
                                <span class="visually-hidden">Elemente in Los </span>
                            </th>

                            <th>
                                <div class='d-flex justify-content-center align-items-center'
                                     data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="Änderungen des Loses">
                                    <i class="fas fa-history"></i>
                                </div>
                                <span class="visually-hidden">Los Historie</span>
                            </th>


                            <th>
                                <div class='d-flex justify-content-center align-items-center'
                                     data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="Notiz">
                                    <i class="fas fa-sticky-note"></i>
                                </div>
                                <span class="visually-hidden">Notiz</span>
                            </th>

                            <th>
                                <div class='d-flex justify-content-center align-items-center'
                                     data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="ToDo-Einträge">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <span class="visually-hidden">ToDos</span>
                            </th>
                            <th>
                                <div class='d-flex justify-content-center align-items-center'
                                     data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="Angebote eingegangen?">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                                <span class="visually-hidden">Angebote eingegangen</span>
                            </th>

                            <th>
                                <div class='d-flex justify-content-center align-items-center' data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="Preis in DB eingetragen?">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <span class="visually-hidden">Preis in DB </span>
                            </th>
                            <th>
                                <div class='d-flex justify-content-center align-items-center'
                                     data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="Preis Eintrag kontrolliert von">
                                    <i class="fas fa-user-check"> </i>
                                </div>
                                <span class="visually-hidden">Preis Eintrag kontrolliert von:</span>
                            </th>

                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="notizModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Notiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="notizModalText"></label><textarea id="notizModalText" class="form-control"
                                                                  rows="6"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="button" class="btn btn-primary" id="notizSaveBtn">Speichern</button>
                </div>
            </div>
        </div>
    </div>

    <?php require "modal_show_lose_todo.php"; ?>
    <?php require "modal_help_allTenderLots.php"; ?>
    <?php require "modal_showLotWorkflow.php"; ?>
    <?php require "modal_showLotElements.php"; ?>
    <?php require "modal_los_aenderungen.php"; ?>

    <script src="utils/_utils.js"></script>
    <script charset="utf-8">
        var tableTenderLots;
        let currentNotizLotId = null;

        $(document).ready(function () {
            $('#dateSelect').val('2025-01-01');
            tableTenderLots = new DataTable('#tableTenderLots', {
                ajax: {
                    url: 'getFilteredLots.php',
                    type: 'POST',
                    data: function (d) {
                        d.datum = $('#dateSelect').val();
                        return d;
                    }
                },
                columnDefs: [
                    {targets: [0, 1], visible: false, searchable: false, sortable: false},
                    {
                        targets: [10],
                        visible: false,
                        searchable: false
                    }
                ],

                select: true,
                search: {search: ''},
                paging: true,
                searching: true,
                info: true,
                order: [[2, 'asc']],
                pagingType: 'full',
                lengthChange: true,
                pageLength: 20,
                lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                    decimal: ',',
                    thousands: '.',
                    searchPlaceholder: 'Suche..',
                    search: "",
                    lengthMenu: "_MENU_",
                    searchBuilder: {
                        title: '',
                        button: ''
                    },
                    buttons: {
                        excel: ''
                    }
                },

                buttons: [
                    {
                        extend: 'colvis', className: "btn  btn-light btn-outline-dark fas fa-eye",
                        text: '',
                        attr: {
                            'data-bs-toggle': 'tooltip',
                            "data-bs-title": 'Spalten ein/ausblenden',
                        }
                    },
                    {
                        extend: 'excel', className: "btn btn-light btn-outline-dark fas fa-file-excel",
                        exportOptions: {
                            columns: function (idx) {
                                return idx !== 0 && idx !== 1 && idx !== 9 && idx !== 13 && idx !== 14;
                            }
                        },
                        attr: {
                            'data-bs-toggle': 'tooltip',
                            "data-bs-title": 'Download als Excel',
                        },

                    },
                    {
                        extend: 'searchBuilder',
                        className: "btn btn-light btn-outline-dark fas fa-filter",
                        attr: {
                            'data-bs-toggle': 'tooltip',
                            "data-bs-title": 'Filter erstellen',
                        }
                    }
                ],
                layout: {
                    topStart: null, topEnd: null,
                    bottomStart: ['pageLength', 'info'],
                    bottomEnd: ['paging', 'search', 'buttons']
                },
                initComplete: function () {
                    let sourceElements = document.getElementsByClassName("dt-buttons");
                    let targetElement = document.getElementById("LoseCardHeaderSub0");
                    Array.from(sourceElements).forEach(function (element) {
                        targetElement.appendChild(element);
                    });

                    $('.dt-search label').remove();
                    $('.dt-search').children()
                        .removeClass('form-control form-control-sm')
                        .addClass("btn btn-sm btn-outline-secondary")
                        .appendTo('#LoseCardHeaderSub0');


                    if (!window.tooltipList) {
                        let tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                            .filter(el => el.nodeType === Node.ELEMENT_NODE && el.nodeName && typeof el.nodeName === 'string');
                        window.tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl, {
                                delay: {"show": 10, "hide": 0}
                            });
                        });
                    }

                }
            });


            $(document).on('change', '.lot-angebote-checkbox', function () {
                const lotId = $(this).data('lot-id');
                const projektId = $(this).data('projekt-id');
                if (!$(this).is(':checked')) return; // only handle checking

                $.ajax({
                    url: 'lotPriceUpdated.php',
                    type: 'POST',
                    data: {lot_id: lotId, projekt_id: projektId, preis_status: 1},
                    success: function (response) {
                        if (response.success) {
                            tableTenderLots.ajax.reload(null, false);
                        } else {
                            alert('Fehler: ' + (response.error || 'Unbekannter Fehler'));
                        }
                    },
                    error: function () {
                        alert('Verbindungsfehler.');
                    }
                });
            });

            // Angebote badge clicked → reset to 0
            $(document).on('click', '.lot-angebote-badge', function () {
                if (!confirm('Angebote-Status zurücksetzen?')) return;
                const lotId = $(this).data('lot-id');
                const projektId = $(this).data('projekt-id');

                $.ajax({
                    url: 'lotPriceUpdated.php',
                    type: 'POST',
                    data: {lot_id: lotId, projekt_id: projektId, preis_status: 0},
                    success: function (response) {
                        if (response.success) tableTenderLots.ajax.reload(null, false);
                        else alert('Fehler: ' + (response.error || 'Unbekannter Fehler'));
                    },
                    error: function () {
                        alert('Verbindungsfehler.');
                    }
                });
            });


            $(document).on('click', '.lot-notiz-btn', function () {
                currentNotizLotId = $(this).data('lot-id');
                $('#notizModalText').val($(this).data('notiz'));
                new bootstrap.Modal(document.getElementById('notizModal')).show();
            });

            $('#notizSaveBtn').on('click', function () {
                $.ajax({
                    url: 'save_los_notiz.php',
                    type: 'POST',
                    data: {
                        lot_id: currentNotizLotId,
                        notiz: $('#notizModalText').val()
                    },
                    success: function (response) {
                        if (response.success) {
                            bootstrap.Modal.getInstance(document.getElementById('notizModal')).hide();
                            tableTenderLots.ajax.reload(null, false);
                        } else {
                            alert('Fehler: ' + (response.error || 'Unbekannter Fehler'));
                        }
                    },
                    error: function () {
                        alert('Verbindungsfehler.');
                    }
                });
            });

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }


            $('#dateSelect').on('change', debounce(function () {
                tableTenderLots.ajax.reload(null, false);
            }, 100));

            $(document).on("click", "button[value='Los ToDos']", function () {
                var lotID = this.id.replace('lottodo_', '');

                $.ajax({
                    url: "get_los_todo.php",
                    type: "POST",
                    data: {lotID: lotID},
                    success: function (data) {
                        $("#todoModalBody").html(data);
                    },
                    error: function () {
                        $("#todoModalBody").html(`<div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Fehler beim Laden der ToDos.
            </div>`);
                    }
                });
            });

            $(document).on("click", "button[value='Los Workflow']", function () {
                var ID = this.id.replace('lotwf_', '');
                $.ajax({
                    url: "getLotWorkflow.php",
                    type: "POST",
                    data: {lotID: ID},
                    success: function (data) {
                        $("#workflowModalBody").html(data);
                    }
                });
            });

            $(document).on("click", "button[value='Los Elemente']", function (e) {
                e.preventDefault();
                var buttonId = $(this).attr('id');  // e.g. "lotelem_456_123"
                var parts = buttonId.split('_');
                var projectID = parts[1];  // 456
                var lotID = parts[2];      // 123
                console.log('projectID:', projectID, 'lotID:', lotID);
                $("#lotElementsModalBody").html(`<div class="text-center p-5">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Laden...</span>
                        </div>
                        <p class="mt-2">Los Elemente werden geladen...</p>
                    </div>`);

                $.ajax({
                    url: "getTenderLotElements.php",
                    type: "POST",
                    data: {
                        lotID: lotID,
                        projectID: projectID
                    },
                    success: function (data) {
                        $("#lotElementsModalBody").html(data);
                    },
                    error: function () {
                        $("#lotElementsModalBody").html(`<div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Fehler beim Laden der Los Elemente.
                </div>`);
                    }
                });
            });

            $(document).on('change', '.lot-preis-checkbox', function () {
                if (!$(this).is(':checked')) return;
                $.ajax({
                    url: 'lotPriceUpdated.php', type: 'POST',
                    data: {lot_id: $(this).data('lot-id'), preis_status: 2},
                    success: function (r) {
                        if (r.success) tableTenderLots.ajax.reload(null, false);
                        else alert('Fehler: ' + (r.error || 'Unbekannter Fehler'));
                    },
                    error: function () {
                        alert('Verbindungsfehler.');
                    }
                });
            });


            $(document).on('click', '.lot-preis-badge', function () {
                if (!confirm('Preis-Eintrag zurücksetzen?')) return;
                $.ajax({
                    url: 'lotPriceUpdated.php', type: 'POST',
                    data: {lot_id: $(this).data('lot-id'), preis_status: 1},
                    success: function (r) {
                        if (r.success) tableTenderLots.ajax.reload(null, false);
                        else alert('Fehler: ' + (r.error || 'Unbekannter Fehler'));
                    },
                    error: function () {
                        alert('Verbindungsfehler.');
                    }
                });
            });

            $(document).on('change', '.lot-kontrolle-checkbox', function () {
                if (!$(this).is(':checked')) return;
                const lotId = $(this).data('lot-id');
                const projektId = $(this).data('projekt-id');
                $.post('update_los_kontrolliert.php', {
                    lot_id: lotId,
                    projekt_id: projektId
                }).done(function (data) {
                    if (data.success) {
                        tableTenderLots.ajax.reload(null, false);
                    } else {
                        alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
                    }
                }).fail(function () {
                    alert('Verbindungsfehler');
                });
            });

// Kontrolle green badge clicked → reset kontrolle_user to NULL
            $(document).on('click', '.kontrolle-badge', function () {
                if (!confirm('Kontrolle zurücksetzen?')) return;
                const lotId = $(this).data('lot-id');
                const projektId = $(this).data('projekt-id');
                $.post('update_los_kontrolliert.php', {
                    lot_id: lotId,
                    projekt_id: projektId,
                    reset: 1
                }).done(function (data) {
                    if (data.success) tableTenderLots.ajax.reload(null, false);
                    else alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
                }).fail(function () {
                    alert('Verbindungsfehler');
                });
            });


            // History
            const LH_FIELDS = [
                {label: 'Los Bezeichnung', altIdx: 4, neuIdx: 3, badgeCls: 'bg-primary'},
                {label: 'Element', altIdx: 7, neuIdx: 8, badgeCls: 'bg-primary'},
                {label: 'Raum', altIdx: 10, neuIdx: 11, badgeCls: 'bg-primary'},
                {label: 'Status', altIdx: 13, neuIdx: 14, badgeCls: 'bg-warning text-dark'},
                {label: 'Lieferdatum', altIdx: 15, neuIdx: 16, badgeCls: 'bg-info text-dark'},
                {label: 'Budget-Position', altIdx: 17, neuIdx: 18, badgeCls: 'bg-success'},
                {label: 'Anzahl', altIdx: 19, neuIdx: 20, badgeCls: 'bg-secondary'},
                {label: 'Kurzbeschreibung', altIdx: 21, neuIdx: 22, badgeCls: 'bg-dark'},
                {label: 'Neu/Bestand', altIdx: 23, neuIdx: 24, badgeCls: 'bg-dark'},
                {label: 'Standort', altIdx: 25, neuIdx: 26, badgeCls: 'bg-dark'},
                {label: 'Verwendung', altIdx: 27, neuIdx: 28, badgeCls: 'bg-dark'},
                {label: 'Anschaffung', altIdx: 29, neuIdx: 30, badgeCls: 'bg-success'},
                {label: 'Internes Los', altIdx: 31, neuIdx: 32, badgeCls: 'bg-danger'},
                {label: 'GHG', altIdx: 33, neuIdx: 34, badgeCls: 'bg-danger'},
                {label: 'GUG', altIdx: 35, neuIdx: 36, badgeCls: 'bg-danger'},
                {label: 'Gewerk', altIdx: 37, neuIdx: 38, badgeCls: 'bg-danger'},
                {label: 'Variante', altIdx: 39, neuIdx: 40, badgeCls: 'bg-light text-dark border'},
            ];

            const lh_norm = v => (v === null || v === undefined || v === '') ? '' : String(v);

            const lh_statusBadges = {
                '0': "<span class='badge bg-danger'>Offen</span>",
                '1': "<span class='badge bg-success'>Fertig</span>",
                '2': "<span class='badge bg-primary'>Wartend</span>",
            };
            const lh_variantLabels = {'1': 'A', '2': 'B', '3': 'C', '4': 'D', '5': 'E'};

            function lh_renderVal(f, raw) {
                const v = lh_norm(raw);
                if (f.altIdx === 13) return lh_statusBadges[v] ?? raw;
                if (f.altIdx === 23) return v === '1' ? 'Ja' : v === '0' ? 'Nein' : raw;
                if (f.altIdx === 39) return lh_variantLabels[v] ?? raw;
                return raw;
            }

            function lh_smartLabel(f, row) {
                const a = lh_norm(row[f.altIdx]), n = lh_norm(row[f.neuIdx]);
                if (f.altIdx === 4) {
                    if (a === '' && n !== '') return 'Zu Los hinzugefügt';
                    if (a !== '' && n === '') return 'Von Los entfernt';
                }
                if (f.altIdx === 19) {
                    if ((a === '' || a === '0') && n !== '' && n !== '0') return 'Element hinzugefügt';
                    if (a !== '' && a !== '0' && (n === '' || n === '0')) return 'Element entfernt';
                }
                return f.label;
            }

            function lh_badges(row) {
                const changed = LH_FIELDS.filter(f => lh_norm(row[f.altIdx]) !== lh_norm(row[f.neuIdx]));
                if (!changed.length) return '<span class="text-muted small">–</span>';
                return changed.map(f =>
                    `<span class="badge rounded-pill ${f.badgeCls} me-1" style="font-size:0.7em">${lh_smartLabel(f, row)}</span>`
                ).join('');
            }

            function lh_buildDetailHtml(row) {
                const d = row;
                const changed = LH_FIELDS.filter(f => lh_norm(d[f.altIdx]) !== lh_norm(d[f.neuIdx]));
                const unchanged = LH_FIELDS.filter(f =>
                    lh_norm(d[f.altIdx]) === lh_norm(d[f.neuIdx]) &&
                    lh_norm(d[f.altIdx]) !== '' &&
                    f.altIdx !== 7 && f.altIdx !== 10  // Element/Raum already in Zuordnung
                );

                const disp = v => (v !== null && v !== undefined && v !== '') ? String(v) : null;
                const buildRow = (label, alt, neu) => {
                    const a = disp(alt), n = disp(neu);
                    const changed = (a ?? '') !== (n ?? '');
                    if (a === null && n === null) return '';
                    const aH = a ?? '<em class="text-muted">–</em>';
                    const nH = n ?? '<em class="text-muted">–</em>';
                    if (neu === undefined) return `<tr><td class="fw-semibold text-muted small" colspan="2">${label}: ${aH}</td></tr>`;
                    const badge = changed ? `<span class="badge bg-warning text-dark ms-1" style="font-size:0.6em">geändert</span>` : '';
                    const nRender = changed ? `<span class="text-success fw-bold">${nH}</span>` : nH;
                    return `<tr class="${changed ? 'table-warning' : ''}">
            <td class="fw-semibold small">${label}${badge}</td>
            <td>${aH}</td><td>${nRender}</td></tr>`;
                };

                // Format ISO timestamp for display
                const tsDisplay = (() => {
                    if (!d[1]) return '–';
                    const [datePart, timePart = ''] = d[1].split(' ');
                    const [y, m, dd] = datePart.split('-');
                    return `${dd}.${m}.${y} ${timePart}`;
                })();

                let html = `<div class="border rounded p-2 mb-3 bg-light">
        <span class="me-3"><i class="fas fa-user me-1 text-muted"></i><strong>${d[2] ?? '–'}</strong></span>
        <span class="me-3"><i class="fas fa-clock me-1 text-muted"></i>${tsDisplay}</span>
        <span class="text-muted small">ID: <code>${d[0]}</code></span>
        <div class="mt-1">${lh_badges(row)}</div>
    </div>`;

                html += `<table class="table table-sm table-bordered mb-0">
        <thead class="table-dark"><tr>
            <th style="width:28%">Feld</th><th>Alt</th>
            <th>Neu <span class="badge bg-warning text-dark" style="font-size:0.6em">geändert</span></th>
        </tr></thead><tbody>`;

                // Zuordnung
                html += `<tr class="table-secondary"><td colspan="3" class="fw-bold small py-1">
        <i class="fas fa-map-marker-alt me-1"></i>Zuordnung</td></tr>`;
                html += buildRow('Aktuelles Los', d[3]);
                html += buildRow('Element', d[9]);
                html += buildRow('Raum', d[12]);

                // Geändert
                html += `<tr class="table-secondary"><td colspan="3" class="fw-bold small py-1">
        <i class="fas fa-edit me-1"></i>Geändert
        <span class="badge bg-danger ms-1" style="font-size:0.6em">${changed.length}</span>
        </td></tr>`;
                if (changed.length) {
                    changed.forEach(f => html += buildRow(f.label, lh_renderVal(f, d[f.altIdx]), lh_renderVal(f, d[f.neuIdx])));
                } else {
                    html += `<tr><td colspan="3" class="text-muted fst-italic small">Kein bekanntes Feld hat sich geändert.</td></tr>`;
                }

                // Kontext
                if (unchanged.length) {
                    html += `<tr class="table-secondary"><td colspan="3" class="fw-bold small py-1">
            <i class="fas fa-info-circle me-1"></i>Kontext (unverändert)</td></tr>`;
                    unchanged.forEach(f => {
                        html += `<tr>
                <td class="small fw-semibold" style="color:#999">${f.label}</td>
                <td colspan="2" class="small" style="color:#999">${lh_renderVal(f, d[f.altIdx])}</td></tr>`;
                    });
                }

                html += '</tbody></table>';
                return html;
            }

            $(document).on('click', 'button[value="Los Historie"]', function () {
                const losID = $(this).data('los-id');
                const losName = $(this).data('los-name');

                $('#losHistorieModalTitle').text(losName);

                $('#losHistorieModalBody').html(`<div class="text-center p-4"><div class="spinner-border" role="status"></div></div>`);

                new bootstrap.Modal(document.getElementById('losHistorieModal')).show();

                $.ajax({
                    url: 'get_los_aenderungshistorie.php',
                    type: 'POST',
                    data: {losID},
                    success: function (resp) {
                        const rows = resp.data ?? [];
                        if (!rows.length) {
                            $('#losHistorieModalBody').html(`<div class="alert alert-info">Keine Änderungen gefunden.</div>`);
                            return;
                        }
                        // Sort newest first (ISO string sorts correctly)
                        rows.sort((a, b) => (b[1] ?? '').localeCompare(a[1] ?? ''));

// Remove consecutive rows where timestamp+user+all fields are identical
                        const deduped = rows.filter((row, i) => {
                            if (i === 0) return true;
                            const prev = rows[i - 1];
                            // Compare timestamp, user, and all alt/neu field indices
                            return !(row[1] === prev[1] && row[2] === prev[2] &&
                                [3, 4, 7, 8, 10, 11, 13, 14, 15, 16, 17, 18,
                                    19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                                    31, 32, 33, 34, 35, 36, 37, 38, 39, 40]
                                    .every(idx => String(row[idx] ?? '') === String(prev[idx] ?? '')));
                        });

                        $('#losHistorieModalBody').html(
                            deduped.map(row => `<div class="mb-4">${lh_buildDetailHtml(row)}</div>`).join('<hr class="my-2">')
                        );
                    },
                    error: function () {
                        $('#losHistorieModalBody').html(`<div class="alert alert-danger">Fehler beim Laden der Historie.</div>`);
                    }
                });
            });


        });
    </script>
</body>
</html>
