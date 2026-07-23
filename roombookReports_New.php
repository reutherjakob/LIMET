<?php
require_once 'utils/_utils.php';
init_page_serversides("", "x");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Berichte</title>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <style>
        .custom-tooltip {
            --bs-tooltip-max-width: 400px;
            font-size: 1rem;
            padding: 1rem;
        }

        #reportButtonsContainer > .btn-group {
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>

    <div class="card">
        <div class="card-header px-1 py-1" id="HeaderTabelleCard">
            <div class="row align-items-center justify-content-end">
                <div class="col-xxl-11 flex-nowrap text-nowrap">
                    <div class="d-inline-flex " id="filtersContainer"></div>
                    <div class="d-inline-flex" id="sub1"></div>
                </div>

                <div class="col-xxl-1" id="dateSelect4ReportContainer">
                    <label for="dateSelect4Report" class="visually-hidden">Report Datum</label>
                    <input type="date" id="dateSelect4Report" name="dateSelect"
                           class="form-control form-control-sm me-2"
                           data-bs-toggle="tooltip"
                           data-bs-title="Dieses Datum wird im Bericht als aktueller Stand angeführt. Wird dieser Wert geändert und dann ein Bericht (in anderem Tab) neu geladen, wird das Datum darauf ebenso aktualisiert."
                           data-bs-custom-class="custom-tooltip"/>
                </div>
            </div>
        </div>

        <div class="card-header px-1 py-1" id="HeaderTabelleCard2">
            <div class="row align-items-center">
                <div class="col-xxl-2 d-flex justify-content-start align-items-center" id="reportCategoryContainer">
                    <label for="reportCategorySelect" class="fw-semibold"></label>
                    <select id="reportCategorySelect" class="form-select form-select-sm"
                            aria-label="Berichtskategorie auswählen"></select>
                </div>
                <div class="col-xxl-9" id="reportButtonsContainer" aria-live="polite"></div>

                <div class="col-xxl-1 d-none" id="dateSelectContainer">
                    <label for="dateSelect" class="visually-hidden">Änderungsdatum</label>
                    <input type="date" id="dateSelect" name="dateSelect" class="form-control form-control-sm"
                           data-bs-toggle="tooltip"
                           data-bs-title="Bis zu welchem Datum Änderungen markiert werden sollen"
                           data-bs-custom-class="custom-tooltip"/>
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
    const reportCategoryOptions = [
        {value: "", text: "-- Berichtkategorie auswählen --", disabled: true, selected: true},
        {value: "Custom", text: "Custom (Parameter wählen)"},
        {value: "bauangaben_neu", text: "Bauangaben Neu"},
        {value: "bauangaben", text: "Bauangaben"},
        {value: "einreichung", text: "Einreichung"},
        {value: "elementReports", text: "Element-/Raum Berichte"},
        {value: "einbringwege", text: "Einbringwege"},
        {value: "oldReports", text: "Historische Berichte"},

    ];
    const reportCategories = {
        Custom: [
            {text: "Select Inputs", url: "pdf_createBauangabenBericht_Custom"},

        ],
        bauangaben: [
            {text: "PDF V1", url: "pdf_createBauangabenPDF"},
            {text: "PDF V2", url: "pdf_createBauangabenV2PDF"},
            {text: "ohne Elemente-PDF", url: "pdf_createBauangabenWithoutElementsPDF"},
            {text: "Detail-PDF", url: "pdf_createBauangabenDetailPDF"},
            {text: "Lab-PDF", url: "pdf_createBauangabenLabPDF"},
            {text: "Lab-Kurz-PDF'", url: "pdf_createBauangabenLabKompaktPDF"},
            {text: "Lab-ENT-PDF", url: "pdf_createBauangabenLabEntPDF"},
        ],
        bauangaben_neu: [
            {text: "BAU A3", url: "pdf_createBauangabenBericht_A3Qeer"},
            {text: "ohne Änderungsmarkierungen", url: "pdf_createBauangabenBericht_A3Qeer_1"},
            {text: "ohne Lab", url: "pdf_createBauangabenBericht_A3Qeer_ohne_Lab_params"},
            {text: "VE", url: "pdf_createBauangabenBericht_A3Qeer_VE"},
            {text: "CINO", url: "pdf_createBauangabenBericht_A3Qeer_CINO"}
        ],
        elementReports: [
            {text: "Elem./Raum (w/Bestand)", url: "pdf_createRoombookElWithoutBestand"},
            {text: "inkl.Elem.Kommentar", url: "pdf_createRoombookElWithoutBestandWithComments"},
        ],
        einbringwege: [
            {text: "Einbringwege Größgeräte", url: "pdf_createElementEinbringwegePDF"},
            {text: "Einbringwege Größgeräte", url: "pdf_createElementEinbringwegePDF2"},
        ],
        einreichung: [
            {text: "KH SAN EINR BBE", url: "pdf_createBereicht_SAN_EINR"},
            {text: "Lab-EIN-PDF", url: "pdf_createBauangabenLabEinrPDF_1"},
        ],
        oldReports: [
            {text: "RB PDF", url: "pdf_createRoombookPDF"},
            {text: "0-PDF", url: "pdf_createRoombookWithout0PDF"},
            {text: "ohne Bestand-PDF", url: "pdf_createRoombookWithoutBestandPDF"},
            {text: "0-ohne Bestand-PDF", url: "pdf_createRoombookWithout0WothoutBestandPDF"},
            {text: "Bauangaben-0-PDF", url: "pdf_createRoombookWithBauangabenWithout0PDF"},
            {text: "Bauang. Großgeräte", url: "pdf_createVBM_Bericht"},
            {text: "BO-PDF", url: "pdf_createBOPDF"},
            {text: "VE-Gesamt-PDF", url: "pdf_createBericht_VE_PDF"},
            {text: "ENT-Gesamt-PDF", url: "pdf_createBericht_ENT_PDF"},
            {text: "Nutzer Formular", url: "pdf_createUserFormPDF"},
            {text: "KHI BT0", url: "pdf_createBerichtKHIMA40"}
        ]
    };

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

        const categorySelect = $('#reportCategorySelect');
        reportCategoryOptions.forEach(opt => {
            categorySelect.append(
                $('<option>', {
                    value: opt.value,
                    text: opt.text,
                    disabled: opt.disabled || false,
                    selected: opt.selected || false
                })
            );
        });
        categorySelect.on('change', function () {
            displayReportsForCategory(this.value);
        });
    });

    function initDataTable() {
        table = $('#tableRooms').DataTable({
            paging: false,
            columnDefs: [{targets: [0], visible: false, searchable: false}],
            orderCellsTop: true,
            order: [[1, "asc"]],
            scrollY: '83vh',
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

    function displayReportsForCategory(category) {
        const container = $('#reportButtonsContainer');
        container.empty();
        const dateSelectContainer = $('#dateSelectContainer');
        // Datumsauswahl auch für Custom anzeigen (Änderungsmarkierung)
        if (category === 'bauangaben_neu' || category === 'Custom') {
            dateSelectContainer.removeClass('d-none');
        } else {
            dateSelectContainer.addClass('d-none');
        }

        if (category === 'Custom') {
            buildCustomParamInterface(container);
            return;
        }

        if (!category || !reportCategories[category]) return;
        const btnGroup = $('<div class="btn-group" role="group" aria-label="Berichtsbuttons"></div>');
        reportCategories[category].forEach(report => {
            const button = $('<button type="button" class="btn btn-light border-dark btn-sm"></button>').text(report.text);
            button.on('click', () => generateReport(report, $('#dateSelect').val()));
            btnGroup.append(button);
        });

        container.append(btnGroup);
    }


    function generateReport(report, date) {
        const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
        const formattedDate = date || getDate("#dateSelect");

        let url;
        if (report.url.startsWith("pdf_createElementEinbringwegePDF")) {
            url = `/PDFs/${report.url}.php?date=${formattedDate}`;
        } else {
            if (!roomIDs.length) {
                alert("Kein Raum ausgewählt!");
                return;
            }
            url = `/PDFs/${report.url}.php?roomID=${roomIDs.join(',')}&date=${formattedDate}`;
        }

        // --- Logging ---
        $.post('log_report_download.php', {
            reportUrl: report.url,
            reportText: report.text,
            roomIDs: roomIDs.join(',')
        });
        // ----------------

        window.open(url);
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
            $.getJSON('PDFs/getReportParamDefinitions.php')
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
            const projectOpts = sets.filter(s => s.scope === 'project').map(optHtml).join('');
            const generalOpts = sets.filter(s => s.scope !== 'project').map(optHtml).join('');
            const groupedOpts =
                (projectOpts ? '<optgroup label="Projekt-Vorlagen">' + projectOpts + '</optgroup>' : '') +
                (generalOpts ? '<optgroup label="Allgemeine Vorlagen">' + generalOpts + '</optgroup>' : '');
            const setsRow = $(
                '<div class="row">' +
                '  <div class="col-12 col-md-7 d-inline-flex flex-nowrap align-items-center gap-2 mb-2">' +
                '    <label class="form-label small mb-0 text-nowrap" for="customSetSelect">Vorlage</label>' +
                '    <select id="customSetSelect" class="form-select form-select-sm">' +
                '      <option value="">\u2014 Vorlage w\u00e4hlen \u2014</option>' + groupedOpts +
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

        // Vorlage anwenden: passende Checkboxen setzen + Optionen übernehmen
        const applyCustomSet = (setId) => {
            const set = (defs.sets || []).find(s => s.id === setId);
            if (!set) {
                return;
            }
            const wanted = new Set(set.params || []);
            container.find('.custom-param-check').each(function () {
                $(this).prop('checked', wanted.has($(this).val()));
            });
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
        const url = `/PDFs/custom_report/pdf_createBauangabenBericht_Custom.php` +
            `?roomID=${roomIDs.join(',')}` +
            `&date=${formattedDate}` +
            `&params=${paramsParam}` +
            `&format=${format}` +
            `&mt_mode=${mtMode}` +
            `&mark_changes=${markChanges}`;

        $.post('log_report_download.php', {
            reportUrl: 'custom_report/pdf_createBauangabenBericht_Custom',
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
        $.post('PDFs/pdf_setSession.php', {PDFdatum: val});

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