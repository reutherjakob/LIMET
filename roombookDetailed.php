<?php
include '_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Detail</title>
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


</head>

<body style="height:100%">
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class='row'>
        <div class='col-lg-8'>
            <div class="mt-2 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">Räume im Projekt</div>
                        <div class="col-md-4"></div>
                        <div class="col-md-3">
                            <label class="float-right">
                                MT-relevante Räume: <input type="checkbox" id="filter_MTrelevantRooms"
                                                           checked="checked">
                            </label>
                        </div>
                        <div id="CardHeaderRaume" class="col-md-2"></div>
                    </div>
                </div>
                <div class="card-body" style="overflow: auto; ">
                    <?php
                    $mysqli = utils_connect_sql();

                    $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Nutzfläche,
                                                    tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, 
                                                    tabelle_räume.Bauabschnitt,  tabelle_räume.Raumnummer_Nutzer,
                                                    tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, 
                                                    tabelle_räume.idTABELLE_Räume, tabelle_räume.`MT-relevant`, `tabelle_räume`.`Anmerkung FunktionBO`
                                                FROM tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
                                                WHERE (((tabelle_projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

                    $result = $mysqli->query($sql);

                    /** @noinspection HtmlDeprecatedAttribute */
                    echo "<table class='table table-striped table-bordered table-sm' id='tableRooms'   >
						<thead><tr>
						<th>ID</th>
						<th>Raumnr</th>
                                                <th>R.NR.Nutzer</th>
						<th>Raumbezeichnung</th>
						<th>Nutzfläche</th>
						<th>Raumbereich Nutzer</th>
                                                <th>Ebene</th>
                                                <th>MT-relevant</th>
                                                <th>BO</th>
						</tr></thead><tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["idTABELLE_Räume"] . "</td>";
                        echo "<td>" . $row["Raumnr"] . "</td>";
                        echo "<td>" . $row["Raumnummer_Nutzer"] . "</td>";
                        echo "<td>" . $row["Raumbezeichnung"] . "</td>";
                        echo "<td>" . $row["Nutzfläche"] . "</td>";
                        echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                        echo "<td>" . $row["Geschoss"] . "</td>";
                        echo "<td>";
                        if ($row["MT-relevant"] === '0') {
                            echo "Nein";
                        } else {
                            echo "Ja";
                        }
                        echo "</td>";
                        echo "<td>";
                        if ($row["Anmerkung FunktionBO"] != null) {
                            echo "<button type='button' class='btn btn-sm btn-outline-dark' style='height=20px; ' id='buttonBO' value='" . $row["Anmerkung FunktionBO"] . "' data-bs-toggle='modal' data-bs-target='#boModal'><i class='fa fa-comment'></i></button>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="mt-2 card">
                <div class="card-header">Vermerke zu Raum</div>
                <div class="card-body" id="roomVermerke"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="mt-4 card">
                <div class="card-header">Elemente im Raum</div>
                <div class="card-body" id="roomElements"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="mt-4 card">
                <div class="card-header">

                    <div class="row">
                        <div class="col-6"> Variantenparameter</div>
                        <div class="col-6 d-flex justify-content-end" id="price"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="elementParameters"></div>
                </div>
            </div>
            <div class="mt-4 card">
                <div class="card-header" id="BestandsdatenCardHeader">Bestandsdaten
                    <button type='button' id='addBestandsElement'
                            class='btn ml-4 mt-2 btn-outline-success btn-sm float-right' value='Hinzufügen'
                            data-bs-toggle='modal' data-bs-target='#addBestandModal'><i class='fas fa-plus'></i>
                    </button>
                    <button type='button' id='reloadBestand'
                            class='btn ml-4 mt-2 btn-outline-secondary  float-right' value='reloadBestand'>
                        <i class="fa fa-retweet" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="card-body" id="elementBestand"></div>
            </div>
            <div class="mt-4 card">
                <div class="card-header">Standort/Verwendungsdaten</div>
                <div class="card-body" id="elementVerwendung"></div>
            </div>
        </div>
    </div>
    <hr>
    <div class="mt-4 card">
        <div class="card-header">
            <button type="button" class="btn btn-outline-dark btn-sm" id="showDBElementData"><i
                        class="fas fa-caret-right"></i></button>
            Datenbank-Elemente
        </div>
        <div class="card-body" style="display:none" id="DBElementData">
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="mt-4 card">
                        <div class="card-header">Elementgruppen</div>
                        <div class="card-body" id="elementGroups">
                            <?php
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
                            echo "</select></div></div>
                                    <div class='form-group row'>
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
                    <div class="mt-4 card">
                        <div class="card-header">Elemente in DB</div>
                        <div class="card-body" id="elementsInDB">
                            <?php
                            $sql = "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_elemente.Kurzbeschreibung
											FROM tabelle_elemente
											ORDER BY tabelle_elemente.ElementID;";

                            $result = $mysqli->query($sql);

                            /** @noinspection HtmlDeprecatedAttribute */
                            echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableElementsInDB'   >
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
                    <div class="mt-4 card">
                        <div class="card-header">Elementparameter</div>
                        <div class="card-body" id="elementParametersInDB"></div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="mt-4 card">
                        <div class="card-header">Elementkosten in anderen Projekten</div>
                        <div class="card-body" id="elementPricesInOtherProjects"></div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row mt-4">
                <div class="col-md-6 col-lg-6">
                    <div class="mt-4 card">
                        <div class="card-header">Geräte</div>
                        <div class="card-body" id="devicesInDB"></div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="mt-4 card">
                        <div class="card-header">Geräteparameter</div>
                        <div class="card-body" id="deviceParametersInDB"></div>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="mt-4 card">
                        <div class="card-header">Gerätepreise</div>
                        <div class="card-body" id="devicePrices"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div class='modal fade' id='boModal' role='dialog'>
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>BO-Anmerkung</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='boModalBody'>

            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    $.fn.dataTable.ext.search.push(
        function (settings, data) {
            if (settings.nTable.id !== 'tableRooms') {
                return true;
            }
            if ($("#filter_MTrelevantRooms").is(':checked')) {
                return data [7] === "Ja";
            } else {
                return true;
            }
        }
    );
    let table;

    $(document).ready(function () {
        /*
        $('#tableProjectVermerke').on('draw.dt', function () {
            $('.vermerk-popover').popover('dispose').popover({
                html: true,
                content: function () {
                    let content = $(this).attr('data-bs-content');
                    return '<div class="popover-content">' + content + '</div>';

                },
                placement: 'right'
            });

        });

        $(document).on('click', function (e) {
            if ($(e.target).closest('.popover').length === 0 && !$(e.target).hasClass('vermerk-popover')) {
                $('.vermerk-popover').popover('hide');
            }
        });

        $(document).on('click', '.vermerk-popover', function (e) {
            $('.vermerk-popover').not(this).popover('hide');
            $(this).popover('toggle');
            e.stopPropagation();
        });
        */
        $("#elementParameters").hide();
        $("#elementBestand").hide();
        $("#elementVerwendung").hide();

        let table = new DataTable('#tableRooms', {
            select: true,
            paging: {
                type: 'simple',
                numbers: 10
            },
            lengthChange: false,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            order: [[1, "asc"]],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: ''
            },
            mark: true
        });

        const table1 = new DataTable('#tableElementsInDB', {
            paging: {
                type: 'simple',
                numbers: 10
            },
            lengthChange: false,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            info: false,
            order: [[1, "asc"]],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json', search: ""
            }
        });


        $('#tableRooms tbody').on('click', 'tr', function () {
            $("#elementParameters").hide();
            $("#elementBestand").hide();
            $("#elementVerwendung").hide();
            const id = table.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"roomID": id},
                type: "GET",
                success: function () {
                    $("#RoomID").text(id);
                    $.ajax({
                        url: "getRoomVermerke.php",
                        type: "GET",
                        success: function (data) {
                            $("#roomVermerke").html(data);
                            $.ajax({
                                url: "getRoomElementsDetailed1.php",
                                type: "GET",
                                success: function (data) {
                                    $("#roomElements").html(data);
                                }
                            });
                        }
                    });
                }
            });
        });

        $('#tableElementsInDB tbody').on('click', 'tr', function () {
            const elementID = table1.row($(this)).data()[0];
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
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
        });

        $('#filter_MTrelevantRooms').change(function () {
            table.draw();
        });

        setTimeout(() => {
            let dt_searcher = $("#dt-search-0");
            dt_searcher.detach();
            $("#CardHeaderRaume").append(dt_searcher);
        }, 100);
    });


    $("button[value='reloadBestand']").click(function () {
        $("#elementBestand").html("");
        $.ajax({
            url: "getElementBestand.php",
            type: "GET",
            success: function (data) {
                makeToaster("Reloaded!", true);
                $("#elementBestand").html(data);
            }
        });
    });

    // DB Elemente einblenden
    $("#showDBElementData").click(function () {
        if ($("#DBElementData").is(':hidden')) {
            $(this).html("<i class='fas fa-caret-down'></i>");
            $("#DBElementData").show();
        } else {
            $(this).html("<i class='fas fa-caret-right'></i>");
            $("#DBElementData").hide();
        }
    });

    // Element Gewerk Änderung
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

    $("button[id='buttonBO']").click(function () {
        $("#boModalBody").html(this.value);
    });

</script>
</body>
</html>
