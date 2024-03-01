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
    <div id="limet-navbar"></div> <!-- Container für Navbar Aufruf über onLoad -->
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
        //Load Navbar onLoad
        window.onload = function(){
            $.get("navbar.html", function(data){
                $("#limet-navbar").html(data);
                $('.navbar-nav').find('li:nth-child(3)')
                  .addClass('active');
            });
        };
    
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
