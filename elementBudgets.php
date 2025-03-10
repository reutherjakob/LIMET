<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
include "_format.php";
init_page_serversides();
$mysqli = utils_connect_sql();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Budgets</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">


    <!-- Rework 2025 CDNs -->
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


</head>

<body>
<div id="limet-navbar"></div>
<div class="container-fluid">

    <div class="mt-4 card">
        <div class="card-header">

            <div class="row">
                <div class="col-8"><b>Elemente</b></div>
                <div class="col-4 d-flex flex-nowrap align-items-center justify-content-end" id="cardHeader"></div>
            </div>
        </div>
        <div class="card-body" id="elementBudgets">
            <?php

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

            $sql = "SELECT tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante,
       tabelle_räume.`Raumbereich Nutzer` AS Ausdr1, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand` AS Ausdr2,
       tabelle_projekt_varianten_kosten.Kosten, tabelle_projekt_varianten_kosten.Kosten*tabelle_räume_has_tabelle_elemente.Anzahl AS PP, tabelle_projektbudgets.Budgetnummer,
       tabelle_räume_has_tabelle_elemente.id, tabelle_projektbudgets.idtabelle_projektbudgets
                                                        FROM tabelle_projektbudgets RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_varianten INNER JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON tabelle_projektbudgets.idtabelle_projektbudgets = tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets
                                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                        ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
            $result = $mysqli->query($sql);

            echo "<table class='table table-striped table-bordered table-hover border border-light border-5 table-sm' id='tableElementsInProjectForBudget'   >
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
                echo "<td>" . $row["Ausdr1"] . "</td>";
                echo "<td>" . $row["Raumnr"] . "-" . $row["Raumbezeichnung"] . "</td>";
                if ($row["Ausdr2"] == 1) {
                    echo "<td>Nein</td>";
                } else {
                    echo "<td>Ja</td>";
                }

                echo "<td>" . format_money($row["Kosten"]) . "</td>";
                echo "<td>" . format_money($row["PP"]) . "</td>";
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
    var table;

    $(document).ready(function () {
        table = new DataTable('#tableElementsInProjectForBudget', {
            paging: true,
            select: true,
            order: [[3, "asc"]],
            columnDefs: [
                {
                    targets: [0, 1],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [11],
                    type: 'string',
                    render: (data, type, row, meta) => {
                        if (type === 'filter' || type === 'sort') {
                            const cell = table.cell({row: meta.row, column: meta.col}).node();
                            data = $('select, input[type="text"]', cell).val();
                        }
                        return data;
                    }
                }
            ],
            orderCellsTop: true,
            pagingType: "full_numbers",
            lengthChange: true,
            pageLength: 25,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                decimal: ",",
                thousands: ".",
                search: "",
                searchPlaceholder: "Suche...",
                searchBuilder: {
                    button: {
                        0: '<i class="fas fa-search"></i> Suche erstellen',
                        1: '<i class="fas fa-search"></i> Suche anpassen',
                        _: '<i class="fas fa-search"></i> Suche (%d)'
                    },
                    title: {
                        0: 'Erweiterte Suche',
                        _: 'Erweiterte Suche (%d)'
                    },
                    clearAll: 'Alle löschen',
                    add: 'Bedingung hinzufügen',
                    condition: 'Bedingung',
                    data: 'Spalte',
                    deleteTitle: 'Löschen',
                    leftTitle: 'Nach links',
                    logicAnd: 'Und',
                    logicOr: 'Oder',
                    rightTitle: 'Nach rechts',
                    value: 'Wert'
                }
            },
            mark: true,
            layout: {
                bottomStart: 'info',
                bottomEnd: ["pageLength", "paging"],
                topStart: null,
                topEnd: ["search", 'buttons']
            },
            searchBuilder: {
                columns: [2, 3, 4, 5, 6, 7, 8, 9, 10],
                preDefined: {
                    criteria: [
                        {
                            condition: '=',
                            data: 'Elementbezeichnung',
                            value: ['']
                        }
                    ]
                },
                depthLimit: 2,
                greyscale: true,
                logic: 'AND',
                conditions: {
                    string: {
                        'custom': {
                            conditionName: 'Benutzerdefiniert',
                            init: function (that, fn, preDefined) {
                                // Custom condition initialization
                            },
                            inputValue: function (el) {
                                // Custom input handling
                            },
                            isInputValid: function (el, that) {
                                // Custom input validation
                            },
                            search: function (value, comparison) {
                                // Custom search logic
                            }
                        }
                    }
                }
            },
            buttons: [
                {
                    extend: 'searchBuilder',
                    config: {
                        columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                },
                "excel", "pdf"
            ],
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-success").appendTo('#cardHeader');
                $('.dt-buttons').children().addClass("btn-sm ms-1 me-1");
            }
        });

        $('#tableElementsInProjectForBudget').on('change', 'tbody select, tbody input[type="text"]', function () {
            let roombookID = this.id;
            let budgetID = this.value;
            $.ajax({
                url: "saveRoombookBudget.php",
                data: {"roombookID": roombookID, "budgetID": budgetID},
                type: "GET",
                success: function (data) {
                    alert(data);
                }
            });
            table.cell($(this).closest('td')).invalidate();
            table.draw(false);
        });
    });
</script>
</body>
</html>
