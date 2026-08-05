<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

if (!function_exists('h')) {
    function h($s)
    {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

$mysqli = utils_connect_sql();
$deviceID = getPostInt('deviceID', 0);
if ($deviceID < 1) {
    die("Invalid device ID.");
}

$stmt = $mysqli->prepare(
    "SELECT 
        tabelle_wartungspreise.idtabelle_wartungspreise,
        tabelle_wartungspreise.Datum, 
        tabelle_wartungspreise.Info, 
        tabelle_wartungspreise.Menge, 
        tabelle_wartungspreise.Wartungsart, 
        tabelle_wartungspreise.WartungspreisProJahr, 
        tabelle_wartungspreise.Kommentar,
        tabelle_projekte.idTABELLE_Projekte   AS projectID,
        tabelle_projekte.Projektname, 
        tabelle_lieferant.idTABELLE_Lieferant AS lieferantID,
        tabelle_lieferant.Lieferant
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

echo "<table class='table table-striped table-sm' id='tableDeviceServicePrices'>
	<thead><tr>";
echo "<th>Datum</th>
		<th>Info</th>
		<th>Anzahl Geräte</th>
		<th>Wartungsart </th>
		<th>Preis/Jahr (1 Gerät)</th>
                <th>Projekt</th>
                <th>Lieferant</th>
                <th>Kommentar</th>
        <th class='' data-bs-toggle='tooltip' title='Bearbeiten'><i class='fa fa-pencil-alt'></i></th>
	</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    $servicePriceID = $row['idtabelle_wartungspreise'] ?? '';
    $date = date_create($row["Datum"]);
    $formattedDate = date_format($date, 'Y-m-d');

    echo "<tr data-serviceprice-id='" . h($servicePriceID) . "'
              data-date='" . h($formattedDate) . "'
              data-info='" . h($row["Info"]) . "'
              data-menge='" . h($row["Menge"]) . "'
              data-wartungsart='" . h($row["Wartungsart"]) . "'
              data-wartungspreis='" . h($row["WartungspreisProJahr"]) . "'
              data-kommentar='" . h($row["Kommentar"] ?? '') . "'
              data-project-id='" . h($row["projectID"] ?? '0') . "'
              data-lieferant-id='" . h($row["lieferantID"] ?? '0') . "'>";

    echo "<td>" . $formattedDate . "</td>";
    echo "<td>" . h($row["Info"] ?? '') . "</td>";
    echo "<td>" . h($row["Menge"] ?? '') . "</td>";
    echo "<td>" . ($row["Wartungsart"] === "0" ? "Betriebswartung" : "Vollwartung") . "</td>";
    echo "<td>" . sprintf('%01.2f', $row["WartungspreisProJahr"]) . "</td>";
    echo "<td>" . h($row["Projektname"] ?? '') . "</td>";
    echo "<td>" . h($row["Lieferant"] ?? '') . "</td>";
    echo "<td>" . h($row["Kommentar"] ?? '') . "</td>";

    echo "<td><button class='btn btn-sm btn-outline-dark edit-serviceprice-btn' 
                title='Wartungspreis ändern' 
                data-bs-toggle='modal'
                data-bs-target='#addServicePriceModal'>
              <i class='fa fa-pencil-alt'></i>
          </button></td>";
    echo "</tr>";
}
echo " </tbody></table>";
?>

<!-- Modal zum Anlegen/Bearbeiten eines Wartungspreises -->
<div class='modal fade' id='addServicePriceModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='modalTitleService'>Wartungspreis hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group" style="display:none;">
                        <input type="hidden" id="servicePriceID" value="0">
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="dateService">Datum:</label>
                        <input type="text" class="form-control" id="dateService" placeholder="jjjj.mm.tt"/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="infoService">Info:</label>
                        <input type="text" class="form-control" id="infoService"
                               placeholder="Verfahrensart, Anmerkung,..."/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="mengeService">Anzahl der Geräte:</label>
                        <input type="text" class="form-control" id="mengeService"/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="wartungsart">Wartungsart:</label>
                        <select class="form-control input-sm" id="wartungsart" name="wartungsart">
                            <option value="0" selected>Betriebswartung</option>
                            <option value="1">Vollwartung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="wartungspreis">Durchschnittlicher Wartungspreis für 1 Jahr:</label>
                        <input type="text" class="form-control" id="wartungspreis" placeholder="Komma ."/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="kommentarService">Optionaler Kommentar:</label>
                        <textarea class="form-control"
                                  id="kommentarService"
                                  rows="2"
                                  maxlength="255"
                                  placeholder="Preisgestaltungsrelevanter Kontext: z.B. Umfang, Bieteranzahl, etc."></textarea>
                    </div>

                    <?php
                    $sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname 
                            FROM tabelle_projekte ORDER BY tabelle_projekte.Interne_Nr;";
                    $result1 = $mysqli->query($sql);

                    echo "<div class='form-group'>
                                <label class='mt-1' for='projectService'>Projekt:</label>									
                                <select class='form-control input-sm' id='projectService' name='projectService'>
                                        <option value=0>Kein Projekt</option>";
                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Projekte"] . ">" . $row["Interne_Nr"] . "-" . $row["Projektname"] . "</option>";
                    }
                    echo "</select> </div>";

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
                        <label class='mt-1' for='lieferantService'>Lieferant:</label>									
                        <select class='form-control input-sm' id='lieferantService' name='lieferantService'>
                                <option value=0>Lieferant auswählen</option>
                                 <option value='add'>Nicht dabei? - Zu Element Hinzufügen! </option>
                                 <option value='new'>Nicht dabei? - Neu Anlegen!</option>";
                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Lieferant"] . ">" . $row["Lieferant"] . "</option>";
                    }
                    echo "</select> </div>"; ?>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addServicePrice' class='btn btn-success btn-sm' value='Speichern'
                       data-bs-dismiss='modal'>
                <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<script>
    var serviceDeviceID = <?= (int)$deviceID ?>;

    $(document).ready(function () {

        $('#WartungspreiseCardHeader').html(`
    <button type='button'
            id='addServicePriceModalButton'
            class='btn btn-sm btn-success'
            value='Wartungspreis hinzufügen'
            data-bs-toggle='modal'
            data-bs-target='#addServicePriceModal'>
        <i class='fas fa-plus'></i>
        Wartungspreis hinzufügen
    </button>
`);

        setTimeout(function () {
            $('#dateService').datepicker({
                format: "yyyy-mm-dd",
                calendarWeeks: true,
                autoclose: true,
                todayBtn: "linked",
                language: "de"
            });
        }, 200);

        // --- ADD-Modus: Modal zurücksetzen ---
        $(document).off('click', '#addServicePriceModalButton').on('click', '#addServicePriceModalButton', function () {
            $('#servicePriceID').val('0');
            $('#modalTitleService').text('Wartungspreis hinzufügen');
            $('#addServicePrice').val('Speichern');
            $('#dateService, #infoService, #mengeService, #wartungspreis, #kommentarService').val('');
            $('#wartungsart').val('0');
            $('#projectService, #lieferantService').val('0');
        });

        // --- EDIT-Modus: Modal aus Zeilendaten befüllen ---
        $(document).off('click', '.edit-serviceprice-btn').on('click', '.edit-serviceprice-btn', function () {
            const row = $(this).closest('tr');
            $('#servicePriceID').val(row.data('serviceprice-id'));
            $('#dateService').val(row.data('date'));
            $('#infoService').val(row.data('info'));
            $('#mengeService').val(row.data('menge'));
            $('#wartungsart').val(String(row.data('wartungsart')));
            $('#wartungspreis').val(row.data('wartungspreis'));
            $('#kommentarService').val(row.data('kommentar'));
            $('#projectService').val(row.data('project-id') || '0');
            $('#lieferantService').val(row.data('lieferant-id') || '0');
            $('#modalTitleService').text('Wartungspreis ändern');
            $('#addServicePrice').val('Änderungen speichern');
        });
    });

    new DataTable('#tableDeviceServicePrices', {
        paging: false,
        lengthChange: false,
        searching: false,
        info: false,
        order: [[0, 'desc']],
        columnDefs: [
            {targets: [8], orderable: false, searchable: false}
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
            decimal: ',',
            thousands: '.'
        },
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: null,
            bottomEnd: null
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

    // ENHANCED SAVE - Handles both ADD and EDIT
    $(document).off('click', '#addServicePrice').on('click', '#addServicePrice', function () {
        let servicePriceID = $('#servicePriceID').val();
        let date = $("#dateService").val();
        let info = $("#infoService").val();
        let menge = $("#mengeService").val();
        let wartungsart = $("#wartungsart").val();
        let wartungspreis = normalizeCosts($("#wartungspreis").val());
        let project = $("#projectService").val();
        let lieferant = $("#lieferantService").val();
        let kommentar = $("#kommentarService").val() || "";

        if (date === "" || info === "" || menge === "" || wartungsart === "" || wartungspreis === "" || lieferant <= 0) {
            makeToaster("Bitte alle Felder ausfüllen!", false);
            return;
        }

        let url = servicePriceID == '0' ? "addServicePriceToDevice.php" : "updateDeviceServicePrice.php";

        $.ajax({
            url: url,
            data: {
                "servicePriceID": servicePriceID,   // 0 = ADD, >0 = UPDATE
                "date": date,
                "info": info,
                "menge": menge,
                "wartungsart": wartungsart,
                "wartungspreis": wartungspreis,
                "project": project,
                "lieferant": lieferant,
                "kommentar": kommentar,
                "deviceID": serviceDeviceID
            },
            type: "POST",
            success: function (data) {
                makeToaster(data.trim(), true);
                $.ajax({
                    url: "getDeviceServicePrices.php",
                    data: {"deviceID": serviceDeviceID},
                    type: "POST",
                    success: function (data) {
                        $("#deviceServicePrices").html(data);
                    }
                });
            },
            error: function () {
                makeToaster("Fehler beim Speichern!", false);
            }
        });
    });
</script>