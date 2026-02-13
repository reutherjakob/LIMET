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

    echo "<table class='table table-striped table-bordered table-sm table-hover border border-light px-0 py-0' id='TableDevicePricesInProjects'>";
    echo "<thead><tr> 
            <th>EP</th>
            <th>NK</th>
            <th>Stk</th>
            <th class='text-center'>      
               <i class='fas fa-calendar-alt' ></i></th>
            <th>Info</th>
            <th>Gerät</th>      
          
            <th>Projekt</th>
            <th>Herst./Lief.</th>
            <th>Beschr.</th>

          </tr></thead><tbody>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = date_create($row["Datum"]);
            echo "<tr>";
            echo "<td  class='text-end'>" . format_money_no_decimals($row["EP"]) . "</td>";
            echo "<td  class='text-end'>" . format_money_no_decimals($row["NK/Stk"]) . "</td>";
            echo "<td>" . $row["Menge"] . "</td>";
            echo "<td>" . date_format($date, 'Y-m-d') . "</td>";
            echo "<td>" . htmlspecialchars($row["Quelle"] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['Gerät']) . "</td>";
            echo "<td>" . htmlspecialchars($row["Projektname"] ?? '') ?? '-' . "</td>";
            echo "<td>" . htmlspecialchars($row["Lieferant"] ?? ' ') . ' - ' .
                htmlspecialchars($row['Hersteller'] ?? '') . "</td>";
            echo "<td>";
            if (!empty($row["Kurzbeschreibung"])) {
                echo "<button type='button' class='btn btn-outline-secondary btn-sm' 
                data-bs-toggle='popover' 
                data-bs-placement='top' 
                data-bs-title=' ' 
                    data-bs-content='" . htmlspecialchars($row["Kurzbeschreibung"])
                    . "'><i class='fas fa-info-circle'></i></button>";
            } else {
                echo "-";
            }
            echo "</td>";
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
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                container: 'body',
                trigger: 'focus hover',
                html: true,
                placement: 'top'
            });
        });

        if ($('#TableDevicePricesInProjects tbody tr').length > 0 &&
            !$('#TableDevicePricesInProjects tbody tr td').hasClass('text-muted')) {
            new DataTable('#TableDevicePricesInProjects', {
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

        $(document).on('click', function (e) {
            $('[data-bs-toggle="popover"]').each(function () {
                if (!$(this).is(e.target) &&
                    $(this).has(e.target).length === 0 &&
                    $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });

    });
</script>