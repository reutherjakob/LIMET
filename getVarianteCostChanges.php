<?php
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
include "utils/_format.php";
check_login();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title>
</head>
<body>


<?php
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_varianten.Variante, tabelle_projekt_varianten_kosten_aenderung.kosten_alt, tabelle_projekt_varianten_kosten_aenderung.kosten_neu, tabelle_projekt_varianten_kosten_aenderung.timestamp, tabelle_projekt_varianten_kosten_aenderung.user
                FROM tabelle_varianten INNER JOIN tabelle_projekt_varianten_kosten_aenderung ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_varianten_kosten_aenderung.variante
                WHERE (((tabelle_projekt_varianten_kosten_aenderung.projekt)=" . $_SESSION["projectID"] . ") AND ((tabelle_projekt_varianten_kosten_aenderung.element)=" . $_GET["elementID"] . "))
                ORDER BY tabelle_projekt_varianten_kosten_aenderung.timestamp DESC;";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm' id='tableVarianteCostChanges'  >
	<thead> <tr>
	<th>Variante</th>
	<th>Kosten <i class='fas fa-hourglass-start'></th>
	<th>Kosten <i class='fas fa-hourglass-end'></th>						
	<th>User</th>
	<th>Datum</th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Variante"] . "</td>";
    echo "<td>" . format_money($row["kosten_alt"]) . "</td>";
    echo "<td>" . format_money($row["kosten_neu"]) . "</td>";
    echo "<td>" . $row["user"] . "</td>";
    echo "<td>" . $row["timestamp"] . "</td>";
    echo "</tr>";

}
echo "</tbody></table>";
$mysqli->close();
?>

<script>

    $(document).ready(function () {
        new DataTable('#tableVarianteCostChanges', {
            paging: false,
            searching: false,
            info: false,
            order: [[4, 'desc']],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: null
            }
        });
    });

</script>
</body>
</html>