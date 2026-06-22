<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Losverwaltung</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
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
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
init_page_serversides();

?>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class='row'>
        <div class='col-xxl-11' id="mainCardColumn">
            <div class="mt-4 card">
                <div class="card-header d-inline-flex justify-content-between align-items-center">
                    <div class="d-inline-flex align-items-center">
                        <span> <strong>Lose im Projekt</strong>  &emsp;</span>

                        <button type='button' id='addTenderLotModalButton'
                                class='btn btn-outline-success btn-sm ms-2'
                                value='Los hinzufügen' data-bs-toggle='modal' data-bs-target='#addTenderLotModal'><i
                                    class="fa fa-plus" aria-hidden="true"></i> Los hinzufügen
                        </button>
                    </div>
                    <div class="d-inline-flex align-items-center" id="LoseCardHeaderSub">
                        <button type='button' class='btn btn-outline-secondary btn-sm ms-2' id='createTenderListPDF'>
                            <i class='far fa-file-pdf'></i> Losliste mit Elementen-PDF
                        </button>
                        <button type='button' class='btn btn-outline-secondary btn-sm  ms-2'
                                id='createTenderListWithoutElementsPDF'>
                            <i class='far fa-file-pdf'></i> Losliste-PDF
                        </button>
                        <button type='button' class='btn btn-outline-secondary btn-sm  ms-2 '
                                id='createTenderWorkflowPDF'>
                            <i class='far fa-file-pdf'></i> Workflow-PDF
                        </button>


                    </div>
                </div>
                <div class="card-body p-0 py-0 m-0" id="projectLots">
                    <div class="p-0">
                        <?php
                        function getVerfahrenBadgeClass($verfahren): string
                        {
                            switch ($verfahren) {
                                case 'Direktvergabe':
                                    return 'bg-secondary';
                                case 'Direktvergabe mit vorheriger Bekanntmachung':
                                    return 'bg-info';
                                case 'Verhandlungsverfahren ohne Bekanntmachung':
                                    return 'bg-warning';
                                case 'Nicht offenes Verfahren ohne Bekanntmachung':
                                    return 'bg-primary';
                                case 'Nicht offenes Verfahren mit Bekanntmachung':
                                case 'RV':
                                    return 'bg-success';
                                case 'Offenes Verfahren':
                                case 'MKF':
                                    return 'bg-danger';


                                default:
                                    return 'bg-dark';
                            }
                        }


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

                        $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, 
                                        tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Versand_LV, 
                                        tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.Verfahren, tabelle_lose_extern.mkf_von_los,
                                        tabelle_lose_extern.Bearbeiter, tabelle_lose_extern.Vergabesumme, 
                                        tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Notiz, tabelle_lose_extern.Kostenanschlag, tabelle_lose_extern.Budget,
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
                                        WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=?))
                                        GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                                    AS losschaetzsumme
                                    ON (tabelle_lose_extern.idtabelle_Lose_Extern = losschaetzsumme.id)
                                LEFT JOIN 
                                        (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS SummeBestand
                                        FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                        WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=?))
                                        GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                                    AS losbestandschaetzsumme
                                    ON (tabelle_lose_extern.idtabelle_Lose_Extern = losbestandschaetzsumme.id)
                                WHERE tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte=?
                                ORDER BY LosNr_Extern;";

                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param('iii', $_SESSION["projectID"], $_SESSION["projectID"], $_SESSION["projectID"]);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        echo "<table  id='tableTenderLots' class='table table-sm table-responsive table-striped compact border border-light border-1'>
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
                                        <th>Schätzung Neu</th>
                                        <th>Schätzung Bestand</th>
                                        <th>Kostenanschlag</th>
                                        <th>Budget (val)</th>
                                        <th>Vergabesumme</th>
                                        <th>Schätzung-Neu</th>
                                        <th>Schätzung-Bestand</th>
                                        <th>Kosten anschlag</th> 
                                        <th>Budget (val)</th>
                                        <th>Vergabesumme</th>
                                        <th>Auftragnehmer</th>
                                        <th>
                                            <i data-bs-toggle='tooltip' data-bs-placement='top' title='Workflow'  class='fas fa-code-branch'></i>
                                        </th>
                                        <th>
                                            <i  data-bs-toggle='tooltip' data-bs-placement='top' title='Notiz'   class='far fa-sticky-note'></i>
                                        </th>
                                        <th>IDLieferant</th>
                                        <th>Abgeschlossen</th>                                                                  
                                        <th>MKF-von_Los</th>    
                                        <th>Historie</th>
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
                            echo '<td><span class="badge rounded-pill ' . getVerfahrenBadgeClass($row['Verfahren']) . '">' . htmlspecialchars($row['Verfahren'] ?? "") . '</span></td>';

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
                            echo "<td><button type='button' id='" . $row["idtabelle_Lose_Extern"] . "' class='btn  btn-sm btn-outline-secondary' value='LotWorkflow' data-bs-toggle='modal' data-bs-target='#workflowDataModal'><i class='fas fa-code-branch'></i></button></td>";


                            // echo "<td>" . $row["Notiz"] . "</td>";

                            $Notiz = trim($row["Notiz"] ?? "");
                            $buttonClass = $Notiz === "" ? "btn-outline-secondary" : "btn-outline-dark";
                            $iconClass = $Notiz === "" ? "fa fa-comment-slash" : "fa fa-comment";
                            $dataAttr = $Notiz === "" ? "data-description=''" : "data-description='" . htmlspecialchars($Notiz, ENT_QUOTES, 'UTF-8') . "'";

                            echo "<td><button type='button'
    class='btn btn-sm " . $buttonClass . " comment-btn' " . $dataAttr . "
    id='" . $row["idtabelle_Lose_Extern"] . "' title='Kommentar'>
    <i class='" . $iconClass . "'></i>
  </button></td>";


                            echo "<td>" . $row["idTABELLE_Lieferant"] . "</td>";
                            echo "<td>" . $row["Vergabe_abgeschlossen"] . "</td>";
                            echo "<td>" . $row["mkf_von_los"] . "</td>";

                            $hauptLose[$row['idtabelle_Lose_Extern']]['idtabelle_Lose_Extern'] = $row['idtabelle_Lose_Extern'];
                            $hauptLose[$row['idtabelle_Lose_Extern']]['LosNr_Extern'] = $row['LosNr_Extern'];
                            $hauptLose[$row['idtabelle_Lose_Extern']]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];

                            echo "<td><button type='button'       class='btn btn-sm btn-outline-secondary'
                                    value='Los Historie'
                                    data-los-id='{$row["idtabelle_Lose_Extern"]}'
                                    data-los-name='" . htmlspecialchars($row["LosBezeichnung_Extern"]) . "'
                                    data-bs-toggle='modal'
                                    data-bs-target='#losHistorieModal'>
                                    <i class='fas fa-history'></i>
                                </button></td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                        $mysqli->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-xxl-1' id='vermerkeCardColumn'>
            <div class='mt-4 card'>
                <div class='card-header' id='vermerkePanelHead'>Vermerke zu Los
                    <button id='toggleVermerkeBtn' class='btn btn-xs float-end'>
                        <i class='fas fa-chevron-left'></i>
                    </button>
                </div>
                <div class='card-body d-none' id='lotVermerke'>
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
</body>
<!-- Modal zum Anlegen eines Loses -->
<div class='modal fade' id='addTenderLotModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Losdaten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form" id="addTenderLotForm">

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
                        <input type='text' class='form-control form-control-sm' id='lotNr' placeholder='Losnummer'
                               required/>
                    </div>
                    <div class='form-group'>
                        <label for='lotName'>Bezeichnung:</label>
                        <input type='text' class='form-control form-control-sm' id='lotName'
                               placeholder='Losbezeichnung' required/>
                    </div>
                    <div class='form-group'>
                        <label for='lotLVSend'>Versand LV:</label>
                        <input type='text' class='form-control form-control-sm' id='lotLVSend'
                               placeholder='jjjj-mm-tt' required/>
                    </div>
                    <div class='form-group'>
                        <label for='lotStart'>Ausführungsbeginn:</label>
                        <input type='text' class='form-control form-control-sm' id='lotStart'
                               placeholder='jjjj-mm-tt' required/>
                    </div>
                    <div class="form-group">
                        <label for="lotVerfahren">Verfahren</label>
                        <select class="form-control form-control-sm" id="lotVerfahren" name="lotVerfahren" required>
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
                            <option value="Andere">Andere - Sags dem Dev ;)</option>
                        </select>
                    </div>

                    <div class='form-group'>
                        <label for='lotLVBearbeiter'>Bearbeiter:</label>
                        <input type='text' class='form-control form-control-sm' id='lotLVBearbeiter'
                               placeholder='Bearbeiter' required/>
                    </div>
                    <div class='form-group'>
                        <label for='kostenanschlag'>Kostenanschlag: (. oder ,)</label>
                        <input type='text' class='form-control form-control-sm' id='kostenanschlag'
                               placeholder='0'/>
                    </div>
                    <div class='form-group'>
                        <label for='budget'>Budget (valorisiert): (. oder ,)</label>
                        <input type='text' class='form-control form-control-sm' id='budget' placeholder='0'/>
                    </div>
                    <div class='form-group'>
                        <label for='lotSum'>Vergabesumme: (. oder ,)</label>
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
                <button type='button' class='btn btn-outline-secondary btn-sm' data-bs-dismiss='modal'>Abbrechen
                </button>
            </div>
        </div>
    </div>
</div>

<?php
include "modal_showLotWorkflow.php";
require "modal_los_aenderungen.php";
?>

<script src="utils/_utils.js"></script>
<!--suppress ES6ConvertVarToLetConst -->
<script charset="utf-8">
    let excelFilename;
    var lotID;
    var lotVerfahren;
    var tableTenderLots;

    $(document).ready(function () {


        getExcelFilename("Lose-im_Projekt")
            .then(filename => {
                // console.log('Generated filename:', filename);
                excelFilename = filename;
                tableTenderLots = new DataTable('#tableTenderLots', {
                    columnDefs: [
                        {
                            targets: [0, 14, 15, 16, 17, 18, 22, 23, 24],
                            visible: false,
                            searchable: false
                        },
                        {
                            targets: [1, 20, 21],
                            sortable: false
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
                            className: "btn btn-outline-secondary bg-light fas fa-file-excel me-2 ms-2 ",
                            title: excelFilename,
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
                        let sourceElements = document.getElementsByClassName("dt-buttons");
                        let targetElement = document.getElementById("LoseCardHeaderSub");
                        Array.from(sourceElements).forEach(function (element) {
                            targetElement.appendChild(element);
                        });
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

                    let verfahrenCell = tableTenderLots.row($(this)).data()[6];
                    let tempDiv = document.createElement('div');
                    tempDiv.innerHTML = verfahrenCell || '';
                    let rawVerfahren = tempDiv.textContent.trim();


                    if (rawVerfahren === "MKF") {
                        $('#lotMKF').bootstrapToggle('enable');
                        $('#lotMKF').bootstrapToggle('on');
                        $('#lotMKF').bootstrapToggle('disable');
                        $("#lotMKFOf").prop('disabled', true);
                    } else {
                        $('#lotMKF').bootstrapToggle('enable');
                        $('#lotMKF').bootstrapToggle('off');
                        $('#lotMKF').bootstrapToggle('disable');
                    }

                    document.getElementById("lotVerfahren").value = rawVerfahren;

                    document.getElementById("lotNr").value = tableTenderLots.row($(this)).data()[2];
                    document.getElementById("lotName").value = tableTenderLots.row($(this)).data()[3];
                    document.getElementById("lotLVSend").value = tableTenderLots.row($(this)).data()[4];
                    document.getElementById("lotStart").value = tableTenderLots.row($(this)).data()[5];
                    document.getElementById("lotLVBearbeiter").value = tableTenderLots.row($(this)).data()[7];
                    document.getElementById("kostenanschlag").value = tableTenderLots.row($(this)).data()[11].replace(/\./g, '');
                    document.getElementById("budget").value = tableTenderLots.row($(this)).data()[12].replace(/\./g, '');
                    document.getElementById("lotSum").value = tableTenderLots.row($(this)).data()[13].replace(/\./g, '');
                    //TODO Auftragnehmeer in modal
                    const htmlString = tableTenderLots.row($(this)).data()[8];
                    const textContent = htmlString.replace(/<[^>]*>/g, '');

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
                    // console.log(tableTenderLots.row($(this)).data());
                    document.getElementById("lotAuftragnehmer").value = tableTenderLots.row($(this)).data()[22];
                    document.getElementById("lotMKFOf").value = tableTenderLots.row($(this)).data()[24];


                    const notizCell = tableTenderLots.row($(this)).data()[21];
                    const tempDivNotiz = document.createElement('div');
                    tempDivNotiz.innerHTML = notizCell || '';
                    const notizBtn = tempDivNotiz.querySelector('button');
                    document.getElementById("lotNotice").value = notizBtn ? (notizBtn.dataset.description || '') : '';


                    $.ajax({
                        url: "getLotVermerke.php",
                        data: {"lotID": lotID},
                        type: "POST",
                        success: function (data) {
                            $("#lotVermerke").html(data);
                            $.ajax({
                                url: "getTenderLotElements.php",
                                data: {"lotID": lotID},
                                type: "POST",
                                success: function (data) {
                                    $("#elementsInLot").html(data);
                                    $("#elementBestand").hide();
                                    $("#elementsvariantenParameterInLot").hide();
                                }
                            });
                        }
                    });
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


        $(document).on('click', 'button[value="Los Historie"]', function () {
            const losID = $(this).data('los-id');
            const losName = $(this).data('los-name');

            $('#losHistorieModalTitle').text(losName);

            $('#losHistorieModalBody').html(`<div class="text-center p-4"><div class="spinner-border" role="status"></div></div>`);

            new bootstrap.Modal(document.getElementById('losHistorieModal')).show();

            $.ajax({
                url: 'get_los_aenderungshistorie.php',
                type: 'POST',
                data: {losID},
                success: function (resp) {
                    const rows = resp.data ?? [];
                    if (!rows.length) {
                        $('#losHistorieModalBody').html(`<div class="alert alert-info">Keine Änderungen gefunden.</div>`);
                        return;
                    }
                    // Sort newest first (ISO string sorts correctly)
                    rows.sort((a, b) => (b[1] ?? '').localeCompare(a[1] ?? ''));

// Remove consecutive rows where timestamp+user+all fields are identical
                    const deduped = rows.filter((row, i) => {
                        if (i === 0) return true;
                        const prev = rows[i - 1];
                        // Compare timestamp, user, and all alt/neu field indices
                        return !(row[1] === prev[1] && row[2] === prev[2] &&
                            [3, 4, 7, 8, 10, 11, 13, 14, 15, 16, 17, 18,
                                19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                                31, 32, 33, 34, 35, 36, 37, 38, 39, 40]
                                .every(idx => String(row[idx] ?? '') === String(prev[idx] ?? '')));
                    });

                    $('#losHistorieModalBody').html(
                        deduped.map(row => `<div class="mb-4">${lh_buildDetailHtml(row)}</div>`).join('<hr class="my-2">')
                    );
                },
                error: function () {
                    $('#losHistorieModalBody').html(`<div class="alert alert-danger">Fehler beim Laden der Historie.</div>`);
                }
            });
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
            lotMKFOf: document.getElementById("lotMKFOf") ? document.getElementById("lotMKFOf").value : "0"
        };

        if (formData.lotMKFOf === "0") {
            if (formData.losNr && formData.losName && formData.losDatum &&
                formData.lotLVSend && formData.lotVerfahren && formData.lotLVBearbeiter) {
                sendData(formData);
            } else {
                alert("Bitte alle Felder außer der Vergabesumme und Auftragnehmer ausfüllen!");
            }
        } else {
            if (formData.losDatum && formData.lotLVSend &&
                formData.lotLVBearbeiter) {
                sendData(formData);
            } else {
                alert("Für MKF bitte alle Felder außer Vergabesumme und Auftragnehmer ausfüllen!");
            }
        }
    });

    function sendData(formData) {
        const modal = document.getElementById('addTenderLotModal');
        if (modal) {
            modal.style.display = 'none';
            if (typeof $ === 'function' && $('#addTenderLotModal').modal) {
                $('#addTenderLotModal').modal('hide');
            }
        }
        fetch("addTenderLot.php", {
            method: "POST",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: new URLSearchParams(formData).toString()
        })
            .then(response => response.text())
            .then(data => {
                makeToaster(data, true);
                location.reload();
            })
            .catch(error => {
                makeToaster("Fehler beim Speichern: " + error, false);
                location.reload();
            });

    }


    $("#saveTenderLot").click(function () {
        let form = document.querySelector('#addTenderLotForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        let losNr = $("#lotNr").val();
        let losName = $("#lotName").val();
        let losDatum = $("#lotStart").val();
        let lotLVSend = $("#lotLVSend").val();
        let kostenanschlag = $("#kostenanschlag").val();
        kostenanschlag = normalizeCosts(kostenanschlag);
        let budget = $("#budget").val();
        budget = normalizeCosts(budget);
        let lotSum = $("#lotSum").val();
        lotSum = normalizeCosts(lotSum);
        let lotVergabe = $("#lotVergabe").val();
        let lotNotice = $("#lotNotice").val();
        let lotAuftragnehmer = $("#lotAuftragnehmer").val();
        let lotVerfahren = $("#lotVerfahren").val();
        let lotLVBearbeiter = $("#lotLVBearbeiter").val();

        if ($("#lotMKF").prop('checked') === false) {
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
                type: "POST",
                success: function (data) {
                    alert(data);
                    window.location.replace("roombook_tenderLots.php");
                }
            });
        } else {
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
                type: "POST",
                success: function (data) {
                    alert(data);
                    window.location.replace("roombook_tenderLots.php");
                }
            });
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
        window.open('PDFs/pdf_createTenderLotElementListPDF.php');
    });

    $('#createTenderListWithoutElementsPDF').click(function () {
        window.open('PDFs/pdf_createTenderLotElementListWithoutElementsPDF.php');
    });

    $('#createTenderWorkflowPDF').click(function () {
        window.open('PDFs/pdf_createTenderWorkflowPDF.php');
    });

    $(document).on("click", "button[value='LotWorkflow']", function () {
        var ID = this.id;
        $.ajax({
            url: "getLotWorkflow.php",
            type: "POST",
            data: {lotID: ID},
            success: function (data) {
                $("#workflowModalBody").html(data);
            }
        });
    });

    $("button[value='reloadBestand']").click(function () {
        $("#elementBestand").html("");
        $.ajax({
            url: "getElementBestand.php",
            type: "POST",
            success: function (data) {
                makeToaster("Reloaded!", true);
                $("#elementBestand").html(data);
            }
        });
    });

    $('#toggleVermerkeBtn').click(function () {

        $('#mainCardColumn').toggleClass('col-xxl-8 col-xxl-11');
        $('#vermerkeCardColumn').toggleClass('col-xxl-4 col-xxl-1');
        $('#lotVermerke').toggleClass('d-none');
        $(this).find('i').toggleClass('fa-chevron-right fa-chevron-left');
    });


    const LH_FIELDS = [
        {label: 'Los Bezeichnung', altIdx: 4, neuIdx: 3, badgeCls: 'bg-primary'},
        {label: 'Element', altIdx: 7, neuIdx: 8, badgeCls: 'bg-primary'},
        {label: 'Raum', altIdx: 10, neuIdx: 11, badgeCls: 'bg-primary'},
        {label: 'Status', altIdx: 13, neuIdx: 14, badgeCls: 'bg-warning text-dark'},
        {label: 'Lieferdatum', altIdx: 15, neuIdx: 16, badgeCls: 'bg-info text-dark'},
        {label: 'Budget-Position', altIdx: 17, neuIdx: 18, badgeCls: 'bg-success'},
        {label: 'Anzahl', altIdx: 19, neuIdx: 20, badgeCls: 'bg-secondary'},
        {label: 'Kurzbeschreibung', altIdx: 21, neuIdx: 22, badgeCls: 'bg-dark'},
        {label: 'Neu/Bestand', altIdx: 23, neuIdx: 24, badgeCls: 'bg-dark'},
        {label: 'Standort', altIdx: 25, neuIdx: 26, badgeCls: 'bg-dark'},
        {label: 'Verwendung', altIdx: 27, neuIdx: 28, badgeCls: 'bg-dark'},
        {label: 'Anschaffung', altIdx: 29, neuIdx: 30, badgeCls: 'bg-success'},
        {label: 'Internes Los', altIdx: 31, neuIdx: 32, badgeCls: 'bg-danger'},
        {label: 'GHG', altIdx: 33, neuIdx: 34, badgeCls: 'bg-danger'},
        {label: 'GUG', altIdx: 35, neuIdx: 36, badgeCls: 'bg-danger'},
        {label: 'Gewerk', altIdx: 37, neuIdx: 38, badgeCls: 'bg-danger'},
        {label: 'Variante', altIdx: 39, neuIdx: 40, badgeCls: 'bg-light text-dark border'},
    ];

    const lh_norm = v => (v === null || v === undefined || v === '') ? '' : String(v);

    const lh_statusBadges = {
        '0': "<span class='badge bg-danger'>Offen</span>",
        '1': "<span class='badge bg-success'>Fertig</span>",
        '2': "<span class='badge bg-primary'>Wartend</span>",
    };
    const lh_variantLabels = {'1': 'A', '2': 'B', '3': 'C', '4': 'D', '5': 'E'};

    function lh_renderVal(f, raw) {
        const v = lh_norm(raw);
        if (f.altIdx === 13) return lh_statusBadges[v] ?? raw;
        if (f.altIdx === 23) return v === '1' ? 'Ja' : v === '0' ? 'Nein' : raw;
        if (f.altIdx === 39) return lh_variantLabels[v] ?? raw;
        return raw;
    }

    function lh_smartLabel(f, row) {
        const a = lh_norm(row[f.altIdx]), n = lh_norm(row[f.neuIdx]);
        if (f.altIdx === 4) {
            if (a === '' && n !== '') return 'Zu Los hinzugefügt';
            if (a !== '' && n === '') return 'Von Los entfernt';
        }
        if (f.altIdx === 19) {
            if ((a === '' || a === '0') && n !== '' && n !== '0') return 'Element hinzugefügt';
            if (a !== '' && a !== '0' && (n === '' || n === '0')) return 'Element entfernt';
        }
        return f.label;
    }

    function lh_badges(row) {
        const changed = LH_FIELDS.filter(f => lh_norm(row[f.altIdx]) !== lh_norm(row[f.neuIdx]));
        if (!changed.length) return '<span class="text-muted small">–</span>';
        return changed.map(f =>
            `<span class="badge rounded-pill ${f.badgeCls} me-1" style="font-size:0.7em">${lh_smartLabel(f, row)}</span>`
        ).join('');
    }

    function lh_buildDetailHtml(row) {
        const d = row;
        const changed = LH_FIELDS.filter(f => lh_norm(d[f.altIdx]) !== lh_norm(d[f.neuIdx]));
        const unchanged = LH_FIELDS.filter(f =>
            lh_norm(d[f.altIdx]) === lh_norm(d[f.neuIdx]) &&
            lh_norm(d[f.altIdx]) !== '' &&
            f.altIdx !== 7 && f.altIdx !== 10  // Element/Raum already in Zuordnung
        );

        const disp = v => (v !== null && v !== undefined && v !== '') ? String(v) : null;
        const buildRow = (label, alt, neu) => {
            const a = disp(alt), n = disp(neu);
            const changed = (a ?? '') !== (n ?? '');
            if (a === null && n === null) return '';
            const aH = a ?? '<em class="text-muted">–</em>';
            const nH = n ?? '<em class="text-muted">–</em>';
            if (neu === undefined) return `<tr><td class="fw-semibold text-muted small" colspan="2">${label}: ${aH}</td></tr>`;
            const badge = changed ? `<span class="badge bg-warning text-dark ms-1" style="font-size:0.6em">geändert</span>` : '';
            const nRender = changed ? `<span class="text-success fw-bold">${nH}</span>` : nH;
            return `<tr class="${changed ? 'table-warning' : ''}">
                <td class="fw-semibold small">${label}${badge}</td>
                <td>${aH}</td><td>${nRender}</td></tr>`;
        };

        // Format ISO timestamp for display
        const tsDisplay = (() => {
            if (!d[1]) return '–';
            const [datePart, timePart = ''] = d[1].split(' ');
            const [y, m, dd] = datePart.split('-');
            return `${dd}.${m}.${y} ${timePart}`;
        })();

        let html = `<div class="border rounded p-2 mb-3 bg-light">
                        <span class="me-3"><i class="fas fa-user me-1 text-muted"></i><strong>${d[2] ?? '–'}</strong></span>
                        <span class="me-3"><i class="fas fa-clock me-1 text-muted"></i>${tsDisplay}</span>
                        <span class="text-muted small">ID: <code>${d[0]}</code></span>
                        <div class="mt-1">${lh_badges(row)}</div>
                       </div>`;

        html += `<table class="table table-sm table-bordered mb-0">
                    <thead class="table-dark"><tr>
                        <th style="width:28%">Feld</th><th>Alt</th>
                        <th>Neu <span class="badge bg-warning text-dark" style="font-size:0.6em">geändert</span></th>
                    </tr></thead><tbody>`;

        // Zuordnung
        html += `<tr class="table-secondary"><td colspan="3" class="fw-bold small py-1">
                       <i class="fas fa-map-marker-alt me-1"></i>Zuordnung</td></tr>`;
        html += buildRow('Aktuelles Los', d[3]);
        html += buildRow('Element', d[9]);
        html += buildRow('Raum', d[12]);

        // Geändert
        html += `<tr class="table-secondary"><td colspan="3" class="fw-bold small py-1">
                    <i class="fas fa-edit me-1"></i>Geändert
                    <span class="badge bg-danger ms-1" style="font-size:0.6em">${changed.length}</span>
                    </td></tr>`;
        if (changed.length) {
            changed.forEach(f => html += buildRow(f.label, lh_renderVal(f, d[f.altIdx]), lh_renderVal(f, d[f.neuIdx])));
        } else {
            html += `<tr><td colspan="3" class="text-muted fst-italic small">Kein bekanntes Feld hat sich geändert.</td></tr>`;
        }

        // Kontext
        if (unchanged.length) {
            html += `<tr class="table-secondary"><td colspan="3" class="fw-bold small py-1">
                  <i class="fas fa-info-circle me-1"></i>Kontext (unverändert)</td></tr>`;
            unchanged.forEach(f => {
                html += `<tr>
                <td class="small fw-semibold" style="color:#999">${f.label}</td>
                <td colspan="2" class="small" style="color:#999">${lh_renderVal(f, d[f.altIdx])}</td></tr>`;
            });
        }

        html += '</tbody></table>';
        return html;
    }
</script>
</html>
