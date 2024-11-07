<?php
session_start();
include '_utils.php';
init_page_serversides("", "x");
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
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
            .dt-input,.dt-button,.dt-buttons,.btn{
                height: 35px !important; 
            }
            .card-header{
                padding: 5px;
            }
        </style>
    </head>
    <body style="height:100%">
        <div class="container-fluid"> 
            <div id="limet-navbar"></div>
            <div class="card mt-2 border-success"> 
                <div class="card-header border-light  d-inline-flex" id="HeaderTabelleCard" style="height: 40px;">
                    <div class="col-md-3 d-flex  align-items-center" id="sub1"> </div>
                    <div class="col-md-3 d-flex  align-items-center" id="sub12">  </div>
                    <div class="col-md-3 d-flex  align-items-center" id="sub2">  </div>
                    <div class="col-md-3 d-flex form-check-inline  justify-content-end align-items-center" id="sub3">
                        <div class="form-check-inline"> <label for="dateSelect"> </label><input type="date" id="dateSelect" name="dateSelect"><div class="spacer"></div></div>
                    </div> 
                </div>         
                <div class="card-header border-light d-flex align-items-center"  id="HeaderTabelleCard2"> </div> 
                <div class="card-header border-light d-flex align-items-center"  id="HeaderTabelleCard3"> </div> 




                <div class="card-body border-dark px-2 py-2">
                    <?php
                    $mysqli = utils_connect_sql();
                    $columns = [
                        'idTABELLE_Räume', 'MT-relevant', 'Raumbezeichnung', 'Raumnr', 'Raumnummer_Nutzer',
                        'Raumbereich Nutzer', 'Geschoss', 'Bauetappe', 'Bauabschnitt', 'Nutzfläche',
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
                addMTFilter('#sub1');
                initButtons();
                setTimeout(() => {
                    moveSearchBox('sub1');
                    addCheckbox('#sub3', "Alte_Berichte", toggleOldReports);
                    let searchbuilder = [{
                            extend: 'searchBuilder',
                            className: "btn fas fa-search",
                            text: " ",
                            titleAttr: "Suche konfigurieren"
                        }];
                    new $.fn.dataTable.Buttons(table, {buttons: searchbuilder}).container().appendTo($('#sub1'));
                }, 300);
            });

            function initButtons() {
                const buttons = [
                    {text: 'All', action: () => table.rows().select()},
                    {text: 'Visible', action: () => table.rows(':visible').select()},
                    {text: 'None', action: () => table.rows().deselect()}
                ];

                const buttonNewReports = [
                    {text: "BAUANGABEN A3", action: generateReport},
                    {text: "Elem./Raum (w/Bestand)", action: generateReport2}
                ];

                const oldButtons = [
                    {text: "Raumbuch-PDF", link: "pdf_createRoombookPDF"},
                    {text: "Raumbuch-0-PDF", link: "pdf_createRoombookWithout0PDF"},
                    {text: "Raumbuch-ohne Bestand-PDF", link: "pdf_createRoombookWithoutBestandPDF"},
                    {text: "Raumbuch-0-ohne Bestand-PDF", link: "pdf_createRoombookWithout0WothoutBestandPDF"},
                    {text: "Raumbuch-inkl Bauangaben-0-PDF", link: "pdf_createRoombookWithBauangabenWithout0PDF"},
                ];

                const ButtonsBauangaben = [{text: "Bauangaben-PDF V1", link: "pdf_createBauangabenPDF"},
                    {text: "Bauangaben-PDF V2", link: "pdf_createBauangabenV2PDF"},
                    {text: "Bauangaben ohne Elemente-PDF", link: "pdf_createBauangabenWithoutElementsPDF"},
                    {text: "BauangabenDetail-PDF", link: "pdf_createBauangabenDetailPDF"},
                    {text: "Bauangaben Lab-PDF", link: "pdf_createBauangabenLabPDF"},
                    {text: "Bauangaben Lab-Kurz-PDF'", link: "pdf_createBauangabenLabKompaktPDF"},
                    {text: "Bauangaben Lab-ENT-PDF", link: "pdf_createBauangabenLabEntPDF"},
                    {text: "Bauangaben Lab-EIN-PDF", link: "pdf_createBauangabenLabEinrPDF_1"}];

                const oldButtons2 = [
                    {text: "BO-PDF", link: "pdf_createBOPDF"},
                    {text: "VE-Gesamt-PDF", link: "pdf_createBericht_VE_PDF"},
                    {text: "ENT-Gesamt-PDF", link: "pdf_createBericht_ENT_PDF"},
                    {text: "Nutzer Formular", link: "pdf_createUserFormPDF"}
                ];

                const createButtonGroup = (buttons, buttonClass) => {
                    const buttonGroup = $('<div class="btn-group" role="group"></div>');
                    buttons.forEach(btn => {
                        const button = $('<button type="button" class="btn btn-sm  ' + buttonClass + '"></button>').text(btn.text);
                        button.on('click', btn.action || (() => generateOldReport(btn.link)));
                        buttonGroup.append(button);
                    });
                    return buttonGroup;
                };

                $('#sub12').append(createButtonGroup(buttons, 'btn-outline-success'));
                $('#sub2').append(createButtonGroup(buttonNewReports, 'btn-outline-dark'));
                $('#HeaderTabelleCard2').append(createButtonGroup(oldButtons, 'btn-outline-dark'));
                $('#HeaderTabelleCard3').append(createButtonGroup(ButtonsBauangaben, 'btn-outline-dark'));
                $('#HeaderTabelleCard2').append(createButtonGroup(oldButtons2, 'btn-outline-dark'));
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
                }).on('key', function (e, datatable, key, cell) {
                    if ([37, 38, 39, 40].includes(key))
                        $(cell.node()).click();
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
                $(location).append(`<input type="checkbox" checked="true" id="CBX${name}" class="form-check-input"><label for="CBX${name}" class="form-check-label">${name}</label>`);
                $(`#CBX${name}`).change(callback);
            }

            function moveSearchBox(location) {
                $('#dt-search-0').appendTo(`#${location}`);
            }



            function generateReport() {
                const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    window.open(`/pdf_createBauangabenBericht_A3Qeer.php?roomID=${roomIDs.join(',')}&date=${getDate()}`);
                }
            }

            function generateReport2() {
                const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    window.open(`/pdf_createRoombookElWithoutBestand.php?roomID=${roomIDs.join(',')}&date=${getDate()}`);
                }
            }

            function generateOldReport(link) {
                const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    window.open(`/${link}.php?roomID=${roomIDs.join(',')}`);
                }
            }

            function toggleOldReports() {
                $('#HeaderTabelleCard2').slideToggle();
                $('#HeaderTabelleCard3').slideToggle();

            }

        </script>
    </body>
</html>
