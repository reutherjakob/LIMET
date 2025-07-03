<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Meeting</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">

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
<!-- 13.2.25: Reworked -->
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides();
?>
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="card">
    <div class="card">
        <div class="card-header">
            <div class="row ">
                <div class="col-6"></div>
                <div class="col-6 d-flex justify-content-end align-items-center" id="CardHeaderRooms">
                    <select id="mtRelevantFilter" class="form-select form-select-sm mx-2" style="width:auto; display:inline-block;">
                        <option value="">MT.-rel</option>
                        <option value="1">Ja</option>
                        <option value="0">Nein</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Nutzfläche, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss,
                            tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_räume.idTABELLE_Räume,
                            tabelle_räume.`MT-relevant`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.Entfallen
                                            FROM tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
                                            WHERE tabelle_projekte.idTABELLE_Projekte=" . $_SESSION["projectID"] . " AND tabelle_räume.Entfallen <>1
                                            ORDER BY tabelle_räume.`MT-relevant` DESC";
            $result = $mysqli->query($sql);
            echo "<table class='table table-striped table-bordered table-sm ' id='tableRooms'   >
                            <thead class='thead'><tr>
                            <th>ID</th>
                            <th>Raumnr</th>
                            <th>Raumbezeichnung</th>
                            <th>Nutzfläche</th>
                            <th>Raumbereich Nutzer</th>
                                <th>Bauabschnitt</th>
                                <th>Bauetappe</th>
                                <th>Geschoss</th>
                                <th>MT-rel.</th>
                            </tr></thead><tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["idTABELLE_Räume"] . "</td>";
                echo "<td>" . $row["Raumnr"] . "</td>";
                echo "<td>" . $row["Raumbezeichnung"] . "</td>";
                echo "<td>" . $row["Nutzfläche"] . "</td>";
                echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                echo "<td>" . $row["Bauabschnitt"] . "</td>";
                echo "<td>" . $row["Bauetappe"] . "</td>";
                echo "<td>" . $row["Geschoss"] . "</td>";
                echo "<td>" . $row["MT-relevant"] . "</td>";

                echo "</tr>";
            }
            echo "</tbody></table>";
            ?>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xxl-1 col-sm-1">
            <div class="card bg-dark text-center">
                <div class="card-body" id="roomInfo" data-bs-toggle="tooltip" data-bs-placement="right"
                     title="Rauminfo">
                    <i class="fas fa-home fa-3x text-light"></i>
                </div>
            </div>
            <div class="card bg-info text-center mt-4">
                <div class="card-body" id="roombookBO" data-bs-toggle="tooltip" data-bs-placement="right"
                     title="Betriebsorganisation">
                    <i class="fas fa-user-md fa-3x text-light"></i>
                </div>
            </div>
            <div class="card bg-success text-center mt-4">
                <div class="card-body" id="roombook" data-bs-toggle="tooltip" data-bs-placement="right"
                     title="Rauminhalt">
                    <i class="fas fa-list fa-3x text-light"></i>
                </div>
            </div>
        </div>
        <div class="col-xxl-11 col-sm-11 ">
            <div class="card">
                <div class="card-header" id="informationHeader"></div>
                <div class="card-body" id="informationOverview"></div>
            </div>
        </div>
    </div>
</div>
</div>
</body>
<!--suppress ES6ConvertVarToLetConst -->
<script>
    var moduleSelected = 1;
    $(document).ready(function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: {"show": 10, "hide": 10} // milliseconds
            });
        });
        var table = $('#tableRooms').DataTable({
            layout: {
                topEnd: ['buttons', 'search'],
                topStart: null,
                bottomEnd: 'paging',
                bottomStart: 'info'
            },
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            select: true,
            searching: true,
            lengthChange: false,
            info: true,
            order: [[1, "asc"]],
            paging: {
                type: 'simple',
                numbers: 10
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            buttons: ["searchBuilder", "colvis"],
            initComplete: function () {
                $('.dt-search label').remove();
                $('#tableRooms_wrapper .dt-search').children().removeClass("form-control form-control-sm").addClass("btn").appendTo("#CardHeaderRooms");
                $('#tableRooms_wrapper .dt-buttons').appendTo("#CardHeaderRooms") ;
                $('#CardHeaderRooms .btn').attr('class', 'btn btn-sm btn-outline-dark');

            }
        });
// After DataTable initialization
        $('#mtRelevantFilter').on('change', function () {
            var val = $(this).val();
            if (val) {
                // Filter for exact match in the MT-relevant column
                table.column(8).search('^' + val + '$', true, false).draw();
            } else {
                // Clear filter
                table.column(8).search('').draw();
            }
        });


        $('#tableRooms tbody').on('click', 'tr', function () {
            var id = table.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"roomID": id},
                type: "GET",
                success: function () {
                    var url = "roombookMeetingRoombook.php";
                    var anzeige = "<H4><i class='fas fa-list'></i> Rauminhalt</H4>";
                    var anzeigeColor = "#5cb85c";
                    if (moduleSelected === 0) {
                        url = "roombookMeetingRoomInfo.php";
                        anzeige = "<H4><i class='fas fa-home'></i> Rauminfo</H4>";
                        anzeigeColor = "#343a40";
                    }
                    if (moduleSelected === 2) {
                        url = "roombookMeetingBO.php";
                        anzeige = "<H4><i class='fas fa-user-md'></i> Betriebsorganisation</H4>";
                        anzeigeColor = "#5bc0de";
                    }
                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function (data) {
                            $("#informationOverview").html(data);
                            document.getElementById("informationHeader").style.backgroundColor = anzeigeColor;
                            document.getElementById("informationHeader").style.color = "#f9f9f9";
                            document.getElementById("informationHeader").innerHTML = anzeige;
                        }

                    });
                }
            });
        });

    });

    //Rauminfo Button CLICK-----------------
    $("#roomInfo").click(function () {
        $.ajax({
            url: "roombookMeetingRoomInfo.php",
            type: "GET",
            success: function (data) {
                $("#informationOverview").html(data);
                document.getElementById("informationHeader").style.backgroundColor = "#343a40";
                document.getElementById("informationHeader").style.color = "#f9f9f9";
                document.getElementById("informationHeader").innerHTML = "<H4><i class='fas fa-home'></i> Rauminfo</H4>";
                moduleSelected = 0;
            }

        });
    });
    //-------------------------------------
    //RauminhaltButton CLICK-----------------
    $("#roombook").click(function () {
        $.ajax({
            url: "roombookMeetingRoombook.php",
            type: "GET",
            success: function (data) {
                $("#informationOverview").html(data);
                document.getElementById("informationHeader").style.backgroundColor = "#5cb85c";
                document.getElementById("informationHeader").style.color = "#f9f9f9";
                document.getElementById("informationHeader").innerHTML = "<H4><i class='fas fa-list'></i> Rauminhalt</H4>";
                moduleSelected = 1;
            }

        });
    });
    //-------------------------------------

    //Betriebsorganisation CLICK-----------------
    $("#roombookBO").click(function () {
        $.ajax({
            url: "roombookMeetingBO.php",
            type: "GET",
            success: function (data) {
                $("#informationOverview").html(data);
                document.getElementById("informationHeader").style.backgroundColor = "#5bc0de";
                document.getElementById("informationHeader").style.color = "#f9f9f9";
                document.getElementById("informationHeader").innerHTML = "<H4><i class='fas fa-user-md'></i> Betriebsorganisation</H4>";
                moduleSelected = 2;
            }

        });
    });
    //-------------------------------------
</script>
</html>
