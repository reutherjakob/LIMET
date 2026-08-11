<?php
require_once "utils/_utils.php";
include "utils/_format.php";

$elementID = getPostInt('elementID', 0);

if ($elementID > 0) {
    $mysqli = utils_connect_sql();

    $sql = "SELECT 
                g.GeraeteID,
                g.Typ AS Gerät,
                g.Kurzbeschreibung,
                h.Hersteller,
                p.Datum,
                p.Info,
                p.Menge,
                p.WartungspreisProJahr AS EP,
                p.Wartungsart ,
                pr.Projektname,
                l.Lieferant,
                p.Kommentar
            FROM tabelle_geraete g
            INNER JOIN tabelle_hersteller h ON g.tabelle_hersteller_idtabelle_hersteller = h.idtabelle_hersteller
            INNER JOIN tabelle_wartungspreise p ON g.idTABELLE_Geraete = p.TABELLE_Geraete_idTABELLE_Geraete
            LEFT JOIN tabelle_projekte pr ON p.TABELLE_Projekte_idTABELLE_Projekte = pr.idTABELLE_Projekte
            LEFT JOIN tabelle_lieferant l ON p.tabelle_lieferant_idTABELLE_Lieferant = l.idTABELLE_Lieferant
            WHERE g.TABELLE_Elemente_idTABELLE_Elemente = ?
            ORDER BY g.GeraeteID, p.Datum DESC";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $elementID);
    $stmt->execute();
    $result = $stmt->get_result();
    $wartungsartLabels = [
        '0' => 'Betriebswartung',
        '1' => 'Vollwartung',
        '2' => 'Medizintechnikgarantie',
        '3' => 'Ersatzteilgarantie',
    ];
    echo "<table class='table table-striped table-bordered table-sm table-hover border border-light px-0 py-0' id='TableDeviceServicePricesInProjects'>";
    echo "<thead><tr> 
            <th>P/a</th>
            <th>Wartungsart</th>
            <th>Stk</th> 
            <th class='text-center'>      <i class='fas fa-calendar-alt' ></i></th>
            <th>Info</th>
            <th>Gerät</th>      
            <th> <i class='fas fa-euro-sign'></i> </i>  <i class='far fa-comment'></i> </th>      
            <th>Herst./Lief.</th> 
            <th>Projekt</th>
          </tr></thead><tbody>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = date_create($row["Datum"]);
            echo "<tr>";
            echo "<td  class='text-end'>" . format_money_no_decimals($row["EP"]) . "</td>";
            $art = htmlspecialchars($wartungsartLabels[$row["Wartungsart"]] ?? '', ENT_QUOTES, 'UTF-8');
            echo "<td  class='text-end'>" . $art . "</td>";

            echo "<td>" . $row["Menge"] . "</td>";
            echo "<td>" . date_format($date, 'Y-m-d') . "</td>";
            echo "<td>" . htmlspecialchars($row["Info"] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['Gerät']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Kommentar']??'') . "</td>";
            echo "<td>" . htmlspecialchars($row["Lieferant"] ?? ' ') . ' - ' .
                htmlspecialchars($row['Hersteller'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row["Projektname"] ?? '') ?? '-' . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9' class='text-center text-muted'>Keine Gerätepreise gefunden</td></tr>";
    }

    echo "</tbody></table> ";

    $stmt->close();
    $mysqli->close();
}
?>

<script>
    $(document).ready(function () {

        if ($('#TableDeviceServicePricesInProjects tbody tr').length > 0 &&
            !$('#TableDeviceServicePricesInProjects tbody tr td').hasClass('text-muted')) {
            new DataTable('#TableDeviceServicePricesInProjects', {
                paging: false,
                searching: false,
                info: false,
                order: [[3, "desc"]], // Datum absteigend
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                    decimal: ",",
                    thousands: ".",
                },
            });
        }



    });
</script>