<?php
session_start();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Meeting</title>
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

 <!--
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.css"/>
 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.js"></script>
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
<body>
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
                                    <a class='dropdown-item' href='roombookSpecificationsLab.php'>Raumbuch - Bauangaben Labor</a>
                                    <a class='dropdown-item active' href='roombookMeeting.php'>Raumbuch - Meeting</a>
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
              <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fa fa-tasks'></i> Ausschreibungen</a>
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
                <div class="card-body">
                    <?php
                            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                            /* change character set to utf8 */
                            if (!$mysqli->set_charset("utf8")) {
                                printf("Error loading character set utf8: %s\n", $mysqli->error);
                                exit();
                            }

                            $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Nutzfläche, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, 
                            tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_räume.idTABELLE_Räume
                                            FROM tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
                                            WHERE (((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."));";


                            $result = $mysqli->query($sql);

                            echo "<table class='table table-striped table-bordered table-sm' id='tableRooms'  cellspacing='0' width='100%'>
                            <thead class='thead'><tr>
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
                <hr>                
            </div>
            <div class="row mt-4">
                <div class="col-md-1">
                    <div class="card bg-dark text-center">
                        <div class="card-body">
                            <div class="card-title">
                                <button type='button' class='btn text-light' style='background-color:transparent' id="roomInfo"><h1><i class="fas fa-home"></i></h1></button>                                                                          
                            </div>
                            <p class="card-text text-light">Rauminfo</p>
                        </div>
                    </div> 
                    <div class="card bg-info text-center mt-4">
                        <div class="card-body">
                            <div class="card-title">
                                <button type='button' class='btn text-light' style='background-color:transparent' id="roombookBO"><h1><i class="fas fa-user-md"></i></h1></button>                                                                    
                            </div>
                            <p class="card-text text-light">Betriebsorganisation</p>
                        </div>
                    </div>                     
                    <div class="card bg-success text-center mt-4">
                        <div class="card-body">
                            <div class="card-title">
                                <button type='button' class='btn text-light' style='background-color:transparent' id="roombook"><h1><i class="fas fa-list"></i></h1></button>                                                                            
                            </div>
                            <p class="card-text text-light">Rauminhalt</p>
                        </div>
                    </div> 
                </div>
                <div class="col-md-11">                    
                    <div class="card">
                        <div class="card-header" id="informationHeader">                               
                        </div>
                        <div class="card-body" id="informationOverview"></div>                            
                    </div>                    
                </div>
            </div>
        </div> 
</div>
</body>
    <script>
        var ext  ="<?php echo $_SESSION["ext"] ?>";
        var moduleSelected = 1;
	// Tabelle formatieren
	$(document).ready(function(){
            if(ext === '0'){
		$('#tableRooms').DataTable( {
                    "columnDefs": [
                        {
                            "targets": [ 0 ],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "select": true,
                    //"paging": true,
                    "searching": true,
                    "lengthChange": false,
                    "info": true,
                    "order": [[ 1, "asc" ]],
                    "pagingType": "simple",
                    "pageLength": 10,
                    //"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}		     
                } );
            }
            else{
                
            }
            
            // CLICK TABELLE RÄUME
	    var table = $('#tableRooms').DataTable();
 
	    $('#tableRooms tbody').on( 'click', 'tr', function () {
			
	        var id = table.row( $(this) ).data()[0];
                $.ajax({
                    url : "setSessionVariables.php",
                    data:{"roomID":id},
                    type: "GET",
                    success: function(data){   
                            var url = "roombookMeetingRoombook.php";
                            var anzeige = "<H4><i class='fas fa-list'></i> Rauminhalt</H4>";
                            var anzeigeColor = "#5cb85c";                            
                            if(moduleSelected === 0){
                                url="roombookMeetingRoomInfo.php";
                                anzeige = "<H4><i class='fas fa-home'></i> Rauminfo</H4>";
                                anzeigeColor = "#343a40";
                            }
                            else{
                                url="roombookMeetingBO.php";
                                anzeige = "<H4><i class='fas fa-user-md'></i> Betriebsorganisation</H4>";
                                anzeigeColor = "#5bc0de";
                            }
                            $.ajax({
                                url : url,
                                type: "GET",
                                success: function(data){
                                    $("#informationOverview").html(data);
                                    document.getElementById("informationHeader").style.backgroundColor = anzeigeColor;
                                    document.getElementById("informationHeader").style.color = "#f9f9f9";
                                    document.getElementById("informationHeader").innerHTML=anzeige;                                    
                                } 
                                
                            });	
                    }
                });		        
	    } );
	           
	});
        
        //Rauminfo Button CLICK-----------------
        $("#roomInfo").click(function() {
            $.ajax({
                url : "roombookMeetingRoomInfo.php",
                type: "GET",
                success: function(data){
                    $("#informationOverview").html(data);                    
                    document.getElementById("informationHeader").style.backgroundColor = "#343a40";
                    document.getElementById("informationHeader").style.color = "#f9f9f9";
                    document.getElementById("informationHeader").innerHTML="<H4><i class='fas fa-home'></i> Rauminfo</H4>";
                    moduleSelected = 0;
                }
                
            });
	});
        //-------------------------------------
        
        //RauminhaltButton CLICK-----------------
        $("#roombook").click(function() {
            $.ajax({
                url : "roombookMeetingRoombook.php",
                type: "GET",
                success: function(data){
                    $("#informationOverview").html(data);                    
                    document.getElementById("informationHeader").style.backgroundColor = "#5cb85c";
                    document.getElementById("informationHeader").style.color = "#f9f9f9";
                    document.getElementById("informationHeader").innerHTML="<H4><i class='fas fa-list'></i> Rauminhalt</H4>";
                    moduleSelected = 1;
                } 
                
            });
	});
        //-------------------------------------
        
        //Betriebsorganisation CLICK-----------------
        $("#roombookBO").click(function() {
            $.ajax({
                url : "roombookMeetingBO.php",
                type: "GET",
                success: function(data){
                    $("#informationOverview").html(data);                    
                    document.getElementById("informationHeader").style.backgroundColor = "#5bc0de";
                    document.getElementById("informationHeader").style.color = "#f9f9f9";
                    document.getElementById("informationHeader").innerHTML="<H4><i class='fas fa-user-md'></i> Betriebsorganisation</H4>";
                    moduleSelected = 2;
                } 
                
            });
	});
        //-------------------------------------
</script>
</html> 
