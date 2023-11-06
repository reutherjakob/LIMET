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
	
	//Elemente im Raum abfragen
	$sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.`Anmerkung allgemein`
			FROM tabelle_räume
			WHERE (((tabelle_räume.idTABELLE_Räume)=".$_SESSION["roomID"]."));";
    
	$result = $mysqli->query($sql);	
	$row = $result->fetch_assoc();
	
	$raumnummer = $row["Raumnr"];
	$raumbezeichnung = $row["Raumbezeichnung"];
	$raumbereich = $row["Raumbereich Nutzer"];
	$flaeche = $row["Nutzfläche"];
	$geschoss = $row["Geschoss"];
	$bauetappe = $row["Bauetappe"];
	$bauteil = $row["Bauabschnitt"];
	$anmerkung = $row["Anmerkung allgemein"];

	$mysqli ->close();	 
	   
	echo "<form class='form-horizontal' role='form'>
			  			 <div class='form-group'>
		  			 			<label class='control-label col-md-4' for='raumnummer'>Raumnummer</label>
		  			 			<div class='col-md-8'>
		  			 				<input type='text' class='form-control input-sm' id='raumnummer' value='$raumnummer'></input>
								</div>						  			 											 						 			
		  			 	</div>		  			 		
		  			 	<div class='form-group row'>
		  			 			<label class='control-label col-md-4' for='raumanmerkung'>Raumanmerkung</label>
		  			 			<div class='col-md-8'>
		  			 				<textarea class='form-control input-sm' rows='3' id='raumanmerkung'>".$anmerkung."</textarea>
		  			 			</div>
		  			 	</div>
		  			 	<div class='form-group row'>
		  			 			<label class='control-label col-md-4' for='raumbezeichnung'>Raumbezeichnung</label>
		  			 			<div class='col-md-8'>
		  			 				<input type='text' class='form-control input-sm' id='raumbezeichnung' value='$raumbezeichnung'></input>
								</div>	
						</div>
		  			 	<div class='form-group row'>		
								<label class='control-label col-md-4' for='raumbereich'>Raumbereich Nutzer</label>
		  			 			<div class='col-md-8'>
		  			 				<select class='form-control input-sm' id='raumbereich' name='raumbereich'>";
		  			 					
											$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	
											
											/* change character set to utf8 */
											if (!$mysqli->set_charset("utf8")) {
											    printf("Error loading character set utf8: %s\n", $mysqli->error);
											    exit();
											}
											
											// Abfrage aller Raumbereiche im Projekt
											$sql = "SELECT tabelle_räume.`Raumbereich Nutzer` FROM tabelle_räume WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " .$_SESSION["projectID"]. ")) GROUP BY tabelle_räume.`Raumbereich Nutzer` HAVING (((tabelle_räume.`Raumbereich Nutzer`) Is Not Null)) ORDER BY tabelle_räume.`Raumbereich Nutzer`";
											
											$result = $mysqli->query($sql);
												
											while($row = $result->fetch_assoc()) {
												if($row["Raumbereich Nutzer"] == $raumbereich){
													echo "<option selected>".$row["Raumbereich Nutzer"]."</option>";	
												}
												else{
													echo "<option>".$row["Raumbereich Nutzer"]."</option>";	

												}												    
											}
											$mysqli ->close();
									echo "</select>
								</div>	
						</div>
		  			 	<div class='form-group row'>
		
								<label class='control-label col-md-4' for='raumbereich_neu'>Neuer Raumbereich</label>
		  			 			<div class='col-md-8'>
		  			 				<input type='text' class='form-control input-sm' id='raumbereich_neu'></input>
								</div>									 
		  			 	</div>	
		  			 	<div class='form-group row'>
		  			 			<label class='control-label col-md-4' for='geschoss'>Geschoß</label>
		  			 			<div class='col-md-8'>
		  			 				<input type='text' class='form-control input-sm' id='geschoss' value='$geschoss'></input>
								</div>
						</div>	
		  			 	<div class='form-group row'>

								<label class='control-label col-md-4' for='nutzflaeche'>Nutzfläche</label>
		  			 			<div class='col-md-8'>
		  			 				<input type='text' class='form-control input-sm' id='nutzflaeche' value='$flaeche'></input>
								</div>	
														  			 											 
		  			 	</div>			
		  			 	<div class='form-group row'>
		  			 			<label class='control-label col-md-4' for='bauteil'>Bauteil</label>
		  			 			<div class='col-md-8'>
		  			 				<input type='text' class='form-control input-sm' id='bauteil' value='$bauteil'></input>
								</div>	
						</div>	
		  			 	<div class='form-group row'>
								<label class='control-label col-md-4' for='bauetappe'>Bauetappe</label>
		  			 			<div class='col-md-8'>
		  			 				<input type='text' class='form-control input-sm' id='bauetappe' value='$bauetappe'></input>
								</div>						  			 											 
		  			 	</div>	
		  			 	<div class='form-group row'>
		  			 			<input type='button' id='newRoom' class='btn btn-success btn-sm' value='Neuen Raum anlegen'></input>
								<input type='button' id='alterRoom' class='btn btn-warning btn-sm' value='Raumänderungen speichern'></input>
		  			 	</div>			  
					</form>";
	
	?>
<script>
	
	// Raumänderung speichern
	$("input[value='Raumänderungen speichern']").click(function(){
		 var raumnummer= $("#raumnummer").val();
		 var raumbezeichnung= $("#raumbezeichnung").val();
		 var raumanmerkung= $("#raumanmerkung").val();
		 var geschoss= $("#geschoss").val();
		 var nutzflaeche= $("#nutzflaeche").val();
		 var bauteil= $("#bauteil").val();
		 var bauetappe= $("#bauetappe").val();
		 var raumbereich_neu =  $("#raumbereich_neu").val();
		 var raumbereich =  $("#raumbereich").val();
		 
		 if(raumbereich_neu !== ""){
		 	raumbereich = raumbereich_neu;
		 }
		 
	     
		 $.ajax({
	        url : "saveRoomData.php",
	        data:{"raumnummer":raumnummer,"raumbezeichnung":raumbezeichnung,"raumanmerkung":raumanmerkung,"geschoss":geschoss,"nutzflaeche":nutzflaeche,"bauteil":bauteil,"bauetappe":bauetappe,"raumbereich":raumbereich},
	        type: "GET",
	        success: function(data){
	        	alert(data);
	        } 
        });
        
    });


</script>

</body>
</html>