<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	   
    $sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_parameter_kategorie.Kategorie, tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter
								FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
								WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente)=".$_SESSION["elementID"].") AND ((tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten)=".$_GET["variantenID"]."))
								ORDER BY tabelle_parameter_kategorie.Kategorie ASC, tabelle_parameter.Bezeichnung ASC;";						
	
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-sm' id='tableVarianteParameters' cellspacing='0'>
	<thead><tr>
	<th></th>
	<th>Kategorie</th>
	<th>Parameter</th>
	<th>Wert</th>
	<th>Einheit</th>
	<th></th>
	</tr></thead>
	<tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";						 
		echo "<td><button type='button' id='".$row["tabelle_parameter_idTABELLE_Parameter"]."' class='btn btn-outline-danger btn-xs' value='deleteParameter'><i class='fas fa-minus'></i></button></td>";
	    echo "<td>".$row["Kategorie"]."</td>";
	    echo "<td>".$row["Bezeichnung"]."</td>";
	    echo "<td><input type='text' id='wert".$row["tabelle_parameter_idTABELLE_Parameter"]."' value='".$row["Wert"]."' size='20'></input></td>";
		echo "<td><input type='text' id='einheit".$row["tabelle_parameter_idTABELLE_Parameter"]."' value='".$row["Einheit"]."' size='45'></input></td>";
		echo "<td><button type='button' id='".$row["tabelle_parameter_idTABELLE_Parameter"]."' class='btn btn-warning btn-sm' value='saveParameter'><i class='far fa-save'></i></button></td>";
	    echo "</tr>";
	    
	}
	
	echo "</tbody></table>";
	
	$mysqli ->close();
	?>
	
<script>
    
	$(document).ready(function() {
		$('#tableVarianteParameters').DataTable( {
			"paging": false,
			"searching": false,
			"info": false,
			"order": [[ 1, "asc" ]],
			"columnDefs": [
	            {
	                "targets": [ 0 ],
	                "visible": true,
	                "searchable": false,
                        "sortable": false
	            }
	        ],
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
	        "scrollY":        '20vh',
	    	"scrollCollapse": true   	 
	     } ); 	    
	} ); 	    

	//Parameter von Variante entfernen
	$("button[value='deleteParameter']").click(function(){
	    var variantenID = $('#variante').val(); 
	    var id = this.id;
	    
	    if(id !== ""){			 
	        $.ajax({
		        url : "deleteParameterFromVariante.php",
		        data:{"parameterID":id,"variantenID":variantenID},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getVarianteParameters.php",
				        data:{"variantenID":variantenID},
				        type: "GET",
				        success: function(data){
				        	$("#variantenParameter").html(data);
				        	$.ajax({
						        url : "getPossibleVarianteParameters.php",
						        data:{"variantenID":variantenID},
						        type: "GET",
						        success: function(data){
						        	$("#possibleVariantenParameter").html(data);
						        } 
					        }); 

				        } 
			        }); 

		        } 
	        }); 
	    }
		
    });
    
    // Parameter ändern bzw speichern
	$("button[value='saveParameter']").click(function(){
	    var id=this.id; 
	    var wert = $("#wert"+id).val();
    	var einheit = $("#einheit"+id).val();
		var variantenID = $('#variante').val();
		
	    if(id !== ""){
	    	$.ajax({
		        url : "updateParameter.php",
		        data:{"parameterID":id,"wert":wert,"einheit":einheit,"variantenID":variantenID},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        }
		    });		    
	    }
	    
	});


</script>

</body>
</html>