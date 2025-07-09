<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title></head>
<body>

<?php
// REWORKED 25
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }

check_login();
?>

<?php
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_parameter_kategorie.Kategorie
		FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN (tabelle_projekt_elementparameter INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
		WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.id)=" . $_GET["id"] . "))
		ORDER BY tabelle_parameter_kategorie.Kategorie;";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm table-hover table-bordered border border-light border-5' id='tableElementParameters'>
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
echo "</tbody></table>";

$mysqli->close();
?>

<script>
    $('#tableElementParameters').DataTable({
        select: true,
        searching: true,
        info: true,
        order: [[1, 'asc']],
        columnDefs: [
            {
                targets: [0],
                visible: true,
                searchable: false,
                sortable: false
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
            search: "",
            searchPlaceholder: "Suche..."
        },
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: ['info', 'search'],
            bottomEnd: ['paging', 'pageLength']
        },
        scrollX: true,
        initComplete: function () {
           $('#variantenParameterCh .xxx').remove();
           $('#variantenParameter .dt-search label').remove();
           $('#variantenParameter .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#variantenParameterCH');
        }
    });


</script>

</body>
</html>