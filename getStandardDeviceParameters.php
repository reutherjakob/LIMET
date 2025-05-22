<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>getStandardDev</title>
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
echo "<button type='button' id='" . $deviceID . "' class='btn btn-outline-success btn-sm' value='ParameterOvertakeBtn' data-bs-toggle='modal' data-bs-target='#parameterOvertakeModal'><span class='glyphicon glyphicon-open-file'></span> Parameter übernehmen</button>";
echo "<button type='button' id='" . $deviceID . "_bearbeiten ' class='btn btn-outline-warning btn-sm' value='ParameterBearbeiten' data-bs-toggle='modal' data-bs-target='#changeDeviceParameters'><span class='glyphicon glyphicon-open-file'></span> Parameter ändern</button>";
?>

<div class='modal' id='parameterOvertakeModal' role='dialog' aria-hidden="true" tabindex='-1'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Parameter übernehmen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyOvertake'>
                <!-- Dynamisch gefüllt -->
            </div>
            <div class='modal-footer'>
                <input type='button' id='saveParametersFromDevice' class='btn btn-success btn-sm' value='Ja' data-bs-dismiss='modal'>
                <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Nein</button>
            </div>
        </div>
    </div>
</div>




<!-- Modal zum Ändern der Parameter -->
<div class='modal fade' id='changeDeviceParameters' role='dialog'>
    <div class='modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'> Geräteparameter bearbeiten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="row">
                        <div class='col-xxl-12'>
                            <div class='mt-1 card' id="GeräteparameterCard">
                                <div class='card-header d-flex justify-content-between align-items-center'
                                     id="CardHeaderGeräteParameter"><label>Geräteparameter</label>
                                </div>
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
                                        echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-outline-danger btn-sm' value='deleteDEVICEParameter'><i class='fas fa-minus'></i></button></td>";
                                        echo "<td>" . $row["Kategorie"] . "</td>";
                                        echo "<td>" . $row["Bezeichnung"] . "</td>";
                                        echo "<td><input type='text' id='wert" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Wert"] . "'></td>";
                                        echo "<td><input type='text' id='einheit" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Einheit"] . "'></td>";
                                        echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-warning btn-sm' value='saveDEVICEParameter'><i class='far fa-save'></i></button></td>";
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
                                <div class='card-header'>
                                    <div class="row">
                                        <div class="col-10 d-flex align-items-center">Mögliche Geräteparameter</div>
                                        <div class="col-2 d-flex align-items-center justify-content-end" id='possibleDeviceParametersCH'></div>
                                    </div>
                                </div>
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
                                        echo "<td><button type='button' id='" . $row["idTABELLE_Parameter"] . "' class='btn btn-outline-success btn-sm' value='addDEVICEParameter'><i class='fas fa-plus'></i></button></td>";
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

<script src="_utils.js"></script>
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
        },
        initComplete: function () {
            $('#CardHeaderGeräteParameter .xxx').remove();
            $('#GeräteparameterCard .dt-search label').remove();
            $('#GeräteparameterCard .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx float-right").appendTo('#CardHeaderGeräteParameter');
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
            topEnd: 'search',
            bottomStart: null,
            bottomEnd: null
        },
        initComplete: function () {
            $('#possibleDeviceParametersCH .xxx').remove();
            $('#possibleDeviceParameters .dt-search label').remove();
            $('#possibleDeviceParameters .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx float-right").appendTo('#possibleDeviceParametersCH');
        }

    });

    //Parameter zu Gerät hinzufügen
    $("button[value='addDEVICEParameter']").click(function () {
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "addParameterToDevice.php",
                data: {"parameterID": id},
                type: "GET",
                success: function (data) {
                    makeToaster(data, data.substring(0, 4) === "Para");
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
    $("button[value='deleteDEVICEParameter']").click(function () {
        if (confirm("Parameter wirklich löschen?")) {
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
        }
    });

    //Parameter ändern
    $("button[value='saveDEVICEParameter']").click(function () { //TODO find and rename btn
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
                makeToaster(data, data.substring(0, 4) === "Vari");
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

    $("button[value='ParameterOvertakeBtn']").click(function() {
        console.log("ParameterOvertakeBtn");
        $.ajax({
            url: 'getModalDataFromSession.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('#mbodyOvertake').html(
                    '<strong>Gerät:</strong> ' + data.deviceTyp + '<br>' +
                    '<strong>Element:</strong> ' + data.elementBezeichnung + '<br><br>' +
                    'Wollen Sie diese Geräteparameter für Ihr Element im Projekt übernehmen?<br>' +
                    'Alle (projektspezifisch) gespeicherten Variantenparameter gehen verloren!'
                );
                $('#parameterOvertakeModal').modal('show');
            },
            error: function( ) {
                $('#mbodyOvertake').html('<span class="text-danger">Fehler beim Laden der Daten.</span>');
                $('#parameterOvertakeModal').modal('show');
            }
        });
    });

</script>
</body>
</html>