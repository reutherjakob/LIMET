<?php
// 2025-11- FX
require_once 'utils/_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Gesamtkosten</title>
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
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

</head>
<body>
<div id="limet-navbar"></div> <!-- Container für Navbar -->

<div class="container-fluid">
    <div class="mt-4 card">
        <div class="card-header d-inline-flex justify-content-start align-items-center">
            Gesamtprojekt
            <button type='button' class='btn btn-outline-dark btn-sm ms-2 me-2' value='createKostenOverallPDF'><i
                        class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk
            </button>
            <button type='button' class='btn btn-outline-dark btn-sm me-2' value='createKostenOverallBauabschnittPDF'><i
                        class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk und Bauabschnitt
                <!--- TODO: Fix Wrong calculations -->
            </button>
            <button type='button' class='btn btn-outline-dark btn-sm me-2'
                    value='createKostenOverallBauabschnittBudgetPDF'>
                <i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk, Bauabschnitt und Budget
            </button>
            <button type='button' class='btn btn-outline-dark btn-sm me-2' value='createKostenInclGHGOverallPDF'><i
                        class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk/GHG
            </button>
            <button type='button' class='btn btn-outline-dark btn-sm me-2' value='createKostenRaumbereichPDF'><i
                        class='far fa-file-pdf'></i> Raumbereich Gewerk/GHG
            </button>
            <!-- <div class="card-body"> </div>-->
        </div>
    </div>
    <div class="mt-4 card">
        <div class="card-header">
            <div class="row">
                <div class="col-xxl-6"><span>Raumbereiche</span></div>
                <div class="col-xxl-6 d-flex align-items-center justify-content-end text-nowrap"
                     id="RaumsucheCardHeaderSub">
                    <button type='button' class='btn btn-outline-dark btn-sm me-5 ml-2' id='createRaumbereichPDF'>
                        <i class='far fa-file-pdf'></i> Kosten-PDF
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body" id="costsRoomArea">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauabschnitt,  tabelle_räume.Bauetappe
                                                    FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
                                                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                    GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
                                                    ORDER BY tabelle_räume.Geschoss;";

            $result = $mysqli->query($sql);
            echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableRaumbereiche'>
                                            <thead><tr>
                                            <th>Raumbereich</th>
                                            <th>Geschoss</th>
                                            <th>Bauabschnitt</th>
                                            <th>Bauetappe</th>
                                            </tr></thead><tbody>";


            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                echo "<td>" . $row["Geschoss"] . "</td>";
                echo "<td>" . $row["Bauabschnitt"] . "</td>";
                echo "<td>" . $row["Bauetappe"] . "</td>";
                echo "</tr>";

            }
            echo "</tbody></table>";
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

<script src="utils/_utils.js"></script>
<script>
    var roomBereiche = [];
    var roomBereichGeschosse = [];
    var roomBauabschnitt = [];
    var table;

    $("button[value='createKostenOverallPDF']").click(function () {
        window.open('PDFs/pdf_createKostenOverallPDF.php');//there are many ways to do this
    });

    $("button[value='createKostenOverallBauabschnittPDF']").click(function () {
        window.open('PDFs/pdf_createKostenOverallBauabschnittPDF.php');//there are many ways to do this
    });

    $("button[value='createKostenOverallBauabschnittBudgetPDF']").click(function () {
        window.open('PDFs/pdf_createKostenOverallBauabschnittBudgetPDF.php');//there are many ways to do this
    });


    $("button[value='createKostenInclGHGOverallPDF']").click(function () {
        window.open('PDFs/pdf_createKostenOverallInclGHGPDF.php');//there are many ways to do this
    });

    $("button[value='createKostenRaumbereichPDF']").click(function () {
        window.open('PDFs/pdf_createKostenRaumbereichInclGHGPDF.php');//there are many ways to do this
    });

    $(document).ready(function () {
        table = $('#tableRaumbereiche').DataTable({
            paging: true,
            searching: true,
            info: false,
            order: [[1, "asc"]],
            pagingType: "simple",
            lengthChange: true,
            pageLength: 10,

            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
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
                bottomEnd: 'paging'
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


        $('#tableRaumbereiche tbody').on('click', 'tr', function () {
            $(this).toggleClass('info');
        });

        $.ajax({        //Diagramm zeichnen
            url: "getChartProjectCosts.php",
            method: 'POST',
            success: function (data) {
                let summeNeu = [];
                let summeBestand = [];
                let summeGesamt = [];
                let datum = [];
                for (var i in data) {
                    summeBestand.push(data[i][1]);
                    summeNeu.push(data[i][2]);
                    let b = parseInt(data[i][1]);
                    let n = parseInt(data[i][2]);
                    let summe = b + n;
                    summeGesamt.push(summe);
                    datum.push(data[i][0]);
                }
                let chartdata = {
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
                };
                let ctx = $("#projectCostChart");
                let lineGraph = new Chart(ctx, {
                    type: 'line',
                    data: chartdata
                });
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    $('#createRaumbereichPDF').click(function () {
        var roomBereicheTemp = [];
        var roomBereichGeschosseTemp = [];
        var roomBauabschnittTemp = [];

        // Iterate over all rows in current order that have 'info' class
        $('#tableRaumbereiche tbody tr.info').each(function () {
            var rowData = table.row(this).data();
            if (rowData) {
                roomBereicheTemp.push(rowData[0]);        // Raumbereich Nutzer
                roomBereichGeschosseTemp.push(rowData[1]); // Geschoss
                roomBauabschnittTemp.push(rowData[2]);    // Bauabschnitt
            }
        });

        if (roomBereicheTemp.length === 0) {
            alert("Kein Raumbereich ausgewählt!");
            return;
        }
        console.log(roomBereicheTemp);
        // Encode and open the PDF generation link with arrays reflecting current table order
        const paramRoomBereiche = encodeURIComponent(roomBereicheTemp.join(','));
        const paramRoomBereichGeschosse = encodeURIComponent(roomBereichGeschosseTemp.join(','));
        window.open(
            'PDFs/pdf_createKostenRaumbereichPDF.php?roomBereiche=' + paramRoomBereiche +
            '&roomBereichGeschosse=' + paramRoomBereichGeschosse
        );
    });


</script>
</body>
</html>
