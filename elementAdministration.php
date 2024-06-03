<?php
session_start();
$_SESSION["dbAdmin"] = "1";
include '_utils.php';
init_page_serversides("x");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <title>Element Admin</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
            <link rel="icon" href="iphone_favicon.png">

                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
                        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

                        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
                        <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

                        </head> 

                        <body style="height:100%">

                            <div class="container-fluid" >
                                <div id="limet-navbar"></div> <!-- Container für Navbar -->		

                                <div class="mt-4 card">
                                    <div class="card-header">Elemente</div>
                                    <div class="card-body">
                                        <div class="row mt-1">
                                            <div class='col-md-6'>
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
                                                    <div class="card-header"><label>Elemente in DB</label></div>
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
    echo "<td><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-outline-dark btn-xs'' value='changeElement' data-toggle='modal' data-target='#changeElementModal'><i class='fas fa-pencil-alt'></i></button></td>";
    echo "</tr>";
}
echo "</tbody></table>";

$mysqli->close();
?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='col-md-6'>
                                                <div class="mt-1 card">
                                                    <div class="card-header"><label>Schätzkosten in Projekten</label></div>
                                                    <div class="card-body" id="elementPricesInOtherProjects"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr></hr>
                                <div class="mt-1 card">
                                    <div class="card-header">Geräte</div>
                                    <div class="card-body">
                                        <div class="row mt-1">
                                            <div class='col-md-4'>
                                                <div class='mt-1 card'>
                                                    <div class='card-header'><label>Geräte zu Element</label></div>
                                                    <div class='card-body' id='devicesInDB'></div>
                                                </div>
                                            </div>
                                            <div class='col-md-4'>
                                                <div class='mt-1 card'>
                                                    <div class='card-header'><label>Geräteparameter</label></div>
                                                    <div class='card-body' id='deviceParametersInDB'></div>
                                                </div>
                                            </div>
                                            <div class='col-md-4'>
                                                <div class='mt-1 card'>
                                                    <div class='card-header'><label>Gerätepreise</label></div>
                                                    <div class='card-body' id='devicePrices'></div>
                                                </div>
                                                <div class='mt-1 card'>
                                                    <div class='card-header'><label>Wartungspreise</label></div>
                                                    <div class='card-body' id='deviceServicePrices'></div>
                                                </div>
                                                <div class='mt-1 card'>
                                                    <div class='card-header'><label>Lieferanten</label></div>
                                                    <div class='card-body' id='deviceLieferanten'></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--	
                                            <div class="col-md-4 col-sm-4">
                                                    <div class="panel-group">
                                                            <div class="panel panel-info">
                                                                    <div class="panel-heading"><label>Elementparameter</label></div>
                                                                    <div class="panel-body" id="elementParametersInDB"></div>
                                                            </div>
                                                    </div>
                                            </div>
                                    </div>
                                    
                                    <div class="row">
                                            <div class="col-md-4 col-sm-4">
                                                    <div class="panel-group">
                                                            <div class="panel panel-success">
                                                                    <div class="panel-heading"><label>Geräte</label></div>
                                                                    <div class="panel-body" id="devicesInDB">
                                                                    </div>
                                                            </div>                                                
                                                    </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4">
                                                    <div class="panel-group">
                                                            <div class="panel panel-success">
                                                                    <div class="panel-heading"><label>Geräteparameter</label></div>
                                                                    <div class="panel-body" id="deviceParametersInDB"></div>
                                                            </div>
                                                    </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4">
                                                    <div class="panel-group">
                                                            <div class="panel panel-success">
                                                                    <div class="panel-heading"><label>Gerätepreise</label></div>
                                                                    <div class="panel-body" id="devicePrices"></div>
                                                            </div>
                                                            <div class="panel panel-success">
                                                                    <div class="panel-heading"><label>Lieferanten</label></div>
                                                                    <div class="panel-body" id="deviceLieferanten">
                                                                    </div>
                                                            </div>
                                                    </div>
                                            </div>
                                    </div>
                            </div>
                                    -->
                                </div>

                                <!-- Modal zum Ändern eines Elements -->
                                <div class='modal fade' id='changeElementModal' role='dialog'>
                                    <div class='modal-dialog modal-md'>

                                        <!-- Modal content-->
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h4 class='modal-title'>Element ändern</h4>
                                                <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
                                            </div>
                                            <div class='modal-body' id='mbody'>
                                                <form role="form">        			        			        		
                                                    <div class="form-group">
                                                        <label for="bezeichnung">Bezeichnung:</label>
                                                        <input type="text" class="form-control" id="bezeichnung" placeholder="Type"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="kurzbeschreibung">Kurzbeschreibung:</label>
                                                        <textarea class="form-control" rows="5" id="kurzbeschreibungModal"></textarea>
                                                    </div>	        	
                                                </form>
                                            </div>
                                            <div class='modal-footer'>
                                                <input type='button' id='saveElement' class='btn btn-warning btn-sm' value='Speichern'></input>
                                                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>		

                                <script>
                                    // Tabellen formatieren
                                    $(document).ready(function () {
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
                                            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
                                        });


                                        // CLICK TABELLE ELEMENTE IN DB
                                        var table1 = $('#tableElementsInDB').DataTable();

                                        $('#tableElementsInDB tbody').on('click', 'tr', function () {

                                            if ($(this).hasClass('info')) {
                                                //$(this).removeClass('info');
                                            } else {
                                                $("#deviceParametersInDB").hide();
                                                $("#devicePrices").hide();
                                                $("#deviceLieferanten").hide();
                                                table1.$('tr.info').removeClass('info');
                                                $(this).addClass('info');
                                                var elementID = table1.row($(this)).data()[0];
                                                document.getElementById("bezeichnung").value = table1.row($(this)).data()[2];
                                                document.getElementById("kurzbeschreibungModal").value = table1.row($(this)).data()[3];

                                                $.ajax({
                                                    url: "setSessionVariables.php",
                                                    data: {"elementID": elementID},
                                                    type: "GET",
                                                    success: function (data) {
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
                                            }
                                        });
                                    });


                                    // Element Gewerk Änderung
                                    $('#elementGewerk').change(function () {
                                        var gewerkID = this.value;

                                        $.ajax({
                                            url: "getElementGroupsByGewerk.php",
                                            data: {"gewerkID": gewerkID},
                                            type: "GET",
                                            success: function (data) {
                                                $("#elementGroups").html(data);
                                            }
                                        });
                                    });

                                    //Element speichern
                                    $("#saveElement").click(function () {

                                        var bezeichnung = $("#bezeichnung").val();
                                        var kurzbeschreibung = $("#kurzbeschreibungModal").val();


                                        if (bezeichnung !== "" && kurzbeschreibung !== "") {

                                            $.ajax({
                                                url: "saveElement.php",
                                                data: {"bezeichnung": bezeichnung, "kurzbeschreibung": kurzbeschreibung},
                                                type: "GET",
                                                success: function (data) {
                                                    alert(data);
                                                    $('#changeElementModal').modal('hide');
                                                    $.ajax({
                                                        url: "getElementsInDB.php",
                                                        type: "GET",
                                                        success: function (data) {
                                                            $("#elementsInDB").html(data);
                                                        }
                                                    });
                                                }
                                            });
                                        } else {
                                            alert("Bitte alle Felder ausfüllen!");
                                        }

                                    });


                                </script>

                        </body>
            
                        </html>
