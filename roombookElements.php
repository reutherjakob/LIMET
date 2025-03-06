<!-- 17.2.25: Reworked -->
<?php
include '_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Elemente</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png"/>

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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .card-body {
            overflow: auto;
        }

        .card-header {
            min-height: 50px; /* Adjust this value as needed */
        }

    </style>
</head>

<body>
<div id="limet-navbar"></div> <!-- Container für Navbar Aufruf über onLoad -->
<div class="container-fluid">
    <div class='row'>
        <div class='col-lg-12'>
            <div class="mt-1 card">
                <div class="card-header">Elemente</div>
                <div class="card-body" id="DBElementData">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mt-1 card">
                                <div class="card-header">Elementgruppen</div>
                                <div class="card-body" id="elementGroups">
                                    <?php
                                    $mysqli = utils_connect_sql();
                                    $sql = "SELECT tabelle_element_gewerke.idtabelle_element_gewerke, tabelle_element_gewerke.Nummer, tabelle_element_gewerke.Gewerk
                                                                FROM tabelle_element_gewerke
                                                                ORDER BY tabelle_element_gewerke.Nummer;";
                                    $result = $mysqli->query($sql);
                                    echo "<div class='form-group row'>
                                                        <label class='control-label col-md-2' for='elementGewerk'>Gewerk</label>
                                                        <div class='col-md-10'>
                                                                <select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value=" . $row["idtabelle_element_gewerke"] . ">" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
                                    }
                                    echo "                       </select>	
                                                        </div>
                                        </div>";
                                    echo "<div class='form-group row'>
                                                        <label class='control-label col-md-2' for='elementHauptgruppe'>Hauptgruppe</label>
                                                        <div class='col-md-10'>
                                                                <select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>
                                                                        <option selected>Gewerk auswählen</option>
                                                                </select>	
                                                        </div>
                                        </div>";
                                    echo "<div class='form-group row'>
                                                        <label class='control-label col-md-2' for='elementGruppe'>Gruppe</label>
                                                        <div class='col-md-10'>
                                                                <select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>
                                                                        <option selected>Gewerk auswählen</option>
                                                                </select>	
                                                        </div>
                                        </div>";
                                    ?>
                                </div>
                            </div>
                            <div class="mt-1 card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">Elemente in DB</div>
                                        <div class="col-6" id="CardHeaderELementesInDb"></div>
                                    </div>
                                </div>
                                <div class="card-body" id="elementsInDB">
                                    <?php
                                    $sql = "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_elemente.Kurzbeschreibung
											FROM tabelle_elemente 
											ORDER BY tabelle_elemente.ElementID;";

                                    $result = $mysqli->query($sql);

                                    echo "<table class='table table-sm table-responsive compact table-striped table-hover border border-light border-5 ' id='tableElementsInDB'>
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
                        <div class="col-md-3 col-lg-3">
                            <div class="mt-1 card">
                                <div class="card-header">Elementparameter</div>
                                <div class="card-body" id="elementParametersInDB"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="mt-1 card">
                                <div class="card-header">Elementkosten in anderen Projekten</div>
                                <div class="card-body" id="elementPricesInOtherProjects"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-lg-6'>
            <div class="mt-1 card">
                <div class="card-header h-100">Räume mit Element</div>
                <div class="card-body" id="roomsWithElement"></div>
            </div>
        </div>
        <div class='col-lg-6'>
            <div class="mt-1 card">
                <div class="card-header h-100" id="RäumeOhneElCardHeader ">

                    <div class="row ">
                        <div class="col-lg-5"> Räume ohne Element</div>
                        <div class="col-lg-7 d-flex flex-nowrap justify-content-end">
                            <button type='button' class='btn btn-outline-success btn-sm ' id='addElements'
                                    data-bs-toggle='modal' data-bs-target='#addElementsToRoomModal'><i
                                        class='fas fa-plus'></i> Element zu Raum hinzufügen
                            </button>
                            <button type='button' id="selectAllRows" class="btn btn-primary btn-sm">Sichtbare
                                auswählen
                            </button>
                        </div>

                    </div>
                </div>
                <div class="card-body" id="roomsWithoutElement"></div>
            </div>
        </div>
    </div>


    <script>
        let targetDiv = document.getElementById('elementPricesInOtherProjects');
        targetDiv.style.height = '650px';
        targetDiv.style.overflow = 'hidden';
        targetDiv.style.overflowY = 'scroll';
        var tableElementsInDB;
        $(document).ready(function () {
            tableElementsInDB = new DataTable('#tableElementsInDB', {
                select: true,
                paging: true,
                pageLength: 10,
                lengthChange: true,
                info: false,
                order: [[1, 'asc']],
                columnDefs: [
                    {
                        targets: [0],
                        visible: false,
                        searchable: false
                    }
                ],
                language: {
                    search: ""
                },
                layout: {
                    bottomStart: 'pageLength',
                    bottomEnd: 'paging',
                    topStart: 'search',
                    topEnd: null
                },
                initComplete: function () {
                    let dt_searcher = document.getElementById("dt-search-0");
                    dt_searcher.parentNode.removeChild(dt_searcher);
                    document.getElementById('CardHeaderELementesInDb').appendChild(dt_searcher);
                }
            });

            $('#tableElementsInDB tbody').on('click', 'tr', function () {


                let elementID = tableElementsInDB.row($(this)).data()[0];
                $.ajax({
                    url: "setSessionVariables.php",
                    data: {"elementID": elementID},
                    type: "GET",
                    success: function () {
                        $.ajax({
                            url: "getStandardElementParameters.php",
                            data: {"elementID": elementID},
                            type: "GET",
                            success: function (data) {
                                $("#elementParametersInDB").html(data);
                                $.ajax({
                                    url: "getElementPricesInDifferentProjects.php",
                                    data: {"elementID": elementID},
                                    type: "GET",
                                    success: function (data) {
                                        $("#elementPricesInOtherProjects").html(data);
                                        $.ajax({
                                            url: "getDevicesToElement.php",
                                            data: {"elementID": elementID},
                                            type: "GET",
                                            success: function (data) {
                                                $("#devicesInDB").html(data);
                                                $.ajax({
                                                    url: "getRoomsWithElement.php",
                                                    data: {"elementID": elementID},
                                                    type: "GET",
                                                    success: function (data) {
                                                        $("#roomsWithElement").html(data);
                                                        $.ajax({
                                                            url: "getRoomsWithoutElement.php",
                                                            data: {"elementID": elementID},
                                                            type: "GET",
                                                            success: function (data) {
                                                                $("#roomsWithoutElement").html(data);

                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            });

            $('#elementGewerk').change(function () {
                let gewerkID = this.value;
                $.ajax({
                    url: "getElementGroupsByGewerk.php",
                    data: {"gewerkID": gewerkID},
                    type: "GET",
                    success: function (data) {
                        $("#elementGroups").html(data);
                    }
                });
            });

            $('#selectAllRows').click(function () {
                $('#roomsWithoutElement table tbody tr:visible').each(function () {
                    $(this).addClass('selected');
                    let roomID = tableRoomsWithoutElement.row($(this)).data()[0];
                    if (!roomIDs.includes(roomID)) {
                        roomIDs.push(roomID);
                    }
                    //console.log(roomIDs);
                });
            });
        });
    </script>
</body>
</html>
