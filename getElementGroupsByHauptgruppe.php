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
	
	$sql = "SELECT tabelle_element_gewerke.idtabelle_element_gewerke, tabelle_element_gewerke.Nummer, tabelle_element_gewerke.Gewerk
												FROM tabelle_element_gewerke
												ORDER BY tabelle_element_gewerke.Nummer;";
												
												
	$result = $mysqli->query($sql);
	echo "<div class='form-group row'>
 			<label class='control-label col-xxl-2' for='elementGewerk'>Gewerk</label>
			<div class='col-xxl-10'>
				<select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
					while($row = $result->fetch_assoc()) {
						if($row["idtabelle_element_gewerke"] == $_GET["gewerkID"]){
							echo "<option value=".$row["idtabelle_element_gewerke"]." selected>".$row["Nummer"]." - ".$row["Gewerk"]."</option>";		
						}
						else{
							echo "<option value=".$row["idtabelle_element_gewerke"].">".$row["Nummer"]." - ".$row["Gewerk"]."</option>";
						}
					}	
				echo "</select>	
			</div>
	</div>";
	
	$sql = "SELECT `tabelle_element_hauptgruppe`.`idTABELLE_Element_Hauptgruppe`,
			    `tabelle_element_hauptgruppe`.`Hauptgruppe`,
			    `tabelle_element_hauptgruppe`.`Nummer`
			FROM `LIMET_RB`.`tabelle_element_hauptgruppe`
			WHERE `tabelle_element_hauptgruppe`.`tabelle_element_gewerke_idtabelle_element_gewerke` = ".$_GET["gewerkID"]."
			ORDER BY `tabelle_element_hauptgruppe`.`Nummer`;";
												
												
	$result = $mysqli->query($sql);

	
	echo "<div class='form-group row'>
 			<label class='control-label col-xxl-2' for='elementHauptgruppe'>Hauptgruppe</label>
			<div class='col-xxl-10'>
				<select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>";
					while($row = $result->fetch_assoc()) {
						if($row["idTABELLE_Element_Hauptgruppe"] == $_GET["hauptgruppeID"]){
							echo "<option value=".$row["idTABELLE_Element_Hauptgruppe"]." selected>".$row["Nummer"]." - ".$row["Hauptgruppe"]."</option>";		
						}
						else{
							echo "<option value=".$row["idTABELLE_Element_Hauptgruppe"].">".$row["Nummer"]." - ".$row["Hauptgruppe"]."</option>";
						}
					}	
				echo "</select>	
			</div>
	</div>";
	
	$sql = "SELECT `tabelle_element_gruppe`.`idTABELLE_Element_Gruppe`,
		    `tabelle_element_gruppe`.`Gruppe`,
		    `tabelle_element_gruppe`.`Nummer`
		FROM `LIMET_RB`.`tabelle_element_gruppe`
		WHERE `tabelle_element_gruppe`.`tabelle_element_hauptgruppe_idTABELLE_Element_Hauptgruppe` = ".$_GET["hauptgruppeID"]."
		ORDER BY `tabelle_element_gruppe`.`Nummer`;";
												
												
	$result = $mysqli->query($sql);

	echo "<div class='form-group row'>
 			<label class='control-label col-xxl-2' for='elementGruppe'>Gruppe</label>
			<div class='col-xxl-10'>
				<select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>";
					echo "<option value=0 selected>Gruppe auswählen</option>";
					while($row = $result->fetch_assoc()) {
						//if($row["idtabelle_element_gewerke"] == $_GET["hauptgruppeID"]){
						//	echo "<option value=".$row["idTABELLE_Element_Hauptgruppe"]." selected>".$row["Nummer"]." - ".$row["Hauptgruppe"]."</option>";		
						//}
						//else{
							
							echo "<option value=".$row["idTABELLE_Element_Gruppe"].">".$row["Nummer"]." - ".$row["Gruppe"]."</option>";
						//}
					}
				echo "</select>	
			</div>
	</div>";
	
	$mysqli ->close();
	?>
<script>
	
 	// Element Gewerk Änderung
	$('#elementGewerk').change(function(){
		var gewerkID = this.value;
		
	    $.ajax({
	        url : "getElementGroupsByGewerk.php",
	        data:{"gewerkID":gewerkID},
	        type: "GET",
	        success: function(data){
	        	$("#elementGroups").html(data);
	        }
	    });
		
	});
	
	// Element Gewerk Änderung
	$('#elementHauptgruppe').change(function(){
		var hauptgruppeID = this.value;
		var gewerkID = $("#elementGewerk").val();
		
	    $.ajax({
	        url : "getElementGroupsByHauptgruppe.php",
	        data:{"gewerkID":gewerkID,"hauptgruppeID":hauptgruppeID},
	        type: "GET",
	        success: function(data){
	        	$("#elementGroups").html(data);
	        }
	    });
		
	});
	
	// Element Gewerk Änderung
	$('#elementGruppe').change(function(){
		var gruppeID = this.value;
		
		if(gruppeID !== 0){
		
		    $.ajax({
		        url : "getElementsByGroup.php",
		        data:{"gruppeID":gruppeID},
		        type: "GET",
		        success: function(data){
		        	$("#elementsInDB").html(data);
		        }
		    });
		 }
		
	});





</script>

</body>
</html>