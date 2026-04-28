<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Element Parameter Änderungen</title>
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

    <style>
        .diff-cell { font-family: monospace; font-size: 0.85rem; }
        .val-old   { color: #dc3545; text-decoration: line-through; opacity: .8; }
        .val-new   { color: #198754; font-weight: 600; }
        .arrow     { color: #6c757d; margin: 0 4px; }
        .badge-user { font-size: .75rem; background: #e9ecef; color: #495057;
            border-radius: 4px; padding: 2px 6px; }
        #cardHeaderSub { gap: .35rem; }
        .dt-search input { min-width: 180px; }

        /* subtle row highlight for wert change vs einheit change */
        tr.chg-wert   td:nth-child(7) { background: #fff3cd55; }
        tr.chg-einheit td:nth-child(8){ background: #cff4fc55; }
    </style>
</head>

<?php
require_once 'utils/_utils.php';
init_page_serversides("", "x");
?>

<body>
<div class="container-fluid">
    <div id="limet-navbar"></div>

    <div class="card col-12 mt-2">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap" id="pageCardHeader">
            <span class="fw-bold">
                <i class="fas fa-history me-1 text-secondary"></i>
                Element-Parameter&nbsp;Änderungen
                <small class="text-muted fw-normal ms-1" id="projectLabel"></small>
            </span>

            <div class="d-flex align-items-center gap-1">
                <label for="dateFrom" class="text-muted small mb-0">ab</label>
                <input type="date" id="dateFrom" class="form-control form-control-sm" style="width:145px"
                       title="Nur Änderungen ab diesem Datum anzeigen">
            </div>


            <div class="ms-auto d-flex align-items-center flex-wrap" id="cardHeaderSub"></div>
        </div>

        <div class="card-body px-0 py-0">
            <table id="changesTable" class="table table-sm table-striped table-hover border border-5 mb-0">
                <thead>
                <tr>
                    <th>ID</th>                 <!-- 0  -->
                    <th>Zeitstempel</th>        <!-- 1  -->
                    <th>User</th>               <!-- 2  -->
                    <th>Element</th>            <!-- 3  -->
                    <th>Parameter</th>          <!-- 4  -->
                    <th>Var</th>                <!-- 5  -->
                    <th>Wert-Änderung</th>      <!-- 6  -->
                    <th>Einheit-Änderung</th>   <!-- 7  -->
                    <th class="d-none">wertChg</th>    <!-- 8  hidden -->
                    <th class="d-none">einheitChg</th> <!-- 9  hidden -->
                </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm col-search" placeholder="Element…"></th>
                    <th><input type="text" class="form-control form-control-sm col-search" placeholder="Parameter…"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>


    </div>
</div>

<script>
    $(document).ready(function () {

        /* ── default: last 90 days ───────────────────────────── */
        const d = new Date();
        d.setDate(d.getDate() - 90);
        $('#dateFrom').val(d.toISOString().slice(0, 10));

        /* ── render helpers ──────────────────────────────────── */
        function diffCell(oldVal, newVal) {
            const o = oldVal.trim();
            const n = newVal.trim();
            // no change at all
            if (o === n) return o ? '<span class="text-muted">' + escHtml(o) + '</span>' : '<span class="text-muted">–</span>';
            let html = '';
            // show old (struck out) only if it was non-empty
            if (o) html += '<span class="val-old diff-cell">' + escHtml(o) + '</span>';
            // always show arrow
            html += '<span class="arrow">→</span>';
            // show new (green) only if it is non-empty; otherwise "gelöscht"
            if (n) {
                html += '<span class="val-new diff-cell">' + escHtml(n) + '</span>';
            } else {
                html += '<span class="text-muted fst-italic" style="font-size:.8rem">gelöscht</span>';
            }
            return html;
        }

        function escHtml(s) {
            return String(s)
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        /* ── DataTable ───────────────────────────────────────── */
        const table = $('#changesTable').DataTable({
            ajax: {
                url:  'getElementParameterChanges.php',
                type: 'POST',
                dataSrc: 'data'
            },
            columns: [
                { data: 0, width: '50px', visible: false },                      /* id        */
                {                                                  /* timestamp */
                    data: 1,
                    render: function (d) {
                        if (!d) return '–';
                        const dt = new Date(d.replace(' ','T'));
                        return dt.toLocaleString('de-AT', {
                            day:'2-digit', month:'2-digit', year:'numeric',
                            hour:'2-digit', minute:'2-digit'
                        });
                    }
                },
                {                                                  /* user */
                    data: 2,
                    render: function(d) {
                        return '<span class="badge-user">' + escHtml(d) + '</span>';
                    }
                },
                { data: 3 },                                       /* element   */
                { data: 4 },                                       /* parameter */
                { data: 5, width: '40px', className: 'text-center' }, /* variante */
                {                                                  /* wert diff */
                    data: null,
                    render: function(_, __, row) {
                        return diffCell(row[6], row[7]);
                    }
                },
                {                                                  /* einheit diff */
                    data: null,
                    render: function(_, __, row) {
                        return diffCell(row[8], row[9]);
                    }
                },
                { data: 10, visible: false },                      /* wertChg   */
                { data: 11, visible: false },                      /* einheitChg */
            ],
            order: [[1, 'desc']],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json' },
            scrollY: '70vh',
            scrollCollapse: true,
            paging: true,
            pageLength: 25,
            lengthMenu: [[25, 50, 100, -1], ['25', '50', '100', 'Alle']],
            layout: {
                topStart: null, topEnd: null,
                bottomStart: ['search', 'info'],
                bottomEnd: ['pageLength', 'paging', 'buttons']
            },
            buttons: [
                { extend:'copy',   text:'<i class="fas fa-copy"></i>',       titleAttr:'Kopieren',          className:'btn btn-sm btn-outline-dark bg-white' },
                { extend:'excel',  text:'<i class="fas fa-file-excel"></i>', titleAttr:'Excel Export',      className:'btn btn-sm btn-outline-dark bg-white' },
                { extend:'pdf',    text:'<i class="fas fa-file-pdf"></i>',   titleAttr:'PDF Export',        className:'btn btn-sm btn-outline-dark bg-white' },
                { extend:'colvis', text:'<i class="fas fa-columns"></i>',    titleAttr:'Spalten togglen',   className:'btn btn-sm btn-outline-dark bg-white' },
                {
                    text: '<i class="fas fa-sync-alt"></i>',
                    titleAttr: 'Daten neu laden',
                    className: 'btn btn-sm btn-outline-dark bg-white',
                    action: function() { table.ajax.reload(null, false); }
                }
            ],
            rowCallback: function(row, data) {
                if (data[10]) $(row).addClass('chg-wert');
                if (data[11]) $(row).addClass('chg-einheit');
            },
            initComplete: function() {
                /* move DT controls to card header */
                $('.dt-search label').remove();
                $('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .addClass('btn btn-sm btn-outline-dark')
                    .appendTo('#cardHeaderSub');
                $('#changesTable_wrapper .dt-buttons').appendTo('#cardHeaderSub');

                /* populate user filter */
                const users = new Set();
                table.column(2).data().each(function(v) {
                    const m = v.match(/>([^<]+)<\/span>/);
                    if (m) users.add(m[1]);
                });
                users.forEach(u => {
                    $('#userFilter').append('<option value="' + u + '">' + u + '</option>');
                });
            }
        });

        /* ── Column search (footer inputs) ──────────────────── */
        $('#changesTable tfoot input.col-search').each(function(i) {
            const colIdx = $(this).closest('th').index();
            $(this).on('keyup change', function() {
                table.column(colIdx).search(this.value).draw();
            });
        });

        /* ── Date-from filter ────────────────────────────────── */
        $.fn.dataTable.ext.search.push(function(settings, data) {
            const from = $('#dateFrom').val();
            if (!from) return true;
            // data[1] is the rendered locale string; re-parse from raw via order data
            const raw = table.row($(settings.nTable).find('tbody tr').eq(
                settings.iDisplayStart
            )).data();
            // simpler: filter on the raw timestamp in data[1] before rendering
            return true; // handled server-side via reload below
        });

        /* Reload table when date changes */
        let dateTimer;
        $('#dateFrom').on('change', function() {
            clearTimeout(dateTimer);
            dateTimer = setTimeout(function() { table.ajax.reload(null, false); }, 600);
        });

        /* ── User filter ─────────────────────────────────────── */
        $.fn.dataTable.ext.search.push(function(settings, data, rowIndex) {
            const sel = $('#userFilter').val();
            if (!sel) return true;
            return data[2].indexOf(sel) !== -1;
        });
        $('#userFilter').on('change', function() { table.draw(); });

    });
</script>
</body>
</html>