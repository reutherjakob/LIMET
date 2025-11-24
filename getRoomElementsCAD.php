<?php
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_elemente.Bezeichnung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.Standort, tabelle_elemente.CAD_notwendig, tabelle_elemente.CAD_familie_vorhanden, tabelle_elemente.CAD_familie_kontrolliert, tabelle_elemente.CAD_dwg_vorhanden, tabelle_elemente.CAD_Kommentar
			FROM ((tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_varianten ON tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_varianten.idtabelle_Varianten) LEFT JOIN (tabelle_hersteller RIGHT JOIN tabelle_geraete ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
			WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . "))
			ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);
echo "<table class='table table-striped' id='tableRoomElements' >
	<thead><tr>
	<th>Stück</th>
	<th>Element ID</th>
	<th>Variante</th>
	<th>Element</th>
	<th>Kommentar</th>
    <th class='d-flex justify-content-center' data-bs-toggle='tooltip' title='Standort'><i class='fab fa-periscope '></i></th>
	<th>CAD-Notwendigkeit</th>
	<th>Revit-Familie</th>
	<th>Revit-Freigabe</th>
	<th>DWG</th>
	<th>CAD-Kommentar</th>
	</tr></thead><tbody>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Anzahl"] . "</td>";
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["Variante"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td class='col-xxl-2'><textarea class='form-control' style='width: 100%;'>" . $row["Kurzbeschreibung"] . "</textarea></td>";
    echo "<td>";
    if ($row["Standort"] == 1) {
        echo "Ja";
    } else {
        echo "Nein";
    }
    echo "</td>";

    echo "<td>";
    if ($row["CAD_notwendig"] == 1) {
        echo "Ja";
    } else {
        echo "Nein";
    }
    echo "</td>";
    echo "<td>";
    if ($row["CAD_familie_vorhanden"] == 1) {
        echo "Ja";
    } else {
        echo "Nein";
    }
    echo "</td>";
    echo "<td>";
    if ($row["CAD_familie_kontrolliert"] == 0) {
        echo "Nicht geprüft";
    } else {
        if ($row["CAD_familie_kontrolliert"] == 1) {
            echo "Freigegeben";
        } else {
            echo "Überarbeiten";
        }
    }
    echo "</td>";

    echo "<td>";
    if ($row["CAD_dwg_vorhanden"] == 1) {
        echo "Ja";
    } else {
        echo "Nein";
    }
    echo "</td>";
    echo "<td class='col-xxl-2'><textarea class='form-control' style='width: 100%;'>" . $row["CAD_Kommentar"] . "</textarea></td>";
    echo "</tr>";

}
echo "</tbody></table>";
$mysqli->close();
?>

<script src="utils/_utils.js"></script>
<script>
    $(document).ready(function () {
        $('#tableRoomElements').DataTable({
             paging: true,
             pagingType: "simple_numbers",
             lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
             language: {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
    });
</script>