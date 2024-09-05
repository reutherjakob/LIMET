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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"/></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"/></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"/></script>



        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"/></script>

        <!--
       <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.css"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
       <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.js"/></script>
        -->
    </head>

    <body style="height:100%">

        <div class="container-fluid" >
            <div id="limet-navbar"></div> 
            <div class="mt-4 card">
                <div class="card-header">Räume im Projekt</div>
                <div class="card-body">
                    <?php
                    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

                    /* change character set to utf8 */
                    if (!$mysqli->set_charset("utf8")) {
                        printf("Error loading character set utf8: %s\n", $mysqli->error);
                        exit();
                    }

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
						</tr>
                                                <tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
                                                <th><select id='filter_MTrelevant'>
                                                    <option value='2'></option>
                                                    <option value='1'>Ja</option>
                                                    <option value='0'>Nein</option>
                                                </select></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
						</tr>
                                                </thead><tbody>";

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

            $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {
                        if (settings.nTable.id !== 'tableRooms') {
                            return true;
                        }


                        if ($("#filter_MTrelevant").val() === '1') {
                            if (data [5] === "Ja")
                            {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            if ($("#filter_MTrelevant").val() === '0') {
                                if (data [5] === "Nein")
                                {
                                    return true;
                                } else {
                                    return false;
                                }
                            } else {
                                return true;
                            }
                        }
                    }
            );

            $('#filter_MTrelevant').change(function () {
                table.draw();
            });



            // Tabellen formatieren
            $(document).ready(function () {

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
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    "scrollY": '20vh',
                    "scrollCollapse": true,
                    dom: 'Bfrtip',
                    select: {
                        style: 'multi'
                    },
                    buttons: [
                        {
                            text: 'Alle auswählen',
                            action: function () {
                                table.rows().select();
                            }
                        },
                        {
                            text: 'Keinen auswählen',
                            action: function () {
                                table.rows().deselect();
                            }
                        },
                        {
                            text: 'Raumbuch-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createRoombookPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'Raumbuch-ohne Bestand-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createRoombookWithoutBestandPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'Raumbuch-0-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createRoombookWithout0PDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'Raumbuch-0-ohne Bestand-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createRoombookWithout0WothoutBestandPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'Raumbuch-inkl Bauangaben-0-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createRoombookWithBauangabenWithout0PDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'Bauangaben-PDF V1',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBauangabenPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'Bauangaben-PDF V2',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBauangabenV2PDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'Bauangaben ohne Elemente-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBauangabenWithoutElementsPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        }
                        ,
                        {
                            text: 'Bauangaben Lab-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBauangabenLabPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        }
                        ,
                        {
                            text: 'Bauangaben Lab-Kurz-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBauangabenLabKompaktPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        }
                        ,
                        {
                            text: 'Bauangaben Lab-ENT-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBauangabenLabEntPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        }
                        ,
                        {
                            text: 'Bauangaben Lab-EIN-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBauangabenLabEinrPDF_1.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        }
                        ,
                        {
                            text: 'BO-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBOPDF.php?roomID=' + roomIDs);
                                }
                            }
                        },
                        {
                            text: 'BauangabenDetail-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBauangabenDetailPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'VE-Gesamt-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBericht_VE_PDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'ENT-Gesamt-PDF',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createBericht_ENT_PDF_2.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        },
                        {
                            text: 'Nutzer-Formular',
                            action: function () {
                                var count = table.rows({selected: true}).data();
                                //RaumIDs zur Auswahl der Berichte
                                var roomIDs = [];
                                for (var i = 0; i < count.length; i++) {
                                    roomIDs.push(count[i][0]);
                                }
                                if (roomIDs.length === 0) {
                                    alert("Kein Raum ausgewählt!");
                                } else {
                                    window.open('/pdf_createUserFormPDF.php?roomID=' + roomIDs);//there are many ways to do this
                                }
                            }
                        }
                    ]
                });


                // CLICK TABELLE RÄUME
                //var table = $('#tableRooms').DataTable(); 
                /*$('#tableRooms tbody').on( 'click', 'tr', function () {
                 if ( $(this).hasClass('info') ) {
                 $(this).removeClass('info');	            
                 for(var i = roomIDs.length - 1; i >= 0; i--) {
                 if(roomIDs[i] === table.row( $(this) ).data()[0]) {
                 roomIDs.splice(i, 1);
                 }
                 }	            
                 }
                 else {
                 $(this).addClass('info');
                 roomIDs.push(table.row( $(this) ).data()[0]);	            
                 }
                 } );
                 */
            });

            $('#createRoombookPDF').click(function () {
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    window.open('/pdf_createRoombookPDF.php?roomID=' + roomIDs);//there are many ways to do this
                }

            });

            $('#createBauangabenPDF').click(function () {
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    window.open('/pdf_createBauangabenPDF.php?roomID=' + roomIDs);//there are many ways to do this
                }
            });

        </script>

    </body>

</html>
