<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title></head>
<body>

<?php
// REWORKED 25
include "_utils.php";

check_login();
?>

<?php
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_parameter_kategorie.Kategorie
		FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN (tabelle_projekt_elementparameter INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
		WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.id)=" . $_GET["id"] . "))
		ORDER BY tabelle_parameter_kategorie.Kategorie;";

$result = $mysqli->query($sql);

echo "<div class='table-responsive'><table class='table table-striped table-sm' id='tableElementParameters'  >
	<thead><tr>
	<th>Parameter</th>
	<th>Wert</th>
	<th>Einheit</th>
        <th>Kategorie</th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Wert"] . "</td>";
    echo "<td>" . $row["Einheit"] . "</td>";
    echo "<td>" . $row["Kategorie"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table></div>";

$mysqli->close();
?>

<script>
    new DataTable("#tableElementParameters", {
        paging: false,
        searching: false,
        info: false,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
            search: "",
            searchPlaceholder: "Suche..."
        },
        scrollY: '20vh',
        scrollCollapse: true
    });


</script>

</body>
</html>