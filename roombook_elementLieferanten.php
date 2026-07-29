<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Potenzielle Auftragnehmer je Element</title>
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

<body id="bodyElementLieferanten">
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
                            <span class="text-nowrap"><strong>LIEFERANTEN JE ELEMENT</strong>&emsp;</span>
                        </div>
                        <div class="col-12 col-lg-7 d-flex flex-wrap align-items-center justify-content-lg-end gap-2">

                            <div class="btn-group btn-group-sm" role="group" aria-label="Quelle">
                                <input type="radio" class="btn-check" name="quelle" id="qAlle" value="alle" checked>
                                <label class="btn btn-outline-secondary" for="qAlle">Alle</label>

                                <input type="radio" class="btn-check" name="quelle" id="qKatalog" value="katalog">
                                <label class="btn btn-outline-primary" for="qKatalog"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Nur gepflegte Gerät-Lieferant-Zuordnung">Katalog</label>

                                <input type="radio" class="btn-check" name="quelle" id="qHistorie" value="historie">
                                <label class="btn btn-outline-warning" for="qHistorie"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Nur aus früheren Vergaben abgeleitet">Historie</label>
                            </div>

                            <div>
                                <label for="dateSelect" class="visually-hidden">Vergaben ab</label>
                                <input type="date" id="dateSelect" name="dateSelect"
                                       class="form-control form-control-sm w-auto"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Vergaben ab welchem Versanddatum berücksichtigen?"/>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnToggleView"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Sortierung umschalten: Element ⇄ Lieferant">
                                <i class="fas fa-exchange-alt"></i> <span id="viewLabel">nach Element</span>
                            </button>

                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#infoModal"
                                    title="Hilfe / Erläuterungen zu dieser Auswertung">
                                <i class="fas fa-info-circle"></i> Info
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-1 py-1 m-1">
                    <table id="tableElementLieferanten"
                           class="table table-sm table-striped compact border border-light border-1 w-100">
                        <thead>
                        <tr>
                            <th>Gruppe</th>
                            <th>Rang</th>
                            <th>ElementID</th>
                            <th>Element</th>
                            <th>Lieferant</th>
                            <th>Quelle</th>
                            <th>Geräte</th>
                            <th>Hersteller</th>
                            <th>Projektliste</th>
                            <th>LosNummern</th>
                            <th>Verfahren</th>
                            <th>Anschrift</th>
                            <th>PLZ</th>
                            <th>Land</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalTitle">
                        <i class="fas fa-info-circle me-2"></i>Lieferanten je Element – Erläuterung
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>

                <div class="modal-body">

                    <p class="lead">
                        Die Auswertung beantwortet eine einzige Frage: <em>Wer könnte dieses Element liefern?</em>
                        Zu jedem Element werden alle Firmen aufgelistet, für die es dafür einen belastbaren
                        Anhaltspunkt gibt – entweder aus den Stammdaten oder aus tatsächlich erteilten Aufträgen.
                        Gedacht ist die Liste als Ausgangspunkt für den Zuschnitt einer neuen Losliste und für die
                        Bieteransprache.
                    </p>

                    <div class="accordion" id="infoAccordion">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#infoQuellen" aria-expanded="true">
                                    Woher kommen die Lieferanten?
                                </button>
                            </h2>
                            <div id="infoQuellen" class="accordion-collapse collapse show"
                                 data-bs-parent="#infoAccordion">
                                <div class="accordion-body">
                                    <dl class="row mb-3">
                                        <dt class="col-sm-3"><span class="badge bg-primary">Katalog</span></dt>
                                        <dd class="col-sm-9">
                                            Gepflegte Zuordnung Gerät&nbsp;→&nbsp;Lieferant. Das Element ist über die
                                            Gerätezuordnung an der Raum-Element-Verknüpfung oder über die Bestandsdaten
                                            mit einem Gerät verbunden. <strong>Belastbarste Quelle</strong>, weil sie
                                            bewusst gepflegt wurde – sie sagt allerdings nichts darüber aus, ob die
                                            Firma je einen Auftrag von uns erhalten hat.
                                        </dd>

                                        <dt class="col-sm-3"><span class="badge bg-warning text-dark">Historie</span>
                                        </dt>
                                        <dd class="col-sm-9">
                                            Abgeleitet: Der Lieferant hat bereits ein Los erhalten, das dieses Element
                                            enthielt. Kein gepflegter Stammdatensatz, aber ein realer Nachweis. Achtung:
                                            Ein Los umfasst meist viele Elemente – die Zuordnung ist deshalb ein
                                            Indiz, keine Bestätigung, dass die Firma genau dieses Element als
                                            Kernkompetenz hat.
                                        </dd>

                                        <dt class="col-sm-3"><span class="badge bg-success">beides</span></dt>
                                        <dd class="col-sm-9">
                                            Katalogzuordnung vorhanden <em>und</em> bereits beauftragt –
                                            die stärksten Kandidaten für die neue Losliste. Diese Zeilen stehen
                                            innerhalb eines Elements immer oben.
                                        </dd>
                                    </dl>
                                    <p class="mb-0 small text-muted">
                                        Über die Schaltergruppe <em>Alle / Katalog / Historie</em> in der Kopfzeile
                                        lässt sich die Auswertung auf eine Quelle einschränken. Die Abfrage wird dabei
                                        serverseitig neu gestellt, nicht nur die Tabelle gefiltert.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#infoBedienung" aria-expanded="false">
                                    Bedienung: Sortierung, Datum, Werkzeuge
                                </button>
                            </h2>
                            <div id="infoBedienung" class="accordion-collapse collapse" data-bs-parent="#infoAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="fas fa-exchange-alt me-2 text-muted"></i>
                                            <strong>Sortierung umschalten</strong> – wechselt zwischen
                                            <em>nach Element</em> (welche Firmen kommen für dieses Element infrage?)
                                            und <em>nach Lieferant</em> (welche Elemente deckt diese Firma ab?).
                                            Die Datenbasis bleibt dieselbe, nur die Reihenfolge ändert sich.
                                        </li>
                                        <li class="list-group-item">
                                            <i class="far fa-calendar-alt me-2 text-muted"></i>
                                            <strong>Vergaben ab</strong> – Stichtag für das Versanddatum der Lose.
                                            Er wirkt <strong>nur auf die Historie</strong>: Katalogzuordnungen bleiben
                                            unabhängig vom Datum bestehen. Ein späterer Stichtag zeigt also, welche
                                            Firmen zuletzt noch aktiv waren.
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-eye me-2 text-muted"></i>
                                            <strong>Spalten ein-/ausblenden</strong> – standardmäßig ausgeblendet sind
                                            Hersteller, Projektliste, LosNummern, Verfahren sowie Anschrift, PLZ und
                                            Land. Für die Bieteransprache lohnt es sich, diese einzublenden.
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-filter me-2 text-muted"></i>
                                            <strong>Filter erstellen</strong> – mehrstufige Bedingungen, z.&nbsp;B.
                                            „Quelle = beides UND Land = DE“.
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-file-excel me-2 text-muted"></i>
                                            <strong>Excel-Export</strong> – exportiert die aktuell gefilterte Sicht
                                            inklusive der eingeblendeten Spalten.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#infoSpalten" aria-expanded="false">
                                    Was bedeuten die Spalten?
                                </button>
                            </h2>
                            <div id="infoSpalten" class="accordion-collapse collapse" data-bs-parent="#infoAccordion">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th style="width:22%">Spalte</th>
                                                <th>Bedeutung</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><strong>Quelle</strong></td>
                                                <td>Katalog, Historie oder beides – siehe erster Abschnitt.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Geräte</strong></td>
                                                <td>Die konkreten Geräte, über die die Verbindung Element ↔ Lieferant
                                                    zustande kommt. Leer bei reinen Historie-Treffern.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Hersteller</strong></td>
                                                <td>Ausblendbar. Hersteller der zugeordneten Geräte.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Projektliste</strong></td>
                                                <td>Ausblendbar. Namen der Projekte, aus denen die Lose stammen.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>LosNummern, Verfahren</strong></td>
                                                <td>Ausblendbar. Die konkreten Lose der Historie und die
                                                    Vergabeverfahren, über die sie gelaufen sind.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Anschrift, PLZ, Land</strong></td>
                                                <td>Ausblendbar. Stammdaten aus dem Lieferantenverzeichnis, für die
                                                    Ansprache und den Excel-Export.
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#infoGrenzen" aria-expanded="false">
                                    Abgrenzungen und Fallstricke
                                </button>
                            </h2>
                            <div id="infoGrenzen" class="accordion-collapse collapse" data-bs-parent="#infoAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-3">
                                        <li>In der <strong>Historie</strong> werden nur Zuordnungen mit
                                            <code>Standort = 1</code> berücksichtigt. Für den <strong>Katalog</strong>
                                            gilt diese Einschränkung nicht.
                                        </li>
                                        <li>Die Liste zeigt <em>ob</em> eine Verbindung besteht, nicht wie stark sie
                                            ist – Stückzahlen und Auftragsvolumen bleiben bewusst außen vor.
                                        </li>
                                        <li>Das Datumsfeld wirkt nur auf die Historie, nicht auf den Katalog.</li>
                                        <li>Eine Firma kann zwischenzeitlich erloschen, umfirmiert oder übernommen
                                            worden sein – Adressdaten stammen aus dem Lieferantenstamm und werden hier
                                            nicht geprüft.
                                        </li>
                                    </ul>
                                    <div class="alert alert-secondary mb-0">
                                        <i class="fas fa-balance-scale me-2"></i>
                                        Die Liste ist eine <strong>Recherchehilfe</strong>. Sie ersetzt weder die
                                        Markterkundung noch die vergaberechtliche Prüfung und begründet keinen
                                        Anspruch auf Beteiligung. Wer hier nicht auftaucht, ist nicht ungeeignet –
                                        er ist nur in unseren Daten noch nicht in Erscheinung getreten.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                </div>
            </div>
        </div>
    </div>

    <script src="utils/_utils.js"></script>
    <script charset="utf-8">
        var tableEL;

        const quelleBadge = {
            'beides':   '<span class="badge bg-success">beides</span>',
            'Katalog':  '<span class="badge bg-primary">Katalog</span>',
            'Historie': '<span class="badge bg-warning text-dark">Historie</span>'
        };

        $(document).ready(function () {
            $('#dateSelect').val('2020-01-01');

            let groupByElement = true;

            tableEL = new DataTable('#tableElementLieferanten', {
                ajax: {
                    url: 'getElementLieferanten.php',
                    type: 'POST',
                    data: function (d) {
                        d.datum = $('#dateSelect').val();
                        d.quelle = $('input[name="quelle"]:checked').val();
                        return d;
                    }
                },
                columns: [
                    /*  0 */ {data: d => groupByElement ? d.elementKey : d.lieferant, visible: false},
                    /*  1 */ {data: 'quelleRank', visible: false},
                    /*  2 */ {data: 'elementID'},
                    /*  3 */ {data: 'element'},
                    /*  4 */ {data: 'lieferant'},
                    /*  5 */ {data: 'quelle', render: (d, t) => t === 'display' ? (quelleBadge[d] ?? d) : d},
                    /*  6 */ {data: 'geraete'},
                    /*  7 */ {data: 'hersteller', visible: false},
                    /*  8 */ {data: 'projekte', visible: false},
                    /*  9 */ {data: 'lose', visible: false},
                    /* 10 */ {
                        data: 'verfahren', visible: false,
                        render: (d, t) => t === 'display' && d
                            ? d.split(', ').map(v => `<span class="badge bg-light text-dark border me-1">${v}</span>`).join('')
                            : d
                    },
                    /* 11 */ {data: 'anschrift', visible: false},
                    /* 12 */ {data: 'plz', visible: false},
                    /* 13 */ {data: 'land', visible: false}
                ],
                order: [[0, 'asc'], [1, 'asc'], [4, 'asc']],
                paging: true,
                pageLength: 50,
                lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'Alle']],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                    searchPlaceholder: 'Element, Lieferant, Gerät..',
                    search: "",
                    lengthMenu: "_MENU_",
                    searchBuilder: {title: '', button: ''},
                    buttons: {excel: ''}
                },
                buttons: [
                    {
                        extend: 'colvis', className: "btn btn-light btn-outline-dark fas fa-eye", text: '',
                        columns: ':gt(1)',
                        attr: {'data-bs-toggle': 'tooltip', 'data-bs-title': 'Spalten ein/ausblenden'}
                    },
                    {
                        extend: 'excel', className: "btn btn-light btn-outline-dark fas fa-file-excel", text: '',
                        title: 'Lieferanten_je_Element',
                        exportOptions: {columns: ':gt(1)'},
                        attr: {'data-bs-toggle': 'tooltip', 'data-bs-title': 'Download als Excel'}
                    },
                    {
                        extend: 'searchBuilder', className: "btn btn-light btn-outline-dark fas fa-filter", text: '',
                        attr: {'data-bs-toggle': 'tooltip', 'data-bs-title': 'Filter erstellen'}
                    }
                ],
                layout: {
                    top: null, topStart: null, topEnd: null,
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

                    /* leer gebliebene Layout-Zeile(n) über/unter der Tabelle entfernen */
                    $('#tableElementLieferanten_wrapper > .dt-layout-row').filter(function () {
                        return $.trim($(this).text()) === ''
                            && $(this).find('button, input, select, a, label').length === 0;
                    }).remove();

                    Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        .forEach(el => new bootstrap.Tooltip(el, {delay: {show: 10, hide: 0}}));
                }
            });

            $('#dateSelect, input[name="quelle"]').on('change', function () {
                tableEL.ajax.reload(null, false);
            });

            $('#btnToggleView').on('click', function () {
                groupByElement = !groupByElement;
                $('#viewLabel').text(groupByElement ? 'nach Element' : 'nach Lieferant');
                tableEL.rows().invalidate();
                tableEL.order([[0, 'asc'], [1, 'asc'], [4, 'asc']]).draw();
            });
        });
    </script>
</body>
</html>