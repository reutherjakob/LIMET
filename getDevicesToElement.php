<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css"/>
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <title>Get Devices 2 Element</title>
</head>
<body>

<?php
// V2.0: 2024-11-29, Reuther & Fux
include "_utils.php";
check_login();

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_geraete.idTABELLE_Geraete, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_geraete.Kurzbeschreibung, tabelle_hersteller.idtabelle_hersteller
			FROM tabelle_geraete INNER JOIN tabelle_hersteller ON tabelle_geraete.tabelle_hersteller_idtabelle_hersteller = tabelle_hersteller.idtabelle_hersteller
			WHERE (((tabelle_geraete.TABELLE_Elemente_idTABELLE_Elemente)=".$_GET["elementID"]."))
			ORDER BY tabelle_geraete.GeraeteID;";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm' id='tableDevicesToElement' cellspacing='0' width='100%'>
	<thead><tr>
	<th>ID</th>
	<th>GeraeteID</th>
	<th>Hersteller</th>
	<th>Typ</th>
    <th>Beschreibung</th>
    <th>HerstellerID</th>
    <th></th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idTABELLE_Geraete"] . "</td>";
    echo "<td>" . $row["GeraeteID"] . "</td>";
    echo "<td>" . $row["Hersteller"] . "</td>";
    echo "<td>" . $row["Typ"] . "</td>";
    echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
    echo "<td>" . $row["idtabelle_hersteller"] . "</td>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Geraete"] . "' class='btn btn-outline-dark btn-xs' value='changeDevice' data-toggle='modal' data-target='#addDeviceModal'><i class='fas fa-pencil-alt'></i></button></td>";
    echo "</tr>";
}

echo "</tbody></table>";
echo "<input type='button' id='addDeviceModalButton' class='btn btn-success btn-sm' value='Gerät hinzufügen' data-toggle='modal' data-target='#addDeviceModal'></input>";
echo "<input type='button' id='" . $_GET["elementID"] . "' class='btn btn-default btn-sm' value='Geräte vergleichen' data-toggle='modal' data-target='#deviceComparisonModal'></input>";
?>

<!-- Modal zum Anlegen eines Gerätes -->
<div class='modal fade' id='addDeviceModal' role='dialog'>
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Gerät hinzufügen/bearbeiten</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <?php
                    $sql = "SELECT `tabelle_hersteller`.`idtabelle_hersteller`, `tabelle_hersteller`.`Hersteller`
									FROM `LIMET_RB`.`tabelle_hersteller`
									ORDER BY `tabelle_hersteller`.`Hersteller`;";


                    $result = $mysqli->query($sql);

                    echo "<div class='form-group'>
                        <label for='hersteller'>Hersteller:</label>
                        <label class='float-right'>
                            <button type='button' id='openAddManufacturer' class='btn btn-xs btn-outline-dark ' value='openAddManufacturer' data-toggle='modal' data-target='#addManufacturerModal'><i class='far fa-plus-square'></i></button>
                        </label>
                            <select class='form-control form-control-sm' id='hersteller' name='hersteller'>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value=" . $row["idtabelle_hersteller"] . ">" . $row["Hersteller"] . "</option>";
                    }
                    echo "</select></div>";

                    $mysqli->close();
                    ?>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <input type="text" class="form-control form-control-sm" id="type" placeholder="Type"/>
                    </div>
                    <div class="form-group">
                        <label for="kurzbeschreibung">Kurzbeschreibung:</label>
                        <textarea class="form-control form-control-sm" rows="5" id="kurzbeschreibung"></textarea>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addDevice' class='btn btn-success btn-sm' value='Hinzufügen'></input>
                <input type='button' id='saveDevice' class='btn btn-warning btn-sm' value='Speichern'></input>
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

    </div>
</div>


<!-- Modal zum Zeigen des Parametervergleichs -->
<div class='modal fade' id='deviceComparisonModal' role='dialog'>
    <div class='modal-dialog modal-lg'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Geräte-Vergleich</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyDeviceComparison'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Anlegen eines Herstellers -->
<div class='modal fade' id='addManufacturerModal' role='dialog'>
    <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Hersteller hinzufügen</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyAddManufacturerModal'>
                <div class="form-group">
                    <label for="manufacturer">Hersteller:</label>
                    <input type="text" class="form-control form-control-sm" id="manufacturer" placeholder="Hersteller"/>
                </div>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addManufacturer' class='btn btn-success btn-sm' value='Hinzufügen'></input>
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>


<script>
    var deviceID;
    $(document).ready(function () {
        $("#tableDevicesToElement").DataTable({
            "columnDefs": [
                {
                    "targets": [0, 5],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [6],
                    "searchable": false,
                    "sortable": false
                }
            ],
            "select": true,
            "paging": true,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "searching": false,
            "info": false,
            "order": [[1, "asc"]],

            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
        });


        var table1 = $('#tableDevicesToElement').DataTable();

        $('#tableDevicesToElement tbody').on('click', 'tr', function () {

            if ($(this).hasClass('info')) {
            } else {
                $("#deviceParametersInDB").show();
                $("#devicePrices").show();
                $("#deviceLieferanten").show();
                table1.$('tr.info').removeClass('info');
                $(this).addClass('info');
                deviceID = table1.row($(this)).data()[0];
                document.getElementById("hersteller").value = table1.row($(this)).data()[5];
                document.getElementById("type").value = table1.row($(this)).data()[3];
                document.getElementById("kurzbeschreibung").value = table1.row($(this)).data()[4];

                $.ajax({
                    url: "getStandardDeviceParameters.php",
                    data: {"deviceID": deviceID},
                    type: "GET",
                    success: function (data) {
                        $("#deviceParametersInDB").html(data);
                        $.ajax({
                            url: "getDevicePrices.php",
                            data: {"deviceID": deviceID},
                            type: "GET",
                            success: function (data) {
                                $("#devicePrices").html(data);
                                $.ajax({
                                    url: "getLieferantenToDevices.php",
                                    type: "GET",
                                    success: function (data) {
                                        $("#deviceLieferanten").html(data);
                                        $.ajax({
                                            url: "getDeviceServicePrices.php",
                                            data: {"deviceID": deviceID},
                                            type: "GET",
                                            success: function (data) {
                                                $("#deviceServicePrices").html(data);
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

    //Geraet hinzufügen
    $("#addDevice").click(function () {
        var hersteller = $("#hersteller").val();
        var type = $("#type").val();
        var kurzbeschreibung = $("#kurzbeschreibung").val();

        if (hersteller !== "" && type !== "" && kurzbeschreibung !== "") {
            $('#addDeviceModal').modal('hide');
            $.ajax({
                url: "addDevice.php",
                data: {"hersteller": hersteller, "type": type, "kurzbeschreibung": kurzbeschreibung},
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDevicesToElement.php",
                        type: "GET",
                        success: function (data) {
                            $("#devicesInDB").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });

    //Geraet speichern
    $("#saveDevice").click(function () {
        var hersteller = $("#hersteller").val();
        var type = $("#type").val();
        var kurzbeschreibung = $("#kurzbeschreibung").val();

        if (hersteller !== "" && type !== "" && kurzbeschreibung !== "") {
            $('#addDeviceModal').modal('hide');
            $.ajax({
                url: "saveDevice.php",
                data: {
                    "deviceID": deviceID,
                    "hersteller": hersteller,
                    "type": type,
                    "kurzbeschreibung": kurzbeschreibung
                },
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDevicesToElement.php",
                        type: "GET",
                        success: function (data) {
                            $("#devicesInDB").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });

    $("#addDeviceModalButton").click(function () {
        document.getElementById("type").value = "";
        document.getElementById("kurzbeschreibung").value = "";
        document.getElementById("saveDevice").style.display = "none";
        document.getElementById("addDevice").style.display = "inline";
    });

    $("button[value='changeDevice']").click(function () {
        // Buttons ein/ausblenden!
        document.getElementById("addDevice").style.display = "none";
        document.getElementById("saveDevice").style.display = "inline";
    });

    //Gerätevergleich anzeigen
    $("input[value='Geräte vergleichen']").click(function () {
        var ID = this.id;

        $.ajax({
            url: "getDeviceComparison.php",
            type: "GET",
            data: {"elementID": ID},
            success: function (data) {
                $("#mbodyDeviceComparison").html(data);
            }
        });
    });

    //Hersteller hinzufügen
    $("#addManufacturer").click(function () {
        var manufacturer = $("#manufacturer").val();
        //var elementID = <?php $_SESSION["elementID"] ?>;

        if (manufacturer !== "") {
            $('#addManufacturerModal').modal('hide');
            $('#addDeviceModal').modal('hide');
            $.ajax({
                url: "addManufacturer.php",
                data: {"manufacturer": manufacturer},
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDevicesToElement.php",
                        data: {"elementID": ""},
                        type: "GET",
                        success: function (data) {
                            $("#devicesInDB").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte Hersteller ausfüllen!");
        }
    });


</script>
</body>
</html>