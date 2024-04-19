<?php
session_start();
include '_utils.php';
check_login();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Projekte</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
  
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
 <!--
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.css"/>
 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.js"></script>
 -->
</head>

<body>    
<div class="container-fluid">
    <div id="limet-navbar"></div>
    <div class='mt-4 row'>  
        <div class='col-md-10'>
            <div class="mt-4 card">
                <div class="card-header"><b>Projekte </b>
                    <label class="float-right">
                        Nur aktive Projekte: <input type="checkbox" id="filter_ActiveProjects" checked="true"> 
                    </label>
                </div>
                <div class="card-body">
                    
                    <div class="table-responsive">
                            <?php
                                                    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                                                    /* change character set to utf8 */
                                                    if (!$mysqli->set_charset("utf8")) {
                                                        printf("Error loading character set utf8: %s\n", $mysqli->error);
                                                        exit();
                                                    }

                                                    // Abfrage aller RÃ¤ume im Projekt
                                                    //$sql="SELECT view_Projekte.idTABELLE_Projekte, view_Projekte.Interne_Nr, view_Projekte.Projektname, view_Projekte.Aktiv, view_Projekte.Neubau, view_Projekte.Bettenanzahl, view_Projekte.BGF, view_Projekte.NF, view_Projekte.Ausfuehrung, tabelle_planungsphasen.Bezeichnung, tabelle_planungsphasen.idTABELLE_Planungsphasen FROM view_Projekte INNER JOIN tabelle_planungsphasen ON view_Projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen ORDER BY view_Projekte.Interne_Nr";						
                                                    $sql="SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, tabelle_projekte.Aktiv, tabelle_projekte.Neubau, tabelle_projekte.Bettenanzahl, tabelle_projekte.BGF, tabelle_projekte.NF, tabelle_projekte.Ausfuehrung, tabelle_planungsphasen.Bezeichnung, tabelle_planungsphasen.idTABELLE_Planungsphasen FROM tabelle_projekte INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen INNER JOIN tabelle_users_have_projects ON tabelle_projekte.idTABELLE_Projekte = tabelle_users_have_projects.tabelle_projekte_idTABELLE_Projekte WHERE tabelle_users_have_projects.User = '".$_SESSION['username']."' ORDER BY tabelle_projekte.Interne_Nr;";
                                                    $result = $mysqli->query($sql);

                                                    echo "<table id='tableProjects' class='table table-striped table-bordered table-sm' cellspacing='0' width='100%'>
                                                    <thead><tr>
                                                        <th>ID</th>
                                                        <th></th>
                                                        <th>Interne_Nr</th>
                                                        <th>Projektname</th>
                                                        <th>Aktiv</th>
                                                        <th>Neubau</th>
                                                        <th>Bettenanzahl</th>
                                                        <th>BGF</th>
                                                        <th>NF</th>
                                                        <th>Bearbeitung</th>
                                                        <th>Planungsphase</th>
                                                        <th>PlanungsphasenID</th>
                                                    </tr></thead>
                                                    <tbody>";


                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td>".$row["idTABELLE_Projekte"]."</td>";
                                                        echo "<td><button type='button' id='".$row["idTABELLE_Projekte"]."' class='btn btn-outline-dark btn-xs' value='changeProject' data-toggle='modal' data-target='#changeProjectModal'><i class='fas fa-pencil-alt'></i></button></td>";
                                                        echo "<td>".$row["Interne_Nr"]."</td>";
                                                        echo "<td>".$row["Projektname"]."</td>";
                                                        echo "<td>";
                                                                if($row["Aktiv"]==1){
                                                                    echo "Ja";
                                                            }
                                                            else{
                                                                    echo "Nein";
                                                            }
                                                        echo"</td>";
                                                        echo "<td>";
                                                                if($row["Neubau"]==1){
                                                                    echo "Ja";
                                                            }
                                                            else{
                                                                    echo "Nein";
                                                            }
                                                        echo"</td>";
                                                        echo "<td>".$row["Bettenanzahl"]."</td>";
                                                        echo "<td>".$row["BGF"]."</td>";
                                                        echo "<td>".$row["NF"]."</td>";
                                                        echo "<td>".$row["Ausfuehrung"]."</td>";
                                                        echo "<td>".$row["Bezeichnung"]."</td>";
                                                        echo "<td>".$row["idTABELLE_Planungsphasen"]."</td>";                                                        
                                                        echo "</tr>";

                                                    }
                                                    echo "</tbody></table>";

                            ?>	
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            
                if($_SESSION["ext"]==0){                                

                    // Projektprüfungs-Dashboard anzeigen------------------------------
                    echo "
                        <div class='col-md-2'>
                            <div class='mt-4 card'>
                                    <div class='card-header'>Quick-Check                                
                                    </div>
                                    <div class='card-body' id='quickCheckDashboard'>                                        
                                    </div>
                            </div>
                        </div>";    
                }

            ?>
        </div> 
    
        <?php 
            
            if($_SESSION["ext"]==0){                                
                
                // Vermerke zu Projekt darstellen------------------------------
                echo "
                <div class='mt-4 row'>    
                    <div class='col-md-12'>
			<div class='card'>
		  		<div class='card-header' id='vermerkPanelHead'>
                                    <form class='form-inline'>
                                    <label class='m-1' for='vermerkeFilter'>Vermerke im Projekt</label>                                          
                                        <select class='form-control form-control-sm' id='vermerkeFilter'"; 
                                                if($_SESSION["projectName"] == "")
                                                    {
                                                        echo " style='display:none'";                                                    
                                                    } 
                                                echo ">
                                            <option value=0 selected>Alle Vermerke</option>   
                                            <option value=1>Bearbeitung offen</option>  
                                            <!--<option value=2>Eigene Vermerke</option>  -->
                                        </select>	
                                    </form>                                 
                                </div>
		  		<div class='card-body'  id='vermerke'>
                                    <div class='row' id='projectVermerke'></div>
		  		</div>
			</div>
                    </div>
                </div>";    
                                                
                // Vergabesumme zu Projekt darstellen------------------------------
                                                /*
                echo "
                <div class='mt-4 row'>    
                    <div class='col-md-12'>
			<div class='card'>
		  		<div class='card-header'>
                                    <label class='m-1' for='vergabeKostenPrognose'>Vergabekosten/Vergabekostenprognose</label>                                                                         
                                </div>
		  		<div class='card-body'  id='vergabeKostenPrognose'>
                                    <div class='col-md-3'><canvas id='chartCanvas' width='auto' height='auto'></canvas></div>
		  		</div>
			</div>
                    </div>
                </div>"; 
                                                 * 
                                                 */
            }
                       
        ?>
   
    
    
    <!-- Modal zum Ã„ndern des Projekts -->
    <div class='modal fade' id='changeProjectModal' role='dialog'>
      <div class='modal-dialog modal-md'>

        <!-- Modal content-->
        <div class='modal-content'>
          <div class='modal-header'>            
            <h4 class='modal-title'>Projekt Ã¤ndern</h4>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
          </div>
            <div class='modal-body' id='mbody'>
                <form role="form">      
                    <div class="form-group">
                        <label for="active">Aktiv:</label>
                        <select class='form-control form-control-sm' id='active' name='active'>  
                            <option value="1">Ja</option> 
                            <option value="0">Nein</option>                                    	
                        </select>	
                    </div>
                    <div class="form-group">
                        <label for="neubau">Neubau:</label>
                        <select class='form-control form-control-sm' id='neubau' name='neubau'>  
                            <option value="1">Ja</option> 
                            <option value="0">Nein</option>                                    	
                        </select>	
                    </div>
                    <div class="form-group">
                      <label for="betten">Bettenanzahl:</label>
                      <input type="text" class="form-control form-control-sm" id="betten" name="betten" />
                    </div>
                    <div class="form-group">
                      <label for="bgf">BGF:</label>
                      <input type="text" class="form-control form-control-sm" id="bgf" name="bgf" />
                    </div>
                    <div class="form-group">
                      <label for="nf">NF:</label>
                      <input type="text" class="form-control form-control-sm" id="nf" name="nf" />
                    </div>
                    <div class="form-group">
                        <label for="bearbeitung">Bearbeitung:</label>
                        <select class='form-control form-control-sm' id='bearbeitung' name='bearbeitung'>  
                            <option value="LIMET">LIMET</option> 
                            <option value="MADER">MADER</option>    
                            <option value="LIMET-MADER">LIMET-MADER</option>      
                        </select>	
                    </div>
                    <div class="form-group">
                        <label for="planungsphase">Planungsphase:</label>
                        <select class='form-control form-control-sm' id='planungsphase' name='planungsphase'>  
                            <option value="1">Vorentwurf</option> 
                            <option value="2">Entwurf</option>    
                            <option value="4">Einreichung</option>     
                            <option value="3">AusfÃ¼hrungsplanung</option> 
                        </select>	
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                  <input type='button' id='saveProject' class='btn btn-warning btn-sm' value='Speichern'></input>
                  <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

      </div>
    </div>    
    
</div>
</body>
    <script>
        var ext  ="<?php echo $_SESSION["ext"] ?>";
        //var table;
        
        $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                    /*var min = parseInt( $('#min').val(), 10 );
                    var max = parseInt( $('#max').val(), 10 );
                    var age = parseFloat( data[6] ) || 0; // use data for the age column

                    if ( ( isNaN( min ) && isNaN( max ) ) ||
                         ( isNaN( min ) && age <= max ) ||
                         ( min <= age   && isNaN( max ) ) ||
                         ( min <= age   && age <= max ) )
                         */
                    if ( settings.nTable.id !== 'tableProjects' ) {
                        return true;
                      }    
                    
                    if($("#filter_ActiveProjects").is(':checked')){
                        if (data [4] === "Ja")
                        {
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                    else{
                        return true;
                    }
                }
            );
        
	// Tabelle formatieren
	$(document).ready(function(){                                    
            if(ext === '0'){
		$('#tableProjects').DataTable( {
                    "columnDefs": [
                        {
                            "targets": [ 0,11 ],
                            "visible": false,
                            "searchable": false
                        },
                        {
                            "targets": [ 1 ],
                            "visible": true,
                            "searchable": false,
                            "sortable": false
                        }
                    ],
                    "select": true,
                    "paging": false,
                    "searching": true,
                    "info": false,
                    "order": [[ 2, "asc" ]],
                    "pagingType": "simple_numbers",
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    "mark":true
                } );
            }
            else{
                $('#tableProjects').DataTable( {
                    "columnDefs": [
                        {
                            "targets": [ 0,1,5,6,7,8,11 ],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "select": true,
                    "paging": false,
                    "searching": true,
                    "info": false,
                    "order": [[ 2, "asc" ]],
                    "pagingType": "simple_numbers",
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}		     
                } );
            }
            
	    
            var table = $('#tableProjects').DataTable();
	    $('#tableProjects tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	                     
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');	
			var id = table.row( $(this) ).data()[0];
                        var projectName = table.row( $(this) ).data()[3];
                        var projectAusfuehrung = table.row( $(this) ).data()[9];
                        var projectPlanungsphase = table.row( $(this) ).data()[10];
                        
                        document.getElementById("betten").value = table.row( $(this) ).data()[6];
                        document.getElementById("bgf").value = table.row( $(this) ).data()[7];
                        document.getElementById("nf").value = table.row( $(this) ).data()[8];
                        document.getElementById("bearbeitung").value = table.row( $(this) ).data()[9];
                        document.getElementById("planungsphase").value = table.row( $(this) ).data()[11];
                        if(ext === '0'){
                            document.getElementById("vermerkeFilter").value = 0;
                        }

                        if(table.row( $(this) ).data()[4] === "Ja"){
                            document.getElementById("active").value = 1;
                        }
                        else{
                            document.getElementById("active").value = 0;
                        }
                        if(table.row( $(this) ).data()[5] === 'Ja'){
                            document.getElementById("neubau").value = 1;
                        }
                        else{
                            document.getElementById("neubau").value = 0;
                        }
                        
          		$.ajax({
			        url : "setSessionVariables.php",
			        data:{"projectID":id,"projectName":projectName,"projectAusfuehrung":projectAusfuehrung,"projectPlanungsphase":projectPlanungsphase},
			        type: "GET",
			        success: function(data){
			            $("#projectSelected").text("Aktuelles Projekt: "+projectName);
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
							            $.ajax({
										        url : "getPersonToProjectField.php",
										        type: "GET",
										        success: function(data){
										            $("#addPersonToProject").html(data);
										            $.ajax({
                                                                                                    url : "getProjectVermerke.php",												       
                                                                                                    type: "GET",
                                                                                                    success: function(data){
                                                                                                        $("#projectVermerke").html(data);                                                                                                        
                                                                                                        $("#vermerkeFilter").show();
                                                                                                        $.ajax({
                                                                                                            url : "getProjectCheck.php",												       
                                                                                                            type: "GET",
                                                                                                            success: function(data){
                                                                                                                $("#quickCheckDashboard").html(data);   
                                                                                                                /*
                                                                                                                //------------------CHART BEFÜLLEN--------------------------------
                                                                                                                $.ajax({
                                                                                                                    url: "getChartPrognoseSum.php",
                                                                                                                    method: "GET",
                                                                                                                    success: function(data) {
                                                                                                                            console.log(data);

                                                                                                                            var deltaAbgeschlossen = 0;
                                                                                                                            var budgetOffen = 0;
                                                                                                                            var budgetAbgeschlossen = 0;

                                                                                                                            for(var i in data) {
                                                                                                                                if(data[i].Vergabe_abgeschlossen === '1'){
                                                                                                                                    deltaAbgeschlossen = deltaAbgeschlossen + data[i].Delta;
                                                                                                                                    budgetAbgeschlossen = budgetAbgeschlossen + data[i].SummevonBudget;
                                                                                                                                }
                                                                                                                                else{                                
                                                                                                                                    budgetOffen = budgetOffen + parseFloat(data[i].SummevonBudget);
                                                                                                                                }
                                                                                                                            }

                                                                                                                            // Relativwert von deltaAbgeschlossen berechnen
                                                                                                                            var deltaAbgeschlossenRelativ = parseFloat(deltaAbgeschlossen/budgetAbgeschlossen) * 100;

                                                                                                                            //Prognose Absolut berechnen
                                                                                                                            var deltaPrognoseAbsolut = parseFloat(budgetOffen * deltaAbgeschlossenRelativ) + parseFloat(deltaAbgeschlossen);

                                                                                                                            var chartdata = {
                                                                                                                                    labels: ['Aktuell', 'Prognose'],
                                                                                                                                    datasets : [
                                                                                                                                        {
                                                                                                                                            label: "Absolut Abweichung",
                                                                                                                                            backgroundColor: [
                                                                                                                                                    'rgba(71, 202, 255, 1)',
                                                                                                                                                    'rgba(71, 202, 255, 1)'
                                                                                                                                            ],
                                                                                                                                            borderColor: 'rgba(75, 192, 192, 1)',
                                                                                                                                            hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                                                                                                                            hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                                                                                                                            data: [
                                                                                                                                                    deltaAbgeschlossen,
                                                                                                                                                    deltaPrognoseAbsolut                                                
                                                                                                                                            ],
                                                                                                                                            yAxisID: 'y-axis-1',
                                                                                                                                            borderWidth: 2                                                
                                                                                                                                        },
                                                                                                                                        {
                                                                                                                                            label: "Relativ / %",
                                                                                                                                            backgroundColor: [
                                                                                                                                                    'rgba(251, 255, 30, 1)',
                                                                                                                                                    'rgba(251, 255, 30, 1)'
                                                                                                                                            ],
                                                                                                                                            borderColor: 'rgba(75, 192, 192, 1)',
                                                                                                                                            hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                                                                                                                            hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                                                                                                                            data: [
                                                                                                                                                    deltaAbgeschlossenRelativ,
                                                                                                                                                    deltaAbgeschlossenRelativ
                                                                                                                                            ],
                                                                                                                                            yAxisID: 'y-axis-2',
                                                                                                                                            borderWidth: 2                                                
                                                                                                                                        }
                                                                                                                                    ]
                                                                                                                            };

                                                                                                                            var ctx = document.getElementById("chartCanvas").getContext('2d');
                                                                                                                            var myChart = new Chart(ctx, {
                                                                                                                                type: 'bar',
                                                                                                                                data: chartdata,                                
                                                                                                                                options: {
                                                                                                                                    responsive: true,                                
                                                                                                                                    scales: {
                                                                                                                                        yAxes: [
                                                                                                                                            {
                                                                                                                                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                                                                                                                                display: true,
                                                                                                                                                position: 'left',
                                                                                                                                                id: 'y-axis-1',
                                                                                                                                                labelString: 'Absolut',
                                                                                                                                                ticks: {
                                                                                                                                                    beginAtZero: true,
                                                                                                                                                    suggestedMin: 50,
                                                                                                                                                    suggestedMax: 100
                                                                                                                                                }
                                                                                                                                            }, 
                                                                                                                                            {
                                                                                                                                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                                                                                                                                display: true,
                                                                                                                                                position: 'right',
                                                                                                                                                id: 'y-axis-2',                                            
                                                                                                                                                labelString: 'Relativ',
                                                                                                                                                gridLines: {
                                                                                                                                                        drawOnChartArea: false
                                                                                                                                                },                                           
                                                                                                                                                ticks: {
                                                                                                                                                    beginAtZero: true,
                                                                                                                                                    suggestedMin: 0,
                                                                                                                                                    suggestedMax: 100
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                        ]
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            });

                                                                                                                    },
                                                                                                                    error: function(data) {
                                                                                                                            console.log(data);
                                                                                                                    }
                                                                                                                });
                                                                                                                            */
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
		        });
	        }                
	    } );
            
            // Event listener to the two range filtering inputs to redraw on input
            $('#filter_ActiveProjects').change( function() {
                table.draw();
            } );
            
            // Wenn Seite geladen, dann Inhalte dazu laden
            $.ajax({
                url : "getProjectVermerke.php",												       
                type: "GET",
                success: function(data){
                    $("#projectVermerke").html(data);
                } 
            });  
            
            // Wenn Seite geladen, dann Project Quick-Check laden
            $.ajax({
                url : "getProjectCheck.php",												       
                type: "GET",
                success: function(data){
                    $("#quickCheckDashboard").html(data);
                } 
            });
            
            /*
            //------------------CHART BEFÜLLEN--------------------------------
            $.ajax({
		url: "getChartPrognoseSum.php",
		method: "GET",
		success: function(data) {
                        console.log(data);
                        
			var deltaAbgeschlossen = 0;
                        var budgetOffen = 0;
                        var budgetAbgeschlossen = 0;
                        
			for(var i in data) {
                            if(data[i].Vergabe_abgeschlossen === '1'){
				deltaAbgeschlossen = deltaAbgeschlossen + data[i].Delta;
                                budgetAbgeschlossen = budgetAbgeschlossen + data[i].SummevonBudget;
                            }
                            else{                                
                                budgetOffen = budgetOffen + parseFloat(data[i].SummevonBudget);
                            }
			}
                        
                        // Relativwert von deltaAbgeschlossen berechnen
                        var deltaAbgeschlossenRelativ = parseFloat(deltaAbgeschlossen/budgetAbgeschlossen) * 100;
                                                                        
                        //Prognose Absolut berechnen
                        var deltaPrognoseAbsolut = parseFloat(budgetOffen * deltaAbgeschlossenRelativ) + parseFloat(deltaAbgeschlossen);

			var chartdata = {
				labels: ['Aktuell', 'Prognose'],
				datasets : [
                                    {
                                        label: "Absolut Abweichung",
                                        backgroundColor: [
                                                'rgba(71, 202, 255, 1)',
                                                'rgba(71, 202, 255, 1)'
                                        ],
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                        hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                        data: [
                                                deltaAbgeschlossen,
                                                deltaPrognoseAbsolut                                                
                                        ],
                                        yAxisID: 'y-axis-1',
                                        borderWidth: 2                                                
                                    },
                                    {
                                        label: "Relativ / %",
                                        backgroundColor: [
                                                'rgba(251, 255, 30, 1)',
                                                'rgba(251, 255, 30, 1)'
                                        ],
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                        hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                        data: [
                                                deltaAbgeschlossenRelativ,
                                                deltaAbgeschlossenRelativ
                                        ],
                                        yAxisID: 'y-axis-2',
                                        borderWidth: 2                                                
                                    }
				]
			};

			var ctx = document.getElementById("chartCanvas").getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: chartdata,                                
                            options: {
                                responsive: true,                                
                                scales: {
                                    yAxes: [
                                        {
                                            type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                            display: true,
                                            position: 'left',
                                            id: 'y-axis-1',
                                            labelString: 'Absolut',
                                            ticks: {
                                                beginAtZero: true,
                                                suggestedMin: 50,
                                                suggestedMax: 100
                                            }
                                        }, 
                                        {
                                            type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                            display: true,
                                            position: 'right',
                                            id: 'y-axis-2',                                            
                                            labelString: 'Relativ',
                                            gridLines: {
                                                    drawOnChartArea: false
                                            },                                           
                                            ticks: {
                                                beginAtZero: true,
                                                suggestedMin: 0,
                                                suggestedMax: 100
                                            }
                                        }
                                    ]
                                }
                            }
			});

                        
		},
		error: function(data) {
			console.log(data);
		}
            });
              */          
	});
        
        // ProjektÃ¤nderungen aus Modal speichern
        $("#saveProject").click(function(){     
            var betten = $("#betten").val();
            var bgf = $("#bgf").val();
            var nf  = $("#nf").val();
            var bearbeitung = $("#bearbeitung").val();
            var planungsphase = $("#planungsphase").val();        
            var active = $("#active").val();
            var neubau = $("#neubau").val();

            if(active !== "" && neubau !== "" && bearbeitung !== "" && planungsphase !== "" && !isNaN(betten) && !isNaN(bgf) && !isNaN(nf)){
                $('#changeProjectModal').modal('hide');

                $.ajax({
                    url : "saveProject.php",
                    data:{"active":active,"neubau":neubau,"bearbeitung":bearbeitung,"planungsphase":planungsphase,"betten":betten,"bgf":bgf,"nf":nf},
                    type: "GET",	        
                    success: function(data){
                        alert(data);
                        // Neu Laden der Projektseite
                        location.reload();  
                    }
                });

            }
            else{
                    alert("Bitte alle Felder korrekt ausfÃ¼llen!");
            }          
        });
        
         // Filter-Änderung
	$('#vermerkeFilter').change(function(){
            var filterValue = this.value;	                        
	    $.ajax({
	        url : "getProjectVermerke.php",
	        data:{"filterValue":filterValue},
	        type: "GET",
	        success: function(data){
                    $("#projectVermerke").html(data);
	        }
	    });            
	});
        
          
                      
        //document.getElementById("checkGewerke").innerHTML = "<span class='badge badge-success'>Gewerke zugeteilt</span>";
        
</script>
</html> 
