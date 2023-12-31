<?php
session_start();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Lose-Elemente</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
<!-- 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
-->


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>

<!--DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

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
              <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fa fa-tasks'></i> Ausschreibungen</a>
              <ul class="dropdown-menu">
                    <a class="dropdown-item" href="tenderLots.php">Los-Verwaltung</a>
                    <a class="dropdown-item" href="tenderCalendar.php">Vergabekalender</a>
                    <?php 
                        if($_SESSION["ext"]==0){
                            echo "<a class='dropdown-item' href='tenderCharts.php'>Vergabe-Diagramme</a>";
                        }
                    ?>
                    <a class="dropdown-item active" href="elementLots.php">Element-Verwaltung</a>
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
        <div class="card-header"><b>Elemente im Projekt</b></div>
        <div class="card-body" id="elementLots">

				  	<?php
								$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	
								
								/* change character set to utf8 */
								if (!$mysqli->set_charset("utf8")) {
								    printf("Error loading character set utf8: %s\n", $mysqli->error);
								    exit();
								}
								/*																
								$sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_projekt_varianten_kosten.Kosten*Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS PP, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
										FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_varianten INNER JOIN (tabelle_lose_extern RIGHT JOIN ((tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
										WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
										GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
										ORDER BY tabelle_elemente.ElementID;";
                                                                 * 
                                                                 */
								$sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_projekt_varianten_kosten.Kosten*Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS PP, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
                                                                        tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_projektbudgets.Budgetnummer
                                                                        FROM tabelle_projekt_varianten_kosten 
                                                                        INNER JOIN (tabelle_varianten 
                                                                                                INNER JOIN (tabelle_lose_extern 
                                                                                                                        RIGHT JOIN ((tabelle_räume_has_tabelle_elemente 
                                                                                                                                                INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) 
                                                                                                            INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
                                                                                                            LEFT JOIN tabelle_projekt_element_gewerk ON tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte=tabelle_räume.tabelle_projekte_idTABELLE_Projekte AND tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente=tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                                                                                                            LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
                                                                                                            LEFT JOIN tabelle_projektbudgets ON tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets = tabelle_projektbudgets.idtabelle_projektbudgets
                                                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
                                                                        GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_projektbudgets.Budgetnummer
                                                                        ORDER BY tabelle_elemente.ElementID;";
                                                                
								$result = $mysqli->query($sql);
														
								echo "<table class='table table-striped table-bordered table-sm' id='tableElementsInProject'  cellspacing='0' width='100%'>
									<thead><tr>
                                                                                <th></th>
										<th>ID-Element</th>
										<th>ID-Variante</th>
										<th>ID-Los</th>
										<th>Bestand-Wert</th>
										<th>Anzahl</th>
										<th>ID</th>
										<th>Element</th>
										<th>Variante</th>
										<th>Raumbereich</th>
										<th>Bestand</th>                                                                              									
										<th>EP</th>
										<th>PP</th>
										<th>Los-Nr</th>
										<th>Los</th>
										<th>Ausführungsbeginn</th>
                                                                                <th>Gewerk</th>
                                                                                <th>Budget</th>
                                                                                <th>Vergabe abgeschlossen</th> 
									</tr>
                                                                        <tr>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th><b>Stk >0 <input type='checkbox' id='filter_count'></b></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th><select id='filter_bestand'>
                                                                                    <option value='2'></option>
                                                                                    <option value='1'>Ja</option>
                                                                                    <option value='0'>Nein</option>
                                                                                </select>
                                                                            </th>                                                                              									
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th><input type='checkbox' id='filter_lot'></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
                                                                            <th></th>
									</tr>
									</thead>";
                                                                       /*
                                                                        if($_SESSION["ext"]==0){
                                                                            echo "<tfoot><tr>
                                                                                        <th colspan='11' style='text-align:right'>Schätzsumme inkl. Bestand:</th>
                                                                                        <th colspan='4'></th>
                                                                                    </tr>
                                                                                        </tfoot>";
                                                                        }
                                                                        * 
                                                                        */
									
						            
									echo "<tbody>";
									//setlocale(LC_MONETARY,"de_DE");
									while($row = $result->fetch_assoc()) {
									    echo "<tr>";
                                                                            echo "<td></td>";
									    echo "<td>".$row["TABELLE_Elemente_idTABELLE_Elemente"]."</td>";
									    echo "<td>".$row["idtabelle_Varianten"]."</td>";
									    echo "<td>".$row["idtabelle_Lose_Extern"]."</td>";
									    echo "<td>".$row["Neu/Bestand"]."</td>";
									    echo "<td>".$row["SummevonAnzahl"]."</td>";
									    echo "<td>".$row["ElementID"]."</td>";
									    echo "<td>".$row["Bezeichnung"]."</td>";
									    echo "<td>".$row["Variante"]."</td>";
									    echo "<td>".$row["Raumbereich Nutzer"]."</td>";
									    if($row["Neu/Bestand"] == 1){
						    				echo"<td>Nein</td>";
                                                                            }
                                                                            else{
                                                                                    echo"<td>Ja</td>";
                                                                            }
									    
									    echo "<td>".money_format("%i", $row["Kosten"])."</td>";	
									    echo "<td>".money_format("%i", $row["PP"])."</td>";	
									    echo "<td>".$row["LosNr_Extern"]."</td>";	
									    echo "<td>".$row["LosBezeichnung_Extern"]."</td>";	
									    echo "<td>".$row["Ausführungsbeginn"]."</td>";	
                                                                            echo "<td>".$row["Gewerke_Nr"]."</td>";
                                                                            echo "<td>".$row["Budgetnummer"]."</td>";
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
									    echo "</tr>";
									    						    
									}
									echo "</tbody></table>";
									$mysqli ->close();

							?>	
		</div>
	</div>
	<!-- Räume mit Element -->
        
	<div class="row">
                <div class="col-sm-8">
                    <div class="mt-4 card">
                        <div class="card-header">Räume mit Element</div>
                        <div class="card-body" id="roomsWithElement"></div>
                    </div>
                </div>
		<div class="col-sm-4">
                        <div class="mt-4 card">
		  		<div class="card-header">Variantenparameter</div>
		  		<div class="card-body" id="variantenParameter"></div>
			</div>
			<div class="mt-4 card">
		  		<div class="card-header">Bestandsdaten</div>
		  		<div class="card-body" id="elementBestand"></div>
			</div>                        
		</div>
	</div>	
</div>

<script>		
	var ext  ="<?php echo $_SESSION["ext"] ?>";
        var table;
        
        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                if ( settings.nTable.id !== 'tableElementsInProject' ) {
                    return true;
                }                    
                               
                
                if($("#filter_bestand").val()==='1'){
                    if($("#filter_count").is(':checked')){
                        if($("#filter_lot").is(':checked')){
                            if (data [10] === "Ja" && Number(data [5]) > 0 && data[13].length > 0)
                            {
                                return true;
                            }
                            else{
                                return false;
                            }
                        }
                        else{
                            if (data [10] === "Ja" && Number(data [5]) > 0 )
                            {
                                return true;
                            }
                            else{
                                return false;
                            }
                        }
                    }
                    else{
                        if($("#filter_lot").is(':checked')){
                            if (data [10] === "Ja" && data[13].length > 0)
                            {
                                return true;
                            }
                            else{
                                return false;
                            }
                        }
                        else{
                            if (data [10] === "Ja" && Number(data [5]) > 0 )
                            {
                                return true;
                            }
                            else{
                                return false;
                            }
                        }
                    }
                }
                else{                    
                    if($("#filter_bestand").val()==='0'){
                        if($("#filter_count").is(':checked')){
                            if($("#filter_lot").is(':checked')){
                                if (data [10] === "Nein" && Number(data [5]) > 0 && data[13].length > 0)
                                {
                                    return true;
                                }
                                else{
                                    return false;
                                }
                            }
                            else{
                                if (data [10] === "Nein" && Number(data [5]) > 0 )
                                {
                                    return true;
                                }
                                else{
                                    return false;
                                }
                            }
                        }
                        else{
                            if($("#filter_lot").is(':checked')){
                                if (data [10] === "Nein" && data[13].length > 0)
                                {
                                    return true;
                                }
                                else{
                                    return false;
                                }
                            }
                            else{
                                if (data [10] === "Nein" && Number(data [5]) > 0 )
                                {
                                    return true;
                                }
                                else{
                                    return false;
                                }
                            }                            
                        }                                              
                    }
                    else{
                        if($("#filter_count").is(':checked')){
                            if($("#filter_lot").is(':checked')){
                                if (Number(data [5]) > 0 && data[13].length > 0)
                                {
                                    return true;
                                }
                                else{
                                    return false;
                                }
                            }
                            else{
                                if (Number(data [5]) > 0)
                                {
                                    return true;
                                }
                                else{
                                    return false;
                                }
                            }                            
                        }
                        else{
                            if($("#filter_lot").is(':checked')){
                                if (data[13].length > 0)
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
                }
            }
        );        

        $('#filter_bestand').change( function() {
            table.draw();
        } );
        $('#filter_count').change( function() {
            table.draw();        
        } );
        $('#filter_lot').change( function() {
            table.draw();        
        } );
        
	$(document).ready(function() {
            // Setup - add a text input to each footer cell
            /*
            $('#tableElementsInProject thead tr').clone(true).appendTo( '#tableElementsInProject thead' );
            $('#tableElementsInProject thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                /*
                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
                
            } );
            */
            
            if(ext === '0'){
		$('#tableElementsInProject').DataTable( {
			"paging": true,
                        "select": true,
			"order": [[ 6, "asc" ]],
			"columnDefs": [
                            {
                                "targets": [ 0,1,2,3,4,15 ],
                                "visible": false,
                                "searchable": false
                            }
                        
	        ],
                "orderCellsTop": true,
	        "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                "mark":true,
                dom: 'Bfrtip',
                   buttons: [
                       'copy'
                   ]
	        //"scrollY":        '20vh',
	    	/*"scrollCollapse": true,
	    	"footerCallback": function ( row, data, start, end, display ) {
		            var api = this.api(), data;
		 
		            // Remove the formatting to get integer data for summation
		            var intVal = function ( i ) {
		                return typeof i === 'string' ?
		                    i.replace(/[\$,]/g, '')*1 :
		                    typeof i === 'number' ?
		                        i : 0;
		            };
		 
		            // Total over all pages
		            total = api
		                .column( 12 )
		                .data()
		                .reduce( function (a, b) {
		                    return intVal(a) + intVal(b);
		                }, 0 );
		 
		            // Total over this page
		            pageTotal = api
		                .column( 12, { page: 'current'} )
		                .data()
		                .reduce( function (a, b) {
		                    return intVal(a) + intVal(b);
		                }, 0 );
		 
		            // Update footer
		            $( api.column( 12 ).footer() ).html(
		                '€ '+pageTotal +' ( € '+ total +' total)'
		            );
                                
		        }*/	   	 
                } );
            }
            else{
                $('#tableElementsInProject').DataTable( {
			"paging": true,
                        "select": true,
			"order": [[ 6, "asc" ]],
			"columnDefs": [
                            {
                                "targets": [ 0,1,2,3,4,11,12,15 ],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
                        //"scrollY":        '20vh',
                        //"scrollCollapse": true	   	 
                } );
            }
	    
	    table = $('#tableElementsInProject').DataTable();
 
	    $('#tableElementsInProject tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            var elementID = table.row( $(this) ).data()[1];	
	            var variantenID = table.row( $(this) ).data()[2];	
	            var losID = table.row( $(this) ).data()[3];	
	            var bestand  = table.row( $(this) ).data()[4];	
                    var raumbereich  = table.row( $(this) ).data()[9];
                                        
	            $.ajax({
                            url : "getRoomsWithElementTenderLots.php",
                            data:{"losID":losID,"variantenID":variantenID,"elementID":elementID,"bestand":bestand,"raumbereich":raumbereich},
                            type: "GET",
                            success: function(data){
                                $("#roomsWithElement").html(data);
                                $("#elementBestand").hide();
                                $.ajax({
                                    url : "getVariantenParameters.php",
                                    data:{"variantenID":variantenID,"elementID":elementID},
                                    type: "GET",
                                    success: function(data){
                                        $("#variantenParameter").html(data);
                                    }
                                });
                            }
	    		} );

	        }
	    } ); 
	} );
    
</script>


</body>

</html>
