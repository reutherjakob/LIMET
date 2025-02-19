<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html" lang="de">
<head>
    <title>RB-Ausschreibungskalender</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">


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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/css/bootstrap-datepicker.min.css">
    <style>
        .card-body {
            overflow: scroll;
        }

        .form-control-sm {
            width: 95px;
        !important;
        }

        .btn-sm {
            width: 95px;
            height: 10px;
            vertical-align: middle;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<?php
include '_utils.php';
init_page_serversides();
?>

<body id="bodyTenderLots">
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mt-4 card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <?php
                        $mysqli = utils_connect_sql();

                        $sql = "SELECT tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow, tabelle_workflow.Name
                                    FROM tabelle_workflow INNER JOIN (tabelle_lose_extern INNER JOIN tabelle_lot_workflow ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern) ON tabelle_workflow.idtabelle_workflow = tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow
                                    WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                    GROUP BY tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow, tabelle_workflow.Name;";
                        $result = $mysqli->query($sql);
                        $counter = 1;
                        $workflows = array();
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='nav-item' role='presentation'>";
                            if ($counter == 1) {
                                echo "<button class='nav-link active' id='tab-" . $row["tabelle_workflow_idtabelle_workflow"] . "-tab' data-bs-toggle='tab' data-bs-target='#tab-" . $row["tabelle_workflow_idtabelle_workflow"] . "' type='button' role='tab' aria-controls='tab-" . $row["tabelle_workflow_idtabelle_workflow"] . "' aria-selected='true'>" . $row["Name"] . "</button>";
                            } else {
                                echo "<button class='nav-link' id='tab-" . $row["tabelle_workflow_idtabelle_workflow"] . "-tab' data-bs-toggle='tab' data-bs-target='#tab-" . $row["tabelle_workflow_idtabelle_workflow"] . "' type='button' role='tab' aria-controls='tab-" . $row["tabelle_workflow_idtabelle_workflow"] . "' aria-selected='false'>" . $row["Name"] . "</button>";
                            }
                            echo "</li>";
                            $workflows[$counter] = $row["tabelle_workflow_idtabelle_workflow"];
                            $counter++;
                        }
                        ?>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <?php
                        $counter = 1;
                        foreach ($workflows as $workFlow) {
                            if ($counter == 1) {
                                echo "<div class='tab-pane fade show active' id='tab-" . $workFlow . "' role='tabpanel' aria-labelledby='tab-" . $workFlow . "-tab'>";
                            } else {
                                echo "<div class='tab-pane fade' id='tab-" . $workFlow . "' role='tabpanel' aria-labelledby='tab-" . $workFlow . "-tab'>";
                            }
                            // -----------------Workflowteile eines Workflows laden----------------------------
                            $sql = "SELECT tabelle_workflowteil.idtabelle_wofklowteil, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, tabelle_workflowteil.aufgabe, tabelle_workflow_has_tabelle_wofklowteil.TageMinDanach
                                        FROM tabelle_workflowteil INNER JOIN tabelle_workflow_has_tabelle_wofklowteil ON tabelle_workflowteil.idtabelle_wofklowteil = tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil
                                        WHERE (((tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow)=" . $workFlow . "))
                                        ORDER BY tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer;";

                            $result1 = $mysqli->query($sql);
                            $workflowTeile = array();

                            while ($row = $result1->fetch_assoc()) {
                                $workflowTeile[$row['idtabelle_wofklowteil']]['idtabelle_wofklowteil'] = $row['idtabelle_wofklowteil'];
                                $workflowTeile[$row['idtabelle_wofklowteil']]['Reihenfolgennummer'] = $row['Reihenfolgennummer'];
                                $workflowTeile[$row['idtabelle_wofklowteil']]['aufgabe'] = $row['aufgabe'];
                                $workflowTeile[$row['idtabelle_wofklowteil']]['TageMinDanach'] = $row['TageMinDanach'];
                            }
                            //-----------------------------------------------------------------------------------

                            echo "<table id='table_" . $workFlow . "' class='table table-striped table-bordered table-compact table-hover table-responsive'   '>
                                <thead><tr>
                                <th rowspan='2'>lotID</th>
                                <th rowspan='2'>Nummer</th>
                                <th rowspan='2'>Bezeichnung</th>
                                <th rowspan='2'>Verfahren</th>
                                <th rowspan='2'>Status</th>
                                <th rowspan='2'></th>";
                            $counterWorkFlowTeile = 0;
                            foreach ($workflowTeile as $array) {
                                $counterWorkFlowTeile++;
                                if ($counterWorkFlowTeile === count($workflowTeile)) {
                                    echo "<th colspan='2'>" . $array['Reihenfolgennummer'] . "-" . $array['aufgabe'] . "</th>";
                                } else {
                                    echo "<th colspan='3'>" . $array['Reihenfolgennummer'] . "-" . $array['aufgabe'] . "</th>";
                                }
                            }
                            echo "</tr>";

                            echo "<tr>";
                            $counterWorkFlowTeile = 0;
                            foreach ($workflowTeile as $array) {
                                echo "<th>Soll-Datum</th>
                                    <th>Ist-Datum</th>";
                                $counterWorkFlowTeile++;
                                if ($counterWorkFlowTeile < count($workflowTeile)) {
                                    echo "<th>Abstand</th>";
                                }
                            }
                            echo "</tr></thead><tbody>";

                            $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Verfahren, tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil, DATE_FORMAT(DATE(tabelle_lot_workflow.Timestamp_Ist), '%Y-%m-%d') as ISTDATE, DATE_FORMAT(DATE(tabelle_lot_workflow.Timestamp_Soll), '%Y-%m-%d') as SOLLDATE, tabelle_lot_workflow.Abgeschlossen, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow
                                                FROM tabelle_workflow_has_tabelle_wofklowteil INNER JOIN (tabelle_lose_extern INNER JOIN tabelle_lot_workflow ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern) ON (tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow = tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow) AND (tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil)
                                                WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow)=" . $workFlow . "))
                                        ORDER BY tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer;";

                            $result = $mysqli->query($sql);
                            $idLot = 0;
                            $sollDatumAlt = "0000-00-00";

                            while ($row = $result->fetch_assoc()) {
                                if ($idLot != $row["idtabelle_Lose_Extern"]) {
                                    echo "<tr>";
                                    echo "<td>" . $row["idtabelle_Lose_Extern"] . "</td>";
                                    echo "<td>" . $row["LosNr_Extern"] . "</td>";
                                    echo "<td>" . $row["LosBezeichnung_Extern"] . "</td>";
                                    echo "<td>" . $row["Verfahren"] . "</td>";
                                    echo "<td>";
                                    switch ($row["Vergabe_abgeschlossen"]) {
                                        case 0:
                                            //echo "<b><font color='red'>&#10007;</font></b>";
                                            echo "<span class='badge bg-danger'>Offen</span>";
                                            break;
                                        case 1:
                                            //echo "<b><font color='green'>&#10003;</font></b>";
                                            echo "<span class='badge bg-success'>Fertig</span>";
                                            break;
                                        case 2:
                                            //echo "<b><font color='blue'>&#8776;</font></b>";
                                            echo "<span class='badge bg-primary'>Wartend</span>";
                                            break;
                                    }
                                    echo "</td>";
                                    echo "<td><button type='button' id='" . $row["idtabelle_Lose_Extern"] . "' class='btn btn-sm' value='calculateDates' data-bs-toggle='modal' data-bs-target='#claculateDatesModal'>Berechnen <i class='far fa-calendar-check'></i></button>"
                                        . "</td>";

                                    if ($row["SOLLDATE"] == "0000-00-00") {
                                        echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm'   id='SOLLDATE-" . $row["idtabelle_Lose_Extern"] . "-" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "-" . $row["tabelle_workflow_idtabelle_workflow"] . "'/>-"
                                            . "<button type='button' name='save_solldate' id='SAVE-SOLLDATE," . $row["idtabelle_Lose_Extern"] . "," . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "," . $row["tabelle_workflow_idtabelle_workflow"] . "' class='btn btn-outline-dark btn-sm'><i class='far fa-save'></i></button>"
                                            . "</form>"
                                            . "</td>";
                                    } else {
                                        echo "<td><form class='form-inline form-check-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm'   id='SOLLDATE-" . $row["idtabelle_Lose_Extern"] . "-" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "-" . $row["tabelle_workflow_idtabelle_workflow"] . "' value='" . $row["SOLLDATE"] . "'/>"
                                            . "<button type='button' name='save_solldate' id='SAVE-SOLLDATE," . $row["idtabelle_Lose_Extern"] . "," . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "," . $row["tabelle_workflow_idtabelle_workflow"] . "' class='btn btn-outline-dark btn-sm'><i class='far fa-save'></i></button>"
                                            . "</form>"
                                            . "<span style='display:none'>" . $row["SOLLDATE"] . "</span></td>";
                                    }

                                } else {

                                    $daysBetween = round((strtotime($row["SOLLDATE"] ?? '') - strtotime($sollDatumAlt ?? '')) / (60 * 60 * 24));
                                    if ($daysBetween >= $sollAbstandDanach) {
                                        echo "<td style='text-align:center'><span class='badge bg-success'>" . $daysBetween . "</span> / <span class='badge bg-secondary'>" . $sollAbstandDanach . "</span></td>";
                                    } else {
                                        echo "<td style='text-align:center'><span class='badge bg-danger'>" . $daysBetween . "</span> / <span class='badge bg-secondary'>" . $sollAbstandDanach . "</span></td>";
                                    }

                                    if ($row["SOLLDATE"] == "0000-00-00") {
                                        echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm'   id='SOLLDATE-" . $row["idtabelle_Lose_Extern"] . "-" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "-" . $row["tabelle_workflow_idtabelle_workflow"] . "'/>"
                                            . "<button type='button' name='save_solldate' id='SAVE-SOLLDATE," . $row["idtabelle_Lose_Extern"] . "," . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "," . $row["tabelle_workflow_idtabelle_workflow"] . "' class='btn btn-outline-dark btn-sm'><i class='far fa-save'></i></button>"
                                            . "</form></td>";
                                    } else {
                                        echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm'   id='SOLLDATE-" . $row["idtabelle_Lose_Extern"] . "-" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "-" . $row["tabelle_workflow_idtabelle_workflow"] . "' value='" . $row["SOLLDATE"] . "'/>"
                                            . "<button type='button' name='save_solldate' id='SAVE-SOLLDATE," . $row["idtabelle_Lose_Extern"] . "," . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "," . $row["tabelle_workflow_idtabelle_workflow"] . "' class='btn btn-outline-dark btn-sm'><i class='far fa-save'></i></button>"
                                            . "</form>"
                                            . "<span style='display:none'>" . $row["SOLLDATE"] . "</span></td>";
                                    }

                                }


                                if ($row["ISTDATE"] == "0000-00-00") {
                                    echo "<td><form class='form-inline'>"
                                        . "<input type='text' name='input_solldate' class='form-control form-control-sm'   id='ISTDATE-" . $row["idtabelle_Lose_Extern"] . "-" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "-" . $row["tabelle_workflow_idtabelle_workflow"] . "'/>"
                                        . "<button type='button' name='save_istdate' id='SAVE-ISTDATE," . $row["idtabelle_Lose_Extern"] . "," . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "," . $row["tabelle_workflow_idtabelle_workflow"] . "' class='btn btn-outline-dark btn-sm'><i class='far fa-save'></i></button>"
                                        . "</form></td>";
                                } else {
                                    echo "<td><form class='form-inline'>"
                                        . "<input type='text' name='input_istdate' class='form-control form-control-sm'   id='ISTDATE-" . $row["idtabelle_Lose_Extern"] . "-" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "-" . $row["tabelle_workflow_idtabelle_workflow"] . "' value='" . $row["ISTDATE"] . "'/>"
                                        . "<button type='button' name='save_istdate' id='SAVE-ISTDATE," . $row["idtabelle_Lose_Extern"] . "," . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "," . $row["tabelle_workflow_idtabelle_workflow"] . "' class='btn btn-outline-dark btn-sm'><i class='far fa-save'></i></button>"
                                        . "</form>"
                                        . "<span style='display:none'>" . $row["ISTDATE"] . "</span></td>";
                                }

                                $idLot = $row["idtabelle_Lose_Extern"];
                                $sollDatumAlt = $row["SOLLDATE"];
                                $sollAbstandDanach = $workflowTeile[$row["tabelle_wofklowteil_idtabelle_wofklowteil"]]['TageMinDanach'];

                            }

                            echo "</tr>";
                            echo "</tbody></table>";
                            echo "</div>";
                            $counter++;
                        }
                        $mysqli->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class='row'>
        <div class='col-sm-12'>
            <div class="mt-4 card">
                <div class="card-header">Bauphasen im Los
                </div>
                <div class="card-body" id="lotBauphasen">
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<div class="modal fade" id="claculateDatesModal" tabindex="-1" aria-labelledby="claculateDatesModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="claculateDatesModalLabel">Daten automatisch berechnen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="mbody">
                Wollen Sie die Soll-Daten automatisiert berechnen und bestehende Werte überschreiben?
            </div>
            <div class="modal-footer">
                <button type="button" id="updateTenderWorkflowDates" class="btn btn-success btn-sm" data-lot-id=""
                        data-bs-dismiss="modal">Ja
                </button>
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Nein</button>
            </div>
        </div>
    </div>
</div>
<script>

    document.addEventListener('DOMContentLoaded', function () {
        initializeDataTable();
        setupRowClickHandler();
        initializeDatePickers();
        setupAutomatedDateUpdate();
        setupIndividualDateUpdate();
        document.querySelectorAll('button[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function () {
                const lotId = this.getAttribute('id');
                document.getElementById('updateTenderWorkflowDates').setAttribute('data-lot-id', lotId);
            });
        });
    });

    function initializeDataTable() {
        document.querySelectorAll('table.table').forEach(function (table) {
            new DataTable(table, {
                select: true,
                searching: true,
                paging: false,
                lengthChange: false,
                order: [[1, "asc"]],
                orderMulti: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json", search: ""
                },
                columnDefs: [
                    {
                        targets: [0],
                        visible: false,
                        searchable: false
                    }
                ],
                //            dom: '<"d-flex justify-content-between"<B><f>>ti',
                layout: {
                    topStart: null,
                    topEnd: ['buttons', 'search'],
                    bottomStart: null,
                    bottomEnd: 'info'
                },
                buttons: ['excel', 'pdf'],
                stateSave: true
            });
        });
    }

    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function () {
            DataTable.tables({visible: true, api: true}).columns.adjust();
        });
    });

    function setupRowClickHandler() {
        document.querySelectorAll('table.table tbody').forEach(function (tbody) {
            tbody.addEventListener('click', function (event) {
                const tr = event.target.closest('tr');
                if (tr) {
                    const table = DataTable.tables({visible: true, api: true});
                    const lotId = table.row(tr).data()[0];
                    loadLotDetails(lotId);
                }
            });
        });
    }

    function loadLotDetails(lotId) {
        fetch(`getBauphasenToLot.php?lotID=${lotId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById("lotBauphasen").innerHTML = data;
            });
    }

    function initializeDatePickers() {
        document.querySelectorAll("input[name='input_solldate'], input[name='input_istdate']").forEach(function (input) {
            $(input).datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayBtn: "linked",
                clearBtn: true,
                language: "de"
            });
        });
    }


    function setupAutomatedDateUpdate() {
        document.getElementById("updateTenderWorkflowDates").addEventListener('click', function () {
            const lotId = this.dataset.lotId; // or use this.getAttribute('data-lot-id')
            console.log(lotId);

            fetch(`updateTenderWorkflowDates.php?lotID=${lotId}`)
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                });
        });
    }

    function setupIndividualDateUpdate() {
        document.querySelectorAll("button[name='save_solldate'], button[name='save_istdate']").forEach(function (button) {
            button.addEventListener('click', function () {
                const idParts = this.id.split(",");
                const dateType = this.name.includes('soll') ? 'SOLLDATE' : 'ISTDATE';
                const date = document.getElementById(`${dateType}-${idParts[1]}-${idParts[2]}-${idParts[3]}`).value;
                const url = this.name.includes('soll') ? 'updateTenderWorkflowDate.php' : 'updateTenderWorkflowDateIST.php';
                updateDate(url, idParts, date);
            });
        });
    }

    function updateDate(url, idParts, date) {
        fetch(`${url}?lotID=${idParts[1]}&workflowTeilID=${idParts[2]}&workflowID=${idParts[3]}&date=${date}`)
            .then(response => response.text())
            .then(data => {
                alert(data);
            });
    }
</script>
</html>