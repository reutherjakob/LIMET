<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$username = $_SESSION['username'] ?? '';

$stmt = $mysqli->prepare("SELECT tabelle_projekte.idTABELLE_Projekte, 
                                 tabelle_projekte.Interne_Nr, 
                                 tabelle_projekte.Projektname,
                                 tabelle_projekte.Aktiv, 
                                 tabelle_projekte.Neubau, 
                                 tabelle_projekte.Bettenanzahl,
                                 tabelle_projekte.BGF, 
                                 tabelle_projekte.NF, 
                                 tabelle_projekte.Ausfuehrung,
                                 tabelle_projekte.Preisbasis,
                                 tabelle_planungsphasen.Bezeichnung, 
                                 tabelle_planungsphasen.idTABELLE_Planungsphasen
                          FROM tabelle_projekte 
                          INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen 
                          INNER JOIN tabelle_users_have_projects ON tabelle_projekte.idTABELLE_Projekte = tabelle_users_have_projects.tabelle_projekte_idTABELLE_Projekte 
                          WHERE tabelle_users_have_projects.User = ? 
                          ORDER BY tabelle_projekte.Interne_Nr");

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();


echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableBestandElements'   >
    <thead><tr>    
    <th>Stück</th>
    <th>Element</th>
    <th>Variante</th> 
    <th>Gewerk</th>
    <th>Lieferant</th>
    <th>Lieferdatum</th> 
    </tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Anzahl"] . "</td>";
    echo "<td>" . $row["ElementID"] . " " . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Variante"] . "</td>";
    echo "<td>" . $row["LosNr_Extern"] . "</td>";
    echo "<td>" . $row["Lieferant"] . "</td>";
    echo "<td>" . $row["Lieferdatum"] . "</td>";
    echo "</tr>";

}
echo "</tbody></table>";
$mysqli->close();
?>
<script>
    $(document).ready(function () {
        $('#tableBestandElements').DataTable({
            select: false,
            paging: true,
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            order: [[1, "asc"]],
            orderMulti: true,
            language: {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
    });
</script>
