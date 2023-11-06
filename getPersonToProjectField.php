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
					
	$sql="SELECT tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten, tabelle_projektzuständigkeiten.Zuständigkeit FROM tabelle_projektzuständigkeiten ORDER BY Zuständigkeit;";
	$result = $mysqli->query($sql);

	
		   
	echo "<form class='form-horizontal' role='form'>
		 <div class='form-group row'>
	 			<label class='control-label col-md-2' for='Name'>Name</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Name'></input>
				</div>						  			 											 						 			
	 	</div>		  			 		
	 	<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Vorname'>Vorname</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Vorname'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Tel'>Tel</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Tel'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Adresse'>Adresse</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Adresse'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='PLZ'>PLZ</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='PLZ'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Ort'>Ort</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Ort'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Land'>Land</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Land'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Email'>Email</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Email'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='zustaendigkeit'>Zuständigkeit</label>
				<div class='col-md-8'>
					<select class='form-control form-control-sm' id='zustaendigkeit' name='selectCategory'>";
						while($row = $result->fetch_assoc()) {
							echo "<option value=".$row["idTABELLE_Projektzuständigkeiten"].">".$row["Zuständigkeit"]."</option>";		
						}	
					echo "</select>	
				</div>
		</div>";
		
		$sql="SELECT tabelle_organisation.idtabelle_organisation, tabelle_organisation.Organisation FROM tabelle_organisation ORDER BY Organisation;";
		$result = $mysqli->query($sql);
		
		echo "<div class='form-group row'>
	 			<label class='control-label col-md-2' for='organisation'>Organisation</label>
				<div class='col-md-8'>
					<select class='form-control form-control-sm' id='organisation' name='organisation'>";
						while($row = $result->fetch_assoc()) {
							echo "<option value=".$row["idtabelle_organisation"].">".$row["Organisation"]."</option>";		
						}	
					echo "</select>	
				</div>
		</div>
                <div class='form-group row'>
	 			<label class='control-label col-md-2' for='Raumnr'>Raumnr</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Raumnr'></input>
				</div>	
		</div>
		
		
	 	<div class='form-group row'>
	 			<input type='button' id='addPersonToProjectButton' class='btn btn-success btn-sm' value='Person zu Projekt hinzufügen'></input>
	 	</div>			  
	</form>";
	
	$mysqli ->close();

	
	?>
	
<script>
	
	var test;
	
	// Projekt auswählen
	$("input[value='Person zu Projekt hinzufügen']").click(function(){
	    
		 var Name= $("#Name").val();
		 var Vorname= $("#Vorname").val();
		 var Tel= $("#Tel").val();
		 var Adresse= $("#Adresse").val();
		 var PLZ= $("#PLZ").val();
		 var Ort= $("#Ort").val();
		 var Land=  $("#Land").val();
		 var Email=  $("#Email").val();
		 var zustaendigkeit=  $("#zustaendigkeit").val();
		 var organisation=  $("#organisation").val();
                 var Raumnr=  $("#Raumnr").val();
	 	
	 	 if(Name.length > 0 && Vorname.length > 0 && Tel.length > 0){
	 	 	
			 $.ajax({
		        url : "addPersonToProject.php",
		        data:{"Name":Name,"Vorname":Vorname,"Tel":Tel,"Adresse":Adresse,"PLZ":PLZ,"Ort":Ort,"Land":Land,"Email":Email,"zustaendigkeit":zustaendigkeit,"organisation":organisation,"Raumnr":Raumnr},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getPersonsOfProject.php",
				        type: "GET",
				        success: function(data){			        
				            $("#personsInProject").html(data);
						} 
		   			});
		        } 
		    });
		    
		   
	 	 }
	 	 else{
	 	 	alert("Bitte überprüfen Sie Ihre Angaben");
	 	 }
	 
    });


</script>

</body>
</html>