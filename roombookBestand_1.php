<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>RB-Bestand</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png"/>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

        <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
        <style>
            .dt-input{
                float:right;
            }
        </style>

    </head>
    <body style="height:100%">
        <div class="container-fluid" >
            <div id="limet-navbar"></div> 
            <div class="mt-4 card">
                <div class="card-header" id="TableCardHeader">Elemente im Bestand</div>
                <div class="card-body">
                    <?php
                    $mysqli = utils_connect_sql();
                    $sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, 
               tabelle_elemente.ElementID, 
               tabelle_elemente.Bezeichnung, 
               tabelle_räume.`Raumbereich Nutzer`, 
               tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
               tabelle_projekt_varianten_kosten.Kosten
        FROM (tabelle_elemente 
              INNER JOIN (tabelle_räume 
                          INNER JOIN tabelle_räume_has_tabelle_elemente 
                          ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
              ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
        INNER JOIN tabelle_projekt_varianten_kosten 
        ON (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) 
        AND (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . ") 
        AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`) = 0) 
        AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . "))
        GROUP BY tabelle_elemente.ElementID, 
                 tabelle_elemente.Bezeichnung, 
                 tabelle_räume.`Raumbereich Nutzer`, 
                 tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
                 tabelle_projekt_varianten_kosten.Kosten
        ORDER BY tabelle_elemente.ElementID;";

                    $result = $mysqli->query($sql);

                    echo "<table class='table table-striped table-bordered table-sm' id='tableBestandsElemente' cellspacing='0' width='100%'>
                        <thead><tr>
                        <th>ID</th>
                        <th>Stk</th>
                        <th>ID</th>
                        <th>Element</th>
                        <th>Raumbereich</th>
                        <th>Kosten</th>
                        </tr></thead>
                        <tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
                        echo "<td>" . $row["SummevonAnzahl"] . "</td>";
                        echo "<td>" . $row["ElementID"] . "</td>";
                        echo "<td>" . $row["Bezeichnung"] . "</td>";
                        echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                        echo "<td>" . $row["Kosten"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    ?>

                </div>
            </div>
            <div class="mt-4 card">
                <div class="card-header" id="CH2"">Bestandsdaten für ausgewähltes Element/Raumbereich</div>
                <div class="card-body" id="bestandsRoombook"></div>
            </div>

        </div>
    </body>
    <script>
        function move_dt_search(id, where2) {
            var dt_searcher = document.getElementById(id);
            dt_searcher.parentNode.removeChild(dt_searcher);
            document.getElementById(where2).appendChild(dt_searcher);
        }

        // Tabelle formatieren
        $(document).ready(function () {
            $('#tableBestandsElemente').DataTable({
                "columnDefs": [
                    {
                        "targets": [0],
                        "visible": false,
                        "searchable": false
                    }
                ],
                "select": true,
                "paging": true,
                "searching": true,
                "info": true,
                "order": [[1, "asc"]],
                "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                    "search": "",
                    "searchBuilder": {
                        "title": null}
                }

            });


            // CLICK TABELLE 
            var table1 = $('#tableBestandsElemente').DataTable();

            $('#tableBestandsElemente tbody').on('click', 'tr', function () {

                if ($(this).hasClass('info')) {
                    //$(this).removeClass('info');
                } else {
                    table1.$('tr.info').removeClass('info');
                    $(this).addClass('info');

                    var elementID = table1.row($(this)).data()[0];
                    var raumbereich = table1.row($(this)).data()[4];

                    $.ajax({
                        url: "getBestandWithRaumbereich.php",
                        data: {"elementID": elementID, "raumbereich": raumbereich},
                        type: "GET",
                        success: function (data) {
                            $("#bestandsRoombook").html(data);

                            //move_dt_search("dt-search-1", "CH2");
                            
                        }
                    });

                }
            });

            setTimeout(function () {
                move_dt_search("dt-search-0", "TableCardHeader");
            }, 100);



        });
    </script>


</html>
