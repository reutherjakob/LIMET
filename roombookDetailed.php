<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Detail</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

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

    <!--- Boostrap Selct2 css -->
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
          rel="stylesheet">

</head>
<?php
require_once 'utils/_utils.php';
init_page_serversides();
?>

<body style="height:100%">
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class='row'>
        <div class='col-xxl-8'>
            <div class="mt-2 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xxl-6">Räume im Projekt</div>

                        <div id="CardHeaderRaume" class="col-xxl-6 d-flex justify-content-end align-items-center">

                            <input type="checkbox" id="filter_EntfalleneRooms" class="btn-check">
                            <label class="btn btn-outline-dark btn-sm float-right" for="filter_EntfalleneRooms">
                                Entfallene ausblenden </label>

                            <input type="checkbox" id="filter_MTrelevantRooms" checked class="btn-check">
                            <label class="btn btn-outline-dark btn-sm float-right ms-1 me-1" for="filter_MTrelevantRooms"> Nur MT-relevante </label>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="overflow: auto; ">
                    <?php
                    $mysqli = utils_connect_sql();
                    $projectID = $_SESSION["projectID"] ?? null;

                    if (!is_numeric($projectID)) {
                        die("Ungültige Projekt-ID");
                    }

                    $stmt = $mysqli->prepare("
                        SELECT 
                            r.Raumnr, r.Raumbezeichnung, r.Nutzfläche,
                            r.`Raumbereich Nutzer`, r.Geschoss, r.Bauetappe, 
                            r.Bauabschnitt, r.Raumnummer_Nutzer,
                            r.`Anmerkung allgemein`, r.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, 
                            r.idTABELLE_Räume, r.`MT-relevant`, 
                            r.`Anmerkung FunktionBO`, r.Entfallen
                        FROM tabelle_räume r
                        INNER JOIN tabelle_projekte p 
                            ON r.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte
                        WHERE p.idTABELLE_Projekte = ?
                    ");

                    $stmt->bind_param("i", $projectID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    echo "<table class='table table-striped table-sm table-hover table-bordered border border-light border-5' id='tableRooms'   >
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
                         <th> <i class='fas fa-slash'></i> </th>
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
                        if ((int)$row["MT-relevant"] === 0) {
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
                        echo "<td>" . $row["Entfallen"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    ?>
                </div>
            </div>
        </div>
        <div class="col-xxl-4">
            <div class="mt-2 card">
                <div class="card-header">Vermerke zu Raum</div>
                <div class="card-body" id="roomVermerke"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-8">
            <div class="mt-4 card">
                <div class="card-header">Elemente im Raum</div>
                <div class="card-body" id="roomElements"></div>
            </div>
        </div>
        <div class="col-xxl-4">
            <div class="mt-4 card">
                <div class="card-header">

                    <div class="row">
                        <div class="col-xxl-6"> Variantenparameter</div>
                        <div class="col-xxl-6 d-flex justify-content-end" id="price"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="elementParameters"></div>
                </div>
            </div>
            <div class="mt-4 card">
                <div class="card-header" id="BestandsdatenCardHeader">Bestandsdaten

                    <button type='button' id='addBestandsElement'
                            class='btn btn-sm ml-4 mt-2 btn-outline-success ' value='Hinzufügen'
                            data-bs-toggle='modal' data-bs-target='#addBestandModal'><i class='fas fa-plus'></i>
                    </button>
                    <button type='button' id='reloadBestand'
                            class='btn btn-sm  ml-4 mt-2 btn-outline-secondary' value='reloadBestand'>
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
                <div class="col-xxl-6">
                    <div class="mt-4 card">
                        <div class="card-header" id="CardHeaderElementGruppen">Elementgruppen
                            <button type="reset" class="btn btn-sm float-end" title="Reset" id="ResetElementGroups">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <div class="card-body" id="elementGroups">
                            <?php include "getElementgruppenCardContent.php"; ?>
                        </div>
                    </div>
                    <div class="mt-4 card">
                        <div class="card-header d-flex justify-content-center align-items-center">
                            <div class="col-10"> Elemente in DB</div>
                            <div class="col-2" id="CardHeaderElementesInDb"></div>
                        </div>
                        <div class="card-body" id="elementsInDB">
                            <?php include "getElementsInDbCardBodyContent.php"; ?>
                        </div>
                    </div>

                </div>
                <div class="col-xxl-3 col-xxl-3">
                    <div class="mt-4 card">
                        <div class="card-header">Elementparameter</div>
                        <div class="card-body" id="elementParametersInDB"></div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xxl-3">
                    <div class="mt-4 card">
                        <div class="card-header">Elementkosten in anderen Projekten</div>
                        <div class="card-body" id="elementPricesInOtherProjects"></div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row mt-4">
                <div class="col-xxl-6 col-xxl-6">
                    <div class="mt-4 card">
                        <div class="card-header">Geräte</div>
                        <div class="card-body" id="devicesInDB"></div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xxl-3">
                    <div class="mt-4 card">
                        <div class="card-header">Geräteparameter</div>
                        <div class="card-body" id="deviceParametersInDB"></div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xxl-3">
                    <div class="mt-4 card">
                        <div class="card-header">Gerätepreise</div>
                        <div class="card-body" id="devicePrices"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div class='modal fade' id='boModal' role='dialog' tabindex="-1">
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

    var tableRooms, tableElementsInDB;
    var currentSort = {column: 0, dir: 'asc'};
    var tableRoomElements;  // tableRoomElements  && hideZeroFilter required for: getRoomELmeentsDetailed1.php
    const hideZeroFilter = function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tableRoomElements') {
            return true; // Don't filter other tables
        }
        let hideZero = $("#hideZeroRows").is(":checked");
        let row = tableRoomElements.row(dataIndex).node();
        let amount = $(row).find('input[id^="amount"]').val();
        amount = parseInt(amount) || 0;
        return !(hideZero && (amount === 0));
    }

    $(document).ready(function () {
        $("#elementParameters").hide();
        $("#elementBestand").hide();
        $("#elementVerwendung").hide();

        tableRooms = new DataTable('#tableRooms', {
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
                search: '',
                searchPlaceholder: 'Suche... '
            },
            mark: true,
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: "info",
                bottomEnd: ["search", "paging"]
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CardHeaderRaume');
            }

        });

        $.fn.dataTable.ext.search.push(
            function (settings, data,) {
                if (settings.nTable.id !== 'tableRooms') {
                    return true;
                }
                let mtRelevant = data[7];
                let filterMTRelevant = $("#filter_MTrelevantRooms").is(':checked');
                if (!filterMTRelevant) {
                    return true;
                }
                return mtRelevant === "Ja";
            }
        );
        $.fn.dataTable.ext.search.push(
            function (settings, data,) {
                if (settings.nTable.id !== 'tableRooms') {
                    return true;
                }
                let entfallen = data[9];
                let filterentfallen = $("#filter_EntfalleneRooms").is(':checked');
                if (!filterentfallen) {
                    return true;
                }
                return entfallen === "0";
            }
        );

        $('#filter_EntfalleneRooms').change(function () {
            tableRooms.draw();
        });

        $('#filter_MTrelevantRooms').change(function () {
            tableRooms.draw();
        });

        $('#tableRooms tbody').on('click', 'tr', function () {
            $("#elementParameters").hide();
            $("#elementBestand").hide();
            $("#elementVerwendung").hide();
            const id = tableRooms.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"roomID": id},
                type: "POST",
                success: function () {
                    $("#RoomID").text(id);
                    $.ajax({
                        url: "getRoomVermerke.php",
                        type: "POST",
                        success: function (data) {
                            $("#roomVermerke").html(data);

                            $.ajax({
                                url: "getRoomElementsDetailed1.php",
                                type: "POST",
                                success: function (data) {
                                    $("#roomElements").html(data);
                                }
                            });

                        }
                    });
                }
            });
        });
        init_table_elementsinDB();
    });

    $("button[value='reloadBestand']").click(function () {
        $("#elementBestand").html("");
        $.ajax({
            url: "getElementBestand.php",
            type: "POST",
            success: function (data) {
                makeToaster("Reloaded!", true);
                $("#elementBestand").html(data);
            }
        });
    });

    $("#showDBElementData").click(function () {
        if ($("#DBElementData").is(':hidden')) {
            $(this).html("<i class='fas fa-caret-down'></i>");
            $("#DBElementData").show();
        } else {
            $(this).html("<i class='fas fa-caret-right'></i>");
            $("#DBElementData").hide();
        }
    });


    $("button[id='buttonBO']").click(function () {
        $("#boModalBody").html(this.value);
    });


    function init_table_elementsinDB() {

        tableElementsInDB = new DataTable('#tableElementsInDB', {
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
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json', search: "",
                searchPlaceholder: "Suche..."
            },
            initComplete: function () {
                $('#CardHeaderElementesInDb .xxx').remove();
                $('#tableElementsInDB_wrapper .dt-search label').remove();
                $('#tableElementsInDB_wrapper .dt-search').children().removeClass("form-control form-control-sm float-right").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#CardHeaderElementesInDb');
            }
        });

        $('#tableElementsInDB tbody').on('click', 'tr', function () {
            const elementID = tableElementsInDB.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"elementID": elementID},
                type: "POST",
                success: function () {
                    $.ajax({
                        url: "getStandardElementParameters.php",
                        data: {"elementID": elementID},
                        type: "POST",
                        success: function (data) {
                            $("#elementParametersInDB").html(data);
                            $.ajax({
                                url: "getElementPricesInDifferentProjects.php",
                                data: {"elementID": elementID},
                                type: "POST",
                                success: function (data) {
                                    $("#elementPricesInOtherProjects").html(data);
                                    $.ajax({
                                        url: "getDevicesToElement.php",
                                        data: {"elementID": elementID},
                                        type: "POST",
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
    }
</script>
</body>
</html>
