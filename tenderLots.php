<?php
session_start();
include '_utils.php';
check_login();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Losverwaltung</title>
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
  




<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>


<!--DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

<!--Bootstrap Toggle -->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

</head>

<body style="height:100%" id="bodyTenderLots">

<div class="container-fluid" >

    <div id="limet-navbar"></div> <!-- Container für Navbar -->	
    
                <div class='row'>
                    <div class='col-sm-8'>  
                        <div class="mt-4 card">
                            <div class="card-header">Lose im Projekt</div>
                            <div class="card-body" id="projectLots">
                                <div class="table-responsive">
                                        <button type='button' class='btn btn-default btn-sm' id='createTenderListPDF'><i class='far fa-file-pdf'></i> Losliste mit Elementen-PDF</button>
                                        <button type='button' class='btn btn-default btn-sm' id='createTenderListWithoutElementsPDF'><i class='far fa-file-pdf'></i> Losliste-PDF</button>
                                        <button type='button' class='btn btn-default btn-sm' id='createTenderWorkflowPDF'><i class='far fa-file-pdf'></i> Workflow-PDF</button>
				  	<?php
								$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	
								
								/* change character set to utf8 */
								if (!$mysqli->set_charset("utf8")) {
								    printf("Error loading character set utf8: %s\n", $mysqli->error);
								    exit();
								}
                                                                
                                                                // Abfrage der möglichen Lieferanten
                                                                $sql = "SELECT `tabelle_lieferant`.`idTABELLE_Lieferant`,
                                                                            `tabelle_lieferant`.`Lieferant`
                                                                        FROM `LIMET_RB`.`tabelle_lieferant`
                                                                        ORDER BY `Lieferant`;";

                                                                $result = $mysqli->query($sql);

                                                                $possibleAuftragnehmer = array();
                                                                while ($row = $result->fetch_assoc()) {
                                                                    $possibleAuftragnehmer[$row['idTABELLE_Lieferant']]['idTABELLE_Lieferant'] = $row['idTABELLE_Lieferant'];
                                                                    $possibleAuftragnehmer[$row['idTABELLE_Lieferant']]['Lieferant'] = $row['Lieferant'];
                                                                }
                                                                
								
								// Abfrage der externen Lose                                                                
                                                                $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, 
                                                                                tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.Verfahren, tabelle_lose_extern.mkf_von_los,
                                                                                tabelle_lose_extern.Bearbeiter, tabelle_lose_extern.Vergabesumme, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Notiz, tabelle_lose_extern.Kostenanschlag, tabelle_lose_extern.Budget,
                                                                                tabelle_lieferant.Lieferant, tabelle_lieferant.idTABELLE_Lieferant,
                                                                                losschaetzsumme.Summe,
                                                                                losbestandschaetzsumme.SummeBestand,
                                                                                losschaetzsumme.id,
                                                                                losbestandschaetzsumme.id
                                                                        FROM tabelle_lieferant 
                                                                        RIGHT JOIN tabelle_lose_extern 
                                                                        ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
                                                                        LEFT JOIN
                                                                                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS Summe
                                                                                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                                                                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                                                                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                                                                            AS losschaetzsumme
                                                                            ON (tabelle_lose_extern.idtabelle_Lose_Extern = losschaetzsumme.id)
                                                                        LEFT JOIN 
                                                                                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS SummeBestand
                                                                                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                                                                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                                                                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                                                                            AS losbestandschaetzsumme
                                                                            ON (tabelle_lose_extern.idtabelle_Lose_Extern = losbestandschaetzsumme.id)
                                                                        WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                                                        ORDER BY LosNr_Extern;";
                                                                
								
								$result = $mysqli->query($sql);                                                                                                                                
                                                                
								echo "<table  id='tableTenderLots' class='table table-striped table-bordered table-sm' cellspacing='0' width='100%'>
								<thead><tr>
								<th>ID</th>
                                                                <th></th>
								<th>Nummer</th>
								<th>Bezeichnung</th>
                                                                <th></th>                                                                
                                                                <th>Versand</th>
								<th>Liefertermin</th>
                                                                <th>Verfahren</th>
                                                                <th>Bearbeiter</th>
								<th>Status</th>
                                                                <th>Schätzung-Neu</th>
                                                                <th>Schätzung-Bestand</th>
                                                                <th>Kostenanschlag</th>
                                                                <th>Budget (val)</th>
								<th>Vergabesumme</th>
                                                                <th>Auftragnehmer</th>                                                                
                                                                <th>IDLieferant</th>
                                                                <th>Vergabe abgeschlossen</th>                                                                  
                                                                <th>MKF-von_Los</th>   
                                                                <th>Notiztext</th> 
                                                                <th></th>
                                                                <th>KA</th>
                                                                <th>BUD</th>
                                                                <th>VS</th>
								</tr></thead>";
								echo "<tbody>";
                                                                
                                                                
                                                                setlocale(LC_MONETARY,"de_DE");
                                                                
                                                                
								//Array zum speichern der Losnummer
                                                                $hauptLose = array();
                                                                
								while($row = $result->fetch_assoc()) {
								    echo "<tr>";
								    echo "<td>".$row["idtabelle_Lose_Extern"]."</td>";                 
                                                                    echo "<td><button type='button' id='".$row["idtabelle_Lose_Extern"]."' class='btn btn-outline-dark btn-xs' value='changeTenderLot'><i class='fas fa-pencil-alt'></i></button></td>";                                         
								    echo "<td>".$row["LosNr_Extern"]."</td>";
								    echo "<td>".$row["LosBezeichnung_Extern"]."</td>";
                                                                    echo "<td>"; 
                                                                        if(strlen($row["Notiz"])>0){
                                                                            echo "<span class='glyphicon glyphicon-list-alt' data-toggle='tooltip' data-placement='auto right' title='".$row["Notiz"]."'>";	
                                                                        }										
                                                                    echo "</td>";                                                                                                            
                                                                    echo "<td>".$row["Versand_LV"]."</td>";
								    echo "<td>".$row["Ausführungsbeginn"]."</td>";	
                                                                    echo "<td>".$row["Verfahren"]."</td>";
                                                                    echo "<td>".$row["Bearbeiter"]."</td>";
								    echo "<td align='center'>";
                                                                    switch ($row["Vergabe_abgeschlossen"]) {
                                                                        case 0:
                                                                            //echo "<b><font color='red'>&#10007;</font></b>";
                                                                            echo "<span class='badge badge-pill badge-danger'>Offen</span>";
                                                                            break;
                                                                        case 1:
                                                                            //echo "<b><font color='green'>&#10003;</font></b>";
                                                                            echo "<span class='badge badge-pill badge-success'>Fertig</span>";
                                                                            break;
                                                                        case 2:
                                                                            //echo "<b><font color='blue'>&#8776;</font></b>";
                                                                            echo "<span class='badge badge-pill badge-primary'>Wartend</span>";
                                                                            break;
                                                                    }									
							   	    echo "</td>";     
                                                                    echo "<td align='right'>".money_format("€ %!n", $row["Summe"])."</td>";
                                                                    echo "<td align='right'>".money_format("€ %!n", $row["SummeBestand"])."</td>";
                                                                    echo "<td align='right'>".money_format("€ %!n", $row["Kostenanschlag"])."</td>";
                                                                    echo "<td align='right'>".money_format("€ %!n", $row["Budget"])."</td>";
                                                                    echo "<td align='right'>".money_format("€ %!n", $row["Vergabesumme"])."</td>";									
                                                                    echo "<td>".$row["Lieferant"]."</td>";                                                                    
								    echo "<td>".$row["idTABELLE_Lieferant"]."</td>";
                                                                    echo "<td>".$row["Vergabe_abgeschlossen"]."</td>";
                                                                    echo "<td>".$row["mkf_von_los"]."</td>";
                                                                    echo "<td>".$row["Notiz"]."</td>";
                                                                    echo "<td><button type='button' id='".$row["idtabelle_Lose_Extern"]."' class='btn btn-outline-dark btn-xs' value='LotWorkflow' data-toggle='modal' data-target='#workflowDataModal'><i class='fas fa-history'></i></button></td>"; 
                                                                    echo "<td>".$row["Kostenanschlag"]."</td>";
                                                                    echo "<td>".$row["Budget"]."</td>";
                                                                    echo "<td>".$row["Vergabesumme"]."</td>";	
                                                                    echo "</tr>";
								    $hauptLose[$row['idtabelle_Lose_Extern']]['idtabelle_Lose_Extern'] = $row['idtabelle_Lose_Extern'];
                                                                    $hauptLose[$row['idtabelle_Lose_Extern']]['LosNr_Extern'] = $row['LosNr_Extern'];
                                                                    $hauptLose[$row['idtabelle_Lose_Extern']]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];
								}
								echo "</tbody></table>";
                                                               
                                                            if($_SESSION["ext"]==0){
								echo "<input type='button' id='addTenderLotModalButton' class='btn btn-success btn-sm' value='Los hinzufügen' data-toggle='modal' data-target='#addTenderLotModal'></input>";
                                                            }
                                                            $mysqli ->close();
					?>	  
				</div>
                            </div>
			</div> 
                    </div>
                    <?php
                    if($_SESSION["ext"]==0){
                        echo "<div class='col-sm-4'> 
                            <div class='mt-4 card'>
                                    <div class='card-header' id='vermerkePanelHead'>Vermerke zu Los</div>
                                    <div class='card-body'  id='lotVermerke'>	                                        
                                    </div>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
                    
        <div class="row">
            <div class="col-sm-8">                
                <div class="mt-4 card">
                            <div class="card-header">Elemente im Los</div>
                            <div class="card-body" id="elementsInLot"></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="mt-4 card">
                    <div class="card-header">Variantenparameter</div>
                    <div class="card-body" id="elementsvariantenParameterInLot"></div>
                </div>
                <div class="mt-4 card">
                            <div class="card-header">Bestandsdaten</div>
                            <div class="card-body" id="elementelementBestandsInLot"></div>
                </div>                        
            </div>
        </div>
</div>
    
    <!-- Modal zum Anlegen eines Loses-->
	  <div class='modal fade' id='addTenderLotModal' role='dialog'>
	    <div class='modal-dialog modal-md'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Losdaten</h4>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='mbody'>
	        		<form role="form">
                                    <form role='form'>  
                                        <input id="lotMKF" data-toggle="toggle" type="checkbox" data-on="MKF" data-off="MKF" data-onstyle="success" data-offstyle="danger"></input>

                                            <div class='form-group'>
                                                <label for='lotMKFOf'>Los wählen:</label>
                                                    <select class='form-control form-control-sm' id='lotMKFOf' disabled>
                                                        <option value=0 selected>Hauptlos wählen</option>
                                                           <?php                                              
                                                                foreach($hauptLose as $array) {                                                                                                                            
                                                                    echo "<option value=".$array['idtabelle_Lose_Extern'].">".$array['LosNr_Extern']." - ".$array['LosBezeichnung_Extern']."</option>";                                                             		
                                                                }
                                                            ?>
                                                    </select>						
                                            </div>
                                            <div class='form-group'>
                                              <label for='lotNr'>Losnummer:</label>
                                              <input type='text' class='form-control form-control-sm' id='lotNr' placeholder='Losnummer'/>
                                            </div>                                                  
                                            <div class='form-group'>
                                              <label for='lotName'>Bezeichnung:</label>
                                              <input type='text' class='form-control form-control-sm' id='lotName' placeholder='Losbezeichnung'/>
                                            </div>
                                            <div class='form-group'>
						    <label for='lotLVSend'>Versand LV:</label>
						    <input type='text' class='form-control form-control-sm' id='lotLVSend' placeholder='jjjj-mm-tt'/>
						  </div>
        				  <div class='form-group'>
						    <label for='lotStart'>Ausführungsbeginn:</label>
						    <input type='text' class='form-control form-control-sm' id='lotStart' placeholder='jjjj-mm-tt'/>
						  </div>
                                            <div class='form-group'>
						    <label for='lotVerfahren'>Verfahren:</label>
						    <input type='text' class='form-control form-control-sm' id='lotVerfahren' placeholder='Verfahren'/>
						  </div>
                                            <div class='form-group'>
						    <label for='lotLVBearbeiter'>Bearbeiter:</label>
						    <input type='text' class='form-control form-control-sm' id='lotLVBearbeiter' placeholder='Bearbeiter'/>
						  </div>
                                                <div class='form-group'>
						    <label for='kostenanschlag'>Kostenanschlag: (.)</label>
						    <input type='text'  class='form-control form-control-sm' id='kostenanschlag' placeholder='0'/>
						  </div>
                                                <div class='form-group'>
						    <label for='budget'>Budget (valorisiert): (.)</label>
						    <input type='text'  class='form-control form-control-sm' id='budget' placeholder='0'/>
						  </div>
						  <div class='form-group'>
						    <label for='lotSum'>Vergabesumme: (.)</label>
						    <input type='text'  class='form-control form-control-sm' id='lotSum' placeholder='Summe'/>
						  </div>
						  <div class='form-group'>
						    <label for='lotVergabe'>Status:</label>
						    	<select class='form-control form-control-sm' id='lotVergabe'>						
                                                            <option value='0' selected>Nicht abgeschlossen</option>
                                                            <option value='1'>Abgeschlossen</option>
                                                            <option value='2'>Wartend</option>
                                                        </select>						
                                                </div>
                                            <div class='form-group'>
                                                <label for='lotAuftragnehmer'>Auftragnehmer:</label>
                                                    <select class='form-control form-control-sm' id='lotAuftragnehmer'>
                                                        <option value=0 selected>Auftragnehmer wählen</option>
                                                           <?php                                              
                                                            foreach($possibleAuftragnehmer as $array) {                                                                                                                            
                                                                echo "<option value=".$array['idTABELLE_Lieferant'].">".$array['Lieferant']."</option>";                                                             		
                                                            }
                                                            ?>
                                                    </select>						
                                            </div>
                                            <div class='form-group'>
                                                <label for='lotNotice'>Notiz:</label>
                                                <textarea class='form-control form-control-sm' rows='5' id='lotNotice' placeholder='Notiz'></textarea>
                                              </div> 						 	
					</form>
			</div>
	        <div class='modal-footer'>
	        	<input type='button' id='addTenderLot' class='btn btn-success btn-sm' value='Hinzufügen'></input>
                        <input type='button' id='saveTenderLot' class='btn btn-warning btn-sm' value='Speichern'></input>
	          	<button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
	        </div>
	      </div>	      
	    </div>
	  </div>
    
    
          <!-- Modal zum Zeigen des Los-Workflows -->
	  <div class='modal fade' id='workflowDataModal' role='dialog'>
	    <div class='modal-dialog modal-lg'>	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>	          
	          <h4 class='modal-title'>Los-Workflow</h4>
                  <button type='button' class='close' data-dismiss='modal'>&times;</button>
	        </div>
	        <div class='modal-body' id='workflowModalBody'>
	    	</div>
	        <div class='modal-footer'>
	          	<button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Schließen</button>
	        </div>
	      </div>	      
	    </div>
	  </div>
          
          <!-- Modal Info -->
	<div class='modal fade' id='infoModal' role='dialog'>
	    <div class='modal-dialog modal-dialog-centered modal-sm'>	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Info</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='infoBody'>
	        </div>
	        <div class='modal-footer'>
                    <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>OK</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>
</body>
<script>

        var ext  ='<?php echo $_SESSION["ext"] ?>';
        var searchV = '<?php echo $_GET["searchValue"] ;?>';
        var lotID;
        var lotVerfahren;
	// Tabelle formatieren
	$(document).ready(function(){              
            if(ext === '0'){                
		$('#tableTenderLots').DataTable( {
			"columnDefs": [
                            {
                                "targets": [ 0,16,17,18,19,21,22,23 ],
                                "visible": false,
                                "searchable": false
                            },
                            {
                                "targets": [ 4,1 ],
                                "sortable": false,
                                "searchable": false
                            }
                        ],     
                        "select": true,
                        "search": {search:searchV},
			"paging": true,
			"searching": true,
			"info": true,
			"order": [[ 2, "asc" ]],
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,                        
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                        dom: 'Blfrtip',
                        "buttons": [
                            'excel'
                        ]
               } );
            }
            else{
                $('#tableTenderLots').DataTable( {
			"columnDefs": [
                            {
                                "targets": [ 0,4,10,11,15,16,17,18,19,21,22,23 ],
                                "visible": false,
                                "searchable": false
                            }                     
                        ],
			"paging": true,
			"searching": true,
			"info": true,
			"order": [[ 1, "asc" ]],
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 20, 
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
                } );
            }
            
            // CLICK TABELLE tenderLots
	    var table1 = $('#tableTenderLots').DataTable();                                    
            
	    $('#tableTenderLots tbody').on( 'click', 'tr', function () {                
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');                                        
	        }
	        else {                  
	            //table1.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            lotID  = table1.row( $(this) ).data()[0]; 
                    lotVerfahren1 = table1.row( $(this) ).data()[5];
                    
                    //Parameter in Modal befüllen                    
                    if(lotVerfahren1 === "MKF"){
                        $('#lotMKF').bootstrapToggle('enable');
                        $('#lotMKF').bootstrapToggle('on'); 
                        $('#lotMKF').bootstrapToggle('disable'); 
                        $("#lotMKFOf").prop('disabled', true);
                    }
                    else{
                        $('#lotMKF').bootstrapToggle('enable');
                        $('#lotMKF').bootstrapToggle('off'); 
                        $('#lotMKF').bootstrapToggle('disable');                        
                    }
                    document.getElementById("lotNr").value = table1.row( $(this) ).data()[2]; 
                    document.getElementById("lotName").value = table1.row( $(this) ).data()[3];
                    document.getElementById("lotLVSend").value = table1.row( $(this) ).data()[5]; 
                    document.getElementById("lotStart").value = table1.row( $(this) ).data()[6];
                    document.getElementById("lotVerfahren").value = table1.row( $(this) ).data()[7];
                    document.getElementById("lotLVBearbeiter").value = table1.row( $(this) ).data()[8];
                    document.getElementById("kostenanschlag").value = table1.row( $(this) ).data()[21];
                    document.getElementById("budget").value = table1.row( $(this) ).data()[22];
                    document.getElementById("lotSum").value = table1.row( $(this) ).data()[23];
                    document.getElementById("lotVergabe").value = table1.row( $(this) ).data()[17]; 
                    document.getElementById("lotAuftragnehmer").value = table1.row( $(this) ).data()[16];
                    document.getElementById("lotMKFOf").value = table1.row( $(this) ).data()[18];
                    document.getElementById("lotNotice").value = table1.row( $(this) ).data()[19];
                    
                    $.ajax({
                        url : "getLotVermerke.php",
                        data:{"lotID":lotID},
                        type: "GET",
                        success: function(data){
                           $("#lotVermerke").html(data);
                            $.ajax({
                                url : "getTenderLotElements.php",
                                data:{"lotID":lotID},
                                type: "GET",
                                success: function(data){
                                    $("#elementsInLot").html(data);
                                    $("#elementelementBestandsInLot").hide();
                                    $("#elementsvariantenParameterInLot").hide();
                                }
                            });
                        }
                    });
	        }
	    } );
            
            $('#lotLVSend').datepicker({
                    format: "yyyy-mm-dd",
                    calendarWeeks: true,
                    autoclose: true,
                    todayBtn: "linked"
            });
            
            $('#lotStart').datepicker({
                    format: "yyyy-mm-dd",
                    calendarWeeks: true,
                    autoclose: true,
                    todayBtn: "linked"
            });
            
	}); 
	
	//Los hinzufügen
	$("#addTenderLot").click(function(){
		var losNr = $("#lotNr").val();
		var losName = $("#lotName").val();
		var losDatum = $("#lotStart").val();
                var kostenanschlag = $("#kostenanschlag").val();
                var budget = $("#budget").val();
		var lotSum = $("#lotSum").val();
		var lotVergabe = $("#lotVergabe").val();
		var lotNotice = $("#lotNotice").val();
                var lotAuftragnehmer = $("#lotAuftragnehmer").val();
                var lotLVSend = $("#lotLVSend").val();
                var lotVerfahren = $("#lotVerfahren").val();
                var lotLVBearbeiter = $("#lotLVBearbeiter").val();
                var lotMKFOf = $("#lotMKFOf").val();                
		if( lotMKFOf === "0"){
                    if(losNr !== "" && losName !== "" && losDatum  !== "" && lotLVSend  !== "" && lotVerfahren  !== "" && lotLVBearbeiter  !== ""){
                        $('#addTenderLotModal').modal('hide');
                        $.ajax({
                            url : "addTenderLot.php",
                            data:{"losNr":losNr,"losName":losName,"losDatum":losDatum,"lotSum":lotSum ,"lotVergabe":lotVergabe,"lotNotice":lotNotice,"lotAuftragnehmer":lotAuftragnehmer,"lotLVSend":lotLVSend,"lotVerfahren":lotVerfahren,"lotLVBearbeiter":lotLVBearbeiter,"lotMKFOf":lotMKFOf,"kostenanschlag":kostenanschlag,"budget":budget},
                            type: "GET",	        
                            success: function(data){
                                    alert(data);
                                    window.location.replace("tenderLots.php");			    	 			        
                            }
                        });			    
                    }
                    else{
                            alert("Bitte alle Felder außer der Vergabesumme und Auftragnehmer ausfüllen!");
                    }  
                }
                else{
                    if(losDatum  !== "" && lotLVSend  !== "" && lotVerfahren  !== "" && lotLVBearbeiter  !== ""){
                        $('#addTenderLotModal').modal('hide');
                        $.ajax({
                            url : "addTenderLot.php",
                            data:{"losNr":losNr,"losName":losName,"losDatum":losDatum,"lotSum":lotSum ,"lotVergabe":lotVergabe,"lotNotice":lotNotice,"lotAuftragnehmer":lotAuftragnehmer,"lotLVSend":lotLVSend,"lotVerfahren":lotVerfahren,"lotLVBearbeiter":lotLVBearbeiter,"lotMKFOf":lotMKFOf,"kostenanschlag":kostenanschlag,"budget":budget},
                            type: "GET",	        
                            success: function(data){
                                    alert(data);
                                    window.location.replace("tenderLots.php");			    	 			        
                            }
                        });			    
                    }
                    else{
                            alert("Für MKF bitte alle Felder außer der Vergabesumme und Auftragnehmer ausfüllen!");
                    }  
                }
        });
        
        //Los speichern
	$("#saveTenderLot").click(function(){
		var losNr = $("#lotNr").val();
		var losName = $("#lotName").val();
		var losDatum  = $("#lotStart").val();
                var kostenanschlag = $("#kostenanschlag").val();
                var budget = $("#budget").val();
		var lotSum = $("#lotSum").val();
		var lotVergabe = $("#lotVergabe").val();
		var lotNotice = $("#lotNotice").val();
                var lotAuftragnehmer = $("#lotAuftragnehmer").val();
                var lotLVSend = $("#lotLVSend").val();
                var lotVerfahren = $("#lotVerfahren").val();
                var lotLVBearbeiter = $("#lotLVBearbeiter").val();

                if( $("#lotMKF").prop('checked') === false){
                    
                    if(losNr !== "" && losName !== "" && losDatum  !== "" && lotLVSend  !== "" && lotVerfahren  !== "" && lotLVBearbeiter  !== ""){
                        $('#addTenderLotModal').modal('hide');
                        $.ajax({
                            url : "setTenderLot.php",
                            data:{"lotID":lotID,"losNr":losNr,"losName":losName,"losDatum":losDatum,"lotSum":lotSum ,"lotVergabe":lotVergabe,"lotNotice":lotNotice,"lotAuftragnehmer":lotAuftragnehmer,"lotLVSend":lotLVSend,"lotVerfahren":lotVerfahren,"lotLVBearbeiter":lotLVBearbeiter,"mkf":0,"kostenanschlag":kostenanschlag,"budget":budget },
                            type: "GET",	        
                            success: function(data){
                                    alert(data);
                                    window.location.replace("tenderLots.php");
                            }
                        });	
                    }
                    else{
			alert("Bitte alle Felder außer der Vergabesumme ausfüllen!");
                    }                   
                }
                else{
                    if(losDatum  !== "" && lotLVSend  !== "" && lotVerfahren  !== "" && lotLVBearbeiter  !== ""){
                        $('#addTenderLotModal').modal('hide');
                        $.ajax({
                            url : "setTenderLot.php",
                            data:{"lotID":lotID,"losNr":losNr,"losName":losName,"losDatum":losDatum,"lotSum":lotSum ,"lotVergabe":lotVergabe,"lotNotice":lotNotice,"lotAuftragnehmer":lotAuftragnehmer,"lotLVSend":lotLVSend,"lotVerfahren":lotVerfahren,"lotLVBearbeiter":lotLVBearbeiter,"mkf":1,"kostenanschlag":kostenanschlag,"budget":budget },
                            type: "GET",	        
                            success: function(data){
                                    alert(data);
                                    window.location.replace("tenderLots.php");
                            }
                        });	
                    }
                    else{
			alert("Bitte alle Felder außer der Vergabesumme ausfüllen!");
                    }  
                }                
                
        
    });
        
        $("#addTenderLotModalButton").click(function(){	    

            document.getElementById("lotNr").value = "";
            document.getElementById("lotName").value = "";
            document.getElementById("lotLVSend").value = "";
            document.getElementById("lotStart").value = "";
            document.getElementById("lotVerfahren").value = ""; 
            document.getElementById("lotLVBearbeiter").value = "";
            document.getElementById("lotSum").value = "";
            document.getElementById("lotVergabe").value = "0";
            document.getElementById("lotAuftragnehmer").value = "";
 
            // Buttons ein/ausblenden!
            document.getElementById("saveTenderLot").style.display = "none";
            document.getElementById("addTenderLot").style.display = "inline";
            $('#lotMKF').bootstrapToggle('enable');
        });
        
        $("button[value='changeTenderLot']").click(function(){           
            // Buttons ein/ausblenden!               
            $('#lotMKF').bootstrapToggle('disable');
            document.getElementById("addTenderLot").style.display = "none";
            document.getElementById("saveTenderLot").style.display = "inline";                
            $('#addTenderLotModal').modal('show');            
        });
        
        //MKF Checkbox   
        $('#lotMKF').change(function() {
            var checked = $(this).prop('checked');          
            if(checked === true){ 
                $("#lotMKFOf").prop('disabled', false);
                $("#lotNr").prop('disabled', true);
                $("#lotName").prop('disabled', true);
                $("#lotVerfahren").prop('disabled', true);
                document.getElementById("lotVerfahren").value = "MKF";
            }
            else{ 
                $("#lotMKFOf").prop('disabled', true );
                $("#lotNr").prop('disabled', false);
                $("#lotName").prop('disabled', false);
                $("#lotVerfahren").prop('disabled', false);
                document.getElementById("lotVerfahren").value = "";
                document.getElementById("lotMKFOf").value = 0;
            }        
        });     
        
        // PDF erzeugen
        $('#createTenderListPDF').click(function(){                  
            window.open('/pdf_createTenderLotElementListPDF.php');
        });
        
        $('#createTenderListWithoutElementsPDF').click(function(){                  
            window.open('/pdf_createTenderLotElementListWithoutElementsPDF.php');
        });
        
        $('#createTenderWorkflowPDF').click(function(){                  
            window.open('/pdf_createTenderWorkflowPDF.php');
        });
        
        // Workflow zu Los in Modal laden
        $("button[value='LotWorkflow']").click(function(){
            var ID = this.id;            
            $.ajax({
                url : "getLotWorkflow.php",
                type: "GET",
                data:{"lotID":ID},
                success: function(data){
                    $("#workflowModalBody").html(data);	            			 			
                } 
            });
            
        });                
</script>


</html>
