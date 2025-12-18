<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("SELECT tabelle_rb_aenderung.Timestamp, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_rb_aenderung.Anzahl, tabelle_rb_aenderung.Anzahl_copy1, tabelle_rb_aenderung.Standort, tabelle_rb_aenderung.Standort_copy1, tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten, tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten_copy1, tabelle_rb_aenderung.Kurzbeschreibung, tabelle_rb_aenderung.Kurzbeschreibung_copy1
       FROM tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_rb_aenderung ON tabelle_räume_has_tabelle_elemente.id = tabelle_rb_aenderung.id) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
       WHERE (tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume=?) 
       AND ((tabelle_rb_aenderung.Anzahl_copy1!=tabelle_rb_aenderung.Anzahl) 
         OR (tabelle_rb_aenderung.Standort_copy1!=tabelle_rb_aenderung.Standort) 
         OR (tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten_copy1!=tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten) 
         OR (tabelle_rb_aenderung.Kurzbeschreibung_copy1!=tabelle_rb_aenderung.Kurzbeschreibung) 
         OR (tabelle_rb_aenderung.Anzahl IS NULL))
       ORDER BY tabelle_rb_aenderung.Timestamp DESC");

$roomID = (int)$_SESSION["roomID"];
$stmt->bind_param("i", $roomID);
$stmt->execute();
$result = $stmt->get_result();


echo "<table class='table table-striped' id='tableRoombookChanges'  >
	<thead><tr>
	<th>Zeitpunkt</th>
	<th>ID</th>
	<th>Element</th>
	<th>Anzahl vorher</th>
	<th>Anzahl nachher</th>
	<th>Standort vorher</th>
	<th>Standort nachher</th>
	<th>Variante vorher</th>
	<th>Variante nachher</th>
	<th>Kommentar vorher</th>
	<th>Kommentar nachher</th>
	</tr></thead><tbody>";


while ($row = $result->fetch_assoc()) {

    echo "<tr>";
    echo "<td>" . $row["Timestamp"] . "</td>";
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Anzahl"] . "</td>";
    echo "<td>" . $row["Anzahl_copy1"] . "</td>";
    echo "<td>" . $row["Standort"] . "</td>";
    echo "<td>" . $row["Standort_copy1"] . "</td>";
    echo "<td>";
    switch ($row["tabelle_Varianten_idtabelle_Varianten"]) {
        case 1:
            echo "A";
            break;
        case 2:
            echo "B";
            break;
        case 3:
            echo "C";
            break;
        case 4:
            echo "D";
            break;
        case 5:
            echo "E";
            break;
        case 6:
            echo "F";
            break;
        case 7:
            echo "G";
            break;
    }
    echo "</td>";
    echo "<td>";
    switch ($row["tabelle_Varianten_idtabelle_Varianten_copy1"]) {
        case 1:
            echo "A";
            break;
        case 2:
            echo "B";
            break;
        case 3:
            echo "C";
            break;
        case 4:
            echo "D";
            break;
        case 5:
            echo "E";
            break;
        case 6:
            echo "F";
            break;
        case 7:
            echo "G";
            break;
    }
    echo "</td>";
    echo "<td class='col-xxl-2'><textarea class='form-control' style='width: 100%;'>" . $row["Kurzbeschreibung"] . "</textarea></td>";
    echo "<td class='col-xxl-2'><textarea class='form-control' style='width: 100%;'>" . $row["Kurzbeschreibung_copy1"] . "</textarea></td>";
    echo "</tr>";

}

echo "</tbody></table>";

$mysqli->close();

?>
<script>

    $(document).ready(function () {
        $('#tableRoombookChanges').DataTable({
            "paging": true,
            "pagingType": "simple_numbers",
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
    });
</script>
