<?php
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Bestand</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">

    <!-- Rework 2025 CDNs -->
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

    <style>
        /* Zeilen ohne hinterlegte Bestandsdaten dezent markieren */
        #tableBestandsElemente tbody tr.ohne-bestandsdaten > td {
            background-color: #fff8e1;
        }
    </style>
</head>

<body style="height:100%">
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-4 card">

        <div class="card-header">
            <div class="row">
                <div class="col-xxl-6"><b>Elemente im Bestand</b></div>
                <div class="col-xxl-6 d-flex flex-nowrap justify-content-end align-items-center gap-2" id="CardHeader">

                    <div class="form-check form-switch d-flex align-items-center mb-0 text-nowrap"
                         title="Zeigt zusätzlich alle Bestands-Elemente, zu denen noch keine Bestandsdaten erfasst wurden">
                        <input class="form-check-input mt-0" type="checkbox" role="switch" id="switchOhneBestandsdaten">
                        <label class="form-check-label ms-2" for="switchOhneBestandsdaten">inkl. ohne
                            Bestandsdaten</label>
                    </div>

                    <div id="dateSelectContainer">
                        <label for="dateSelect" class="visually-hidden">Änderungsdatum</label>
                        <input type="date" id="dateSelect" name="dateSelect" class="form-control form-control-sm"
                               title="Datum am Bericht"/>
                    </div>

                    <button type="button" class="btn btn-outline-dark btn-sm" value="createBestandsPDF">
                        <i class="far fa-file-pdf"></i> Bestands-PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-striped table-bordered table-sm table-hover border border-light border-5"
                   id="tableBestandsElemente" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>
                        <div class="d-flex justify-content-center align-items-center" data-bs-toggle="tooltip"
                             title="Element ID"><i class="fas fa-fingerprint"></i></div>
                    </th>
                    <th>Element</th>
                    <th>Inventarnr</th>
                    <th>Seriennr</th>
                    <th>Anschaffungsjahr</th>
                    <th>Gerät</th>
                    <th>Raumnr</th>
                    <th>Raum</th>
                    <th>Raumbereich</th>
                    <th>
                        <div class="d-flex justify-content-center align-items-center" data-bs-toggle="tooltip"
                             title="Standort"><i class="fab fa-periscope"></i></div>
                    </th>
                    <th>
                        <div class="d-flex justify-content-center align-items-center" data-bs-toggle="tooltip"
                             title="Kosten"><i class="fas fa-euro-sign"></i></div>
                    </th>
                    <th>Kosten</th><!-- unformatiert -->
                    <th>
                        <div class="d-flex justify-content-center align-items-center" data-bs-toggle="tooltip"
                             title="Kommentar"><i class="far fa-comments"></i></div>
                    </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
</body>
<script src="utils/_utils.js"></script>
<script>
    $(document).ready(function () {

        // null-Werte sauber ausgeben + escapen
        function txt(v) {
            return (v === null || v === undefined) ? '' : $('<div>').text(v).html();
        }

        function alleAktiv() {
            return $('#switchOhneBestandsdaten').is(':checked') ? 1 : 0;
        }

        var table = new DataTable('#tableBestandsElemente', {
            ajax: {
                url: 'api_bestand_elemente.php',
                dataSrc: 'data',
                data: function (d) {
                    d.alle = alleAktiv();
                },
                error: function (xhr) {
                    console.error('Bestandsdaten laden fehlgeschlagen', xhr.status, xhr.responseText);
                    alert('Bestandsdaten konnten nicht geladen werden (HTTP ' + xhr.status + ').\n' +
                        'Details in der Browser-Konsole, oder api_bestand_elemente.php?debug=1 direkt aufrufen.');
                }
            },
            columns: [
                {data: 'id', visible: false, searchable: false},                        // 0
                {data: 'ElementID', render: txt},                                       // 1
                {data: 'Bezeichnung', render: txt},                                     // 2
                {data: 'Inventarnummer', render: txt},                                  // 3
                {data: 'Seriennummer', render: txt},                                    // 4
                {data: 'Anschaffungsjahr', render: txt},                                // 5
                {data: 'Geraet', render: txt},                                          // 6
                {data: 'Raumnr', render: txt},                                          // 7
                {data: 'Raumbezeichnung', render: txt},                                 // 8
                {data: 'RaumbereichNutzer', render: txt},                               // 9
                {data: 'AktuellerOrt', render: txt},                                    // 10
                {data: 'KostenFormatiert', render: txt},                                // 11
                {data: 'Kosten', visible: false, searchable: false},                    // 12 unformatiert
                {                                                                       // 13 Kommentar
                    data: 'Kurzbeschreibung',
                    orderable: false,
                    render: function (d) {
                        if (!d) {
                            return '';
                        }
                        return "<button type='button' class='btn btn-sm btn-outline-dark' " +
                            "data-bs-toggle='popover' data-bs-placement='top' " +
                            "data-bs-content=\"" + txt(d) + "\" title='Kommentar'>" +
                            "<i class='fa fa-comment'></i></button>";
                    }
                }
            ],
            paging: true,
            pagingType: 'simple',
            lengthChange: true,
            pageLength: 25,
            searching: true,
            info: true,
            order: [[1, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.',
                search: "",
                searchPlaceholder: ""
            },
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="far fa-file-excel"></i> Excel',
                    exportOptions: {
                        // ID (0) und formatierte Kosten (11) raus, Rohwert (12) bleibt drin
                        columns: function (idx) {
                            return idx !== 0 && idx !== 11;
                        }
                    }
                }
            ],
            mark: true,
            layout: {
                topStart: "buttons",
                topEnd: "search",
                bottomStart: "info",
                bottomEnd: ["pageLength", "paging"]
            },
            rowCallback: function (row, data) {
                $(row).toggleClass('ohne-bestandsdaten', data.hatBestandsdaten === 0);
            },
            drawCallback: function () {
                // Popover je Button nur einmal initialisieren
                $('#tableBestandsElemente [data-bs-toggle="popover"]').each(function () {
                    if (!bootstrap.Popover.getInstance(this)) {
                        new bootstrap.Popover(this);
                    }
                });
            },
            initComplete: function () {
                $('.dt-buttons').children().addClass("btn-sm").appendTo('#CardHeader');
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm")
                    .addClass("btn btn-sm btn-outline-dark").appendTo('#CardHeader');
            }
        });

        // Switch -> Daten neu holen (Sortierung/Suche bleiben erhalten)
        $('#switchOhneBestandsdaten').on('change', function () {
            table.ajax.reload(null, false);
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('[data-bs-toggle="popover"]').length &&
                !$(e.target).closest('.popover').length) {
                $('[data-bs-toggle="popover"]').popover('hide');
            }
        });

        $("button[value='createBestandsPDF']").click(function () {
            var datum = $('#dateSelect').val();
            if (!datum) {
                datum = new Date().toISOString().slice(0, 10); // aktuelles Datum YYYY-MM-DD
            }
            window.open("PDFs/pdf_createBestandPDF.php?datum=" + encodeURIComponent(datum) +
                "&alle=" + alleAktiv());
        });
    });
</script>
</html>