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
                           class="form-control form-control-sm me-2" data-bs-toggle="tooltip"
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

                <div class="col-xxl-1" id="dateSelectContainer">
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
        {value: "", text: "-- Berichtkathegorie auswählen --", disabled: true, selected: true},
        {value: "bauangaben", text: "Bauangaben"},
        {value: "elementReports", text: "Element-/Raum Berichte"},
        {value: "oldReports", text: "Alte Berichte"},

    ];

    const reportCategories = {
        bauangaben: [
            {text: "PDF V1", link: "pdf_createBauangabenPDF"},
            {text: "PDF V2", link: "pdf_createBauangabenV2PDF"},
            {text: "ohne Elemente-PDF", link: "pdf_createBauangabenWithoutElementsPDF"},
            {text: "Detail-PDF", link: "pdf_createBauangabenDetailPDF"},
            {text: "Lab-PDF", link: "pdf_createBauangabenLabPDF"},
            {text: "Lab-Kurz-PDF'", link: "pdf_createBauangabenLabKompaktPDF"},
            {text: "Lab-ENT-PDF", link: "pdf_createBauangabenLabEntPDF"},
            {text: "Lab-EIN-PDF", link: "pdf_createBauangabenLabEinrPDF_1"},
            {text: "BAU A3", reportType: "BAUANGABEN A3"},
            {text: "ohne Lab", reportType: "BAUANGABEN A3 3"},
            {text: "ohne Änderungsmarkierungen", reportType: "BAUANGABEN A3 2"},
            {text: "VE", reportType: "BAUANGABEN A3 4"}
        ],

        elementReports: [
            {text: "Elem./Raum (w/Bestand)", reportType: "Elem./Raum (w/Bestand)"},
            {text: "inkl.Elem.Kommentar", reportType: "inkl.Elem.Kommentar"}
        ],
        oldReports: [
            {text: "PDF", link: "pdf_createRoombookPDF"},
            {text: "0-PDF", link: "pdf_createRoombookWithout0PDF"},
            {text: "ohne Bestand-PDF", link: "pdf_createRoombookWithoutBestandPDF"},
            {text: "0-ohne Bestand-PDF", link: "pdf_createRoombookWithout0WothoutBestandPDF"},
            {text: "Bauangaben-0-PDF", link: "pdf_createRoombookWithBauangabenWithout0PDF"},
            {text: "Bauang. Großgeräte", link: "pdf_createVBM_Bericht"}
            {text: "BO-PDF", link: "pdf_createBOPDF"},
            {text: "VE-Gesamt-PDF", link: "pdf_createBericht_VE_PDF"},
            {text: "ENT-Gesamt-PDF", link: "pdf_createBericht_ENT_PDF"},
            {text: "Nutzer Formular", link: "pdf_createUserFormPDF"}
        ],

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
            scrollY: '75vh',
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
                    text: 'Visible',              className: "btn btn-sm btn-outline-dark bg-white",
                    action: () => table.rows({search: 'applied'}).select()
                },
                {
                    text: 'None',               className: "btn btn-sm btn-outline-dark bg-white",
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

        if (!category || !reportCategories[category]) return;

        const btns = reportCategories[category];
        const btnGroup = $('<div class="btn-group" role="group" aria-label="Berichtsbuttons"></div>');

        btns.forEach(btn => {
            const button = $('<button type="button" class="btn btn-light border-dark btn-sm"></button>').text(btn.text);
            button.on('click', () => generateReport({reportType: btn.reportType, link: btn.link}, $('#dateSelect').val()));
            btnGroup.append(button);
        });
        container.append(btnGroup);
    }

    function generateReport({reportType = null, link = null}, date) {
        const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
        if (!roomIDs.length) {
            alert("Kein Raum ausgewählt!");
            return;
        }
        const formattedDate = date || getDate("#dateSelect");

        if (reportType) {
            const reportMap = {
                "BAUANGABEN A3": "/PDFs/pdf_createBauangabenBericht_A3Qeer.php",
                "BAUANGABEN A3 2": "/PDFs/pdf_createBauangabenBericht_A3Qeer_1.php",
                "BAUANGABEN A3 3": "/PDFs/pdf_createBauangabenBericht_A3Qeer_ohne_Lab_params.php",
                "BAUANGABEN A3 4": "/PDFs/pdf_createBauangabenBericht_A3Qeer_PSy.php",
                "Elem./Raum (w/Bestand)": "/PDFs/pdf_createRoombookElWithoutBestand.php",
                "inkl.Elem.Kommentar": "/PDFs/pdf_createRoombookElWithoutBestandWithComments.php"
            };
            if (reportMap[reportType]) {
                window.open(`${reportMap[reportType]}?roomID=${roomIDs.join(',')}&date=${formattedDate}`);
            } else {
                alert("Unbekannter Berichtstyp!");
            }
        } else if (link) {
            window.open(`/PDFs/${link}.php?roomID=${roomIDs.join(',')}&date=${formattedDate}`);
        } else {
            alert("Berichtstyp oder Link fehlt!");
        }
    }

    function addMTFilter(location) {
        const select = $('<select class="form-select form-select-sm ms-1 me-1" aria-label="MT Filter">' +
            '<option value="">MT</option>' +
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
            '<option value="">Entf</option>' +
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
        $.get('PDFs/pdf_setSession.php', {PDFdatum: val});
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
