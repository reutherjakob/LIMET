<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Lose &amp; Elemente – Übersicht</title>
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
</head>

<body id="bodyLosElemente">
<?php
require_once 'utils/_utils.php';
init_page_serversides("x");
?>

<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-xxl-12">
            <div class="mt-4 card">
                <div class="card-header">
                    <div class="row g-2">
                        <div class="col-12 col-lg-5 d-flex align-items-center" id="cardHeaderLeft">
                            <span class="text-nowrap"><strong>LOSE &amp; ELEMENTE</strong>&emsp;</span>
                        </div>
                        <div class="col-12 col-lg-7 d-flex flex-wrap align-items-center justify-content-lg-end gap-2">

                            <div>
                                <label for="dateSelect" class="visually-hidden">Versand ab</label>
                                <input type="date" id="dateSelect" name="dateSelect"
                                       class="form-control form-control-sm w-auto"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Ab welchem Versanddatum Lose laden?"/>
                            </div>

                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="chkNurNeu" checked>
                                <label class="form-check-label small" for="chkNurNeu"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Nur Elemente mit Neu/Bestand = Neu">nur Neu</label>
                            </div>

                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="chkNurStandort" checked>
                                <label class="form-check-label small" for="chkNurStandort"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Nur Hauptstandort (Standort = 1) – verhindert Doppelzählung">nur Standort</label>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnToggleGroup"
                                    data-bs-toggle="tooltip" data-bs-title="Gruppierung nach Los ein/aus">
                                <i class="fas fa-layer-group"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-1 py-1 m-1">
                    <div class="row g-2 mb-2">
                        <div class="col-6 col-md-3">
                            <div class="card border-light">
                                <div class="card-body py-2">
                                    <div class="small text-muted">Lose</div>
                                    <div class="fs-5 fw-bold" id="statLose">–</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-light">
                                <div class="card-body py-2">
                                    <div class="small text-muted">Elementpositionen</div>
                                    <div class="fs-5 fw-bold" id="statPos">–</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-light">
                                <div class="card-body py-2">
                                    <div class="small text-muted">Stück gesamt</div>
                                    <div class="fs-5 fw-bold" id="statStk">–</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-light">
                                <div class="card-body py-2">
                                    <div class="small text-muted">PP</div>
                                    <div class="fs-5 fw-bold" id="statSumme">–</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table id="tableLosElemente"
                           class="table table-sm table-striped compact border border-light border-1 w-100">
                        <thead>
                        <tr>
                            <th>Projekt</th>
                            <th>Los</th>
                            <th>LosNr</th>
                            <th>Losbezeichnung</th>
                            <th>Versand</th>
                            <th>Verfahren</th>
                            <th>Status</th>
                            <th>Auftragnehmer</th>
                            <th>ElementID</th>
                            <th>Element</th>
                            <th>Var</th>
                            <th>N/B</th>
                            <th class="text-end">Stk</th>
                            <th class="text-end">Räume</th>
                            <th>Raumliste</th>
                            <th class="text-end">EP</th>
                            <th class="text-end">PP</th>
                            <th class="text-end">Vergabesumme</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="utils/_utils.js"></script>
    <script charset="utf-8">
        var tableLosElemente;

        const eur = new Intl.NumberFormat('de-DE', {style: 'currency', currency: 'EUR'});
        const num = new Intl.NumberFormat('de-DE');
        const money = d => (d === null || d === undefined || d === 0) ? '' : eur.format(d);

        $(document).ready(function () {
            $('#dateSelect').val('2025-01-01');

            tableLosElemente = new DataTable('#tableLosElemente', {
                ajax: {
                    url: 'getLosElementeUebersicht.php',
                    type: 'POST',
                    data: function (d) {
                        d.datum = $('#dateSelect').val();
                        d.nurNeu = $('#chkNurNeu').is(':checked') ? 1 : 0;
                        d.nurStandort = $('#chkNurStandort').is(':checked') ? 1 : 0;
                        return d;
                    }
                },
                columns: [
                    {data: 'projekt'},
                    {data: 'losKey'},
                    {data: 'losNr'},
                    {data: 'losBez'},
                    {data: 'versand'},
                    {data: 'verfahren'},
                    {data: 'status'},
                    {data: 'auftragnehmer'},
                    {data: 'elementID'},
                    {data: 'element'},
                    {data: 'variante'},
                    {data: 'neubestand'},
                    {data: 'anzahl', className: 'text-end'},
                    {data: 'anzRaeume', className: 'text-end'},
                    {data: 'raeume', visible: false},
                    {data: 'einzelkosten', className: 'text-end', render: (d, t) => t === 'display' ? money(d) : d},
                    {data: 'schaetzsumme', className: 'text-end', render: (d, t) => t === 'display' ? money(d) : d},
                    {data: 'vergabesumme', className: 'text-end', render: (d, t) => t === 'display' ? money(d) : d}
                ],
                columnDefs: [
                    {targets: [1], visible: false},          // Gruppierungsspalte
                    {targets: [2, 3], visible: false}         // in Gruppenzeile enthalten
                ],
                rowGroup: {
                    dataSrc: 'losKey',
                    startRender: function (rows, group) {
                        const d = rows.data()[0];
                        const stk = rows.data().pluck('anzahl').reduce((a, b) => a + b, 0);
                        const sum = rows.data().pluck('schaetzsumme').reduce((a, b) => a + b, 0);
                        return $('<tr class="table-light"/>').append(
                            `<td colspan="18">
                                <strong>${d.projekt}</strong>
                                <span class="badge bg-dark ms-2">${d.losNr}</span>
                                <span class="ms-2">${d.losBez}</span>
                                ${d.verfahren}
                                ${d.status}
                                <span class="ms-2 text-muted small">
                                    <i class="fas fa-truck me-1"></i>${d.auftragnehmer}
                                </span>
                                <span class="float-end small">
                                    <span class="badge bg-secondary me-1">${rows.count()} Positionen</span>
                                    <span class="badge bg-secondary me-1">${num.format(stk)} Stk</span>
                                    <span class="badge bg-success">${eur.format(sum)}</span>
                                </span>
                             </td>`
                        );
                    }
                },
                order: [[1, 'asc'], [8, 'asc']],
                paging: true,
                pageLength: 50,
                lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'Alle']],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                    decimal: ',',
                    thousands: '.',
                    searchPlaceholder: 'Suche..',
                    search: "",
                    lengthMenu: "_MENU_",
                    searchBuilder: {title: '', button: ''},
                    buttons: {excel: ''}
                },
                buttons: [
                    {
                        extend: 'colvis', className: "btn btn-light btn-outline-dark fas fa-eye", text: '',
                        attr: {'data-bs-toggle': 'tooltip', 'data-bs-title': 'Spalten ein/ausblenden'}
                    },
                    {
                        extend: 'excel', className: "btn btn-light btn-outline-dark fas fa-file-excel", text: '',
                        title: 'Losliste_Elemente',
                        exportOptions: {columns: ':visible, :hidden'},
                        attr: {'data-bs-toggle': 'tooltip', 'data-bs-title': 'Download als Excel'}
                    },
                    {
                        extend: 'searchBuilder', className: "btn btn-light btn-outline-dark fas fa-filter", text: '',
                        attr: {'data-bs-toggle': 'tooltip', 'data-bs-title': 'Filter erstellen'}
                    }
                ],
                layout: {
                    topStart: null, topEnd: null,
                    bottomStart: ['pageLength', 'info'],
                    bottomEnd: ['paging', 'search', 'buttons']
                },
                initComplete: function () {
                    const target = document.getElementById('cardHeaderLeft');
                    Array.from(document.getElementsByClassName('dt-buttons'))
                        .forEach(el => target.appendChild(el));

                    $('.dt-search label').remove();
                    $('.dt-search').children()
                        .removeClass('form-control form-control-sm')
                        .addClass('btn btn-sm btn-outline-secondary')
                        .appendTo('#cardHeaderLeft');

                    Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        .forEach(el => new bootstrap.Tooltip(el, {delay: {show: 10, hide: 0}}));
                }
            });

            // Kennzahlen aktualisieren (berücksichtigt aktive Filter/Suche)
            function updateStats() {
                const rows = tableLosElemente.rows({search: 'applied'}).data();
                const lose = new Set();
                let stk = 0, sum = 0;
                rows.each(function (d) {
                    lose.add(d.losID);
                    stk += d.anzahl;
                    sum += d.schaetzsumme;
                });
                $('#statLose').text(num.format(lose.size));
                $('#statPos').text(num.format(rows.length));
                $('#statStk').text(num.format(stk));
                $('#statSumme').text(eur.format(sum));
            }

            tableLosElemente.on('draw', updateStats);

            function reload() {
                tableLosElemente.ajax.reload(null, false);
            }

            $('#dateSelect, #chkNurNeu, #chkNurStandort').on('change', reload);

            let grouped = true;
            $('#btnToggleGroup').on('click', function () {
                grouped = !grouped;
                tableLosElemente.rowGroup().enable(grouped);
                tableLosElemente.column(2).visible(!grouped);
                tableLosElemente.column(3).visible(!grouped);
                $(this).toggleClass('btn-outline-secondary btn-secondary');
                tableLosElemente.draw();
            });
        });
    </script>
</body>
</html>
