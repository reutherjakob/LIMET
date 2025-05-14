<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Development Stats</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
</head>
<body>
<div id="limet-navbar"> </div>
<div class="container-fluid">

<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides("x", "x");
$mysqli = utils_connect_sql();

function fetch_data($mysqli, $query, $params = [], $types = '')
{
    $stmt = $mysqli->prepare($query);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$query = "";
if (isset($_POST['query_type'])) {
    $query_type = $_POST['query_type'];
    if ($query_type == 'more_than_4_entries') {
        $query = "
            SELECT 
                TABELLE_Geraete_idTABELLE_Geraete, 
                COUNT(DISTINCT Datum) AS price_entries
            FROM 
                tabelle_preise
            GROUP BY 
                TABELLE_Geraete_idTABELLE_Geraete
            HAVING 
                COUNT(DISTINCT Datum) > 4";
    } elseif ($query_type == 'most_frequent') {
        $query = "
            SELECT TABELLE_Geraete_idTABELLE_Geraete, COUNT(*) AS frequency 
            FROM tabelle_preise 
            GROUP BY TABELLE_Geraete_idTABELLE_Geraete 
            ORDER BY frequency DESC 
            LIMIT 10;
        ";
    }
}

$result = $query ? fetch_data($mysqli, $query) : [];

$geraete_ids = array_column($result, 'TABELLE_Geraete_idTABELLE_Geraete');
$geraete_details = $geraete_ids ? fetch_data($mysqli, "SELECT idTABELLE_Geraete, Typ FROM tabelle_geraete WHERE idTABELLE_Geraete IN (" . implode(',', array_fill(0, count($geraete_ids), '?')) . ")", $geraete_ids, str_repeat('i', count($geraete_ids))) : [];
$geraete_map = [];
foreach ($geraete_details as $detail) {
    $geraete_map[$detail['idTABELLE_Geraete']] = $detail['Typ'];
}

$top_geraete_entries = $geraete_ids ? fetch_data($mysqli, "SELECT * FROM tabelle_preise WHERE TABELLE_Geraete_idTABELLE_Geraete IN (" . implode(',', array_fill(0, count($geraete_ids), '?')) . ")", $geraete_ids, str_repeat('i', count($geraete_ids))) : [];

$mysqli->close();

$data = [];
foreach ($top_geraete_entries as $entry) {
    $month = date('Y-m', strtotime($entry['Datum']) ?? '');
    $geraet_id = $entry['TABELLE_Geraete_idTABELLE_Geraete'];
    $data[$geraet_id][$month][] = $entry['Preis'];
}

$price_changes = [];
$overall_development = [];
$annual_inflation = []; // New array to store annual inflation

foreach ($data as $geraet_id => $months) {
    ksort($months);
    $previous_month = null;
    $first_month_price = null;
    $first_month = null;
    $last_month_price = null;
    $last_month = null;

    foreach ($months as $month => $prices) {
        $average_price = array_sum($prices) / count($prices);

        if ($previous_month === null) {
            $first_month_price = $average_price;
            $first_month = $month;
            $price_changes[$geraet_id][$month] = [
                'average_price' => $average_price,
                'percentage_change' => null,
                'raw_prices' => $prices
            ];
        } else {
            $previous_price = $price_changes[$geraet_id][$previous_month]['average_price'];
            $percentage_change = (($average_price - $previous_price) / $previous_price) * 100;
            $price_changes[$geraet_id][$month] = [
                'average_price' => $average_price,
                'percentage_change' => round($percentage_change, 2),
                'raw_prices' => $prices
            ];
        }

        $last_month_price = $average_price;
        $last_month = $month;
        $previous_month = $month;
    }

    if ($first_month_price !== null && $last_month_price !== null) {
        $overall_percentage_change = (($last_month_price - $first_month_price) / $first_month_price) * 100;
        $overall_development[$geraet_id] = round($overall_percentage_change, 2);

        // Calculate annual inflation
        $total_months = (strtotime($last_month ?? '') - strtotime($first_month ?? '')) / (60 * 60 * 24 * 30.5); // Approx. number of months
        if ($total_months >= 1) {
            $annual_inflation[$geraet_id] = round(pow($last_month_price / $first_month_price, 12 / $total_months) - 1, 4) * 100;
        } else {
            $annual_inflation[$geraet_id] = 'Insufficient Data';
        }
    }
}

$total_percentage_change = 0;
$total_entries = 0;

foreach ($price_changes as $geraet_id => $months) {
    foreach ($months as $month => $stats) {
        if ($stats['percentage_change'] !== null) {
            $total_percentage_change += $stats['percentage_change'];
            $total_entries++;
        }
    }
}

$average_percentage_change = $total_entries ? round($total_percentage_change / $total_entries, 2) : 0;
?>


    <div class="card">
        <form method="post" action="">
            <div class="form-group row">
                <div class="col-xxl-10">
                    <label for="query_type">Select Query Type:</label>
                    <select class="form-control" id="query_type" name="query_type">
                        <option value="more_than_4_entries">Geräte more than 4 entries with different dates</option>
                        <option value="most_frequent">Most Frequent Geräte</option>
                    </select>
                </div>
                <div class="col-xxl-2 align-self-end">
                    <button type="submit" class="btn btn-primary">Fetch Data</button>
                </div>
            </div>
        </form>
        <div class="row mt-4">
            <div class="col-xxl-3">
                <div class="card" id="PriceDevCard">
                    <div class="card-header">Price Development Details and Results</div>
                    <div class="card-body">
                        <?php if ($price_changes): ?>
                            <ul class="list-group">
                                <?php foreach ($price_changes as $geraet_id => $months): ?>
                                    <li class="list-group-item">
                                        <strong>Gerät ID: <?= $geraet_id ?> (Typ: <?= $geraete_map[$geraet_id] ?>
                                            )</strong>
                                        <ul>
                                            <?php foreach ($months as $month => $stats): ?>
                                                <li><?= $month ?>:
                                                    Average Price = <?= $stats['average_price'] ?>,
                                                    (Prices = <?= implode(', ', $stats['raw_prices']) ?>) <br>
                                                    <strong>% Change
                                                        = <?= $stats['percentage_change'] !== null ? "<strong>{$stats['percentage_change']}%</strong>" : 'N/A' ?></strong>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No data available for price development.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xxl-9">
                <div class="card">
                    <div class="card-header">Price Development Plot</div>
                    <div class="card-body">
                        <canvas id="priceDevelopmentChart"></canvas>
                    </div>
                </div>

                <div class="card" id="ResultCard">
                    <div class="card-header">Overall Results</div>
                    <div class="card-body">
                        <?php if ($overall_development): ?>
                            <h5>Device Results:</h5>
                            <ul class="list-group">
                                <?php foreach ($overall_development as $geraet_id => $dev): ?>
                                    <li class="list-group-item">
                                        Gerät ID: <?= $geraet_id ?> (Typ: <?= $geraete_map[$geraet_id] ?>) <br>
                                        Overall Percentage Change: <strong><?= $dev ?>%</strong> <br>
                                        Annual Inflation Rate: <strong><?= $annual_inflation[$geraet_id] ?>%</strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <h5 class="mt-4">Summary for All Devices:</h5>
                            <p><strong>Average Percentage Price Change Over All Devices and
                                    Months: <?= $average_percentage_change ?>%</strong></p>
                        <?php else: ?>
                            <p>No data available for overall results.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('priceDevelopmentChart').getContext('2d');
        if (!ctx) {
            console.error('Canvas element not found');
            return;
        }

        const data = <?php echo json_encode($data); ?>;
        const geraeteMap = <?php echo json_encode($geraete_map); ?>;
        const datasets = [];
        const colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#A133FF'];

        Object.keys(data).forEach((geraet_id, index) => {
            const months = data[geraet_id];
            const normalized_prices = [];
            const sortedMonths = Object.keys(months).sort();

            let first_price = null;
            if (sortedMonths.length > 0) {
                first_price = months[sortedMonths[0]][0];
            }

            if (first_price !== null) {
                sortedMonths.forEach(month => {
                    months[month].forEach(price => {
                        normalized_prices.push({
                            x: new Date(month),
                            y: price / first_price
                        });
                    });
                });

                datasets.push({
                    label: `Gerät ID ${geraet_id} (Typ: ${geraeteMap[geraet_id]})`,
                    data: normalized_prices,
                    borderColor: colors[index % colors.length],
                    fill: false,
                    showLine: true,
                    pointRadius: 5,
                    pointStyle: 'cross'
                });
            }
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                datasets: datasets
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Normalized Price Development per Month for Each Device'
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'month'
                        },
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Normalized Price'
                        }
                    }
                }
            }
        });
    });

</script>
</body>
</html>
