<?php
    session_start();
    $_SESSION["dbAdmin"]="0";
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ÖBA - Dashboard</title>
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
                                    <a class='dropdown-item active' href='roombookDetailed.php'>Raumbuch - Detail</a>
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
                    <a class="dropdown-item active" href="dashboardAusfuehrung.php"><i class='fas fa-tachometer-alt'></i> Dashboard</a>
                    <a class="dropdown-item" href="roombookVorleistungen.php"><i class='fas fa-tasks'></i> Vorleistungen</a>
                    <a class="dropdown-item" href="roombookAusfuehrung.php"><i class='fas fa-building'></i> Räume</a>
                    <a class="dropdown-item" href="roombookAusfuehrungLiefertermine.php"><i class='far fa-calendar-alt'></i> Liefertermine</a>
                    <a class="dropdown-item" href="roombookAbrechnung.php"><i class='fas fa-euro-sign'></i> Abrechnung</a>
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
    <div class='row mt-4 mb-4'>
        <div class='col-sm-3'>
            <div class="card border-info">
                <div class="card-body">
                    <h4 class="card-subtitle text-muted">Vorleistungen kontrolliert</h4>
                    <?php
                            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                            /* change character set to utf8 */
                            if (!$mysqli->set_charset("utf8")) {
                                printf("Error loading character set utf8: %s\n", $mysqli->error);
                                exit();
                            }       
                            
                            $sqlVorleistungen = "SELECT Count(tabelle_lose_extern.Vorleistungspruefung) AS AnzahlvonVorleistungspruefung, tabelle_lose_extern.Vorleistungspruefung
                                                FROM tabelle_lose_extern
                                                WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                                GROUP BY tabelle_lose_extern.Vorleistungspruefung;";
                            
                            $vorleistungGeprfueft = 0;
                            $vorleistungUngeprueft = 0;
                            $result = $mysqli->query($sqlVorleistungen);
                            while($row = $result->fetch_assoc()) {
                                if($row["Vorleistungspruefung"] == 0){
                                    $vorleistungUngeprueft = $row["AnzahlvonVorleistungspruefung"];
                                }
                                else{
                                    if($row["Vorleistungspruefung"] == 1){
                                        $vorleistungGeprfueft = $row["AnzahlvonVorleistungspruefung"];
                                    }
                                }
                            }
                    ?> 
                    <h1 class="card-title text-info mt-2"><?php echo round($vorleistungGeprfueft/($vorleistungGeprfueft+$vorleistungUngeprueft) * 100,2); ?> %</h1>                       
                    <a href="roombookVorleistungen.php" class="card-link">Festlegen ></a>
                </div>
            </div>
        </div>
        <div class='col-sm-3'>
            <div class="card border-danger">
                <div class="card-body">
                    <h4 class="card-subtitle text-muted">Liefertermine fixiert</h4>
                    <?php                            
                            $sql1 = "SET @lieferDatumGesetzt = 
                                    (
                                    SELECT Count(tabelle_räume_has_tabelle_elemente.Lieferdatum) AS AnzahlvonLieferdatum
                                    FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Anzahl)>0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
                                    HAVING (((Count(tabelle_räume_has_tabelle_elemente.Lieferdatum)) Is Not Null))
                                    )";

                            $sql2 = "SET @gesamtElemente = 
                                    (
                                    SELECT Count(*) AS AnzahlvonLieferdatum
                                    FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Anzahl)>0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
                                    )";
                            
                            $sql3 = "SELECT FORMAT(@lieferDatumGesetzt/@gesamtElemente, 4) AS ergebnis;";
                                                                                    
                            $result1 = $mysqli->query($sql1);
                            $result2 = $mysqli->query($sql2);
                            $result3 = $mysqli->query($sql3);
                            while($row = $result3->fetch_assoc()) {
                                $lieferDatumProzent = $row["ergebnis"];
                            }
                             
                    ?>  
                    <h1 class="card-title text-danger mt-2"><?php echo $lieferDatumProzent * 100; ?> %</h1>    
                    <a href="roombookAusfuehrungLiefertermine.php" class="card-link">Festlegen ></a>
                </div>
            </div>
        </div>
        <div class='col-sm-3'>
            <div class="card border-success">
                <div class="card-body">
                    <h4 class="card-subtitle text-muted">Abgerechnet</h4>
                    <?php                            
                            $sql = "SELECT Sum(tabelle_lose_extern.Vergabesumme) AS SummevonVergabesumme, Sum(tabelle_rechnungen.Rechnungssumme) AS SummevonRechnungssumme
                                    FROM tabelle_rechnungen RIGHT JOIN tabelle_lose_extern ON tabelle_rechnungen.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                                    WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."));";
                            
                            $result = $mysqli->query($sql);
                            while($row = $result->fetch_assoc()) {
                                $vergabesumme = $row["SummevonVergabesumme"];                                
                                $abrechngungssumme = $row["SummevonRechnungssumme"];   
                            }
                    ?>                       
                    <h1 class="card-title text-success mt-2"><?php echo round($abrechngungssumme/$vergabesumme * 100,2); ?> %</h1>    
                    <a href="roombookAbrechnung.php" class="card-link">Festlegen ></a>
                </div>
            </div>
        </div>
        <div class='col-sm-3'>
            <div class="card border-warning">
                <div class="card-body">
                    <h4 class="card-subtitle text-muted">Schlussgerechnet</h4>
                    <?php                            
                            $sql = "SELECT Count(tabelle_lose_extern.Schlussgerechnet) AS AnzahlvonLosen, tabelle_lose_extern.Schlussgerechnet
                                    FROM tabelle_lose_extern
                                    WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                    GROUP BY tabelle_lose_extern.Schlussgerechnet;";
                            
                            $result = $mysqli->query($sql);
                            while($row = $result->fetch_assoc()) {
                                if($row["Schlussgerechnet"] === "0"){
                                    $notFinishedLots = $row["AnzahlvonLosen"];
                                }
                                if($row["Schlussgerechnet"] === "1"){
                                    $finishedLots = $row["AnzahlvonLosen"];
                                }
                            }
                    ?>   
                    <h1 class="card-title text-warning mt-2"><?php echo round($finishedLots/($finishedLots + $notFinishedLots) * 100,2); ?> %</h1>    
                    <a href="roombookAbrechnung.php" class="card-link">Festlegen ></a>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class='row'>
        <div class='col-sm-6'>  
            <div class="mt-4 card">
                <div class="card-header"><h4>ToDo's</h4></div>
                <div class="card-body">
                    <div class="row">
                        <div class='col-sm-12'>                            
                            <?php
                                $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.idtabelle_Vermerke
                                        FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                                        WHERE (((tabelle_Vermerke.Vermerkart)='Bearbeitung') AND ((tabelle_Vermerkgruppe.Gruppenart)='ÖBA-Protokoll') AND ((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                        ORDER BY tabelle_Vermerkgruppe.Datum DESC;";

                                $result = $mysqli->query($sql);	
                                
                                echo "<div class='table-responsive'><table class='table table-striped table-bordered table-sm' id='tableOEBAVermerke' cellspacing='0' width='100%'> 
                                        <thead><tr>
                                        <th>ID</th> 
                                        <th>Protokoll</th>
                                        <th>Gewerk</th>
                                        <th>Status</th>
                                        <th>Wer</th>
                                        <th>Fälligkeit</th>
                                        <th>Vermerk</th>
                                        <th>Raum</th>                                        	        
                                        <th>Status</th>
                                        </tr></thead><tbody>";
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>".$row["idtabelle_Vermerke"]."</td>";
                                    echo "<td>".$row["Gruppenname"]."</td>";
                                    echo "<td>".$row["LosNr_Extern"]."</td>"; 
                                    echo "<td>";  
                                        if($row["Bearbeitungsstatus"]=="0"){
                                            if($row["Faelligkeit"] < date("Y-m-d")){
                                                echo "<span class='badge badge-pill badge-danger'> Überfällig </span>";
                                            }
                                            else{
                                                 echo "<span class='badge badge-pill badge-warning'> Offen </span>";
                                            }
                                        }
                                        else{
                                            echo "<span class='badge badge-pill badge-success'> Abgeschlossen </span>";
                                        }
                                    echo "</td>";
                                    echo "<td>".$row["Name"]." ".$row["Vorname"]."</td>";
                                    echo "<td>";
                                        if($row["Vermerkart"]!="Info"){
                                            echo $row["Faelligkeit"];
                                        }
                                    echo "</td>"; 
                                    echo "<td><button type='button' class='btn btn-xs btn-light' data-toggle='popover' title='Vermerk' data-placement='right' data-content='".$row["Vermerktext"]."'><i class='far fa-comment'></i></button></td>";
                                    echo "<td>".$row["Raumnr"]." ".$row["Raumbezeichnung"]."</td>";                                                          
                                    echo "<td>".$row["Bearbeitungsstatus"]."</td>";	 
                                    echo "</tr>";
                                }
                                echo "</tbody></table></div>";
                        ?>             
                        </div>
                    </div>                                            	
                </div>
            </div>
        </div>
        <div class='col-sm-6'>  
            <div class="mt-4 card">
                <div class="card-body">
                    <div class="row">
                        <div class='col-sm-12'>
                            <h4 class="card-subtitle text-muted">Kommende Termine</h4>
                            <?php
                                $sql = "SELECT WEEK(tabelle_räume_has_tabelle_elemente.Lieferdatum) as lieferWeek, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, WEEK(CURDATE()) as currentWeek, tabelle_lieferant.Lieferant
                                        FROM tabelle_lieferant
                                        RIGHT JOIN tabelle_lose_extern
                                        RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                                        ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND WEEK(tabelle_räume_has_tabelle_elemente.Lieferdatum) >= WEEK(CURDATE()) AND WEEK(tabelle_räume_has_tabelle_elemente.Lieferdatum) <= WEEK(CURDATE())+4 )
                                        GROUP BY lieferWeek, LosNr_Extern
                                        ORDER BY lieferWeek asc;";

                                $result = $mysqli->query($sql);	
                                $currentWeek = 0;
                                while($row = $result->fetch_assoc()) {
                                    if($row["lieferWeek"] !== $currentWeek){
                                        if($currentWeek > 0){
                                            echo "</div></div>";
                                        }
                                        echo "<div class='mt-4 card'>
                                            <div class='card-header bg-info rounded'>
                                            <h4><span class='badge badge-light'>KW ".$row["lieferWeek"]."</span></h4>
                                          </div>
                                          <div class='card-body'>
                                          <h4>".$row["LosNr_Extern"]."-".$row["LosBezeichnung_Extern"].": ".$row["Lieferant"]."</span></h4>";
                                        $currentWeek = $row["lieferWeek"];
                                    }
                                    else{
                                        echo "<h4>".$row["LosNr_Extern"]."-".$row["LosBezeichnung_Extern"].": ".$row["Lieferant"]."</span></h4>";
                                    }
                                }
                                $mysqli ->close();
                        ?>             
                        </div>
                    </div>                                            	
                </div>
            </div>
        </div>
    </div>  
</div> 
<script>    
    // Tabellen formatieren
    $(document).ready(function(){	                        
        $('#tableOEBAVermerke').DataTable( {
            "select":false,
            "paging": false,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 20,
            "columnDefs": [
                {
                    "targets": [ 0, 8 ],
                    "visible": false,
                    "searchable": false
                }
            ],
            "order": [[ 5, "asc" ]],
            "orderMulti": false,
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
        } );
        
        // Popover for Vermerk	
        $(function () {
            $('[data-toggle="popover"]').popover();
        });
    });  
</script>
</body>
</html>
