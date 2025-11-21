<?php
// 25Fux
require_once 'utils/_utils.php';
include "utils/_format.php";
check_login();


$elementID = getPostInt("elementID", 0);
$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("SELECT tabelle_projekte.Projektname,
       tabelle_projekte.Interne_Nr,
       tabelle_projekte.Preisbasis, 
       tabelle_varianten.Variante,
       tabelle_projekt_varianten_kosten.Kosten
FROM tabelle_varianten
         INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_projekte
                     ON tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =
                        tabelle_projekte.idTABELLE_Projekte) ON tabelle_varianten.idtabelle_Varianten =
                                                                tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten
WHERE (((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) = ?) AND
       NOT (tabelle_projekte.Projektname = 'Test1'))");
$stmt->bind_param('i', $elementID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableElementPricesInProjects'>
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
        new DataTable('#tableElementPricesInProjects', {
            paging: false,
            searching: false,
            info: false,
            order: [[1, "asc"]],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                decimal: ",",
                thousands: ".",
                emptyTable: "Keine Daten verfügbar"
            },

        });
    });


</script>
</body>
</html>