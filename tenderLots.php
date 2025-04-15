<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Losverwaltung</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">
    <!-- CDNz25 Rework -->
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

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <!--DATEPICKER -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <!--Bootstrap Toggle -->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>


</head>

<body id="bodyTenderLots">
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
include "_format.php";
init_page_serversides();
?>
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">

    <div class='row'>
        <div class='col-xxl-9' id="mainCardColumn">
            <div class="mt-4 card">
                <div class="card-header d-inline-flex justify-content-between align-items-center">
                    <div class="d-inline-flex align-items-center">
                        <span> <strong>Lose im Projekt</strong>  &emsp;</span>

                        <button type='button' id='addTenderLotModalButton' class='btn btn-outline-success btn-sm me-2 ms-2 '
                                value='Los hinzufügen' data-bs-toggle='modal' data-bs-target='#addTenderLotModal'>    <i class="fa fa-plus" aria-hidden="true"></i>Los hinzufügen </button>
                    </div>
                    <div class="d-inline-flex align-items-center" id="LoseCardHeaderSub">
                        <button type='button' class='btn btn-outline-secondary btn-sm' id='createTenderListPDF'>
                            <i class='far fa-file-pdf'></i> Losliste mit Elementen-PDF
                        </button>
                        <button type='button' class='btn btn-outline-secondary btn-sm me-2 ms-2 '
                                id='createTenderListWithoutElementsPDF'>
                            <i class='far fa-file-pdf'></i> Losliste-PDF
                        </button>
                        <button type='button' class='btn btn-outline-secondary btn-sm me-2 ms-2 ' id='createTenderWorkflowPDF'>
                            <i class='far fa-file-pdf'></i> Workflow-PDF
                        </button>


                    </div>
                </div>
                <div class="card-body" id="projectLots">
                    <div class="">
                        <?php
                        $mysqli = utils_connect_sql();
                        $sql = "SELECT `tabelle_lieferant`.`idTABELLE_Lieferant`,
                                                                            `tabelle_lieferant`.`Lieferant`
                                                                        FROM `LIMET_RB`.`tabelle_lieferant`
                                                                        ORDER BY `Lieferant`;";

                        $result = $mysqli->query($sql);
                        $possibleAuftragnehmer = array();
                        while ($row = $result->fetch_assoc()) {
                            $possibleAuftragnehmer[$row['idTABELLE_Lieferant']]['idTABELLE_Lieferant'] = $row['idTABELLE_Lieferant'];
                            $possibleAuftragnehmer[$row['idTABELLE_Lieferant']]['Lieferant'] = $row['Lieferant'];
                        }

                        // Abfrage der externen Lose
                        $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, 
                                                                                tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.Verfahren, tabelle_lose_extern.mkf_von_los,
                                                                                tabelle_lose_extern.Bearbeiter, tabelle_lose_extern.Vergabesumme, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Notiz, tabelle_lose_extern.Kostenanschlag, tabelle_lose_extern.Budget,
                                                                                tabelle_lieferant.Lieferant, tabelle_lieferant.idTABELLE_Lieferant,
                                                                                losschaetzsumme.Summe,
                                                                                losbestandschaetzsumme.SummeBestand,
                                                                                losschaetzsumme.id,
                                                                                losbestandschaetzsumme.id
                                                                        FROM tabelle_lieferant 
                                                                        RIGHT JOIN tabelle_lose_extern 
                                                                        ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
                                                                        LEFT JOIN
                                                                                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS Summe
                                                                                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                                                                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                                                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                                                                            AS losschaetzsumme
                                                                            ON (tabelle_lose_extern.idtabelle_Lose_Extern = losschaetzsumme.id)
                                                                        LEFT JOIN 
                                                                                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS SummeBestand
                                                                                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                                                                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                                                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                                                                            AS losbestandschaetzsumme
                                                                            ON (tabelle_lose_extern.idtabelle_Lose_Extern = losbestandschaetzsumme.id)
                                                                        WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                                        ORDER BY LosNr_Extern;";

                        $result = $mysqli->query($sql);

                        echo "<table  id='tableTenderLots' class='table table-sm table-responsive table-striped compact border border-light border-5'>
								<thead><tr>
								<th>ID</th>
                                        <th></th>
                                        <th>Nummer</th>
                                        <th>Bezeichnung</th>                                       
                                        <th>Versand</th>
								        <th>Liefertermin</th>
                                        <th>Verfahren</th>
                                        <th>Bearbeiter</th>
								        <th>Status</th>
                                        <th>Schätzung-Neu</th>
                                        <th>Schätzung-Bestand</th>
                                        <th>Kostenanschlag</th>
                                        <th>Budget (val)</th>
                                        <th>Vergabesumme</th>
                                            <th>Schätzung-Neu</th>
                                            <th>Schätzung-Bestand</th>
                                            <th>Kostenanschlag</th> 
                                            <th>Budget (val)</th>
                                            <th>Vergabesumme</th>
                                        <th>Auftragnehmer</th>      
                                        <th></th>          
                                        <th>Notiztext</th>                                         
                                            <th>IDLieferant</th>
                                            <th>Abgeschlossen</th>                                                                  
                                            <th>MKF-von_Los</th>   
                 
								</tr></thead>";
                        echo "<tbody>";
                        $hauptLose = array();
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["idtabelle_Lose_Extern"] . "</td>";

                            echo "<td> <button type='button' id='" . $row["idtabelle_Lose_Extern"] . "' class='btn btn-outline-dark btn-sm' value='changeTenderLot'><i class='fas fa-pencil-alt'></i></button></td>";
                            echo "<td>" . $row["LosNr_Extern"] . "</td>";
                            echo "<td>" . $row["LosBezeichnung_Extern"] . "</td>";

                            echo "<td>" . $row["Versand_LV"] . "</td>";
                            echo "<td>" . $row["Ausführungsbeginn"] . "</td>";
                            echo "<td>" . $row["Verfahren"] . "</td>";
                            echo "<td>" . $row["Bearbeiter"] . "</td>";
                            echo "<td >";
                            switch ($row["Vergabe_abgeschlossen"]) {
                                case 0:
                                    //echo "<b><font color='red'>&#10007;</font></b>";
                                    echo "<span class='badge badge-pill bg-danger'>Offen</span>";
                                    break;
                                case 1:
                                    //echo "<b><font color='green'>&#10003;</font></b>";
                                    echo "<span class='badge badge-pill bg-success'>Fertig</span>";
                                    break;
                                case 2:
                                    //echo "<b><font color='blue'>&#8776;</font></b>";
                                    echo "<span class='badge badge-pill bg-primary'>Wartend</span>";
                                    break;
                            }
                            echo "</td>";
                            echo "<td>" . format_money($row["Summe"]) . "</td>";
                            echo "<td>" . format_money($row["SummeBestand"]) . "</td>";
                            echo "<td>" . format_money($row["Kostenanschlag"]) . "</td>";
                            echo "<td>" . format_money($row["Budget"]) . "</td>";
                            echo "<td>" . format_money($row["Vergabesumme"]) . "</td>";

                            $out = "0";
                            if ($row["Summe"] == null) {
                                $out = "0.00";
                            } else {
                                $out = $row["Summe"];
                            }
                            echo "<td>" . $out . "</td>";
                            if ($row["SummeBestand"] == null) {
                                $out = "0.00";
                            } else {
                                $out = $row["SummeBestand"];
                            }
                            echo "<td>" . $out . "</td>";
                            echo "<td>" . $row["Kostenanschlag"] . "</td>";
                            echo "<td>" . $row["Budget"] . "</td>";
                            echo "<td>" . $row["Vergabesumme"] . "</td>";

                            echo "<td>" . $row["Lieferant"] . "</td>";

                            echo "<td><button type='button' id='" . $row["idtabelle_Lose_Extern"] . "' class='btn btn-outline-secondary btn-sm' value='LotWorkflow' data-bs-toggle='modal' data-bs-target='#workflowDataModal'><i class='fas fa-history'></i></button></td>";
                            echo "<td>" . $row["Notiz"] . "</td>";

                            echo "<td>" . $row["idTABELLE_Lieferant"] . "</td>";
                            echo "<td>" . $row["Vergabe_abgeschlossen"] . "</td>";
                            echo "<td>" . $row["mkf_von_los"] . "</td>";

                            echo "</tr>";
                            $hauptLose[$row['idtabelle_Lose_Extern']]['idtabelle_Lose_Extern'] = $row['idtabelle_Lose_Extern'];
                            $hauptLose[$row['idtabelle_Lose_Extern']]['LosNr_Extern'] = $row['LosNr_Extern'];
                            $hauptLose[$row['idtabelle_Lose_Extern']]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];
                        }
                        echo "</tbody></table>";


                        $mysqli->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-xxl-3' id='vermerkeCardColumn'>
            <div class='mt-4 card'>
                <div class='card-header' id='vermerkePanelHead'>Vermerke zu Los
                    <button id='toggleVermerkeBtn' class='btn btn-sm float-end'>
                        <i class='fas fa-chevron-right'></i>
                    </button>
                </div>
                <div class='card-body' id='lotVermerke'>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-8">
            <div class="mt-4 card">
                <div class="card-header d-inline-flex justify-content-between align-items-center"
                     id="elementsInLotCardHeader">Elemente im Los
                </div>
                <div class="card-body" id="elementsInLot"></div>
            </div>
        </div>
        <div class="col-xxl-4">
            <div class="mt-4 card">
                <div class="card-header">Variantenparameter</div>
                <div class="card-body" id="elementsvariantenParameterInLot"></div>
            </div>
            <div class="mt-4 card">
                <div class="card-header">Bestandsdaten
                    <button type='button' id='addBestandsElement'
                            class='btn btn-sm ml-4 mt-2 btn-outline-secondary float-right' value='Hinzufügen'
                            data-bs-toggle='modal' data-bs-target='#addBestandModal'><i class='fas fa-plus'></i>
                    </button>
                    <button type='button' id='reloadBestand'
                            class='btn btn-sm ml-4 mt-2 btn-outline-secondary float-right' value='reloadBestand'>
                        <i class="fa fa-retweet" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="card-body" id="elementBestand"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Anlegen eines Loses-->
<div class='modal fade' id='addTenderLotModal' role='dialog'>
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Losdaten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <form role='form'>
                        <label for="lotMKF"></label>
                        <input id="lotMKF" data-bs-toggle="toggle" type="checkbox" data-on="MKF" data-off="MKF"
                               data-onstyle="success" data-offstyle="danger">

                        <div class='form-group'>
                            <label for='lotMKFOf'>Los wählen:</label>
                            <select class='form-control form-control-sm' id='lotMKFOf' disabled>
                                <option value=0 selected>Hauptlos wählen</option>
                                <?php
                                foreach ($hauptLose as $array) {
                                    echo "<option value=" . $array['idtabelle_Lose_Extern'] . ">" . $array['LosNr_Extern'] . " - " . $array['LosBezeichnung_Extern'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class='form-group'>
                            <label for='lotNr'>Losnummer:</label>
                            <input type='text' class='form-control form-control-sm' id='lotNr' placeholder='Losnummer'/>
                        </div>
                        <div class='form-group'>
                            <label for='lotName'>Bezeichnung:</label>
                            <input type='text' class='form-control form-control-sm' id='lotName'
                                   placeholder='Losbezeichnung'/>
                        </div>
                        <div class='form-group'>
                            <label for='lotLVSend'>Versand LV:</label>
                            <input type='text' class='form-control form-control-sm' id='lotLVSend'
                                   placeholder='jjjj-mm-tt'/>
                        </div>
                        <div class='form-group'>
                            <label for='lotStart'>Ausführungsbeginn:</label>
                            <input type='text' class='form-control form-control-sm' id='lotStart'
                                   placeholder='jjjj-mm-tt'/>
                        </div>
                        <div class='form-group'>
                            <label for='lotVerfahren'>Verfahren:</label>
                            <input type='text' class='form-control form-control-sm' id='lotVerfahren'
                                   placeholder='Verfahren'/>
                        </div>
                        <div class='form-group'>
                            <label for='lotLVBearbeiter'>Bearbeiter:</label>
                            <input type='text' class='form-control form-control-sm' id='lotLVBearbeiter'
                                   placeholder='Bearbeiter'/>
                        </div>
                        <div class='form-group'>
                            <label for='kostenanschlag'>Kostenanschlag: (.)</label>
                            <input type='text' class='form-control form-control-sm' id='kostenanschlag'
                                   placeholder='0'/>
                        </div>
                        <div class='form-group'>
                            <label for='budget'>Budget (valorisiert): (.)</label>
                            <input type='text' class='form-control form-control-sm' id='budget' placeholder='0'/>
                        </div>
                        <div class='form-group'>
                            <label for='lotSum'>Vergabesumme: (.)</label>
                            <input type='text' class='form-control form-control-sm' id='lotSum' placeholder='Summe'/>
                        </div>
                        <div class='form-group'>
                            <label for='lotVergabe'>Status:</label>
                            <select class='form-control form-control-sm' id='lotVergabe'>
                                <option value='0' selected>Nicht abgeschlossen</option>
                                <option value='1'>Abgeschlossen</option>
                                <option value='2'>Wartend</option>
                            </select>
                        </div>
                        <div class='form-group'>
                            <label for='lotAuftragnehmer'>Auftragnehmer:</label>
                            <select class='form-control form-control-sm' id='lotAuftragnehmer'>
                                <option value=0 selected>Auftragnehmer wählen</option>
                                <?php
                                foreach ($possibleAuftragnehmer as $array) {
                                    echo "<option value=" . $array['idTABELLE_Lieferant'] . ">" . $array['Lieferant'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class='form-group'>
                            <label for='lotNotice'>Notiz:</label>
                            <textarea class='form-control form-control-sm' rows='5' id='lotNotice'
                                      placeholder='Notiz'></textarea>
                        </div>
                    </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addTenderLot' class='btn btn-success btn-sm' value='Hinzufügen'>
                <input type='button' id='saveTenderLot' class='btn btn-warning btn-sm' value='Speichern'>
                <button type='button' class='btn btn-outline-secondary btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal zum Zeigen des Los-Workflows -->
<div class='modal fade' id='workflowDataModal' role='dialog' data-bs-keyboard="true">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Los-Workflow</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='workflowModalBody'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>


</body>
<!-- Modal Info
<div class='modal fade' id='infoModal' role='dialog'>
    <div class='modal-dialog modal-dialog-centered modal-sm'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Info</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='infoBody'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>OK</button>
            </div>
        </div>

    </div>
</div>
-->

<script src="_utils.js"></script>
<!--suppress ES6ConvertVarToLetConst -->
<script charset="utf-8" type="module">
    var lotID;
    var lotVerfahren;
    var tableTenderLots;
    $(document).ready(function () {
        tableTenderLots = new DataTable('#tableTenderLots', {
            columnDefs: [
                {
                    targets: [0, 14, 15, 16, 17, 18, 22, 23, 24],
                    visible: false,
                    searchable: false
                }
            ],
            select: true,
            search: {search: ''},
            paging: true,
            searching: true,
            info: true,
            order: [[2, 'asc']],
            pagingType: 'simple',
            lengthChange: true,
            pageLength: 10,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.',
                searchPlaceholder: 'Suche..',
                search: ""
            },
            buttons: [
                {
                    extend: 'excel',
                    className: "btn btn-outline-secondary bg-white fas fa-file-excel me-2 ms-2 ",
                    exportOptions: {
                        columns: function (idx) {
                            return idx !== 0 &&
                                idx !== 1 &&
                                idx !== 9 &&
                                idx !== 10 &&
                                idx !== 11 &&
                                idx !== 12 &&
                                idx !== 13 &&
                                idx !== 20 &&
                                idx !== 22 &&
                                idx !== 23 &&
                                idx !== 24;
                        }
                    }
                }
            ],
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: 'info',
                bottomEnd: ['pageLength', 'paging', 'search', 'buttons']
            },
            initComplete: function () {
                move_item_by_class("dt-buttons", "LoseCardHeaderSub");
                const button = document.querySelector(".dt-buttons");
                if (button) {
                    button.classList.remove("dt-buttons");
                }

                $('.dt-search label').remove();
                $('.dt-search').children().removeClass('form-control form-control-sm').addClass("btn btn-sm btn-outline-secondary").appendTo('#LoseCardHeaderSub');
            }
        });

        $('#tableTenderLots tbody').on('click', 'tr', function () {
            if ($.fn.DataTable.isDataTable('#tableLotElements1')) {
            }
            lotID = tableTenderLots.row($(this)).data()[0];

            let lotVerfahren1 = tableTenderLots.row($(this)).data()[5];

            if (lotVerfahren1 === "MKF") {
                $('#lotMKF').bootstrapToggle('enable');
                $('#lotMKF').bootstrapToggle('on');
                $('#lotMKF').bootstrapToggle('disable');
                $("#lotMKFOf").prop('disabled', true);
            } else {
                $('#lotMKF').bootstrapToggle('enable');
                $('#lotMKF').bootstrapToggle('off');
                $('#lotMKF').bootstrapToggle('disable');
            }

            document.getElementById("lotNr").value = tableTenderLots.row($(this)).data()[2];
            document.getElementById("lotName").value = tableTenderLots.row($(this)).data()[3];
            document.getElementById("lotLVSend").value = tableTenderLots.row($(this)).data()[4];
            document.getElementById("lotStart").value = tableTenderLots.row($(this)).data()[5];
            document.getElementById("lotVerfahren").value = tableTenderLots.row($(this)).data()[6];
            document.getElementById("lotLVBearbeiter").value = tableTenderLots.row($(this)).data()[7];
            document.getElementById("kostenanschlag").value = tableTenderLots.row($(this)).data()[11];
            document.getElementById("budget").value = tableTenderLots.row($(this)).data()[12];
            document.getElementById("lotSum").value = tableTenderLots.row($(this)).data()[13];


            const htmlString = tableTenderLots.row($(this)).data()[8];
            const textContent = htmlString.replace(/<[^>]*>/g, '');
            console.log(textContent);
            let selectedValue;
            switch (textContent) {
                case 'Offen':
                    selectedValue = 0;
                    break;
                case 'Fertig':
                    selectedValue = 1;
                    break;
                case 'Wartend':
                    selectedValue = 2;
                    break;
                default:
                    selectedValue = 0; // Default to 'Offen' if not recognized
            }
            document.getElementById("lotVergabe").value = selectedValue;
            const selectElement = document.getElementById("lotVergabe");
            for (let i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value === selectedValue) {
                    selectElement.options[i].selected = true;
                    break;
                }
            }
            document.getElementById("lotAuftragnehmer").value = tableTenderLots.row($(this)).data()[18];
            document.getElementById("lotNotice").value = tableTenderLots.row($(this)).data()[21];

            document.getElementById("lotMKFOf").value = tableTenderLots.row($(this)).data()[24]; //TODO ??
            $.ajax({
                url: "getLotVermerke.php",
                data: {"lotID": lotID},
                type: "GET",
                success: function (data) {
                    $("#lotVermerke").html(data);
                    $.ajax({
                        url: "getTenderLotElements.php",
                        data: {"lotID": lotID},
                        type: "GET",
                        success: function (data) {
                            $("#elementsInLot").html(data);
                            $("#elementBestand").hide();
                            $("#elementsvariantenParameterInLot").hide();
                        }
                    });
                }
            });

        });

        $('#lotLVSend').datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked"
        });

        $('#lotStart').datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked"
        });

    });

    document.getElementById("addTenderLot").addEventListener("click", function () {
        const formData = {
            losNr: document.getElementById("lotNr").value,
            losName: document.getElementById("lotName").value,
            losDatum: document.getElementById("lotStart").value,
            kostenanschlag: document.getElementById("kostenanschlag").value,
            budget: document.getElementById("budget").value,
            lotSum: document.getElementById("lotSum").value,
            lotVergabe: document.getElementById("lotVergabe").value,
            lotNotice: document.getElementById("lotNotice").value,
            lotAuftragnehmer: document.getElementById("lotAuftragnehmer").value,
            lotLVSend: document.getElementById("lotLVSend").value,
            lotVerfahren: document.getElementById("lotVerfahren").value,
            lotLVBearbeiter: document.getElementById("lotLVBearbeiter").value,
            lotMKFOf: document.getElementById("lotMKFOf").value
        };

        console.log("Form Data:", formData);

        if (formData.lotMKFOf === "0") {
            if (formData.losNr !== "" && formData.losName !== "" && formData.losDatum !== "" &&
                formData.lotLVSend !== "" && formData.lotVerfahren !== "" && formData.lotLVBearbeiter !== "") {
                sendData(formData);
            } else {
                alert("Bitte alle Felder außer der Vergabesumme und Auftragnehmer ausfüllen!");
            }
        } else {
            if (formData.losDatum !== "" && formData.lotLVSend !== "" &&
                formData.lotVerfahren !== "" && formData.lotLVBearbeiter !== "") {
                sendData(formData);
            } else {
                alert("Für MKF bitte alle Felder r ausfüllen (außer der Vergabesumme und Auftragnehmer und Auftragnehme)!");
            }
        }
    });

    function sendData(formData) {
        console.log("Sending data:", formData);
        document.getElementById('addTenderLotModal').style.display = 'none';
        $('#addTenderLotModal').modal('hide');

        const queryString = new URLSearchParams(formData).toString();
        const url = `addTenderLot.php?${queryString}`;
        console.log(url);
        fetch(url, {
            method: "GET",
        })
            .then(response => response.text())
            .then(data => {
                console.log("Response:", data);
                alert(data);
                window.location.replace("tenderLots.php");
            })
            .catch(error => {
                console.error('Error:', error);
            });
       // window.location.replace("tenderLots.php");
    }


    //Los speichern
    $("#saveTenderLot").click(function () {
        let losNr = $("#lotNr").val();
        let losName = $("#lotName").val();
        let losDatum = $("#lotStart").val();
        let kostenanschlag = $("#kostenanschlag").val();
        let budget = $("#budget").val();
        let lotSum = $("#lotSum").val();
        let lotVergabe = $("#lotVergabe").val();
        let lotNotice = $("#lotNotice").val();
        let lotAuftragnehmer = $("#lotAuftragnehmer").val();
        let lotLVSend = $("#lotLVSend").val();
        let lotVerfahren = $("#lotVerfahren").val();
        let lotLVBearbeiter = $("#lotLVBearbeiter").val();

        if ($("#lotMKF").prop('checked') === false) {
            console.log("$('#lotMKF').prop('checked') === false");
            if (losNr !== "" && losName !== "" && losDatum !== "" && lotLVSend !== "" && lotVerfahren !== "" && lotLVBearbeiter !== "") {
                $('#addTenderLotModal').modal('hide');
                $.ajax({
                    url: "setTenderLot.php",
                    data: {
                        "lotID": lotID,
                        "losNr": losNr,
                        "losName": losName,
                        "losDatum": losDatum,
                        "lotSum": lotSum,
                        "lotVergabe": lotVergabe,
                        "lotNotice": lotNotice,
                        "lotAuftragnehmer": lotAuftragnehmer,
                        "lotLVSend": lotLVSend,
                        "lotVerfahren": lotVerfahren,
                        "lotLVBearbeiter": lotLVBearbeiter,
                        "mkf": 0,
                        "kostenanschlag": kostenanschlag,
                        "budget": budget
                    },
                    type: "GET",
                    success: function (data) {
                        alert(data);
                        window.location.replace("tenderLots.php");
                    }
                });
            } else {
                alert("Bitte alle Felder außer der Vergabesumme/Auftragnehmer ausfüllen!");
            }

        } else {
            if (losDatum !== "" && lotLVSend !== "" && lotVerfahren !== "" && lotLVBearbeiter !== "") {
                $('#addTenderLotModal').modal('hide');
                $.ajax({
                    url: "setTenderLot.php",
                    data: {
                        "lotID": lotID,
                        "losNr": losNr,
                        "losName": losName,
                        "losDatum": losDatum,
                        "lotSum": lotSum,
                        "lotVergabe": lotVergabe,
                        "lotNotice": lotNotice,
                        "lotAuftragnehmer": lotAuftragnehmer,
                        "lotLVSend": lotLVSend,
                        "lotVerfahren": lotVerfahren,
                        "lotLVBearbeiter": lotLVBearbeiter,
                        "mkf": 1,
                        "kostenanschlag": kostenanschlag,
                        "budget": budget
                    },
                    type: "GET",
                    success: function (data) {
                        alert(data);
                        window.location.replace("tenderLots.php");
                    }
                });
            } else {
                alert("Bitte alle Felder außer der Vergabesumme/Auftragnehmer ausfüllen!");
            }
        }
    });

    $("button[value='changeTenderLot']").click(function () {
        $('#lotMKF').bootstrapToggle('disable');
        document.getElementById("addTenderLot").style.display = "none";
        document.getElementById("saveTenderLot").style.display = "inline";
        $('#addTenderLotModal').modal('show');
    });


    $("#addTenderLotModalButton").click(function () {
        document.getElementById("lotNr").value = "";
        document.getElementById("lotName").value = "";
        document.getElementById("lotLVSend").value = "";
        document.getElementById("lotStart").value = "";
        document.getElementById("lotVerfahren").value = "";
        document.getElementById("lotLVBearbeiter").value = "";
        document.getElementById("lotSum").value = "";
        document.getElementById("lotVergabe").value = "";
        document.getElementById("lotAuftragnehmer").value = "";

        document.getElementById("saveTenderLot").style.display = "none";      // Buttons ein/ausblenden!
        document.getElementById("addTenderLot").style.display = "inline";
        $('#lotMKF').bootstrapToggle('enable');
    });


    //MKF Checkbox
    $('#lotMKF').change(function () {
        var checked = $(this).prop('checked');
        if (checked === true) {
            $("#lotMKFOf").prop('disabled', false);
            $("#lotNr").prop('disabled', true);
            $("#lotName").prop('disabled', true);
            $("#lotVerfahren").prop('disabled', true);
            document.getElementById("lotVerfahren").value = "MKF";
        } else {
            $("#lotMKFOf").prop('disabled', true);
            $("#lotNr").prop('disabled', false);
            $("#lotName").prop('disabled', false);
            $("#lotVerfahren").prop('disabled', false);
            document.getElementById("lotVerfahren").value = "";
            document.getElementById("lotMKFOf").value = 0;
        }
    });


    // PDF erzeugen
    $('#createTenderListPDF').click(function () {
        window.open('/pdf_createTenderLotElementListPDF.php');
    });

    $('#createTenderListWithoutElementsPDF').click(function () {
        window.open('/pdf_createTenderLotElementListWithoutElementsPDF.php');
    });

    $('#createTenderWorkflowPDF').click(function () {
        window.open('/pdf_createTenderWorkflowPDF.php');
    });

    $("button[value='LotWorkflow']").click(function () {
        var ID = this.id;
        $.ajax({
            url: "getLotWorkflow.php",
            type: "GET",
            data: {"lotID": ID},
            success: function (data) {
                $("#workflowModalBody").html(data);
            }
        });

    });

    $("button[value='reloadBestand']").click(function () {
        $("#elementBestand").html("");
        $.ajax({
            url: "getElementBestand.php",
            type: "GET",
            success: function (data) {
                makeToaster("Reloaded!", true);
                $("#elementBestand").html(data);
            }
        });
    });

    $('#toggleVermerkeBtn').click(function () {
        $('#mainCardColumn').toggleClass('col-xxl-9 col-xxl-11');
        $('#vermerkeCardColumn').toggleClass('col-xxl-3 col-xxl-1');
        $('#lotVermerke').toggleClass('d-none');
        $(this).find('i').toggleClass('fa-chevron-right fa-chevron-left');
    });


</script>


</html>
