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
    <style>
        td, th {
            style = "text-align:center;"
        }

    </style>

</head>
<body id="bodyTenderLots">
<?php
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
init_page_serversides("x");

?>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class='row'>
        <div class='col-xxl-12' id="mainCardColumn">
            <div class="mt-4 card">

                <div class="card-header d-inline-flex justify-content-start align-items-center">
                    <div class="d-inline-flex align-items-center">
                        <span> <strong>Lose im Projekt</strong>  &emsp;</span>
                    </div>

                    <div class="d-inline-flex align-items-center" id="LoseCardHeaderSub">

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
                                    return 'bg-success';
                                case 'Offenes Verfahren':
                                    return 'bg-danger';
                                case 'MKF':
                                    return "bg-danger";
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

                        // Abfrage der externen Lose

                        $sql = "
                            SELECT 
                                tabelle_lose_extern.idtabelle_Lose_Extern,
                                tabelle_lose_extern.LosNr_Extern, 
                                tabelle_lose_extern.LosBezeichnung_Extern, 
                                tabelle_lose_extern.Versand_LV, 
                                tabelle_lose_extern.Ausführungsbeginn, 
                                tabelle_lose_extern.Verfahren, 
                                tabelle_lose_extern.mkf_von_los,
                              #  tabelle_lose_extern.Bearbeiter, 
                                tabelle_lose_extern.Vergabesumme, 
                                tabelle_lose_extern.Vergabe_abgeschlossen, 
                              #  tabelle_lose_extern.Notiz, 
                                tabelle_lose_extern.Kostenanschlag, 
                              #  tabelle_lose_extern.Budget,
                                tabelle_lieferant.Lieferant, 
                                tabelle_lieferant.idTABELLE_Lieferant,
                                tabelle_projekte.Projektname,
                              #  losschaetzsumme.Summe,
                                # losbestandschaetzsumme.SummeBestand,
                                mkf_los.LosNr_Extern AS mkf_losnummer  -- NEU: Losnummer des MKF-Quell-Loses
                            FROM tabelle_lieferant 
                            RIGHT JOIN tabelle_lose_extern 
                                ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
                            LEFT JOIN
                                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, 
                                        Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS Summe,
                                        tabelle_räume.tabelle_projekte_idTABELLE_Projekte
                                 FROM tabelle_räume 
                                 INNER JOIN (tabelle_projekt_varianten_kosten 
                                     INNER JOIN tabelle_räume_has_tabelle_elemente 
                                     ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten 
                                     AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
                                 ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte 
                                 AND tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                                 WHERE tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1 
                                   AND tabelle_räume_has_tabelle_elemente.Standort=1 
                                 GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern,
                                          tabelle_räume.tabelle_projekte_idTABELLE_Projekte
                                ) AS losschaetzsumme ON tabelle_lose_extern.idtabelle_Lose_Extern = losschaetzsumme.id
                            LEFT JOIN 
                                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, 
                                        Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS SummeBestand,
                                        tabelle_räume.tabelle_projekte_idTABELLE_Projekte
                                 FROM tabelle_räume 
                                 INNER JOIN (tabelle_projekt_varianten_kosten 
                                     INNER JOIN tabelle_räume_has_tabelle_elemente 
                                     ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten 
                                     AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
                                 ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte 
                                 AND tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                                 WHERE tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0 
                                   AND tabelle_räume_has_tabelle_elemente.Standort=1 
                                 GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern,
                                          tabelle_räume.tabelle_projekte_idTABELLE_Projekte
                                ) AS losbestandschaetzsumme ON tabelle_lose_extern.idtabelle_Lose_Extern = losbestandschaetzsumme.id
                            LEFT JOIN tabelle_projekte ON tabelle_projekte.idTABELLE_Projekte = COALESCE(losschaetzsumme.tabelle_projekte_idTABELLE_Projekte, losbestandschaetzsumme.tabelle_projekte_idTABELLE_Projekte)
                            LEFT JOIN tabelle_lose_extern AS mkf_los ON tabelle_lose_extern.mkf_von_los = mkf_los.idtabelle_Lose_Extern  -- NEU: MKF-Bezug
                            ORDER BY tabelle_projekte.Projektname, tabelle_lose_extern.LosNr_Extern;
    ";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->get_result();


                        echo "<table  id='tableTenderLots' class='table table-sm table-responsive table-striped compact border border-light border-1'>
								<thead><tr>
								<th>ID</th>
                                <th>Projekt</th>  
                                 <th>LosNummer</th>
                                <th>Bezeichnung</th>                                       
                                <th>Versand</th>
                                <th>Liefertermin</th>
                                <th >Verfahren</th>
                                <th>Status</th>
                                <th>Vergabesumme</th>
                                <th>Vergabesumme</th>
                                <th>Auftragnehmer</th>                       
                                <th>MKF-von_Los</th>    
                       
                                <th>
                                        <i data-bs-toggle='tooltip' data-bs-placement='top' title='Workflow'  class='fas fa-code-branch'></i>
                                </th>
								</tr></thead>";
                        echo "<tbody>";


                        $hauptLose = array();
                        while ($row = $result->fetch_assoc()) {
                            if (empty($row["Projektname"])
                                || $row["Projektname"] === "Test_Projekt"
                                || stripos($row["LosBezeichnung_Extern"] ?? "", "löschen")
                                || stripos($row["LosBezeichnung_Extern"] ?? "", "ENTFÄLLT")
                                || stripos($row["LosBezeichnung_Extern"] ?? "", "Entfallen")
                                || empty($row["Verfahren"])
                            ) {
                                continue;
                            }
                            echo "<tr>";
                            echo "<td>" . $row["idtabelle_Lose_Extern"] . "</td>";
                            echo "<td>" . $row["Projektname"] . "</td>";
                            echo "<td>" . $row["LosNr_Extern"] . "</td>";
                            echo "<td>" . $row["LosBezeichnung_Extern"] . "</td>";
                            echo "<td>" . $row["Versand_LV"] . "</td>";
                            echo "<td>" . $row["Ausführungsbeginn"] . "</td>";
                            echo '<td> <span class="badge rounded-pill ' . getVerfahrenBadgeClass($row['Verfahren']) . '">' . htmlspecialchars($row['Verfahren'] ?? "") . '</span></td>';
                            echo "<td>";
                            switch ($row["Vergabe_abgeschlossen"]) {
                                case 0:
                                    echo "<span class='badge badge-pill bg-danger'>Offen</span>";
                                    break;
                                case 1:
                                    echo "<span class='badge badge-pill bg-success'>Fertig</span>";
                                    break;
                                case 2:
                                    echo "<span class='badge badge-pill bg-primary'>Wartend</span>";
                                    break;
                            }
                            echo "</td>";
                            echo "<td>" . format_money($row["Vergabesumme"]) . "</td>";
                            echo "<td>" . $row["Vergabesumme"] . "</td>";
                            echo "<td>" . $row["Lieferant"] . "</td>";
                            echo "<td>" . $row["mkf_losnummer"] . "</td>";
                            echo "<td><button type='button' id='" . $row["idtabelle_Lose_Extern"] . "' class='btn  btn-sm btn-outline-secondary' value='LotWorkflow' data-bs-toggle='modal' data-bs-target='#workflowDataModal'><i class='fas fa-history'></i></button></td>";

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
    </div>
</body>

<?php
require_once "modal_showLotWorkflow.php";
?>

<script src="utils/_utils.js"></script>
<script charset="utf-8">
    var tableTenderLots;
    $(document).ready(function () {
        tableTenderLots = new DataTable('#tableTenderLots', {
            columnDefs: [
                {
                    targets: [0, 9], // in excel code unten auch anpassen
                    visible: false,
                    searchable: false
                },
                {
                    targets: [5, 10, 11],
                    visible: false,
                },
                {
                    targets: [0],
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
            pageLength: 100,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.',
                searchPlaceholder: 'Suche..',
                search: ""
            },
            buttons: [
                {
                    extend: 'colvis',
                    className: "btn btn-success fas fa-eye me-2 ms-2",
                    text: 'Spaltensichtbarkeit',
                    columns: function (idx) {
                        return idx !== 0 && idx !== 9;  // Index 0=ID, Index 8= visuell formatierte.Vergabesumme
                    }
                },
                {
                    extend: 'excel',
                    className: "btn btn-success fas fa-file-excel me-2 ms-2 ",
                    title: "Losliste",
                    exportOptions: {
                        columns: function (idx) {
                            return idx !== 0 && idx !== 8;
                        }
                    }
                },
                {
                    extend: 'searchBuilder',
                    className: "btn btn-success fas fa-filter me-2 ms-2",
                    //  config: { columns: [1, 2, 3, 4, 5, 6, 8, 10, 11] // Projekt, Bezeichnung, Versand, etc. }
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


                $("button[value='LotWorkflow']").click(function () {
                    var ID = this.id;
                    $.ajax({
                        url: "getLotWorkflow.php",
                        type: "POST",
                        data: {"lotID": ID},
                        success: function (data) {
                            $("#workflowModalBody").html(data);
                        }
                    });
                });

            }
        });
    });

</script>
</html>
