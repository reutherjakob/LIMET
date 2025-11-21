<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("SELECT tabelle_parameter.Bezeichnung, tabelle_elemente_has_tabelle_parameter.Wert, tabelle_elemente_has_tabelle_parameter.Einheit
			FROM tabelle_parameter INNER JOIN tabelle_elemente_has_tabelle_parameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_elemente_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter
			WHERE (((tabelle_elemente_has_tabelle_parameter.TABELLE_Elemente_idTABELLE_Elemente)=?))");
$elementID = getPostInt("elementID", 0);
$stmt->bind_param('i', $elementID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-sm' id='tableStandardElementParameters' >
	<thead><tr>
	<th>Parameter</th>
	<th>Wert</th>
	<th>Einheit</th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Wert"] . "</td>";
    echo "<td>" . $row["Einheit"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
echo "<button id='" . $elementID . "' class='btn btn-outline-success' value='Elementparameter-Vergleich' data-bs-toggle='modal' data-bs-target='#elementParameterComparisonModal'> Element Parameter Vergleich</button>";
$mysqli->close();
$stmt->close();
?>
<!-- Modal zum Zeigen des Parametervergleichs -->
<div class='modal fade' id='elementParameterComparisonModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Element-Parameter-Vergleich</h4>
            </div>
            <div class='modal-body' id='mbodyElementParameterComparison'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        $("#tableStandardElementParameters").DataTable({
            paging: false,
            searching: false,
            info: false,
            language: {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json", search: ""}
        });
    });

    //Gerätevergleich anzeigen
    $("button[value='Elementparameter-Vergleich']").click(function () {
        let ID = this.id;
        $.ajax({
            url: "getElementParameterComparison.php",
            type: "POST",
            data: {"elementID": ID},
            success: function (data) {
                $("#mbodyElementParameterComparison").html(data);
            }
        });
    });

</script>

</body>
</html>