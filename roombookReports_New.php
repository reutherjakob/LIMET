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
        <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
            <link rel="icon" href="iphone_favicon.png">

                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">

                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
                        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

                        <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet">

                            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
                            <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>

                            </head>

                            <body style="height:100%"> 
                                <div class="container-fluid" >
                                    <div id="limet-navbar" class=' '> </div> 

                                    <div class="mt-4 card">
                                        <div class="card-header form-check-inline form-check-inline" id ="HeaderTabelleCard">

                                        </div> 
                                        <div class="card-header form-check-inline form-check-inline" id ="HeaderTabelleCard2">
                                            <div class="form-group"> 
                                                <label for="dateSelect"> Änderugen bis:</label>
                                                <input type="date" id="dateSelect" name="dateSelect">
                                            </div>
                                        </div> 
                                        <!--<div class="card-header d-inline-flex form-check form-check-inline" id ="HeaderTabelleCard3"></div>-->
                                        <div class="card-body">
                                            <?php
                                            $mysqli = utils_connect_sql();

                                            $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Nutzfläche, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, 
						tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_räume.idTABELLE_Räume, tabelle_räume.`MT-relevant`
								FROM tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
								WHERE (((tabelle_projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

                                            $result = $mysqli->query($sql);

                                            echo "<table class='table display compact table-striped table-bordered table-sm' id='tableRooms'  cellspacing='0' width='100%'>
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
						</tr></thead><tbody>";

                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["idTABELLE_Räume"] . "</td>";
                                                echo "<td>" . $row["Raumnr"] . "</td>";
                                                echo "<td>" . $row["Raumbezeichnung"] . "</td>";
                                                echo "<td>" . $row["Nutzfläche"] . "</td>";
                                                echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                                                if ($row["MT-relevant"] == '0') {
                                                    echo "<td>Nein</td>";
                                                } else {
                                                    echo "<td>Ja</td>";
                                                }
                                                echo "<td>" . $row["Geschoss"] . "</td>";
                                                echo "<td>" . $row["Bauetappe"] . "</td>";
                                                echo "<td>" . $row["Bauabschnitt"] . "</td>";
                                                echo "</tr>";
                                            }
                                            echo "</tbody></table>";
                                            $mysqli->close();
                                            ?>	
                                        </div>
                                    </div>
                                    <div class="mt-4 card">
                                        <div class="card-header form-check-inline form-check-inline" id ="Card2">
                                             
                                        </div> 
                                        <!--<div class="card-header d-inline-flex form-check form-check-inline" id ="HeaderTabelleCard3"></div>-->
                                        <div class="card-body" id ="CB2"></div></div>
                                </div>

                                <script>
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

                                    const report_input_bool_labels = ["Bestandsäume(x)", "Bestands-MT(x)", "BO-Beschr.", "Allgemein", "ET", "HT", "MEDGAS", "BauStatik", "MT-Tabelle", "MT-Liste", "LAB(x)"];
                                    let report_input_bools = new Array(report_input_bool_labels.length).fill(true);

                                    $(document).ready(function () {
                                        init_dt();
                                        add_MT_rel_filter('#HeaderTabelleCard');
                                        init_btns('#HeaderTabelleCard')

                                        var textforoldreports = "Show old Reports";
                                        addCheckbox('#HeaderTabelleCard', textforoldreports);
//                                        add_Berichtinput_checkboxes('#HeaderTabelleCard2');

                                        init_btns_old('#HeaderTabelleCard2');
                                        add_btn_vis_checkbox_functionality(textforoldreports);
                                        setTimeout(function () {
                                            move_dt_search('#HeaderTabelleCard');
                                        }, 50);
                                        // synchronizeCheckboxes("CBXMT-Tabelle", "CBXMT-Liste");
                                    });

                                    function getDate() {
                                        var dateInput = $("#dateSelect").val();
                                        var date = dateInput ? new Date(dateInput) : new Date();
//                                    console.log("Date: ", date);
                                        var day = date.getDate();
//                                    console.log("Day: ", day);
                                        var month = date.getMonth() + 1; // Months are zero based
//                                    console.log("Month: ", month);
                                        var year = date.getFullYear();
//                                    console.log("Year: ", year);
                                        day = ('0' + day).slice(-2);
//                                    console.log("Formatted Day: ", day);
                                        month = ('0' + month).slice(-2);
//                                    console.log("Formatted Month: ", month);
                                        var formattedDate = day + '-' + month + '-' + year;
                                        console.log("Formatted Date: ", formattedDate);
                                        return formattedDate;
                                    }

                                    function add_Berichtinput_checkboxes(location) {
                                        for (let i = 0; i < report_input_bool_labels.length; i++) {
                                            addCheckbox(location, report_input_bool_labels[i], "report_input");
                                        }
                                        const checkboxes = document.querySelectorAll('.report_input');
                                        checkboxes.forEach((checkbox, index) => {
                                            checkbox.addEventListener('change', () => {
                                                report_input_bools[index] = checkbox.checked;
                                                console.log(`Checkbox "${report_input_bool_labels[index]}" is now ${checkbox.checked}`);
                                            });
                                        });
                                    }

                                    function synchronizeCheckboxes(checkbox1Id, checkbox2Id) {
                                        const checkbox1 = document.getElementById(checkbox1Id);
                                        const checkbox2 = document.getElementById(checkbox2Id);
//                                        checkbox2.checked = false;
                                        checkbox1.addEventListener('change', function () {
                                            if (this.checked) {
                                                checkbox2.checked = false;
                                            }
                                        });

                                        checkbox2.addEventListener('change', function () {
                                            if (this.checked) {
                                                checkbox1.checked = false;
                                            }
                                        });
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
                                        var dropdownHtml = '<select class="form-control-sm " id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
                                        $(location).append(dropdownHtml);
                                        $('#columnFilter').change(function () {
                                            var filterValue = $(this).val();
                                            table.column(5).search(filterValue).draw();
                                        });
                                    }

                                    function move_dt_search(location) {
                                        var move = $("#dt-search-0");
                                        $(location).prepend(move);
                                    }

                                    function init_dt() {
                                        table = $('#tableRooms').DataTable({
                                            "paging": false,
//                                            pageLength: 20,
//                                            lengthChange:true,

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
                                                        className: "btn-xs " + btn.link,
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
                                                spacer,
                                                {extend: 'searchBuilder', label: "Search"},
//                                                spacer,
//                                                {text: 'Select:', enabled:false},
                                                {extend: 'spacer', text: "SELECT:", style: 'bar'},
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
                                                            const bools2int2str = report_input_bools.map((bool) => (bool ? 1 : 0)).join(',');
                                                            window.open('/pdf_createBericht_A3Qeer.php?roomID=' + roomIDs+ "&date=" + date);// + "&PDFinputs=" + bools2int2str 
//                                                            window.open('/pdf_createBericht_custom.php?roomID=' + roomIDs + "&PDFinputs=" + bools2int2str);  //custom bericht page ! 
                                                        }
                                                    }
                                                },
                                                spacer
                                            ]}).container().appendTo($(location));
                                    }

                                    function add_btn_vis_checkbox_functionality(name) {
                                        btns.forEach(btn => {
                                            document.querySelector('.' + btn.link).style.display = 'none';
                                        });
                                        document.getElementById("CBX" + name).addEventListener('change', function () {
                                            if (this.checked) {
                                                btns.forEach(btn => {
                                                    document.querySelector('.' + btn.link).style.display = 'inline-block';
                                                });
                                            } else {
                                                btns.forEach(btn => {
                                                    document.querySelector('.' + btn.link).style.display = 'none';
                                                });
                                            }
                                        });
                                    }
                                </script>

                            </body>

                            </html>
