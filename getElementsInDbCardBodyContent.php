<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_elemente.Kurzbeschreibung
  											FROM tabelle_elemente
  											ORDER BY tabelle_elemente.ElementID;";

$result = $mysqli->query($sql);

echo "<table class='table compact table-striped table-sm table-hover border border-light border-5' id='tableElementsInDB' >
  									<thead><tr>
  									<th>ID</th>
  									<th>ElementID</th>
  									<th>Element</th>
  									<th>Beschreibung</th>
                                      <th></th>
  									</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idTABELLE_Elemente"] . "</td>";
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-outline-dark btn-sm' value='changeElement' data-bs-toggle ='modal' data-bs-target='#changeElementModal'><i class='fas fa-pencil-alt'></i></button></td>";
    echo "</tr>";
}
echo "</tbody></table>  ";
$mysqli->close();


?>