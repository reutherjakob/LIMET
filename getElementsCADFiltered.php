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
	
	// Wenn kein Filter ausgewählt
	if($_GET['filterValueDWGNotwendig'] == 2 && $_GET['filterValueDWGVorhanden'] == 2 && $_GET['filterValueFamilieVorhanden'] == 2){
		// Abfrage aller Elemente mit Suchkriterien
		$sql = "SELECT `tabelle_elemente`.`idTABELLE_Elemente`,
			    `tabelle_elemente`.`Bezeichnung`,
			    `tabelle_elemente`.`ElementID`,
			    `tabelle_elemente`.`Kurzbeschreibung`,
			    `tabelle_elemente`.`CAD_notwendig`,
			    `tabelle_elemente`.`CAD_dwg_vorhanden`,
			    `tabelle_elemente`.`CAD_dwg_kontrolliert`,
			    `tabelle_elemente`.`CAD_familie_vorhanden`,
			    `tabelle_elemente`.`CAD_familie_kontrolliert`,
			    `tabelle_elemente`.`CAD_Kommentar`
			FROM `LIMET_RB`.`tabelle_elemente` 
			ORDER BY `tabelle_elemente`.`ElementID`;";
	}
	else{ // Sonst zumindest ein Filter ausgewählt
		
		$sql = "SELECT `tabelle_elemente`.`idTABELLE_Elemente`,
			    `tabelle_elemente`.`Bezeichnung`,
			    `tabelle_elemente`.`ElementID`,
			    `tabelle_elemente`.`Kurzbeschreibung`,
			    `tabelle_elemente`.`CAD_notwendig`,
			    `tabelle_elemente`.`CAD_dwg_vorhanden`,
			    `tabelle_elemente`.`CAD_dwg_kontrolliert`,
			    `tabelle_elemente`.`CAD_familie_vorhanden`,
			    `tabelle_elemente`.`CAD_familie_kontrolliert`,
			    `tabelle_elemente`.`CAD_Kommentar`
			FROM `LIMET_RB`.`tabelle_elemente` 
			WHERE ";
		
		if(	$_GET['filterValueDWGNotwendig'] != 2 && $_GET['filterValueDWGVorhanden'] != 2 && $_GET['filterValueFamilieVorhanden'] != 2){
			$sql .= "`tabelle_elemente`.`CAD_notwendig` = ".$_GET['filterValueDWGNotwendig']." AND 
					`tabelle_elemente`.`CAD_dwg_vorhanden` = ".$_GET['filterValueDWGVorhanden']." AND
					`tabelle_elemente`.`CAD_familie_vorhanden` = ".$_GET['filterValueFamilieVorhanden'];
		}
		else{
			
			if(	$_GET['filterValueDWGNotwendig'] != 2 && $_GET['filterValueDWGVorhanden'] != 2 && $_GET['filterValueFamilieVorhanden'] == 2){
				$sql .= "`tabelle_elemente`.`CAD_notwendig` = ".$_GET['filterValueDWGNotwendig']." AND 
						`tabelle_elemente`.`CAD_dwg_vorhanden` = ".$_GET['filterValueDWGVorhanden'];
			}
			else{
				if(	$_GET['filterValueDWGNotwendig'] != 2 && $_GET['filterValueDWGVorhanden'] == 2 && $_GET['filterValueFamilieVorhanden'] != 2){
					$sql .= "`tabelle_elemente`.`CAD_notwendig` = ".$_GET['filterValueDWGNotwendig']." AND 
							`tabelle_elemente`.`CAD_familie_vorhanden` = ".$_GET['filterValueFamilieVorhanden'];
				}
				else{
				
					if(	$_GET['filterValueDWGNotwendig'] != 2 && $_GET['filterValueDWGVorhanden'] == 2 && $_GET['filterValueFamilieVorhanden'] == 2){
						$sql .= "`tabelle_elemente`.`CAD_notwendig` = ".$_GET['filterValueDWGNotwendig'];
					}
					else{
						
						if(	$_GET['filterValueDWGNotwendig'] == 2 && $_GET['filterValueDWGVorhanden'] != 2 && $_GET['filterValueFamilieVorhanden'] != 2){
							$sql .= "`tabelle_elemente`.`CAD_dwg_vorhanden` = ".$_GET['filterValueDWGVorhanden']." AND
									`tabelle_elemente`.`CAD_familie_vorhanden` = ".$_GET['filterValueFamilieVorhanden'];
						}
						else{
							if(	$_GET['filterValueDWGNotwendig'] == 2 && $_GET['filterValueDWGVorhanden'] == 2 && $_GET['filterValueFamilieVorhanden'] != 2){
								$sql .= "`tabelle_elemente`.`CAD_familie_vorhanden` = ".$_GET['filterValueFamilieVorhanden'];
							}
							else{
								if(	$_GET['filterValueDWGNotwendig'] == 2 && $_GET['filterValueDWGVorhanden'] != 2 && $_GET['filterValueFamilieVorhanden'] == 2){
									$sql .= "`tabelle_elemente`.`CAD_dwg_vorhanden` = ".$_GET['filterValueDWGVorhanden'];
								}
							}
						}
					}
				}
			}
		}

		$sql .= " ORDER BY `tabelle_elemente`.`ElementID`;";
	}
	$result = $mysqli->query($sql);

	//echo "<table class='table table-striped' id='tableElements'>
	echo "<table id='tableElements' class='table table-striped table-bordered table-condensed' cellspacing='0' width='100%'>
	<thead><tr>
	<th>ID</th>
	<th>Element</th>
	<th>Beschreibung</th>
	<th>CAD Notwendigkeit
	<select class='form-control input-sm' id='filter_dwg_notwendig'>";
		if($_GET['filterValueDWGNotwendig']==0){
		    	echo "
				<option value='2'></option>
				<option value='0' selected>Nein</option>
				<option value='1'>Ja</option>	
				";
		}
	    else{
	    	if($_GET['filterValueDWGNotwendig']==1){
		    	echo "
				<option value='2'></option>
				<option value='0'>Nein</option>
				<option value='1' selected>Ja</option>	
				";
			}
			else{
		    	echo "
				<option selected value='2'></option>
				<option value='0'>Nein</option>
				<option value='1'>Ja</option>		
				";
			}
	    }	
	echo "</select>
	</th>
	<th>DWG vorhanden
	<select class='form-control input-sm' id='filterCAD_dwg_vorhanden'>";
		if($_GET['filterValueDWGVorhanden']==0){
		    	echo "
				<option value='2'></option>
				<option value='0' selected>Nein</option>
				<option value='1'>Ja</option>	
				";
		}
	    else{
	    	if($_GET['filterValueDWGVorhanden']==1){
		    	echo "
				<option value='2'></option>
				<option value='0'>Nein</option>
				<option value='1' selected>Ja</option>	
				";
			}
			else{
		    	echo "
				<option selected value='2'></option>
				<option value='0'>Nein</option>
				<option value='1'>Ja</option>		
				";
			}
	    }	
	echo "</select>
	</th>
	<th>DWG geprüft</th>
	<th>Familie vorhanden
	<select class='form-control input-sm' id='filterCAD_familie_vorhanden'>";
		if($_GET['filterValueFamilieVorhanden']==0){
		    	echo "
				<option value='2'></option>
				<option value='0' selected>Nein</option>
				<option value='1'>Ja</option>	
				";
		}
	    else{
	    	if($_GET['filterValueFamilieVorhanden']==1){
		    	echo "
				<option value='2'></option>
				<option value='0'>Nein</option>
				<option value='1' selected>Ja</option>	
				";
			}
			else{
		    	echo "
				<option selected value='2'></option>
				<option value='0'>Nein</option>
				<option value='1'>Ja</option>		
				";
			}
	    }	
	echo "</select>
	</th>
	<th>Familie geprüft</th>
	<th>CAD Kommentar</th>
	<th>Speichern</th>
	</tr></thead>
	<tfoot><tr>
	<th>ID</th>
	<th>Element</th>
	<th>Beschreibung</th>
	<th>CAD Notwendigkeit</th>
	<th>DWG vorhanden</th>
	<th>DWG geprüft</th>
	<th>Familie vorhanden</th>
	<th>Familie geprüft</th>
	<th>CAD Kommentar</th>
	<th>Speichern</th>
	</tr></tfoot><tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["ElementID"]."</td>";
	    echo "<td>".$row["Bezeichnung"]."</td>";
	    echo "<td>".$row["Kurzbeschreibung"]."</td>";
	   	echo "<td><select class='form-control input-sm' id='selectCAD_notwendig".$row["idTABELLE_Elemente"]."'>";
		    if($row["CAD_notwendig"]==0){
		    	echo "
				<option selected>Nein</option>
				<option>Ja</option>		
				";
		    }
		    else{
		    	echo "
				<option>Nein</option>
				<option selected>Ja</option>		
				";
		    }
	    echo "</select></td>";
	    	echo "<td><select class='form-control input-sm' id='selectCAD_dwg_vorhanden".$row["idTABELLE_Elemente"]."'>";
		    if($row["CAD_dwg_vorhanden"]==0){
		    	echo "
				<option selected>Nein</option>
				<option>Ja</option>		
				";
		    }
		    else{
		    	echo "
				<option>Nein</option>
				<option selected>Ja</option>		
				";
		    }
	    echo "</select></td>";
			echo "<td><select class='form-control input-sm' id='selectCAD_dwg_kontrolliert".$row["idTABELLE_Elemente"]."'>";
		    if($row["CAD_dwg_kontrolliert"]==0){
		    	echo "
				<option selected>Nicht geprüft</option>
				<option>Freigegeben</option>
				<option>Überarbeiten</option>	
				";
		    }
		    if($row["CAD_dwg_kontrolliert"]==1){
		    	echo "
				<option>Nicht geprüft</option>
				<option selected>Freigegeben</option>
				<option>Überarbeiten</option>		
				";
		    }
		    if($row["CAD_dwg_kontrolliert"]==2){
		    	echo "
				<option>Nicht geprüft</option>
				<option>Freigegeben</option>
				<option selected>Überarbeiten</option>		
				";
		    }

	    echo "</select></td>";

			echo "<td><select class='form-control input-sm' id='selectCAD_familie_vorhanden".$row["idTABELLE_Elemente"]."'>";
		    if($row["CAD_familie_vorhanden"]==0){
		    	echo "
				<option selected>Nein</option>
				<option>Ja</option>		
				";
		    }
		    else{
		    	echo "
				<option>Nein</option>
				<option selected>Ja</option>		
				";
		    }
	    echo "</select></td>";

			echo "<td><select class='form-control input-sm' id='selectCAD_familie_kontrolliert".$row["idTABELLE_Elemente"]."'>";
		    if($row["CAD_familie_kontrolliert"]==0){
		    	echo "
				<option selected>Nicht geprüft</option>
				<option>Freigegeben</option>
				<option>Überarbeiten</option>	
				";
		    }
		    if($row["CAD_familie_kontrolliert"]==1){
		    	echo "
				<option>Nicht geprüft</option>
				<option selected>Freigegeben</option>
				<option>Überarbeiten</option>		
				";
		    }
		    if($row["CAD_familie_kontrolliert"]==2){
		    	echo "
				<option>Nicht geprüft</option>
				<option>Freigegeben</option>
				<option selected>Überarbeiten</option>		
				";
		    }
	    echo "</select></td>";
	    echo "<td><textarea id='CADcomment".$row["idTABELLE_Elemente"]."' class='form-control' style='width: 100%; height: 100%;'>".$row["CAD_Kommentar"]."</textarea></td>";					
	    echo "<td><input type='button' id='".$row["idTABELLE_Elemente"]."' class='btn btn-warning btn-sm' value='Speichern'></td>";
	    echo "</tr>";
	    
	}
	echo "</tbody></table>";
	
	$mysqli ->close();
	?>
<script>
	
	// Tabelle formatieren
	$(document).ready(function(){		
		$('#tableElements').DataTable( {
			"paging": true,
			"ordering": false,
	        "pagingType": "simple_numbers",
	        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}     
	    } );

	});
	
	 //Filter DWG vorhanden geändert
	$('#filterCAD_dwg_vorhanden').change(function(){
		var filterValueDWGVorhanden = this.value;
		var filterValueDWGNotwendig = $('#filter_dwg_notwendig').val();
		var filterValueFamilieVorhanden = $('#filterCAD_familie_vorhanden').val();
	    $.ajax({
	        url : "getElementsCADFiltered.php",
	        data:{"filterValueDWGNotwendig":filterValueDWGNotwendig,"filterValueDWGVorhanden":filterValueDWGVorhanden,"filterValueFamilieVorhanden":filterValueFamilieVorhanden},
	        type: "GET",
	        success: function(data){
		            $("#cadElements").html(data);		            
			}
	    });
	});
	
	 //Filter DWG notwendig geändert
	$('#filter_dwg_notwendig').change(function(){
		var filterValueDWGVorhanden = $('#filterCAD_dwg_vorhanden').val();
		var filterValueDWGNotwendig = this.value;
		var filterValueFamilieVorhanden = $('#filterCAD_familie_vorhanden').val();
	    $.ajax({
	        url : "getElementsCADFiltered.php",
	        data:{"filterValueDWGNotwendig":filterValueDWGNotwendig,"filterValueDWGVorhanden":filterValueDWGVorhanden,"filterValueFamilieVorhanden":filterValueFamilieVorhanden},
	        type: "GET",
	        success: function(data){
		            $("#cadElements").html(data);		            
			}
	    });
	});
	
	 //Filter Familie vorhanden geändert
	$('#filterCAD_familie_vorhanden').change(function(){
		var filterValueDWGVorhanden = $('#filterCAD_dwg_vorhanden').val();
		var filterValueDWGNotwendig = $('#filter_dwg_notwendig').val();
		var filterValueFamilieVorhanden = this.value;
	    $.ajax({
	        url : "getElementsCADFiltered.php",
	        data:{"filterValueDWGNotwendig":filterValueDWGNotwendig,"filterValueDWGVorhanden":filterValueDWGVorhanden,"filterValueFamilieVorhanden":filterValueFamilieVorhanden},
	        type: "GET",
	        success: function(data){
		            $("#cadElements").html(data);		            
			}
	    });
	});



</script>

</body>
</html>