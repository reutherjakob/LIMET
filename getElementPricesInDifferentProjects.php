<?php
// 25 Fx
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
       NOT (tabelle_projekte.idTABELLE_Projekte = 4) AND NOT (tabelle_projekte_idTABELLE_Projekte =1) )");
$stmt->bind_param('i', $elementID);
$stmt->execute();
$result = $stmt->get_result();

echo "<div class='table-responsive'>
      <table class='table table-striped table-bordered table-sm table-hover' id='tableElementPricesInProjects'>
            <thead><tr>
            <th>Projekt</th>
            <th>Interne Nr</th>
            <th>Variante</th>
            <th>Kosten</th>
            <th>Preisbasis</th>
            </tr></thead>
            <tbody>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["Projektname"] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row["Interne_Nr"]?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row["Variante"]?? '') . "</td>";
        echo "<td class='text-end'>" . format_money_no_decimals($row["Kosten"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Preisbasis"]?? '') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center text-muted'>Keine Schätzkosten gefunden</td></tr>";
}

echo "</tbody></table></div>";
?>

<script>
    $(document).ready(function () {
        // Nur initialisieren wenn Daten vorhanden
        if ($('#tableElementPricesInProjects tbody tr').length > 0 &&
            !$('#tableElementPricesInProjects tbody tr td').hasClass('text-muted')) {
            new DataTable('#tableElementPricesInProjects', {
                paging: false,
                searching: false,
                info: false,
                order: [[1, "asc"]],
                scrollCollapse: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                    decimal: ",",
                    thousands: ".",
                },
            });
        }
    });
</script>