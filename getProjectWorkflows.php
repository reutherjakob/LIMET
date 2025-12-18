<?php
// 25 FX
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();


$sql = "SELECT tabelle_workflow.idtabelle_workflow, tabelle_workflow.Name
FROM tabelle_workflowtyp
         INNER JOIN (tabelle_workflow_has_tabelle_projekte INNER JOIN tabelle_workflow
                     ON tabelle_workflow_has_tabelle_projekte.tabelle_workflow_idtabelle_workflow =
                        tabelle_workflow.idtabelle_workflow) ON tabelle_workflowtyp.idtabelle_workflowtyp =
                                                                tabelle_workflow.tabelle_workflowtyp_idtabelle_workflowtyp
WHERE tabelle_workflow_has_tabelle_projekte.tabelle_projekte_idTABELLE_Projekte =?
AND tabelle_workflow.tabelle_workflowtyp_idtabelle_workflowtyp = 1";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-sm' id='tableprojectWorkflows'  >
            <thead><tr>
            <th>WorkflowID</th>
            <th>Workflow</th>
            <th></th>						
            </tr></thead>
            <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idtabelle_workflow"] . "</td>";
    echo "<td>" . $row["Name"] . "</td>";
    echo "<td><button type='button' id='" . $row["idtabelle_workflow"] . "' class='btn btn-outline-success btn-sm' value='addWorkflow'><i class='fas fa-plus'></i></button></td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>

<script>
    new DataTable('#tableprojectWorkflows', {
        paging: false,
        searching: false,
        info: false,
        order: [[1, "asc"]],
        ordering: false,
        columnDefs: [
            {
                targets: [0],
                visible: false
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
        },
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: null,
            bottomEnd: null
        }
    });

    $("button[value='addWorkflow']").click(function () {
        let workflowID = this.id;
        if (workflowID === "") {
            alert("Keinen Workflow gefunden!");
        } else {
            $.ajax({
                url: "addWorkflowToLot.php",
                data: {"workflowID": workflowID},
                type: "POST",
                success: function (data) {
                    $.ajax({
                        url: "getLotWorkflow.php",
                        type: "POST",
                        success: function (data) {
                            $("#workflowModalBody").html(data);
                        }
                    });
                    makeToaster("Workflow erfolgreich zu Los hinzugefügt!", data = "Erfolg!");

                }
            });
        }
    });
</script>
