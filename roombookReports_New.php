

<!DOCTYPE html>
<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

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

                            <style>
                                .fix_size{
                                    height: 30px !important;
                                    font-size: 16px;
                                }
                                .rotated {
                                    writing-mode: vertical-lr !important; /* Rotate text vertically */
                                    /*transform: rotate(180deg);  Flip the vertical text */
                                }
                            </style>
                            <body style="height:100%"> 
                                <div class="container-fluid" >
                                    <div id="limet-navbar" class=' '> </div> 

                                    <div class="mt-4 card">
                                        <div class="card-header d-inline-flex" id ="HeaderTabelleCard"></div>
                                        <div class="card-body">
                                            <?php
                                            $mysqli = utils_connect_sql();

                                            $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Nutzfläche, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, 
						tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_räume.idTABELLE_Räume, tabelle_räume.`MT-relevant`
								FROM tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
								WHERE (((tabelle_projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

                                            $result = $mysqli->query($sql);

                                            echo "<table class='table table-striped table-bordered table-sm' id='tableRooms'  cellspacing='0' width='100%'>
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
                                            //echo "<button type='button' class='btn btn-default btn-sm' id='createRoombookPDF'><span class='glyphicon glyphicon-open-file'></span> Raumbuch-PDF</button>";
                                            //echo "<button type='button' class='btn btn-default btn-sm' id='createBauangabenPDF'><span class='glyphicon glyphicon-open-file'></span> Bauangaben-PDF</button>";			
                                            $mysqli->close();
                                            ?>	
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    var table;
                                    init_dt();
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
                                        {text: "ENT-Gesamt-PDF", link: "pdf_createBericht_ENT_PDF_2"},
                                        {text: "Nutzer Formular", link: "pdf_createUserFormPDF"}
                                    ];

                                    function send2backend() {
                                        $.ajax({
                                            url: 'backend.php',
                                            type: 'post',
                                            data: {
                                                bool1: true,
                                                bool2: false
                                            },
                                            success: function (response) {
                                                // handle response
                                            }
                                        });

                                    }

                                    $(document).ready(function () {
                                        move_dt_search('#HeaderTabelleCard');
                                        add_MT_rel_filter('#HeaderTabelleCard');

                                        init_btns('#HeaderTabelleCard');
                                        addCheckbox('#HeaderTabelleCard');
                                        init_btns_old('#HeaderTabelleCard');
                                        add_btn_vis_checkbox_functionality();
                                    });

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
//                                        move.addClass("fix_size");
                                        $(location).append(move);
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
                                            "scrollY": '20vh',
                                            "scrollCollapse": true,
                                            dom: 'rtif',
                                            select: {
                                                style: 'multi'
                                            },
                                            language: {
                                                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                                                search: "",
                                                searchBuilder: {
                                                    label: "Search",
                                                    depthLimit: 2
                                                }
                                            }
                                        });
                                    }

                                    function init_btns_old(location) {
                                        let spacer = {extend: 'spacer', style: 'bar'};
                                        new $.fn.dataTable.Buttons(table, {
                                            buttons: [
                                                btns.map(btn => ({
                                                        text: btn.text,
                                                        className: "btn-sm " + btn.link,
                                                        action: function () {
                                                            var count = table.rows({selected: true}).data();
                                                            var roomIDs = [];
                                                            for (var i = 0; i < count.length; i++) {
                                                                roomIDs.push(count[i][0]);
                                                            }
                                                            if (roomIDs.length === 0) {
                                                                alert("Kein Raum ausgewählt!");
                                                            } else {
                                                                send2backend();
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
                                                spacer,
                                                {
                                                    text: 'Select All',
                                                    action: function () {
                                                        table.rows().select();
                                                    }
                                                },
                                                {
                                                    text: 'Select None',
                                                    action: function () {
                                                        table.rows().deselect();
                                                    }
                                                },
                                                spacer,
                                                {
                                                    text: "NEU!",
                                                    className: "btn-sm ",
                                                    action: function () {
                                                        var count = table.rows({selected: true}).data();
                                                        var roomIDs = [];
                                                        for (var i = 0; i < count.length; i++) {
                                                            roomIDs.push(count[i][0]);
                                                        }
                                                        if (roomIDs.length === 0) {
                                                            alert("Kein Raum ausgewählt!");
                                                        } else {
                                                            send2backend();
                                                            window.open('/pdf_createBericht_NEW.php?roomID=' + roomIDs);
                                                        }
                                                    }
                                                }
                                            ]}).container().appendTo($(location));
                                    }

                                    function addCheckbox(location) {
                                        var checkbox = document.createElement('input');
                                        checkbox.type = 'checkbox';
                                        checkbox.id = 'btnVisibilityCBX';
                                        checkbox.checked = false;

                                        var label = document.createElement('label');
                                        label.htmlFor = 'btnVisibility';
                                        label.class = "rotated";
                                        label.appendChild(document.createTextNode('OLD-PDFs'));
                                        document.querySelector(location).appendChild(checkbox);
                                        document.querySelector(location).appendChild(label);
                                    }

                                    function add_btn_vis_checkbox_functionality() {
                                        btns.forEach(btn => {
                                            document.querySelector('.' + btn.link).style.display = 'none';
                                        });
                                        console.log("BTNS hidden");
                                        document.getElementById("btnVisibilityCBX").addEventListener('change', function () {
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
