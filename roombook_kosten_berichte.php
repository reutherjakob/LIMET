<?php
// 26 FX
require_once '_utils/_utils.php';
init_page_serversides();

$h = static fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
$projectID = (int)$_SESSION['projectID'];
$mysqli = utils_connect_sql();

// Anzahl noch nicht freigegebener Budgets (weder freigegeben = 1 noch abgelehnt = 2)
$stmt = $mysqli->prepare("SELECT COUNT(*) AS n FROM tabelle_projektbudgets
                          WHERE tabelle_projekte_idTABELLE_Projekte = ?
                            AND NOT (status <=> 1) AND NOT (status <=> 2)");
$stmt->bind_param('i', $projectID);
$stmt->execute();
$anzahlNichtFreigegeben = (int)($stmt->get_result()->fetch_assoc()['n'] ?? 0);
$stmt->close();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Gesamtkosten</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_utils/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logos/iphone_favicon.png">
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
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

</head>
<body>
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="mt-4 card">
        <div class="card-header d-flex flex-wrap align-items-center gap-2">
            <span class="me-1">Gesamtprojekt</span>
            <button type='button' class='btn btn-outline-dark btn-sm' data-pdf='pdf_createKostenOverallPDF.php'>
                <i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk
            </button>
            <button type='button' class='btn btn-outline-dark btn-sm' data-pdf='pdf_createKostenOverallBauabschnittPDF.php'>
                <i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk und Bauabschnitt
            </button>
            <button type='button' class='btn btn-outline-dark btn-sm' data-pdf='pdf_createKostenOverallBauabschnittBudgetPDF.php'>
                <i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk, Bauabschnitt und Budget
            </button>
            <button type='button' class='btn btn-outline-dark btn-sm' data-pdf='pdf_createKostenOverallInclGHGPDF.php'>
                <i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk/GHG
            </button>
            <button type='button' class='btn btn-outline-dark btn-sm' data-pdf='pdf_createKostenRaumbereichInclGHGPDF.php'>
                <i class='far fa-file-pdf'></i> Raumbereich Gewerk/GHG
            </button>

            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="switchNichtFreigegeben">
                    <label class="form-check-label text-nowrap" for="switchNichtFreigegeben">
                        Noch nicht freigegebene Budgets einbeziehen
                        <?php if ($anzahlNichtFreigegeben > 0): ?>
                            <span class="badge text-bg-danger ms-1"
                                  title="Budgets im Projekt, die noch nicht freigegeben sind"><?= $anzahlNichtFreigegeben ?></span>
                        <?php endif; ?>
                    </label>
                </div>
                <span class="badge rounded-pill bg-light text-dark p-2"
                      data-bs-toggle="popover"
                      data-bs-content="Kosten f. Elemente aus entfallenen Räumen sind enthalten! (Anzahl auf Null setzten). Standardmäßig werden nur freigegebene Budgets und Elemente ohne Budget berechnet. Der Schalter nimmt noch nicht freigegebene Budgets dazu. Abgelehnte Budgets sind nie enthalten. Gilt für alle Kostenberichte.">
                    <i class="fas fa-info-circle"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="mt-4 card">
        <div class="card-header">
            <div class="row">
                <div class="col-xxl-6"><span>Raumbereiche</span></div>
                <div class="col-xxl-6 d-flex align-items-center justify-content-end text-nowrap"
                     id="RaumsucheCardHeaderSub">
                    <button type='button' class='btn btn-outline-dark btn-sm me-5 ml-2' id='createRaumbereichPDF'>
                        <i class='far fa-file-pdf'></i> Kosten je Raumbereich
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body" id="costsRoomArea">
            <?php
            $sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauabschnitt, tabelle_räume.Bauetappe
                    FROM tabelle_räume
                    INNER JOIN tabelle_räume_has_tabelle_elemente
                        ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                    INNER JOIN tabelle_projekt_element_gewerk
                        ON tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                       AND tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte
                    WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
                    GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
                    ORDER BY tabelle_räume.Geschoss";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $projectID);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <table class='table table-striped table-bordered table-sm table-hover border border-light border-5'
                   id='tableRaumbereiche'>
                <thead>
                <tr>
                    <th>Raumbereich</th>
                    <th>Geschoss</th>
                    <th>Bauabschnitt</th>
                    <th>Bauetappe</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $h($row["Raumbereich Nutzer"]) ?></td>
                        <td><?= $h($row["Geschoss"]) ?></td>
                        <td><?= $h($row["Bauabschnitt"]) ?></td>
                        <td><?= $h($row["Bauetappe"]) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php
            $stmt->close();
            $mysqli->close();
            ?>
        </div>
    </div>
    <div class="mt-4 card">
        <div class="card-header" id='projektKosten'>Projektkostenentwicklung</div>
        <div class="card-body" id="projectCosts">
            <canvas id="projectCostChart"></canvas>
        </div>
    </div>
</div>

<script src="_utils/_utils.js"></script>
<script>
    const SWITCH_STORAGE_KEY = 'kosten_inklNichtFreigegeben';
    let table;

    function inklNichtFreigegeben() {
        return $('#switchNichtFreigegeben').is(':checked');
    }

    // Baut die PDF-URL inkl. Budget-Schalter
    function pdfUrl(datei, params = {}) {
        const p = new URLSearchParams(params);
        if (inklNichtFreigegeben()) {
            p.set('inklNichtFreigegeben', '1');
        }
        const query = p.toString();
        return 'PDFs/' + datei + (query ? '?' + query : '');
    }

    function decodeHtmlEntities(str) {
        let tempDiv = document.createElement('div');
        tempDiv.innerHTML = str;
        return tempDiv.textContent || tempDiv.innerText || '';
    }

    $('button[data-pdf]').on('click', function () {
        window.open(pdfUrl($(this).data('pdf')));
    });

    $('#createRaumbereichPDF').on('click', function () {
        const auswahl = [];
        table.rows({selected: true}).every(function () {
            const rowData = this.data();
            auswahl.push({
                bereich: decodeHtmlEntities(rowData[0]),
                geschoss: decodeHtmlEntities(rowData[1])
            });
        });

        if (auswahl.length === 0) {
            makeToaster("Kein Raumbereich ausgewählt!", false);
            return;
        }
        window.open(pdfUrl('pdf_createKostenRaumbereichPDF.php', {auswahl: JSON.stringify(auswahl)}));
    });

    $(document).ready(function () {
        // Schalterstellung merken (pro Browser)
        try {
            $('#switchNichtFreigegeben').prop('checked', localStorage.getItem(SWITCH_STORAGE_KEY) === '1');
        } catch (e) {
        }
        $('#switchNichtFreigegeben').on('change', function () {
            try {
                localStorage.setItem(SWITCH_STORAGE_KEY, this.checked ? '1' : '0');
            } catch (e) {
            }
        });

        let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function (popoverTriggerEl) {
            new bootstrap.Popover(popoverTriggerEl);
        });

        table = $('#tableRaumbereiche').DataTable({
            paging: true,
            searching: true,
            info: true,
            order: [[1, "asc"]],
            pagingType: "simple",
            lengthChange: true,
            pageLength: 10,
            language: {
                url: "/_utils/de-DE.json",
                searchPlaceholder: "Suche...",
                search: ""
            },
            select: {
                style: 'multi'
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['search', 'pageLength'],
                bottomEnd: ['info', 'paging']
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#RaumsucheCardHeaderSub');
            }
        });
        const searchbuilder = [
            {
                extend: 'searchBuilder',
                text: " ",
                className: "fa fa-search me-2",
                titleAttr: "searchBuilder"
            }
        ];
        new $.fn.dataTable.Buttons(table, {buttons: searchbuilder}).container().appendTo($('#RaumsucheCardHeaderSub'));
        $('.dt-buttons').children().children().remove();

        $.ajax({        //Diagramm zeichnen
            url: "getChartProjectCosts.php",
            method: 'POST',
            success: function (data) {
                let summeNeu = [];
                let summeBestand = [];
                let summeGesamt = [];
                let datum = [];
                for (let i in data) {
                    summeBestand.push(data[i][1]);
                    summeNeu.push(data[i][2]);
                    summeGesamt.push(parseInt(data[i][1]) + parseInt(data[i][2]));
                    datum.push(data[i][0]);
                }
                new Chart($("#projectCostChart"), {
                    type: 'line',
                    data: {
                        labels: datum,
                        datasets: [
                            {
                                label: "Neu",
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                data: summeNeu,
                                borderWidth: 2
                            },
                            {
                                label: "Bestand",
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(0, 217, 0, 1)',
                                hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                data: summeBestand,
                                borderWidth: 2
                            },
                            {
                                label: "Gesamt",
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(255, 255, 0, 1)',
                                hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                data: summeGesamt,
                                borderWidth: 2
                            }
                        ]
                    }
                });
            },
            error: function () {
                console.log("Couldnt Load Chart");
            }
        });
    });
</script>
</body>
</html>