<!DOCTYPE html>
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
// V3.0: 2025 Rework: Reuther & Fux
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$elementID = "0";
if (!empty($_GET["elementID"])) {
    $elementID = $_GET["elementID"];
} elseif (!empty($_SESSION["elementID"])) {
    $elementID = $_SESSION["elementID"];
}

$sql = "SELECT tabelle_geraete.idTABELLE_Geraete, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_geraete.Kurzbeschreibung, tabelle_hersteller.idtabelle_hersteller
        FROM tabelle_geraete
        INNER JOIN tabelle_hersteller ON tabelle_geraete.tabelle_hersteller_idtabelle_hersteller = tabelle_hersteller.idtabelle_hersteller
        WHERE tabelle_geraete.TABELLE_Elemente_idTABELLE_Elemente = ?
        ORDER BY tabelle_geraete.GeraeteID DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $elementID); // 'i' specifies the type as integer
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

echo "<table class='table table-striped table-sm' id='tableDevicesToElement'>
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
    echo "<td><button type='button' id='" . $row["idTABELLE_Geraete"] . "' class='btn btn-outline-dark btn-sm' value='changeDevice' data-bs-toggle='modal' data-bs-target='#addDeviceModal'><i class='fas fa-pencil-alt'></i></button></td>";
    echo "</tr>";
}

echo "</tbody></table>";
echo "<input type='button' id='addDeviceModalButton' class='btn btn-success btn-sm' value='Gerät hinzufügen' data-bs-toggle='modal' data-bs-target='#addDeviceModal'><input type='button' id='";
echo $elementID;
echo "' class='btn btn-default btn-sm' value='Geräte vergleichen' data-bs-toggle='modal' data-bs-target='#deviceComparisonModal'>";
?>


<div class='modal fade' id='addDeviceModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Gerät hinzufügen/bearbeiten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
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
                            <button type='button' id='openAddManufacturer' class='btn btn-sm btn-outline-dark ' value='openAddManufacturer' data-bs-toggle='modal' data-bs-target='#addManufacturerModal'><i class='far fa-plus-square'></i></button>
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
                <input type='button' id='addDevice' class='btn btn-success btn-sm' value='Hinzufügen'>
                <input type='button' id='saveDevice' class='btn btn-warning btn-sm' value='Speichern'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal zum Zeigen des Parametervergleichs -->
<div class='modal fade' id='deviceComparisonModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-lg'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Geräte-Vergleich</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyDeviceComparison'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Anlegen eines Herstellers -->
<div class='modal fade' id='addManufacturerModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Hersteller hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyAddManufacturerModal'>
                <div class="form-group">
                    <label for="manufacturer">Hersteller:</label>
                    <input type="text" class="form-control form-control-sm" id="manufacturer" placeholder="Hersteller"/>
                </div>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addManufacturer' class='btn btn-success btn-sm' value='Hinzufügen'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>

<!--suppress ES6ConvertVarToLetConst -->
<script charset="utf-8" type="text/javascript">

    var deviceID;

    var tableDevicesToElement;

    $(document).ready(function () {
        tableDevicesToElement = $('#tableDevicesToElement').DataTable({
            columnDefs: [
                {
                    targets: [0, 5],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [6],
                    searchable: false,
                    sortable: false
                }
            ],
            select: true,
            paging: true,
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 10,
            searching: true, // Enable searching
            info: false,
            order: [[1, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "", //'<i class="fa fa-filter" aria-hidden="true"></i>', // Custom search icon
                searchPlaceholder: 'Suche...' // Custom placeholder
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: ['search', 'paging']
            }
        });

        $('#tableDevicesToElement tbody').on('click', 'tr', function () {
            $("#deviceParametersInDB").show();
            $("#devicePrices").show();
            $("#deviceLieferanten").show();
            deviceID = tableDevicesToElement.row($(this)).data()[0];
            //console.log("GetDev2Element", deviceID);
            document.getElementById("hersteller").value = tableDevicesToElement.row($(this)).data()[5];
            document.getElementById("type").value = tableDevicesToElement.row($(this)).data()[3];
            document.getElementById("kurzbeschreibung").value = tableDevicesToElement.row($(this)).data()[4];
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

        });
    });

    //Geraet hinzufügen
    $("#addDevice").click(function () {
        let hersteller = $("#hersteller").val();
        let type = $("#type").val();
        let kurzbeschreibung = $("#kurzbeschreibung").val();

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
        let hersteller = $("#hersteller").val();
        let type = $("#type").val();
        let kurzbeschreibung = $("#kurzbeschreibung").val();

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
        let ID = this.id;
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
        let manufacturer = $("#manufacturer").val();
        //var elementID = // $_SESSION["elementID"];
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