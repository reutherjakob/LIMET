<?php
session_start();
include '_utils.php';
init_page_serversides();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <title>LIMET - Raumbuch - Elemente im Raum</title>
        <link rel="icon" href="iphone_favicon.png"></link>
        
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs-3.3.7/jq-3.2.1/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-flash-1.5.1/b-html5-1.5.1/r-2.2.1/datatables.css"/> 
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jq-3.2.1/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-flash-1.5.1/b-html5-1.5.1/r-2.2.1/datatables.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <!--        <style>
            .navbar-brand {
                padding: 0px;
            }
            .navbar-brand>img {
                height: 100%;
                width: auto;
            }

        </style>-->


    </head>

    <body style="height:100%">

        <div class="container-fluid">
            ´<div id="limet-navbar"></div> <!-- Container für Navbar -->
            <div class="panel panel-default">
                <div class="panel-heading"><label>Elemente</label></div>
                <div class="panel-body" id="cadElements">
                    <div class="col-md-12 col-sm-12" >
<?php
$mysqli = utils_connect_sql();

// Abfrage aller Räume im Projekt
$sql = "SELECT `tabelle_elemente`.`idTABELLE_Elemente`,
							    `tabelle_elemente`.`Bezeichnung`,
							    `tabelle_elemente`.`ElementID`,
							    `tabelle_elemente`.`Kurzbeschreibung`,
							    `tabelle_elemente`.`CAD_notwendig`,
							    `tabelle_elemente`.`CAD_dwg_vorhanden`,
							    `tabelle_elemente`.`CAD_dwg_kontrolliert`,
							    `tabelle_elemente`.`CAD_familie_vorhanden`,
							    `tabelle_elemente`.`CAD_familie_kontrolliert`,
							    `tabelle_elemente`.`CAD_Kommentar`
							FROM `LIMET_RB`.`tabelle_elemente` 
							ORDER BY `tabelle_elemente`.`ElementID`;";

$result = $mysqli->query($sql);

//echo "<table class='table table-striped' id='tableElements'>
echo "<table id='tableElements' class='table table-striped table-bordered table-condensed' cellspacing='0' width='100%'>
						<thead><tr>
						<th>ID</th>
						<th>Element</th>
						<th>Beschreibung</th>
						<th>CAD Notwendigkeit
						<select class='form-control input-sm' id='filter_dwg_notwendig'>
							<option selected value='2'></option>
							<option value='0'>Nein</option>
							<option value='1'>Ja</option>		
						</select>
						</th>
						<th>DWG vorhanden
						<select class='form-control input-sm' id='filterCAD_dwg_vorhanden'>
							<option selected value='2'></option>
							<option value='0'>Nein</option>
							<option value='1'>Ja</option>		
						</select></th>
						<th>DWG geprüft</th>
						<th>Familie vorhanden
						<select class='form-control input-sm' id='filterCAD_familie_vorhanden'>
							<option selected value='2'></option>
							<option value='0'>Nein</option>
							<option value='1'>Ja</option>		
						</select></th>
						<th>Familie geprüft</th>
						<th>CAD Kommentar</th>
						<th>Speichern</th>
						</tr></thead>
						<tfoot><tr>
						<th>ID</th>
						<th>Element</th>
						<th>Beschreibung</th>
						<th>CAD Notwendigkeit</th>
						<th>DWG vorhanden</th>
						<th>DWG geprüft</th>
						<th>Familie vorhanden</th>
						<th>Familie geprüft</th>
						<th>CAD Kommentar</th>
						<th>Speichern</th>
						</tr></tfoot><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
    echo "<td><select class='form-control input-sm' id='selectCAD_notwendig" . $row["idTABELLE_Elemente"] . "'>";
    if ($row["CAD_notwendig"] == 0) {
        echo "
									<option selected>Nein</option>
									<option>Ja</option>		
									";
    } else {
        echo "
									<option>Nein</option>
									<option selected>Ja</option>		
									";
    }
    echo "</select></td>";
    echo "<td><select class='form-control input-sm' id='selectCAD_dwg_vorhanden" . $row["idTABELLE_Elemente"] . "'>";
    if ($row["CAD_dwg_vorhanden"] == 0) {
        echo "
									<option selected>Nein</option>
									<option>Ja</option>		
									";
    } else {
        echo "
									<option>Nein</option>
									<option selected>Ja</option>		
									";
    }
    echo "</select></td>";
    echo "<td><select class='form-control input-sm' id='selectCAD_dwg_kontrolliert" . $row["idTABELLE_Elemente"] . "'>";
    if ($row["CAD_dwg_kontrolliert"] == 0) {
        echo "
									<option selected>Nicht geprüft</option>
									<option>Freigegeben</option>
									<option>Überarbeiten</option>	
									";
    }
    if ($row["CAD_dwg_kontrolliert"] == 1) {
        echo "
									<option>Nicht geprüft</option>
									<option selected>Freigegeben</option>
									<option>Überarbeiten</option>		
									";
    }
    if ($row["CAD_dwg_kontrolliert"] == 2) {
        echo "
									<option>Nicht geprüft</option>
									<option>Freigegeben</option>
									<option selected>Überarbeiten</option>		
									";
    }

    echo "</select></td>";

    echo "<td><select class='form-control input-sm' id='selectCAD_familie_vorhanden" . $row["idTABELLE_Elemente"] . "'>";
    if ($row["CAD_familie_vorhanden"] == 0) {
        echo "
									<option selected>Nein</option>
									<option>Ja</option>		
									";
    } else {
        echo "
									<option>Nein</option>
									<option selected>Ja</option>		
									";
    }
    echo "</select></td>";

    echo "<td><select class='form-control input-sm' id='selectCAD_familie_kontrolliert" . $row["idTABELLE_Elemente"] . "'>";
    if ($row["CAD_familie_kontrolliert"] == 0) {
        echo "
									<option selected>Nicht geprüft</option>
									<option>Freigegeben</option>
									<option>Überarbeiten</option>	
									";
    }
    if ($row["CAD_familie_kontrolliert"] == 1) {
        echo "
									<option>Nicht geprüft</option>
									<option selected>Freigegeben</option>
									<option>Überarbeiten</option>		
									";
    }
    if ($row["CAD_familie_kontrolliert"] == 2) {
        echo "
									<option>Nicht geprüft</option>
									<option>Freigegeben</option>
									<option selected>Überarbeiten</option>		
									";
    }
    echo "</select></td>";
    echo "<td><textarea id='CADcomment" . $row["idTABELLE_Elemente"] . "' class='form-control' style='width: 100%; height: 100%;'>" . $row["CAD_Kommentar"] . "</textarea></td>";
    echo "<td><input type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-warning btn-sm' value='Speichern'></td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>	
                    </div>
                </div>	
            </div>
        </div>

        <script>
            // Tabelle formatieren
            $(document).ready(function () {
                $('#tableElements').DataTable({
                    "paging": true,
                    "ordering": [[0, "asc"]],
                    "pagingType": "simple",
                    "lengthChange": false,
                    "pageLength": 15,
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
                });

            });


            // Element speichern
            $("input[value='Speichern']").click(function () {
                var id = this.id;
                var selectCAD_notwendig = $("#selectCAD_notwendig" + id).val();
                var selectCAD_dwg_vorhanden = $("#selectCAD_dwg_vorhanden" + id).val();
                var selectCAD_dwg_kontrolliert = $("#selectCAD_dwg_kontrolliert" + id).val();
                var selectCAD_familie_vorhanden = $("#selectCAD_familie_vorhanden" + id).val();
                var selectCAD_familie_kontrolliert = $("#selectCAD_familie_kontrolliert" + id).val();
                var CADcomment = $("#CADcomment" + id).val();

                $.ajax({
                    url: "saveCADElement.php",
                    data: {"id": id, "selectCAD_notwendig": selectCAD_notwendig, "selectCAD_dwg_vorhanden": selectCAD_dwg_vorhanden, "selectCAD_dwg_kontrolliert": selectCAD_dwg_kontrolliert, "selectCAD_familie_vorhanden": selectCAD_familie_vorhanden, "selectCAD_familie_kontrolliert": selectCAD_familie_kontrolliert, "CADcomment": CADcomment},
                    type: "GET",
                    success: function (data) {
                        alert(data);
                    }
                });

            });

            //Filter DWG vorhanden geändert
            $('#filterCAD_dwg_vorhanden').change(function () {
                var filterValueDWGVorhanden = this.value;
                var filterValueDWGNotwendig = $('#filter_dwg_notwendig').val();
                var filterValueFamilieVorhanden = $('#filterCAD_familie_vorhanden').val();
                $.ajax({
                    url: "getElementsCADFiltered.php",
                    data: {"filterValueDWGNotwendig": filterValueDWGNotwendig, "filterValueDWGVorhanden": filterValueDWGVorhanden, "filterValueFamilieVorhanden": filterValueFamilieVorhanden},
                    type: "GET",
                    success: function (data) {
                        $("#cadElements").html(data);
                    }
                });
            });

            //Filter DWG notwendig geändert
            $('#filter_dwg_notwendig').change(function () {
                var filterValueDWGVorhanden = $('#filterCAD_dwg_vorhanden').val();
                var filterValueDWGNotwendig = this.value;
                var filterValueFamilieVorhanden = $('#filterCAD_familie_vorhanden').val();
                $.ajax({
                    url: "getElementsCADFiltered.php",
                    data: {"filterValueDWGNotwendig": filterValueDWGNotwendig, "filterValueDWGVorhanden": filterValueDWGVorhanden, "filterValueFamilieVorhanden": filterValueFamilieVorhanden},
                    type: "GET",
                    success: function (data) {
                        $("#cadElements").html(data);
                    }
                });
            });

            //Filter Familie vorhanden geändert
            $('#filterCAD_familie_vorhanden').change(function () {
                var filterValueDWGVorhanden = $('#filterCAD_dwg_vorhanden').val();
                var filterValueDWGNotwendig = $('#filter_dwg_notwendig').val();
                var filterValueFamilieVorhanden = this.value;
                $.ajax({
                    url: "getElementsCADFiltered.php",
                    data: {"filterValueDWGNotwendig": filterValueDWGNotwendig, "filterValueDWGVorhanden": filterValueDWGVorhanden, "filterValueFamilieVorhanden": filterValueFamilieVorhanden},
                    type: "GET",
                    success: function (data) {
                        $("#cadElements").html(data);
                    }
                });
            });



        </script>


    </body>

</html>
