<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides();
include "_format.php";
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Bestand Eintragen</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png"/>

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
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-4 card">
        <div class="card-header">
            <div class="row d-flex flex-nowrap">
                <div class="col-xxl-4">
                    <strong> Elemente im Projekt </strong>
                </div>

                <div class="col-xxl-8 d-flex justify-content-end" id="CH_EIP">
                    <button type='button' class='btn btn-outline-dark ' id='createElementListPDF'>
                        <i class='far fa-file-pdf'></i> Elementliste PDF
                    </button>
                    <button type='button' class='btn btn-outline-dark ' id='createElementListWithPricePDF'>
                        <i class='far fa-file-pdf'></i> inkl. Preis
                    </button>
                </div>

            </div>
        </div>

        <div class="card-body">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
                        tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
                        tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Intern_idtabelle_Lose_Intern,
                        tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG
										FROM tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_elemente INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)) ON tabelle_elemente.idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
										WHERE (((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
										GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
										ORDER BY tabelle_elemente.ElementID;";
            $result = $mysqli->query($sql);
            echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableElementsInProject'>
									<thead><tr>
										<th>ID</th>
										<th>Anzahl</th>
										<th>ID</th>
										<th>Element</th>
										<th>Variante</th>
										<th>VariantenID</th>
										<th>Bestand</th>										
										<th>Kosten </th> 
										<th>Positionspreis</th>
										<th>Gewerk</th>
				
									</tr>
									</thead>
									<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
                echo "<td>" . $row["SummevonAnzahl"] . "</td>";
                echo "<td>" . $row["ElementID"] . "</td>";
                echo "<td>" . $row["Bezeichnung"] . "</td>";
                echo "<td>" . $row["Variante"] . "</td>";
                echo "<td>" . $row["idtabelle_Varianten"] . "</td>";
                if ($row["Neu/Bestand"] == 1) {
                    echo "<td>Nein</td>";
                } else {
                    echo "<td>Ja</td>";
                }

                echo "<td>" . format_money($row["Kosten"]) . "</td>";
                echo "<td>" . format_money(intval($row["Kosten"]) * intval($row["SummevonAnzahl"])) . "</td>";

                echo "<td>" . $row["Gewerke_Nr"] . "</td>";

                echo "</tr>";
            }
            echo "</tbody></table>";
            $mysqli->close();
            ?>
        </div>
    </div>

    <div class="mt-4 card">
        <div class="row">
            <div class="col-xxl-8">
                <div class="mt-1 card">
                    <div class="card-header">
                        <button type="button" class="btn btn-outline-dark btn-sm" id="showRoomsWithAndWithoutElement">
                            <i class="fas fa-caret-up"></i>
                        </button>
                        <label>Räume mit Element</label>
                        <div class="float-right" id="CH_RME"></div>
                    </div>
                    <div class="card-body" id="roomsWithAndWithoutElements">
                    </div>
                </div>
            </div>

            <div class="col-xxl-4">
                <div class="mt-1 card">
                    <div class="card-header " id="BestandsdatenCardHeader">
                        <div class="row">
                            <div class="col-xxl-6"><label>Bestandsdaten</label></div>
                            <div class="col-xxl-6 d-flex align-items-center justify-content-end">
                                <button type='button' id='addBestandsElement'
                                        class='btn btn-outline-success btn-sm' value='Hinzufügen'
                                        data-bs-toggle='modal' data-bs-target='#addBestandModal'><i
                                            class='fas fa-plus'></i>
                                </button>
                                <button type='button' id='reloadBestand'
                                        class='btn  btn-sm btn-outline-secondary ' value='reloadBestand'>
                                    <i class="fa fa-retweet" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" id="elementBestand"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 card">
        <div class="card-header">
            <button type="button" class="btn btn-outline-dark btn-sm" id="showDBElementData">
                <i class="fas fa-caret-down"></i></button>
            <label>DB Elemente</label>

        </div>
        <div class="card-body" id="DBElementData" style="display: none;">
            <div class="row">
                <div class="col-xxl-8">
                    <div class='mt-1 card'>
                        <div class='card-header'><label>Elementgruppen</label></div>
                        <div class='card-body' id='elementGroups'>
                            <?php
                            $mysqli = utils_connect_sql();
                            $sql = "SELECT tabelle_element_gewerke.idtabelle_element_gewerke, tabelle_element_gewerke.Nummer, tabelle_element_gewerke.Gewerk
												FROM tabelle_element_gewerke
												ORDER BY tabelle_element_gewerke.Nummer;";

                            $result = $mysqli->query($sql);
                            echo "<div class='form-group row'>
									 			<label class='control-label col-xxl-2' for='elementGewerk'>Gewerk</label>
												<div class='col-xxl-10'>
													<select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=" . $row["idtabelle_element_gewerke"] . ">" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
                            }
                            echo "</select>	
												</div>
										</div>";

                            echo "<div class='form-group row'>
									 			<label class='control-label col-xxl-2' for='elementHauptgruppe'>Hauptgr.</label>
												<div class='col-xxl-10'>
													<select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>
														<option selected>Gewerk auswählen</option>
													</select>	
												</div>
										</div>";

                            echo "<div class='form-group row'>
									 			<label class='control-label col-xxl-2' for='elementGruppe'>Gruppe</label>
												<div class='col-xxl-10'>
													<select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>
														<option selected>Gewerk auswählen</option>
													</select>	
												</div>
										</div>";
                            ?>
                        </div>
                    </div>
                    <div class="mt-1 card">
                        <div class="card-header ">
                            <label>Elemente in DB</label>
                            <div class="d-inline-flex float-right" id="CH_elementsInDB"></div>
                        </div>
                        <div class="card-body" id="elementsInDB">
                            <?php
                            $sql = "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_elemente.Kurzbeschreibung
											FROM tabelle_elemente
											ORDER BY tabelle_elemente.ElementID;";

                            $result = $mysqli->query($sql);

                            echo "<table class='table table-striped table-sm table-hover border border-light border-5' id='tableElementsInDB' >
									<thead><tr>
									<th>ID</th>
									<th>ElementID</th>
									<th>Element</th>
									<th>Beschreibung</th>
                                                                        <th></th>
									</tr></thead><tbody>";

                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["idTABELLE_Elemente"] . "</td>";
                                echo "<td>" . $row["ElementID"] . "</td>";
                                echo "<td>" . $row["Bezeichnung"] . "</td>";
                                echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
                                echo "<td><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-outline-dark btn-sm'' value='changeElement' data-bs-toggle='modal' data-bs-target='#changeElementModal'><i class='fas fa-pencil-alt'></i></button></td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";

                            $mysqli->close();
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4">
                    <div class="mt-1 card">
                        <div class='card-header'><label>Geräte zu Element</label></div>
                        <div class='card-body' id='devicesInDB'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script src="_utils.js"></script>
<script>
    var tableElementsInProject, tableElementsInDB;
    $(document).ready(function () {


        tableElementsInProject = new DataTable('#tableElementsInProject', {
            paging: true,
            select: true,
            lengthChange: true,
            pageLength: 15,
            order: [[2, "asc"]],
            columnDefs: [
                {
                    targets: [0, 5],
                    visible: false,
                    searchable: false
                }
            ],
            keys: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "",
                decimal: ",",
                thousands: ".",
                searchPlaceholder: "Suche..."
            },
            stateSave: true,
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: 'info',
                bottomEnd: ['search', 'pageLength', 'paging']
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CH_EIP');

            }
        });


        $('#tableElementsInProject tbody').on('click', 'tr', function () {
            $("#devicesInDB").html("");
            $("#elementBestand").html("");

            let elementID = tableElementsInProject.row($(this)).data()[0];
            let variantenID = tableElementsInProject.row($(this)).data()[5];
            let bestand = 1;
            if (tableElementsInProject.row($(this)).data()[6] === "Ja") {
                bestand = 0;
            }
            $.ajax({
                url: "getRoomsWithElement1.php",

                data: {"elementID": elementID, "variantenID": variantenID, "bestand": bestand},
                type: "GET",
                success: function (data) {
                    $("#roomsWithAndWithoutElements").html(data);
                    setTimeout(function () {
                        $('#tableRoomsWithElement tbody').on('click', 'tr', function () {
                            let id = tableRoomsWithElement.row($(this)).data()[0].display;
                            let stk = $("#amount" + id).val();
                            $.ajax({
                                url: "getElementBestand.php",
                                data: {"id": id, "stk": stk},
                                type: "GET",
                                success: function (data) {
                                    $("#elementBestand").html(data);
                                }
                            });
                        });
                    }, 100)
                }
            });
        });

        tableElementsInDB = new DataTable('#tableElementsInDB', {
            paging: true,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [4],
                    visible: true,
                    searchable: false,
                    orderable: false
                }
            ],
            select: true,
            info: true,
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            order: [[1, "asc"]],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "", searchPlaceholder: "Suche..."
            },
            layout: {
                topEnd: "search",
                topStart: null,
                bottomStart: 'info',
                bottomEnd: ["pageLength", 'paging']
            },
            initComplete: function (settings, json) {
                // Your initComplete function here
            }
        });


        $('#tableElementsInDB tbody').on('click', 'tr', function () { //todo
            let elementID = tableElementsInDB.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"elementID": elementID},
                type: "GET",
                success: function () {
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
        });

    });

    // PDF erzeugen
    $('#createElementListPDF').click(function () {
        window.open('/pdf_createElementListPDF.php');
    });

    $('#createElementListWithPricePDF').click(function () {
        window.open('/pdf_createElementListWithPricePDF.php');
    });

    $("#showRoomsWithAndWithoutElement").click(function () {
        if ($("#roomsWithAndWithoutElements").is(':hidden')) {
            $(this).html("<i class='fas fa-caret-up'></i>");
            $("#roomsWithAndWithoutElements").show();
            $("#elementBestand").show();
        } else {
            $(this).html("<i class='fas fa-caret-down'></i>");
            $("#roomsWithAndWithoutElements").hide();
            $("#elementBestand").hide();
        }
    });

    $("#showDBElementData").click(function () {
        if ($("#DBElementData").is(':hidden')) {
            $(this).html("<i class='fas fa-caret-up'></i>");
            $("#DBElementData").show();
        } else {
            $(this).html("<i class='fas fa-caret-down'></i>");
            $("#DBElementData").hide();
        }
    });


    $("#addBestand").click(function () {
        $("#addBestandModal").modal('hide');
        let inventarNr = $("#invNr").val();
        let anschaffungsJahr = $("#year").val();
        let serienNr = $("#serNr").val();
        let gereatID = $("#geraetNr").val();
        let currentPlace = $("#currentPlace").val();
        if (inventarNr !== "") {
            $.ajax({
                url: "addBestand.php",
                data: {
                    "inventarNr": inventarNr,
                    "anschaffungsJahr": anschaffungsJahr,
                    "serienNr": serienNr,
                    "gereatID": gereatID,
                    "currentPlace": currentPlace
                },
                type: "GET",
                success: function (data) {
                    // alert(data);
                    makeToaster(data, true);
                    $("#addBestandModal").modal('hide');
                    $.ajax({
                        url: "getElementBestand.php",
                        type: "GET",
                        success: function (data) {
                            $("#elementBestand").html(data);
                        }
                    });
                }
            });

        } else {
            alert("Bitte Inventarnummer angeben!");
        }
    });

</script>
</body>
</html>
