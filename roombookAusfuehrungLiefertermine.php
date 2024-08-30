<?php
    session_start();
    $_SESSION["dbAdmin"]="0"; 
    include '_utils.php';
    init_page_serversides();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB - Ausführung</title>
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

<!--DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
 
</head>
<body style="height:100%">
 
<div class="container-fluid" >
    <div id="limet-navbar"></div> 
    <div class='row'>
        <div class='col-sm-11'>  
            <div class="mt-4 card">
                <div class="card-header"><b>Elemente im Projekt</b>
                </div>
                <div class="card-body">
		  			<?php
						$mysqli = utils_connect_sql();
						
                                                $sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_bauphasen.bauphase, tabelle_bauphasen.datum_fertigstellung, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosBezeichnung_Extern,tabelle_lose_extern.LosNr_Extern, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Lieferdatum, tabelle_räume_has_tabelle_elemente.id
                                                        FROM ((((tabelle_räume LEFT JOIN tabelle_bauphasen ON tabelle_räume.tabelle_bauphasen_idtabelle_bauphasen = tabelle_bauphasen.idtabelle_bauphasen) INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) INNER JOIN tabelle_varianten ON tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_varianten.idtabelle_Varianten) LEFT JOIN tabelle_lose_extern ON tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.Anzahl > 0);";
                                                
						$result = $mysqli->query($sql);
		
						echo "<table class='table table-striped table-bordered table-sm' id='tableElements'  cellspacing='0' width='100%'>
						<thead><tr>
						<th>ID</th>
                                                <th>Raumbereich Nutzer</th>
                                                <th>Geschoss</th>
                                                <th>Bauetappe</th>
                                                <th>Bauabschnitt</th>
                                                <th>Bauphase</th>
                                                <th>Bauphase-Fertigstellung</th>
						<th>Raumnr</th>
						<th>Raumbezeichnung</th>
						<th>Gewerk</th>		
//                                                <th>LosBezeichnung</th>
                                                <th>Anzahl</th>                                                
                                                <th>ElementID</th>                                                
                                                <th>Element</th>
                                                <th>Variante</th>                                                 
                                                <th>Bestand</th> 
                                                <th>Lieferdatum</th> 
						</tr></thead><tbody>";
						
						while($row = $result->fetch_assoc()) {
						    echo "<tr>";
						    echo "<td>".$row["id"]."</td>";
                                                    echo "<td>".$row["Raumbereich Nutzer"]."</td>";
						    echo "<td>".$row["Geschoss"]."</td>";
						    echo "<td>".$row["Bauetappe"]."</td>";
						    echo "<td>".$row["Bauabschnitt"]."</td>";
						    echo "<td>".$row["bauphase"]."</td>";
                                                    echo "<td>".$row["datum_fertigstellung"]."</td>";
                                                    echo "<td>".$row["Raumnr"]."</td>";
                                                    echo "<td>".$row["Raumbezeichnung"]."</td>";
                                                    echo "<td>".$row["LosNr_Extern"]."</td>";
                                                     echo "<td>".$row["LosBezeichnung_Extern"]."</td>";
                                                    echo "<td>".$row["Anzahl"]."</td>";
                                                    echo "<td>".$row["ElementID"]."</td>";
                                                    echo "<td>".$row["Bezeichnung"]."</td>";
                                                    echo "<td>".$row["Variante"]."</td>";                                                                                                      
                                                    echo "<td>";
                                                        if($row["Neu/Bestand"] === '0'){
                                                            echo "Ja";
                                                        }
                                                        else{
                                                            echo "Nein";
                                                        }
                                                    echo "</td>";
                                                    echo "<td>".$row["Lieferdatum"]."</td>";
						    echo "</tr>";
						    
						}
						echo "</tbody></table>";												
					?>	
                        </div>
                </div>
        </div>
        <div class='col-sm-1'>  
            <div class="mt-4 card">
                <div class="card-header"><b>Lieferdatum</b>
                </div>
                <div class="card-body">
                    <form role='form'>  
                        <div class='form-group'>
                            <label for='lieferdatum'>Lieferdatum:</label>
                            <input type='text' class='form-control form-control-sm' id='lieferdatum' placeholder='jjjj-mm-tt'/>
                            <input type='button' id='checkElements' class='btn btn-success btn-sm' value='Speichern' ></input>
                        </div>						 	
                    </form>
                </div>
            </div>
        </div>
    </div>                
</div> 
    
    <!-- Modal für Abfrage -->
    <div class='modal fade' id='saveLieferdatumModal' role='dialog'>
      <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
          <div class='modal-header'>	          
            <h4 class='modal-title'>Lieferdatum speichern</h4>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
          </div>
          <div class='modal-body' id='mbody'>Wollen Sie das Lieferdatum für alle gewählten Elemente ändern?
          </div>
          <div class='modal-footer'>
                  <input type='button' id='saveLieferdatum' class='btn btn-success btn-sm' value='Ja' data-dismiss='modal'></input>
                  <button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Nein</button>
          </div>
        </div>
      </div>
    </div>
<script>        
	var elementIDs = [];

	$(document).ready(function(){	            
            
            $('#tableElements').DataTable( {
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
                        "orderMulti": true,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                        "mark":true,
                        "select": {
                            style: 'multi'
                        }
	    } );
            
            var table = $('#tableElements').DataTable();
            $('#tableElements tbody').on( 'click', 'tr', function () {			
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
                    for(var i = elementIDs.length - 1; i >= 0; i--) {
                        if(elementIDs[i] === table.row( $(this) ).data()[0]) {
                           elementIDs.splice(i, 1);
                        }
                    }	
	        }
	        else {                  
	            //table1.$('tr.info').removeClass('info');
	            $(this).addClass('info');                    
                    document.getElementById("lieferdatum").value = table.row( $(this) ).data()[15];                     
                    elementIDs.push(table.row( $(this) ).data()[0]);
	        }
	    } );
            
            $('#lieferdatum').datepicker({
                    format: "yyyy-mm-dd",
                    calendarWeeks: true,
                    autoclose: true,
                    todayBtn: "linked"
            });
	});
        
        $("#checkElements").click(function(){
	    if(elementIDs.length === 0){
	    	alert("Kein Element ausgewählt!");
	    }
	    else{
                $('#saveLieferdatumModal').modal('show'); 
            }
        });
        
        $("#saveLieferdatum").click(function(){
            var lieferdatum = $("#lieferdatum").val();
	    $.ajax({
                url : "saveLieferdatum.php",
                type: "GET",
                data:{"elements":elementIDs, "lieferdatum":lieferdatum},
                success: function(data){
                    $('#saveLieferdatumModal').modal('hide');
                    alert(data);
                    window.location.replace("roombookAusfuehrungLiefertermine.php");
                }
            });
        });
</script>

</body>

</html>
