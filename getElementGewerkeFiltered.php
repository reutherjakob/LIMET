<?php
session_start();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
<head>
</head>
<body>
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
   
   
   function br2nl($string){
$return= str_replace(array("<br/>"), "\n", $string);
return $return;
}

?>

<?php
	//$_SESSION["elementID"]=$_GET["elementID"];
	
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	echo "<form class='form-inline'>";
		// Wenn GHG geändert
		if( null != ($_GET["filterValueGHG"])  && null != ($_GET["filterValueGewerke"]) ){
			$sql="SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.Bezeichnung, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
						FROM tabelle_projekte INNER JOIN tabelle_auftraggeber_gewerke ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes
						WHERE (((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."))
						ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";
				$result = $mysqli->query($sql);
					
				echo "<label class='m-4' for='gewerk'>Gewerk</label> 
				  <select class='form-control form-control-sm' id='gewerk'>";
					echo "<option value=0>Bitte auswählen</option>";
				  	while($row = $result->fetch_assoc()) {
				  		if($_GET["filterValueGewerke"] == $row["idTABELLE_Auftraggeber_Gewerke"]){
				  			echo "<option selected value=".$row["idTABELLE_Auftraggeber_Gewerke"].">".$row["Gewerke_Nr"]." - ".$row["Bezeichnung"]."</option>";
				  		}
				  		else{
							echo "<option value=".$row["idTABELLE_Auftraggeber_Gewerke"].">".$row["Gewerke_Nr"]." - ".$row["Bezeichnung"]."</option>";		
						}
					}
				  						 
				  echo "</select>
			  	<label class='m-4' for='ghg'>GHG</label>
					  <select class='form-control form-control-sm' id='ghg'>";					  
					  		$sql="SELECT tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeber_ghg.Bezeichnung, tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG
									FROM tabelle_auftraggeber_ghg
									WHERE (((tabelle_auftraggeber_ghg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke)=".$_GET["filterValueGewerke"]."));";
							$result = $mysqli->query($sql);
					  		echo "<option value=0>Bitte auswählen</option>";
					  		while($row = $result->fetch_assoc()) {
					  			if($_GET["filterValueGHG"] == $row["idtabelle_auftraggeber_GHG"]){
									echo "<option selected value=".$row["idtabelle_auftraggeber_GHG"].">".$row["GHG"]." - ".$row["Bezeichnung"]."</option>";
								}
								else{
									echo "<option value=".$row["idtabelle_auftraggeber_GHG"].">".$row["GHG"]." - ".$row["Bezeichnung"]."</option>";
								}		
							}
					  	echo "</select>
					<label class='m-4' for='gug'>GUG</label>
						  <select class='form-control form-control-sm' id='gug'>";
					  		$sql="SELECT tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG, tabelle_auftraggeberg_gug.GUG, tabelle_auftraggeberg_gug.Bezeichnung
									FROM tabelle_auftraggeberg_gug
									WHERE (((tabelle_auftraggeberg_gug.tabelle_auftraggeber_GHG_idtabelle_auftraggeber_GHG)=".$_GET["filterValueGHG"]."));";
							$result = $mysqli->query($sql);
					  		echo "<option selected value=0>Bitte auswählen</option>";
					  		while($row = $result->fetch_assoc()) {
								echo "<option value=".$row["idtabelle_auftraggeberg_GUG"].">".$row["GUG"]." - ".$row["Bezeichnung"]."</option>";	
							}
				  	echo "</select>";
			
			}
			else{
				// Wenn Gewerk geändert		
				if(null !=  ($_GET["filterValueGewerke"]) ){
					$sql="SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.Bezeichnung, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
								FROM tabelle_projekte INNER JOIN tabelle_auftraggeber_gewerke ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes
								WHERE (((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."))
								ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";
						$result = $mysqli->query($sql);
							
						echo "<label class='m-4' for='gewerk'>Gewerk</label>
						  <select class='form-control form-control-sm' id='gewerk'>";
						  
							echo "<option value=0>Bitte auswählen</option>";
						  	while($row = $result->fetch_assoc()) {
						  		if($_GET["filterValueGewerke"] == $row["idTABELLE_Auftraggeber_Gewerke"]){
						  			echo "<option selected value=".$row["idTABELLE_Auftraggeber_Gewerke"].">".$row["Gewerke_Nr"]." - ".$row["Bezeichnung"]."</option>";
						  		}
						  		else{
									echo "<option value=".$row["idTABELLE_Auftraggeber_Gewerke"].">".$row["Gewerke_Nr"]." - ".$row["Bezeichnung"]."</option>";		
								}
							}
								 
						  echo "</select>
						<label class='m-4' for='ghg'>GHG</label>
							  <select class='form-control form-control-sm' id='ghg'>";
							 
							  		$sql="SELECT tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeber_ghg.Bezeichnung, tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG
											FROM tabelle_auftraggeber_ghg
											WHERE (((tabelle_auftraggeber_ghg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke)=".$_GET["filterValueGewerke"]."));";
									$result = $mysqli->query($sql);
							  		echo "<option selected value=0>Bitte auswählen</option>";
							  		while($row = $result->fetch_assoc()) {
										echo "<option value=".$row["idtabelle_auftraggeber_GHG"].">".$row["GHG"]." - ".$row["Bezeichnung"]."</option>";	
									}
							  echo "</select>
						<label class='m-4' for='gug'>GUG</label>
							  <select class='form-control form-control-sm' id='gug'>";
								  	echo "<option selected value=0>Bitte GHG auswählen</option>";
					  	echo "</select>";
				}
			}					  
			echo "<button type='button' id='saveElementGewerk' class='btn btn-outline-dark btn-sm ml-1' value='saveElementGewerk'><i class='far fa-save'></i>Gewerk speichern</button>		
		</form>";
		$mysqli ->close();
?>

<script>

 	//GHG geändert
	$('#ghg').change(function(){
		var ghgid = $('#ghg').val();
		var gewerkid = $('#gewerk').val();
		if(gewerkid !== 0 && ghgid !== 0){
		    $.ajax({
		        url : "getElementGewerkeFiltered.php",
		        data:{"filterValueGHG":ghgid,"filterValueGewerke":gewerkid},
		        type: "GET",
		        success: function(data){
			            $("#elementGewerk").html(data);		            
				}
		    });
		}
	});

	//Gewerk geändert
	$('#gewerk').change(function(){
		var gewerkid = $('#gewerk').val();
		if(gewerkid !== 0){
		    $.ajax({
		        url : "getElementGewerkeFiltered.php",
		        data:{"filterValueGewerke":gewerkid},
		        type: "GET",
		        success: function(data){
			            $("#elementGewerk").html(data);		            
				}
		    });
		}
	});

	// Gewerk speichern
	$("button[value='saveElementGewerk']").click(function(){
    	if($('#gewerk').val() === "0"){
			alert("Kein Gewerk ausgewählt!");
		}
		else{
	    	$.ajax({
		        url : "saveElementGewerk.php",
		        data:{"gewerk":$('#gewerk').val(),"ghg":$('#ghg').val(),"gug":$('#gug').val()},
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