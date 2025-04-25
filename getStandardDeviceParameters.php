<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title>
</head>
<body>

<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();


$deviceID = 0;
if (isset($_GET["deviceID"]) && $_GET["deviceID"] != "") {
    $deviceID = $_GET["deviceID"];
}

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_geraete_has_tabelle_parameter.Wert, tabelle_geraete_has_tabelle_parameter.Einheit, tabelle_parameter_kategorie.Kategorie
                FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_geraete_has_tabelle_parameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                WHERE (((tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete)=" . $deviceID . "));";
$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm' id='tableStandardDeviceParameters'  >
	<thead><tr>
        <th>Kategorie</th>
	<th>Parameter</th>
	<th>Wert</th>
	<th>Einheit</th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Kategorie"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Wert"] . "</td>";
    echo "<td>" . $row["Einheit"] . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";
echo "<button type='button' id='" . $deviceID . "' class='btn btn-outline-success btn-sm' value='ParameterOvertake' data-bs-toggle='modal' data-bs-target='#parameterOvertakeModal'><span class='glyphicon glyphicon-open-file'></span> Parameter übernehmen</button>";
?>
<!-- Modal zum Übernehmen der Geräteparameter -->
<div class='modal fade' id='parameterOvertakeModal' role='dialog'>
    <div class='modal-dialog modal-sm'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Parameter übernehmen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>Wollen Sie diese Geräteparameter für ihr Element im Projekt übernehmen?
                <br> Alle (projektspezifisch) gespeicherten Variantenparameter gehen verloren?
            </div>
            <div class='modal-footer'>
                <input type='button' id='saveParametersFromDevice' class='btn btn-success btn-sm' value='Ja'
                       data-bs-dismiss='modal'></input>
                <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Nein</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal zum Ändern der Parameter -->
<div class='modal fade' id='changeDeviceParameters' role='dialog'>
    <div class='modal-dialog modal-lg'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Parameter bearbeiten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="row">
                        <div class='col-xxl-12'>
                            <div class='mt-1 card'>
                                <div class='card-header'><label>Geräteparameter</label></div>
                                <div class='card-body' id='deviceParameters'>
                                    <?php
                                    $sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung, tabelle_geraete_has_tabelle_parameter.Wert, tabelle_geraete_has_tabelle_parameter.Einheit, tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter, tabelle_geraete_has_tabelle_parameter.tabelle_parameter_idTABELLE_Parameter
                                                    FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_geraete_has_tabelle_parameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                                                    WHERE (((tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete)=" . $deviceID . "));";
                                    $result = $mysqli->query($sql);

                                    echo "<table class='table table-striped table-sm' id='tableDeviceParameters'  >
                                            <thead><tr>
                                            <th>ID</th>
                                            <th>Kategorie</th>
                                            <th>Parameter</th>
                                            <th>Wert</th>
                                            <th>Einheit</th>
                                            <th></th>
                                            </tr></thead>
                                            <tbody>";

                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-outline-danger btn-sm' value='deleteParameter'><i class='fas fa-minus'></i></button></td>";
                                        echo "<td>" . $row["Kategorie"] . "</td>";
                                        echo "<td>" . $row["Bezeichnung"] . "</td>";
                                        echo "<td><input type='text' id='wert" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Wert"] . "'></input></td>";
                                        echo "<td><input type='text' id='einheit" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Einheit"] . "'></input></td>";
                                        echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-warning btn-sm' value='saveParameter'><i class='far fa-save'></i></button></td>";
                                        echo "</tr>";

                                    }
                                    echo "</tbody></table>";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr></hr>
                    <div class="row">
                        <div class='col-xxl-12'>
                            <div class='mt-1 card'>
                                <div class='card-header'><label>Mögliche Geräteparameter</label></div>
                                <div class='card-body' id='possibleDeviceParameters'>
                                    <?php
                                    $sql = "SELECT tabelle_parameter.idTABELLE_Parameter, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie 
			  					FROM tabelle_parameter, tabelle_parameter_kategorie 
			  					WHERE tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie 
								AND tabelle_parameter.idTABELLE_Parameter NOT IN 
								(SELECT tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter
                                                                FROM tabelle_geraete_has_tabelle_parameter
                                                                WHERE (((tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete)=" . $deviceID . "))) 
								ORDER BY tabelle_parameter_kategorie.Kategorie;";

                                    $result = $mysqli->query($sql);

                                    echo "<table class='table table-striped table-sm' id='tablePossibleDeviceParameters'  >
						<thead><tr>
						<th>ID</th>
						<th>Kategorie</th>
						<th>Parameter</th>
						</tr></thead>
						<tbody>";

                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td><button type='button' id='" . $row["idTABELLE_Parameter"] . "' class='btn btn-outline-success btn-sm' value='addParameter'><i class='fas fa-plus'></i></button></td>";
                                        echo "<td>" . $row["Kategorie"] . "</td>";
                                        echo "<td>" . $row["Bezeichnung"] . "</td>";
                                        echo "</tr>";

                                    }

                                    echo "</tbody></table>";
                                    $mysqli->close();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' value='closeModal'>Schließen</button>
            </div>
        </div>
    </div>
</div>


<script>
    deviceID = '<?php echo $deviceID; ?>';
    new DataTable('#tableStandardDeviceParameters', {
        paging: false,
        searching: false,
        info: false,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
        },
        scrollY: '20vh',
        scrollCollapse: true,
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: null,
            bottomEnd: null
        }
    });


    new DataTable('#tableDeviceParameters', {
        paging: false,
        searching: true,
        info: false,
        order: [[1, "asc"]],
        columnDefs: [
            {
                targets: [0],
                visible: true,
                searchable: false
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
        },
        scrollY: '20vh',
        scrollCollapse: true,
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: null,
            bottomEnd: 'search'
        }
    });

    new DataTable('#tablePossibleDeviceParameters', {
        paging: false,
        searching: true,
        info: false,
        order: [[1, "asc"]],
        columnDefs: [
            {
                targets: [0],
                visible: true,
                searchable: false
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
        },
        scrollY: '20vh',
        scrollCollapse: true,
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: null,
            bottomEnd: 'search'
        }
    });

    //Parameter zu Gerät hinzufügen
    $("button[value='addParameter']").click(function () {
        $('#variantenParameterCh .xxx').remove();
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "addParameterToDevice.php",
                data: {"parameterID": id},
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDeviceParameters.php",
                        type: "GET",
                        success: function (data) {
                            $("#deviceParameters").html(data);
                            $.ajax({
                                url: "getPossibleDeviceParameters.php",
                                type: "GET",
                                success: function (data) {
                                    $("#possibleDeviceParameters").html(data);
                                }
                            });

                        }
                    });
                }
            });
        }
    });

    //Parameter von Gerät entfernen
    $("button[value='deleteParameter']").click(function () {
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "deleteParameterFromDevice.php",
                data: {"parameterID": id},
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDeviceParameters.php",
                        type: "GET",
                        success: function (data) {
                            $("#deviceParameters").html(data);
                            $.ajax({
                                url: "getPossibleDeviceParameters.php",
                                type: "GET",
                                success: function (data) {
                                    $("#possibleDeviceParameters").html(data);
                                }
                            });
                        }
                    });
                }
            });

        } else {
            alert("Fehler beim Löschen des Parameters!");
        }
    });

    //Parameter ändern
    $("button[value='saveParameter']").click(function () {
        let id = this.id;
        let wert = $("#wert" + id).val();
        let einheit = $("#einheit" + id).val();
        if (id !== "") {
            $.ajax({
                url: "updateDeviceParameter.php",
                data: {"parameterID": id, "wert": wert, "einheit": einheit},
                type: "GET",
                success: function (data) {
                    alert(data);
                }
            });
        }
    });

    //Modal schließen und Parameter aktualisieren
    $("button[value='closeModal']").click(function () {
        $.ajax({
            url: "getStandardDeviceParameters.php",
            type: "GET",

            success: function (data) {
                $("#deviceParametersInDB").html(data);
                $('#changeDeviceParameters').modal('hide');
                $('.modal-backdrop').remove();
            }
        });
    });

    $("#saveParametersFromDevice").click(function () {
        $.ajax({
            url: "addDeviceParametersToVariante.php",
            type: "GET",
            success: function (data) {
                alert(data);
                $.ajax({
                    url: "getElementVariante.php",
                    type: "GET",
                    success: function (data) {
                        $("#elementVarianten").html(data);
                    }
                });

            }
        });
    });
</script>
</body>
</html>