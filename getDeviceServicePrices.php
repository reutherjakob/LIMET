<?php
// V2.0
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$deviceID = getPostInt('deviceID', 0);

if ($deviceID < 1) {
    die("Invalid device ID.");
}
$stmt = $mysqli->prepare(
    "SELECT 
        tabelle_wartungspreise.Datum, 
        tabelle_wartungspreise.Info, 
        tabelle_wartungspreise.Menge, 
        tabelle_wartungspreise.Wartungsart, 
        tabelle_wartungspreise.WartungspreisProJahr, 
        tabelle_projekte.Projektname, 
        tabelle_lieferant.Lieferant, 
        tabelle_wartungspreise.idtabelle_wartungspreise
     FROM 
        tabelle_wartungspreise
     LEFT JOIN 
        tabelle_lieferant ON tabelle_wartungspreise.tabelle_lieferant_idTABELLE_Lieferant = tabelle_lieferant.idTABELLE_Lieferant
     LEFT JOIN 
        tabelle_projekte ON tabelle_wartungspreise.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
     WHERE 
        tabelle_wartungspreise.tabelle_geraete_idTABELLE_Geraete = ?"
);

$stmt->bind_param('i', $deviceID);
$stmt->execute();

$result = $stmt->get_result();


echo "<table class='table table-striped table-sm' id='tableDeviceServicePrices'  >
	<thead><tr>";
echo "<th>Datum</th>
		<th>Info</th>
		<th>Menge</th>
		<th>Wartungsart</th>
		<th>Preis/Jahr</th>
                <th>Projekt</th>
                <th>Lieferant</th>
	</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    $date = date_create($row["Datum"]);
    echo "<td>" . date_format($date, 'Y-m-d') . "</td>";
    echo "<td>" . $row["Info"] . "</td>";
    echo "<td>" . $row["Menge"] . "</td>";
    if ($row["Wartungsart"] === "0") {
        echo "<td>Betriebswartung</td>";
    } else {
        echo "<td>Vollwartung</td>";
    }
    echo "<td>" . sprintf('%01.2f', $row["WartungspreisProJahr"]) . "</td>";
    echo "<td>" . $row["Projektname"] . "</td>";
    echo "<td>" . $row["Lieferant"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
echo "<input type='button' id='addServicePriceModalButton' class='btn btn-success btn-sm' value='Wartungspreis hinzufügen' data-bs-toggle='modal' data-bs-target='#addServicePriceModal'></input>";
?>

<!-- Modal zum Anlegen eines Preises -->
<div class='modal fade' id='addServicePriceModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Wartungspreis hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="dateService">Datum:</label>
                        <input type="text" class="form-control" id="dateService" placeholder="jjjj.mm.tt"/>
                    </div>
                    <div class="form-group">
                        <label for="infoService">Info:</label>
                        <input type="text" class="form-control" id="infoService"
                               placeholder="Verfahrensart, Anmerkung,..."/>
                    </div>
                    <div class="form-group">
                        <label for="mengeService">Menge:</label>
                        <input type="text" class="form-control" id="mengeService"/>
                    </div>
                    <div class="form-group">
                        <label for="wartungsart">Wartungsart:</label>
                        <select class="form-control input-sm" id="wartungsart" name="wartungsart">
                            <option value="0" selected>Betriebswartung</option>
                            <option value="1">Vollwartung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="wartungspreis">Durchschnittlicher Wartungspreis für 1 Jahr:</label>
                        <input type="text" class="form-control" id="wartungspreis" placeholder="Komma ."/>
                    </div>

                    <?php
                    $sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname"
                        . " FROM tabelle_projekte ORDER BY tabelle_projekte.Interne_Nr;";

                    $result1 = $mysqli->query($sql);

                    echo "<div class='form-group'>
                                                    <label for='projectService'>Projekt:</label>									
                                                    <select class='form-control input-sm' id='projectService' name='projectService'>
                                                            <option value=0>Kein Projekt</option>";
                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Projekte"] . ">" . $row["Interne_Nr"] . "-" . $row["Projektname"] . "</option>";
                    }
                    echo "</select>										
                                                </div>";


                    $stmt = $mysqli->prepare(
                        "SELECT tabelle_lieferant.Lieferant, tabelle_lieferant.idTABELLE_Lieferant
                                     FROM tabelle_lieferant
                                     INNER JOIN tabelle_geraete_has_tabelle_lieferant
                                       ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_geraete_has_tabelle_lieferant.tabelle_lieferant_idTABELLE_Lieferant
                                     WHERE tabelle_geraete_has_tabelle_lieferant.tabelle_geraete_idTABELLE_Geraete = ?");

                    $stmt->bind_param("i", $deviceID);
                    $stmt->execute();

                    $result1 = $stmt->get_result();

                    $stmt->close();
                    $mysqli->close();
                    echo "<div class='form-group'>
                                                    <label for='lieferantService'>Lieferant:</label>									
                                                    <select class='form-control input-sm' id='lieferantService' name='lieferantService'>
                                                            <option value=0>Lieferant auswählen</option>
                                                             <option value='add'>Nicht dabei? - Zu Element Hinzufügen! </option>
                                                             <option value='new'>Nicht dabei? - Neu Anlegen!</option>";
                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Lieferant"] . ">" . $row["Lieferant"] . "</option>";
                    }
                    echo "</select>										
                                                </div>";

                    ?>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addServicePrice' class='btn btn-success btn-sm' value='Speichern'
                       data-bs-dismiss='modal'></input>
                <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function () {
        setTimeout(function () {
            $('#dateService').datepicker({
                format: "yyyy-mm-dd",
                calendarWeeks: true,
                autoclose: true,
                todayBtn: "linked",
                language: "de"
            });
        }, 500);
    });

    new DataTable('#tableDeviceServicePrices', {
        paging: true,
        pagingType: 'simple',
        lengthChange: false,
        searching: false,
        info: false,
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
            decimal: ',',
            thousands: '.'
        },
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: null,
            bottomEnd: 'paging'
        }
    });


    document.getElementById('lieferantService').addEventListener('change', function () {
        if (this.value === 'new') {
            window.location.href = 'firmenkontakte.php';
        }
        if (this.value === 'add') {
            $('#addServicePriceModal').modal("hide");
            $('#addLieferantModal').modal('toggle');
        }
    });


    $("#addServicePrice").click(function () {//Wartungspreis zu Geraet hinzufügen
        let date = $("#dateService").val();
        let info = $("#infoService").val();
        let menge = $("#mengeService").val();
        let wartungsart = $("#wartungsart").val();
        let wartungspreis = $("#wartungspreis").val();
        let project = $("#projectService").val();
        let lieferant = $("#lieferantService").val();

        if (date !== "" && info !== "" && menge !== "" && wartungsart !== "" && wartungspreis !== "" && lieferant > 0) {
            $.ajax({
                url: "addServicePriceToDevice.php",
                data: {
                    "date": date,
                    "info": info,
                    "menge": menge,
                    "wartungsart": wartungsart,
                    "wartungspreis": wartungspreis,
                    "project": project,
                    "lieferant": lieferant
                },
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDeviceServicePrices.php",
                        type: "POST",
                        success: function (data) {
                            $("#deviceServicePrices").html(data);
                        }
                    });
                }
            });

        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });


</script>
