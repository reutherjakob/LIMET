y<?php
session_start();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Bauangaben</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/r-2.4.0/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/r-2.4.0/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

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
                                    <a class='dropdown-item active' href='roombookSpecificationsLab.php'>Raumbuch - Bauangaben Labor</a>
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
        <div class="card-header">
            <b>Ein-/Ausblenden: </b>  
                    <a class="toggle-vis" data-column="7">NF</a> -  
                    <a class="toggle-vis" data-column="8">RH</a> -  
                    <a class="toggle-vis" data-column="9">Leistungsbedarf</a> -  
                    <a class="toggle-vis" data-column="10">Leistung</a> -  
                    <a class="toggle-vis" data-column="11">Strahlen</a> -  
                    <a class="toggle-vis" data-column="12">Laser</a> -  
                    <a class="toggle-vis" data-column="13">Laserklasse</a> - 
                    <a class="toggle-vis" data-column="14">AV</a> -  
                    <a class="toggle-vis" data-column="15">SV</a> -  
                    <a class="toggle-vis" data-column="16">ZSV</a> -  
                    <a class="toggle-vis" data-column="17">USV</a> -  
                    <a class="toggle-vis" data-column="18">IT</a> -  
                    <a class="toggle-vis" data-column="19">RJ45</a> -  
                    <a class="toggle-vis" data-column="20">B5220</a> - 
                    <a class="toggle-vis" data-column="21">EMV j/n</a> -  
                    <a class="toggle-vis" data-column="22">5x10mm2 AV Stk</a> -  
                    <a class="toggle-vis" data-column="23">5x10mm2 SV Stk</a> -  
                    <a class="toggle-vis" data-column="24">5x10mm2 USV Stk</a> -  
                    <a class="toggle-vis" data-column="25">5x10mm2 Digestorium Stk</a> -  
                    <a class="toggle-vis" data-column="26">Digestorium MSR 230V SV Stk</a> -  
                    <a class="toggle-vis" data-column="27">32A 3Phasig Einzelanschluss</a> - 
                    <a class="toggle-vis" data-column="28">64A 3Phasig Einzelanschluss</a> -  
                    <a class="toggle-vis" data-column="29">CO2-Melder</a> -  
                    <a class="toggle-vis" data-column="30">O2-Mangel</a> -    
                    <a class="toggle-vis" data-column="31">HT_Waermeabgabe W/m2</a> -  
                    <a class="toggle-vis" data-column="32">HT_Wärme</a> -  
                    <a class="toggle-vis" data-column="33">HT_Abluft Sicherheitsschrank Stk</a> -  
                    <a class="toggle-vis" data-column="34">HT_Abluft Sicherheitsschrank Unterbau Stk</a> -  
                    <a class="toggle-vis" data-column="35">HT_Punktabsaugung Stk</a> -  
                    <a class="toggle-vis" data-column="36">HT_Abluft Digestorium Stk</a> -                      
                    <a class="toggle-vis" data-column="37">HT_Kühlwasser</a> - 
                    <a class="toggle-vis" data-column="38">HT_Kühlwasserleistung W</a> -  
                    <a class="toggle-vis" data-column="39">HT_Notdusche</a> -  
                    <a class="toggle-vis" data-column="40">HT_Spuele Stk</a>  
        </div>
                <div class="card-body">
                    <?php                                                        
                            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	
                            
                            /* change character set to utf8 */
                            if (!$mysqli->set_charset("utf8")) {
                                printf("Error loading character set utf8: %s\n", $mysqli->error);
                                exit();
                            }						

                            /*
                            $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr
                                    FROM tabelle_räume
                                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                    ORDER BY tabelle_räume.Raumnr;";
                            */
                            $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Raumhoehe, tabelle_räume.Geschoss, 
                                    tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung,  tabelle_räume.Laserklasse,  
                                    tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.`IT Anbindung`, tabelle_räume.`ET_RJ45-Ports`, tabelle_räume.`Fussboden OENORM B5220`,
                                    tabelle_räume.`MT-relevant`, tabelle_räume.`ET_5x10mm2_AV_Stk`, tabelle_räume.`ET_5x10mm2_SV_Stk`, tabelle_räume.`ET_5x10mm2_USV_Stk`, tabelle_räume.`ET_5x10mm2_Digestorium_Stk`,
                                    tabelle_räume.`ET_Digestorium_MSR_230V_SV_Stk`, tabelle_räume.`ET_32A_3Phasig_Einzelanschluss`, tabelle_räume.`ET_64A_3Phasig_Einzelanschluss`, 
                                    tabelle_räume.`CO2_Melder`, tabelle_räume.`O2_Mangel`, tabelle_räume.`ET_EMV_ja-nein`, tabelle_räume.`EL_Leistungsbedarf_W_pro_m2`,
                                    tabelle_räume.`HT_Waermeabgabe`, 
                                    tabelle_räume.`HT_Notdusche`, tabelle_räume.`HT_Spuele_Stk`, 
                                    tabelle_räume.`HT_Abluft_Sicherheitsschrank_Stk`, tabelle_räume.`HT_Abluft_Sicherheitsschrank_Unterbau_Stk`, tabelle_räume.`HT_Punktabsaugung_Stk`, tabelle_räume.`HT_Abluft_Digestorium_Stk`,
                                    tabelle_räume.`HT_Kühlwasser`, tabelle_räume.`HT_Kühlwasserleistung_W`
                                    FROM tabelle_räume
                                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                    ORDER BY tabelle_räume.Raumnr;";
                            
                            $result = $mysqli->query($sql);
                            
                            echo "<table class='table table-striped table-bordered table-sm' id='tableRoomsLab'  cellspacing='0' width='100%'>
                            <thead><tr>
                            <th>ID</th>
                            <th></th>
                            <th>MT/LT
                                <select id='filter_MTrelevant'>
                                    <option value='2'></option>
                                    <option value='1'>Ja</option>
                                    <option value='0'>Nein</option>
                                </select>
                            </th>   
                            <th>Raumnr</th>                            
                            <th>Name</th>
                            <th>Raumbereich</th>
                            <th>Geschoss</th>
                            <th>NF</th>
                            <th>RH</th>
                            <th>ET Leistungsbedarf W/m2</th>                            
                            <th>ET_Leistung W</th>
                            <th>Strahlen j/n</th>
                            <th>Laser j/n</th>
                            <th>Laserklasse</th>
                            <th>AV j/n</th>
                            <th>SV j/n</th>
                            <th>ZSV j/n</th>
                            <th>USV j/n</th>
                            <th>IT j/n</th>
                            <th>RJ45-Ports Stk</th>
                            <th>FB ÖNORM B5220</th>
                            <th>EMV j/n</th>                                                                                                       
                            <th>ET 5x10mm2 AV Stk</th>   
                            <th>ET 5x10mm2 SV Stk</th>   
                            <th>ET 5x10mm2 USV Stk</th>   
                            <th>ET 5x10mm2 Digestorium Stk</th> 
                            <th>ET Digestorium MSR 230V SV Stk</th> 
                            <th>ET 32A 3Phasig Einzelanschluss Stk</th> 
                            <th>ET 64A 3Phasig Einzelanschluss Stk</th> 
                            <th>CO2-Melder j/n</th>
                            <th>O2-Mangel j/n</th>    
                            <th>HT_Waermeabgabe W/m2</th> 
                            <th>HT_Wärme W</th>     
                            <th>HT_Abluft Sicherheitsschrank Stk</th>   
                            <th>HT_Abluft Sicherheitsschrank Unterbau Stk</th> 
                            <th>HT_Punktabsaugung Stk</th>
                            <th>HT_Abluft Digestorium Stk</th>
                            <th>HT_Kühlwasser j/n</th>
                            <th>HT_Kühlwasserleistung W</th>
                            <th>HT_Notdusche Stk</th>
                            <th>HT_Spuele Stk</th>                            
                            </tr>                               
                            </thead>
                            <tbody>";
                            
                                                                                
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>".$row["idTABELLE_Räume"]."</td>"; 
                                echo "<td></td>"; 
                                echo "<td>".$row["MT-relevant"]."</td>"; 
                                echo "<td>".$row["Raumnr"]."</td>";                                         
                                echo "<td>".$row["Raumbezeichnung"]."</td>";
                                echo "<td>".$row["Raumbereich Nutzer"]."</td>";
                                echo "<td>".$row["Geschoss"]."</td>";
                                echo "<td>".$row["Nutzfläche"]."</td>";
                                echo "<td>".$row["Raumhoehe"]."</td>";  
                                echo "<td>".$row["EL_Leistungsbedarf_W_pro_m2"]."</td>"; 
                                echo "<td>".$row["Nutzfläche"]*$row["EL_Leistungsbedarf_W_pro_m2"]."</td>"; 
                                echo "<td>".$row["Strahlenanwendung"]."</td>";
                                echo "<td>".$row["Laseranwendung"]."</td>";
                                echo "<td>".$row["Laserklasse"]."</td>";                                
                                echo "<td>".$row["AV"]."</td>";
                                echo "<td>".$row["SV"]."</td>";
                                echo "<td>".$row["ZSV"]."</td>";
                                echo "<td>".$row["USV"]."</td>";
                                echo "<td>".$row["IT Anbindung"]."</td>";
                                echo "<td>".$row["ET_RJ45-Ports"]."</td>";                                                                
                                echo "<td>".$row["Fussboden OENORM B5220"]."</td>";                                
                                echo "<td>".$row["ET_EMV_ja-nein"]."</td>";                                                                  
                                echo "<td>".$row["ET_5x10mm2_AV_Stk"]."</td>"; 
                                echo "<td>".$row["ET_5x10mm2_SV_Stk"]."</td>"; 
                                echo "<td>".$row["ET_5x10mm2_USV_Stk"]."</td>";
                                echo "<td>".$row["ET_5x10mm2_Digestorium_Stk"]."</td>";
                                echo "<td>".$row["ET_Digestorium_MSR_230V_SV_Stk"]."</td>";
                                echo "<td>".$row["ET_32A_3Phasig_Einzelanschluss"]."</td>";
                                echo "<td>".$row["ET_64A_3Phasig_Einzelanschluss"]."</td>";                                
                                echo "<td>".$row["CO2_Melder"]."</td>";                                
                                echo "<td>".$row["O2_Mangel"]."</td>";   
                                echo "<td>".$row["HT_Waermeabgabe"]."</td>"; 
                                echo "<td>".$row["Nutzfläche"]*$row["HT_Waermeabgabe"]."</td>";
                                echo "<td>".$row["HT_Abluft_Sicherheitsschrank_Stk"]."</td>";
                                echo "<td>".$row["HT_Abluft_Sicherheitsschrank_Unterbau_Stk"]."</td>";
                                echo "<td>".$row["HT_Punktabsaugung_Stk"]."</td>";
                                echo "<td>".$row["HT_Abluft_Digestorium_Stk"]."</td>";                                
                                echo "<td>".$row["HT_Kühlwasser"]."</td>";
                                echo "<td>".$row["HT_Kühlwasserleistung_W"]."</td>";
                                echo "<td>".$row["HT_Notdusche"]."</td>";
                                echo "<td>".$row["HT_Spuele_Stk"]."</td>";                                
                                echo "</tr>";
                            }
                            echo "</tbody></table>";                                            
                            $mysqli ->close();

                    ?>	
            </div>
    </div>           
</div>    
    
    
<script>
    var table;
    var column_clicked;
    var row_clicked;
    
    
    $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                if ( settings.nTable.id !== 'tableRoomsLab' ) {
                    return true;
                }                    
                               
                
                if($("#filter_MTrelevant").val()==='1'){
                    if (data [2] === "1")
                    {
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    if($("#filter_MTrelevant").val()==='0'){
                        if (data [2] === "0")
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
            table = $('#tableRoomsLab').DataTable( {                    
                    language: {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},                                          
                    columns: [
                        { 
                            "data": "id",
                            "visible": false,
                            "orderable": false,
                            "searchable": false
                        },
                        { 
                            "data": "responsive",
                            "className": 'control',
                            "orderable": false
                        },
                        { 
                            "data": "MT",
                            "orderable": false
                        },
                        { 
                            "data": "Raumnr",
                            "name": "Raumnr",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "Raumbezeichnung",
                            "name": "Raumbezeichnung",
                            "class": 'editable-text'                            
                        },
                        { 
                            "data": "Raumbereich Nutzer",
                            "name": "Raumbereich Nutzer"
                        },
                        { 
                            "data": "Geschoss",
                            "name": "Geschoss"
                        },
                        { 
                            "data": "Nutzfläche",
                            "name": "Nutzfläche"
                        },
                        { 
                            "data": "Raumhoehe",
                            "name": "Raumhoehe"
                        },
                        { 
                            "data": "EL_Leistungsbedarf_W_pro_m2",
                            "name": "EL_Leistungsbedarf_W_pro_m2",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ET_Leistung W",
                            "name": "ET_Leistung W"
                        },
                        { 
                            "data": "Strahlenanwendung",
                            "name": "Strahlenanwendung",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "Laseranwendung",
                            "name": "Laseranwendung",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "Laserklasse",
                            "name": "Laserklasse",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "AV",
                            "name": "AV",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "SV",
                            "name": "SV",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ZSV",
                            "name": "ZSV",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "USV",
                            "name": "USV",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "IT Anbindung",
                            "name": "IT Anbindung"
                        },
                        { 
                            "data": "ET_RJ45-Ports",
                            "name": "ET_RJ45-Ports",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "Fussboden OENORM B5220",
                            "name": "Fussboden OENORM B5220"
                        },
                        { 
                            "data": "ET_EMV_ja-nein",
                            "name": "ET_EMV_ja-nein",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ET_5x10mm2_AV_Stk",
                            "name": "ET_5x10mm2_AV_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ET_5x10mm2_SV_Stk",
                            "name": "ET_5x10mm2_SV_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ET_5x10mm2_USV_Stk",
                            "name": "ET_5x10mm2_USV_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ET_5x10mm2_Digestorium_Stk",
                            "name": "ET_5x10mm2_Digestorium_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ET_Digestorium_MSR_230V_SV_Stk",
                            "name": "ET_Digestorium_MSR_230V_SV_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ET_32A_3Phasig_Einzelanschluss",
                            "name": "ET_32A_3Phasig_Einzelanschluss",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "ET_64A_3Phasig_Einzelanschluss",
                            "name": "ET_64A_3Phasig_Einzelanschluss",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "CO2_Melder",
                            "name": "CO2_Melder",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "O2_Mangel",
                            "name": "O2_Mangel",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Waermeabgabe",
                            "name": "HT_Waermeabgabe",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Wärme",
                            "name": "HT_Wärme"
                        },
                        { 
                            "data": "HT_Abluft_Sicherheitsschrank_Stk",
                            "name": "HT_Abluft_Sicherheitsschrank_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Abluft_Sicherheitsschrank_Unterbau_Stk",
                            "name": "HT_Abluft_Sicherheitsschrank_Unterbau_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Punktabsaugung_Stk",
                            "name": "HT_Punktabsaugung_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Abluft_Digestorium_Stk",
                            "name": "HT_Abluft_Digestorium_Stk",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Kühlwasser",
                            "name": "HT_Kühlwasser",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Kühlwasserleistung_W",
                            "name": "HT_Kühlwasserleistung_W",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Notdusche",
                            "name": "HT_Notdusche",
                            "class": 'editable-text'
                        },
                        { 
                            "data": "HT_Spuele_Stk",
                            "name": "HT_Spuele_Stk",
                            "class": 'editable-text'
                        }
                        
                    ],
                    "paging": true,
                    "order": [[ 3, "asc" ]],
                    "orderCellsTop": true,
                    "select": true,
                    "pagingType": "simple",
                    "lengthChange": false,
                    "pageLength": 20,
                    "info": true,
                    "mark":true,
                    "stateSave": true,
                    "dom": 'Bfrtip',
                    "buttons": [
                        'excel', 'copy', 'csv'
                    ],
                    responsive: {
                        details: {
                            type: 'column',
                            target: 1
                        }
                    }                                                                                                                 
            } );

	   //table = $('#tableRoomsLabElektro').DataTable();
            
            
	    $("#tableRoomsLab tbody").on('click', 'td.editable-text', function () {
                let oldHtml = $(this).html();
                var columns = table.settings().init().columns;
                var cellData_clicked = table.cell(this).data();
                column_clicked = table.cell(this).index().column;
                row_clicked = $(this).closest('tr');
                var id = table.row(row_clicked).data()['id'];
                                
                if(!oldHtml.startsWith('<input')){
                    //var id = table.row( $(this) ).data()[0];    
                    
                    //var row_clicked = $(this).closest('tr');                
                    //var row_object = table.row(row_clicked).data()[columns[column_clicked].name]; 

                    //var clickedRow = $($(this).closest('td')).closest('tr');                
                    var html = fnCreateTextBox(cellData_clicked, columns[column_clicked].name, id);    
                    $(this).html($(html)); 
                    //table.draw();
                }
            });
            
            $('a.toggle-vis').on( 'click', function (e) {
                e.preventDefault();

                // Get the column API object
                var column = table.column( $(this).attr('data-column') );

                // Toggle the visibility
                column.visible( ! column.visible() );    
            } );
            
	    	    
    });
    
        
    function fnCreateTextBox(value, fieldprop, id) {    
        return "<input name='" + fieldprop + "' type='text' value='" + value + "' id='"+fieldprop+"-"+id+"'></input><button type='button' id='SAVE,"+fieldprop+","+id+"' class='btn btn-xs btn-outline-dark' name='saveElement'  onclick='saveElement(this.id)'><i class='far fa-save'></i></button>";            
    }       
        
    function saveElement(clicked_id) {     
        //alert(clicked_id);
        var newString = clicked_id.split(",");
        var column = newString[1];
        var idValue = newString[2];
        var value = $('input[id='+newString[1]+'-'+newString[2]+']').val();                
        alert("ID: "+idValue+" Spalte: "+column+" Wert: "+value);   
        
        $.ajax({
            url : "saveRoomProperties.php",
            data:{"roomID":idValue,"column":column,"value":value},
            type: "GET",
            success: function(data){
                alert(data);
                if(data.trim() === "Erfolgreich aktualisiert!"){
                    $('#tableRoomsLab').dataTable().fnUpdate(value,row_clicked,column_clicked);
                }
            }
        }); 
        
        //var row = table.row( '#idRow');
        
        //table.DataTable().draw();
    }            
</script>
</body>
</html>
