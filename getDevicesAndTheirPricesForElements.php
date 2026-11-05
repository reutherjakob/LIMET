<?php
// getDevicesAndTheirPricesForElements
require_once "_utils/_utils.php";
include "_utils/_format_money.php";

$elementID = getPostInt('elementID', 0);
$chartData = []; // Gerät => Liste der Chart-Punkte

if ($elementID > 0) {
    $mysqli = utils_connect_sql();

    $sql = "SELECT 
                g.GeraeteID,
                g.Typ AS Gerät,
                g.Kurzbeschreibung,
                h.Hersteller,
                p.Datum,
                p.Quelle,
                p.Menge,
                p.Preis AS EP,
                p.Nebenkosten AS 'NK/Stk', 
                pr.Projektname,
                l.Lieferant,
                p.Kommentar
            FROM tabelle_geraete g
            INNER JOIN tabelle_hersteller h ON g.tabelle_hersteller_idtabelle_hersteller = h.idtabelle_hersteller
            INNER JOIN tabelle_preise p ON g.idTABELLE_Geraete = p.TABELLE_Geraete_idTABELLE_Geraete
            LEFT JOIN tabelle_projekte pr ON p.TABELLE_Projekte_idTABELLE_Projekte = pr.idTABELLE_Projekte
            LEFT JOIN tabelle_lieferant l ON p.tabelle_lieferant_idTABELLE_Lieferant = l.idTABELLE_Lieferant
            WHERE g.TABELLE_Elemente_idTABELLE_Elemente = ?
            ORDER BY g.GeraeteID, p.Datum DESC";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $elementID);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table class='table table-striped table-bordered table-sm table-hover border border-light px-0 py-0' id='TableDevicePricesInProjects'>";
    echo "<thead><tr> 
            <th>EP</th>
            <th>NK</th>
            <th>Stk</th>
            <th class='text-center'>      
               <i class='fas fa-calendar-alt' ></i></th>
            <th>Info</th>
            <th>Gerät</th>      
            <th> <i class='fas fa-euro-sign'></i> <i class='far fa-comment'></i></th>      
            
          
            <th>Herst./Lief.</th>
            <th>El.-Beschr.</th>

            <th>Projekt</th>
          </tr></thead><tbody>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = date_create($row["Datum"]);
            echo "<tr>";
            echo "<td  class='text-end'>" . format_money_no_decimals($row["EP"]) . "</td>";
            echo "<td  class='text-end'>" . format_money_no_decimals($row["NK/Stk"]) . "</td>";
            echo "<td>" . $row["Menge"] . "</td>";
            echo "<td>" . date_format($date, 'Y-m-d') . "</td>";
            echo "<td>" . htmlspecialchars($row["Quelle"] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['Gerät']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Kommentar']??'') . "</td>";
            echo "<td>" . htmlspecialchars($row["Lieferant"] ?? ' ') . ' - ' .
                htmlspecialchars($row['Hersteller'] ?? '') . "</td>";
            echo "<td>";
            if (!empty($row["Kurzbeschreibung"])) {
                echo "<button type='button' class='btn btn-outline-secondary btn-sm' 
                data-bs-toggle='popover' 
                data-bs-placement='top' 
                data-bs-title=' ' 
                    data-bs-content='" . htmlspecialchars($row["Kurzbeschreibung"])
                    . "'><i class='fas fa-info-circle'></i></button>";
            } else {
                echo "-";
            }
            echo "</td>";
            echo "<td>" . htmlspecialchars($row["Projektname"] ?? '-') . "</td>";
            echo "</tr>";

            // Chart: x = Jahr inkl. Position im Jahr, y = Punkte (1 Punkt = 1000 Euro)
            if (!empty($row["Datum"]) && $row["EP"] !== null) {
                $chartData[$row['Gerät']][] = [
                    'x' => round((int)date_format($date, 'Y') + (int)date_format($date, 'z') / 365, 3),
                    'y' => round((float)$row["EP"] / 1000, 3),
                    'date' => date_format($date, 'Y-m-d'),
                    'project' => $row["Projektname"] ?? '',
                ];
            }
        }
    } else {
        echo "<tr><td colspan='10' class='text-center text-muted'>Keine Gerätepreise gefunden</td></tr>";
    }

    echo "</tbody></table> ";

    $stmt->close();
    $mysqli->close();
}

if (!empty($chartData)) {
    echo "<div class='mt-3'><canvas id='ChartDevicePricesByYear' width='1000' height='350'></canvas></div>";
}
?>

<script>
    $(document).ready(function () {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                container: 'body',
                trigger: 'focus hover',
                html: true,
                placement: 'top'
            });
        });

        if ($('#TableDevicePricesInProjects tbody tr').length > 0 &&
            !$('#TableDevicePricesInProjects tbody tr td').hasClass('text-muted')) {
            new DataTable('#TableDevicePricesInProjects', {
                paging: false,
                searching: false,
                info: false,
                order: [[3, "desc"]], // Datum absteigend
                language: {
                    url: "/_utils/de-DE.json",
                    decimal: ",",
                    thousands: ".",
                },
            });
        }

        $(document).on('click', function (e) {
            $('[data-bs-toggle="popover"]').each(function () {
                if (!$(this).is(e.target) &&
                    $(this).has(e.target).length === 0 &&
                    $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });

        initDevicePriceChart(<?= json_encode(
            $chartData,
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        ) ?>);
    });

    function initDevicePriceChart(chartData) {
        const paneSelector = '#elementPricesInOtherProjects-2';
        const tabEvent = 'shown.bs.tab.devicePriceChart';

        // Dieser Inhalt wird bei jedem Elementwechsel neu geladen: alten Zustand aufräumen
        $(document).off(tabEvent);
        if (window.devicePriceChart) {
            window.devicePriceChart.destroy();
            window.devicePriceChart = null;
        }

        const canvas = document.getElementById('ChartDevicePricesByYear');
        if (!canvas) {
            return;
        }

        const colors = ['#0d6efd', '#dc3545', '#198754', '#ffc107', '#6f42c1', '#fd7e14', '#20c997', '#d63384', '#0dcaf0', '#6c757d'];
        const datasets = Object.keys(chartData).map(function (device, index) {
            const color = colors[index % colors.length];
            return {
                label: device,
                data: chartData[device],
                showLine: false,
                backgroundColor: color,
                borderColor: color,
                pointBackgroundColor: color,
                pointBorderColor: color,
                pointRadius: 5,
                pointHoverRadius: 7
            };
        });

        const xValues = [].concat.apply([], datasets.map(function (dataset) {
            return dataset.data.map(function (point) {
                return point.x;
            });
        }));
        const minYear = Math.floor(Math.min.apply(null, xValues));
        const maxYear = Math.floor(Math.max.apply(null, xValues)) + 1;

        function renderChart() {
            window.devicePriceChart = new Chart(canvas.getContext('2d'), {
                type: 'scatter',
                data: {datasets: datasets},
                options: {
                    legend: {position: 'bottom'},
                    scales: {
                        xAxes: [{
                            type: 'linear',
                            position: 'bottom',
                            ticks: {
                                min: minYear,
                                max: maxYear,
                                stepSize: 1,
                                callback: function (value) {
                                    return Math.floor(value) === value ? value : '';
                                }
                            },
                            scaleLabel: {display: true, labelString: 'Jahr'}
                        }],
                        yAxes: [{
                            ticks: {beginAtZero: true},
                            scaleLabel: {display: true, labelString: 'Punkte (1 Punkt = 1.000 €)'}
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                const dataset = data.datasets[tooltipItem.datasetIndex];
                                const point = dataset.data[tooltipItem.index];
                                const euro = (point.y * 1000).toLocaleString('de-DE');
                                const project = point.project ? ', ' + point.project : '';
                                return dataset.label + ': ' + point.y.toLocaleString('de-DE') +
                                    ' Punkte (' + euro + ' €, ' + point.date + project + ')';
                            }
                        }
                    }
                }
            });
        }

        // In einem versteckten Tab hätte der Canvas die Breite 0
        if ($(paneSelector).is(':visible')) {
            renderChart();
        } else {
            $(document).on(tabEvent, function (e) {
                if ($(e.target).attr('data-bs-target') === paneSelector) {
                    $(document).off(tabEvent);
                    renderChart();
                }
            });
        }
    }
</script>