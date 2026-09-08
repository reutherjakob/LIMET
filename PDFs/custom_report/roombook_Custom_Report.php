<?php
require_once '../../utils/_utils.php';
init_page_serversides("", "x");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Custom-Report</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../../Logo/iphone_favicon.png"/>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
</head>
<body>
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>

    <!-- ================= Parameter- & Vorlagen-Auswahl (Custom) ================= -->
    <div class="card mb-2">
        <div class="card-header px-2 py-2">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    <span class="fw-semibold">Custom Report &ndash; Parameterauswahl</span>
                </div>
                <div class="col"></div>
                <div class="col-auto d-flex align-items-center gap-2">
                    <label for="dateSelect" class="form-label small mb-0 text-nowrap">Änderungen ab</label>
                    <input type="date" id="dateSelect" name="dateSelect" class="form-control form-control-sm"
                           data-bs-toggle="tooltip"
                           data-bs-title="Bis zu welchem Datum Änderungen markiert werden sollen"/>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    <label for="dateSelect4Report" class="form-label small mb-0 text-nowrap">Report&shy;datum</label>
                    <input type="date" id="dateSelect4Report" name="dateSelect4Report"
                           class="form-control form-control-sm"
                           data-bs-toggle="tooltip"
                           data-bs-title="Dieses Datum wird im Bericht als aktueller Stand angeführt."/>
                </div>

            </div>
        </div>
        <div class="card-body px-2 py-2">
            <!-- Das Custom-Parameter-Interface wird hier hinein gerendert -->
            <div id="reportButtonsContainer" aria-live="polite"></div>
        </div>
    </div>

    <!-- ================= Raumauswahl ================= -->
    <div class="card">
        <div class="card-header px-1 py-1" id="HeaderTabelleCard">
            <div class="row align-items-center justify-content-end g-1">
                <div class="col-12 flex-nowrap text-nowrap">
                    <div class="d-inline-flex" id="filtersContainer"></div>
                    <div class="d-inline-flex" id="sub1"></div>
                </div>
            </div>
        </div>

        <div class="card-body px-2 py-2">
            <?php
            $mysqli = utils_connect_sql();
            $columns = [
                'idTABELLE_Räume', 'MT-relevant', 'Raumnr', 'Raumbezeichnung', 'Raumnummer_Nutzer', 'Nutzfläche',
                'Raumbereich Nutzer', 'Geschoss', 'Bauetappe', 'Bauabschnitt',
                'Anmerkung allgemein', 'Entfallen'
            ];
            $sql = "SELECT " . implode(", ", array_map(function ($col) {
                    return "r.`$col`";
                }, $columns)) .
                " FROM tabelle_räume r 
                 INNER JOIN tabelle_projekte p  
                 ON r.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte 
                 WHERE p.idTABELLE_Projekte=" . $_SESSION["projectID"];
            $result = $mysqli->query($sql);
            if (!$result) {
                die("Query failed: " . $mysqli->error);
            }
            echo "<table class='table display compact table-striped table-bordered table-sm' id='tableRooms'>
                    <thead><tr>";
            foreach ($columns as $col) {
                echo "<th>" . str_replace('_', ' ', $col) . "</th>";
            }
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $col) {
                    $value = $row[$col];
                    if ($col === 'MT-relevant') {
                        $value = $value === '0' ? 'Nein' : 'Ja';
                    }
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
            $mysqli->close();
            ?>
        </div>
    </div>
</div>


<script>
    let table;

    $(document).ready(function () {
        $('#dateSelect').val(new Date().toISOString().split('T')[0]);
        $('#dateSelect4Report').val(new Date().toISOString().split('T')[0]);
        initDataTable();
        addMTFilter('#filtersContainer');
        addEntfallenFilter('#filtersContainer');

        setTimeout(() => {
            const searchbuilderBtns = [{
                extend: 'searchBuilder',
                className: "btn btn-sm btn-outline-dark bg-light fas fa-search me-1 ms-1",
                text: "",
                titleAttr: "Suche konfigurieren"
            }];
            new $.fn.dataTable.Buttons(table, {buttons: searchbuilderBtns}).container().appendTo($('#sub1'));
            moveSearchBox('sub1');
            initTooltips();
        }, 100);

        $('#dateSelect4Report').change(handleReportDateSelection);
        handleReportDateSelection();

        // Custom-Parameter-Interface sofort aufbauen (diese Seite kennt nur Custom)
        buildCustomParamInterface($('#reportButtonsContainer'));
    });

    function initDataTable() {
        table = $('#tableRooms').DataTable({
            paging: false,
            columnDefs: [{targets: [0], visible: false, searchable: false}],
            orderCellsTop: true,
            order: [[1, "asc"]],
            scrollY: '55vh',
            scrollCollapse: true,
            dom: 'frtip',
            select: {style: 'multi'},
            language: {
                search: "",
                searchBuilder: {label: "", depthLimit: 3}
            },
            keys: true,
            buttons: [
                {
                    text: 'All',
                    className: "btn btn-sm btn-outline-dark bg-white",
                    action: () => table.rows().select()
                },
                {
                    text: 'Visible', className: "btn btn-sm btn-outline-dark bg-white",
                    action: () => table.rows({search: 'applied'}).select()
                },
                {
                    text: 'None', className: "btn btn-sm btn-outline-dark bg-white",
                    action: () => table.rows().deselect()
                }
            ]
        });

        let btnsContainer = $('<div class="btn-group d-inline-flex flex-nowrap"></div>');
        table.buttons().container().children().appendTo(btnsContainer);
        $('#sub1').empty().append(btnsContainer);
    }

    // ---------------------------------------------------------------------
    //  CUSTOM-BERICHT: Parameter-Auswahl-Interface (nur Bootstrap, kein CSS)
    // ---------------------------------------------------------------------
    let customParamDefs = null; // Cache der vom Server geladenen Definitionen

    function buildCustomParamInterface(container) {
        container.html('<div class="text-muted small py-1">Parameter werden geladen …</div>');

        const render = () => renderCustomParamInterface(container, customParamDefs);

        if (customParamDefs) {
            render();
        } else {
            $.getJSON('pdf_getReportParamDefinitions.php')
                .done(data => {
                    customParamDefs = data;
                    render();
                })
                .fail(() => {
                    container.html('<div class="alert alert-danger py-1 mb-0">Parameter-Definitionen konnten nicht geladen werden.</div>');
                });
        }
    }

    function renderCustomParamInterface(container, defs) {
        const preselected = new Set(defs.preselected || []);
        const opts = defs.defaults || {mt_mode: 'list', mark_changes: true, format: 'A3'};

        const wrapper = $('<div class="w-100"></div>');

        // Optionsleiste (Format / Med.-tech.-Modus / Änderungsmarkierung)
        const sel = (v, cur) => (v === cur ? 'selected' : '');
        const optionsBar = $(
            '<div class="row">' +
            '   <div class="col-3 d-inline-flex flex-nowrap align-items-center gap-2 mb-2"> ' +
            '      <button type="button" class="btn btn-primary btn-sm" id="customGenerateBtn">Bericht erstellen</button>' +
            '      <button type="button" class="btn btn-outline-secondary btn-sm" id="customSelectAll">Alle</button>' +
            '      <button type="button" class="btn btn-outline-secondary btn-sm" id="customSelectNone">Keine</button>' +
            '      <span class="text-muted small ms-1" id="customSelectionCount"></span>' +
            '   </div>' +
            '  <div class="col-3 d-inline-flex flex-nowrap align-items-center gap-2 mb-2">' +
            '    <label class="form-label small mb-0" for="customFormat">Format</label>' +
            '    <select id="customFormat" class="form-select form-select-sm">' +
            '      <option value="A3" ' + sel('A3', opts.format) + '>A3 quer</option>' +
            '      <option value="A4" ' + sel('A4', opts.format) + '>A4 hoch</option>' +
            '    </select>' +
            '  </div>' +
            '  <div class="col-3 d-inline-flex flex-nowrap align-items-center gap-2 mb-2">' +
            '    <label class="form-label small mb-0" for="customMtMode">Med.-Technik</label>' +
            '    <select id="customMtMode" class="form-select form-select-sm">' +
            '      <option value="none" ' + sel('none', opts.mt_mode) + '>keine</option>' +
            '      <option value="list" ' + sel('list', opts.mt_mode) + '>nur Auflistung</option>' +
            '      <option value="details" ' + sel('details', opts.mt_mode) + '>mit Parametern</option>' +
            '    </select>' +
            '  </div>' +
            '  <div class="col-3 d-inline-flex flex-nowrap align-items-center gap-2 mb-2">' +
            '    <div class="form-check mb-1">' +
            '      <input class="form-check-input" type="checkbox" id="customMarkChanges" ' + (opts.mark_changes ? 'checked' : '') + '>' +
            '      <label class="form-check-label small" for="customMarkChanges">Änderungen markieren</label>' +
            '    </div>' +
            '  </div>' +
            '</div> </div>  '
        );
        wrapper.append(optionsBar);

        // Vorlagen / Sets (vordefinierte Zusammenstellungen) – reines Bootstrap
        const sets = defs.sets || [];
        if (sets.length) {
            const optHtml = s =>
                '<option value="' + escapeAttr(s.id) + '">' + escapeHtml(s.label) + '</option>';
            const setOpts = sets.map(optHtml).join('');
            const setsRow = $(
                '<div class="row">' +
                '  <div class="col-12 col-md-7 d-inline-flex flex-nowrap align-items-center gap-2 mb-2">' +
                '    <label class="form-label small mb-0 text-nowrap" for="customSetSelect">Vorlage</label>' +
                '    <select id="customSetSelect" class="form-select form-select-sm">' +
                '      <option value="">\u2014 Vorlage w\u00e4hlen \u2014</option>' + setOpts +
                '    </select>' +
                '    <button type="button" class="btn btn-outline-secondary btn-sm text-nowrap" id="customSetApply">Anwenden</button>' +
                '  </div>' +
                '</div>'
            );
            wrapper.append(setsRow);
        }

        // Schnellsuche ueber alle Parameter (reines Bootstrap, kein eigenes CSS)
        const searchBar = $(
            '<div class="row">' +
            '  <div class="col-12 col-md-6 mb-2">' +
            '    <div class="input-group input-group-sm">' +
            '      <span class="input-group-text">Suche</span>' +
            '      <input type="text" class="form-control" id="customParamSearch" ' +
            'placeholder="Parameter filtern \u2026" autocomplete="off">' +
            '      <button class="btn btn-outline-secondary" type="button" id="customParamSearchClear">&times;</button>' +
            '      <span class="input-group-text text-muted" id="customParamSearchCount"></span>' +
            '    </div>' +
            '  </div>' +
            '</div>'
        );
        wrapper.append(searchBar);

        // Accordion mit je einem Block (Sondergruppen ausgenommen)
        const accId = 'customParamAccordion';
        const accordion = $('<div class="accordion accordion-flush border rounded" id="' + accId + '"></div>');

        defs.groups.filter(g => !g.special).forEach((group, gi) => {
            const collapseId = 'cpaCollapse_' + group.id;
            const headingId = 'cpaHeading_' + group.id;

            const checks = group.params.map(p => {
                const id = 'cp_' + group.id + '_' + cssSafe(p.key);
                const checked = preselected.has(p.key) ? 'checked' : '';
                return (
                    '<div class="col-12 col-md-6 col-xxl-4 custom-param-col">' +
                    '  <div class="form-check">' +
                    '    <input class="form-check-input custom-param-check" type="checkbox" value="' +
                    escapeAttr(p.key) + '" id="' + id + '" data-group="' + group.id + '" ' + checked + '>' +
                    '    <label class="form-check-label small" for="' + id + '">' + escapeHtml(p.label) + '</label>' +
                    '  </div>' +
                    '</div>'
                );
            }).join('');

            const item = $(
                '<div class="accordion-item">' +
                '  <h2 class="accordion-header" id="' + headingId + '">' +
                '    <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" ' +
                'data-bs-target="#' + collapseId + '" aria-expanded="false" aria-controls="' + collapseId + '">' +
                '      <span class="fw-semibold me-2">' + escapeHtml(group.label) + '</span>' +
                '      <span class="badge bg-secondary group-count" data-group="' + group.id + '"></span>' +
                '    </button>' +
                '  </h2>' +
                '  <div id="' + collapseId + '" class="accordion-collapse collapse" aria-labelledby="' + headingId +
                '" data-bs-parent="#' + accId + '">' +
                '    <div class="accordion-body py-2">' +
                '      <div class="mb-2">' +
                '        <button type="button" class="btn btn-link btn-sm p-0 me-3 group-select-all" data-group="' + group.id + '">Alle</button>' +
                '        <button type="button" class="btn btn-link btn-sm p-0 group-select-none" data-group="' + group.id + '">Keine</button>' +
                '      </div>' +
                '      <div class="row g-1">' + checks + '</div>' +
                '    </div>' +
                '  </div>' +
                '</div>'
            );
            accordion.append(item);
        });

        wrapper.append(accordion);
        container.empty().append(wrapper);

        // --- Events ---
        container.find('#customGenerateBtn').on('click', generateCustomReport);

        // Standard-Beschriftungen (key -> label) aus den Gruppen, um beim
        // Vorlagenwechsel Set-Overrides sauber wieder zurücksetzen zu können.
        const defaultLabels = {};
        (defs.groups || []).forEach(g =>
            (g.params || []).forEach(p => {
                defaultLabels[p.key] = p.label;
            }));

        // Checkbox-Beschriftungen setzen: Set-Override > Standard-Label > Key.
        const relabelChecks = (labels) => {
            labels = labels || {};
            container.find('.custom-param-check').each(function () {
                const key = $(this).val();
                const lbl = $(this).closest('.form-check').find('.form-check-label');
                if (labels[key] != null) {
                    lbl.text(labels[key]);
                } else if (defaultLabels[key] != null) {
                    lbl.text(defaultLabels[key]);
                } else {
                    lbl.text(key);
                }
            });
        };

        // Vorlage anwenden: passende Checkboxen setzen + Optionen + Beschriftungen
        const applyCustomSet = (setId) => {
            const set = (defs.sets || []).find(s => s.id === setId);
            if (!set) {
                // keine (gültige) Vorlage -> Beschriftungen auf Standard zurück
                relabelChecks({});
                return;
            }
            const wanted = new Set(set.params || []);
            container.find('.custom-param-check').each(function () {
                $(this).prop('checked', wanted.has($(this).val()));
            });
            relabelChecks(set.labels || {});
            const o = set.options || {};
            if (o.format) {
                container.find('#customFormat').val(o.format);
            }
            if (o.mt_mode) {
                container.find('#customMtMode').val(o.mt_mode);
            }
            if (typeof o.mark_changes === 'boolean') {
                container.find('#customMarkChanges').prop('checked', o.mark_changes);
            }
            updateCustomCounts(container);
        };
        container.find('#customSetSelect').on('change', function () {
            applyCustomSet($(this).val());
        });
        container.find('#customSetApply').on('click', function () {
            applyCustomSet(container.find('#customSetSelect').val());
        });

        container.find('#customSelectAll').on('click', () => {
            container.find('.custom-param-check').prop('checked', true);
            updateCustomCounts(container);
        });
        container.find('#customSelectNone').on('click', () => {
            container.find('.custom-param-check').prop('checked', false);
            updateCustomCounts(container);
        });

        container.find('.group-select-all').on('click', function () {
            const g = $(this).data('group');
            container.find('.custom-param-check[data-group="' + g + '"]').prop('checked', true);
            updateCustomCounts(container);
        });
        container.find('.group-select-none').on('click', function () {
            const g = $(this).data('group');
            container.find('.custom-param-check[data-group="' + g + '"]').prop('checked', false);
            updateCustomCounts(container);
        });

        container.find('.custom-param-check').on('change', () => updateCustomCounts(container));

        container.find('#customParamSearch').on('input', function () {
            filterCustomParams(container, $(this).val());
        });
        container.find('#customParamSearchClear').on('click', function () {
            container.find('#customParamSearch').val('');
            filterCustomParams(container, '');
        });

        updateCustomCounts(container);
    }

    function updateCustomCounts(container) {
        let total = 0;
        container.find('.group-count').each(function () {
            const g = $(this).data('group');
            const checks = container.find('.custom-param-check[data-group="' + g + '"]');
            const checked = checks.filter(':checked').length;
            total += checked;
            $(this).text(checked + ' / ' + checks.length);
            $(this).toggleClass('bg-secondary', checked === 0).toggleClass('bg-success', checked > 0);
        });
        container.find('#customSelectionCount').text(total + ' Parameter ausgewählt');
    }

    // Live-Filter: blendet nicht passende Parameter/Gruppen aus (nur Bootstrap-Klassen)
    function filterCustomParams(container, term) {
        term = (term || '').trim().toLowerCase();
        const filtering = term.length > 0;
        let totalMatches = 0;

        container.find('.accordion-item').each(function () {
            const item = $(this);
            let groupMatches = 0;
            item.find('.custom-param-col').each(function () {
                const col = $(this);
                const label = col.find('.form-check-label').text().toLowerCase();
                const hit = !filtering || label.indexOf(term) !== -1;
                col.toggleClass('d-none', !hit);
                if (hit && filtering) groupMatches++;
            });
            totalMatches += groupMatches;

            const collapse = item.find('.accordion-collapse');
            const button = item.find('.accordion-button');
            if (filtering) {
                item.toggleClass('d-none', groupMatches === 0);
                if (groupMatches > 0) {
                    collapse.addClass('show');
                    button.removeClass('collapsed').attr('aria-expanded', 'true');
                }
            } else {
                item.removeClass('d-none');
                collapse.removeClass('show');
                button.addClass('collapsed').attr('aria-expanded', 'false');
            }
        });

        const countEl = container.find('#customParamSearchCount');
        countEl.text(filtering ? (totalMatches + ' Treffer') : '');
    }

    function generateCustomReport() {
        const container = $('#reportButtonsContainer');
        const selectedKeys = container.find('.custom-param-check:checked')
            .map(function () {
                return $(this).val();
            }).get();

        const format = $('#customFormat').val() || 'A3';
        const mtMode = $('#customMtMode').val() || 'list';
        const markChanges = $('#customMarkChanges').is(':checked') ? '1' : '0';

        // Mindestens ein Parameter ODER eine Med.-tech.-Ausgabe muss gewählt sein
        if (!selectedKeys.length && mtMode === 'none') {
            alert('Bitte mindestens einen Parameter oder eine Med.-tech.-Ausgabe wählen!');
            return;
        }

        const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
        if (!roomIDs.length) {
            alert('Kein Raum ausgewählt!');
            return;
        }

        const formattedDate = $('#dateSelect').val() || getDate('#dateSelect');
        const paramsParam = encodeURIComponent(selectedKeys.join(','));

        // Gewählte Vorlage mitgeben: die konkrete Parameter-Auswahl kommt zwar
        // aus den Checkboxen (URL(params) hat Vorrang), aber Set-Labels und der
        // Set-Header werden serverseitig nur angewendet, wenn 'set' übergeben wird.
        const setId = $('#customSetSelect').val() || '';
        const setParam = setId ? `&set=${encodeURIComponent(setId)}` : '';

        const url = `pdf_createBauangabenBericht_Custom.php` +
            `?roomID=${roomIDs.join(',')}` +
            `&date=${formattedDate}` +
            `&params=${paramsParam}` +
            `&format=${format}` +
            `&mt_mode=${mtMode}` +
            `&mark_changes=${markChanges}` +
            setParam;

        $.post('log_report_download.php', {
            reportUrl: 'pdf_createBauangabenBericht_Custom',
            reportText: `Custom ${format} (${selectedKeys.length} Param, MT:${mtMode}, Chg:${markChanges})`,
            roomIDs: roomIDs.join(',')
        });

        window.open(url);
    }

    // Hilfsfunktionen zum sicheren Einbetten
    function cssSafe(s) {
        return String(s).replace(/[^a-zA-Z0-9_-]/g, '_');
    }

    function escapeAttr(s) {
        return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;');
    }

    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function addMTFilter(location) {
        const select = $('<select class="form-select form-select-sm ms-1 me-1" aria-label="MT Filter">' +
            '<option value="">MT-relevant</option>' +
            '<option selected value="Ja">Ja</option>' +
            '<option value="Nein">Nein</option>' +
            '</select>');
        $(location).append(select);
        select.change(function () {
            table.column(1).search($(this).val()).draw();
        });
        table.column(1).search("Ja").draw();
    }

    function addEntfallenFilter(location) {
        const select = $('<select class="form-select form-select-sm me-1 ms-1" aria-label="Entfallen Filter">' +
            '<option value="">Entfallen</option>' +
            '<option value="1">1</option>' +
            '<option selected value="0">0</option>' +
            '</select>');
        $(location).append(select);
        select.change(function () {
            table.column(11).search($(this).val()).draw();
        });
        table.column(11).search("0").draw();
    }

    function initTooltips() {
        const opts = {delay: {show: 0, hide: 200}};
        let el;
        el = document.getElementById('dateSelect');
        if (el) new bootstrap.Tooltip(el, opts);
        el = document.getElementById('dateSelect4Report');
        if (el) new bootstrap.Tooltip(el, opts);
    }

    function handleReportDateSelection() {
        const val = $('#dateSelect4Report').val();
        $.post('../pdf_setSession.php', {PDFdatum: val});
    }

    function getDate(selector) {
        const d = new Date($(selector).val() || Date.now());
        return `${('0' + d.getDate()).slice(-2)}-${('0' + (d.getMonth() + 1)).slice(-2)}-${d.getFullYear()}`;
    }

    function moveSearchBox(location) {
        $('#dt-search-0').appendTo(`#${location}`).addClass("btn-sm");
    }
</script>
</body>
</html>