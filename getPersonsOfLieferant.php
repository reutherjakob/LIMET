<?php
// 25FX
include "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$lieferantenID = getPostInt('lieferantID', 0);

$stmt = $mysqli->prepare("SELECT tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_ansprechpersonen.Tel, tabelle_ansprechpersonen.Adresse, tabelle_ansprechpersonen.PLZ, tabelle_ansprechpersonen.Ort, tabelle_ansprechpersonen.Land, tabelle_ansprechpersonen.Mail, tabelle_abteilung.Abteilung
                        FROM tabelle_abteilung INNER JOIN tabelle_ansprechpersonen ON tabelle_abteilung.idtabelle_abteilung = tabelle_ansprechpersonen.tabelle_abteilung_idtabelle_abteilung
                        WHERE (((tabelle_ansprechpersonen.tabelle_lieferant_idTABELLE_Lieferant)=?))");
$stmt->bind_param("i", $lieferantenID);
$result = $stmt->execute();

echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tablePersonsOfLieferant'   >
                <thead><tr>
                <th>ID</th>
                <th>Name</th>
                <th>Vorname</th>
                <th>Tel</th>
                <th>Mail</th>
                <th>Adresse</th>
                <th>PLZ</th>
                <th>Ort</th>
                <th>Land</th>
                <th>Abteilung</th>
                </tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idTABELLE_Ansprechpersonen"] . "</td>";
    echo "<td>" . $row["Name"] . "</td>";
    echo "<td>" . $row["Vorname"] . "</td>";
    echo "<td>" . $row["Tel"] . "</td>";
    echo "<td>" . $row["Mail"] . "</td>";
    echo "<td>" . $row["Adresse"] . "</td>";
    echo "<td>" . $row["PLZ"] . "</td>";
    echo "<td>" . $row["Ort"] . "</td>";
    echo "<td>" . $row["Land"] . "</td>";
    echo "<td>" . $row["Abteilung"] . "</td>";
    echo "</tr>";

}
echo "</tbody></table>";
?>

<script>

    document.addEventListener('DOMContentLoaded', () => {
        const table = new DataTable('#tablePersonsOfLieferant', {
            columnDefs: [
                {
                    targets: 0,
                    visible: false,
                    searchable: false
                }
            ],
            paging: false,
            searching: false,
            info: true,
            order: [[1, 'asc']],
            pagingType: 'simple_numbers',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
            }
        });
    });

</script>
