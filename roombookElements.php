<?php
session_start();
$_SESSION["dbAdmin"]="2";
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Elemente</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>


 <style>

.btn-xs {
  height: 22px;
  padding: 2px 5px;
  font-size: 12px;
  line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
  border-radius: 3px;
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
<div class="container-fluid" >
	<nav class="navbar navbar-expand-md bg-light navbar-light">	
      <a class="py-0 navbar-brand" href="#"><img src="LIMET_logo.png" alt="LIMETLOGO" height="40"/></a>
          <ul class="navbar-nav">
              <?php 
              if($_SESSION["ext"]==0){
                  echo "<ul class='navbar-nav'>
                        <li class='nav-item'><a class='py-0 nav-link' href='dashboard.php'><i class='fa fa-tachometer-alt'></i> Dashboard</a></li>
                      </ul>";
              }
            ?>
            <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fa fa-list-alt'></i> Projekte</a>              
              <ul class="dropdown-menu">
                  <a class="dropdown-item" href="projects.php"><i class='fa fa-list-alt'></i> Projektauswahl</a> 
                  <?php 
                        if($_SESSION["ext"]==0){
                            echo "<a class='dropdown-item' href='projectParticipants.php'><i class='fa fa-users'></i> Projektbeteiligte</a>
                                  <a class='dropdown-item' href='documentationV2.php'><i class='fa fa-comments'></i> Dokumentation</a>";
                        }
                    ?>
              </ul>
            </li>
              <?php 
                    if($_SESSION["ext"]==0){
                        echo "<li class='nav-item dropdown'>
                                <a class=' py-0 nav-link dropdown-toggle' data-toggle='dropdown' href='#'><i class='fa fa-book'></i> Raumbuch</a>              
                                <ul class='dropdown-menu'>
                                    <a class='dropdown-item' href='roombookSpecifications.php'>Raumbuch - Bauangaben</a>
                                    <a class='dropdown-item' href='roombookSpecificationsLab.php'>Raumbuch - Bauangaben Labor</a>
                                    <a class='dropdown-item' href='roombookMeeting.php'>Raumbuch - Meeting</a>
                                    <a class='dropdown-item' href='roombookDetailed.php'>Raumbuch - Detail</a>
                                    <a class='dropdown-item active' href='roombookElements.php'>Raumbuch - Räume mit Element</a>
                                    <a class='dropdown-item' href='roombookBO.php'>Raumbuch - Betriebsorganisation</a>
                                    <a class='dropdown-item' href='roombookReports.php'>Raumbuch - Berichte</a>
                                    <a class='dropdown-item' href='elementsInProject.php'>Elemente im Projekt</a>
                                    <a class='dropdown-item' href='roombookList.php'>Raumbuch - Liste</a>
                                </ul>
                              </li>
                              <li class='nav-item dropdown'>
                                <a class='py-0 nav-link dropdown-toggle' data-toggle='dropdown' href='#'><i class='fa fa-euro-sign'></i> Kosten</a>              
                                <ul class='dropdown-menu'>
                                    <a class='dropdown-item' href='costsOverall.php'>Kosten - Berichte</a> 
                                    <a class='dropdown-item' href='costsRoomArea.php'>Kosten - Raumbereich</a>
                                    <a class='dropdown-item' href='costChanges.php'>Kosten - Änderungen</a>
                                    <a class='dropdown-item' href='elementBudgets.php'>Kosten - Budgets</a>
                                </ul>
                              </li>";
                    }
                ?>            	                 
            <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fa fa-recycle'></i> Bestand</a>             
              <ul class="dropdown-menu">
                  <a class="dropdown-item" href="roombookBestand.php">Bestand - Raumbereich</a>	
                  <a class="dropdown-item" href="roombookBestandElements.php">Bestand - Gesamt</a>
              </ul>
            </li>
            <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fa fa-tasks'></i> Ausschreibungen</a>
              <ul class="dropdown-menu">
                    <a class="dropdown-item" href="tenderLots.php">Los-Verwaltung</a>
                    <a class="dropdown-item" href="tenderCalendar.php">Vergabekalender</a>
                    <?php 
                        if($_SESSION["ext"]==0){
                            echo "<a class='dropdown-item' href='tenderCharts.php'>Vergabe-Diagramme</a>";
                        }
                    ?>
                    <a class="dropdown-item" href="elementLots.php">Element-Verwaltung</a>
              </ul>
            </li>
              <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fas fa-wrench'></i> Ausführung-ÖBA</a>
              <ul class="dropdown-menu">
                  <a class="dropdown-item" href="dashboardAusfuehrung.php"><i class='fas fa-tachometer-alt'></i> Dashboard</a>
                    <a class="dropdown-item" href="roombookAusfuehrung.php"><i class='fas fa-building'></i> Räume</a>
                    <a class="dropdown-item" href="roombookAusfuehrungLiefertermine.php"><i class='far fa-calendar-alt'></i> Liefertermine</a>
              </ul>
            </li>
          
          <?php 
                if($_SESSION["ext"]==0){
                    echo "<li class='py-0 nav-item dropdown'>
                                <a class='py-0 nav-link dropdown-toggle' data-toggle='dropdown' href='#'><i class='fa fa-buromobelexperte '></i> Datenbank-Verwaltung</a>              
                                <ul class='dropdown-menu'>
                                    <a class='dropdown-item' href='elementAdministration.php'>Elemente-Verwaltung</a>
                                    <a class='dropdown-item' href='elementeCAD.php'>Elemente-CAD</a>
                                </ul>
                           </li>    
                        <ul class='navbar-nav'>
                          <li class='nav-item'><a class='py-0 nav-link' href='firmenkontakte.php'><i class='fa fa-address-card'></i> Firmenkontakte</a></li>
                        </ul>";
                }
            ?>
              </ul>
          <ul class="navbar-nav ml-auto">
              <li class="py-0 nav-item "><a class="py-0 nav-link text-success disabled" id="projectSelected">Aktuelles Projekt: <?php  if ($_SESSION["projectName"] != ""){echo $_SESSION["projectName"];}else{echo "Kein Projekt ausgewÃ¤hlt!";}?></a></li>
              <li><a class="py-0 nav-link" href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a></li>
          </ul>              
    </nav> 
    <div class='row'>
        <div class='col-sm-12'> 
            <div class="mt-4 card">
                <div class="card-header">Elemente</div>
                <div class="card-body" id="DBElementData">                         
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mt-4 card">
                            <div class="card-header">Elementgruppen</div>
                            <div class="card-body" id="elementGroups">
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
                                                        <label class='control-label col-md-2' for='elementGewerk'>Gewerk</label>
                                                        <div class='col-md-10'>
                                                                <select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
                                                                        while($row = $result->fetch_assoc()) {
                                                                                echo "<option value=".$row["idtabelle_element_gewerke"].">".$row["Nummer"]." - ".$row["Gewerk"]."</option>";		
                                                                        }	
                                                                echo "</select>	
                                                        </div>
                                        </div>";


                                        echo "<div class='form-group row'>
                                                        <label class='control-label col-md-2' for='elementHauptgruppe'>Hauptgruppe</label>
                                                        <div class='col-md-10'>
                                                                <select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>
                                                                        <option selected>Gewerk auswählen</option>
                                                                </select>	
                                                        </div>
                                        </div>";


                                        echo "<div class='form-group row'>
                                                        <label class='control-label col-md-2' for='elementGruppe'>Gruppe</label>
                                                        <div class='col-md-10'>
                                                                <select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>
                                                                        <option selected>Gewerk auswählen</option>
                                                                </select>	
                                                        </div>
                                        </div>";

                                ?>
                                        </div>
                                    </div>
                                    <div class="mt-4 card">
                                        <div class="card-header">Elemente in DB</div>
                                        <div class="card-body" id="elementsInDB">
								<?php
									$sql = "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_elemente.Kurzbeschreibung
											FROM tabelle_elemente
											ORDER BY tabelle_elemente.ElementID;";
									
									
									$result = $mysqli->query($sql);
					
									echo "<table class='table table-striped table-bordered table-sm' id='tableElementsInDB'  cellspacing='0' width='100%'>
									<thead><tr>
									<th>ID</th>
									<th>ElementID</th>
									<th>Element</th>
									<th>Beschreibung</th>
									</tr></thead><tbody>";
									
									while($row = $result->fetch_assoc()) {
									    echo "<tr>";
									    echo "<td>".$row["idTABELLE_Elemente"]."</td>";
									    echo "<td>".$row["ElementID"]."</td>";
									    echo "<td>".$row["Bezeichnung"]."</td>";
									    echo "<td>".$row["Kurzbeschreibung"]."</td>";
									    echo "</tr>";
									    
									}
									echo "</tbody></table>";									
									$mysqli ->close();
								?>	
									
                                        </div>
                                    </div>
				</div>
				<div class="col-md-3 col-sm-3">
                                    <div class="mt-4 card">
                                        <div class="card-header">Elementparameter</div>
                                        <div class="card-body" id="elementParametersInDB"></div>
                                    </div>
                                </div>
				<div class="col-md-3 col-sm-3">
                                    <div class="mt-4 card">
                                        <div class="card-header">Elementkosten in anderen Projekten</div>
                                        <div class="card-body" id="elementPricesInOtherProjects"></div>
                                    </div>
				</div>
                    </div>
                </div>
            </div>
	</div>  
    </div>
    <div class='row'>
        <div class='col-sm-6'> 
            <div class="mt-4 card">
                <div class="card-header">Räume mit Element</div>
                <div class="card-body" id="roomsWithElement"></div>
            </div>
        </div>
        <div class='col-sm-6'> 
            <div class="mt-4 card">
                <div class="card-header">Räume ohne Element </div>
                <div class="card-body" id="roomsWithoutElement"></div>
            </div>
        </div>
    </div>
</div>               
    
<script>        
	// Tabellen formatieren
	$(document).ready(function(){	            	    
	    $('#tableElementsInDB').DataTable( {
			"paging": true,
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,
			"columnDefs": [
                            {
                                "targets": [ 0 ],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        "info": false,
                        "order": [[ 1, "asc" ]],
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"} 
	    } );				
	    	    
	    // CLICK TABELLE ELEMENTE IN DB
	    var table1 = $('#tableElementsInDB').DataTable();
 
	    $('#tableElementsInDB tbody').on( 'click', 'tr', function () {
			
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table1.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            var elementID = table1.row( $(this) ).data()[0];
                    $.ajax({
                        url : "getStandardElementParameters.php",
                        data:{"elementID":elementID},
                        type: "GET",
                        success: function(data){
                            $("#elementParametersInDB").html(data);
                            $.ajax({
                                url : "getElementPricesInDifferentProjects.php",
                                data:{"elementID":elementID},
                                type: "GET",
                                success: function(data){
                                    $("#elementPricesInOtherProjects").html(data);
                                    $.ajax({
                                        url : "getDevicesToElement.php",
                                        data:{"elementID":elementID},
                                        type: "GET",
                                        success: function(data){
                                            $("#devicesInDB").html(data);
                                            $.ajax({
                                                url : "getRoomsWithElement.php",
                                                data:{"elementID":elementID},
                                                type: "GET",
                                                success: function(data){
                                                    $("#roomsWithElement").html(data);
                                                    $.ajax({
                                                        url : "getRoomsWithoutElement.php",
                                                        data:{"elementID":elementID},
                                                        type: "GET",
                                                        success: function(data){
                                                            $("#roomsWithoutElement").html(data);
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    });

                                }
                            });

                        }
                    });
	        }
	    } );
	});
        
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
	
</script>

</body>

</html>
