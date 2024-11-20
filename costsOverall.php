<?php
session_start();
include '_utils.php';
init_page_serversides("");
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Gesamtkosten</title>
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

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<!--DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

</head>

<body style="height:100%">

<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">

    <div class="mt-4 card">
        <div class="card-header">Gesamtprojekt</div>
        <div class="card-body">
            <button type='button' class='btn btn-outline-dark btn-sm' value='createKostenOverallPDF'><i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk</button>
            <button type='button' class='btn btn-outline-dark btn-sm' value='createKostenOverallBauabschnittPDF'><i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk und Bauabschnitt</button>
            <button type='button' class='btn btn-outline-dark btn-sm' value='createKostenOverallBauabschnittBudgetPDF'><i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk, Bauabschnitt und Budget</button>
            <button type='button' class='btn btn-outline-dark btn-sm' value='createKostenInclGHGOverallPDF'><i class='far fa-file-pdf'></i> Gesamtkosten nach Gewerk/GHG</button>
            <button type='button' class='btn btn-outline-dark btn-sm' value='createKostenRaumbereichPDF'><i class='far fa-file-pdf'></i> Raumbereich Gewerk/GHG</button>
        </div>
    </div>
    <!--
        <div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="panel panel-default">
		  		<div class="panel-heading"> <label>Gesamtkosten zu gewissem Zeitpunkt</label>                                       		  			
				</div>
		  		<div class="panel-body" id="costsAtTimestamp">
                                     <label for='date'>Ausführungsbeginn:</label>
                                     <input type='text' class='form-control' id='date' placeholder='tt.mm.jjjj'/>
                                     <button type='button' class='btn btn-default btn-md' value='createKostenOverallAtTimePDF'><span class='glyphicon glyphicon-open-file'></span> Gesamt-Kosten-Bericht</button>
		  		</div>
			</div>
		</div>
	</div>
    -->
    <div class="mt-4 card">
        <div class="card-header">Raumbereiche</div>
        <div class="card-body" id="costsRoomArea">
                                    <?php
                                        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                                        /* change character set to utf8 */
                                        if (!$mysqli->set_charset("utf8")) {
                                            printf("Error loading character set utf8: %s\n", $mysqli->error);
                                            exit();
                                        }

                                        // Abfrage aller Raumbereiche im Projekt                                       
                                        /*$sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
                                                FROM tabelle_räume
                                                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                                GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
                                                ORDER BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss;";
                                        */
                                        $sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
                                                FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
                                                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                                GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
                                                ORDER BY tabelle_räume.Geschoss;";
                                        
                                        $result = $mysqli->query($sql);

                                        echo "<table class='table table-striped table-bordered table-sm' id='tableRaumbereiche'  cellspacing='0' width='100%'>
                                        <thead><tr>
                                        <th>Raumbereich</th>
                                        <th>Geschoss</th>
                                        </tr></thead><tbody>";


                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>".$row["Raumbereich Nutzer"]."</td>";
                                            echo "<td>".$row["Geschoss"]."</td>";
                                            echo "</tr>";

                                        }
                                        echo "</tbody></table>";
                                        echo "<button type='button' class='btn btn-outline-dark btn-sm' id='createRaumbereichPDF'><i class='far fa-file-pdf'></i> Kosten-PDF</button>";                     
					?>
		</div>
	</div>
        <div class="mt-4 card">
            <div class="card-header" id='projektKosten'>Projektkostenentwicklung</div>
            <div class="card-body" id="projectCosts">	
                <canvas id="projectCostChart"></canvas>
            </div>
        </div>
    
</div>

<script>
    //RaumBereiche zur Auswahl der Berichte
    var roomBereiche = [];
    var roomBereichGeschosse = [];
    
    $("button[value='createKostenOverallPDF']").click(function(){         
        window.open('/pdf_createKostenOverallPDF.php');//there are many ways to do this
    });
    
    $("button[value='createKostenOverallBauabschnittPDF']").click(function(){         
        window.open('/pdf_createKostenOverallBauabschnittPDF.php');//there are many ways to do this
    });
    
    $("button[value='createKostenOverallBauabschnittBudgetPDF']").click(function(){         
        window.open('/pdf_createKostenOverallBauabschnittBudgetPDF.php');//there are many ways to do this
    });
    
    
    $("button[value='createKostenInclGHGOverallPDF']").click(function(){         
        window.open('/pdf_createKostenOverallInclGHGPDF.php');//there are many ways to do this
    });
    
    
    $("button[value='createKostenRaumbereichPDF']").click(function(){         
        window.open('/pdf_createKostenRaumbereichInclGHGPDF.php');//there are many ways to do this
    });
    
    $("button[value='createKostenOverallAtTimePDF']").click(function(){ 
        var datum = $("#date").val();
        alert(datum);
       // window.open('/pdf_createKostenOverallAtTimePDF.php?datum='+datum);//there are many ways to do this
    });
    
    
    $(document).ready(function(){            
        $('#tableRaumbereiche').DataTable( {
                "paging": true,
                "searching": true,
                "info": false,
                "order": [[ 1, "asc" ]],
                "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
                "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                "select": {
                    style: 'multi'
                }
                
        } );
        
        // CLICK TABELLE RÄUME
        var table = $('#tableRaumbereiche').DataTable(); 
        $('#tableRaumbereiche tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('info') ) {
                $(this).removeClass('info');	            
                for(var i = roomBereiche.length - 1; i >= 0; i--) {
                    if(roomBereiche[i] === table.row( $(this) ).data()[0]) {
                       roomBereiche.splice(i, 1);
                    }
                }
                for(var i = roomBereichGeschosse.length - 1; i >= 0; i--) {
                    if(roomBereichGeschosse[i] === table.row( $(this) ).data()[1]) {
                       roomBereichGeschosse.splice(i, 1);
                    }
                }
            }
            else {
                $(this).addClass('info');
                roomBereiche.push(table.row( $(this) ).data()[0]);
                roomBereichGeschosse.push(table.row( $(this) ).data()[1]);
            }
        } );
        
        $('#date').datepicker({
                    format: "dd.mm.yyyy",
                    calendarWeeks: true,
                    autoclose: true,
                    todayBtn: "linked"
        });
        
        //Diagramm zeichnen
        $.ajax({
		url: "getChartProjectCosts.php",
		method: "GET",
		success: function(data) {
			console.log(data);
			var summeNeu = [];
                        var summeBestand = [];
                        var summeGesamt = [];
			var datum = [];

			for(var i in data) {                            
				summeBestand.push(data[i][1]);
                                summeNeu.push(data[i][2]);
                                var b = parseInt(data[i][1]);
                                var n = parseInt(data[i][2]);
                                var summe = b+n;
                                summeGesamt.push(summe);
				datum.push(data[i][0]);
			}                        
                       
			var chartdata = {
                            labels: datum,
                            datasets : [
                                {
                                    label: "Neu",
                                    backgroundColor: 'rgba(0, 0, 0, 0)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                    hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                    data: summeNeu,
                                    borderWidth: 2                                                
                                },
                                {
                                    label: "Bestand",
                                    backgroundColor: 'rgba(0, 0, 0, 0)',
                                    borderColor: 'rgba(0, 217, 0, 1)',
                                    hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                    hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                    data: summeBestand,
                                    borderWidth: 2                                                
                                },
                                {
                                    label: "Gesamt",
                                    backgroundColor: 'rgba(0, 0, 0, 0)',
                                    borderColor: 'rgba(255, 255, 0, 1)',
                                    hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                                    hoverBorderColor: 'rgba(200, 200, 200, 1)',
                                    data: summeGesamt,
                                    borderWidth: 2                                                
                                }
                            ]
			};
                        

			var ctx = $("#projectCostChart");

			var lineGraph = new Chart(ctx, {
				type: 'line',
				data: chartdata
                                
			});
		},
		error: function(data) {
			console.log(data);
		}
	});
        
    } );
    
    $('#createRaumbereichPDF').click(function(){    
            if(roomBereiche.length === 0){
	    	alert("Kein Raumbereich ausgewählt!");
	    }
            else{      
               window.open('/pdf_createKostenRaumbereichPDF.php?roomBereiche='+roomBereiche+'&roomBereichGeschosse='+roomBereichGeschosse);//there are many ways to do this
            }            
            
        });


	
</script>

</body>

</html>
