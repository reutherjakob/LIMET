<?php
require_once "_utils.php";
check_login();
$mysqli = utils_connect_sql();

$elementID = $_GET["elementID"];
$query = "
    SELECT
        GROUP_CONCAT(DISTINCT
            CONCAT(
                'MAX(IF(tabelle_parameter.Bezeichnung = ''',
                tabelle_parameter.Bezeichnung,
                ''', CONCAT(tabelle_geraete_has_tabelle_parameter.Wert, tabelle_geraete_has_tabelle_parameter.Einheit), NULL)) AS ',
                tabelle_parameter.Bezeichnung
            )
        ) AS columns
    FROM tabelle_hersteller
    INNER JOIN tabelle_geraete ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    LEFT JOIN tabelle_geraete_has_tabelle_parameter ON tabelle_geraete.idTABELLE_Geraete = tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete
    RIGHT JOIN tabelle_parameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter
    LEFT JOIN tabelle_parameter_kategorie ON tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie = tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie
    INNER JOIN tabelle_elemente ON tabelle_geraete.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
    WHERE tabelle_elemente.idTABELLE_Elemente = ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $elementID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$columns = $row['columns'];
if (empty($columns)) {
    die("No columns were generated. Check your data and query.   -> Broke. Lemme know if u need this... ");
}

// Prepare and execute the final query
$finalQuery = "
    SELECT tabelle_geraete.Typ, " .  $mysqli->real_escape_string($columns)  ."  
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

echo "<table class='table table-striped table-sm' id='tableDeviceComparison'  >";
echo "<thead><tr>";
$fields = $result->fetch_fields();
foreach ($fields as $field) {
    echo "<th>" . htmlspecialchars($field->name) . "</th>";
}
echo "</tr></thead><tbody>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
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
