<?php
// 25 FX - CORRECTED VERSION
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$elementID = getPostInt('elementID', 0);

// 1. Hole ALLE relevanten Parameter-Bezeichnungen als Liste
$query = "
    SELECT GROUP_CONCAT(DISTINCT QUOTE(tabelle_parameter.Bezeichnung) SEPARATOR ',') AS param_list
    FROM tabelle_hersteller
    INNER JOIN tabelle_geraete ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    LEFT JOIN tabelle_geraete_has_tabelle_parameter ON tabelle_geraete.idTABELLE_Geraete = tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete
    RIGHT JOIN tabelle_parameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter
    INNER JOIN tabelle_elemente ON tabelle_geraete.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
    WHERE tabelle_elemente.idTABELLE_Elemente = ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $elementID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$paramList = $row['param_list'];

if (empty($paramList)) {
    die("Keine Parameter gefunden.");
}

// 2. Baue dynamische Spalten PHP-seitig (sicher!)
$columns = [];
$params = explode(',', str_replace("'", '', $paramList)); // QUOTE entfernt, Komma-Split

foreach ($params as $param) {
    $param = trim($param);
    if (empty($param)) continue;

    // SQL-escaped String für Vergleich
    $escapedParam = $mysqli->real_escape_string($param);
    // Sicherer Alias-Name (Backticks + bereinigt)
    $alias = str_replace([' ', '-', '(', ')'], '_', $param);

    $columns[] = "MAX(IF(tabelle_parameter.Bezeichnung = '$escapedParam', " .
        "CONCAT(tabelle_geraete_has_tabelle_parameter.Wert, " .
        "tabelle_geraete_has_tabelle_parameter.Einheit), NULL)) AS `$alias`";
}

$columnsSql = implode(', ', $columns);


// 3. Final Query mit sauberen Spalten
$finalQuery = "
    SELECT tabelle_geraete.Typ, $columnsSql
    FROM tabelle_hersteller
    INNER JOIN tabelle_geraete ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    LEFT JOIN tabelle_geraete_has_tabelle_parameter ON tabelle_geraete.idTABELLE_Geraete = tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete
    RIGHT JOIN tabelle_parameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter
    LEFT JOIN tabelle_parameter_kategorie ON tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie = tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie
    INNER JOIN tabelle_elemente ON tabelle_geraete.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
    WHERE tabelle_elemente.idTABELLE_Elemente = ?
    GROUP BY tabelle_geraete.Typ
";

$stmt = $mysqli->prepare($finalQuery);
$stmt->bind_param("i", $elementID);
$stmt->execute();
$result = $stmt->get_result();

// Rest unverändert...
echo "<table class='table table-striped table-sm' id='tableDeviceComparison'>";
echo "<thead><tr>";
$fields = $result->fetch_fields();
foreach ($fields as $field) {
    echo "<th>" . htmlspecialchars($field->name) . "</th>";
}
echo "</tr></thead><tbody>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
    }
    echo "</tr>";
}
echo "</tbody></table>";

$mysqli->close();
?>

<script>
    $("#tableDeviceComparison").DataTable({
        paging: false,
        searching: false,
        info: false,
        order: [[0, "desc"]],
        language: {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
        scrollX: true
    });
</script>
