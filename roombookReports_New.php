<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides("", "x");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Berichte</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png"/>

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
    <div class="card">
        <div class="card-header px-1 py-1 text-nowrap" id="HeaderTabelleCard">
            <div class="row">
                <div class="col-xxl-1 d-flex justify-content-start " id="">Select</div>
                <div class="col-xxl-5 d-flex justify-content-start " id="sub1"> </div>
                <div class="col-xxl-6 d-flex justify-content-start " id="">
                    <label for="dateSelect"></label> <input type="date" id="dateSelect" name="dateSelect"> &ensp;
                </div>
            </div>
        </div>

        <div class="card-header px-1 py-1" id="HeaderTabelleCard2">
            <div class="row">
                <div class="col-xxl-1 d-flex justify-content-start " id="">Bauangaben</div>
                <div class="col-xxl-5 d-flex justify-content-start " id="sub23"></div>
                <div class="col-xxl-6 d-flex justify-content-start " id="sub2"></div>
            </div>
        </div>

        <div class="card-header px-1 py-1 ">
            <div class="row">
                <div class="col-xxl-1 d-flex justify-content-start " id="">Raumbuch</div>
                <div class="col-xxl-5 d-flex justify-content-start " id="sub21"> </div>
                <div class="col-xxl-6 d-flex justify-content-start " id="sub22">
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
            echo "<table class='table display compact table-striped table-bordered table-sm' id='tableRooms' >
                        <thead><tr>";
            foreach ($columns as $col) {
                echo "<th>" . str_replace('_', ' ', $col) . "</th>";
            }
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $col) {
                    $value = $row[$col];
                    if ($col == 'MT-relevant') {
                        $value = $value == '0' ? 'Nein' : 'Ja';
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

<script charset="utf-8">
    $(document).ready(function () {
        const dateInput = document.getElementById('dateSelect');
        dateInput.value = new Date().toISOString().split('T')[0];
        initDataTable();
        initButtons();
        setTimeout(() => {
            moveSearchBox('sub1');
            let searchbuilder = [{
                extend: 'searchBuilder',
                className: "btn fas fa-search",
                text: " ",
                titleAttr: "Suche konfigurieren"
            }];
            new $.fn.dataTable.Buttons(table, {buttons: searchbuilder}).container().appendTo($('#sub1'));
        }, 300);
        addMTFilter('#sub1');
        add_entfallen_filter('#sub1');
    });

    function generateNewReports(reportType, date) {
        const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
        if (roomIDs.length === 0) {
            alert("Kein Raum ausgewählt!");
        } else {
            const formattedDate = date || getDate();
            const reportURLs = {
                "BAUANGABEN A3": "/pdf_createBauangabenBericht_A3Qeer.php",
                "BAUANGABEN A3 2": "/pdf_createBauangabenBericht_A3Qeer_1.php",
                "BAUANGABEN A3 3": "/pdf_createBauangabenBericht_A3Qeer_ohne_Lab_params.php",
                "BAUANGABEN A3 4": "/pdf_createBauangabenBericht_A3Qeer_PSy.php",
                "-": "-",
                "Elem./Raum (w/Bestand)": "/pdf_createRoombookElWithoutBestand.php",
                "inkl.Elem.Kommentar": "/pdf_createRoombookElWithoutBestandWithComments.php"
            };

            if (reportURLs[reportType]) {
                window.open(`${reportURLs[reportType]}?roomID=${roomIDs.join(',')}&date=${formattedDate}`);
            } else {
                alert("Unbekannter Berichtstyp!");
            }
        }
    }

    function initButtons() {
        const buttons = [
            {text: 'All', action: () => table.rows().select()},
            {text: 'Visible', action: () => table.rows(':visible').select()},
            {text: 'None', action: () => table.rows().deselect()}
        ];

        const buttonNewReports = [
            {text: "BAU A3", action: () => generateNewReports("BAUANGABEN A3", $("#dateSelect").val())},
            {text: "ohne Lab", action: () => generateNewReports("BAUANGABEN A3 3", $("#dateSelect").val())},
            {text: "ohne Datum", action: () => generateNewReports("BAUANGABEN A3 2", $("#dateSelect").val())},
            {text: "Psy", action: () => generateNewReports("BAUANGABEN A3 4", $("#dateSelect").val())},
            {text: "-"},
            {
                text: "Elem./Raum (w/Bestand)",
                action: () => generateNewReports("Elem./Raum (w/Bestand)", $("#dateSelect").val())
            },
            {
                text: "inkl.Elem.Kommentar",
                action: () => generateNewReports("inkl.Elem.Kommentar", $("#dateSelect").val())
            }
        ];

        const oldButtons = [
            {text: "PDF", link: "pdf_createRoombookPDF"},
            {text: "0-PDF", link: "pdf_createRoombookWithout0PDF"},
            {text: "ohne Bestand-PDF", link: "pdf_createRoombookWithoutBestandPDF"},
            {text: "0-ohne Bestand-PDF", link: "pdf_createRoombookWithout0WothoutBestandPDF"},
            {text: "Bauangaben-0-PDF", link: "pdf_createRoombookWithBauangabenWithout0PDF"}
        ];

        const ButtonsBauangaben = [
            {text: "PDF V1", link: "pdf_createBauangabenPDF"},
            {text: "PDF V2", link: "pdf_createBauangabenV2PDF"},
            {text: "ohne Elemente-PDF", link: "pdf_createBauangabenWithoutElementsPDF"},
            {text: "Detail-PDF", link: "pdf_createBauangabenDetailPDF"},
            {text: "Lab-PDF", link: "pdf_createBauangabenLabPDF"},
            {text: "Lab-Kurz-PDF'", link: "pdf_createBauangabenLabKompaktPDF"},
            {text: "Lab-ENT-PDF", link: "pdf_createBauangabenLabEntPDF"},
            {text: "Lab-EIN-PDF", link: "pdf_createBauangabenLabEinrPDF_1"}
        ];

        const oldButtons2 = [
            {text: "BO-PDF", link: "pdf_createBOPDF"},
            {text: "VE-Gesamt-PDF", link: "pdf_createBericht_VE_PDF"},
            {text: "ENT-Gesamt-PDF", link: "pdf_createBericht_ENT_PDF"},
            {text: "Nutzer Formular", link: "pdf_createUserFormPDF"}
        ];

        const createButtonGroup = (buttons, buttonClass) => {
            const buttonGroup = $('<div class="btn-group" role="group"></div>');
            buttons.forEach(btn => {
                const button = $('<button type="button" class="btn btn-sm ' + buttonClass + '"></button>').text(btn.text);
                button.on('click', btn.action || (() => generateOldReport(btn.link)));
                buttonGroup.append(button);
            });
            return buttonGroup;
        };

        $('#sub1').append(createButtonGroup(buttons, 'btn-success'));
        $('#sub2').append(createButtonGroup(buttonNewReports, 'btn-light border-dark'));
        $('#sub21').append(createButtonGroup(oldButtons, 'btn-light border-dark'));
        $('#sub22').append(createButtonGroup(oldButtons2, 'btn-light border-dark'));
        $('#sub23').append(createButtonGroup(ButtonsBauangaben, 'btn-light  border-dark'));

    }

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
            keys: true
        });
    }

    function addMTFilter(location) {
        $(location).append('<select class="form-control-sm" id="columnFilter"><option value="">MT</option><option selected="true"  value="Ja">Ja</option><option value="Nein">Nein</option></select>');
        $('#columnFilter').change(function () {
            table.column(1).search($(this).val()).draw();
        });
        table.column(1).search("Ja").draw();
    }

    function add_entfallen_filter(location) {
        var dropdownHtml2 = '<select class="form-control-sm" id="EntfallenFilter">' + '<option value="">Entf</option><option value="1">1</option>' + '<option selected ="true" value="0">0</option></select>';
        $(location).append(dropdownHtml2);

        $('#EntfallenFilter').change(function () {
            var filterValue = $(this).val();
            table.column(11).search(filterValue).draw();
        });
        table.column(11).search(0).draw();
    }


    function getDate() {
        let date = new Date($("#dateSelect").val() || Date.now());
        return `${('0' + date.getDate()).slice(-2)}-${('0' + (date.getMonth() + 1)).slice(-2)}-${date.getFullYear()}`;
    }

    function addCheckbox(location, name, callback) {
        $(location).append(`<input type="checkbox" id="CBX${name}" class="form-check-input" ><label for="CBX${name}" class="form-check-label">${name}</label>`);
        $(`#CBX${name}`).change(callback);
    }

    function moveSearchBox(location) {
        $('#dt-search-0').appendTo(`#${location}`).addClass("btn-sm");
    }

    function generateOldReport(link) {
        const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
        if (roomIDs.length === 0) {
            alert("Kein Raum ausgewählt!");
        } else {
            window.open(`/${link}.php?roomID=${roomIDs.join(',')}`);
        }
    }
</script>
</body>
</html>
