<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<title>LIMET - Raumbuch - Elemente im Raum</title>
<link rel="icon" href="iphone_favicon.png"></link>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs-3.3.7/jq-3.2.1/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-flash-1.5.1/b-html5-1.5.1/r-2.2.1/datatables.css"/> 
<script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jq-3.2.1/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-flash-1.5.1/b-html5-1.5.1/r-2.2.1/datatables.js"></script>
<style>
.navbar-brand {
  padding: 0px;
}
.navbar-brand>img {
  height: 100%;
  width: auto;
}


</style>


</head>

<body style="height:100%">
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }

?>
<div class="container-fluid">
	<nav class="navbar navbar-default">
		  
		    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                      </button>  
		      <a class="navbar-brand"><img src="LIMET_logo.png" alt="LIMETLOGO"/></a>
		    </div>
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav">
		        <li class="dropdown">
		          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class='glyphicon glyphicon-th-large'></span> Projekte
		          <span class="caret"></span></a>
		          <ul class="dropdown-menu">		          	
                                <li><a href="projects.php"><span class='glyphicon glyphicon-th-large'></span> Projektauswahl</a></li>
                                <?php 
                                    if($_SESSION["ext"]==0){
                                        echo "<li><a href='projectParticipants.php'><span class='glyphicon glyphicon-user'></span> Projektbeteiligte</a></li>
                                                <li><a href='documentationV2.php'><span class='glyphicon glyphicon-comment'></span> Dokumentation</a></li>";
                                    }
                                ?>
                          </ul>
                        </li>
		        <li class="dropdown">
		          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class='glyphicon glyphicon-th-list'></span> Raumbuch
		          <span class="caret"></span></a>
		          <ul class="dropdown-menu">
		          	<li><a href="roombookSpecifications.php">Raumbuch - Bauangaben</a></li>
		          	<li><a href="roombookSimple.php">Raumbuch - Einfach</a></li>
		          	<li><a href="roombookDetailed.php">Raumbuch - Detail</a></li>
                                <li><a class='dropdown-item' href='roombookElements.php'>Raumbuch - Räume mit Element</a></li>
		          	<li><a href="roombookBO.php">Raumbuch - Betriebsorganisation</a></li>
                                <li><a href="roombookReports.php">Raumbuch - Berichte</a></li>
		            <li><a href="elementsInProject.php">Elemente im Projekt</a></li>	
                            <li><a class='dropdown-item' href='roombookList.php'>Raumbuch - Liste</a></li>
		          </ul>
		        </li>
		        <li class="dropdown">
		          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class='glyphicon glyphicon-euro'></span> Kosten
		          <span class="caret"></span></a>
		          <ul class="dropdown-menu">
                                <li><a href='costsOverall.php'>Kosten - Berichte</a></li> 
		          	<li><a href="costsRoomArea.php">Kosten - Raumbereich</a></li>
		          	<li><a href="costChanges.php">Kosten - Änderungen</a></li>
		          </ul>
		        </li>
                        <li class="dropdown">
		          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class='glyphicon glyphicon-retweet'></span> Bestand
		          <span class="caret"></span></a>
		          <ul class="dropdown-menu">
                              <li><a href="roombookBestand.php">Bestand - Raumbereich</a></li>	
                              <li><a href="roombookBestandElements.php">Bestand - Gesamt</a></li>
		          </ul>
		        </li>
				<li class="dropdown">
		          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class='glyphicon glyphicon-tasks'></span> Ausschreibungen
		          <span class="caret"></span></a>
		          <ul class="dropdown-menu">
		          	<li><a href="tenderLots.php">Los-Verwaltung</a></li>
                                <li><a href="tenderCharts.php">Vergabe-Diagramme</a></li>
		          	<li><a href="elementLots.php">Element-Verwaltung</a></li>
		          </ul>
		        </li>
                        
		        <li class="dropdown">
		          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class='glyphicon glyphicon-list-alt'></span> Datenbank-Verwaltung
		          <span class="caret"></span></a>
		          <ul class="dropdown-menu">
		          	<li><a href="elementAdministration.php">Elemente-Verwaltung</a></li>
		            <li class="active"><a href="elementeCAD.php">Elemente-CAD</a></li>
		          </ul>
		        </li>
                        <li ><a href="firmenkontakte.php"><span class='glyphicon glyphicon-user'></span> Firmenkontakte</a></li>
		      </ul>
                        <ul class="nav navbar-nav navbar-right">
                           <li><a>Aktuelles Projekt: <?php  if ($_SESSION["projectName"] != ""){echo $_SESSION["projectName"];}else{echo "Kein Projekt ausgewählt!";}?></a></li>
                          <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                      </ul>
		    </div>
		  
	</nav>
	<div class="panel panel-default">
  		<div class="panel-heading"><label>Elemente</label></div>
  		<div class="panel-body" id="cadElements">
		  <div class="col-md-12 col-sm-12" >
		  	<?php
						$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	
						
						/* change character set to utf8 */
						if (!$mysqli->set_charset("utf8")) {
						    printf("Error loading character set utf8: %s\n", $mysqli->error);
						    exit();
						}
						
						// Abfrage aller Räume im Projekt
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
						
						$result = $mysqli->query($sql);
		
						//echo "<table class='table table-striped' id='tableElements'>
						echo "<table id='tableElements' class='table table-striped table-bordered table-condensed' cellspacing='0' width='100%'>
						<thead><tr>
						<th>ID</th>
						<th>Element</th>
						<th>Beschreibung</th>
						<th>CAD Notwendigkeit
						<select class='form-control input-sm' id='filter_dwg_notwendig'>
							<option selected value='2'></option>
							<option value='0'>Nein</option>
							<option value='1'>Ja</option>		
						</select>
						</th>
						<th>DWG vorhanden
						<select class='form-control input-sm' id='filterCAD_dwg_vorhanden'>
							<option selected value='2'></option>
							<option value='0'>Nein</option>
							<option value='1'>Ja</option>		
						</select></th>
						<th>DWG geprüft</th>
						<th>Familie vorhanden
						<select class='form-control input-sm' id='filterCAD_familie_vorhanden'>
							<option selected value='2'></option>
							<option value='0'>Nein</option>
							<option value='1'>Ja</option>		
						</select></th>
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
		  </div>
		</div>	
	</div>
</div>

<script>		
	// Tabelle formatieren
	$(document).ready(function(){		
		$('#tableElements').DataTable( {
			"paging": true,
			"ordering": [[ 0, "asc" ]],
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 15,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}     
	    } );

	});

	
	// Element speichern
	$("input[value='Speichern']").click(function(){
	    var id=this.id; 
	    var selectCAD_notwendig = $("#selectCAD_notwendig"+id).val();
	    var selectCAD_dwg_vorhanden = $("#selectCAD_dwg_vorhanden"+id).val();
	    var selectCAD_dwg_kontrolliert = $("#selectCAD_dwg_kontrolliert"+id).val();
	    var selectCAD_familie_vorhanden = $("#selectCAD_familie_vorhanden"+id).val();
	    var selectCAD_familie_kontrolliert = $("#selectCAD_familie_kontrolliert"+id).val();
	    var CADcomment = $("#CADcomment"+id).val();
		
        $.ajax({
	        url : "saveCADElement.php",
	        data:{"id":id,"selectCAD_notwendig":selectCAD_notwendig,"selectCAD_dwg_vorhanden":selectCAD_dwg_vorhanden,"selectCAD_dwg_kontrolliert":selectCAD_dwg_kontrolliert,"selectCAD_familie_vorhanden":selectCAD_familie_vorhanden,"selectCAD_familie_kontrolliert":selectCAD_familie_kontrolliert,"CADcomment":CADcomment},
	        type: "GET",
	        success: function(data){
	            alert(data);
	        } 
        });
          
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
