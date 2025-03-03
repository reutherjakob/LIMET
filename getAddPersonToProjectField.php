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
	
	// Personendaten im Projekt laden	
	
	$sql = "SELECT `tabelle_ansprechpersonen`.`Name`,
		    `tabelle_ansprechpersonen`.`Vorname`,
		    `tabelle_ansprechpersonen`.`Tel`,
		    `tabelle_ansprechpersonen`.`Adresse`,
		    `tabelle_ansprechpersonen`.`PLZ`,
		    `tabelle_ansprechpersonen`.`Ort`,
		    `tabelle_ansprechpersonen`.`Land`,
		    `tabelle_ansprechpersonen`.`Mail`
			FROM `LIMET_RB`.`tabelle_ansprechpersonen` WHERE `tabelle_ansprechpersonen`.`idTABELLE_Ansprechpersonen`=".$_GET["personID"].";";
	
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
					
	   
	echo "<form class='form-horizontal' role='form'>
		 <div class='form-group row'>
	 			<label class='control-label col-md-2' for='Name'>Name</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Name' value='".$name."' disabled='disabled'></input>
				</div>						  			 											 						 			
	 	</div>		  			 		
	 	<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Vorname'>Vorname</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Vorname' value='".$vorname."' disabled='disabled'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Tel'>Tel</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Tel' value='".$tel."' disabled='disabled'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Adresse'>Adresse</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Adresse' value='".$adresse."' disabled='disabled'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='PLZ'>PLZ</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='PLZ' value='".$plz."' disabled='disabled'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Ort'>Ort</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Ort' value='".$ort."' disabled='disabled'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Land'>Land</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Land' value='".$land."' disabled='disabled'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Email'>Email</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Email' value='".$mail."' disabled='disabled'></input>
				</div>	
		</div>";
		
		$sql="SELECT tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten, tabelle_projektzuständigkeiten.Zuständigkeit FROM tabelle_projektzuständigkeiten ORDER BY Zuständigkeit;";
		$result = $mysqli->query($sql);
		
		
		echo "<div class='form-group row'>
	 			<label class='control-label col-md-2' for='zustaendigkeit'>Zuständigkeit</label>
				<div class='col-md-8'>
					<select class='form-control form-control-sm' id='zustaendigkeit' name='selectCategory'>
						<option value=0 selected>Bitte auswählen</option>";
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
					<select class='form-control form-control-sm' id='organisation' name='organisation'>
						<option value=0 selected>Bitte auswählen</option>";
						while($row = $result->fetch_assoc()) {
							echo "<option value=".$row["idtabelle_organisation"].">".$row["Organisation"]."</option>";		
						}	
					echo "</select>	
				</div>
		</div>
	 	<div class='form-group row'>
			<input type='button' id='".$_GET["personID"]."' class='btn btn-success btn-sm' value='Person zu Projekt hinzufügen'></input>
	 	</div>			  
	</form>";
	
	$mysqli ->close();

	
	?>
	
<script>
	
	
	// Person zu Projekt hinzufügen
	$("input[value='Person zu Projekt hinzufügen']").click(function(){
		 var id = this.id;
		 var zustaendigkeit=  $("#zustaendigkeit").val();
		 var organisation=  $("#organisation").val();
	 	 
	 	 if(zustaendigkeit === "0"){
	 	 	alert("Keine Zuständigkeit ausgewählt!");
	 	 }
	 	 else{
	 	 	if(organisation === "0"){
	 	 		alert("Keine Organisation ausgewählt!");
	 	 	}
	 	 	else{
				 $.ajax({
			        url : "addExistingPersonToProject.php",
			        data:{"personID":id,"zustaendigkeit":zustaendigkeit,"organisation":organisation},
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
			 }
		}
	 
    });


</script>

</body>
</html>