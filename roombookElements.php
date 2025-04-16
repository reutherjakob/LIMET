<!-- 17.2.25: Reworked -->
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
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
        .card-header {
            min-height: 4vh !important;
        }

    </style>
</head>

<body>
<div id="limet-navbar"></div> <!-- Container für Navbar Aufruf über onLoad -->
<div class="container-fluid bg-secondary bg-opacity-10">

    <div class="row">
        <div class="col-xxl-6">
            <div class="card mt-1">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xxl-6">Elemente in DB</div>
                        <div class="col-xxl-6 d-flex justify-content-end" id="CardHeaderELementesInDb"></div>
                    </div>
                </div>
                <div class="card-body" id="elementsInDB">
                    <?php
                    $mysqli = utils_connect_sql();
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

                    ?>
                </div>
            </div>
        </div>

        <div class="col-xxl-3">
            <div class="card mt-1">
                <div class="card-header">Elementgruppen</div>
                <div class="card-body" id="elementGroups">
                    <?php

                    $sql = "SELECT tabelle_element_gewerke.idtabelle_element_gewerke, tabelle_element_gewerke.Nummer, tabelle_element_gewerke.Gewerk
                                                                FROM tabelle_element_gewerke
                                                                ORDER BY tabelle_element_gewerke.Nummer;";
                    $result = $mysqli->query($sql);
                    echo "<div class='form-group row mt-1'>
                                                        <label class='control-label col-xxl-3' for='elementGewerk'>Gewerk</label>
                                                        <div class='col-xxl-9'>
                                                                <select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value=" . $row["idtabelle_element_gewerke"] . ">" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
                    }
                    echo "                       </select>	
                                                        </div>
                                        </div>";
                    echo "<div class='form-group row mt-1'>
                                                        <label class='control-label col-xxl-3' for='elementHauptgruppe'>Hauptgruppe</label>
                                                        <div class='col-xxl-9'>
                                                                <select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>
                                                                        <option selected>Gewerk auswählen</option>
                                                                </select>	
                                                        </div>
                                        </div>";
                    echo "<div class='form-group row mt-1'>
                                                        <label class='control-label col-xxl-3' for='elementGruppe'>Gruppe</label>
                                                        <div class='col-xxl-9'>
                                                                <select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>
                                                                        <option selected>Gewerk auswählen</option>
                                                                </select>	
                                                        </div>
                                        </div>";
                    $mysqli->close();
                    ?>
                </div>
            </div>
        </div>

        <div class="col-xxl-3">
            <div class="card mt-1">
                <div class="card-header"> Hier könnte ihre Inhalt stehen.</div>
                <div class="card-body" id=""> Wenn Mensch hier weitere Informationen sehen will, sag Bescheid welche. </div>
            </div>
        </div>

    </div>


    <div class='row'>
        <div class='col-xxl-6'>
            <div class="card mt-1">
                <div class="card-header">Räume mit Element</div>
                <div class="card-body flex-xxl-grow-1 h-auto" id="roomsWithElement"></div>
            </div>
        </div>
        <div class='col-xxl-6'>
            <div class="card mt-1">
                <div class="card-header" id="RäumeOhneElCardHeader ">

                    <div class="row ">
                        <div class="col-xxl-4 d-flex flex-nowrap align-items-center" id="CardHeaderRäumeOhneElement">
                            Räume ohne Element
                        </div>
                        <div class="col-xxl-8 d-flex flex-nowrap justify-content-end">
                            <button type='button' class='btn btn-outline-success btn-sm ' id='addElements'
                                    data-bs-toggle='modal' data-bs-target='#addElementsToRoomModal'><i
                                        class='fas fa-plus'></i> Element zu Raum hinzufügen
                            </button>
                            <button type='button' id="selectAllRows"
                                    class="btn btn-outline-primary btn-sm me-2 ms-2 ">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                                Sichtbare auswählen
                            </button>
                            <button type='button' id="deselectAllRows" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-times" aria-hidden="true"></i>
                                Keinen Raum auswählen
                            </button>
                        </div>

                    </div>
                </div>
                <div class="card-body h-auto" id="roomsWithoutElement"></div>
            </div>
        </div>
    </div>
</div>


<script>
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
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                bottomStart: 'pageLength',
                bottomEnd: 'paging',
                topStart: 'search',
                topEnd: null
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CardHeaderELementesInDb');

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

        $('#deselectAllRows').click(function () {
            $('#roomsWithoutElement table tbody tr:visible').each(function () {
                $(this).removeClass('selected');
                roomIDs = []

                //console.log(roomIDs);
            });
        });


    });


</script>
</body>
</html>
