<?php
    session_start();
    $_SESSION["dbAdmin"]="0";
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

<!--Bootstrap Toggle -->
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

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
                                    <a class='dropdown-item' href='roombookElements.php'>Raumbuch - Räume mit Element</a>
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
              <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fas fa-tasks'></i> Ausschreibungen</a>
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
                    <a class="dropdown-item active" href="roombookAbrechnung.php"><i class='fas fa-euro-sign'></i> Abrechnung</a>
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
    <div class='row mt-4 '>
        <div class='col-sm-5'>  
            <div class="card">
                <div class="card-header"><h4>Gewerke</h4>
                </div>
                <div class="card-body">
                    <?php
                        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                        /* change character set to utf8 */
                        if (!$mysqli->set_charset("utf8")) {
                            printf("Error loading character set utf8: %s\n", $mysqli->error);
                            exit();
                        }
                        
                        $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lieferant.Lieferant, tabelle_lose_extern.Schlussgerechnet, tabelle_lose_extern.Vergabesumme, Sum(tabelle_rechnungen.Rechnungssumme) AS SummevonRechnungssumme
                                FROM tabelle_rechnungen RIGHT JOIN (tabelle_lose_extern LEFT JOIN tabelle_lieferant ON tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant = tabelle_lieferant.idTABELLE_Lieferant) ON tabelle_rechnungen.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                                WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                GROUP BY tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lieferant.Lieferant, tabelle_lose_extern.Schlussgerechnet, tabelle_lose_extern.Vergabesumme
                                ORDER BY tabelle_lose_extern.LosNr_Extern;";
                        
                        $result = $mysqli->query($sql);

                        echo "<table class='table table-striped table-bordered table-sm' id='tableGewerke'  cellspacing='0' width='100%'>
                        <thead><tr>
                        <th>ID</th>
                        <th>Nummer</th>
                        <th>Gewerk</th>
                        <th>Lieferant</th>
                        <th>Schlussgerechnet</th>
                        <th>Vergabesumme</th>   
                        <th>Abgerechnet</th>  
                        <th>Schlussgerechnet</th>
                        </tr></thead><tbody>";

                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row["idtabelle_Lose_Extern"]."</td>";
                            echo "<td>".$row["LosNr_Extern"]."</td>";
                            echo "<td>".$row["LosBezeichnung_Extern"]."</td>";
                            echo "<td>".$row["Lieferant"]."</td>";
                            echo "<td style='text-align:center'>";
                                if($row["Schlussgerechnet"] === '0'){
                                    echo "<span class='badge badge-pill badge-light'> Nein </span>";
                                }
                                else{
                                    echo "<span class='badge badge-pill badge-success'> Ja </span>";
                                }
                            echo "</td>";
                            echo "<td style='text-align:right'>".number_format($row["Vergabesumme"], 2, ',', ' ')."</td>";
                            echo "<td style='text-align:right'>".number_format($row["SummevonRechnungssumme"], 2, ',', ' ')."</td>";
                            echo "<td>".$row["Schlussgerechnet"]."</td>";
                            echo "</tr>";

                        }
                        echo "</tbody></table>";
                ?>
                </div>
            </div>
        </div>
        <div class='col-sm-5'>  
            <div class="card">
                <div class="card-header"><h4>Rechnungen</h4>                     
                </div>
                <div class="card-body" id="rechnungen"></div>
                <div class="card-footer">
                        <label for='lotSchlussrechnung'>Schlussgerechnet:</label>
                        <input id="lotSchlussrechnung" type="checkbox" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="secondary" data-on="Ja" data-off="Nein" disabled></input>  
                        <input type='button' id='saveLot' class='btn btn-outline-dark btn-sm' value='Speichern'></input>
                </div>
            </div>
        </div>
        <div class='col-sm-2'>  
            <div class="card">
                <div class="card-header"><h4>Rechnungsdetails</h4></div>
                <div class="card-body">
                    <form role='form'>            
                        <div class='form-group'>
                            <input type='hidden' id='rechnungID'/>
                        </div> 
                        <div class='form-group'>
                            <label for='teilRechnungNr'>Teilrechnungsnummer:</label>
                            <input type='text' class='form-control form-control-sm' id='teilRechnungNr' placeholder='Teilrechnungsnummer'/>
                        </div>  
                        <div class='form-group'>
                            <label for='rechnungNr'>Rechnungsnummer:</label>
                            <input type='text' class='form-control form-control-sm' id='rechnungNr' placeholder='Rechnungsnummer'/>
                        </div>  
                        <div class='form-group'>
                            <label for='rechnungAusstellungsdatum'>Ausstellungsdatum:</label>
                            <input type='text' class='form-control form-control-sm' id='rechnungAusstellungsdatum' placeholder='jjjj-mm-tt'/>
                        </div>
                        <div class='form-group'>
                            <label for='rechnungEingangsdatum'>Eingangsdatum:</label>
                            <input type='text' class='form-control form-control-sm' id='rechnungEingangsdatum' placeholder='jjjj-mm-tt'/>
                        </div>
                        <div class='form-group'>
                            <label for='rechnungSum'>Rechnungssumme: (.)</label>
                            <input type='text'  class='form-control form-control-sm' id='rechnungSum' placeholder='0.0'/>
                        </div>
                        <div class='form-group'>
                            <label for='rechnungBearbeiter'>Bearbeiter:</label>
                            <input type='text' class='form-control form-control-sm' id='rechnungBearbeiter' placeholder='Bearbeiter'/>
                        </div> 
                        <div class='form-group'>
                            <label for='rechnungSchlussrechnung'>Schlussgerechnet:</label>
                            <input id="rechnungSchlussrechnung" type="checkbox" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="secondary" data-on="Ja" data-off="Nein"></input>  
                        </div>                         
                        <div class='form-group'>
                            <input type='button' id='saveRechnung' class='btn btn-outline-dark btn-sm' value='Speichern'></input>
                            <input type='button' id='addRechnung' class='btn btn-outline-dark btn-sm' value='Hinzufügen'></input>                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>     
        <!-- Modal für PDF-Upload -->
    <div class='modal fade' id='uploadRechnungModal' role='dialog'>
      <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
          <div class='modal-header'>	          
            <h4 class='modal-title'>Rechnung uploaden</h4>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
          </div>
          <div class='modal-body' id='mbody'>
            <form role='form' id="uploadForm" enctype="multipart/form-data">   
                <div class='form-group'>
                    <input type='hidden' id='rechnungIDFile'/>
                </div>
                <div class='form-group'>
                    <label for='fileUpload'>Filename:</label>
                    <input type="file" name="fileUpload" id="uploadFile"> <br>
                </div>                         
            </form>              
          </div>
          <div class='modal-footer'>
            <input type='button' id='uploadRechnungButton' class='btn btn-outline-dark btn-sm' value='Upload' data-dismiss='modal'></input> 
          </div>
        </div>
      </div>
    </div>  
<script>   
        var id = 0;
	// Tabellen formatieren
	$(document).ready(function(){	                        
            $('#tableGewerke').DataTable( {
			"select":true,
                        "paging": true,
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 20,
			"columnDefs": [
                            {
                                "targets": [ 0,7 ],
                                "visible": false,
                                "searchable": false
                            }
                        ],
			"order": [[ 1, "asc" ]],
                        "orderMulti": true,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                        "mark":true
	    } );
            
            $('#rechnungAusstellungsdatum').datepicker({
                    format: "yyyy-mm-dd",
                    calendarWeeks: true,
                    autoclose: true,
                    todayBtn: "linked"
            });
            
            $('#rechnungEingangsdatum').datepicker({
                    format: "yyyy-mm-dd",
                    calendarWeeks: true,
                    autoclose: true,
                    todayBtn: "linked"
            });
            
            var table = $('#tableGewerke').DataTable();
            $('#tableGewerke tbody').on( 'click', 'tr', function () {
			
	        if ( $(this).hasClass('info') ) {
	        }
	        else {                   
	            id = table.row( $(this) ).data()[0];
                    
                    // Schlusgerechnet Toggle setzen
                    if(table.row( $(this) ).data()[7] === '0'){
                        $('#lotSchlussrechnung').bootstrapToggle('enable');
                        $('#lotSchlussrechnung').bootstrapToggle('off'); 
                    }
                    else{
                        $('#lotSchlussrechnung').bootstrapToggle('enable');
                        $('#lotSchlussrechnung').bootstrapToggle('on');
                    }
                    
                    $.ajax({
                        url : "getRechnungenToLot.php",
                        data:{"lotID":id},
                        type: "GET",
                        success: function(data){
                            $("#rechnungen").html(data);                            	
                        }
                    });	

	        }
	    } );
	});
        
        $("#saveRechnung").click(function(){
            if(id === 0){
                alert("Kein Los ausgewählt!");
            }
            else{                          
                var rechnungID = $("#rechnungID").val();
                var teilRechnungNr = $("#teilRechnungNr").val();
                var rechnungNr = $("#rechnungNr").val();
                var rechnungAusstellungsdatum = $("#rechnungAusstellungsdatum").val();
                var rechnungEingangsdatum = $("#rechnungEingangsdatum").val();
                var rechnungSum = $("#rechnungSum").val();
                var rechnungBearbeiter = $("#rechnungBearbeiter").val();                

                if($("#rechnungSchlussrechnung").prop('checked') === false){
                    var rechnungSchlussrechnung = 0;
                }
                else{
                    var rechnungSchlussrechnung = 1;
                }
                
                $.ajax({
                    url : "saveRechnung.php",
                    type: "GET",
                    data:{"rechnungID":rechnungID, "rechnungNr":rechnungNr, "teilRechnungNr":teilRechnungNr, "rechnungAusstellungsdatum":rechnungAusstellungsdatum,"rechnungEingangsdatum":rechnungEingangsdatum, "rechnungSum":rechnungSum, "rechnungBearbeiter":rechnungBearbeiter, "rechnungSchlussrechnung":rechnungSchlussrechnung},
                    success: function(data){
                        alert(data);
                        $.ajax({
                            url : "getRechnungenToLot.php",
                            data:{"lotID":id},
                            type: "GET",
                            success: function(data){
                                $("#rechnungen").html(data);                            	
                            }
                        });
                    }
                });
            }
        });
        
        $("#addRechnung").click(function(){
            if(id === 0){
                alert("Kein Los ausgewählt!");
            }
            else{                          
                var rechnungID = $("#rechnungID").val();
                var teilRechnungNr = $("#teilRechnungNr").val();
                var rechnungNr = $("#rechnungNr").val();
                var rechnungAusstellungsdatum = $("#rechnungAusstellungsdatum").val();
                var rechnungEingangsdatum = $("#rechnungEingangsdatum").val();
                var rechnungSum = $("#rechnungSum").val();
                var rechnungBearbeiter = $("#rechnungBearbeiter").val();                

                if($("#rechnungSchlussrechnung").prop('checked') === false){
                    var rechnungSchlussrechnung = 0;
                }
                else{
                    var rechnungSchlussrechnung = 1;
                }
                
                $.ajax({
                    url : "addRechnung.php",
                    type: "GET",
                    data:{"lotID":id, "rechnungID":rechnungID, "teilRechnungNr":teilRechnungNr, "rechnungNr":rechnungNr, "rechnungAusstellungsdatum":rechnungAusstellungsdatum,"rechnungEingangsdatum":rechnungEingangsdatum, "rechnungSum":rechnungSum, "rechnungBearbeiter":rechnungBearbeiter, "rechnungSchlussrechnung":rechnungSchlussrechnung},
                    success: function(data){
                        alert(data);
                        $.ajax({
                            url : "getRechnungenToLot.php",
                            data:{"lotID":id},
                            type: "GET",
                            success: function(data){
                                $("#rechnungen").html(data);                            	
                            }
                        });
                    }
                });
            }
        });
        
        //Schlussrechnungsstatus updaten
        $('#saveLot').click(function() {
            if($("#lotSchlussrechnung").prop('checked') === false){
                var schlussgerechnet = 0;
            }
            else{
                var schlussgerechnet = 1; 
            }
            $.ajax({
                url : "saveLosSchlussrechnungsStatus.php",
                type: "GET",
                data:{"schlussgerechnet":schlussgerechnet,"lotID":id},
                success: function(data){
                    alert(data);
                    window.location.replace("roombookAbrechnung.php");
                }
            });
        });  
        
        $("#uploadRechnungButton").click(function(){
            var files = document.getElementById("uploadFile").files;
            
            if(files.length > 0){
                var formData = new FormData();
                formData.append("fileUpload", files[0]);
                formData.append("rechnungIDFile",$("#rechnungIDFile").val());

                var xhttp = new XMLHttpRequest();

                // Set POST method and ajax file path
                xhttp.open("POST", "uploadFileRechnung.php", true);

                // call on request changes state
                xhttp.onreadystatechange = function() {
                   if (this.readyState == 4 && this.status == 200) {
                       alert(this.responseText);
                   }
                };
                // Send request with data
                xhttp.send(formData);
            }
            else{
                alert("Datei auswählen.");
            }            
        });

</script>

</body>

</html>
