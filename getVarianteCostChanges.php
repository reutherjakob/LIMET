<?php
include "_utils.php";
include "_format.php";
check_login();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

echo "<table class='table table-striped table-sm' id='tableVarianteCostChanges' cellspacing='0' width='100%'>
	<thead><tr>
	<th>Variante</th>
	<th>Kosten vorher</th>
	<th>Kosten nachher</th>						
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
    echo "<td>" . $row["user"] . "</td>";
    echo "<td>" . $row["timestamp"] . "</td>";
    echo "</tr>";

}

echo "</tbody></table>";
$mysqli->close();
?>

<script>

    $("#tableVarianteCostChanges").DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "order": [[4, "desc"]],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
    });

</script>
</body>
</html>