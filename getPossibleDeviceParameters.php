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
	    
			
	$sql = "SELECT tabelle_parameter.idTABELLE_Parameter, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie 
			  					FROM tabelle_parameter, tabelle_parameter_kategorie 
			  					WHERE tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie 
								AND tabelle_parameter.idTABELLE_Parameter NOT IN 
								(SELECT tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter
                                                                FROM tabelle_geraete_has_tabelle_parameter
                                                                WHERE (((tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete)=".$_SESSION["deviceID"]."))) 
								ORDER BY tabelle_parameter_kategorie.Kategorie;";	
						
        $result = $mysqli->query($sql);

        echo "<table class='table table-striped table-sm' id='tablePossibleDeviceParameters'  >
        <thead><tr>
        <th>ID</th>
        <th>Kategorie</th>
        <th>Parameter</th>
        </tr></thead>
        <tbody>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><button type='button' id='".$row["idTABELLE_Parameter"]."' class='btn btn-outline-success btn-sm' value='addParameter'><i class='fas fa-plus'></i></button></td>";
            echo "<td>".$row["Kategorie"]."</td>";
            echo "<td>".$row["Bezeichnung"]."</td>";
            echo "</tr>";

        }

        echo "</tbody></table>";

        $mysqli ->close();
?>
	
<script>
    
    
    $('#tablePossibleDeviceParameters').DataTable( {
            "paging": false,
            "searching": true,
            "info": false,
            "order": [[ 1, "asc" ]],
            "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": true,
                "searchable": false
            }
        ],
        "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
        "scrollY": '20vh',
        "scrollCollapse": true   	 
     } );  
     
     //Parameter zu Gerät hinzufügen
    $("button[value='addParameter']").click(function(){
        var id = this.id;
        if(id !== ""){
            $.ajax({
                url : "addParameterToDevice.php",
                data:{"parameterID":id},
                type: "GET",
                success: function(data){
                    alert(data);
                    $.ajax({
                        url : "getDeviceParameters.php",
                        type: "GET",
                        success: function(data){
                            $("#deviceParameters").html(data);
                            $.ajax({
                                url : "getPossibleDeviceParameters.php",
                                type: "GET",
                                success: function(data){
                                    $("#possibleDeviceParameters").html(data); 
                                }                                 
                            }); 
                            
                        } 
                    }); 
                } 
            }); 
        }		
    });   
     

</script>

</body>
</html>