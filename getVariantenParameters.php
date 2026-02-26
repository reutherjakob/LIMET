
<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$projectID = $_SESSION["projectID"];
$elementID = getPostInt('elementID', 0);
$variantenID = getPostInt('variantenID', 0);

$sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, 
        tabelle_parameter_kategorie.Kategorie, tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter
        FROM tabelle_parameter_kategorie 
        INNER JOIN (tabelle_parameter 
        INNER JOIN tabelle_projekt_elementparameter 
            ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
        ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
        WHERE tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ? 
          AND tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = ? 
          AND tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = ?
        ORDER BY tabelle_parameter_kategorie.Kategorie;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iii", $projectID, $elementID, $variantenID);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$mysqli->close();

echo "<table class='table table-striped table-sm' id='tableVariantenParameters' >
	<thead><tr>
        <th>Kategorie</th>
	<th>Parameter</th>
	<th>Wert</th>
	<th>Einheit</th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Kategorie"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Wert"] . "</td>";
    echo "<td>" . $row["Einheit"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
?>

<script charset="utf-8">
    $("#tableVariantenParameters").DataTable({
        savestate: true,
        paging: false,
        searching: true,
        info: false,
        language: {url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
        scrollY: '20vh',
        scrollCollapse: true
    });
</script>
