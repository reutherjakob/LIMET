<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Nicht verwendete Elemente</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
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
<?php
require_once 'utils/_utils.php';
init_page_serversides("x"); ?>

<body style="height:100%">
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="mt-2 card">
        <div class="card-header">
            <div class="row">
                <div class="col-6"> Elemente mit NICHT VERWENDEN) in der Kurzbeschreibung</div>
                <div class="col-6 d-flex align-items-center justify-content-end" id="CHNVE"></div>
            </div>
        </div>
        <div class="card-body">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID,
                    tabelle_elemente.Bezeichnung, tabelle_elemente.Kurzbeschreibung
                    FROM tabelle_elemente
                    WHERE Kurzbeschreibung LIKE '%(NICHT VERWENDEN)%'
                    ORDER BY tabelle_elemente.ElementID;";

            $result = $mysqli->query($sql);

            echo "<table class='table compact table-striped table-sm table-hover border border-light border-5' id='tableNichtVerwendeteElemente'>
                    <thead><tr>
                        <th>ID</th>
                        <th>ElementID</th>
                        <th>Element</th>
                        <th>Beschreibung</th>
                    </tr></thead><tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["idTABELLE_Elemente"] . "</td>";
                echo "<td>" . $row["ElementID"] . "</td>";
                echo "<td>" . $row["Bezeichnung"] . "</td>";
                echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            $mysqli->close();
            ?>
        </div>
    </div>
</div>
</body>

<script charset="utf-8" type="text/javascript">
    $(document).ready(function () {
        new DataTable('#tableNichtVerwendeteElemente', {
            paging: true,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            info: true,
            pagingType: 'simple',
            lengthChange: true,
            pageLength: 10,
            order: [[1, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche...",
                info: "_START_ bis _END_ von _TOTAL_",
                infoEmpty: "Keine Einträge vorhanden",
                infoFiltered: "",
                lengthMenu: "_MENU_"
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Nicht verwendete Elemente',
                    className: 'btn btn-secondary btn-sm bg-light text-dark',
                    title: "Nicht verwendete Elemente"
                }
            ],
            layout: {
                topStart: null,// '',
                topEnd: null,
                bottomStart: 'info',
                bottomEnd: ['pageLength', 'paging', 'search', 'buttons']
            },
            initComplete: function () {
                $('#CHNVE').empty();
                $('#tableNichtVerwendeteElemente_wrapper .dt-buttons').appendTo('#CHNVE');
                $('#tableNichtVerwendeteElemente_wrapper .dt-search label').remove();
                $('#tableNichtVerwendeteElemente_wrapper .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#CHNVE');
            }
        });
    });
</script>
</html>