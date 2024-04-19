<?php
session_start();
include '_utils.php';
check_login();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Detail</title>
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

</head>
<body>
 
    
<div class="container-fluid">
<div id="limet-navbar"></div> 

    <div class='mt-4 row'>    
        <div class='col-sm-7'>
            <div class="mt-4 card">
                <div class="card-header">Räume im Projekt</div>
                <div class="card-body">
                    <div class="table-responsive">
                            <?php
                                    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	
						
                                    /* change character set to utf8 */
                                    if (!$mysqli->set_charset("utf8")) {
                                        printf("Error loading character set utf8: %s\n", $mysqli->error);
                                        exit();
                                    }
 
                                    $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Nutzfläche, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, 
                                                    tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_räume.idTABELLE_Räume
                                                    FROM tabelle_räume INNER JOIN view_Projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = view_Projekte.idTABELLE_Projekte
                                                    WHERE (((view_Projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."));";

                                    $result = $mysqli->query($sql);

                                    echo "<table class='table table-striped table-bordered table-sm' id='tableRooms'  cellspacing='0' style='width:100%'>                 
                                    <thead><tr>
                                    <th>ID</th>
                                    <th>Raumnr</th>
                                    <th>Raumbezeichnung</th>
                                    <th>Nutzfläche</th>
                                    <th>Raumbereich Nutzer</th>
                                    </tr></thead><tbody>";

                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>".$row["idTABELLE_Räume"]."</td>";
                                        echo "<td>".$row["Raumnr"]."</td>";
                                        echo "<td>".$row["Raumbezeichnung"]."</td>";
                                        echo "<td>".$row["Nutzfläche"]."</td>";
                                        echo "<td>".$row["Raumbereich Nutzer"]."</td>";
                                        echo "</tr>";

                                    }
                                    echo "</tbody></table>";
                            ?>	
                        </div>
                    </div>
        </div> 
    </div>  
    <div class='col-sm-5'>
        <div class='mt-4 card'>
                <div class='card-header' id='vermerkPanelHead'>Vermerke zu Raum</div>
                <div class='card-body'  id='vermerke'>
                    <div class='row' id='roomVermerke'></div>
                </div>
        </div>
    </div>
</div> 
<div class="mt-4 row">
            <div class="col-sm-7">
                <div class="mt-4 card">
                    <div class='card-header'>Elemente im Raum</div>
                    <div class='card-body' >
                        <div class='row' id='roomElements'></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="mt-4 card">                                
                        <div class='card-header' id='vermerkPanelHead'>Variantenparameter</div>
                        <div class='card-body'>
                            <div class="row" id="price"></div>
                            <div class="row" id="elementParameters"></div>
                        </div>                                
                </div>
                <div class="mt-1 card">
                        <div class='card-header'>Bestandsdaten</div>
                        <div class='card-body'>
                            <div class='row' id='elementBestand'></div>
                        </div>
                </div>
                <div class="mt-1 card">
                        <div class='card-header'>Standort/Verwendungsdaten</div>
                        <div class='card-body'>
                            <div class='row' id='elementVerwendung'></div>
                        </div>
                </div>
            </div>
    </div>
    
    
</div>
</body>
    <script>
        	// Tabellen formatieren
	$(document).ready(function(){	
            $("#elementParameters").hide();
            $("#elementBestand").hide();
            $("#elementVerwendung").hide();
            
		$('#tableRooms').DataTable( {
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
			"order": [[ 1, "asc" ]],
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}                       
	    } );
	    
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
		
		
		// CLICK TABELLE RÄUME
	    var table = $('#tableRooms').DataTable();
 
	    $('#tableRooms tbody').on( 'click', 'tr', function () {
			
	        //if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	       // }
	       // else {
                    // Ausblenden der Info/Bearbeitungsfelder um Fehlinteraktion zu verhindern
                    $("#elementParameters").hide();
                    $("#elementBestand").hide();
                    $("#elementVerwendung").hide();
                    
	            //table.$('tr.info').removeClass('info');
	            //$(this).addClass('info');
	            var id = table.row( $(this) ).data()[0];
                    $.ajax({
                        url : "setSessionVariables.php",
                        data:{"roomID":id},
                        type: "GET",
                        success: function(data){
                            $("#RoomID").text(id);
                            $.ajax({
                                url : "getRoomVermerke.php",
                                type: "GET",
                                success: function(data){
                                    $("#roomVermerke").html(data);
                                    $.ajax({
                                        url : "getRoomElementsDetailed1.php",
                                        type: "GET",
                                        success: function(data){
                                            $("#roomElements").html(data);
                                        } 
                                    });
                                }
                            });	
                        }
                    });	

	        //}
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
							        }
							    });

					        }
					    });

			        }
			    });
	        }
	    } );	    
	});
	
	// DB Elemente einblenden
	
	$("#showDBElementData").click(function() {
	  if($("#DBElementData").is(':hidden')){
	    $(this).html("<span class='glyphicon glyphicon-menu-down'></span>");
	    $("#DBElementData").show();
	  }
	  else {
	  	$(this).html("<span class='glyphicon glyphicon-menu-right'></span>");
	    $("#DBElementData").hide();
	  }
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
</html> 
