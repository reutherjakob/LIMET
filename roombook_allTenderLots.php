<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Losverwaltung</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
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

    <?php require "modal_showLotWorkflow.php"; ?>
    <?php require "modal_showLotElements.php"; ?>

    <script src="utils/_utils.js"></script>
    <script charset="utf-8">
        var tableTenderLots;

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

        $(document).ready(function () {
            $('#dateSelect').val('2024-01-01');
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
                    {targets: [0, 1], visible: false, searchable: false, sortable: false}, {
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
                        .appendTo('#LoseCardHeaderSub');

                    $('#dateSelect').on('change', debounce(function () {
                        if ($(this).val() !== $(this).data('oldValue')) { // Nur bei neuem Datum
                            $(this).data('oldValue', $(this).val());
                            tableTenderLots.ajax.reload(null, false);
                        }
                    }, 500));

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
            $(document).on("click", "button[value='Los Workflow']", function () {
                var ID = this.id;
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
                var $checkbox = $(this);
                var lotId = $checkbox.data('lot-id');
                var projektId = $checkbox.data('projekt-id');
                var newStatus = $checkbox.is(':checked') ? 1 : 0;
                var $label = $checkbox.siblings('.form-check-label');
                $label.html('<span class="spinner-border spinner-border-sm me-1"></span>Laden...');
                $.ajax({
                    url: 'lotPriceUpdated.php',
                    type: 'POST',
                    data: {
                        lot_id: lotId,
                        projekt_id: projektId,
                        preis_status: newStatus
                    },
                    success: function (response) {
                        if (response.success) {
                            tableTenderLots.ajax.reload(null, false);

                        } else {
                            alert('Fehler beim Speichern: ' + (response.error || 'Unbekannter Fehler'));
                        }
                    },
                    error: function () {
                        alert('Verbindungsfehler. Bitte versuchen Sie es erneut.');
                    }
                });
            });

            $(document).on('click', '.kontrolle-btn', function () {
                if (this.disabled) return;
                const $btn = $(this); // Button-Referenz speichern
                const projektId = $btn.data('projekt-id');
                const lotId = $btn.data('lot-id');
                $.post('update_los_kontrolliert.php', {
                    projekt_id: projektId,
                    lot_id: lotId
                }).done(function (data) {
                    if (data.success) {
                        makeToaster("Kontrolliert", true);
                        $btn.removeClass('btn-outline-success')
                            .addClass('btn-success')
                            .prop('disabled', true)
                            .attr('title', 'Kontrolliert.')
                            .text("Erledigt");
                        tableTenderLots.ajax.reload(null, false);
                    } else {
                        alert('Fehler: ' + (data.error || 'Unbekannter Fehler'));
                    }
                }).fail(function () {
                    alert('Verbindungsfehler');
                });
            });
        });
    </script>
</body>
</html>
