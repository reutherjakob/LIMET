<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <title></title>
</head>
<body>
<?php
require_once 'utils/_utils.php';
include "utils/_format.php";
check_login();

$mysqli = utils_connect_sql();
if (isset($_GET["deviceID"])) {
    $_SESSION["deviceID"] = $_GET["deviceID"];
}
$deviceID = isset($_SESSION["deviceID"]) ? intval($_SESSION["deviceID"]) : 0;
$sql = "SELECT tabelle_preise.Datum,
               tabelle_preise.Quelle,
               tabelle_preise.Menge,
               tabelle_preise.Preis,
               tabelle_preise.Nebenkosten,
               tabelle_projekte.Interne_Nr,
               tabelle_projekte.Projektname,
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
$stmt->close();

echo "<table class='table table-striped table-sm' id='tableDevicePrices'>
	<thead><tr>";
echo "<th>Datum</th>
		<th>Info</th>
		<th>Menge</th>
		<th>EP</th>
		<th>NK/Stk</th>
                <th>Projekt</th>
                <th>Lieferant</th>
               
	</tr></thead><tbody>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    $date = date_create($row["Datum"]);
    echo "<td>" . date_format($date, 'Y-m-d') . "</td>";
    echo "<td>" . $row["Quelle"] . "</td>";
    echo "<td>" . $row["Menge"] . "</td>";
    echo "<td>" . format_money($row["Preis"]) . "</td>";
    echo "<td>" . format_money($row["Nebenkosten"]) . "</td>";
    echo "<td>" . $row["Projektname"] . "</td>";
    echo "<td>" . $row["Lieferant"] . "</td>";

    echo "</tr>";
}
echo "</tbody></table>";
echo "<button type='button' id='addPriceModal' class='btn btn-success' value='Preis hinzufügen' data-bs-toggle='modal' data-bs-target='#addPriceToElementModal'> Preis hinzufügen</button>";
?>

<div class='modal fade' id='addPriceToElementModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Preis hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="date">Datum:</label>
                        <input type="text" class="form-control" id="date" placeholder="jjjj.mm.tt"/>
                    </div>
                    <div class="form-group">
                        <label for="quelle">Info:</label>
                        <input type="text" class="form-control" id="quelle" placeholder="Verfahrensart, Anmerkung,..."/>
                    </div>
                    <div class="form-group">
                        <label for="menge">Menge:</label>
                        <input type="text" class="form-control" id="menge"/>
                    </div>
                    <div class="form-group">
                        <label for="ep">EP:</label>
                        <input type="text" class="form-control" id="ep" placeholder="Komma ."/>
                    </div>
                    <div class="form-group">
                        <label for="nk">NK/Stk:</label>
                        <input type="text" class="form-control" id="nk" placeholder="Komma ."/>
                    </div>

                    <?php

                    $sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname"
                        . " FROM tabelle_projekte ORDER BY tabelle_projekte.Interne_Nr;";

                    $result1 = $mysqli->query($sql);

                    echo "<div class='form-group'>
                                                    <label for='project'>Projekt:</label>									
                                                    <select class='form-control input-sm' id='project' name='project'>
                                                            <option value=0>Kein Projekt</option>";

                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Projekte"] . ">" . $row["Interne_Nr"] . "-" . $row["Projektname"] . "</option>";
                    }
                    echo "</select> </div>";


                    echo "<div class='form-group'> 
                                    <label class='' for='project'>Geräte Lieferant auswählen:</label>									
                                    <select class='form-control input-sm' id='lieferant' name='lieferant'>
                                            <option value='0'>Geräte Lieferant auswählen</option>";
                    include "getDeviceLieferantenOptions.php";


                    echo "</select> 
                        </div> 


                        <div class='row mt-3'>
                        <div class='col-6'>    
                            <input type='button' id='addPrice' class='btn btn-success btn-sm col-12' value='Speichern'
                                           data-bs-dismiss='modal'>
                        </div>
                        <div class='col-6'>
                            <button type='button' class='btn btn-danger btn-sm col-12' data-bs-dismiss='modal'>Abbrechen</button>
                        </div>
                    </div>
                 </form>";


                    echo "<hr>       
                     <div class='row'> 
                             <div class='col-6'>  
                                <button type='button' class='btn btn-sm btn-outline-success mt-2' id='addNewDevLieferant' title='Geräte Lieferant hinzufügen'>
                                <i class='fas fa-plus'></i> Geräte Lieferant hinzufügen
                                </button>     
                             </div>     
                             <div class='col-6'>   
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
                            <input type='text' class='form-control mb-1' id='newLieferantLand' placeholder='Land' /> ";
                    echo "</select>
                        
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
                    ?>
            </div>
        </div>
    </div>

</div>
</div>

<script src="utils/_utils.js"></script>
<script>
    $(document).ready(function () {
        // $('#date').datepicker({
        //     format: "yyyy-mm-dd",
        //     calendarWeeks: true,
        //     autoclose: true,
        //     todayBtn: "linked",
        //     language: "de"
        // });

        // // Initialize Select2
        // $('#project').select2({
        //     width: '100%',
        //     placeholder: 'Projekt auswählen',
        //     allowClear: true
        // });

        //$('#lieferant').select2({
        //    width: '100%',
        //    placeholder: 'Lieferant auswählen',
        //    allowClear: true
        //});

        function reloadLieferantOptions() {
            $.ajax({
                url: 'getDeviceLieferantenOptions.php',
                type: 'GET',
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
                type: 'GET',
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
                    dataType: "json", // expect JSON response
                    success: function () {
                        reloadLieferantOptions();
                        loadPossibleLieferantenOptions();
                        $.ajax({
                            url: "getLieferantenToDevices.php",
                            type: "GET",
                            success: function (data) {
                                makeToaster("Lieferant zu Gerät hinzugefügt.", trues);
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
                    type: 'GET',
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
            order: [[0, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.'
            },
            scrollY: '20vh',
            scrollCollapse: true,
            //deferRender: true,           // improves performance with large data sets
            //pagingType: 'simple_numbers',  // uncomment if needed
            //lengthMenu: [[5,10,25,50,-1], [5,10,25,50,'All']]  // uncomment if needed
        });


        //Preis zu Geraet hinzufügen
        $("#addPrice").click(function () {  // TODO test preis hinzufügen...
            var date = $("#date").val();
            var quelle = $("#quelle").val();
            var menge = $("#menge").val();

            var nk = $("#nk").val();
            if (nk.toLowerCase().endsWith('k')) {
                nk = nk.slice(0, -1) + '000';
            }
            nk.replace(/,/g, '.').replace(/[^0-9.]/g, '');

            var project = $("#project").val();
            var lieferant = $("#lieferant").val();

            let ep = $("#ep").val();
            if (ep.toLowerCase().endsWith('k')) {
                ep = ep.slice(0, -1) + '000';
            }
            ep.replace(/,/g, '.').replace(/[^0-9.]/g, '');

            if (date !== "" && quelle !== "" && menge !== "" && ep !== "" && nk !== "" && lieferant > 0) {
                $.ajax({
                    url: "addPriceToDevice.php",
                    data: {
                        "date": date,
                        "quelle": quelle,
                        "menge": menge,
                        "ep": ep,
                        "nk": nk,
                        "project": project,
                        "lieferant": lieferant
                    },
                    type: "GET",
                    success: function (data) {
                        makeToaster(data, true);
                        $.ajax({
                            url: "getDevicePrices.php",
                            type: "GET",
                            success: function (data) {
                                $("#devicePrices").html(data);
                            }
                        });
                    }
                });

            } else {
                makeToaster("Bitte alle Felder ausfüllen!", false);
                let myModal = new bootstrap.Modal(document.getElementById('addPriceToElementModal'));
                myModal.show();

            }
        });
    });

</script>

</body>
</html>