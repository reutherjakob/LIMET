<?php
session_start();
include '_utils.php';
check_login();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Kostenänderungen</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
 


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
 
 
</head>

<body style="height:100%">
 
<div class="container-fluid" >
     <div id="limet-navbar"></div> <!-- Container für Navbar -->		

    <div class="mt-4 card">
                <div class="card-header">Raumbuchänderungen</div>
                <div class="card-body">
                    <?php
                            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                            /* change character set to utf8 */
                            if (!$mysqli->set_charset("utf8")) {
                                printf("Error loading character set utf8: %s\n", $mysqli->error);
                                exit();
                            }

                            $sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_rb_aenderung.Timestamp, tabelle_rb_aenderung.User, tabelle_rb_aenderung.Anzahl, tabelle_rb_aenderung.Anzahl_copy1, tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten, tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten_copy1, tabelle_elemente.idTABELLE_Elemente, tabelle_varianten.Variante AS Var_Alt, tabelle_varianten_1.Variante AS Var_Neu, tabelle_rb_aenderung.`Neu/Bestand`, tabelle_rb_aenderung.`Neu/Bestand_copy1`, tabelle_rb_aenderung.Standort, tabelle_rb_aenderung.Standort_copy1
                                FROM tabelle_varianten AS tabelle_varianten_1 RIGHT JOIN (tabelle_varianten RIGHT JOIN (tabelle_elemente INNER JOIN (tabelle_rb_aenderung INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_rb_aenderung.id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten) ON tabelle_varianten_1.idtabelle_Varianten = tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten_copy1
                                WHERE (((Not (tabelle_rb_aenderung.Anzahl)=`tabelle_rb_aenderung`.`Anzahl_copy1`)) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].")) OR (((tabelle_rb_aenderung.Anzahl) Is Null) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].")) OR (((Not (tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten)=`tabelle_rb_aenderung`.`tabelle_Varianten_idtabelle_Varianten_copy1`)) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].")) OR (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((Not (tabelle_rb_aenderung.`Neu/Bestand`)=`tabelle_rb_aenderung`.`Neu/Bestand_copy1`))) OR (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((Not (tabelle_rb_aenderung.Standort)=`tabelle_rb_aenderung`.`Standort_copy1`)))
                                ORDER BY tabelle_rb_aenderung.Timestamp DESC;";
                            $result = $mysqli->query($sql);

                            echo "<table class='table table-striped table-bordered table-sm' id='tableCostChanges'  cellspacing='0' width='100%'>
                            <thead><tr>
                            <th>Raumbereich Nutzer</th>
                            <th>Raumnr</th>
                            <th>Raumbezeichnung</th>
                            <th>Element</th>
                            <th>Datum</th>
                            <th>Nutzer</th>
                            <th>Stk vorher</th>
                            <th>Stk nachher</th>
                            <th>Varianten vorher</th>
                            <th>Variante nachher</th>
                            <th>Bestand vorher</th>
                            <th>Bestand nachher</th>
                            <th>Standort vorher</th>
                            <th>Standort nachher</th>
                            <th></th>
                            </tr></thead><tbody>";

                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>".$row["Raumbereich Nutzer"]."</td>";
                                echo "<td>".$row["Raumnr"]."</td>";
                                echo "<td>".$row["Raumbezeichnung"]."</td>";
                                echo "<td>".$row["ElementID"]." - ".$row["Bezeichnung"]."</td>";
                                echo "<td>".$row["Timestamp"]."</td>";
                                echo "<td>".$row["User"]."</td>";
                                echo "<td>".$row["Anzahl"]."</td>";
                                echo "<td>".$row["Anzahl_copy1"]."</td>";
                                echo "<td>".$row["Var_Alt"]."</td>";
                                echo "<td>".$row["Var_Neu"]."</td>";
                                echo "<td>".$row["Neu/Bestand"]."</td>";
                                echo "<td>".$row["Neu/Bestand_copy1"]."</td>";
                                echo "<td>".$row["Standort"]."</td>";
                                echo "<td>".$row["Standort_copy1"]."</td>";
                                echo "<td><button type='button' id='".$row["idTABELLE_Elemente"]."' class='btn btn-outline-dark btn-sm' value='showVarianteCostChanges'  data-toggle='modal' data-target='#getElementPriceHistoryModal'><i class='fas fa-chart-area'></i>Kosten-Änderungen</button></td>";
                                echo "</tr>";						    
                            }
                            echo "</tbody></table>";												
                    ?>	
	</div>
</div>

<!-- Modal zum Zeigen der Kostenänderungen -->
	  <div class='modal fade' id='getElementPriceHistoryModal' role='dialog'>
	    <div class='modal-dialog modal-md'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Varianten-Kostenänderungen</h4>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='mbody'>
	    	</div>
	        <div class='modal-footer'>
	          	<button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Schließen</button>
	        </div>
	      </div>	      
	    </div>
	  </div>


<script>
		
	// Tabellen formatieren
	$(document).ready(function(){		
		$('#tableCostChanges').DataTable( {
			"paging": true,
			"info": true,
			"order": [[ 4, "desc" ]],
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,                        
                        dom: 'Bfrtip',
                        buttons: [
                            'excel', 'copy', 'csv'
                        ]
	        
	    } );
	});

	//Variantenkostenänderungen anzeigen
        /*
	$("input[value='showVarianteCostChanges']").click(function(){
	    var ID = this.id;

		$.ajax({
                        url : "getVarianteCostChanges.php",
                        type: "GET",
                        data:{"elementID":ID},
                        success: function(data){
                            $("#mbody").html(data);	            			 			
                        } 
                });        
        });
        */

	$("button[value='showVarianteCostChanges']").click(function(){
	    var ID = this.id;

		$.ajax({
	        url : "getVarianteCostChanges.php",
	        type: "GET",
	        data:{"elementID":ID},
	        success: function(data){
	            $("#mbody").html(data);	            			 			
	        } 
        });		     
    });

</script>

</body>

</html>
