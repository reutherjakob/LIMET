<?php
/**
 * roombook_budgetTimeline.php
 * ---------------------------------------------------------------------------
 * Übersicht & Timeline der Projektbudgets.
 *   - Graph:   Bestandswert je Budget über die Zeit (Stufenlinie)
 *   - Fluss:   Änderungsvolumen je Budget im Zeitraum (Zugang/Abgang/Netto)
 *   - Detail:  Einzelne wertwirksame Änderungen
 *   - Gesamtsumme der gewählten Budgets prominent
 *
 * Datenquelle: getBudgetTimeline.php (rekonstruiert aus tabelle_rb_aenderung).
 * Grundbedingung: nur Standort = 1. Preisbasis: aktuelle Kosten.
 * Nur Bootstrap 5 - kein eigenes CSS.
 * ---------------------------------------------------------------------------
 */
require_once '../utils/_utils.php';
init_page_serversides("");

$mysqli = utils_connect_sql();
$projectID = isset($_SESSION["projectID"]) ? (int)$_SESSION["projectID"] : 0;

// Budgets für das Auswahlfeld
$budgetOptions = [['id' => 0, 'label' => 'Ohne Budget']];
if ($stmt = $mysqli->prepare(
    "SELECT idtabelle_projektbudgets, Budgetnummer, Budgetname
       FROM tabelle_projektbudgets
      WHERE tabelle_projekte_idTABELLE_Projekte = ?
      ORDER BY Budgetnummer")) {
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $budgetOptions[] = [
            'id' => (int)$row['idtabelle_projektbudgets'],
            'label' => trim($row['Budgetnummer'] . ' - ' . $row['Budgetname'])
        ];
    }
    $stmt->close();
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Budget-Timeline</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
<div id="limet-navbar"></div>

<div class="container-fluid mt-3">

    <!-- Filterleiste -->
    <div class="card mb-3">
        <div class="card-header bg-light">

            <div class="row d-flex justify-content-between flex-nowrap">
                <div class="col-2 d-flex flex-nowrap justify-content-start text-nowrap align-items-center white">
                    <strong id="budgetTimelineTitle" style="cursor:pointer"  >
                        <i class="fas fa-chart-line me-1"></i>Budget-Timeline</strong>
                </div>

                <div class="col-10">
                    <div class="d-flex flex-nowrap justify-content-between align-items-end mb-1">
                        <select id="budgetSelect" class="form-select" multiple>
                            <?php foreach ($budgetOptions as $b): ?>
                                <option value="<?= (int)$b['id'] ?>"><?= htmlspecialchars($b['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="btn-group  text-nowrap" role="group">
                            <button type="button" class="btn btn-outline-success fas fa-check-double" id="selAll">
                                Alle
                            </button>
                            <button type="button" class="btn btn-outline-dark far fa-times-circle" id="selNone">
                                Keine
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body collapse show" id="SummendarstellungeCardHEader">
            <div class="row align-items-center">
                <div class="col-4">
                    <div class="card text-bg-secondary ">
                        <div class="card-body d-inline-flex align-items-center flex-nowrap">
                            <div class="text-uppercase small opacity-75 me-2">Gesamtsumme (Endstand, gewählte Budgets)
                            </div>
                            <div class="fs-3 fw-bold" id="sumEnd">–</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card ">
                        <div class="card-body  d-inline-flex align-items-center flex-nowrap">
                            <div class="text-uppercase small text-muted me-2">Startwert im Zeitraum</div>
                            <div class="fs-3 fw-semibold" id="sumStart">–</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card ">
                        <div class="card-body  d-inline-flex align-items-center flex-nowrap">
                            <div class="text-uppercase small text-muted me-2">Netto-Veränderung</div>
                            <div class="fs-3 fw-semibold" id="sumNetto">–</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Graph -->
    <div class="card mb-3">
        <div class="card-header">

            <div class="row d-flex justify-content-between flex-nowrap">
                <div class="col-6  d-flex flex-nowrap justify-content-start text-nowrap">
                    <span><i class="fas fa-chart-area me-1"></i>Bestandsverlauf je Budget</span>
                </div>
                <div class="col-6 d-flex flex-nowrap justify-content-end text-nowrap align-items-center">

                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="showTotalLine" checked>
                        <label class="form-check-label small me-2" for="showTotalLine">Gesamtlinie</label>
                    </div>
                    <div class="d-flex me-2">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Schnellauswahl Zeitraum">
                            <button type="button" class="btn btn-outline-secondary rounded-2 me-1 rangeBadge"
                                    data-range="2w">2 Wochen
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-2 me-1 rangeBadge"
                                    data-range="1m">1 Monat
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-2 me-1 rangeBadge"
                                    data-range="6m">6 Monate
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-2 me-1 rangeBadge"
                                    data-range="1y">1 Jahr
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-2 me-1 rangeBadge active"
                                    data-range="all">
                                Alles
                            </button>
                        </div>
                        <div class="d-flex ms-2 me-2">
                            <input type="date" id="dateFrom" class=" form-control form-control-sm" style="width:150px">
                            <span class="text-muted">–</span>
                            <input type="date" id="dateTo" class="form-control form-control-sm" style="width:150px">
                        </div>
                        <button id="reloadBtn" class="btn btn-success">
                            <i class="fas fa-sync-alt me-1"></i> Aktualisieren
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-body">
            <div id="chartWrap" class="ratio ratio-21x9">
                <canvas id="chart"></canvas>
            </div>
            <div id="chartEmpty" class="text-center text-muted py-5 d-none"></div>
        </div>
    </div>

    <!-- Fluss-Tabelle -->
    <div class="card mb-3">
        <div class="card-header bg-light">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-auto">
                    <i class="fas fa-exchange-alt me-1"></i><strong>Änderungsvolumen je Budget</strong>
                </div>
                <div class="col d-flex align-items-center justify-content-end gap-2 flex-wrap"
                     id="flowHeaderTools"></div>
            </div>
        </div>
        <div class="card-body">
            <table id="flowTable" class="table table-striped table-hover align-middle w-100">
                <thead class="table-light">
                <tr>
                    <th>Budget</th>
                    <th class="text-end">Startwert</th>
                    <th class="text-end">Zugänge</th>
                    <th class="text-end">Abgänge</th>
                    <th class="text-end">Netto</th>
                    <th class="text-end">Endstand</th>
                    <th class="text-end">Änderungen</th>
                </tr>
                </thead>
                <tbody></tbody>
                <tfoot class="table-group-divider">
                <tr class="fw-bold">
                    <td>Gesamt</td>
                    <td class="text-end" id="fStart">–</td>
                    <td class="text-end" id="fZugang">–</td>
                    <td class="text-end" id="fAbgang">–</td>
                    <td class="text-end" id="fNetto">–</td>
                    <td class="text-end" id="fEnd">–</td>
                    <td class="text-end" id="fCount">–</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Detailliste -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-auto">
                    <i class="fas fa-list me-1"></i><strong>Einzelne Änderungen</strong>
                </div>
                <div class="col d-flex align-items-center justify-content-end gap-2 flex-wrap"
                     id="detailHeaderTools"></div>
            </div>
        </div>
        <div class="card-body">
            <table id="detailTable" class="table table-sm table-striped table-hover w-100">
                <thead>
                <tr>
                    <th>Zeitpunkt</th>
                    <th>User</th>
                    <th>Raum</th>
                    <th>Element</th>
                    <th>Variante</th>
                    <th>Budget (alt)</th>
                    <th>Budget (neu)</th>
                    <th class="text-end">Anzahl alt</th>
                    <th class="text-end">Anzahl neu</th>
                    <th class="text-end">&Delta; Wert</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<script>
    // Überschrift klickbar machen und Summen-Header ein-/ausklappen
    (function () {
        const header = document.getElementById('SummendarstellungeCardHEader');
        const trigger = document.querySelector('#budgetTimelineTitle'); // s. Hinweis unten
        if (!header || !trigger) return;

        // Collapse-Instanz ohne sofortiges Umschalten erzeugen
        const collapse = bootstrap.Collapse.getOrCreateInstance(header, {toggle: false});

        trigger.style.cursor = 'pointer';
        trigger.addEventListener('click', () => collapse.toggle());

        // Chart neu zeichnen, wenn wieder aufgeklappt (Canvas misst sonst 0)
        header.addEventListener('shown.bs.collapse', () => {
            if (DATA) renderChart();
        });
    })();
    $(function () {
        const eur = new Intl.NumberFormat('de-AT', {style: 'currency', currency: 'EUR'});
        const PALETTE = ['#0d6efd', '#dc3545', '#198754', '#fd7e14', '#6f42c1', '#20c997',
            '#d63384', '#ffc107', '#0dcaf0', '#6c757d', '#495057', '#adb5bd'];

        let DATA = null;
        let chart = null;
        let flowTable = null;
        let detailTable = null;

        /* ---------- Hilfsfunktionen -------------------------------------- */
        const moneyCell = (v, withSign) => {
            const n = +v || 0;
            const cls = n > 0 ? 'text-success' : (n < 0 ? 'text-danger' : 'text-muted');
            const s = (withSign && n > 0 ? '+' : '') + eur.format(n);
            return '<span class="' + cls + '">' + s + '</span>';
        };

        function selectedBudgetIds() {
            return ($('#budgetSelect').val() || []).map(Number);
        }

        function allBudgetIds() {
            // Strings zurückgeben, damit select2 .val() korrekt matcht
            return $('#budgetSelect option').map((i, o) => o.value).get();
        }

        /* ---------- Select2 + Alle/Keine --------------------------------- */
        $('#budgetSelect').select2({width: '100%', placeholder: 'Budget(s) wählen', closeOnSelect: false});
        $('#selAll').on('click', () => $('#budgetSelect').val(allBudgetIds()).trigger('change'));
        $('#selNone').on('click', () => $('#budgetSelect').val([]).trigger('change'));

        /* ---------- Zeitraum-Schnellauswahl ------------------------------ */
        function setRange(range) {
            const to = new Date();
            let from = new Date();
            switch (range) {
                case '2w':
                    from.setDate(to.getDate() - 14);
                    break;
                case '1m':
                    from.setMonth(to.getMonth() - 1);
                    break;
                case '6m':
                    from.setMonth(to.getMonth() - 6);
                    break;
                case '1y':
                    from.setFullYear(to.getFullYear() - 1);
                    break;
                case 'all':
                    from = new Date('2000-01-01');
                    break;
            }
            $('#dateFrom').val(from.toISOString().slice(0, 10));
            $('#dateTo').val(to.toISOString().slice(0, 10));
        }

        // Default: Alles
        setRange('all');

        $('.rangeBadge').on('click', function () {
            $('.rangeBadge').removeClass('active');
            $(this).addClass('active');
            setRange($(this).data('range'));
            // bewusst KEIN Reload - erst beim Klick auf "Aktualisieren"
        });
        // Manuelle Datumsänderung hebt die Schnellauswahl-Markierung auf
        $('#dateFrom, #dateTo').on('change', () => $('.rangeBadge').removeClass('active'));

        /* ---------- DataTables ------------------------------------------- */
        function relocateControls(api, targetSel) {
            const $c = $(api.table().container());
            $c.find('.dt-search label').remove();
            $c.find('.dt-search input')
                .removeClass('form-control-sm').addClass('form-control form-control-sm')
                .attr('placeholder', 'Suche…');
            $c.find('.dt-search').appendTo(targetSel);
            api.buttons().container().appendTo(targetSel);
        }

        const commonButtons = [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-light btn-outline-dark',
                titleAttr: 'Excel-Export'
            },
            {
                extend: 'searchBuilder',
                text: '<i class="fas fa-filter"></i> Filter',
                className: 'btn btn-sm btn-light btn-outline-dark',
                titleAttr: 'Erweiterte Suche'
            }
        ];

        flowTable = $('#flowTable').DataTable({
            data: [],
            columns: [
                {title: 'Budget'},
                {title: 'Startwert', className: 'text-end', render: (d, t) => t === 'display' ? eur.format(d) : d},
                {title: 'Zugänge', className: 'text-end', render: (d, t) => t === 'display' ? moneyCell(d, true) : d},
                {
                    title: 'Abgänge',
                    className: 'text-end',
                    render: (d, t) => t === 'display' ? (d ? '<span class="text-danger">-' + eur.format(d) + '</span>' : '<span class="text-muted">–</span>') : d
                },
                {title: 'Netto', className: 'text-end', render: (d, t) => t === 'display' ? moneyCell(d, false) : d},
                {
                    title: 'Endstand',
                    className: 'text-end',
                    render: (d, t) => t === 'display' ? '<span class="fw-semibold">' + eur.format(d) + '</span>' : d
                },
                {title: 'Änderungen', className: 'text-end'}
            ],
            order: [[5, 'desc']],
            paging: false,
            info: false,
            language: {url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'},
            layout: {topStart: null, topEnd: ['search', 'buttons'], bottomStart: null, bottomEnd: null},
            buttons: commonButtons,
            footerCallback: function () {
                const api = this.api();
                const sum = i => api.column(i, {search: 'applied'}).data().reduce((a, b) => a + (parseFloat(b) || 0), 0);
                const tStart = sum(1), tZ = sum(2), tA = sum(3), tN = sum(4), tE = sum(5);
                let tC = 0;
                api.column(6, {search: 'applied'}).data().each(v => tC += parseInt(v) || 0);

                $('#fStart').text(eur.format(tStart));
                $('#fZugang').html(tZ ? '<span class="text-success">+' + eur.format(tZ) + '</span>' : '–');
                $('#fAbgang').html(tA ? '<span class="text-danger">-' + eur.format(tA) + '</span>' : '–');
                $('#fNetto').html(moneyCell(tN, false));
                $('#fEnd').text(eur.format(tE));
                $('#fCount').text(tC);

                // Summary-Karten spiegeln die angezeigten Zeilen
                $('#sumEnd').text(eur.format(tE));
                $('#sumStart').text(eur.format(tStart));
                $('#sumNetto').text(eur.format(tN))
                    .removeClass('text-success text-danger')
                    .addClass(tN > 0 ? 'text-success' : (tN < 0 ? 'text-danger' : ''));
            },
            initComplete: function () {
                relocateControls(this.api(), '#flowHeaderTools');
            }
        });

        detailTable = $('#detailTable').DataTable({
            data: [],
            columns: [
                {title: 'Zeitpunkt'},
                {title: 'User'},
                {title: 'Raum'},
                {title: 'Element'},
                {title: 'Variante'},
                {title: 'Budget (alt)'},
                {title: 'Budget (neu)'},
                {title: 'Anzahl alt', className: 'text-end'},
                {title: 'Anzahl neu', className: 'text-end'},
                {title: 'Δ Wert', className: 'text-end', render: (d, t) => t === 'display' ? moneyCell(d, true) : d}
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], ['10', '25', '50', 'Alle']],
            language: {url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'},
            layout: {
                topStart: null,
                topEnd: ['search', 'buttons'],
                bottomStart: ['pageLength', 'info'],
                bottomEnd: 'paging'
            },
            buttons: commonButtons,
            initComplete: function () {
                relocateControls(this.api(), '#detailHeaderTools');
            }
        });

        /* ---------- Laden (nur per Button) ------------------------------- */
        function fetchData() {
            $('#reloadBtn').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span> Laden…');
            $.ajax({
                url: 'getBudgetTimeline.php',
                type: 'POST',
                dataType: 'json',
                data: {dateFrom: $('#dateFrom').val(), dateTo: $('#dateTo').val()}
            }).done(function (resp) {
                if (resp.error) {
                    alert('Fehler: ' + resp.error);
                    return;
                }
                DATA = resp;
                renderAll();
            }).fail(function (xhr) {
                alert('Serverfehler beim Laden.\n' + (xhr.responseText || '').slice(0, 300));
            }).always(function () {
                $('#reloadBtn').prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> Aktualisieren');
            });
        }

        /* ---------- Rendern (clientseitig, ohne Neuladen) ---------------- */
        function renderAll() {
            if (!DATA) return;
            renderChart();
            renderFlow();
            renderDetail();
        }

        function renderChart() {
            const sel = selectedBudgetIds();
            const tl = DATA.timeline || [];
            const labels = tl.map(p => p.t);
            const budgetMap = {};
            (DATA.budgets || []).forEach(b => budgetMap[b.id] = b.label);

            const datasets = [];
            sel.forEach((bid, i) => {
                datasets.push({
                    label: budgetMap[bid] || ('#' + bid),
                    data: tl.map(p => (p.stock[bid] != null ? p.stock[bid] : 0)),
                    borderColor: PALETTE[i % PALETTE.length],
                    backgroundColor: PALETTE[i % PALETTE.length],
                    stepped: true, fill: false, pointRadius: 0, pointHoverRadius: 4, borderWidth: 2
                });
            });
            if ($('#showTotalLine').is(':checked') && sel.length > 1) {
                datasets.push({
                    label: 'Gesamt',
                    data: tl.map(p => sel.reduce((s, bid) => s + (p.stock[bid] != null ? p.stock[bid] : 0), 0)),
                    borderColor: '#212529', backgroundColor: '#212529', borderDash: [6, 4],
                    stepped: true, fill: false, pointRadius: 0, borderWidth: 2
                });
            }

            const hasData = labels.length > 0 && datasets.length > 0;
            $('#chartEmpty').text(sel.length === 0 ? 'Bitte Budget(s) auswählen.' : 'Keine Daten im gewählten Zeitraum.');
            $('#chartWrap').toggleClass('d-none', !hasData);
            $('#chartEmpty').toggleClass('d-none', hasData);

            if (chart) {
                chart.destroy();
                chart = null;
            }
            if (!hasData) return;

            chart = new Chart(document.getElementById('chart').getContext('2d'), {
                type: 'line',
                data: {labels: labels, datasets: datasets},
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: {mode: 'index', intersect: false},
                    plugins: {
                        legend: {position: 'bottom'},
                        tooltip: {callbacks: {label: c => c.dataset.label + ': ' + eur.format(c.parsed.y)}}
                    },
                    scales: {
                        y: {ticks: {callback: v => eur.format(v)}},
                        x: {ticks: {maxRotation: 0, autoSkip: true, maxTicksLimit: 12}}
                    }
                }
            });
        }

        function renderFlow() {
            const sel = selectedBudgetIds();
            const flow = DATA.flow || {};
            const rows = [];
            sel.forEach(bid => {
                const f = flow[bid];
                if (!f) return;
                rows.push([f.label, f.start, f.zugang, f.abgang, f.netto, f.end, f.count]);
            });
            flowTable.clear().rows.add(rows).draw();  // footerCallback aktualisiert Summen + Karten
            if (sel.length === 0) {
                $('#sumEnd, #sumStart, #sumNetto').text('–');
                $('#sumNetto').removeClass('text-success text-danger');
            }
        }

        function renderDetail() {
            const sel = new Set(selectedBudgetIds());
            const rows = (DATA.changes || [])
                .filter(c => sel.has(c[10]) || sel.has(c[11]))
                .map(c => [c[0], c[1], c[2], c[3], c[4], c[5], c[6], c[7], c[8], c[9]]);
            detailTable.clear().rows.add(rows).draw();
        }

        /* ---------- Events ----------------------------------------------- */
        $('#reloadBtn').on('click', fetchData);
        $('#budgetSelect, #showTotalLine').on('change', renderAll);

        // Erstes Laden (danach nur noch per Button)
        fetchData();
    });
</script>
</body>
</html>