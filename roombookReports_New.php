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


<!-- ===================================================================== -->
<!--  MODAL: Parameter-Picker (Sets befüllen vor, User justiert frei)      -->
<!-- ===================================================================== -->
<div class="modal fade" id="paramPickerModal" tabindex="-1" aria-labelledby="paramPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="paramPickerModalLabel">Bericht konfigurieren</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                <!-- Vorlage + Optionen -->
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label mb-1 small fw-semibold" for="pickerSetSelect">Vorlage laden</label>
                        <select id="pickerSetSelect" class="form-select form-select-sm"></select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small fw-semibold" for="pickerFormat">Format</label>
                        <select id="pickerFormat" class="form-select form-select-sm">
                            <option value="A3">A3 quer</option>
                            <option value="A4">A4 hoch</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1 small fw-semibold" for="pickerMtMode">Med.-tech.</label>
                        <select id="pickerMtMode" class="form-select form-select-sm">
                            <option value="none">keine</option>
                            <option value="list">Liste</option>
                            <option value="details">Details</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pickerMarkChanges">
                            <label class="form-check-label small" for="pickerMarkChanges">Änderungen markieren</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="pickerSelectAll">Alle</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="pickerSelectNone">Keine</button>
                    <span class="text-muted small ms-2">Vorlage füllt vor – Auswahl frei änderbar.</span>
                </div>

                <div id="pickerGroups" class="row g-3"></div>
            </div>
            <div class="modal-footer py-2">
                <span id="pickerCount" class="me-auto small text-muted"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-primary btn-sm" id="pickerGenerate">PDF erzeugen</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Ordner, in dem pdf_createBauangabenBericht_Custom.php UND
    // getReportParamDefinitions.php liegen. <-- an echten Pfad anpassen!
    const CUSTOM_PDF_DIR = '/PDFs/custom_report';

    let table;
    let customDefsCache = null;          // { groups, preselected, defaults, sets }
    let paramPickerModalInstance = null; // Bootstrap-Modal (lazy)

    const reportCategoryOptions = [
        {value: "", text: "-- Berichtkategorie auswählen --", disabled: true, selected: true},
        {value: "custom_sets", text: "Vorlagen (Custom)"},
        {value: "bauangaben_neu", text: "Bauangaben Neu"},
        {value: "bauangaben", text: "Bauangaben"},
        {value: "einreichung", text: "Einreichung"},
        {value: "elementReports", text: "Element-/Raum Berichte"},
        {value: "einbringwege", text: "Einbringwege"},
        {value: "oldReports", text: "Historische Berichte"},

    ];
    const reportCategories = {
        bauangaben: [
            {text: "PDF V1", url: "pdf_createBauangabenPDF"},
            {text: "PDF V2", url: "pdf_createBauangabenV2PDF"},
            {text: "ohne Elemente-PDF", url: "pdf_createBauangabenWithoutElementsPDF"},
            {text: "Detail-PDF", url: "pdf_createBauangabenDetailPDF"},
            {text: "Lab-PDF", url: "pdf_createBauangabenLabPDF"},
            {text: "Lab-Kurz-PDF'", url: "pdf_createBauangabenLabKompaktPDF"},
            {text: "Lab-ENT-PDF", url: "pdf_createBauangabenLabEntPDF"},
        ],
        bauangaben_neu:[
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

        // ---- Picker-Modal: statische Steuerelemente einmal verdrahten ----
        $('#pickerSetSelect').on('change', function () {
            const id = this.value;
            if (!id) {
                applyPickerSelection(customDefsCache.preselected || [], customDefsCache.defaults || {});
                return;
            }
            const set = (customDefsCache.sets || []).find(s => s.id === id);
            applyPickerSelection(set ? (set.params || []) : [], set ? (set.options || {}) : {});
        });
        $('#pickerSelectAll').on('click', function () {
            $('#pickerGroups input.picker-param').prop('checked', true);
            updatePickerCount();
        });
        $('#pickerSelectNone').on('click', function () {
            $('#pickerGroups input.picker-param').prop('checked', false);
            updatePickerCount();
        });
        $('#pickerGenerate').on('click', generateFromPicker);
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
        // Änderungsdatum auch für Vorlagen (Custom) einblenden
        if (category === 'bauangaben_neu' || category === 'custom_sets') {
            dateSelectContainer.removeClass('d-none');
        } else {
            dateSelectContainer.addClass('d-none');
        }

        // Sonderfall: Custom -> eigene Auswahl + Vorlagen, beide öffnen den Picker
        if (category === 'custom_sets') {
            displayCustomControls(container);
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

    // =====================================================================
    //  CUSTOM: Definitionen laden + Steuerung (Eigene Auswahl / Vorlagen)
    // =====================================================================

    /**
     * Lädt (einmalig, gecached) Gruppen/Vorauswahl/Defaults/Sets vom Endpoint.
     * Bei Fehlern steht die echte Ursache in der Browser-Konsole (F12).
     */
    function ensureCustomDefs(cb, container) {
        if (customDefsCache) { cb(customDefsCache); return; }

        const url = `${CUSTOM_PDF_DIR}/getReportParamDefinitions.php`;
        if (container) container.append('<span class="text-muted small">Lade Konfiguration…</span>');

        $.ajax({url: url, dataType: 'text'})
            .done(raw => {
                let data;
                try {
                    data = JSON.parse(raw);
                } catch (e) {
                    console.error('Custom-Defs: Antwort ist kein JSON.\nURL:', url, '\nAntwort:', raw);
                    if (container) {
                        container.empty();
                        container.append('<span class="text-danger small">Konfiguration ungültig (Details in Konsole).</span>');
                    }
                    return;
                }
                customDefsCache = data;
                cb(customDefsCache);
            })
            .fail(xhr => {
                console.error('Custom-Defs laden fehlgeschlagen.\nURL:', url,
                    '\nStatus:', xhr.status, xhr.statusText, '\nAntwort:', xhr.responseText);
                if (container) {
                    container.empty();
                    container.append('<span class="text-danger small">Konnte nicht geladen werden (Status '
                        + xhr.status + ', Details in Konsole).</span>');
                }
            });
    }

    /**
     * Buttons in der Berichtszeile: "Parameter wählen…" (leer/Vorauswahl) und
     * je Vorlage ein Button. Alle öffnen den Picker (Vorlage füllt nur vor).
     */
    function displayCustomControls(container) {
        ensureCustomDefs(function (defs) {
            container.empty();

            const own = $('<button type="button" class="btn btn-primary btn-sm me-2 mb-1">Parameter wählen…</button>');
            own.on('click', () => openParamPicker(null));
            container.append(own);

            const sets = defs.sets || [];
            const scopeLabel = {project: 'Projekt', general: 'Allgemein'};
            ['project', 'general'].forEach(scope => {
                const list = sets.filter(s => (s.scope || 'general') === scope);
                if (!list.length) return;
                const bg = $('<div class="btn-group me-2 mb-1" role="group"></div>')
                    .attr('aria-label', 'Vorlagen ' + scopeLabel[scope]);
                list.forEach(set => {
                    const fmt = (set.options && set.options.format) ? ' (' + set.options.format + ')' : '';
                    const b = $('<button type="button" class="btn btn-light border-dark btn-sm"></button>')
                        .text(set.label)
                        .attr('title', scopeLabel[scope] + fmt + ' – öffnet die Auswahl');
                    b.on('click', () => openParamPicker(set.id));
                    bg.append(b);
                });
                container.append(bg);
            });
        }, container);
    }

    // =====================================================================
    //  PICKER-MODAL
    // =====================================================================

    function getParamPickerModal() {
        if (!paramPickerModalInstance) {
            paramPickerModalInstance = new bootstrap.Modal(document.getElementById('paramPickerModal'));
        }
        return paramPickerModalInstance;
    }

    /**
     * Öffnet den Picker. setId != null -> mit Vorlage vorbefüllt,
     * sonst mit Projekt-Vorauswahl.
     */
    function openParamPicker(setId) {
        ensureCustomDefs(function (defs) {
            buildPickerGroups(defs.groups || []);
            fillPickerSetSelect(defs.sets || [], setId || '');

            if (setId) {
                const set = (defs.sets || []).find(s => s.id === setId);
                applyPickerSelection(set ? (set.params || []) : [], set ? (set.options || {}) : {});
            } else {
                applyPickerSelection(defs.preselected || [], defs.defaults || {});
            }
            getParamPickerModal().show();
        });
    }

    /**
     * Baut die Checkbox-Gruppen (Sondergruppen wie Med.-tech. werden über
     * die mt_mode-Auswahl gesteuert und hier ausgelassen).
     */
    function buildPickerGroups(groups) {
        const wrap = $('#pickerGroups').empty();

        groups.forEach((g, gi) => {
            if (g.special) return;
            const params = g.params || [];
            if (!params.length) return;

            const col = $('<div class="col-12 col-md-6 col-lg-4"></div>');
            const card = $('<div class="card h-100"></div>');

            const head = $('<div class="card-header py-1 px-2 d-flex align-items-center justify-content-between"></div>');
            head.append($('<span class="fw-semibold small"></span>').text(g.label));
            const grp = $('<div class="btn-group btn-group-sm"></div>');
            const bAll = $('<button type="button" class="btn btn-outline-secondary py-0">alle</button>');
            const bNone = $('<button type="button" class="btn btn-outline-secondary py-0">keine</button>');
            bAll.on('click', () => { card.find('input.picker-param').prop('checked', true); updatePickerCount(); });
            bNone.on('click', () => { card.find('input.picker-param').prop('checked', false); updatePickerCount(); });
            grp.append(bAll, bNone);
            head.append(grp);
            card.append(head);

            const body = $('<div class="card-body py-2 px-2"></div>');
            params.forEach((p, pi) => {
                const id = 'pk_' + gi + '_' + pi;
                const text = (p.label || p.key).replace(/[:\s]+$/, '');   // trailing ": " weg
                const fc = $('<div class="form-check"></div>');
                const inp = $('<input class="form-check-input picker-param" type="checkbox">')
                    .attr('id', id).val(p.key);
                inp.on('change', updatePickerCount);
                const lbl = $('<label class="form-check-label small"></label>').attr('for', id).text(text);
                fc.append(inp, lbl);
                body.append(fc);
            });
            card.append(body);
            col.append(card);
            wrap.append(col);
        });
    }

    /**
     * Füllt die Vorlagen-Auswahl im Modal (Projekt zuerst, dann allgemein).
     */
    function fillPickerSetSelect(sets, selectedId) {
        const sel = $('#pickerSetSelect').empty();
        sel.append($('<option value="">— keine Vorlage (freie Auswahl) —</option>'));
        const scopeLabel = {project: 'Projekt', general: 'Allgemein'};
        ['project', 'general'].forEach(scope => {
            const list = sets.filter(s => (s.scope || 'general') === scope);
            if (!list.length) return;
            const og = $('<optgroup>').attr('label', scopeLabel[scope]);
            list.forEach(set => og.append($('<option>').val(set.id).text(set.label)));
            sel.append(og);
        });
        sel.val(selectedId || '');
    }

    /**
     * Setzt Häkchen + Optionen anhand einer Key-Liste und Options (Set/Default).
     */
    function applyPickerSelection(keys, options) {
        const set = new Set(keys || []);
        $('#pickerGroups input.picker-param').each(function () {
            this.checked = set.has(this.value);
        });

        const d = (customDefsCache && customDefsCache.defaults) || {};
        const o = options || {};
        $('#pickerFormat').val(o.format || d.format || 'A3');
        $('#pickerMtMode').val(o.mt_mode || d.mt_mode || 'list');
        const mc = (o.mark_changes !== undefined) ? o.mark_changes : d.mark_changes;
        $('#pickerMarkChanges').prop('checked', !!mc);

        updatePickerCount();
    }

    function updatePickerCount() {
        const n = $('#pickerGroups input.picker-param:checked').length;
        $('#pickerCount').text(n + ' Parameter ausgewählt');
    }

    /**
     * Erzeugt das PDF aus der aktuellen Picker-Auswahl (params schlägt set).
     */
    function generateFromPicker() {
        const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
        if (!roomIDs.length) {
            alert("Kein Raum ausgewählt!");
            return;
        }

        const keys = $('#pickerGroups input.picker-param:checked').map(function () {
            return this.value;
        }).get();

        const mtMode = $('#pickerMtMode').val();
        if (!keys.length && mtMode === 'none') {
            alert("Keine Parameter ausgewählt.");
            return;
        }

        const date = $('#dateSelect').val() || getDate("#dateSelect");
        // Leere Auswahl bewusst erzwingen (sonst würde der Generator auf die
        // Projekt-Vorauswahl zurückfallen): Sentinel, der auf keinen Key matcht.
        const csv = keys.length ? keys.join(',') : '__none__';
        const params = encodeURIComponent(csv);
        const format = $('#pickerFormat').val();
        const markChanges = $('#pickerMarkChanges').is(':checked') ? 1 : 0;

        const url = `${CUSTOM_PDF_DIR}/pdf_createBauangabenBericht_Custom.php`
            + `?roomID=${roomIDs.join(',')}&date=${date}`
            + `&params=${params}&format=${format}&mt_mode=${mtMode}&mark_changes=${markChanges}`;

        // --- Logging ---
        $.post('log_report_download.php', {
            reportUrl:  'pdf_createBauangabenBericht_Custom.php',
            reportText: 'Custom-Auswahl (' + keys.length + ' Params)',
            roomIDs:    roomIDs.join(',')
        });
        // ----------------

        window.open(url);
        getParamPickerModal().hide();
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
            reportUrl:  report.url,
            reportText: report.text,
            roomIDs:    roomIDs.join(',')
        });
        // ----------------

        window.open(url);
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