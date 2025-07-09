<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Improved Inflation Calculator</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <!-- DATEPICKER tenderCalender -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/css/bootstrap-datepicker.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body>

<div id="limet-navbar"></div>
<div class="container-fluid my-3">

    <?php
    if (!function_exists('utils_connect_sql')) {
        include "utils/_utils.php";
    }

    init_page_serversides("x");
    $mysqli = utils_connect_sql();

    function fetch_data($mysqli, $query, $params = [], $types = '') {
        $stmt = $mysqli->prepare($query);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all device types for the dropdown
    $all_types = fetch_data(
        $mysqli,
        "SELECT DISTINCT g.Typ 
     FROM tabelle_geraete g
     INNER JOIN tabelle_preise p ON g.idTABELLE_Geraete = p.TABELLE_Geraete_idTABELLE_Geraete
     ORDER BY g.Typ ASC"
    );

    // Handle filters from POST
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $device_type = $_POST['device_type'] ?? [];
    if (!is_array($device_type)) $device_type = [$device_type];

    $min_entries = isset($_POST['min_entries']) ? intval($_POST['min_entries']) : 4;
    $exclude_outliers = isset($_POST['exclude_outliers']) && $_POST['exclude_outliers'] == 1;

    // Build WHERE clauses
    $where = [];
    $params = [];
    $types = '';

    if ($start_date) {
        $where[] = "Datum >= ?";
        $params[] = $start_date;
        $types .= 's';
    }
    if ($end_date) {
        $where[] = "Datum <= ?";
        $params[] = $end_date;
        $types .= 's';
    }
    if ($device_type && !in_array('', $device_type)) {
        // Build placeholders (?, ?, ?, ...) for each selected type
        $placeholders = implode(',', array_fill(0, count($device_type), '?'));
        $where[] = "TABELLE_Geraete_idTABELLE_Geraete IN (
        SELECT idTABELLE_Geraete FROM tabelle_geraete WHERE Typ IN ($placeholders)
    )";
        foreach ($device_type as $dt) {
            $params[] = $dt;
            $types .= 's';
        }
    }

    $where_sql = $where ? "WHERE " . implode(' AND ', $where) : "";

    // First, get all device IDs with enough entries
    $query = "
    SELECT TABELLE_Geraete_idTABELLE_Geraete, COUNT(DISTINCT Datum) AS price_entries
    FROM tabelle_preise
    $where_sql
    GROUP BY TABELLE_Geraete_idTABELLE_Geraete
    HAVING COUNT(DISTINCT Datum) >= ?
";
    $params2 = $params;
    $params2[] = $min_entries;
    $types2 = $types . 'i';
    $device_entry_counts = fetch_data($mysqli, $query, $params2, $types2);
    $geraete_ids = array_column($device_entry_counts, 'TABELLE_Geraete_idTABELLE_Geraete');

    // Get device type names
    $geraete_map = [];
    if ($geraete_ids) {
        $geraete_details = fetch_data(
            $mysqli,
            "SELECT idTABELLE_Geraete, Typ FROM tabelle_geraete WHERE idTABELLE_Geraete IN (" . implode(',', array_fill(0, count($geraete_ids), '?')) . ")",
            $geraete_ids,
            str_repeat('i', count($geraete_ids))
        );
        foreach ($geraete_details as $detail) {
            $geraete_map[$detail['idTABELLE_Geraete']] = $detail['Typ'];
        }
    }

    // Get all price entries for selected devices
    $top_geraete_entries = [];
    if ($geraete_ids) {
        $top_geraete_entries = fetch_data(
            $mysqli,
            "SELECT * FROM tabelle_preise WHERE TABELLE_Geraete_idTABELLE_Geraete IN (" . implode(',', array_fill(0, count($geraete_ids), '?')) . ")" .
            ($where_sql ? " AND " . implode(' AND ', $where) : ""),
            array_merge($geraete_ids, $params),
            str_repeat('i', count($geraete_ids)) . $types
        );
    }
    $mysqli->close();

    // Outlier removal function
    function remove_outliers($prices) {
        if (count($prices) < 3) return $prices;
        $mean = array_sum($prices) / count($prices);
        $sd = sqrt(array_sum(array_map(fn($x) => pow($x - $mean, 2), $prices)) / count($prices));
        return array_values(array_filter($prices, fn($x) => abs($x - $mean) <= 2 * $sd));
    }

    // Organize data by device and month
    $data = [];
    foreach ($top_geraete_entries as $entry) {
        $month = date('Y-m', strtotime($entry['Datum']));
        $geraet_id = $entry['TABELLE_Geraete_idTABELLE_Geraete'];
        $data[$geraet_id][$month][] = floatval($entry['Preis']);
    }

    // Calculate stats
    $price_changes = [];
    $overall_development = [];
    $annual_inflation = [];

    foreach ($data as $geraet_id => $months) {
        ksort($months);
        $previous_month = null;
        $first_month_price = null;
        $first_month = null;
        $last_month_price = null;
        $last_month = null;

        foreach ($months as $month => $prices) {
            if ($exclude_outliers) $prices = remove_outliers($prices);
            if (!$prices) continue;
            $average_price = array_sum($prices) / count($prices);

            if ($previous_month === null) {
                $first_month_price = $average_price;
                $first_month = $month;
                $price_changes[$geraet_id][$month] = [
                    'average_price' => round($average_price, 2),
                    'percentage_change' => null,
                    'raw_prices' => $prices
                ];
            } else {
                $previous_price = $price_changes[$geraet_id][$previous_month]['average_price'];
                $percentage_change = (($average_price - $previous_price) / $previous_price) * 100;
                $price_changes[$geraet_id][$month] = [
                    'average_price' => round($average_price, 2),
                    'percentage_change' => round($percentage_change, 2),
                    'raw_prices' => $prices
                ];
            }
            $last_month_price = $average_price;
            $last_month = $month;
            $previous_month = $month;
        }
        if ($first_month_price !== null && $last_month_price !== null && $first_month !== $last_month) {
            $overall_percentage_change = (($last_month_price - $first_month_price) / $first_month_price) * 100;
            $overall_development[$geraet_id] = round($overall_percentage_change, 2);

            // Calculate annual inflation
            $total_months = (strtotime($last_month) - strtotime($first_month)) / (60 * 60 * 24 * 30.5);
            if ($total_months >= 1) {
                $annual_inflation[$geraet_id] = round((pow($last_month_price / $first_month_price, 12 / $total_months) - 1) * 100, 2);
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

    <div class="card mb-4">
        <div class="card-header">Inflation Calculator - Filters</div>
        <div class="card-body">
            <form method="post" action="">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="start_date" class="form-label mb-0">Start Date:</label>
                        <input type="text" id="start_date" name="start_date" class="form-control datepicker"
                               value="<?= htmlspecialchars($start_date) ?>" autocomplete="off" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-auto">
                        <label for="end_date" class="form-label mb-0">End Date:</label>
                        <input type="text" id="end_date" name="end_date" class="form-control datepicker"
                               value="<?= htmlspecialchars($end_date) ?>" autocomplete="off" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-auto">
                        <label for="device_type"> </label>
                        <select name="device_type[]" id="device_type" class="form-control select2" multiple="multiple">
                            <option value="">All</option>
                            <?php foreach ($all_types as $type): ?>
                                <option value="<?= htmlspecialchars($type['Typ']) ?>"
                                    <?= (isset($device_type) && is_array($device_type) && in_array($type['Typ'], $device_type)) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type['Typ']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                    <div class="col-auto">
                        <label for="min_entries" class="form-label mb-0">Min Entries:</label>
                        <input type="number" name="min_entries" id="min_entries" class="form-control"
                               value="<?= htmlspecialchars($min_entries) ?>" min="1">
                    </div>
                    <div class="col-auto">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="exclude_outliers" value="1" class="form-check-input" id="excludeOutliers" <?= $exclude_outliers ? 'checked' : '' ?>>
                            <label class="form-check-label" for="excludeOutliers">Exclude Outliers</label>
                        </div>
                    </div>
                    <div class="col-auto align-self-end">
                        <button type="submit" class="btn btn-primary mt-2">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xxl-3">
            <div class="card h-auto">
                <div class="card-header">Price Development Details</div>
                <div class="card-body" >
                    <?php if ($price_changes): ?>
                        <ul class="list-group">
                            <?php foreach ($price_changes as $geraet_id => $months): ?>
                                <li class="list-group-item">
                                    <strong> <?= htmlspecialchars($geraete_map[$geraet_id] ?? 'N/A') ?></strong>
                                    <ul>
                                        <?php foreach ($months as $month => $stats): ?>
                                            <li><?= $month ?>:
                                                Avg Price = <?= $stats['average_price'] ?>,
                                                (Prices = <?= implode(', ', $stats['raw_prices']) ?>)
                                                <br>
                                                <strong>% Change = <?= $stats['percentage_change'] !== null ? "{$stats['percentage_change']}%" : 'N/A' ?></strong>
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
            <div class="card mb-4">
                <div class="card-header">Normalized Price Development per Month for Each Device</div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;" >
                    <canvas id="priceDevelopmentChart"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Overall Results</div>
                <div class="card-body">
                    <?php if ($overall_development): ?>
                        <h5>Device Results:</h5>
                        <ul class="list-group">
                            <?php foreach ($overall_development as $geraet_id => $dev): ?>
                                <li class="list-group-item">
                                    <?= htmlspecialchars($geraete_map[$geraet_id] ?? 'N/A') ?> <br>
                                    Overall Percentage Change: <strong><?= $dev ?>%</strong> <br>
                                    Annual Inflation Rate: <strong><?= $annual_inflation[$geraet_id] ?>%</strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <h5 class="mt-4">Summary for All Devices:</h5>
                        <p><strong>Average Percentage Price Change Over All Devices and Months: <?= $average_percentage_change ?>%</strong></p>
                    <?php else: ?>
                        <p>No data available for overall results.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select device type(s)",
            allowClear: true,
            width: 'resolve'
        });

        $(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: "bottom"
            });
        });

    });



    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('priceDevelopmentChart').getContext('2d');
        if (!ctx) return;

        const data = <?php echo json_encode($data); ?>;
        const geraeteMap = <?php echo json_encode($geraete_map); ?>;
        const datasets = [];
        const colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#A133FF', '#1abc9c', '#e67e22', '#8e44ad', '#34495e', '#2ecc71'];

        Object.keys(data).forEach((geraet_id, index) => {
            const months = data[geraet_id];
            const normalized_prices = [];
            const sortedMonths = Object.keys(months).sort();
            let first_price = null;
            if (sortedMonths.length > 0) {
                const firstMonthPrices = months[sortedMonths[0]];
                first_price = firstMonthPrices.reduce((a, b) => a + b, 0) / firstMonthPrices.length;
            }
            if (first_price !== null) {
                sortedMonths.forEach(month => {
                    const avg = months[month].reduce((a, b) => a + b, 0) / months[month].length;
                    normalized_prices.push({
                        x: new Date(month + '-01'),
                        y: avg / first_price
                    });
                });
                datasets.push({
                    label: `(${geraeteMap[geraet_id]})`,
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
            data: { datasets: datasets },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: false,
                        text: 'Normalized Price Development per Month for Each Device'
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: { unit: 'month' },
                        title: { display: true, text: 'Month' }
                    },
                    y: {
                        title: { display: true, text: 'Normalized Price' }
                    }
                }
            }
        });
    });
</script>
</body>
</html>