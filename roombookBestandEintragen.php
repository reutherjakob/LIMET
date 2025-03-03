<?php
include '_utils.php';
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>


    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>

    <style>
        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .top .dt-buttons {
            margin-right: 10px;
        }

        .bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bottom .dataTables_info {
            margin-right: 10px;
        }

        .card-body {
            padding: 3px;
            overflow: auto;
        }

        .card-header {
            height: 50px;
        }

    </style>
</head>

<body style="height:100%">
<div id="limet-navbar"></div>
<div class="container-fluid">

    <div class="mt-4 card">
        <div class="card-header container-fluid d-flex">

            <div class="row w-100">
                <div class="col-md-2">
                    <strong> Elemente im Projekt </strong>
                </div>

                <div class="col-md-7 d-inline-flex justify-content-start">
                    <button type='button' class='btn h-75 btn-outline-dark ' id='createElementListPDF'>
                        <i class='far fa-file-pdf'></i> Elementliste PDF
                    </button>
                    <button type='button' class='btn h-75 btn-outline-dark ' id='createElementListWithPricePDF'>
                        <i class='far fa-file-pdf'></i> inkl. Preis
                    </button>
                </div>
                <div class="col-md-3 d-inline-flex justify-content-end" id="CH_EIP"></div>
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
            echo "<table class='table table-striped table-bordered table-sm' id='tableElementsInProject'  cellspacing='0' width='100%'>
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
            <div class="col-md-8">
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

            <div class="col-md-4">
                <div class="mt-1 card">
                    <div class="card-header" id="BestandsdatenCardHeader">
                        <label>Bestandsdaten</label>
                        <button type='button' id='addBestandsElement'
                                class='btn btn-outline-success btn-sm float-right' value='Hinzufügen'
                                data-toggle='modal' data-target='#addBestandModal'><i class='fas fa-plus'></i></button>
                        <button type='button' id='reloadBestand'
                                class='btn btn-outline-secondary btn-sm float-right' value='reloadBestand'>
                            <i class="fa fa-retweet" aria-hidden="true"></i>
                        </button>
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
                <div class="col-md-8">
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
									 			<label class='control-label col-md-2' for='elementGewerk'>Gewerk</label>
												<div class='col-md-10'>
													<select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=" . $row["idtabelle_element_gewerke"] . ">" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
                            }
                            echo "</select>	
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

                            echo "<table class='table table-striped table-sm' id='tableElementsInDB'  cellspacing='0' width='100%'>
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
                                echo "<td><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-outline-dark btn-sm'' value='changeElement' data-toggle='modal' data-target='#changeElementModal'><i class='fas fa-pencil-alt'></i></button></td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";

                            $mysqli->close();
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
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
    $(document).ready(function () {
        $('#tableElementsInProject').DataTable({
            "paging": true,
            "select": true,
            "lengthChange": true,
            "pageLength": 15,
            "order": [[2, "asc"]],
            "columnDefs": [
                {
                    "targets": [0, 5],
                    "visible": false,
                    "searchable": false
                }
            ],
            "keys":true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                "search": "",
                "decimal": ",",
                "thousands": ".",
                "searchPlaceholder": "Suche"

            },
            "stateSave": true,
            /* "buttons":[{extend: 'excel', exportOptions: {columns: function (idx) {return idx !== 5 && idx !== 8;}}}], */
            "dom": '<"top"lf>rt<"bottom"ip><"clear">',
            "initComplete": function () {
                move_item("tableElementsInProject_filter", "CH_EIP");
                $('#tableElementsInProject_filter label').contents().filter(function () {
                    return this.nodeType === 3; // Node.TEXT_NODE
                }).remove();
            }

        });

        let table = $('#tableElementsInProject').DataTable();

        $('#tableElementsInProject tbody').on('click', 'tr', function () {
            $("#devicesInDB").html("");
            $("#elementBestand").html("");

            var elementID = table.row($(this)).data()[0];
            let variantenID = table.row($(this)).data()[5];
            var bestand = 1;
            if (table.row($(this)).data()[6] === "Ja") {
                bestand = 0;
            }
            $.ajax({
                url: "getRoomsWithElement_2.php",
                data: {"elementID": elementID, "variantenID": variantenID, "bestand": bestand},
                type: "GET",
                success: function (data) {
                    $("#roomsWithAndWithoutElements").html(data);

                }
            });
        });

        $('#tableElementsInDB').DataTable({
            "paging": true,
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [4],
                    "visible": true,
                    "searchable": false,
                    "sortable": false
                }
            ],
            "select": true,
            "info": true,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "order": [[1, "asc"]],
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
            "initComplete": function () {
                move_item("tableElementsInDB_filter", "CH_elementsInDB");
                $('#tableElementsInDB_filter label').contents().filter(function () {
                    return this.nodeType === 3; // Node.TEXT_NODE
                }).remove();

            }
        });

        let table1 = $('#tableElementsInDB').DataTable();
        $('#tableElementsInDB tbody').on('click', 'tr', function () {
            let elementID = table1.row($(this)).data()[0];
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
        console.log("Add BEstand Click");
        $("#addBestandModal").modal('hide');
        var inventarNr = $("#invNr").val();
        var anschaffungsJahr = $("#year").val();
        var serienNr = $("#serNr").val();
        var gereatID = $("#geraetNr").val();
        var currentPlace = $("#currentPlace").val();

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
                            $("#elementBestand").html(data)
                            //$("#elementelementBestandsInLot").html(data);
                        }
                    });
                }
            });

        } else {
            alert("Bitte Inventarnummer angeben!");
        }

    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            // Close all modals
            let modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
                modal.style.display = 'none';
            });

            // Remove the backdrop
            let backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => {
                backdrop.parentNode.removeChild(backdrop);
            });

            // Ensure the body is scrollable again
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        }
    });

</script>
</body>
</html>
