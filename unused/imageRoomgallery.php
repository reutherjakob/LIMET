<?php

// 10-2025 FX - unused
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="">
<head>
    <title>RB-Projekte</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>

    <style>

        .btn-sm {
            height: 22px;
            padding: 2px 5px;
            font-size: 12px;
            line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
            border-radius: 3px;
        }

    </style>

</head>
<body>


<div class="container-fluid">
    <div class='mt-4 row'>
        <div class='col-xxl-12'>
            <div class="mt-4 card">
                <div class="card-header"><b>Räume im Projekt</b>
                </div>
                <div class="card-body">
                    <?php

                    $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Nutzfläche, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, 
                        tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_räume.idTABELLE_Räume, tabelle_räume.`MT-relevant`
                                        FROM tabelle_räume INNER JOIN view_Projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = view_Projekte.idTABELLE_Projekte
                                        WHERE (((view_Projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";
                    $result = $mysqli->query($sql);
                    echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableRooms'   >
                        <thead><tr>
                        <th>ID</th>
                        <th>Raumnr</th>
                        <th>Raumbezeichnung</th>
                        <th>Nutzfläche</th>
                        <th>Raumbereich Nutzer</th>
                        <th>MT-relevant</th>
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
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-xxl-6'>
            <div class='mt-4 card'>
                <div class="card-header"><b>Fotos zu Raum</b></div>
                <div class="card-body" id="roomImages">

                </div>
            </div>
        </div>
        <div class='col-xxl-6'>
            <div class='mt-4 card'>
                <div class="card-header"><b>Fotos im Projekt</b></div>
                <div class="card-body" id="projectImages">
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    var table;

    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableRooms') {
                return true;
            }
            if ($("#filter_MTrelevant").val() === '1') {
                if (data [5] === "Ja") {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($("#filter_MTrelevant").val() === '0') {
                    if (data [5] === "Nein") {
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
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
            "scrollCollapse": true,
            select: {
                style: 'single'
            }
        });

        $('#tableRooms tbody').on('click', 'tr', function () {
            var raumID = table.row($(this)).data()[0];

            $.ajax({
                url: "getImagesToRoom.php",
                data: {"roomID": raumID},
                type: "GET",
                success: function (data) {
                    $("#roomImages").html(data);
                    $.ajax({
                        url: "getImagesNotInRoom.php",
                        data: {"roomID": raumID},
                        type: "GET",
                        success: function (data) {
                            $("#projectImages").html(data);
                        }
                    });
                }
            });
        });
    });
</script>
</html> 
