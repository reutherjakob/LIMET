<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Projekt Elemente Parameter Tabelle</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables with SearchBuilder (sb) -->
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
</head>

<?php
// 25 FX
require_once 'utils/_utils.php';
init_page_serversides("", "x");
?>

<body>
<div class="container-fluid">
    <div id="limet-navbar"></div>
    <div class="card">
        <div style="height: 50px" class="card-header">
            <div class="row">
                <div class="col-8 d-inline-flex align-content-start align-items-center"
                     id="elemetsParamsTableCardHeader">
                    <label class="form-check-label me-3 text-nowrap">Element-Parameter im Projekt</label>
                </div>
                <div class="col-4 d-inline-flex align-content-start justify-content-end" id="EPinPrCardHeader"></div>
            </div>
        </div>
        <div class="card-body" id="elemetsParamsTableCard">
            <div id="elemetsParamsTable"></div>
        </div>
    </div>
</div>

<script>
    // K2R is populated dynamically after categories are loaded
    var K2R = [];

    let dataTable;

    // Keys to exclude from dynamic parameter columns
    const EXCLUDE_KEYS = new Set([
        'ElementID', 'Bezeichnung', 'Variante', 'Neu/Bestand', 'Standort', 'SummevonAnzahl',
        'TABELLE_Elemente_idTABELLE_Elemente', 'tabelle_Varianten_idtabelle_Varianten',
        'roomID', 'Raumbezeichnung', 'Raumnr', 'Bauabschnitt', 'Geschoss',
        'Raumbereich', 'Bauetappe', 'MTrelevant', 'FunktionsteilstellenID'
    ]);

    // Load all categories actually used in this project, build checkboxes dynamically
    function init_checkboxes4selectingKathegories(callback) {
        $.getJSON('get_parameter_kategorien.php', function (kategorien) {
            // All checked by default → K2R gets all IDs
            K2R = kategorien.map(k => String(k.id));

            const div = document.querySelector('#elemetsParamsTableCardHeader');
            kategorien.forEach((kat, index) => {
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = 'katCheckbox' + index;
                checkbox.value = String(kat.id);
                checkbox.className = 'form-check-input ';
                checkbox.checked = true;

                const label = document.createElement('label');
                label.htmlFor = checkbox.id;
                label.className = 'form-check-label ms-1 me-3 text-nowrap';
                label.textContent = kat.label;

                div.appendChild(checkbox);
                div.appendChild(label);

                checkbox.addEventListener('change', function () {
                    if (this.checked) {
                        if (!K2R.includes(this.value)) K2R.push(this.value);
                    } else {
                        K2R = K2R.filter(v => v !== this.value);
                    }
                    loadElementsParamTable();
                });
            });

            if (callback) callback();
        });
    }

    function loadElementsParamTable() {
        if ($.fn.DataTable.isDataTable('#elementsTable')) {
            dataTable.destroy();
            $('#elementsTable').remove();
        }
        $('#elemetsParamsTable').empty();
        $('#EPinPrCardHeader').empty();

        const url = 'getRoomElementsInProjectParameterData.php?K2Return=' + encodeURIComponent(JSON.stringify(K2R));

        $.getJSON(url, function (response) {
            // Support both old (array) and new (envelope) response format
            const data = Array.isArray(response) ? response : response.rooms;
            const paramMeta = Array.isArray(response) ? null : response.paramMeta; // [{key, label}]

            // Build key→label lookup for full parameter names
            const paramLabelMap = {};
            if (paramMeta) {
                paramMeta.forEach(p => {
                    paramLabelMap[p.key] = p.label;
                });
            }

            if (!data || !data.length) {
                $('#elemetsParamsTable').html('<p class="text-muted p-2">Keine Daten gefunden.</p>');
                return;
            }

            // Flatten data: one row per element with roomID
            const tableData = [];
            data.forEach(room => {
                const roomID = room.roomID;
                if (room.elements) {
                    room.elements.forEach(element => {
                        element.roomID = roomID;
                        tableData.push(element);
                    });
                }
            });

            if (!tableData.length) {
                $('#elemetsParamsTable').html('<p class="text-muted p-2">Keine Elemente gefunden.</p>');
                return;
            }

            // ── Fixed columns ────────────────────────────────────────────────
            const fixedCols = [
                {
                    title: 'Raum ID',
                    data: 'roomID',
                    visible: false
                },
                {
                    title: 'Raumbezeichnung',
                    data: 'Raumbezeichnung'
                },
                {
                    title: 'Raumnr',
                    data: 'Raumnr'
                },
                {
                    title: 'Bauabschnitt',
                    data: 'Bauabschnitt'
                },
                {
                    title: 'Geschoss',
                    data: 'Geschoss'
                },
                {
                    // Fingerprint icon in table header, plain text "ElementID" in Excel
                    title: "<span data-bs-toggle='tooltip' title='ElementID'><i class='fas fa-fingerprint'></i></span>",
                    data: 'ElementID',
                    render: function (data, type) {
                        if (type === 'export') return data; // plain value for Excel/CSV
                        return data;
                    }
                },
                {
                    title: 'Bezeichnung',
                    data: 'Bezeichnung'
                },
                {
                    title: 'Var',
                    data: 'Variante'
                },
                {
                    title: 'Neu/Bestand',
                    data: 'Neu/Bestand'
                },
                {
                    // Periscope icon in table header, plain "Standort" in Excel
                    title: "<span data-bs-toggle='tooltip' title='Standort'><i class='fab fa-periscope'></i></span>",
                    data: 'Standort',
                    render: function (data, type) {
                        if (type === 'export') return data;
                        return data;
                    }
                },
                {
                    // Stk in table header — clear label instead of "#"
                    title: 'Stk',
                    data: 'SummevonAnzahl',
                    render: function (data, type) {
                        // For Excel: output as number if possible
                        if (type === 'export') {
                            const n = parseFloat(data);
                            return isNaN(n) ? (data ?? '') : n;
                        }
                        return data;
                    }
                }
            ];

            // ── Dynamic parameter columns ────────────────────────────────────
            // Collect param keys from the first element (excluding fixed keys and __num suffixes)
            const firstElem = tableData[0];
            const paramKeys = Object.keys(firstElem).filter(k =>
                !EXCLUDE_KEYS.has(k) && !k.endsWith('__num')
            );

            // Only include columns that have at least one non-empty value
            const nonEmptyParamKeys = paramKeys.filter(key =>
                tableData.some(row => row[key] !== '' && row[key] !== null && row[key] !== undefined)
            );

            const paramCols = nonEmptyParamKeys.map(key => {
                const fullLabel = paramLabelMap[key] || key; // full name or fallback to key
                return {
                    title: fullLabel,   // shown in table header AND Excel
                    data: key,
                    render: function (data, type, row) {
                        if (type === 'export') {
                            // Use pre-computed numeric value for Excel if available
                            const numKey = key + '__num';
                            if (row[numKey] !== undefined && row[numKey] !== null) {
                                return row[numKey]; // real JS number → Excel number cell
                            }
                            return data ?? '';
                        }
                        return data ?? '';
                    }
                };
            });

            // ── Excel export: column header mapping ──────────────────────────
            // For fixed cols that use icon HTML, we provide plain-text headers via exportOptions.format.header
            const iconColHeaders = {
                5: 'ElementID',
                9: 'Standort',
                10: 'Stk'
            };

            const columns = fixedCols.concat(paramCols);

            // Build dynamic Excel header labels for all columns
            function getExcelHeader(data, colIdx) {
                if (iconColHeaders[colIdx] !== undefined) return iconColHeaders[colIdx];
                // Strip any HTML from header
                return $('<div>').html(data).text().trim() || data;
            }

            // Create table
            const tableEl = $('<table id="elementsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%"></table>');
            $('#elemetsParamsTable').append(tableEl);

            dataTable = $('#elementsTable').DataTable({
                data: tableData,
                columns: columns,
                scrollX: true,
                scrollCollapse: true,
                paging: true,
                pagingType: 'simple_numbers',
                pageLength: 10,
                lengthChange: true,
                lengthMenu: [[10, 20, 50, 100, -1], ['10', '20', '50', '100', 'Alle']],
                searching: true,
                info: true,
                order: [[1, 'asc']],
                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: ['pageLength', 'info'],
                    bottomEnd: ['paging', 'buttons', 'search']
                },
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i>',
                        titleAttr: 'Kopieren',
                        className: 'btn btn-sm btn-outline-dark bg-white',
                        exportOptions: {
                            format: {header: getExcelHeader}
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i>',
                        titleAttr: 'CSV Export',
                        className: 'btn btn-sm btn-outline-dark bg-white',
                        exportOptions: {
                            format: {header: getExcelHeader}
                        }
                    },
                    {
                        // ── Excel: numeric cells, full column names ──────────
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i>',
                        titleAttr: 'Excel Export',
                        className: 'btn btn-sm btn-outline-dark bg-white',
                        exportOptions: {
                            format: {
                                header: getExcelHeader,
                                body: function (data, row, column, node) {
                                    // Strip HTML tags from cell display values
                                    const stripped = $('<div>').html(data).text().trim();
                                    if (stripped === '') return '';
                                    // Only convert to number if the value is STRICTLY a number
                                    // (allows one optional comma/dot as decimal separator, but NOT e.g. "2.1.3")
                                    const normalized = stripped.replace(',', '.');
                                    const isStrictNumber = /^-?\d+(\.\d+)?$/.test(normalized);
                                    if (isStrictNumber) return parseFloat(normalized);
                                    return stripped;
                                }
                            }
                        },
                        customize: function (xlsx) {
                            // Force number format on all cells that are numeric
                            const sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c', sheet).each(function () {
                                const cellVal = $('v', this).text();
                                const num = parseFloat(cellVal);
                                if (!isNaN(num) && cellVal !== '') {
                                    $(this).attr('t', ''); // remove 'str' type → treat as number
                                }
                            });
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i>',
                        titleAttr: 'PDF Export',
                        className: 'btn btn-sm btn-outline-dark bg-white',
                        exportOptions: {
                            format: {header: getExcelHeader}
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i>',
                        titleAttr: 'Drucken',
                        className: 'btn btn-sm btn-outline-dark bg-white',
                        exportOptions: {
                            format: {header: getExcelHeader}
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fas fa-columns"></i>',
                        titleAttr: 'Spalten ein-/ausblenden',
                        className: 'btn btn-sm btn-outline-dark bg-white'
                    },
                    {
                        // ── SearchBuilder ────────────────────────────────────
                        extend: 'searchBuilder',
                        text: '<i class="fas fa-filter"></i>',
                        titleAttr: 'Filter / SearchBuilder',
                        className: 'btn btn-sm btn-outline-dark bg-white'
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json',
                    search: '',
                    searchPlaceholder: 'Suche...',
                    lengthMenu: '_MENU_',
                    searchBuilder: {
                        title: {
                            0: 'Kein Filter aktiv',
                            _: '%d Filter aktiv'
                        },
                        add: 'Bedingung hinzufügen',
                        button: {
                            0: 'Filter',
                            _: 'Filter (%d)'
                        },
                        clearAll: 'Alle löschen',
                        condition: 'Bedingung',
                        conditions: {
                            string: {
                                contains: 'Enthält',
                                empty: 'Leer',
                                endsWith: 'Endet mit',
                                equals: 'Gleich',
                                not: 'Ungleich',
                                notContains: 'Enthält nicht',
                                notEmpty: 'Nicht leer',
                                startsWith: 'Beginnt mit'
                            },
                            number: {
                                between: 'Zwischen',
                                empty: 'Leer',
                                equals: 'Gleich',
                                gt: 'Größer als',
                                gte: 'Größer oder gleich',
                                lt: 'Kleiner als',
                                lte: 'Kleiner oder gleich',
                                not: 'Ungleich',
                                notBetween: 'Nicht zwischen',
                                notEmpty: 'Nicht leer'
                            }
                        },
                        data: 'Spalte',
                        deleteTitle: 'Filter löschen',
                        leftTitle: 'Einrücken',
                        logicAnd: 'UND',
                        logicOr: 'ODER',
                        rightTitle: 'Ausrücken',
                        value: 'Wert',
                        valueJoiner: 'und'
                    }
                },

                initComplete: function () {
                    // Remove search label, style and move search input to card header
                    $('#elementsTable_wrapper .dt-search label').remove();
                    $('#elementsTable_wrapper .dt-search')
                        .children()
                        .removeClass('form-control form-control-sm')
                        .addClass('btn btn-sm btn-outline-dark bg-white ms-1')
                        .appendTo('#EPinPrCardHeader');

                    // Move buttons to card header
                    $('#elementsTable_wrapper .dt-buttons')
                        .children()
                        .appendTo('#EPinPrCardHeader');

                    // Init Bootstrap tooltips on icon headers
                    $('[data-bs-toggle="tooltip"]').each(function () {
                        new bootstrap.Tooltip(this);
                    });
                }
            });
        });
    }

    $(document).ready(function () {
        // Load categories first, then load table (categories determine K2R)
        init_checkboxes4selectingKathegories(function () {
            loadElementsParamTable();
        });
    });
</script>
</body>
</html>