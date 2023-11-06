<?php
session_start();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Dashboard</title>
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

 <style>

.btn-xs {
  height: 22px;
  padding: 2px 5px;
  font-size: 12px;
  line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
  border-radius: 3px;
}

#body, html, .container-fluid {
        height: 100%;
        font-size:13px;
    }
    
.card {
    overflow:hidden;
}

.card-body .rotate {
    z-index: 8;
    float: right;
    height: 100%;
}

.card-body .rotate i {
    color: rgba(20, 20, 20, 0.15);
    position: absolute;
    left: 0;
    left: auto;
    right: 10px;
    bottom: 0;
    display: block;
    -webkit-transform: rotate(-44deg);
    -moz-transform: rotate(-44deg);
    -o-transform: rotate(-44deg);
    -ms-transform: rotate(-44deg);
    transform: rotate(-44deg);
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
                        <li class='nav-item active'><a class='py-0 nav-link' href='dashboard.php'><i class='fa fa-tachometer-alt'></i> Dashboard</a></li>
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
              <li class="py-0 nav-item "><a class="py-0 nav-link text-success disabled" id="projectSelected">Aktuelles Projekt: <?php  if ($_SESSION["projectName"] != ""){echo $_SESSION["projectName"];}else{echo "Kein Projekt ausgewählt!";}?></a></li>
              <li><a class="py-0 nav-link" href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a></li>
          </ul>              
    </nav>
    <div class="row mt-3">
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="card text-dark h-100">
                <div class="card-header bg-light"><h3>Anzahl meiner aktiven Projekte</h3></div>
                      <div class="card-body text-dark">
                          <div class="rotate">
                              <i class="fa fa-list fa-4x"></i>
                          </div>
                          <h1 class="display-4" id="numberOfActiveProjects"></h1>
                      </div>
                  </div>
        </div>
    </div>
    <!--
    <div class="row mt-3">
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="card text-white bg-warning h-100">
                <div class="card-header"><h3>Offene Vermerke in meinen Projekten</h3></div>      
                <div class="card-body">                            
                    <div class="rotate">
                        <i class="fa fa-question fa-4x"></i>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    -->
    <div class="row mt-3">               
        <div class="col-xl-6 col-sm-6 py-2">
            <div class="card text-dark h-100">
                <div class="card-header bg-light "><h3>Offene Protokollpunkte</h3></div>      
                <div class="card-body"> 
                    <canvas id="chart-openVermerkeInActiveProjects"></canvas>                  
                </div>
            </div>
        </div>        
        <div class="col-xl-6 col-sm-6 py-2">
            <div class="card text-dark h-100">
                <div class="card-header bg-light "><h3>Offene Verfahren nach Status</h3></div>      
                <div class="card-body"> 
                    <canvas id="chart-openTendersWithStatusInActiveProjects"></canvas>
                </div>
            </div>
        </div>
    </div>
   
</div>    
</body>
<script>
    
$(document).ready(function(){ 
    $.ajax({
        url : "getNumberOfActiveProjects.php",
        type: "GET",
        success: function(data){
            $("#numberOfActiveProjects").html(data);
        }
    });
    
    
    $.ajax({
        url : "getChartDataActiveOpenVermerke.php",
        type: "GET",
        success: function(data){
            console.log(data);
            var projects = [];            
            var openVermerke = [];
            var openVermerkeOverdue = [];
            
            for(var i in data) {
                
                projects[i] = data[i].Interne_Nr + " " + data[i].Projektname;
                openVermerke[i] = data[i].VermerkeOffen;
                openVermerkeOverdue[i] = data[i].VermerkeUeberf;
            }
            var chartConfig = {
                    type: 'bar',
                    data: {
                        datasets: [{
                                data: openVermerkeOverdue,
                                backgroundColor: 'rgba(217, 83, 79, 1)',
                                label: 'Überfällig'
                            },
                            {
                                data: openVermerke,
                                backgroundColor: 'rgba(240, 173, 78, 1)',
                                label: 'Offen'
                            }],
                        labels: projects
                    },
                    options: {
                        responsive: true,
                        legend: {
                                display: true,
                                position: 'top'
                        },
                        title: {
                                display: false,
                                text: 'Chart.js Bar Chart'
                        },
                        animation: {
                                animateScale: true,
                                animateRotate: true
                        },
                        scales: {
                            xAxes: [{
                                stacked: true,
                                ticks: {
                                    fontSize: 10,
                                    maxRotation: 90,
                                    minRotation: 80
                                }
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        }
                    }
            };
            var ctx = document.getElementById('chart-openVermerkeInActiveProjects').getContext('2d');
            var chart = new Chart(ctx, chartConfig);   
        } 
    });
    
    $.ajax({
        url : "getChartDataActiveOpenTendersWithStatus.php",
        type: "GET",
        success: function(data){
            console.log(data);
            var counterTendersStatus0 = [];
            var counterTendersStatus2 = [];
            var projects = [];
            var currentProject = "";
            var projectCounter = 0;
            var statusBefore = 0;
            var counterStatus0 = 0;
            var counterStatus2 = 0;
            for(var i in data) {                
                //Neues Projekt in Daten
                if(data[i].idTABELLE_Projekte !== currentProject){
                    projects[projectCounter] = data[i].Projektname;
                    projectCounter++;   
                    
                    if(data[i].Status === "0" && statusBefore === 0){
                        counterTendersStatus0[counterStatus0] = data[i].Counter;
                        counterStatus0++;
                        if(counterStatus2 > 0){                            
                            counterTendersStatus2[counterStatus2] = 0;  
                            counterStatus2++;
                        }
                    }
                    else{
                        if(data[i].Status === "2" && statusBefore === 0){
                            counterTendersStatus2[counterStatus2] = data[i].Counter;
                            counterStatus2++;
                            if(counterStatus0 > 0){
                                counterTendersStatus0[counterStatus0] = 0;
                                counterStatus0++;
                            }
                        }
                        else{
                            if(data[i].Status === "2" && statusBefore === 2){
                                counterTendersStatus0[counterStatus0] = 0;
                                counterStatus0++;
                                counterTendersStatus2[counterStatus2] = data[i].Counter;
                                counterStatus2++;
                            }
                            else{
                                counterTendersStatus0[counterStatus0] = data[i].Counter;
                                counterStatus0++;
                            }
                        }                        
                    }                    
                }
                else{
                    if(data[i].Status === "0"){
                        counterTendersStatus0[counterStatus0] = data[i].Counter;
                        counterStatus0++;
                    }
                    else{
                        counterTendersStatus2[counterStatus2] = data[i].Counter;
                        counterStatus2++;
                    }
                }
                statusBefore = parseInt(data[i].Status);            
                currentProject = data[i].idTABELLE_Projekte;      
            }
            
            var chartConfig = {
                    type: 'bar',
                    data: {
                        datasets: [{
                                data: counterTendersStatus0,
                                backgroundColor: 'rgba(217, 83, 79, 1)',
                                label: 'Nicht abgeschlossen'
                        },
                        {
                                data: counterTendersStatus2,
                                backgroundColor: 'rgba(240, 173, 78, 1)',
                                label: 'Wartend'
                        }],
                        labels: projects
                    },
                    options: {
                        responsive: true,
                        legend: {
                                display: true,
                                position: 'top'
                        },
                        title: {
                                display: false,
                                text: 'Chart.js Bar Chart'
                        },
                        animation: {
                                animateScale: true,
                                animateRotate: true
                        },
                        scales: {
                            xAxes: [{
                                stacked: true,
                                ticks: {
                                    fontSize: 10,
                                    maxRotation: 90,
                                    minRotation: 80
                                }
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        }
                    }
            };
            var ctx = document.getElementById('chart-openTendersWithStatusInActiveProjects').getContext('2d');
            var chart = new Chart(ctx, chartConfig);   
        } 
    }); 
});
    
</script>
</html> 
