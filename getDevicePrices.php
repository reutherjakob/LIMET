<?php
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
check_login();
$mysqli = utils_connect_sql();
$deviceID = getPostInt('deviceID', 0);


$sql = "SELECT tabelle_preise.Datum,
       tabelle_preise.idTABELLE_Preise,
               tabelle_preise.Quelle,
               tabelle_preise.Menge,
               tabelle_preise.Preis,
               tabelle_preise.Nebenkosten,
               tabelle_preise.Kommentar,
               tabelle_projekte.idTABELLE_Projekte AS projectID,
               tabelle_projekte.Interne_Nr,
               tabelle_projekte.Projektname,
               tabelle_lieferant.idTABELLE_Lieferant AS lieferantID,
               tabelle_lieferant.Lieferant
        FROM tabelle_lieferant
        RIGHT JOIN (tabelle_preise LEFT JOIN tabelle_projekte
                   ON tabelle_preise.TABELLE_Projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte)
                  ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_preise.tabelle_lieferant_idTABELLE_Lieferant
        WHERE tabelle_preise.TABELLE_Geraete_idTABELLE_Geraete = ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    exit("Prepare failed: " . $mysqli->error);
}
$stmt->bind_param("i", $deviceID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-sm' id='tableDevicePrices'>
    <thead><tr>";
echo "
       <th>Datum</th>
       <th>Verfahren</th>
       <th>Menge</th>
       <th>EP</th>
       <th>NK/Stk</th>
       <th>Projekt</th>
       <th>Lieferant</th> 
           <th>Kommentar</th> 
            
       <th class='' data-bs-toggle='tooltip' title='Bearbeiten'>  <i class='fa fa-pencil-alt'></i> </th>   
    </tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    $priceID = $row['idTABELLE_Preise'] ?? '';
    $date = date_create($row["Datum"]);
    $formattedDate = date_format($date, 'Y-m-d');

    echo "<tr data-price-id='" . h($priceID) . "' 
              data-date='" . h($formattedDate) . "'
              data-quelle='" . h($row["Quelle"]) . "'
              data-menge='" . h($row["Menge"]) . "'
              data-ep='" . h($row["Preis"]) . "'
              data-nk='" . h($row["Nebenkosten"]) . "'
              data-preiskommentar='" . h($row["Kommentar"]) . "'
              data-project-id='" . h($row["projectID"] ?? '0') . "'
              data-lieferant-id='" . h($row["lieferantID"] ?? '0') . "'>";


    echo "<td>" . $formattedDate . "</td>";
    echo "<td>" . h($row["Quelle"] ?? '') . "</td>";
    echo "<td>" . h($row["Menge"] ?? '') . "</td>";
    echo "<td>" . format_money($row["Preis"] ?? '') . "</td>";
    echo "<td>" . format_money($row["Nebenkosten"] ?? '') . "</td>";
    echo "<td>" . h($row["Projektname"] ?? '') . "</td>";
    echo "<td>" . h($row["Lieferant"] ?? '') . "</td>";
    echo "<td>" . h($row["Kommentar"] ?? '') . "</td>";

    echo "<td> <button class='btn btn-sm btn-outline-dark edit-price-btn' 
                title='Preis ändern' 
                data-bs-toggle='modal'
                data-bs-target='#addPriceToElementModal'>
              <i class='fa fa-pencil-alt'></i>
          </button></td>";
    echo "</tr>";
}

echo "</tbody></table>"; ?>
<!--div class="col-12 d-flex justify-content-end">
    <button type='button'
            id='addPriceModal_show'
            class='btn btn-sm btn-success'
            value='Preis hinzufügen'
            data-bs-toggle='modal'
            data-bs-target='#addPriceToElementModal'>
        <i class='fas fa-plus'></i>
        Preis hinzufügen
    </button>
</div -->

<div class='modal fade' id='addPriceToElementModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='modalTitle'>Preis hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>

            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group" style="display:none;">
                        <input type="hidden" id="priceID" value="0">
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="date">Datum:</label>
                        <input type="text" class="form-control" id="date" placeholder="jjjj.mm.tt"/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="quelle">Info zum Verfahren:</label>
                        <select class="form-control form-control-sm" id="quelle" name="quelle" required>
                            <option value="" selected disabled>Verfahren wählen</option>
                            <option value="Direktvergabe">Direktvergabe</option>
                            <option value="Direktvergabe mit vorheriger Bekanntmachung">Direktvergabe mit vorheriger
                                Bekanntmachung
                            </option>
                            <option value="Verhandlungsverfahren ohne Bekanntmachung">Verhandlungsverfahren ohne
                                Bekanntmachung
                            </option>
                            <option value="Nicht offenes Verfahren ohne Bekanntmachung">Nicht offenes Verfahren ohne
                                Bekanntmachung
                            </option>
                            <option value="Nicht offenes Verfahren mit Bekanntmachung">Nicht offenes Verfahren mit
                                Bekanntmachung
                            </option>
                            <option value="Offenes Verfahren">Offenes Verfahren</option>
                            <option value="Verhandlungsverfahren mit Bekanntmachung">Verhandlungsverfahren mit
                                Bekanntmachung
                            </option>
                            <option value="MKF">MKF</option>
                            <option value="RV">RV</option>
                            <option value="Andere">Andere</option>
                        </select>
                        <input type="text" class="form-control mt-1" id="quelleAndere"
                               placeholder="Verfahren beschreiben..." style="display:none;"/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="menge">Menge:</label>
                        <input type="text" class="form-control" id="menge"/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="ep">EP:</label>
                        <input type="text" class="form-control" id="ep" placeholder="Komma ."/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="nk">NK/Stk:</label>
                        <input type="text" class="form-control" id="nk" placeholder="Komma ."/>
                    </div>
                    <div class="form-group">
                        <label class="mt-1" for="preiskommentar">Optionaler Kommentar: </label>
                        <textarea class="form-control"
                                  id="preiskommentar"
                                  rows="2"
                                  maxlength="255"
                                  placeholder="Preisgestaltungsrelevanter Kontext: z.B. Zubehör, Bieteranzahl, etc."></textarea>
                    </div>
                    <?php
                    $sql = "SELECT tabelle_projekte.idTABELLE_Projekte,
                                    tabelle_projekte.Interne_Nr, 
                                    tabelle_projekte.Projektname 
                            FROM tabelle_projekte ORDER BY tabelle_projekte.Interne_Nr;";
                    $result1 = $mysqli->query($sql);
                    echo "<div class='form-group'>
                        <label class='mt-1' for='project'>Projekt:</label>									
                        <select class='form-control input-sm' id='project' name='project'>
                                <option value=0>Kein Projekt</option>";
                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Projekte"] . ">" . $row["Interne_Nr"] . "-" . $row["Projektname"] . "</option>";
                    }
                    echo "</select> </div>";

                    echo "<div class='form-group'> 
                                    <label class='mt-1' for='project'>Geräte Lieferant auswählen:</label>									
                                    <select class='form-control input-sm' id='lieferant' name='lieferant'>
                                            <option value='0'>Geräte Lieferant auswählen</option>";
                    include "getDeviceLieferantenOptions.php";

                    echo "</select> 
                        </div> 
                        <div class='row mt-3'>
                        <div class='col-6'>    
                            <input type='button' id='addPrice' class='btn btn-success btn-sm col-12' value='Speichern'  data-bs-dismiss='modal'>
                        </div>
                        <div class='col-6'>
                            <button type='button' class='btn btn-danger btn-sm col-12' data-bs-dismiss='modal'>Abbrechen</button>
                        </div>
                    </div>
                 </form><hr>        
                     <div class='row'> 
                             <div class='col-6'>  
                                <button type='button' class='btn btn-sm btn-outline-success mt-2' id='addNewDevLieferant' title='Geräte Lieferantttt hinzufügen'>
                                <i class='fas fa-plus'></i> Geräte Lieferant hinzufügen
                                </button>     
                             </div>     
                             <div class='col-6 d-inline-flex justify-content-end'>   
                                <button type='button' class='btn btn-sm btn-outline-success mt-2' id='addNewLieferant' title='Neuen Lieferant anlegen'>
                                <i class='fas fa-plus'></i> Neuen Lieferant anlegen
                                </button>  
                            </div>  
                     </div>
                 
                    <div class='mt-2'>
                        <div class='row align-items-center' style='display:none;' id='NeuenLieferantZuGerätHinzufügen'>
                                          <label for='Lieferant'>Neuen Lieferant zu Gerät hinzufügen:</label>		
                            <div class='col-10'> 
                                    <select class='form-control input-sm' id='idlieferant2Dev' name='lieferant'>
                                            <option value=0>Lieferant auswählen </option>";
                    include "getPossibleLieferantenOptions.php";

                    echo "  </select>  
                            </div>
                            <div class='col-2'>   
                                <button id='addLieferant2Dev' class='btn btn-success btn-sm'><i class='fas fa-plus'></i></button> 
                            </div>
                        </div> 
                    </div>
                    <div id='inlineAddLieferant' style='display:none;'>
                            <input type='text' class='form-control mb-1' id='newLieferantName' placeholder='Name des Lieferanten' /> 
                            <input type='text' class='form-control mb-1' id='newLieferantTel' placeholder='Telefon' />
                            <input type='text' class='form-control mb-1' id='newLieferantAdresse' placeholder='Adresse' />
                            <input type='text' class='form-control mb-1' id='newLieferantPLZ' placeholder='PLZ' />
                            <input type='text' class='form-control mb-1' id='newLieferantOrt' placeholder='Ort' />
                            <input type='text' class='form-control mb-1' id='newLieferantLand' placeholder='Land' /> 
                            </select>
                            <div class='row'>
                                <div class='col-6'>  
                                    <button type='button' class='btn btn-success btn-sm col-12' id='submitNewLieferant'>Speichern</button>
                                </div>
                                <div class='col-6'>  
                                    <button type='button' class='btn btn-secondary btn-sm col-12' id='cancelNewLieferant'>Abbrechen</button>
                                </div>
                            </div>
                 </div>";
                    $mysqli->close();
                    $stmt->close();

                    ?>
            </div>
        </div>
    </div>
</div>


<script src="utils/_utils.js"></script>
<script>

    var deviceID = <?= (int)$deviceID ?>;

    $(document).ready(function () {

        $('#GerätepreiseCardHeader').html(`
        <button type='button'
                id='addPriceModal_show'
                class='btn btn-sm btn-success'
                value='Preis hinzufügen'
                data-bs-toggle='modal'
                data-bs-target='#addPriceToElementModal'>
            <i class='fas fa-plus'></i>
            Preis hinzufügen
        </button>     `);

        $('#quelle').on('change', function () {
            if ($(this).val() === 'Andere') {
                $('#quelleAndere').show().focus();
            } else {
                $('#quelleAndere').hide().val('');
            }
        });

        $(document).on('click', '.edit-price-btn', function () {
            //e.preventDefault();
            const row = $(this).closest('tr');
            $('#priceID').val(row.data('price-id'));
            $('#date').val(row.data('date'));
            const gespeichertesVerfahren = row.data('quelle');
            const bekannteVerfahren = ['Direktvergabe', 'Direktvergabe mit vorheriger Bekanntmachung',
                'Verhandlungsverfahren ohne Bekanntmachung', 'Nicht offenes Verfahren ohne Bekanntmachung',
                'Nicht offenes Verfahren mit Bekanntmachung', 'Offenes Verfahren',
                'Verhandlungsverfahren mit Bekanntmachung', 'MKF', 'RV', 'Andere'];

            if (bekannteVerfahren.includes(gespeichertesVerfahren)) {
                $('#quelle').val(gespeichertesVerfahren).trigger('change');
            } else {
                $('#quelle').val('Andere').trigger('change');
                $('#quelleAndere').val(gespeichertesVerfahren);
            }
            $('#menge').val(row.data('menge'));
            $('#ep').val(row.data('ep'));
            $('#nk').val(row.data('nk'));
            $('#preiskommentar').val(row.data('preiskommentar'));

            $('#project').val(row.data('project-id') || '0').trigger('change');
            $('#lieferant').val(row.data('lieferant-id') || '0').trigger('change');
            $('#modalTitle').text('Preis ändern');
            $('#addPrice').val('Änderungen speichern');
            //let myModal = new bootstrap.Modal(document.getElementById('addPriceToElementModal'));
            //myModal.show();
        });

        $('#addPriceModal_show').click(function () {
            $('#priceID').val('0');
            $('#modalTitle').text('Preis hinzufügen');
            $('#addPrice').val('Speichern');
            $('#date, #quelle, #menge, #ep, #nk').val('');
            $('#project, #lieferant').val('0').trigger('change');
        });

        // ENHANCED SAVE - Handles both ADD and EDIT
        $("#addPrice").click(function () {
            let priceID = $('#priceID').val();
            let date = $("#date").val();
            let quelle = $('#quelle').val() === 'Andere'
                ? $('#quelleAndere').val()
                : $('#quelle').val();
            let preiskommentar = $("#preiskommentar").val() || "";
            let menge = $("#menge").val();
            let nk = normalizeCosts($("#nk").val());
            let project = $("#project").val();
            let lieferant = $("#lieferant").val();
            let ep = normalizeCosts($("#ep").val());

            if (date === "" || quelle === "" || menge === "" || ep === "" || nk === "" || lieferant <= 0) {
                makeToaster("Bitte alle Felder ausfüllen!", false);
                return;
            }
            let url = priceID == '0' ? "addPriceToDevice.php" : "updateDevicePrice.php";

            let selectedRow = table1.row('.info');  // or table1.row({ selected: true })
            let elementID = selectedRow.data() ? selectedRow.data()[0] : null;

            $.ajax({
                url: url,
                data: {
                    "priceID": priceID,  // 0 for ADD, >0 for UPDATE
                    "date": date,
                    "quelle": quelle,
                    "menge": menge,
                    "ep": ep,
                    "nk": nk,
                    "project": project,
                    "lieferant": lieferant,
                    "preiskommentar": preiskommentar,
                     "geraeteID":deviceID
                },
                type: "POST",
                success: function (data) {
                    makeToaster(data.trim(), true);

                    $.ajax({
                        url: "getDevicePrices.php",
                        data: {"deviceID": deviceID},
                        type: "POST",
                        success: function (data) {
                            $("#devicePrices").html(data);
                        }
                    });

                    if (elementID) {
                        $.ajax({
                            url: "getDevicesAndTheirPricesForElements.php",
                            data: {"elementID": elementID},
                            type: "POST",
                            success: function (data) {
                                $("#elementPricesInOtherProjects-2").html(data);
                            }
                        });
                    }


                },
                error: function () {
                    makeToaster("Fehler beim Speichern!", false);
                }
            });
        });

        $('#date').datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked",
            language: "de"
        });

        $('#project').select2({
            width: '100%',
            placeholder: 'Projekt auswählen',
            allowClear: true,
            dropdownCssClass: 'select2-dropdown-long',
            dropdownParent: $('#addPriceToElementModal') // bind dropdown inside modal
        });

        $('#lieferant').select2({
            width: '100%',
            placeholder: 'Lieferant auswählen',
            dropdownParent: $('#addPriceToElementModal'), // bind dropdown inside modal
            allowClear: true,
            dropdownCssClass: 'select2-dropdown-long'
        });

        function reloadLieferantOptions() {
            $.ajax({
                url: 'getDeviceLieferantenOptions.php',
                type: 'POST',
                success: function (optionsHtml) {
                    $('#lieferant').html(optionsHtml).trigger('change'); // refresh Select2 dropdown
                },
                error: function () {
                    alert('Fehler beim Laden der Lieferanten.');
                }
            });
        }

        function loadPossibleLieferantenOptions() {
            $.ajax({
                url: 'getPossibleLieferantenOptions.php',
                type: 'POST',
                success: function (optionsHtml) {
                    $('#idlieferant2Dev').html(optionsHtml).trigger('change'); // trigger for Select2 refresh if used
                },
                error: function () {
                    alert('Fehler beim Laden der Lieferanten.');
                }
            });
        }

        $("#addLieferant2Dev").click(function () {
            let lieferantenID = $("#idlieferant2Dev").val();
            console.log("addLieferant2Dev!", lieferantenID);
            if (lieferantenID !== "0") {
                $.ajax({
                    url: "addLieferantToDevice.php",
                    data: {lieferantenID: lieferantenID},
                    type: "POST",
                    success: function () {
                        reloadLieferantOptions();
                        loadPossibleLieferantenOptions();
                        $.ajax({
                            url: "getLieferantenToDevices.php",
                            type: "POST",
                            success: function (data) {
                                makeToaster("Lieferant zu Gerät hinzugefügt.", true);
                                $("#deviceLieferanten").html(data);
                            }
                        });
                        $('#inlineAddLieferant').hide();
                        $('#NeuenLieferantZuGerätHinzufügen').hide();
                    },
                    error: function (xhr, status, error) {
                        alert("Fehler bei der Anfrage: " + error);
                    }
                });
            } else {
                alert("Bitte Lieferant auswählen.");
            }
        });

        $('#addNewLieferant').click(function () {
            confirm("Wurde schon genau geprüft, ob es den Lieferant nicht gibt?");
            if ($('#inlineAddLieferant').is(':visible')) {
                $('#inlineAddLieferant').hide();
                $('#addNewDevLieferant').hide();

            } else {
                $('#inlineAddLieferant').show();
                $('#addNewDevLieferant').show();
                $('#NeuenLieferantZuGerätHinzufügen').hide();
            }
        });

        $('#addNewDevLieferant').click(function () {
            console.log("Btn werqs");
            if ($('#NeuenLieferantZuGerätHinzufügen').is(':visible')) {
                $('#NeuenLieferantZuGerätHinzufügen').hide();

            } else {
                $('#NeuenLieferantZuGerätHinzufügen').show();
                $('#inlineAddLieferant').hide();
            }
        });

        $('#cancelNewLieferant').click(function () {
            $('#inlineAddLieferant').hide();
        });

        $('#submitNewLieferant').click(function () {
            let name = $('#newLieferantName').val();
            let land = $('#newLieferantLand').val();
            let ort = $('#newLieferantOrt').val();
            let tel = $('#newLieferantTel').val();
            let adresse = $('#newLieferantAdresse').val();
            let plz = $('#newLieferantPLZ').val();
            if (name && land && ort && tel && adresse && plz) {
                $.ajax({
                    url: 'addFirma.php',
                    type: 'POST',
                    data: {
                        firma: name,
                        lieferantLand: land,
                        lieferantOrt: ort,
                        lieferantTel: tel,
                        lieferantAdresse: adresse,
                        lieferantPLZ: plz
                    },
                    success: function () {
                        reloadLieferantOptions();
                        loadPossibleLieferantenOptions();
                        makeToaster("Neue Firma erfolgreich angelegt.", true);
                    }
                });
            } else {
                alert("Bitte alle Felder ausfüllen!");
            }
        });

        new DataTable('#tableDevicePrices', {
            paging: false,
            searching: false,
            info: false,
            order: [[1, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.'
            },
            scrollY: '20vh',
            scrollCollapse: true
        });

    });

</script>