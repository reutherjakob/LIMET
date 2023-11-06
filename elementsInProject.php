<?php
session_start();
$_SESSION["dbAdmin"]="0";
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Elemente im Projekt</title>
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
	<nav class="navbar navbar-expand-lg bg-light navbar-light">	
      <a class="py-0 navbar-brand" href="#"><img src="LIMET_logo.png" alt="LIMETLOGO" height="40"/></a>
          <ul class="navbar-nav">
              <?php 
              if($_SESSION["ext"]==0){
                  echo "<ul class='navbar-nav'>
                        <li class='nav-item'><a class='py-0 nav-link' href='firmenkontakte.php'><i class='fa fa-tachometer-alt'></i> Dashboard</a></li>
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
                                    <a class='dropdown-item active' href='elementsInProject.php'>Elemente im Projekt</a>
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
                    <a class="dropdown-item" href="tenderLots.php">Vergabekalender</a>
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
    <div class="mt-4 card">
                <div class="card-header">Elemente im Projekt</div>
                <div class="card-body">
                    <button type='button' class='btn btn-outline-dark btn-sm' id='createElementListPDF'><i class='far fa-file-pdf'></i> Elementliste-PDF</button>
                    <button type='button' class='btn btn-outline-dark btn-sm' id='createElementListWithPricePDF'><i class='far fa-file-pdf'></i> Elementliste inkl. Preis - PDF</button>
                    <button type='button' class='btn btn-outline-dark btn-sm' id='createElementEinbringwegePDF'><i class='far fa-file-pdf'></i> Einbringwege - PDF</button>
				  	<?php
								$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	
								
								/* change character set to utf8 */
								if (!$mysqli->set_charset("utf8")) {
								    printf("Error loading character set utf8: %s\n", $mysqli->error);
								    exit();
								}
								
								
								$sql= "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Intern_idtabelle_Lose_Intern, tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG
										FROM tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_elemente INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)) ON tabelle_elemente.idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
										WHERE (((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
										GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
										ORDER BY tabelle_elemente.ElementID;";

								
								$result = $mysqli->query($sql);
														
								echo "<table class='table table-striped table-bordered table-sm' id='tableElementsInProject'  cellspacing='0' width='100%'>
									<thead><tr>
										<th>ID</th>
										<th>Anzahl</th>
										<th>ID</th>
										<th>Element</th>
										<th>Variante</th>
										<th>VariantenID</th>
										<th>Bestand</th>
										<th>Kosten</th>
										<th>Gewerk</th>
										<th>GHG</th>
										<th>GUG</th>
									</tr>
									</thead>
									<tbody>";
									//setlocale(LC_MONETARY,"de_DE");
									while($row = $result->fetch_assoc()) {
									    echo "<tr>";
									    echo "<td>".$row["TABELLE_Elemente_idTABELLE_Elemente"]."</td>";
									    echo "<td>".$row["SummevonAnzahl"]."</td>";
									    echo "<td>".$row["ElementID"]."</td>";
									    echo "<td>".$row["Bezeichnung"]."</td>";
									    echo "<td>".$row["Variante"]."</td>";
									    echo "<td>".$row["idtabelle_Varianten"]."</td>";
									    if($row["Neu/Bestand"] == 1){
						    				echo"<td>Nein</td>";
						    			}
						    			else{
						    				echo"<td>Ja</td>";
						    			}
									    
									    echo "<td>".money_format("%i", $row["Kosten"])."</td>";	
									    echo "<td>".$row["Gewerke_Nr"]."</td>";	
									    echo "<td>".$row["GHG"]."</td>";	
									    echo "<td>".$row["GUG"]."</td>";	
									    echo "</tr>";
									    						    
									}
									echo "</tbody></table>";
									$mysqli ->close();

							?>	
		</div>
	</div>
        <div class="mt-1 card">
            <div class="card-header"><button type="button" class="btn btn-outline-dark btn-xs" id="showElementVariante"><i class="fas fa-caret-right"></i></button><label>Elementvarianten</label></div>
            <div class="card-body" id="elementInfo" style="display:none">
                <div class="mt-1 row" id="elementGewerk"></div>
                <div class="mt-1 row" id="elementVarianten"></div>
            </div>
        </div>
	<div class="mt-1 card">
  		<div class="card-header"><button type="button" class="btn btn-outline-dark btn-xs" id="showDBData"><i class="fas fa-caret-right"></i></button><label>Datenbank-Vergleichsdaten</label></div>
  		<div class="card-body" style="display:none" id="dbData">
  			<div class="row mt-4">
				<div class='col-md-4'>
					<div class='mt-1 card'>
				  		<div class='card-header'><label>DB-Elementparameter</label></div>
				  		<div class='card-body' id='elementDBParameter'></div>				 
				 	</div>
				 </div>
				 <div class="col-md-4">
                                        <div class="mt-1 card">
                                                <div class="card-header"><label>Elementkosten in anderen Projekten</label></div>
                                                <div class="card-body" id="elementPricesInOtherProjects"></div>
                                        </div>
				</div>
			</div>
			<div class="row">
				 <div class='col-md-4'>
					<div class='mt-1 card'>
				  		<div class='card-header'><label>Geräte zu Element</label></div>
				  		<div class='card-body' id='devicesToElement'></div>				 
				 	</div>
				 </div>
				 <div class='col-md-4'>
					<div class='mt-1 card'>
				  		<div class='card-header'><label>Geräteparameter</label></div>
				  		<div class='card-body' id='deviceParametersInDB'></div>				 
				 	</div>
				 </div>
				 <div class="col-md-4">
                                        <div class="mt-1 card">
                                                <div class="card-header"><label>Gerätepreise</label></div>
                                                <div class="card-body" id="devicePrices"></div>
                                        </div>
				</div>
			</div>
		</div>
	</div>
	<!-- Räume mit Element -->
        <div class="mt-1 card">
            <div class="card-header"><button type="button" class="btn btn-outline-dark btn-xs" id="showRoomsWithAndWithoutElement"><i class="fas fa-caret-right"></i></button><label>Räume mit Element</label></div>
            <div class="card-body" id="roomsWithAndWithoutElements" style="display:none"></div>
        </div>
        
        
</div>


<script>		
	
	$(document).ready(function() {
		$('#tableElementsInProject').DataTable( {
                    "paging": true,
                    "select": true,
                    "pagingType": "simple",
                    "lengthChange": false,
                    "pageLength": 10,
                    "order": [[ 2, "asc" ]],
                    "columnDefs": [
                        {
                            "targets": [ 0 ],
                            "visible": false,
                            "searchable": false
                        },
                         {
                            "targets": [ 5 ],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},               
                    "stateSave": true,
                    dom: 'Blfrtip',
                    "buttons": [
                        'excel'
                    ],
                    "mark":true
	    } );
	    
	    var table = $('#tableElementsInProject').DataTable();
 
	    $('#tableElementsInProject tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            var elementID = table.row( $(this) ).data()[0];	
	            var variantenID = table.row( $(this) ).data()[5];	
                    var bestand = 1;
                    if(table.row( $(this) ).data()[6]==="Ja"){
                        bestand = 0;
                    }
				$.ajax({
			        url : "getRoomsWithElement1.php",
			        data:{"elementID":elementID,"variantenID":variantenID,"bestand":bestand},
			        type: "GET",
			        success: function(data){
			            $("#roomsWithAndWithoutElements").html(data);
                                        $.ajax({
                                                    url : "getElementVariante.php",
                                                    data:{"elementID":elementID,"variantenID":variantenID},
                                                    type: "GET",
                                                    success: function(data){
                                                        $("#elementVarianten").html(data);
                                                        $.ajax({
                                                                    url : "getStandardElementParameters.php",
                                                                    data:{"elementID":elementID},
                                                                    type: "GET",
                                                                    success: function(data){
                                                                        $("#elementDBParameter").html(data);
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
                                                                                                        $("#devicesToElement").html(data);
                                                                                                        $.ajax({
                                                                                                                    url : "getElementGewerke.php",
                                                                                                                    data:{"elementID":elementID},
                                                                                                                    type: "GET",
                                                                                                                    success: function(data){
                                                                                                                        $("#elementGewerk").html(data);
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
	} );
	
		
	// ElementVariantenPanel einblenden
	$("#showElementVariante").click(function() {
	  if($("#elementInfo").is(':hidden')){
	    $(this).html("<i class='fas fa-caret-down'></i>");
	    $("#elementInfo").show();
	  }
	  else {
	  	$(this).html("<i class='fas fa-caret-right'></i>");
	    $("#elementInfo").hide();
	  }
	});

	
	// ElementGewerkPanel einblenden
        /*
	$("#showElementGewerk").click(function() {
	  if($("#elementInfo").is(':hidden')){
	    $(this).html("<span class='glyphicon glyphicon-menu-down'></span>");
	    $("#elementGewerk").show();
	  }
	  else {
	  	$(this).html("<span class='glyphicon glyphicon-menu-right'></span>");
	    $("#elementGewerk").hide();
	  }
	});
        */

	
	// DB Element/Gerätedaten einblenden
	
	$("#showDBData").click(function() {
	  if($("#dbData").is(':hidden')){
	    $(this).html("<i class='fas fa-caret-down'></i>");
	    $("#dbData").show();
	  }
	  else {
	  	$(this).html("<i class='fas fa-caret-right'></i>");
	    $("#dbData").hide();
	  }
	});

    // Räume mit und ohne Element einblenden
	
	$("#showRoomsWithAndWithoutElement").click(function() {
	  if($("#roomsWithAndWithoutElements").is(':hidden')){
	    $(this).html("<i class='fas fa-caret-down'></i>");
	    $("#roomsWithAndWithoutElements").show();
	  }
	  else {
	  	$(this).html("<i class='fas fa-caret-right'></i>");
	    $("#roomsWithAndWithoutElements").hide();
	  }
	});
        
        
        // PDF erzeugen
        $('#createElementListPDF').click(function(){                  
            window.open('/pdf_createElementListPDF.php');
        });
        
        $('#createElementListWithPricePDF').click(function(){                  
            window.open('/pdf_createElementListWithPricePDF.php');
        });
        
         $('#createElementEinbringwegePDF').click(function(){                  
            window.open('/pdf_createElementEinbringwegePDF.php');
        });
        

    
</script>


</body>

</html>
