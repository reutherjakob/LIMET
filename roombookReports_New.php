<?php
session_start();
include '_utils.php';
init_page_serversides("", "x");
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
    <head>
        <title>RB-Berichte</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png"/>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
        <style>
            .table>thead>tr>th {
                background-color: rgba(100, 140, 25, 0.15);
            }
        </style>
    </head>
    <body>
        <div class="container-fluid"> 
            <div id="limet-navbar"></div>
            <div class="card">
                <div class="card-header-l px-2 py-2  d-inline-flex align-items-baseline justify-content-start border-light" id="HeaderTabelleCard">
                    <div class="col-md-2 d-inline-flex justify-content-start align-items-baseline" id="sub1"></div>           
                    <div class="col-md-3 d-inline-flex align-items-baseline" id="sub12">Select</div>
                    <div class="col-md-5 d-inline-flex align-items-baseline" id="sub2"></div>

                    <div class="form-check-inline  align-items-baseline"><label for="dateSelect"> </label><input type="date" id="dateSelect" name="dateSelect"><div class="spacer"></div></div>     
                    <div class="col-md-1 form-check-inline justify-content-end  align-items-baseline" id="sub3"></div>
                </div>

                <div class="card-header-s  px-2 py-2 border-light form-check-inline  flex-nowrap" id="HeaderTabelleCard2"> </div>
                <div class="card-header-s  px-2 py-2 border-light form-check-inline  flex-nowrap" id="HeaderTabelleCard3">  </div> 
                <div class="card-body px-2 py-2">
                    <?php
                    $mysqli = utils_connect_sql();
                    $columns = [
                        'idTABELLE_Räume', 'MT-relevant', 'Raumnr', 'Raumbezeichnung', 'Raumnummer_Nutzer', 'Nutzfläche',
                        'Raumbereich Nutzer', 'Geschoss', 'Bauetappe', 'Bauabschnitt',
                        'Anmerkung allgemein'
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
                    echo "<table class='table display compact table-striped table-bordered table-sm' id='tableRooms' cellspacing='0' width='100%'>
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
                initDataTable();
                $('#HeaderTabelleCard2').slideToggle();
                $('#HeaderTabelleCard3').slideToggle();
                initButtons();
                setTimeout(() => {
                    moveSearchBox('sub1');
//                    addCheckbox('#sub3', "Show-old-Reports", toggleOldReports);
                    let searchbuilder = [{
                            extend: 'searchBuilder',
                            className: "btn fas fa-search",
                            text: " ",
                            titleAttr: "Suche konfigurieren"
                        }];
                    new $.fn.dataTable.Buttons(table, {buttons: searchbuilder}).container().appendTo($('#sub1'));
                }, 300);
                addMTFilter('#sub1');


                const toggleOldReportsButton = $('<button type="button" class="btn btn-light border-dark" id="toggleOldReports">Show Old Reports</button>');
                toggleOldReportsButton.on('click', toggleOldReports);
                $('#sub3').append(toggleOldReportsButton);

            });

            function toggleOldReports() {
                $('#HeaderTabelleCard2').slideToggle(() => {
                    $('#HeaderTabelleCard3').slideToggle(() => {
                        const button = $('#toggleOldReports');
                        if ($('#HeaderTabelleCard2').is(':visible') || $('#HeaderTabelleCard3').is(':visible')) {
                            button.text('Hide Old Reports');
                        } else {
                            button.text('Show Old Reports');
                        }
                    });
                });
            }

            function generateNewReports(reportType, date) {
                const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    const formattedDate = date || getDate();
                    const reportURLs = {
                        "BAUANGABEN A3": "/pdf_createBauangabenBericht_A3Qeer.php",
                        "BAUANGABEN A3 2": "/pdf_createBauangabenBericht_A3Qeer_1.php",
                        "Elem./Raum (w/Bestand)": "/pdf_createRoombookElWithoutBestand.php"
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
                    {text: "BAU A3 no date", action: () => generateNewReports("BAUANGABEN A3 2", $("#dateSelect").val())},
                    {text: "Elem./Raum (w/Bestand)", action: () => generateNewReports("Elem./Raum (w/Bestand)", $("#dateSelect").val())}
                ];

                const oldButtons = [
                    {text: "Raumbuch-PDF", link: "pdf_createRoombookPDF"},
                    {text: "Raumbuch-0-PDF", link: "pdf_createRoombookWithout0PDF"},
                    {text: "Raumbuch-ohne Bestand-PDF", link: "pdf_createRoombookWithoutBestandPDF"},
                    {text: "Raumbuch-0-ohne Bestand-PDF", link: "pdf_createRoombookWithout0WothoutBestandPDF"},
                    {text: "Raumbuch-inkl Bauangaben-0-PDF", link: "pdf_createRoombookWithBauangabenWithout0PDF"}
                ];

                const ButtonsBauangaben = [
                    {text: "Bauangaben-PDF V1", link: "pdf_createBauangabenPDF"},
                    {text: "Bauangaben-PDF V2", link: "pdf_createBauangabenV2PDF"},
                    {text: "Bauangaben ohne Elemente-PDF", link: "pdf_createBauangabenWithoutElementsPDF"},
                    {text: "BauangabenDetail-PDF", link: "pdf_createBauangabenDetailPDF"},
                    {text: "Bauangaben Lab-PDF", link: "pdf_createBauangabenLabPDF"},
                    {text: "Bauangaben Lab-Kurz-PDF'", link: "pdf_createBauangabenLabKompaktPDF"},
                    {text: "Bauangaben Lab-ENT-PDF", link: "pdf_createBauangabenLabEntPDF"},
                    {text: "Bauangaben Lab-EIN-PDF", link: "pdf_createBauangabenLabEinrPDF_1"}
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

                $('#sub12').append(createButtonGroup(buttons, 'btn-success btn-sm'));
                $('#sub2').append(createButtonGroup(buttonNewReports, 'btn-light border-dark btn-sm'));
                $('#HeaderTabelleCard2').append(createButtonGroup(oldButtons, 'btn-light  border-dark'));
                $('#HeaderTabelleCard3').append(createButtonGroup(ButtonsBauangaben, 'btn-light  border-dark'));
                $('#HeaderTabelleCard2').append(createButtonGroup(oldButtons2, 'btn-light  border-dark'));
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
                $(location).append('<select class="form-control-sm" id="columnFilter"><option value="">MT</option><option value="Ja">Ja</option><option value="Nein">Nein</option></select>');
                $('#columnFilter').change(function () {
                    table.column(1).search($(this).val()).draw();
                });
            }

            function getDate() {
                let date = new Date($("#dateSelect").val() || Date.now());
                return `${('0' + date.getDate()).slice(-2)}-${('0' + (date.getMonth() + 1)).slice(-2)}-${date.getFullYear()}`;
            }

            function addCheckbox(location, name, callback) {
                $(location).append(`<input type="checkbox" id="CBX${name}" class="form-check-input" checked="false"><label for="CBX${name}" class="form-check-label">${name}</label>`);
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

//            function toggleOldReports() {
//                $('#HeaderTabelleCard2').slideToggle();
//                $('#HeaderTabelleCard3').slideToggle();
//            }

        </script>
    </body>
</html>
