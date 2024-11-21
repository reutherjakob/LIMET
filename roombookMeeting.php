<?php
include '_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Meeting</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
</head>
<body>

<div class="container-fluid">
    <div id="limet-navbar"></div> <!-- Container für Navbar -->

    <div class="mt-4 card">
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

            echo "<table class='table table-striped table-bordered table-sm' id='tableRooms'  cellspacing='0' width='100%'>
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
        <div class="col-md-1">
            <div class="card bg-dark text-center">
                <div class="card-body">
                    <div class="card-title">
                        <button type='button' class='btn text-light' style='background-color:transparent' id="roomInfo">
                            <h1><i class="fas fa-home"></i></h1></button>
                    </div>
                    <p class="card-text text-light">Rauminfo</p>
                </div>
            </div>
            <div class="card bg-info text-center mt-4">
                <div class="card-body">
                    <div class="card-title">
                        <button type='button' class='btn text-light' style='background-color:transparent'
                                id="roombookBO"><h1><i class="fas fa-user-md"></i></h1></button>
                    </div>
                    <p class="card-text text-light">Betriebsorganisation</p>
                </div>
            </div>
            <div class="card bg-success text-center mt-4">
                <div class="card-body">
                    <div class="card-title">
                        <button type='button' class='btn text-light' style='background-color:transparent' id="roombook">
                            <h1><i class="fas fa-list"></i></h1></button>
                    </div>
                    <p class="card-text text-light">Rauminhalt</p>
                </div>
            </div>
        </div>
        <div class="col-md-11">
            <div class="card">
                <div class="card-header" id="informationHeader">
                </div>
                <div class="card-body" id="informationOverview"></div>
            </div>
        </div>
    </div>
</div>

</body>
<script>
    var ext = "<?php echo $_SESSION["ext"] ?>";
    var moduleSelected = 1;
    // Tabelle formatieren
    $(document).ready(function () {
        if (ext === '0') {
            $('#tableRooms').DataTable({
                "columnDefs": [
                    {
                        "targets": [0],
                        "visible": false,
                        "searchable": false
                    }
                ],
                "select": true,
                //"paging": true,
                "searching": true,
                "lengthChange": false,
                "info": true,
                "order": [[1, "asc"]],
                "pagingType": "simple",
                "pageLength": 10,
                //"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
            });
        } else {

        }

        // CLICK TABELLE RÄUME
        var table = $('#tableRooms').DataTable();

        $('#tableRooms tbody').on('click', 'tr', function () {

            var id = table.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"roomID": id},
                type: "GET",
                success: function (data) {
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
