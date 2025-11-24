<?php
include 'utils/_utils.php';
init_page_serversides("x");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <title>LIMET - Raumbuch - Elemente im Raum</title>
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
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">

    <div class="card  ">
        <div class="card-header"><label>Elemente</label></div>
        <div class="card-body" id="cadElements">
            <div class="col-md-12 col-sm-12">
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
            paging: true,
            ordering: [[0, "asc"]],
            pagingType: "simple",
            lengthChange: false,
            pageLength: 15,
            language: {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
        });

    });


    // Element speichern
    $("input[value='Speichern']").click(function () {
        let id = this.id;
        let selectCAD_notwendig = $("#selectCAD_notwendig" + id).val();
        let selectCAD_dwg_vorhanden = $("#selectCAD_dwg_vorhanden" + id).val();
        let selectCAD_dwg_kontrolliert = $("#selectCAD_dwg_kontrolliert" + id).val();
        let selectCAD_familie_vorhanden = $("#selectCAD_familie_vorhanden" + id).val();
        let selectCAD_familie_kontrolliert = $("#selectCAD_familie_kontrolliert" + id).val();
        let CADcomment = $("#CADcomment" + id).val();

        $.ajax({
            url: "saveCADElement.php",
            data: {
                "id": id,
                "selectCAD_notwendig": selectCAD_notwendig,
                "selectCAD_dwg_vorhanden": selectCAD_dwg_vorhanden,
                "selectCAD_dwg_kontrolliert": selectCAD_dwg_kontrolliert,
                "selectCAD_familie_vorhanden": selectCAD_familie_vorhanden,
                "selectCAD_familie_kontrolliert": selectCAD_familie_kontrolliert,
                "CADcomment": CADcomment
            },
            type: "POST",
            success: function (data) {
                alert(data);
            }
        });

    });

    //Filter DWG vorhanden geändert
    $('#filterCAD_dwg_vorhanden').change(function () {
        let filterValueDWGVorhanden = this.value;
        let filterValueDWGNotwendig = $('#filter_dwg_notwendig').val();
        let filterValueFamilieVorhanden = $('#filterCAD_familie_vorhanden').val();
        $.ajax({
            url: "getElementsCADFiltered.php",
            data: {
                "filterValueDWGNotwendig": filterValueDWGNotwendig,
                "filterValueDWGVorhanden": filterValueDWGVorhanden,
                "filterValueFamilieVorhanden": filterValueFamilieVorhanden
            },
            type: "POST",
            success: function (data) {
                $("#cadElements").html(data);
            }
        });
    });

    //Filter DWG notwendig geändert
    $('#filter_dwg_notwendig').change(function () {
        let filterValueDWGVorhanden = $('#filterCAD_dwg_vorhanden').val();
        let filterValueDWGNotwendig = this.value;
        let filterValueFamilieVorhanden = $('#filterCAD_familie_vorhanden').val();
        $.ajax({
            url: "getElementsCADFiltered.php",
            data: {
                "filterValueDWGNotwendig": filterValueDWGNotwendig,
                "filterValueDWGVorhanden": filterValueDWGVorhanden,
                "filterValueFamilieVorhanden": filterValueFamilieVorhanden
            },
            type: "POST",
            success: function (data) {
                $("#cadElements").html(data);
            }
        });
    });

    //Filter Familie vorhanden geändert
    $('#filterCAD_familie_vorhanden').change(function () {
        let filterValueDWGVorhanden = $('#filterCAD_dwg_vorhanden').val();
        let filterValueDWGNotwendig = $('#filter_dwg_notwendig').val();
        let filterValueFamilieVorhanden = this.value;
        $.ajax({
            url: "getElementsCADFiltered.php",
            data: {
                "filterValueDWGNotwendig": filterValueDWGNotwendig,
                "filterValueDWGVorhanden": filterValueDWGVorhanden,
                "filterValueFamilieVorhanden": filterValueFamilieVorhanden
            },
            type: "POST",
            success: function (data) {
                $("#cadElements").html(data);
            }
        });
    });


</script>
</body>
</html>

