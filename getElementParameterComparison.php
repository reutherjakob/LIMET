<?php
include "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

// Validate and sanitize input
$elementID = isset($_GET["elementID"]) ? (int)$_GET["elementID"] : 0;
if ($elementID < 1) die("Invalid element ID");

// Set group concat only once
if (!$mysqli->query("SET group_concat_max_len=15000")) {
    die("Error setting group concat: " . $mysqli->error);
}

// Dynamic column generation (fixed table references)
$columnQuery = "SELECT GROUP_CONCAT(DISTINCT
                CONCAT(
                    'MAX(IF(param.Bezeichnung = ''',
                    REPLACE(param.Bezeichnung, '''', ''''''),
                    ''', CONCAT(e.Wert, e.Einheit), NULL)) AS `',
                    REPLACE(param.Bezeichnung, '`', '``'),
                    '`'
                )
            ) INTO @sql
            FROM tabelle_projekt_elementparameter AS e
            JOIN tabelle_parameter AS param
              ON e.tabelle_parameter_idTABELLE_Parameter = param.idTABELLE_Parameter
            WHERE e.tabelle_elemente_idTABELLE_Elemente = " . $elementID;

error_log("Generated column query: " . $columnQuery);
if (!$mysqli->query($columnQuery)) {
    die("Column generation failed: " . $mysqli->error);
}

// Build main query with consistent aliases
$mainQuery = "SET @sql = CONCAT('
    SELECT 
        p.Interne_Nr,
        v.Variante, 
        ', @sql, '
    FROM tabelle_projekte AS p
    RIGHT JOIN tabelle_projekt_elementparameter AS e 
      ON p.idTABELLE_Projekte = e.tabelle_projekte_idTABELLE_Projekte
    JOIN tabelle_parameter AS param 
      ON e.tabelle_parameter_idTABELLE_Parameter = param.idTABELLE_Parameter
    JOIN tabelle_varianten AS v 
      ON e.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
    WHERE e.tabelle_elemente_idTABELLE_Elemente = " . $elementID . " 
    GROUP BY p.idTABELLE_Projekte, v.Variante')";

if (!$mysqli->query($mainQuery)) {
    die("Query build failed: " . $mysqli->error);
}

// Execute prepared statement
$mysqli->query("PREPARE stmt FROM @sql");
$result = $mysqli->query("EXECUTE stmt");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>Element Parameter Comparison</title>
</head>
<body>

<?php if ($result): ?>
    <table class="table table-striped table-condensed" id="tableElementParameterComparison" style="width:100%">
        <thead>
        <tr>
            <?php foreach ($result->fetch_fields() as $field):
                echo '<th>' . htmlspecialchars($field->name ?? '') . '</th>';
            endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <?php
                foreach ($row as $value):
                    echo '<td>' . htmlspecialchars($value ?? '') . '</td>';
                endforeach ?>
            </tr>
        <?php endwhile ?>
        </tbody>
    </table>
    <?php $result->free(); ?>
<?php endif; ?>

<script>
    $(document).ready(function () {
        $('#tableElementParameterComparison').DataTable({
            paging: false,
            searching: false,
            info: false,
            order: [[0, "desc"]],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
            },
            scrollX: true
        });
    });
</script>
</body>
</html>
