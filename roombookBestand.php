<?php
require_once 'utils/_utils.php';
init_page_serversides();
include "utils/_format.php";
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Bestand</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

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
<div class="container-fluid bg-light" >
    <div id="limet-navbar"></div>
    <div class="mt-4 card">
        <div class="card-header" >
            <div class="row">
                <div class="col-8"><b>Elemente im Bestand</b></div>
                <div class="col-4 d-flex flex-nowrap align-items-center justify-content-end" id="TableCardHeader"></div>
            </div>
        </div>
        <div class="card-body">
            <?php
            $mysqli = utils_connect_sql();
            $stmt = "SELECT 
                                Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, 
                                tabelle_elemente.ElementID, 
                                tabelle_elemente.Bezeichnung, 
                                tabelle_räume.`Raumbereich Nutzer`, 
                                tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
                                tabelle_projekt_varianten_kosten.Kosten, 
                                tabelle_varianten.Variante
                            FROM 
                                tabelle_varianten 
                            INNER JOIN 
                                ((tabelle_elemente 
                                    INNER JOIN 
                                        (tabelle_räume 
                                        INNER JOIN tabelle_räume_has_tabelle_elemente 
                                        ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
                                    ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                                INNER JOIN tabelle_projekt_varianten_kosten 
                                ON 
                                    (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) 
                                    AND 
                                    (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)) 
                            ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                            WHERE 
                                ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION["projectID"] . ") 
                                    AND (tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0) 
                                    AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =" . $_SESSION["projectID"] . " )
                                )
                            GROUP BY 
                                tabelle_elemente.ElementID, 
                                tabelle_elemente.Bezeichnung, 
                                tabelle_räume.`Raumbereich Nutzer`, 
                                tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
                                tabelle_projekt_varianten_kosten.Kosten, 
                                tabelle_varianten.Variante
                            ORDER BY 
                                tabelle_elemente.ElementID;";

            $result = $mysqli->query($stmt);

            echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableBestandsElemente'>
                        <thead><tr>
                        <th>ID</th>
                        <th>Stk</th>
                        <th>ID</th>
                        <th>Element</th>
                        <th>Raumbereich</th>
                        <th>Variante</th>
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
                echo "<td>" . $row["Variante"] . "</td>";
                echo "<td>" . format_money($row["Kosten"]) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            ?>

        </div>
    </div>
    <div class="mt-4 card">
        <div class="card-header" id="CH2"
        ">Bestandsdaten für ausgewähltes Element/Raumbereich
    </div>
    <div class="card-body overflow-scroll" id="bestandsRoombook"></div>
</div>

</body>
<script>
    var table1;
    $(document).ready(function () {
        table1 = new DataTable('#tableBestandsElemente', {
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            select: true,
            paging: true,
            searching: true,
            info: true,
            order: [[1, "asc"]],
            pagingType: "full_numbers",
            lengthChange: false,
            pageLength: 10,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                decimal: ",",
                thousands: ".",
                search: "",
                searchBuilder: {
                    title: null
                }
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomEnd: 'paging',
                bottomStart: ['search', 'info']
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#TableCardHeader');
            }
        });

        $('#tableBestandsElemente tbody').on('click', 'tr', function () {
                let elementID = table1.row($(this)).data()[0];
                let raumbereich = table1.row($(this)).data()[4];
                $.ajax({
                    url: "getBestandWithRaumbereich.php",
                    data: {"elementID": elementID, "raumbereich": raumbereich},
                    type: "GET",
                    success: function (data) {
                        $("#bestandsRoombook").html(data);
                    }
                });

        });
    });
</script>
</html>
