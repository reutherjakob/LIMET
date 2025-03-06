<?php
session_start();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /></head>
<body>
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
?>

<?php
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	}
	
        $sql = "SELECT tabelle_workflow.idtabelle_workflow, tabelle_workflow.Name
                FROM tabelle_workflowtyp INNER JOIN (tabelle_workflow_has_tabelle_projekte INNER JOIN tabelle_workflow ON tabelle_workflow_has_tabelle_projekte.tabelle_workflow_idtabelle_workflow = tabelle_workflow.idtabelle_workflow) ON tabelle_workflowtyp.idtabelle_workflowtyp = tabelle_workflow.tabelle_workflowtyp_idtabelle_workflowtyp
                WHERE (((tabelle_workflow_has_tabelle_projekte.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_workflow.tabelle_workflowtyp_idtabelle_workflowtyp)=1));";
        
	$result = $mysqli->query($sql);	     
        echo "<table class='table table-sm' id='tableprojectWorkflows'  >
            <thead><tr>
            <th>WorkflowID</th>
            <th>Workflow</th>
            <th></th>						
            </tr></thead>
            <tbody>";

        while($row = $result->fetch_assoc()) {                     
            echo "<tr>";
            echo "<td>".$row["idtabelle_workflow"]."</td>";
            echo "<td>".$row["Name"]."</td>";
            echo "<td><button type='button' id='".$row["idtabelle_workflow"]."' class='btn btn-outline-success btn-sm' value='addWorkflow'><i class='fas fa-plus'></i></button></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        
	$mysqli ->close();
	?>
	
<script>
    
    
    $("#tableprojectWorkflows").DataTable( {
        "paging": false,
        "searching": false,
        "info": false,
        "order": [[ 1, "asc" ]],
        "ordering": false,
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false
            }
        ],
        //"pagingType": "simple_numbers",
        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"} 		     
    } );
    
    
    // Workfloweintrag speichern
    
    $("button[value='addWorkflow']").click(function(){
        var workflowID = this.id;   
        
        if(workflowID === ""){
            alert("Keinen Workflow gefunden!");
        }
        else{                
            $.ajax({
                url : "addWorkflowToLot.php",
                data:{"workflowID":workflowID},
                type: "GET",
                success: function(data){
                    $.ajax({
                        url : "getLotWorkflow.php",
                        type: "GET",
                        success: function(data){
                            $("#workflowModalBody").html(data);	            			 			
                        } 
                    });
                    $("#infoBody").html(data);	
                    $('#infoModal').modal('show');                        
                } 
            });                
        }   
        
    });
    
</script>

</body>
</html>