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
	
	// Personendaten im Projekt laden	
	$sql = "SELECT tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte, 
			tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen, 
			tabelle_ansprechpersonen.Name, 
			tabelle_ansprechpersonen.Vorname, 
			tabelle_ansprechpersonen.Tel, 
			tabelle_ansprechpersonen.Adresse, 
			tabelle_ansprechpersonen.PLZ, 
			tabelle_ansprechpersonen.Ort, 
			tabelle_ansprechpersonen.Land, 
			tabelle_ansprechpersonen.Mail, 
                        tabelle_ansprechpersonen.Raumnr,
			tabelle_organisation.idtabelle_organisation, 
			tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten
			FROM tabelle_ansprechpersonen INNER JOIN (tabelle_organisation INNER JOIN (tabelle_projektzuständigkeiten INNER JOIN tabelle_projekte_has_tabelle_ansprechpersonen ON tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten = tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten) ON tabelle_organisation.idtabelle_organisation = tabelle_projekte_has_tabelle_ansprechpersonen.tabelle_organisation_idtabelle_organisation) ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen
			WHERE (((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen)=".$_GET["personID"]."));";
	
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	
	$name = $row["Name"];
	$vorname = $row["Vorname"];
	$tel = $row["Tel"];
	$adresse = $row["Adresse"];
	$plz = $row["PLZ"];
	$ort = $row["Ort"];
	$land = $row["Land"];
	$mail = $row["Mail"];
	$id_organisation = $row["idtabelle_organisation"];
	$id_zustaendigkeit = $row["idTABELLE_Projektzuständigkeiten"];
        $raumNr = $row["Raumnr"];
	
					
	   
	echo "<form class='form-horizontal' role='form'>
		 <div class='form-group row'>
	 			<label class='control-label col-md-2' for='Name'>Name</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Name' value='".$name."'></input>
				</div>						  			 											 						 			
	 	</div>		  			 		
	 	<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Vorname'>Vorname</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Vorname' value='".$vorname."'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Tel'>Tel</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Tel' value='".$tel."'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Adresse'>Adresse</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Adresse' value='".$adresse."'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='PLZ'>PLZ</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='PLZ' value='".$plz."'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Ort'>Ort</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Ort' value='".$ort."'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Land'>Land</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Land' value='".$land."'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Email'>Email</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Email' value='".$mail."'></input>
				</div>	
		</div>";
		
		$sql="SELECT tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten, tabelle_projektzuständigkeiten.Zuständigkeit FROM tabelle_projektzuständigkeiten ORDER BY Zuständigkeit;";
		$result = $mysqli->query($sql);
		
		
		echo "<div class='form-group row'>
	 			<label class='control-label col-md-2' for='zustaendigkeit'>Zuständigkeit</label>
				<div class='col-md-8'>
					<select class='form-control form-control-sm' id='zustaendigkeit' name='selectCategory'>";
						while($row = $result->fetch_assoc()) {
							if($id_zustaendigkeit == $row["idTABELLE_Projektzuständigkeiten"]){
								echo "<option value=".$row["idTABELLE_Projektzuständigkeiten"]." selected>".$row["Zuständigkeit"]."</option>";		
							}
							else{
								echo "<option value=".$row["idTABELLE_Projektzuständigkeiten"].">".$row["Zuständigkeit"]."</option>";	
							}
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
							if($id_organisation == $row["idtabelle_organisation"]){
								echo "<option value=".$row["idtabelle_organisation"]." selected>".$row["Organisation"]."</option>";		
							}
							else{
								echo "<option value=".$row["idtabelle_organisation"].">".$row["Organisation"]."</option>";	
							}		
						}	
					echo "</select>	
				</div>
		</div>
                <div class='form-group row'>
	 			<label class='control-label col-md-2' for='Raumnr'>Raumnr</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Raumnr' value='".$raumNr."'></input>
				</div>	
		</div>
	 	<div class='form-group row'>
	 		<input type='button' id='".$_GET["personID"]."' class='btn btn-warning btn-sm' value='Personendaten ändern'></input>
			<input type='button' id='addPersonToProjectButton' class='btn btn-success btn-sm' value='Person zu Projekt hinzufügen'></input>
	 		<input type='button' id='".$_GET["personID"]."' class='btn btn-danger btn-sm' value='Person von Projekt entfernen'></input>
	 	</div>			  
	</form>";
	
	$mysqli ->close();

	
	?>
	
<script>
	
	
	// Person zu Projekt hinzufügen
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
    
    
    // Personendaten ändern
	$("input[value='Personendaten ändern']").click(function(){
	     
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
		 var personID = this.id;
	 	
	 	 if(Name.length > 0 && Vorname.length > 0 && Tel.length > 0){
	 	 	
			 $.ajax({
		        url : "savePersonProjectData.php",
		        data:{"Name":Name,"Vorname":Vorname,"Tel":Tel,"Adresse":Adresse,"PLZ":PLZ,"Ort":Ort,"Land":Land,"Email":Email,"personID":personID,"zustaendigkeit":zustaendigkeit,"organisation":organisation,"Raumnr":Raumnr},
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
	
	// Person von Projekt entfernen
	$("input[value='Person von Projekt entfernen']").click(function(){
	     
		 var personID = this.id;
		 
		 $.ajax({
	        url : "deletePersonFromProject.php",
	        data:{"personID":personID},
	        type: "GET",
	        success: function(data){
	        	alert(data);
	        	$.ajax({
			        url : "getPersonsOfProject.php",
			        type: "GET",
			        success: function(data){			        
			            $("#personsInProject").html(data);
			            $.ajax({
					        url : "getPersonsNotInProject.php",
					        type: "GET",
					        success: function(data){			        
					            $("#personsNotInProject").html(data);
							} 
			   			});

					} 
	   			});
	        } 
	    }); 
    });



</script>

</body>
</html>