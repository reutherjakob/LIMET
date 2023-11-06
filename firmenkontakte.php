<?php
session_start();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Firmenkontakte</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/r-2.2.2/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/r-2.2.2/datatables.min.js"></script>
<!--
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
-->

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

<div class="container-fluid">
	<nav class="navbar navbar-expand-lg bg-light navbar-light">	
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
                                    <a class='dropdown-item' href='roombookMeeting.php'>Raumbuch - Meeting</a>
                                    <a class='dropdown-item' href='roombookDetailed.php'>Raumbuch - Detail</a>
                                    <a class='dropdown-item' href='roombookElements.php'>Raumbuch - Räume mit Element</a>
                                    <a class='dropdown-item' href='roombookBO.php'>Raumbuch - Betriebsorganisation</a>
                                    <a class='dropdown-item' href='roombookReports.php'>Raumbuch - Berichte</a>
                                    <a class='dropdown-item' href='elementsInProject.php'>Elemente im Projekt</a>
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
                          <li class='nav-item active'><a class='py-0 nav-link' href='firmenkontakte.php'><i class='fa fa-address-card'></i> Firmenkontakte</a></li>
                        </ul>";
                }
            ?>
              </ul>
          <ul class="navbar-nav ml-auto">
              <li class="py-0 nav-item "><a class="py-0 nav-link text-success disabled" id="projectSelected">Aktuelles Projekt: <?php  if ($_SESSION["projectName"] != ""){echo $_SESSION["projectName"];}else{echo "Kein Projekt ausgewÃ¤hlt!";}?></a></li>
              <li><a class="py-0 nav-link" href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a></li>
          </ul>              
    </nav>	
        <div class="mt-4 card">
                <div class="card-header">Lieferantenkontakte</div>
                <div class="card-body">
                <?php
                        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                        /* change character set to utf8 */
                        if (!$mysqli->set_charset("utf8")) {
                            printf("Error loading character set utf8: %s\n", $mysqli->error);
                            exit();
                        }

                        // Abfrage der Lieferanten-Kontakte
                        $sql="SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_ansprechpersonen.Tel, tabelle_ansprechpersonen.Adresse, tabelle_ansprechpersonen.PLZ, tabelle_ansprechpersonen.Ort, tabelle_ansprechpersonen.Land, tabelle_ansprechpersonen.Mail, tabelle_lieferant.Lieferant, tabelle_abteilung.Abteilung,
                                 tabelle_lieferant.idTABELLE_Lieferant, tabelle_abteilung.idtabelle_abteilung, tabelle_ansprechpersonen.Gebietsbereich
                                FROM tabelle_abteilung INNER JOIN (tabelle_lieferant INNER JOIN tabelle_ansprechpersonen ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_ansprechpersonen.tabelle_lieferant_idTABELLE_Lieferant) ON tabelle_abteilung.idtabelle_abteilung = tabelle_ansprechpersonen.tabelle_abteilung_idtabelle_abteilung;";						
                        $result = $mysqli->query($sql);

                        echo "<table class='table table-striped table-bordered  table-sm' id='tableLieferanten'  cellspacing='0' width='100%'>
                        <thead><tr>
                        <th>ID</th>
                        <th></th>
                        <th>Name</th>
                        <th>Vorname</th>
                        <th>Tel</th>
                        <th>Mail</th>
                        <th>Adresse</th>
                        <th>PLZ</th>
                        <th>Ort</th>
                        <th>Land</th>
                        <th>Lieferant</th>
                        <th>Abteilung</th>
                        <th>Gebiet</th>
                        <th></th>
                        <th></th>
                        <th></th>                                    
                        </tr></thead><tbody>";


                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row["idTABELLE_Ansprechpersonen"]."</td>";
                            echo "<td><button type='button' id='".$row["idTABELLE_Ansprechpersonen"]."' class='btn btn-outline-dark btn-sm' value='addressCard' data-toggle='modal' data-target='#showAddressCard'><i class='far fa-address-card'></i></button></td>";                         
                            echo "<td>".$row["Name"]."</td>";
                            echo "<td>".$row["Vorname"]."</td>";
                            echo "<td>".$row["Tel"]."</td>";
                            echo "<td>".$row["Mail"]."</td>";
                            echo "<td>".$row["Adresse"]."</td>";
                            echo "<td>".$row["PLZ"]."</td>";
                            echo "<td>".$row["Ort"]."</td>";
                            echo "<td>".$row["Land"]."</td>";
                            echo "<td>".$row["Lieferant"]."</td>";
                            echo "<td>".$row["Abteilung"]."</td>";
                            echo "<td>".$row["Gebietsbereich"]."</td>";
                            echo "<td><button type='button' id='".$row["idTABELLE_Ansprechpersonen"]."' class='btn btn-outline-dark btn-xs' value='changeContact' data-toggle='modal' data-target='#addContactModal'><i class='fa fa-pencil-alt'></i></button></td>";                            
                            echo "<td>".$row["idTABELLE_Lieferant"]."</td>";
                            echo "<td>".$row["idtabelle_abteilung"]."</td>";                                        
                            echo "</tr>";

                        }
                        echo "</tbody></table>";	
                ?>                
                <input type='button' id='addContactModalButton' class='btn btn-success btn-sm' value='Lieferantenkontakt hinzufügen' data-toggle='modal' data-target='#addContactModal'></input>
            </div>            
        </div>
    <div class='mt-4 row'>
        <div class='col-sm-6'>
            <div class="mt-4 card">
                <div class="card-header">Lieferanten <label class="float-right"><button type='button' id='addLieferantButton' class='btn btn-outline-dark btn-sm' value='addLieferant' data-toggle='modal' data-target='#changeLieferantModal'>Lieferant hinzufügen <i class='far fa-plus-square'></i></button></label></div>
                <div class="card-body">
                <?php                       
                        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                        /* change character set to utf8 */
                        if (!$mysqli->set_charset("utf8")) {
                            printf("Error loading character set utf8: %s\n", $mysqli->error);
                            exit();
                        }

                        // Abfrage der Lieferanten-                                   
                        $sql = "SELECT tabelle_lieferant.idTABELLE_Lieferant, tabelle_lieferant.Lieferant, tabelle_lieferant.Tel, tabelle_lieferant.Anschrift, tabelle_lieferant.PLZ, tabelle_lieferant.Ort, tabelle_lieferant.Land
                                FROM tabelle_lieferant;";
                        $result = $mysqli->query($sql);

                        echo "<table class='table table-striped table-bordered nowrap table-sm' id='tableLieferantenUnternehmen'  cellspacing='0' width='100%'>
                        <thead><tr>
                        <th>ID</th>
                        <th></th>
                        <th>Lieferant</th>
                        <th>Tel</th>
                        <th>Adresse</th>
                        <th>PLZ</th>
                        <th>Ort</th>
                        <th>Land</th>                                   
                        </tr></thead><tbody>";


                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row["idTABELLE_Lieferant"]."</td>";
                            echo "<td></td>";
                            echo "<td>".$row["Lieferant"]."</td>";
                            echo "<td>".$row["Tel"]."</td>";
                            echo "<td>".$row["Anschrift"]."</td>";
                            echo "<td>".$row["PLZ"]."</td>";
                            echo "<td>".$row["Ort"]."</td>";
                            echo "<td>".$row["Land"]."</td>";                                      
                            echo "</tr>";

                        }
                        echo "</tbody></table>";	
                ?>	                                
                </div>
            </div>
        </div>
        <div class='col-sm-6'>
            <div class="mt-4 card">
                <div class="card-header">Lieferantenumsaetze</div>
                <div class="card-body" id="lieferantenumsaetze">
            </div>
        </div>
    </div>
        
    <!-- Modal zum Anzeigen der Visitenkarte -->
	  <div class='modal fade' id='showAddressCard' role='dialog'>
	    <div class='modal-dialog modal-sm'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Kontaktdaten</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='mbody'>
                    <address class="m-t-md">
                        <strong><label class='control-label' id="cardName"></label></strong><br>
                        <label class='control-label' id="cardLieferant"></label><br>    
                        <label class='control-label' id="cardAddress"></label><br>   
                        <label class='control-label' id="cardPlace"></label><br>  
                        <abbr title="Phone">T: </abbr><label class='control-label' id="cardTel"></label><br>
                        <abbr title="Mail">M: </abbr><label class='control-label' id="cardMail"></label><br>                         
                    </address>
		</div>
	        <div class='modal-footer'>
	        </div>
	      </div>	      
	    </div>
	  </div> 
        
    
     <!-- Modal zum Anlegen eines Firmenkontakts -->
        <div class='modal fade' id='addContactModal' role='dialog'>
          <div class='modal-dialog modal-md'>

            <!-- Modal content-->
            <div class='modal-content'>
              <div class='modal-header'>
                  <h4 class='modal-title'>Lieferantenkontakt hinzufügen</h4>
                  <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
              </div>
              <div class='modal-body' id='mbody'>
                  <form role="form">                        
                      <div class='form-group'>
                          <label for='lieferantenName'>Name</label>
                          <input type='text' class='form-control form-control-sm' id='lieferantenName'></input>					  			 											 						 			
                      </div>		  			 		
                      <div class='form-group'>
                          <label for='lieferantenVorname'>Vorname</label>
                          <input type='text' class='form-control form-control-sm' id='lieferantenVorname'></input>	
                      </div>
                          <div class='form-group'>
                                          <label class='control-label' for='lieferantenTel'>Tel</label>
                                          <input type='text' class='form-control form-control-sm' id='lieferantenTel'></input>
                          </div>
                          <div class='form-group'>
                                          <label class='control-label' for='lieferantenAdresse'>Adresse</label>
                                           <input type='text' class='form-control form-control-sm' id='lieferantenAdresse'></input>	
                          </div>
                          <div class='form-group'>
                                          <label class='control-label' for='lieferantenPLZ'>PLZ</label>
                                          <input type='text' class='form-control form-control-sm' id='lieferantenPLZ'></input>	
                          </div>
                          <div class='form-group'>
                                          <label class='control-label' for='lieferantenOrt'>Ort</label>
                                          <input type='text' class='form-control form-control-sm' id='lieferantenOrt'></input>	
                          </div>
                          <div class='form-group'>
                                          <label class='control-label' for='lieferantenLand'>Land</label>
                                          <input type='text' class='form-control form-control-sm' id='lieferantenLand'></input>	
                          </div>
                          <div class='form-group'>
                                          <label class='control-label' for='lieferantenEmail'>Email</label>
                                          <input type='text' class='form-control form-control-sm' id='lieferantenEmail'></input>
                          </div>
                          <?php 
                              $sql = "SELECT `tabelle_lieferant`.`idTABELLE_Lieferant`,
                                           `tabelle_lieferant`.`Lieferant`
                                       FROM `LIMET_RB`.`tabelle_lieferant` ORDER BY Lieferant;"; 
                               $result = $mysqli->query($sql);

                              echo "<div class='form-group'>
                                              <label class='control-label' for='lieferant'>Lieferant</label>
                                                      <select class='form-control form-control-sm' id='lieferant'>";
                                                              while($row = $result->fetch_assoc()) {
                                                                      echo "<option value=".$row["idTABELLE_Lieferant"].">".$row["Lieferant"]."</option>";		
                                                              }	
                                                      echo "</select>	
                              </div>";                                                        

                          $sql = "SELECT `tabelle_abteilung`.`idtabelle_abteilung`,
                                       `tabelle_abteilung`.`Abteilung`
                                   FROM `LIMET_RB`.`tabelle_abteilung` ORDER BY Abteilung;";
                          $result = $mysqli->query($sql);

                          echo "<div class='form-group'>
                              <label class='control-label' for='abteilung'>Abteilung</label>
                                      <select class='form-control form-control-sm' id='abteilung'>";
                                              while($row = $result->fetch_assoc()) {
                                                      echo "<option value=".$row["idtabelle_abteilung"].">".$row["Abteilung"]."</option>";		
                                              }
                                      echo "</select>
                                  </div>";
                              $mysqli ->close(); 
                          ?>
                          <div class='form-group'>
                              <label class='control-label' for='lieferantenGebiet'>Gebiet</label>
                              <input type='text' class='form-control form-control-sm' id='lieferantenGebiet'></input>
                          </div>
                  </form>
              </div>
              <div class='modal-footer'>
                  <input type='button' id='addLieferantenKontakt' class='btn btn-success btn-sm' value='Hinzufügen'></input>
                  <input type='button' id='saveLieferantenKontakt' class='btn btn-warning btn-sm' value='Speichern'></input>
                  <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
              </div>
            </div>	      
          </div>
        </div>
     
     <!-- Modal zum Anzeigen der Visitenkarte -->
	  <div class='modal fade' id='showAddressCard' role='dialog'>
	    <div class='modal-dialog modal-sm'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Kontaktdaten</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='mbody'>
                    <address class="m-t-md">
                        <strong><label class='control-label' id="cardName"></label></strong><br>
                        <label class='control-label' id="cardLieferant"></label><br>    
                        <label class='control-label' id="cardAddress"></label><br>   
                        <label class='control-label' id="cardPlace"></label><br>  
                        <abbr title="Phone">T: </abbr><label class='control-label' id="cardTel"></label><br>
                        <abbr title="Mail">M: </abbr><label class='control-label' id="cardMail"></label><br>                         
                    </address>
		</div>
	        <div class='modal-footer'>
	        </div>
	      </div>	      
	    </div>
	  </div> 
        
    
     <!-- Modal zum Anlegen eines Lieferante--------------------------------------- -->
        <div class='modal fade' id='changeLieferantModal' role='dialog'>
          <div class='modal-dialog modal-md'>
            <!-- Modal content-->
            <div class='modal-content'>
              <div class='modal-header'>
                  <h4 class='modal-title'>Lieferant</h4>
                  <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
              </div>
              <div class='modal-body' id='mbody'>
                  <form role="form">                        
                        <div class='form-group'>
                            <label for='firma'>Lieferant</label>
                            <input type='text' class='form-control form-control-sm' id='firma'></input>					  			 											 						 			
                        </div>		  			 		
                        <div class='form-group'>
                            <label class='control-label' for='lieferantTel'>Tel</label>
                            <input type='text' class='form-control form-control-sm' id='lieferantTel'></input>
                        </div>
                        <div class='form-group'>
                            <label class='control-label' for='lieferantAdresse'>Adresse</label>
                             <input type='text' class='form-control form-control-sm' id='lieferantAdresse'></input>	
                        </div>
                        <div class='form-group'>
                            <label class='control-label' for='lieferantPLZ'>PLZ</label>
                            <input type='text' class='form-control form-control-sm' id='lieferantPLZ'></input>	
                        </div>
                        <div class='form-group'>
                            <label class='control-label' for='lieferantOrt'>Ort</label>
                            <input type='text' class='form-control form-control-sm' id='lieferantOrt'></input>	
                        </div>
                        <div class='form-group'>
                            <label class='control-label' for='lieferantLand'>Land</label>
                            <input type='text' class='form-control form-control-sm' id='lieferantLand'></input>	
                        </div>
                  </form>
              </div>
              <div class='modal-footer'>
                  <input type='button' id='addLieferant' class='btn btn-success btn-sm' value='Hinzufügen'></input>                  
                  <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
              </div>
            </div>	      
          </div>
        </div>
     <!--------------------------------------------------------------------- -->
     
    
</div>
</body>
<script>
        var ansprechID;
	// Tabelle formatieren
	$(document).ready(function(){
                $('#tableLieferanten').DataTable( {
			"columnDefs": [
                            {
                                "targets": [ 0,14,15 ],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        "select": true,
                        "paging": true,                        
                        "searching": true,
                        "info": true,
                        "order": [[ 2, "asc" ]],
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                        dom: 'Bfrtip',
                    buttons: [
                        'copy'
                    ]
                    
                } );
                
                $('#tableLieferantenUnternehmen').DataTable( {
			"columnDefs": [
                        {
                            "targets": [ 0 ],
                            "visible": false,
                            "searchable": false
                        },
                        {
		            className: 'control',
		            orderable: false,
		            targets:   1
	        	}
                    ],
                    "select": true,
                    "paging": true,
                    "searching": true,
                    "info": true,
                    "order": [[ 2, "asc" ]],
                    "pagingType": "simple",
                    "lengthChange": false,
                    "pageLength": 10,
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    responsive: {
                        details: {
                            type: 'column',
                            target: 1
                        }
                    }
                } );
                
                var table1 = $('#tableLieferanten').DataTable();
                var table2 = $('#tableLieferantenUnternehmen').DataTable();
 
                $('#tableLieferanten tbody').on( 'click', 'tr', function () {
                    if ( $(this).hasClass('info') ) {

                    }
                    else {
                        table1.$('tr.info').removeClass('info');
                        $(this).addClass('info');	
                        ansprechID = table1.row( $(this) ).data()[0];                        
                        document.getElementById("lieferantenName").value = table1.row( $(this) ).data()[2];
                        document.getElementById("lieferantenVorname").value = table1.row( $(this) ).data()[3];
                        document.getElementById("lieferantenTel").value = table1.row( $(this) ).data()[4];                                    
                        document.getElementById("lieferantenAdresse").value = table1.row( $(this) ).data()[6];
                        document.getElementById("lieferantenPLZ").value = table1.row( $(this) ).data()[7];
                        document.getElementById("lieferantenOrt").value = table1.row( $(this) ).data()[8];
                        document.getElementById("lieferantenLand").value = table1.row( $(this) ).data()[9];        
                        document.getElementById("lieferantenEmail").value = table1.row( $(this) ).data()[5];  
                        document.getElementById("lieferant").value = table1.row( $(this) ).data()[14]; 
                        document.getElementById("abteilung").value = table1.row( $(this) ).data()[15]; 
                        document.getElementById("lieferantenGebiet").value = table1.row( $(this) ).data()[12]; 
                        
                        //  Setzen der Visitenkarteninformation
                        document.getElementById("cardName").innerHTML = table1.row( $(this) ).data()[2] + " " + table1.row( $(this) ).data()[3];
                        document.getElementById("cardLieferant").innerHTML = table1.row( $(this) ).data()[10];
                        document.getElementById("cardTel").innerHTML = table1.row( $(this) ).data()[4];
                        document.getElementById("cardMail").innerHTML = table1.row( $(this) ).data()[5];
                        document.getElementById("cardAddress").innerHTML = table1.row( $(this) ).data()[6];
                        document.getElementById("cardPlace").innerHTML = table1.row( $(this) ).data()[7] + ", "+table1.row( $(this) ).data()[8];                                                
                    }
                });  
                
                $('#tableLieferantenUnternehmen tbody').on( 'click', 'tr', function () {
                    if ( $(this).hasClass('info') ) {

                    }
                    else {
                        table2.$('tr.info').removeClass('info');
                        $(this).addClass('info');
                        $.ajax({
                            url : "getLieferantenUmsaetze.php",
                            data:{"lieferantenID":table2.row( $(this) ).data()[0]},
                            type: "GET",
                            success: function(data){
                                $("#lieferantenumsaetze").html(data); 

                            } 
                        });
                    }
                });  
	});
	
        
        $("#addLieferantenKontakt").click(function(){	    
		 var Name= $("#lieferantenName").val();
		 var Vorname= $("#lieferantenVorname").val();
		 var Tel= $("#lieferantenTel").val();
		 var Adresse= $("#lieferantenAdresse").val();
		 var PLZ= $("#lieferantenPLZ").val();
		 var Ort= $("#lieferantenOrt").val();
		 var Land=  $("#lieferantenLand").val();
		 var Email=  $("#lieferantenEmail").val();
		 var lieferant=  $("#lieferant").val();
		 var abteilung =  $("#abteilung").val();
                 var gebiet =  $("#lieferantenGebiet").val();
	 	
	 	 if(Name.length > 0 && Vorname.length > 0 && Tel.length > 0){
                     $('#addContactModal').modal('hide');
                    $.ajax({
		        url : "addLieferant.php",
		        data:{"Name":Name,"Vorname":Vorname,"Tel":Tel,"Adresse":Adresse,"PLZ":PLZ,"Ort":Ort,"Land":Land,"Email":Email,"lieferant":lieferant,"abteilung":abteilung,"gebiet":gebiet},
		        type: "GET",
		        success: function(data){
                            alert(data);                                
                            $.ajax({
                                url : "getLieferantenPersonen.php",
                                data:{"ansprechID":ansprechID,"Name":Name,"Vorname":Vorname,"Tel":Tel,"Adresse":Adresse,"PLZ":PLZ,"Ort":Ort,"Land":Land,"Email":Email,"lieferant":lieferant,"abteilung":abteilung,"gebiet":gebiet},
                                type: "GET",
                                success: function(data){
                                    $("#lieferanten").html(data); 
                                    
                                } 
                            });
                            
		        } 
		    });		    		   
	 	 }
	 	 else{
                    alert("Bitte überprüfen Sie Ihre Angaben! Name, Vorname und Tel ist Pflicht!");
	 	 }	 
        });
        
        $("#saveLieferantenKontakt").click(function(){	    
		 var Name= $("#lieferantenName").val();
		 var Vorname= $("#lieferantenVorname").val();
		 var Tel= $("#lieferantenTel").val();
		 var Adresse= $("#lieferantenAdresse").val();
		 var PLZ= $("#lieferantenPLZ").val();
		 var Ort= $("#lieferantenOrt").val();
		 var Land=  $("#lieferantenLand").val();
		 var Email=  $("#lieferantenEmail").val();
		 var lieferant=  $("#lieferant").val();
		 var abteilung =  $("#abteilung").val();
                 var gebiet =  $("#lieferantenGebiet").val();
	 	
	 	 if(Name.length > 0 && Vorname.length > 0 && Tel.length > 0){
                    $('#addContactModal').modal('hide');
                    $.ajax({
		        url : "saveLieferantenKontakt.php",
		        data:{"ansprechID":ansprechID,"Name":Name,"Vorname":Vorname,"Tel":Tel,"Adresse":Adresse,"PLZ":PLZ,"Ort":Ort,"Land":Land,"Email":Email,"lieferant":lieferant,"abteilung":abteilung,"gebiet":gebiet},
		        type: "GET",
		        success: function(data){
                            alert(data);                                
                            $.ajax({
                                url : "getLieferantenPersonen.php",
                                type: "GET",
                                success: function(data){
                                    $("#lieferanten").html(data); 
                                    
                                } 
                            });
                            
		        } 
		    });		    		   
	 	 }
	 	 else{
                    alert("Bitte überprüfen Sie Ihre Angaben! Name, Vorname und Tel ist Pflicht!");
	 	 }	 
        });
        
        $("#addContactModalButton").click(function(){	    
            document.getElementById("lieferantenName").value = "";
            document.getElementById("lieferantenVorname").value = "";
            document.getElementById("lieferantenTel").value = "";
            document.getElementById("lieferantenAdresse").value = "";
            document.getElementById("lieferantenPLZ").value = "";
            document.getElementById("lieferantenOrt").value = "";
            document.getElementById("lieferantenLand").value = "";        
            document.getElementById("lieferantenEmail").value = "";  
            document.getElementById("lieferantenGebiet").value = ""; 
            // Buttons ein/ausblenden!
            document.getElementById("saveLieferantenKontakt").style.display = "none";
            document.getElementById("addLieferantenKontakt").style.display = "inline";
        });
        
        $("button[value='changeContact']").click(function(){	    
            // Buttons ein/ausblenden!
            document.getElementById("addLieferantenKontakt").style.display = "none";
            document.getElementById("saveLieferantenKontakt").style.display = "inline";
        });
        
        function showAdressCard() {
            alert("I am an alert box!");
        }
        
        $("#addLieferant").click(function(){	    
		 var firma = $("#firma").val();
		 var lieferantTel = $("#lieferantTel").val();
		 var lieferantAdresse = $("#lieferantAdresse").val();
		 var lieferantPLZ = $("#lieferantPLZ").val();
		 var lieferantOrt = $("#lieferantOrt").val();
		 var lieferantLand =  $("#lieferantLand").val();
	 	
	 	 if(firma !== "" && lieferantTel !== "" && lieferantAdresse !== "" && lieferantPLZ !== "" && lieferantOrt !== "" && lieferantLand !== ""){
                     $('#changeLieferantModal').modal('hide');
                    $.ajax({
		        url : "addFirma.php",
		        data:{"firma":firma,"lieferantTel":lieferantTel,"lieferantAdresse":lieferantAdresse,"lieferantPLZ":lieferantPLZ,"lieferantOrt":lieferantOrt,"lieferantLand":lieferantLand},
		        type: "GET",
		        success: function(data){
                            alert(data);
                            // Neu Laden der Seite
                            location.reload();                              
		        } 
		    });		    		   
	 	 }
	 	 else{
                    alert("Bitte alle Felder ausfüllen!");
	 	 }	 
        });
        
        
    
</script>

</html>
