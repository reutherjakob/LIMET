<?php
// 25 FX
include "utils/_utils.php";
init_page_serversides();
$mysqli = utils_connect_sql();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB - Ausführung</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
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
<body style="height:100%">
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class='row'>
        <div class='col-xxl-12'>
            <div class="mt-4 card">
                <div class="card-header  d-inline-flex align-items-center">
                    <div class="col-2"><b>Räume im Projekt</b></div>
                    <div class="col-10 d-flex justify-content-end">
                        <input type="checkbox" class="btn-check" id="filter_MTrelevantRooms">
                        <label class="btn btn-outline-dark" for="filter_MTrelevantRooms"> Nur MT-relevante </label>


                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $mysqli->prepare("SELECT  tabelle_räume.idTABELLE_Räume,
                                                            tabelle_räume.`Raumbereich Nutzer`, 
                                                            tabelle_räume.Raumnr, 
                                                            tabelle_räume.Raumbezeichnung,
                                                            tabelle_räume.Nutzfläche, 
                                                            tabelle_räume.Geschoss,
                                                            tabelle_räume.Bauetappe, 
                                                            tabelle_räume.Bauabschnitt,
                                                            tabelle_bauphasen.bauphase,
                                                            tabelle_bauphasen.datum_fertigstellung,
                                                            tabelle_räume.`MT-relevant`, 
                                                            tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen
                                                        FROM (tabelle_räume INNER JOIN view_Projekte
                                                        ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte =
                                                           view_Projekte.idTABELLE_Projekte) 
                                                        LEFT JOIN tabelle_bauphasen 
                                                            ON tabelle_räume.tabelle_bauphasen_idtabelle_bauphasen
                                                                = tabelle_bauphasen.idtabelle_bauphasen
                                                        WHERE view_Projekte.idTABELLE_Projekte = ?");

                    $stmt->bind_param("i", $_SESSION["projectID"]);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableRooms'   >
						<thead><tr>
						<th>ID</th>
                        <th>Raumbereich Nutzer</th>
						<th>Raumnr</th>
						<th>Raumbezeichnung</th>
						<th>Nutzfläche</th>
						<th>Geschoss</th>
                        <th>Bauetappe</th>
                        <th>Bauabschnitt</th>
                        <th>Bauphase</th>
                        <th>Bauphase-Fertigstellung</th>
                        <th>MT-relevant</th>                                                
						</tr></thead><tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["idTABELLE_Räume"] . "</td>";
                        echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                        echo "<td>" . $row["Raumnr"] . "</td>";
                        echo "<td>" . $row["Raumbezeichnung"] . "</td>";
                        echo "<td>" . $row["Nutzfläche"] . "</td>";
                        echo "<td>" . $row["Geschoss"] . "</td>";
                        echo "<td>" . $row["Bauetappe"] . "</td>";
                        echo "<td>" . $row["Bauabschnitt"] . "</td>";
                        echo "<td>" . $row["bauphase"] . "</td>";
                        echo "<td>" . $row["datum_fertigstellung"] . "</td>";
                        echo "<td>";
                        if ($row["MT-relevant"] === '0') {
                            echo "Nein";
                        } else {
                            echo "Ja";
                        }
                        echo "</td>";
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
            <div class="mt-4 card">
                <div class="card-header"><b>Neu</b>
                </div>
                <div class="card-body" id="newElements"></div>
            </div>
        </div>
        <div class='col-xxl-6'>
            <div class="mt-4 card">
                <div class="card-header"><b>Bestand</b>
                </div>
                <div class="card-body" id="bestandElements"></div>
            </div>
        </div>
    </div>
</div>

<script>


    $(document).ready(function () {
        var table = $('#tableRooms').DataTable({
            select: true,
            paging: true,
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            columnDefs: [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }
            ],
            order: [[1, "asc"]],
            orderMulti: true,
            language: {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
            mark: true
        });

        $('#tableRooms tbody').on('click', 'tr', function () {
            if ($(this).hasClass('info')) {
            } else {
                let id = table.row($(this)).data()[0];
                $.ajax({
                    url: "getNewElementsInRoomAusfuehrung.php",
                    data: {"roomID": id},
                    type: "POST",
                    success: function (data) {
                        $("#newElements").html(data);
                        $.ajax({
                            url: "getBestandElementsInRoomAusfuehrung.php",
                            data: {"roomID": id},
                            type: "POST",
                            success: function (data) {
                                $("#bestandElements").html(data);
                            }
                        });
                    }
                });
            }
        });

        $('#filter_MTrelevantRooms').change(function () {
            table.draw();
        });

        $.fn.dataTable.ext.search.push(
            function (settings, data) {
                if (settings.nTable.id !== 'tableRooms') {
                    return true;
                }
                if ($("#filter_MTrelevantRooms").is(':checked')) {
                    return data [10] === "Ja";
                } else {
                    return true;
                }
            }
        );
    });
</script>
</body>
</html>
