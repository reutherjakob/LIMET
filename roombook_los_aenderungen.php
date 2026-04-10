<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Los-Änderungshistorie</title>
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

<body>
<?php
require_once 'utils/_utils.php';
init_page_serversides("x");
?>

<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-xxl-12" id="mainCardColumn">
            <div class="mt-4 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center justify-content-start" id="AenderungCardHeaderLeft">
                            <span><strong>ÄNDERUNGSHISTORIE LOSE</strong> &emsp;</span>
                        </div>
                        <div class="col-6 d-flex align-items-center justify-content-end gap-2" id="AenderungCardHeaderRight">
                            <label for="dateFrom" class="visually-hidden">Von</label>
                            <input type="date" id="dateFrom" class="form-control form-control-sm w-auto"
                                   data-bs-toggle="tooltip" data-bs-title="Änderungen ab diesem Datum"/>
                            <span class="text-muted small">–</span>
                            <label for="dateTo" class="visually-hidden">Bis</label>
                            <input type="date" id="dateTo" class="form-control form-control-sm w-auto"
                                   data-bs-toggle="tooltip" data-bs-title="Änderungen bis zu diesem Datum"/>
                        </div>
                    </div>
                </div>

                <div class="card-body p-1 py-1 m-1">
                    <table id="tableAenderungen"
                           class="table table-sm table-responsive table-striped compact border border-light border-1 w-100">
                        <thead>
                        <tr>
                            <th>ID</th>                  <!-- 0  hidden -->
                            <th>Zeitpunkt</th>           <!-- 1  visible -->
                            <th>User</th>                <!-- 2  hidden -->
                            <th>Los Bez. neu</th>        <!-- 3  visible -->
                            <th>Los Bez. alt</th>        <!-- 4  hidden -->
                            <th>Los ext ID alt</th>      <!-- 5  hidden -->
                            <th>Los ext ID neu</th>      <!-- 6  hidden -->
                            <th>Elem. ID alt</th>        <!-- 7  hidden -->
                            <th>Elem. ID neu</th>        <!-- 8  hidden -->
                            <th>Element</th>             <!-- 9  visible -->
                            <th>Raum ID alt</th>         <!-- 10 hidden -->
                            <th>Raum ID neu</th>         <!-- 11 hidden -->
                            <th>Raum</th>                <!-- 12 visible -->
                            <th>Status alt</th>          <!-- 13 hidden -->
                            <th>Status neu</th>          <!-- 14 hidden -->
                            <th>Lief. alt</th>           <!-- 15 hidden -->
                            <th>Lief. neu</th>           <!-- 16 hidden -->
                            <th>Budget alt</th>          <!-- 17 hidden -->
                            <th>Budget neu</th>          <!-- 18 hidden -->
                            <th>Anz. alt</th>            <!-- 19 hidden -->
                            <th>Anz. neu</th>            <!-- 20 hidden -->
                            <th>Kurzbeschr. alt</th>     <!-- 21 hidden -->
                            <th>Kurzbeschr. neu</th>     <!-- 22 hidden -->
                            <th>Neu/Best. alt</th>       <!-- 23 hidden -->
                            <th>Neu/Best. neu</th>       <!-- 24 hidden -->
                            <th>Standort alt</th>        <!-- 25 hidden -->
                            <th>Standort neu</th>        <!-- 26 hidden -->
                            <th>Verw. alt</th>           <!-- 27 hidden -->
                            <th>Verw. neu</th>           <!-- 28 hidden -->
                            <th>Ansch. alt</th>          <!-- 29 hidden -->
                            <th>Ansch. neu</th>          <!-- 30 hidden -->
                            <th>Los int alt</th>         <!-- 31 hidden -->
                            <th>Los int neu</th>         <!-- 32 hidden -->
                            <th>GHG alt</th>             <!-- 33 hidden -->
                            <th>GHG neu</th>             <!-- 34 hidden -->
                            <th>GUG alt</th>             <!-- 35 hidden -->
                            <th>GUG neu</th>             <!-- 36 hidden -->
                            <th>Gewerk alt</th>          <!-- 37 hidden -->
                            <th>Gewerk neu</th>          <!-- 38 hidden -->
                            <th>Var. alt</th>            <!-- 39 hidden -->
                            <th>Var. neu</th>            <!-- 40 hidden -->
                            <th>Geänderte Felder</th>    <!-- 41 virtual  -->
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail-Modal -->
<div class="modal fade" id="aenderungDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-history me-2"></i>Änderungsdetails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="aenderungDetailBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script charset="utf-8">
    var tableAenderungen;

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }

    const norm = v => (v === null || v === undefined || v === '') ? '' : String(v);

    function disp(val) {
        return (val !== null && val !== undefined && val !== '') ? String(val) : null;
    }

    // ── Value renderers ───────────────────────────────────────────
    const statusBadges = {
        '0': "<span class='badge bg-danger'>Offen</span>",
        '1': "<span class='badge bg-success'>Fertig</span>",
        '2': "<span class='badge bg-primary'>Wartend</span>",
    };

    const variantLabels = { '1': 'A', '2': 'B', '3': 'C', '4': 'D', '5': 'E' };

    function renderVal(f, rawVal) {
        const v = norm(rawVal);
        if (f.altIdx === 13 || f.altIdx === 14) // Status
            return statusBadges[v] ?? rawVal;
        if (f.neuIdx === 24) //Bestand
            return v === '0' ? 'Ja' : v === '1' ? 'Nein' : rawVal;
        if (f.neuIdx === 26) // Standort
            return v === '1' ? 'Ja' : v === '0' ? 'Nein' : rawVal;
        if (f.neuIdx === 28) // Verwendung
            return v === '1' ? 'Ja' : v === '0' ? 'Nein' : rawVal;
        if (f.altIdx === 39 || f.altIdx === 40) // Variante
            return variantLabels[v] ?? rawVal;
        return rawVal;
    }

    function buildRow(label, alt, neu) {
        const altStr    = disp(alt);
        const neuStr    = disp(neu);
        const isChanged = (altStr ?? '') !== (neuStr ?? '');
        if (altStr === null && neuStr === null) return '';
        const altHtml   = altStr ?? '<em class="text-muted">–</em>';
        const neuHtml   = neuStr ?? '<em class="text-muted">–</em>';
        if (neu === undefined) {
            return `<tr>
                <td class="fw-semibold text-muted small">${label}</td>
                <td colspan="2">${altHtml}</td>
            </tr>`;
        }
        const rowClass  = isChanged ? 'table-warning' : '';
        const neuRender = isChanged ? `<span class="text-success fw-bold">${neuHtml}</span>` : neuHtml;
        const badge     = isChanged ? `<span class="badge bg-warning text-dark ms-1" style="font-size:0.65em">geändert</span>` : '';
        return `<tr class="${rowClass}">
            <td class="fw-semibold small">${label}${badge}</td>
            <td>${altHtml}</td>
            <td>${neuRender}</td>
        </tr>`;
    }

    function smartLabel(f, row) {
        const a = norm(row[f.altIdx]);
        const n = norm(row[f.neuIdx]);

        if (f.altIdx === 4) { // Los Bezeichnung
            if (a === '' && n !== '') return 'Zu Los hinzugefügt';
            if (a !== '' && n === '') return 'Von Los entfernt';
        }
        if (f.altIdx === 19) { // Anzahl
            if ((a === '' || a === '0') && n !== '' && n !== '0') return 'Element hinzugefügt';
            if (a !== '' && a !== '0' && (n === '' || n === '0')) return 'Element entfernt';
        }
        return f.label;
    }

    function buildSummaryBadges(row) {
        const changed = FIELDS.filter(f => norm(row[f.altIdx]) !== norm(row[f.neuIdx]));
        if (!changed.length) return '<span class="text-muted small">–</span>';
        return changed.map(f =>
            `<span class="badge rounded-pill ${f.badgeCls} me-1 mb-1" style="font-size:0.7em">${smartLabel(f, row)}</span>`
        ).join('');
    }

    // ── Single source of truth ────────────────────────────────────────────
    // label     : shown in badges, modal "Geändert", and modal "Kontext"
    // altIdx    : SQL result column index for the old value
    // neuIdx    : SQL result column index for the new value
    // badgeCls  : Bootstrap bg-* class for the table badge
    // noContext : skip in "Kontext (unverändert)" — already shown in Zuordnung
    const FIELDS = [
        // Zuordnung — primary (blau)
        { label: 'Los Bezeichnung', altIdx:  4, neuIdx:  3, badgeCls: 'bg-primary',   noContext: false },
        { label: 'Element',         altIdx:  7, neuIdx:  8, badgeCls: 'bg-primary',   noContext: true  },
        { label: 'Raum',            altIdx: 10, neuIdx: 11, badgeCls: 'bg-primary',   noContext: true  },
        // Status — warning (orange)
        { label: 'Status',          altIdx: 13, neuIdx: 14, badgeCls: 'bg-warning text-dark', noContext: false },
        // Zeitlich — purple (via custom, closest Bootstrap is info)
        { label: 'Lieferdatum',     altIdx: 15, neuIdx: 16, badgeCls: 'bg-info text-dark',    noContext: false },
        // Finanziell — success (grün)
        { label: 'Budget-Position', altIdx: 17, neuIdx: 18, badgeCls: 'bg-success',   noContext: false },
        { label: 'Anschaffung',     altIdx: 29, neuIdx: 30, badgeCls: 'bg-success',   noContext: false },
        // Mengenmäßig — secondary (grau)
        { label: 'Anzahl',          altIdx: 19, neuIdx: 20, badgeCls: 'bg-secondary', noContext: false },
        // Beschreibend — dark
        { label: 'Element Kommentar',altIdx: 21, neuIdx: 22, badgeCls: 'bg-dark',      noContext: false },
        { label: 'Bestand',         altIdx: 23, neuIdx: 24, badgeCls: 'bg-dark',      noContext: false },
        { label: 'Standort',        altIdx: 25, neuIdx: 26, badgeCls: 'bg-dark',      noContext: false },
        { label: 'Verwendung',      altIdx: 27, neuIdx: 28, badgeCls: 'bg-dark',      noContext: false },
        // Organisatorisch — danger (rot)
        { label: 'Internes Los',    altIdx: 31, neuIdx: 32, badgeCls: 'bg-danger',    noContext: true },
        { label: 'GHG',             altIdx: 33, neuIdx: 34, badgeCls: 'bg-danger',    noContext: false },
        { label: 'GUG',             altIdx: 35, neuIdx: 36, badgeCls: 'bg-danger',    noContext: false },
        { label: 'Gewerk',          altIdx: 37, neuIdx: 38, badgeCls: 'bg-danger',    noContext: false },
        // Sonstige — light
        { label: 'Variante',        altIdx: 39, neuIdx: 40, badgeCls: 'bg-light text-dark border', noContext: false },
    ];

    $(document).ready(function () {
        const today    = new Date().toISOString().split('T')[0];
        const monthAgo = new Date(Date.now() - 360 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        $('#dateFrom').val(monthAgo);
        $('#dateTo').val(today);

        // SQL: 41 columns (0–40) — HTML: 41 <th> + 1 virtual = 42 total
        const visibleCols = [1, 3, 9, 12];
// Returns a contextual display label for a changed field


        tableAenderungen = new DataTable('#tableAenderungen', {
            ajax: {
                url: 'getLotAenderungen.php',
                type: 'POST',
                data: function (d) {
                    d.dateFrom = $('#dateFrom').val();
                    d.dateTo   = $('#dateTo').val();
                    return d;
                }
            },
            columns: [
                // 0–40: real SQL columns
                ...Array.from({length: 41}, (_, i) => ({
                    data: i,
                    visible: visibleCols.includes(i),
                    searchable: visibleCols.includes(i),
                    render: i === 1
                        ? (data, type) => {
                            if (type !== 'display' || !data) return data ?? '';
                            const [datePart, timePart = ''] = data.split(' ');
                            const [y, m, d] = datePart.split('-');
                            return `${d}.${m}.${y} ${timePart}`;
                        }
                        : (i === 9 || i === 12)
                            ? (data) => data || '<span class="text-muted">–</span>'
                            : null
                })),
                // virtual: colored badge summary
                {
                    data: null,
                    visible: true,
                    searchable: true,
                    orderable: false,
                    render: (data, type, row) => type === 'display' ? buildSummaryBadges(row) : ''
                }
            ],
            select: false,
            paging: true,
            searching: true,
            info: true,
            order: [[1, 'desc']],
            pagingType: 'full',
            lengthChange: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.',
                searchPlaceholder: 'Suche..',
                search: "",
                lengthMenu: "_MENU_",
            },
            buttons: [
                {
                    extend: 'colvis',
                    className: "btn btn-light btn-outline-dark fas fa-eye",
                    text: '',
                    attr: { 'data-bs-toggle': 'tooltip', 'data-bs-title': 'Spalten ein/ausblenden' }
                },
                {
                    extend: 'excel',
                    className: "btn btn-light btn-outline-dark fas fa-file-excel",
                    text: '',
                    exportOptions: { columns: ':visible' },
                    attr: { 'data-bs-toggle': 'tooltip', 'data-bs-title': 'Download als Excel' }
                },
                {
                    extend: 'searchBuilder',
                    className: "btn btn-light btn-outline-dark fas fa-filter",
                    text: '',
                    attr: { 'data-bs-toggle': 'tooltip', 'data-bs-title': 'Filter erstellen' }
                }
            ],
            layout: {
                topStart: null, topEnd: null,
                bottomStart: ['pageLength', 'info'],
                bottomEnd: ['paging', 'search', 'buttons']
            },
            initComplete: function () {
                let sourceElements = document.getElementsByClassName("dt-buttons");
                let targetElement  = document.getElementById("AenderungCardHeaderLeft");
                Array.from(sourceElements).forEach(el => targetElement.appendChild(el));

                $('.dt-search label').remove();
                $('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .addClass("btn btn-sm btn-outline-secondary")
                    .appendTo('#AenderungCardHeaderLeft');

                const reloadDebounced = debounce(() => tableAenderungen.ajax.reload(null, false), 500);
                $('#dateFrom, #dateTo').on('change', reloadDebounced);

                if (!window.tooltipList) {
                    let triggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        .filter(el => el.nodeType === Node.ELEMENT_NODE);
                    window.tooltipList = triggerList.map(el =>
                        new bootstrap.Tooltip(el, { delay: { show: 10, hide: 0 } })
                    );
                }
            }
        });

        // Klick auf Zeile → Detail-Modal
        $('#tableAenderungen tbody').on('click', 'tr', function () {
            const d = tableAenderungen.row(this).data();
            if (!d) return;

            const changed           = FIELDS.filter(f => norm(d[f.altIdx]) !== norm(d[f.neuIdx]));
            const unchangedWithValue = FIELDS.filter(f =>
                !f.noContext &&
                norm(d[f.altIdx]) === norm(d[f.neuIdx]) &&
                norm(d[f.altIdx]) !== ''
            );

            let html = `<p class="text-muted small mb-2">
                <i class="fas fa-user me-1"></i><strong>${d[2] ?? '–'}</strong>
                &nbsp;|&nbsp;
                <i class="fas fa-clock me-1"></i>${d[1] ?? '–'}
                &nbsp;
            </p>`;

            html += `<table class="table table-sm table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:28%">Feld</th>
                        <th>Alt</th>
                        <th>Neu <span class="badge bg-warning text-dark" style="font-size:0.65em">geändert</span></th>
                    </tr>
                </thead><tbody>`;

            // ── Zuordnung ────────────────────────────────────────────────
            html += `<tr class="table-secondary">
                <td colspan="3" class="fw-bold small py-1">
                    <i class="fas fa-map-marker-alt me-1"></i>Zuordnung
                </td></tr>`;
            html += buildRow('Aktuelles Los', d[3]);
            html += buildRow('Element Bez.', d[9]);
            html += buildRow('Raum Bez.',    d[12]);

            // ── Geänderte Felder ─────────────────────────────────────────
            html += `<tr class="table-secondary">
                <td colspan="3" class="fw-bold small py-1">
                    <i class="fas fa-edit me-1"></i>Geändert
                    <span class="badge bg-danger ms-1" style="font-size:0.65em">${changed.length}</span>
                </td></tr>`;
            if (changed.length) {
                changed.forEach(f => html += buildRow(f.label, renderVal(f, d[f.altIdx]), renderVal(f, d[f.neuIdx])));
            } else {
                html += `<tr><td colspan="3" class="text-muted fst-italic small px-2">
                    Kein bekanntes Feld hat sich geändert.</td></tr>`;
            }

            // ── Kontext (unveränderte Felder mit Wert) ───────────────────
            if (unchangedWithValue.length) {
                html += `<tr class="table-secondary">
                    <td colspan="3" class="fw-bold small py-1">
                        <i class="fas fa-info-circle me-1"></i>Kontext (unverändert)
                    </td></tr>`;
                unchangedWithValue.forEach(f => {
                    html += `<tr>
                        <td class="small fw-semibold" style="color:#999">${f.label}</td>
                        <td colspan="2" class="small" style="color:#999">${renderVal(f, d[f.neuIdx])}</td>
                    </tr>`;
                });
            }

            html += '</tbody></table>';
            $('#aenderungDetailBody').html(html);
            new bootstrap.Modal(document.getElementById('aenderungDetailModal')).show();
        });
    });
</script>
</body>
</html>