<?php
// V2.0: 2024-11-29, Reuther & Fux
include "_utils.php";
include "_format.php";
check_login();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<head>
    <title></title></head>
<body>

<?php
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_projekte.Projektname, tabelle_projekte.Interne_Nr, tabelle_projekte.Preisbasis , tabelle_varianten.Variante, tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_varianten INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_projekte ON tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte) ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=" . $_GET["elementID"] . ") AND NOT (tabelle_projekte.Projektname = 'Test1' ) );";
$result = $mysqli->query($sql);
echo "<table class='table table-striped table-bordered table-sm' id='tableElementPricesInProjects'>
            <thead><tr>
            <th>Projekt</th>
            <th>Interne Nr</th>
            <th>Variante</th>
            <th>Kosten</th>
            <th>Preisbasis </th>
            </tr></thead>
            <tbody>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Projektname"] . "</td>";
    echo "<td>" . $row["Interne_Nr"] . "</td>";
    echo "<td>" . $row["Variante"] . "</td>";
    echo "<td>" . format_money($row["Kosten"]) . "</td>";
    echo "<td>" . $row["Preisbasis"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
?>

<script>
    $(document).ready(function () {
        $("#tableElementPricesInProjects").DataTable({
            paging: false,
            searching: false,
            info: false,
            order: [[1, "asc"]],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                decimal: ",",
                thousands: "."
            }
        });
    });

</script>
</body>
</html>