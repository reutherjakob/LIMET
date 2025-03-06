<?php
session_start();
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

 <style>

.btn-sm {
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
              <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fa fa-list-alt'></i> Projekte</a>
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
                                <a class=' py-0 nav-link dropdown-toggle' data-bs-toggle='dropdown' href='#'><i class='fa fa-book'></i> Raumbuch</a>              
                                <ul class='dropdown-menu'>
                                    <a class='dropdown-item' href='roombookSpecifications.php'>Raumbuch - Bauangaben</a>
                                    <a class='dropdown-item' href='roombookMeeting.php'>Raumbuch - Meeting</a>
                                    <a class='dropdown-item' href='roombookDetailed.php'>Raumbuch - Detail</a>
                                    <a class='dropdown-item' href='roombookBO.php'>Raumbuch - Betriebsorganisation</a>
                                    <a class='dropdown-item' href='roombookReports.php'>Raumbuch - Berichte</a>
                                    <a class='dropdown-item' href='elementsInProject.php'>Elemente im Projekt</a>
                                </ul>
                              </li>
                              <li class='nav-item dropdown'>
                                <a class='py-0 nav-link dropdown-toggle' data-bs-toggle='dropdown' href='#'><i class='fa fa-euro-sign'></i> Kosten</a>              
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
              <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fa fa-recycle'></i> Bestand</a>
              <ul class="dropdown-menu">
                  <a class="dropdown-item" href="roombookBestand.php">Bestand - Raumbereich</a>	
                  <a class="dropdown-item" href="roombookBestandElements.php">Bestand - Gesamt</a>
              </ul>
            </li>
            <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fa fa-tasks'></i> Ausschreibungen</a>
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
              <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fas fa-wrench'></i> Ausführung-ÖBA</a>
              <ul class="dropdown-menu">
                  <a class="dropdown-item" href="dashboardAusfuehrung.php"><i class='fas fa-tachometer-alt'></i> Dashboard</a>
                    <a class="dropdown-item" href="roombookAusfuehrung.php"><i class='fas fa-building'></i> Räume</a>
                    <a class="dropdown-item" href="roombookAusfuehrungLiefertermine.php"><i class='far fa-calendar-alt'></i> Liefertermine</a>
              </ul>
            </li>
             <li class="py-0 nav-item dropdown">
                <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fas fa-camera'></i> Fotos</a>
                <ul class="dropdown-menu">
                    <a class='dropdown-item' href='imageGallery.php'><i class='fas fa-images'></i> Gesamt</a>
                    <a class='dropdown-item active' href='imageRoomgallery.php'><i class='fas fa-sitemap'></i> Raumzuordnung</a>
                </ul>
            </li>
              
          <?php  
                if($_SESSION["ext"]==0){
                    echo "<li class='py-0 nav-item dropdown'>
                                <a class='py-0 nav-link dropdown-toggle' data-bs-toggle='dropdown' href='#'><i class='fa fa-buromobelexperte '></i> Datenbank-Verwaltung</a>              
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
    <div class='mt-4 row'>  
        <div class='col-md-12'>
            <div class="mt-4 card">
                <div class="card-header"><b>Räume im Projekt</b>
                </div>
                <div class="card-body">    
                    <?php
                        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                        /* change character set to utf8 */
                        if (!$mysqli->set_charset("utf8")) {
                            printf("Error loading character set utf8: %s\n", $mysqli->error);
                            exit();
                        }

                        $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Nutzfläche, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, 
                        tabelle_räume.`Anmerkung allgemein`, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_räume.idTABELLE_Räume, tabelle_räume.`MT-relevant`
                                        FROM tabelle_räume INNER JOIN view_Projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = view_Projekte.idTABELLE_Projekte
                                        WHERE (((view_Projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."));";


                        $result = $mysqli->query($sql);

                        echo "<table class='table table-striped table-bordered table-sm' id='tableRooms'   >
                        <thead><tr>
                        <th>ID</th>
                        <th>Raumnr</th>
                        <th>Raumbezeichnung</th>
                        <th>Nutzfläche</th>
                        <th>Raumbereich Nutzer</th>
                        <th>MT-relevant</th>
                        </tr>
                        <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><select id='filter_MTrelevant'>
                            <option value='2'></option>
                            <option value='1'>Ja</option>
                            <option value='0'>Nein</option>
                        </select></th>
                        </tr>
                        </thead><tbody>";

                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row["idTABELLE_Räume"]."</td>";
                            echo "<td>".$row["Raumnr"]."</td>";
                            echo "<td>".$row["Raumbezeichnung"]."</td>";
                            echo "<td>".$row["Nutzfläche"]."</td>";
                            echo "<td>".$row["Raumbereich Nutzer"]."</td>";
                            if($row["MT-relevant"] == '0'){
                                echo "<td>Nein</td>";
                            }
                            else{
                                echo "<td>Ja</td>";
                            }
                            echo "</tr>";						    
                        }
                        echo "</tbody></table>";					
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <div class='mt-4 card'>
                <div class="card-header"><b>Fotos zu Raum</b></div>
                <div class="card-body" id="roomImages">
                    
                </div>
            </div>
        </div>
        <div class='col-md-6'>
            <div class='mt-4 card'>
                <div class="card-header"><b>Fotos im Projekt</b></div>
                <div class="card-body" id="projectImages">
                </div>
            </div>
        </div>        
    </div>
</div>  
</body>        
<script>       
    var table;
    
    $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                if ( settings.nTable.id !== 'tableRooms' ) {
                    return true;
                }                    
                               
                
                if($("#filter_MTrelevant").val()==='1'){
                    if (data [5] === "Ja")
                    {
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    if($("#filter_MTrelevant").val()==='0'){
                        if (data [5] === "Nein")
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
            }
    );        

    $('#filter_MTrelevant').change( function() {
        table.draw();
    } );
    
    // Tabellen formatieren
    $(document).ready(function(){	  
        table = $('#tableRooms').DataTable( {
            "paging": false,
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],
            "orderCellsTop": true,
            "order": [[ 1, "asc" ]],
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
            "scrollCollapse": true,
            select: {
                style: 'single'
            }
        } );
        
        $('#tableRooms tbody').on( 'click', 'tr', function () {            
            var raumID = table.row( $(this) ).data()[0];              
                    
            $.ajax({
                url : "getImagesToRoom.php",
                data:{"roomID":raumID},
                type: "GET",
                success: function(data){			            
                    $("#roomImages").html(data); 
                    $.ajax({
                        url : "getImagesNotInRoom.php",
                        data:{"roomID":raumID},
                        type: "GET",
                        success: function(data){			            
                            $("#projectImages").html(data);    
                        } 
                    });
                } 
            });				 
        });
    });
</script>
</html> 
