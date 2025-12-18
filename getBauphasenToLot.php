<?php
// 25 FX
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();
$lotID = getPostInt('lotID', 0);

$stmt = $mysqli->prepare("SELECT tabelle_bauphasen.bauphase, 
                                 tabelle_bauphasen.datum_beginn, 
                                 tabelle_bauphasen.datum_fertigstellung, 
                                 SUM(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, 
                                 tabelle_bauphasen.idtabelle_bauphasen
                          FROM tabelle_räume_has_tabelle_elemente 
                          INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume
                          LEFT JOIN tabelle_bauphasen ON tabelle_räume.tabelle_bauphasen_idtabelle_bauphasen = tabelle_bauphasen.idtabelle_bauphasen
                          WHERE tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern = ?
                          GROUP BY tabelle_bauphasen.bauphase, tabelle_bauphasen.datum_beginn, tabelle_bauphasen.datum_fertigstellung, tabelle_bauphasen.idtabelle_bauphasen
                          ORDER BY tabelle_bauphasen.datum_beginn");

$stmt->bind_param("i", $lotID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableBauphasen'   >
    <thead><tr>  
    <th>id</th>
    <th>Bauphase</th>
    <th>Stk</th>
    <th>Beginn</th>
    <th>Fertigstellung</th> 
    </tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idtabelle_bauphasen"] . "</td>";
    echo "<td>" . $row["bauphase"] . "</td>";
    echo "<td>" . $row["SummevonAnzahl"] . "</td>";
    echo "<td>" . $row["datum_beginn"] . "</td>";
    echo "<td>" . $row["datum_fertigstellung"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

$stmt->close();
$mysqli->close();
?>

<script>
    $(document).ready(function () {
        new DataTable('#tableBauphasen', {
            select: true,
            searching: false,
            paging: false,
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            order: [[3, "asc"]],
            orderMulti: false,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
            },
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: null
            }
        });
    });
</script>
