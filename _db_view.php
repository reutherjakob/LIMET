<?php
session_start();
include '_utils.php';
init_page_serversides("X");
$mysqli = utils_connect_sql();

function fetch_schema_data($mysqli) {
    $stmt = "SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'LIMET_RB'";
    $result = $mysqli->query($stmt);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['TABLE_NAME']][] = $row['COLUMN_NAME'];
    }
    return $data;
}

function fetch_table_columns($mysqli, $table_name) {
    $stmt = $mysqli->prepare("SELECT COLUMN_NAME AS ColumnName, DATA_TYPE AS DataType, CHARACTER_MAXIMUM_LENGTH AS CharacterLength FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?");
    $stmt->bind_param("s", $table_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}
function fetch_table_data($mysqli, $table_name, $limit, $fetch_order) {
    $order = $fetch_order === 'first' ? 'ASC' : 'DESC';  // Choose the order based on user input
    $stmt = "SELECT * FROM $table_name ORDER BY 1 $order LIMIT $limit"; // Ordering based on the selection
    $result = $mysqli->query($stmt);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}


function fetch_table_statistics($mysqli, $table_name) {
    $stmt = "SELECT COUNT(*) AS COLUMN_count, AVG(LENGTH(COLUMN_NAME)) AS avg_col_length FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
    $stmt = $mysqli->prepare($stmt);
    $stmt->bind_param("s", $table_name);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$schema_data = fetch_schema_data($mysqli);
$table_columns = $table_data = $table_statistics = [];
$error_message = "";
$default_table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';
$limit = isset($_POST['limit']) ? (int) $_POST['limit'] : 1000;
$max_columns_name_length_to_show = isset($_POST['max_columns_length']) ? (int) $_POST['max_columns_length'] : 5;

if (isset($_POST['table_name'])) {
    $table_name = $_POST['table_name'];
    $fetch_order = isset($_POST['fetch_order']) ? $_POST['fetch_order'] : 'last';  // Default to 'last'
    if (!empty($table_name)) {
        $table_columns = fetch_table_columns($mysqli, $table_name);
        if (!empty($table_columns)) {
            $table_data = fetch_table_data($mysqli, $table_name, $limit, $fetch_order);
            $table_statistics = fetch_table_statistics($mysqli, $table_name);
        } else {
            $error_message = "Invalid table name or table has no columns.";
        }
    } else {
        $error_message = "Table name cannot be empty.";
    }
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Viewer</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    </head>
    <style>
        .card-body {
            overflow: auto;
            padding: 2px;
        }
    </style>
    <body>
        <div id="limet-navbar"></div>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <form method="post" class="form-inline" id="tableForm">
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="table_name" class="sr-only">Table Name</label>
                            <select class="form-control" id="table_name" name="table_name" required>
                                <option value="" disabled>Select table name</option>
                                <?php foreach (array_keys($schema_data) as $table): ?>
                                    <option value="<?php echo $table; ?>" <?php echo $table === $default_table_name ? 'selected' : ''; ?>>
                                        <?php echo $table; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="limit" class="">Rows2Show</label>
                            <input style="width: 100px; " type="number" class="form-control" id="limit" name="limit" value="<?php echo $limit; ?>" placeholder="Rows limit" required>
                        </div>
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="fetch_order" class="mr-2">Fetch Entries</label>
                            <select class="form-control" id="fetch_order" name="fetch_order">
                                <option value="last" <?php echo (isset($_POST['fetch_order']) && $_POST['fetch_order'] === 'last') ? 'selected' : ''; ?>>Last Entries</option>
                                <option value="first" <?php echo (isset($_POST['fetch_order']) && $_POST['fetch_order'] === 'first') ? 'selected' : ''; ?>>First Entries</option>
                            </select>
                        </div>


                        <div class="form-group mx-sm-3 mb-2">
                            <label for="max_columns_length" class="">ColumnlabelLength</label>
                            <input style="width: 100px; " type="number" class="form-control" id="max_columns_length" name="max_columns_length" value="<?php echo $max_columns_name_length_to_show; ?>" placeholder="Column abbreviation length" required>
                        </div>
                        <button type="submit" class="btn btn-primary mb-2">Get Table Info</button>
                    </form>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger mt-3">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card" id="TableColumnsCard">
                        <div class="card-header">Table Columns</div>
                        <div class="card-body">
                            <?php if (!empty($table_statistics)): ?>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <strong>Row Count:</strong> <?php echo $table_statistics['COLUMN_count']; ?>
                                    </li>
                                </ul>
                            <?php endif; ?>

                            <?php if (!empty($table_columns)): ?> 
                                <table class="table table-bordered responsive">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Abbr.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($table_columns as $column): ?>
                                            <tr>
                                                <?php
                                                $abbreviated_name = strlen($column['ColumnName']) > abs($max_columns_name_length_to_show) ?
                                                        substr($column['ColumnName'], 0, abs($max_columns_name_length_to_show)) :
                                                        $column['ColumnName'];
                                                ?>
                                                <td><?php echo htmlspecialchars($column['ColumnName']); ?></td>
                                                <td><?php echo htmlspecialchars($column['DataType']); ?></td>
                                                <td><?php echo htmlspecialchars($abbreviated_name); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card" id="DatabaseSchemaCard">
                        <div class="card-header">Database</div>
                        <div class="card-body" style="display: none;">
                            <ul class="list-group">
                                <?php foreach ($schema_data as $table_name => $columns): ?>
                                    <li class="list-group-item">
                                        <strong><?php echo $table_name; ?></strong>
                                        <ul>
                                            <?php foreach ($columns as $column_name): ?>
                                                <li><?php echo $column_name; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card" style="overflow:auto;">
                        <table id="dataTable" class="display table " style="width: 100%;">
                            <thead>
                                <tr>
                                    <?php if (!empty($table_columns)): ?>
                                        <?php foreach ($table_columns as $column): ?>
                                            <?php
                                            $abbreviated_name = strlen($column['ColumnName']) > abs($max_columns_name_length_to_show) ?
                                                    substr($column['ColumnName'], 0, abs($max_columns_name_length_to_show)) :
                                                    $column['ColumnName'];
                                            ?>
                                            <th><?php echo htmlspecialchars($abbreviated_name); ?></th>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($table_data)): ?>
                                    <?php foreach ($table_data as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $value): ?>
                                                <td><?php echo htmlspecialchars($value); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <script>
            $(document).ready(function () {
                $('#table_name').select2({
                    placeholder: 'Select table name',
                    allowClear: true
                });
                addToggleButton('TableColumnsCard');
                addToggleButton('DatabaseSchemaCard');
                $('.card-body').hide();
                let table = new DataTable('#dataTable', {
                    layout: {
                        bottomStart: null,
                        bottomEnd: null,
                        topStart: ['search', 'info'],
                        topEnd: ['pageLength', 'paging']
                    }
                });

                function addToggleButton(cardId) {
                    const card = document.getElementById(cardId);
                    const cardHeader = card.querySelector('.card-header');
                    const cardBody = card.querySelector('.card-body');
                    $(cardHeader).click(function () {
                        $(cardBody).toggle();
                    });
                }
            });
        </script>
    </body>
</html>
