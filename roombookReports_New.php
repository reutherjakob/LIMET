<?php
session_start();
include '_utils.php';
init_page_serversides();
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

            .fix_size {
                padding:0;
                margin: 1px;
                height: 30px;
                font-size: 15px;
            }
            .btn, .dt-button {
                padding: 0;
                margin: 1px;
                height: 35px;
            }
        </style>
    </head>
    <body style="height:100%">
        <div class="container-fluid">
            <div id="limet-navbar"></div>
            <div class="mt-1 card">
                <div class="card-header d-flex border-light align-items-center" style="height:4px; font-size: 10px;">
                    <div class="col-md-3">Räume im Projekt</div>
                    <div class="col-md-2">Select</div>
                    <div class="col-md-1">Berichte PDFs</div>
                    <div class="col-md-4"></div>
                </div>
                <div class="card-header d-inline-flex justify-content-start align-items-bottom form-check-inline" style="height: 33px;" id="HeaderTabelleCard">
                    <div class="col-md-3 form-check-inline" id="sub1"></div>
                    <div class="col-md-6 form-check-inline" id="sub2"></div>
                    <div class="col-md-3 form-check-inline justify-content-end" id="sub3"></div>
                </div>
                <div class="card-header form-check-inline justify-content" style="flex-wrap:nowrap; display:none; padding:5px; " id="HeaderTabelleCard2"></div>
                <div class="card-body px-0">
                    <?php
                    $mysqli = utils_connect_sql();
                    $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Raumnummer_Nutzer, tabelle_räume.Nutzfläche, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_räume.idTABELLE_Räume, tabelle_räume.`MT-relevant` FROM tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte WHERE tabelle_projekte.idTABELLE_Projekte=" . $_SESSION["projectID"];
                    $result = $mysqli->query($sql);
                    echo "<table class='table display compact table-striped table-bordered table-sm' id='tableRooms' cellspacing='0' width='100%'>
                        <thead><tr>
                        <th>ID</th>
                        <th>Raumnr</th>
                        <th>Raumbezeichnung</th>
                        <th>Nutzfläche</th>
                        <th>Raumbereich Nutzer</th>
                        <th>MT-relevant</th>
                        <th>Ebene</th>
                        <th>Bauetappe</th>
                        <th>Bauabschnitt</th>
                        <th>RNR Nutzer</th>
                        </tr></thead><tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['idTABELLE_Räume']}</td>
                            <td>{$row['Raumnr']}</td>
                            <td>{$row['Raumbezeichnung']}</td>
                            <td>{$row['Nutzfläche']}</td>
                            <td>{$row['Raumbereich Nutzer']}</td>
                            <td>" . ($row['MT-relevant'] == '0' ? 'Nein' : 'Ja') . "</td>
                            <td>{$row['Geschoss']}</td>
                            <td>{$row['Bauetappe']}</td>
                            <td>{$row['Bauabschnitt']}</td>
                            <td>{$row['Raumnummer_Nutzer']}</td>
                          </tr>";
                    }
                    echo "</tbody></table>";
                    $mysqli->close();
                    ?>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                initDataTable();
                addMTFilter('#sub1');
                addDateSelect('#sub3');
                initButtons('#sub2', '#HeaderTabelleCard2');
                setTimeout(() => {
                    moveSearchBox('sub1');
                    addCheckbox('#sub3', "Show-old-Reports", toggleOldReports);
                    let searchbuilder = [{
                            extend: 'searchBuilder',
                            className: "btn fas fa-search",
                            text: "",
                            titleAttr: "Suche konfigurieren"
                        }];
                    new $.fn.dataTable.Buttons(table, {buttons: searchbuilder}).container().appendTo($('#sub1'));
                }, 500);

            });

            function initDataTable() {
                table = $('#tableRooms').DataTable({
                    paging: false,
                    columnDefs: [{targets: [0], visible: false, searchable: false}],
                    orderCellsTop: true,
                    order: [[1, "asc"]],
                    scrollY: '75vh',
                    scrollCollapse: true,
                    dom: 'frtip', // Added 'Q' for SearchBuilder button
                    select: {style: 'multi'},
                    language: {
//                        url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
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
                $(location).append('<select class="form-control-sm fix_size" id="columnFilter"><option value="">MT</option><option value="Ja">Ja</option><option value="Nein">Nein</option></select>');
                $('#columnFilter').change(function () {
                    table.column(5).search($(this).val()).draw();
                });
            }

            function addDateSelect(location) {
                $(location).append('<div class="form-check-inline"><label for="dateSelect"> </label><input type="date" id="dateSelect" name="dateSelect"><div class="spacer"></div></div>');
            }

            function getDate() {
                let date = new Date($("#dateSelect").val() || Date.now());
                return `${('0' + date.getDate()).slice(-2)}-${('0' + (date.getMonth() + 1)).slice(-2)}-${date.getFullYear()}`;
            }

            function addCheckbox(location, name, callback) {
                $(location).append(`<input type="checkbox" id="CBX${name}" class="form-check-input"><label for="CBX${name}" class="form-check-label">${name}</label>`);
                $(`#CBX${name}`).change(callback);
            }

            function moveSearchBox(location) {
                $('#dt-search-0').appendTo(`#${location}`).addClass("fix_size");
            }

            function initButtons(location, oldReportsLocation) {
                const buttons = [
                    {text: 'All', action: () => table.rows().select()},
                    {text: 'Visible', action: () => table.rows(':visible').select()},
                    {text: 'None', action: () => table.rows().deselect()},
                    {extend: 'spacer', style: 'bar'},
                    {text: "BAUANGABEN A3", action: generateReport}
                ];

                new $.fn.dataTable.Buttons(table, {buttons}).container().appendTo($(location));
                initOldButtons(oldReportsLocation);
            }

            function initOldButtons(location) {
                const oldButtons = [
                    {text: "Raumbuch-PDF", link: "pdf_createRoombookPDF"},
                    {text: "Raumbuch-0-PDF", link: "pdf_createRoombookWithout0PDF"},
                    {text: "Raumbuch-ohne Bestand-PDF", link: "pdf_createRoombookWithoutBestandPDF"},
                    {text: "Raumbuch-0-ohne Bestand-PDF", link: "pdf_createRoombookWithout0WothoutBestandPDF"},
                    {text: "Raumbuch-inkl Bauangaben-0-PDF", link: "pdf_createRoombookWithBauangabenWithout0PDF"},
                    {text: "Bauangaben-PDF V1", link: "pdf_createBauangabenPDF"},
                    {text: "Bauangaben-PDF V2", link: "pdf_createBauangabenV2PDF"},
                    {text: "Bauangaben ohne Elemente-PDF", link: "pdf_createBauangabenWithoutElementsPDF"},
                    {text: "Bauangaben Lab-PDF", link: "pdf_createBauangabenLabPDF"},
                    {text: "Bauangaben Lab-Kurz-PDF'", link: "pdf_createBauangabenLabKompaktPDF"},
                    {text: "Bauangaben Lab-ENT-PDF", link: "pdf_createBauangabenLabEntPDF"},
                    {text: "Bauangaben Lab-EIN-PDF", link: "pdf_createBauangabenLabEinrPDF_1"},
                    {text: "BO-PDF", link: "pdf_createBOPDF"},
                    {text: "BauangabenDetail-PDF", link: "pdf_createBauangabenDetailPDF"},
                    {text: "VE-Gesamt-PDF", link: "pdf_createBericht_VE_PDF"},
                    {text: "ENT-Gesamt-PDF", link: "pdf_createBericht_ENT_PDF"},
                    {text: "Nutzer Formular", link: "pdf_createUserFormPDF"}
                ];

                new $.fn.dataTable.Buttons(table, {
                    buttons: oldButtons.map(btn => ({
                            text: btn.text,
                            className: "btn-xs " + btn.link + " fix_size",
                            action: () => generateOldReport(btn.link)
                        }))
                }).container().appendTo($(location));
            }

            function generateReport() {
                const roomIDs = table.rows({selected: true}).data().toArray().map(row => row[0]);
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    window.open(`/pdf_createBauangabenBericht_A3Qeer.php?roomID=${roomIDs.join(',')}&date=${getDate()}`);
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
            }

            /* 
             
             var table;
             const btns = [
             {text: "Raumbuch-PDF", link: "pdf_createRoombookPDF"},
             {text: "Raumbuch-0-PDF", link: "pdf_createRoombookWithout0PDF"},
             {text: "Raumbuch-ohne Bestand-PDF", link: "pdf_createRoombookWithoutBestandPDF"},
             {text: "Raumbuch-0-ohne Bestand-PDF", link: "pdf_createRoombookWithout0WothoutBestandPDF"},
             {text: "Raumbuch-inkl Bauangaben-0-PDF", link: "pdf_createRoombookWithBauangabenWithout0PDF"},
             {text: "Bauangaben-PDF V1", link: "pdf_createBauangabenPDF"},
             {text: "Bauangaben-PDF V2", link: "pdf_createBauangabenV2PDF"},
             {text: "Bauangaben ohne Elemente-PDF", link: "pdf_createBauangabenWithoutElementsPDF"},
             {text: "Bauangaben Lab-PDF", link: "pdf_createBauangabenLabPDF"},
             {text: "Bauangaben Lab-Kurz-PDF'", link: "pdf_createBauangabenLabKompaktPDF"},
             {text: "Bauangaben Lab-ENT-PDF", link: "pdf_createBauangabenLabEntPDF"},
             {text: "Bauangaben Lab-EIN-PDF", link: "pdf_createBauangabenLabEinrPDF_1"},
             {text: "BO-PDF", link: "pdf_createBOPDF"},
             {text: "BauangabenDetail-PDF", link: "pdf_createBauangabenDetailPDF"},
             {text: "VE-Gesamt-PDF", link: "pdf_createBericht_VE_PDF"},
             {text: "ENT-Gesamt-PDF", link: "pdf_createBericht_ENT_PDF"},
             {text: "Nutzer Formular", link: "pdf_createUserFormPDF"}
             ];
             
             $(document).ready(function () {
             init_dt();
             add_MT_rel_filter('#sub1');
             add_date_select();
             init_btns_old('#HeaderTabelleCard2');
             setTimeout(function () {
             move_dt_search('sub1');
             addCheckbox('#sub3', "Old Reports");
             add_btn_vis_checkbox_functionality("Show-old-Reports");
             }, 500);
             init_btns('#sub2');
             });
             
             function add_date_select() {
             var cardHeader = document.getElementById('sub3');
             var newElement = document.createElement('div');
             newElement.className = 'form-check-inline';
             newElement.innerHTML = '<div class="form-check-inline"><label for="dateSelect"> </label> <input type="date" id="dateSelect" name="dateSelect"><div class="spacer"></div></div>';
             cardHeader.appendChild(newElement);
             }
             
             function getDate() {
             var dateInput = $("#dateSelect").val();
             var date = dateInput ? new Date(dateInput) : new Date();//                                    console.log("Date: ", date);
             var day = date.getDate();//                                    console.log("Day: ", day);
             var month = date.getMonth() + 1; // Months are zero based //                                    console.log("Month: ", month);
             var year = date.getFullYear();//                                    console.log("Year: ", year);
             day = ('0' + day).slice(-2);//                                    console.log("Formatted Day: ", day);
             month = ('0' + month).slice(-2);//                                    console.log("Formatted Month: ", month);
             var formattedDate = day + '-' + month + '-' + year;
             console.log("Formatted Date: ", formattedDate);
             return formattedDate;
             }
             
             function addCheckbox(location, name, css = "") {
             var checkbox = document.createElement('input');
             checkbox.type = 'checkbox';
             checkbox.id = 'CBX' + name;
             checkbox.checked = false;
             checkbox.classList.add("form-check-input");
             if (css.trim() !== "") {
             checkbox.classList.add(css);
             }
             var label = document.createElement('label');
             label.htmlFor = 'CBX' + name;
             label.classList.add("form-check-label");
             label.appendChild(document.createTextNode(name));
             document.querySelector(location).appendChild(checkbox);
             document.querySelector(location).appendChild(label);
             }
             
             function add_MT_rel_filter(location) {
             var dropdownHtml = '<select class="form-control-sm fix_size" id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
             $(location).append(dropdownHtml);
             $('#columnFilter').change(function () {
             var filterValue = $(this).val();
             table.column(5).search(filterValue).draw();
             });
             }
             
             function move_dt_search(location) {
             var dt_searcher = document.getElementById("dt-search-0");
             dt_searcher.parentNode.removeChild(dt_searcher);
             document.getElementById(location).appendChild(dt_searcher);
             dt_searcher.classList.add("fix_size");
             }
             
             function init_dt() {
             table = $('#tableRooms').DataTable({
             "paging": false,
             "columnDefs": [
             {
             "targets": [0],
             "visible": false,
             "searchable": false
             }
             ],
             "orderCellsTop": true,
             "order": [[1, "asc"]],
             "scrollY": '75vh',
             "scrollCollapse": true,
             dom: 'frti',
             select: {
             style: 'multi'
             },
             language: {
             "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
             search: "",
             searchBuilder: {
             label: "Search",
             depthLimit: 3
             }
             },
             keys: true
             });
             table.on('key', function (e, datatable, key, cell, originalEvent) {
             if ([37, 38, 39, 40].includes(key)) {
             $(cell.node()).click();
             }
             });
             }
             
             function init_btns_old(location) {
             let spacer = {extend: 'spacer', style: 'bar'};
             new $.fn.dataTable.Buttons(table, {
             buttons: [
             btns.map(btn => ({
             text: btn.text,
             className: "btn-xs " + btn.link + " fix_size",
             action: function () {
             var count = table.rows({selected: true}).data();
             var roomIDs = [];
             for (var i = 0; i < count.length; i++) {
             roomIDs.push(count[i][0]);
             }
             if (roomIDs.length === 0) {
             alert("Kein Raum ausgewählt!");
             } else {
             window.open('/' + btn.link + '.php?roomID=' + roomIDs);
             }
             }}))
             ]}).container().appendTo($(location));
             }
             
             function init_btns(location) {
             let spacer = {extend: 'spacer', style: 'bar'};
             new $.fn.dataTable.Buttons(table, {
             buttons: [
             {extend: 'searchBuilder', label: "Search"},
             spacer,
             {
             text: 'All',
             action: function () {
             table.rows().select();
             }
             }, {
             text: 'Visible',
             action: function () {
             table.rows(':visible').select();
             }
             },
             {
             text: 'None',
             action: function () {
             table.rows().deselect();
             }
             },
             
             spacer,
             {
             text: "BAUANGABEN A3",
             action: function () {
             var count = table.rows({selected: true}).data();
             var roomIDs = [];
             for (var i = 0; i < count.length; i++) {
             roomIDs.push(count[i][0]);
             }
             if (roomIDs.length === 0) {
             alert("Kein Raum ausgewählt!");
             } else {
             let date = getDate();
             //                                    const bools2int2str = report_input_bools.map((bool) => (bool ? 1 : 0)).join(',');
             window.open('/pdf_createBauangabenBericht_A3Qeer.php?roomID=' + roomIDs + "&date=" + date);// + "&PDFinputs=" + bools2int2str 
             //                                                            window.open('/pdf_createBericht_custom.php?roomID=' + roomIDs + "&PDFinputs=" + bools2int2str);  //custom bericht page ! 
             }
             }
             }, spacer
             //,{
             //                            text: "Bauang. Text",
             //                            action: function () {
             //                                var count = table.rows({selected: true}).data();
             //                                var roomIDs = [];
             //                                for (var i = 0; i < count.length; i++) {
             //                                    roomIDs.push(count[i][0]);
             //                                }
             //                                if (roomIDs.length === 0) {
             //                                    alert("Kein Raum ausgewählt!");
             //                                } else {
             //                                    let date = getDate();
             //                                    const bools2int2str = report_input_bools.map((bool) => (bool ? 1 : 0)).join(',');
             //                                    window.open('/pdf_createVBM_Bericht.php?date=' + date + "&roomID=" + roomIDs);// + "&PDFinputs=" + bools2int2str 
             ////                                                            window.open('/pdf_createBericht_custom.php?roomID=' + roomIDs + "&PDFinputs=" + bools2int2str);  //custom bericht page ! 
             //                                }
             //                            }
             //                        },
             //                        spacer
             ]}).container().appendTo($(location));
             }
             
             function add_btn_vis_checkbox_functionality(name) {
             document.getElementById("CBX" + name).addEventListener('change', function () {
             $('#HeaderTabelleCard2').slideToggle();
             });
             }
             //            const report_input_bool_labels = ["Bestandsäume(x)", "Bestands-MT(x)", "BO-Beschr.", "Allgemein", "ET", "HT", "MEDGAS", "BauStatik", "MT-Tabelle", "MT-Liste", "LAB(x)"];
             //            let report_input_bools = new Array(report_input_bool_labels.length).fill(true);
             //            function add_Berichtinput_checkboxes(location) {
             //                for (let i = 0; i < report_input_bool_labels.length; i++) {
             //                    addCheckbox(location, report_input_bool_labels[i], "report_input");
             //                }
             //                const checkboxes = document.querySelectorAll('.report_input');
             //                checkboxes.forEach((checkbox, index) => {
             //                    checkbox.addEventListener('change', () => {
             //                        report_input_bools[index] = checkbox.checked;
             //                        console.log(`Checkbox "${report_input_bool_labels[index]}" is now ${checkbox.checked}`);
             //                    });
             //                });
             //            }
             */
        </script>
    </body>
</html>
