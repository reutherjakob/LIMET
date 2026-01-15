<?php
// getDevicesAndTheirPricesForElement.php
require_once "utils/_utils.php";
include "utils/_format.php";

$elementID = getPostInt('elementID', 0);

if ($elementID > 0) {
    $mysqli = utils_connect_sql();

    $sql = "SELECT 
                g.GeraeteID,
                g.Typ AS Gerät,
                h.Hersteller,
                p.Datum,
                p.Quelle,
                p.Menge,
                p.Preis AS EP,
                p.Nebenkosten AS 'NK/Stk', 
                pr.Projektname,
                l.Lieferant
            FROM tabelle_geraete g
            INNER JOIN tabelle_hersteller h ON g.tabelle_hersteller_idtabelle_hersteller = h.idtabelle_hersteller
            INNER JOIN tabelle_preise p ON g.idTABELLE_Geraete = p.TABELLE_Geraete_idTABELLE_Geraete
            LEFT JOIN tabelle_projekte pr ON p.TABELLE_Projekte_idTABELLE_Projekte = pr.idTABELLE_Projekte
            LEFT JOIN tabelle_lieferant l ON p.tabelle_lieferant_idTABELLE_Lieferant = l.idTABELLE_Lieferant
            WHERE g.TABELLE_Elemente_idTABELLE_Elemente = ?
            ORDER BY g.GeraeteID, p.Datum DESC";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $elementID);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<div class='table-responsive'>";
    echo "<table class='table table-sm table-striped table-hover'>";
    echo "<thead class='table-dark'><tr>
            <th>Gerät</th>
            <th>Hersteller</th>
            <th>Datum</th>
            <th>Info</th>
            <th>Menge</th>
            <th>EP</th>
            <th>NK/Stk</th>
            <th>Projekt</th>
            <th>Lieferant</th>
          </tr></thead><tbody>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = date_create($row["Datum"]);
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($row['Gerät']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($row['Hersteller']) . "</td>";
            echo "<td>" . date_format($date, 'Y-m-d') . "</td>";
            echo "<td>" . htmlspecialchars($row["Quelle"]) . "</td>";
            echo "<td>" . $row["Menge"] . "</td>";
            echo "<td>" . format_money($row["EP"]) . "</td>";
            echo "<td>" . format_money($row["NK/Stk"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Projektname"]) ?? '-' . "</td>";
            echo "<td>" . htmlspecialchars($row["Lieferant"]) ?? '-' . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9' class='text-center text-muted py-4'>Keine Gerätepreise gefunden</td></tr>";
    }

    echo "</tbody></table></div>";

    $stmt->close();
    $mysqli->close();
}
?>
