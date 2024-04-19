<?php
session_start();
include '_utils.php';
check_login();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>RB-Budgets</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
            <link rel="icon" href="iphone_favicon.png">

                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
                        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

                        <!-- 
                        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
                        <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
                        -->


                        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
                        <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
                        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

                        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
                        <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
                        <script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>

                        <!--DATEPICKER -->
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
                            <script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>


                            </head>

                            <body style="height:100%">

                                <div class="container-fluid" >
                                    <div id="limet-navbar"></div> 
                                    <div class="mt-4 card">
                                        <div class="card-header"><b>Elemente</b></div>
                                        <div class="card-body" id="elementBudgets">
                                            <?php
                                            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

                                            /* change character set to utf8 */
                                            if (!$mysqli->set_charset("utf8")) {
                                                printf("Error loading character set utf8: %s\n", $mysqli->error);
                                                exit();
                                            }

                                            // Array mit Projektbudgets befüllen
                                            $sql = "SELECT tabelle_projektbudgets.idtabelle_projektbudgets, tabelle_projektbudgets.Budgetnummer, tabelle_projektbudgets.Budgetname
                                                        FROM tabelle_projektbudgets
                                                        WHERE (((tabelle_projektbudgets.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                        ORDER BY tabelle_projektbudgets.Budgetnummer;";
                                            $result = $mysqli->query($sql);
                                            $projectBudgets = array();
                                            while ($row = $result->fetch_assoc()) {
                                                $projectBudgets[$row['idtabelle_projektbudgets']]['idtabelle_projektbudgets'] = $row['idtabelle_projektbudgets'];
                                                $projectBudgets[$row['idtabelle_projektbudgets']]['Budgetnummer'] = $row['Budgetnummer'];
                                                $projectBudgets[$row['idtabelle_projektbudgets']]['Budgetname'] = $row['Budgetname'];
                                            }

                                            $sql = "SELECT tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer` AS Ausdr1, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand` AS Ausdr2, tabelle_projekt_varianten_kosten.Kosten, tabelle_projekt_varianten_kosten.Kosten*tabelle_räume_has_tabelle_elemente.Anzahl AS PP, tabelle_projektbudgets.Budgetnummer, tabelle_räume_has_tabelle_elemente.id, tabelle_projektbudgets.idtabelle_projektbudgets
                                                        FROM tabelle_projektbudgets RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_varianten INNER JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON tabelle_projektbudgets.idtabelle_projektbudgets = tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets
                                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                        ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
                                            $result = $mysqli->query($sql);

                                            echo "<table class='table table-striped table-bordered table-sm' id='tableElementsInProjectForBudget'  cellspacing='0' width='100%'>
                                                        <thead><tr>
                                                                <th>id</th>										
                                                                <th>idBudget</th>										
                                                                <th>Anzahl</th>
                                                                <th>ID</th>
                                                                <th>Element</th>
                                                                <th>Variante</th>
                                                                <th>Raumbereich</th>
                                                                <th>Raum</th>
                                                                <th>Bestand</th>                                                                              									
                                                                <th>EP</th>
                                                                <th>PP</th>										
                                                                <th>Budget</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>";

                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row["id"] . "</td>";
                                                echo "<td>" . $row["idtabelle_projektbudgets"] . "</td>";
                                                echo "<td>" . $row["Anzahl"] . "</td>";
                                                echo "<td>" . $row["ElementID"] . "</td>";
                                                echo "<td>" . $row["Bezeichnung"] . "</td>";
                                                echo "<td>" . $row["Variante"] . "</td>";
                                                echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                                                echo "<td>" . $row["Raumnr"] . "-" . $row["Raumbezeichnung"] . "</td>";
                                                if ($row["Neu/Bestand"] == 1) {
                                                    echo"<td>Nein</td>";
                                                } else {
                                                    echo"<td>Ja</td>";
                                                }

                                                echo "<td>" . money_format("%i", $row["Kosten"]) . "</td>";
                                                echo "<td>" . money_format("%i", $row["PP"]) . "</td>";
                                                echo "<td>";
                                                echo "<select class='form-control form-control-sm' id='" . $row["id"] . "'>";
                                                if ($row["idtabelle_projektbudgets"] != "") {
                                                    echo "<option value=0>0-Budget wählen</option>";
                                                    foreach ($projectBudgets as $array) {
                                                        if ($array['idtabelle_projektbudgets'] == $row["idtabelle_projektbudgets"]) {
                                                            echo "<option selected value=" . $array['idtabelle_projektbudgets'] . ">" . $array['idtabelle_projektbudgets'] . "-" . $array['Budgetnummer'] . "-" . $array['Budgetname'] . "</option>";
                                                        } else {
                                                            echo "<option value=" . $array['idtabelle_projektbudgets'] . ">" . $array['idtabelle_projektbudgets'] . "-" . $array['Budgetnummer'] . "-" . $array['Budgetname'] . "</option>";
                                                        }
                                                    }
                                                } else {
                                                    echo "<option value=0 selected>0-Budget wählen</option>";
                                                    foreach ($projectBudgets as $array) {
                                                        echo "<option value=" . $array['idtabelle_projektbudgets'] . ">" . $array['idtabelle_projektbudgets'] . "-" . $array['Budgetnummer'] . "-" . $array['Budgetname'] . "</option>";
                                                    }
                                                }
                                                echo "</select></td>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                            echo "</tbody></table>";
                                            $mysqli->close();
                                            ?>	
                                        </div>
                                    </div>	
                                </div>

                                <script>


                                    $(document).ready(function () {


                                        $('#tableElementsInProjectForBudget').DataTable({
                                            "paging": true,
                                            "select": true,
                                            "searchable": false,
                                            "order": [[3, "asc"]],
                                            "columnDefs": [
                                                {
                                                    "targets": [0, 1],
                                                    "visible": false,
                                                    "searchable": false
                                                },
                                                {
                                                    targets: [11],
                                                    type: 'string',
                                                    render: function (data, type, full, meta) {
                                                        if (type === 'filter' || type === 'sort') {
                                                            var api = new $.fn.dataTable.Api(meta.settings);
                                                            var td = api.cell({row: meta.row, column: meta.col}).node();
                                                            data = $('select, input[type="text"]', td).val();
                                                        }
                                                        return data;
                                                    }
                                                }
                                            ],
                                            "orderCellsTop": true,
                                            "pagingType": "simple",
                                            "lengthChange": false,
                                            "pageLength": 10,
                                            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                                            "mark": true
                                        });

                                        $('#tableElementsInProjectForBudget').on('change', 'tbody select, tbody input[type="text"]', function () {
                                            var roombookID = this.id;
                                            var budgetID = this.value;
                                            $.ajax({
                                                url: "saveRoombookBudget.php",
                                                data: {"roombookID": roombookID, "budgetID": budgetID},
                                                type: "GET",
                                                success: function (data) {
                                                    alert(data);
                                                }
                                            });
                                            table.cell($(this).closest('td')).invalidate();

                                            // Redraw table (optional)
                                            table.draw(false);
                                        });

                                    });


                                </script>


                            </body>

                            </html>
