<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
 <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"/>

<!-- jQuery library -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<!-- Datatables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/js/Simple-jQuery-Dropdown-Table-Filter-Plugin-ddtf-js/ddtf.js"></script>

</head>
<body>
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
?>

<?php
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	
	//Elemente im Raum abfragen
	//$sql = "SELECT view_Raeume_has_Elemente.id, view_Raeume_has_Elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, tabelle_varianten.Variante, tabelle_elemente.Bezeichnung, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, view_Raeume_has_Elemente.`Neu/Bestand`, view_Raeume_has_Elemente.Standort, view_Raeume_has_Elemente.Verwendung, view_Raeume_has_Elemente.Kurzbeschreibung, view_Raeume_has_Elemente.TABELLE_Elemente_idTABELLE_Elemente, view_Raeume_has_Elemente.TABELLE_Geraete_idTABELLE_Geraete
	//		FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((view_Raeume_has_Elemente LEFT JOIN tabelle_geraete ON view_Raeume_has_Elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON view_Raeume_has_Elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = view_Raeume_has_Elemente.tabelle_Varianten_idtabelle_Varianten
	//		WHERE (((view_Raeume_has_Elemente.TABELLE_Räume_idTABELLE_Räume)=".$_SESSION["roomID"]."))
	//		ORDER BY tabelle_elemente.ElementID;";
			
	$sql ="SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_elemente.Bezeichnung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.Standort, tabelle_elemente.CAD_notwendig, tabelle_elemente.CAD_familie_vorhanden, tabelle_elemente.CAD_familie_kontrolliert, tabelle_elemente.CAD_dwg_vorhanden, tabelle_elemente.CAD_Kommentar
			FROM ((tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_varianten ON tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_varianten.idtabelle_Varianten) LEFT JOIN (tabelle_hersteller RIGHT JOIN tabelle_geraete ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
			WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=".$_SESSION["roomID"]."))
			ORDER BY tabelle_elemente.ElementID;";

    
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped' id='tableRoomElements' cellspacing='0'>
	<thead><tr>
	<th>Stück</th>
	<th>Element ID</th>
	<th>Variante</th>
	<th>Element</th>
	<th>Kommentar</th>
	<th>Standort</th>
	<th>CAD-Notwendigkeit</th>
	<th>Revit-Familie</th>
	<th>Revit-Freigabe</th>
	<th>DWG</th>
	<th>CAD-Kommentar</th>
	</tr></thead><tbody>";

	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["Anzahl"]."</td>";
	    echo "<td>".$row["ElementID"]."</td>";
	    echo "<td>".$row["Variante"]."</td>";
	    echo "<td>".$row["Bezeichnung"]."</td>";
	    echo "<td class='col-md-2'><textarea class='form-control' style='width: 100%;'>".$row["Kurzbeschreibung"]."</textarea></td>";
  	    echo "<td>";
   	    	if($row["Standort"]==1){
   	    		echo "Ja";
   	    	}
   	    	else{
   	    		echo "Nein";
   	    	}
   	    echo "</td>";

   	    echo "<td>";
   	    	if($row["CAD_notwendig"]==1){
   	    		echo "Ja";
   	    	}
   	    	else{
   	    		echo "Nein";
   	    	}
   	    echo "</td>";
   	    echo "<td>";
   	    	if($row["CAD_familie_vorhanden"]==1){
   	    		echo "Ja";
   	    	}
   	    	else{
   	    		echo "Nein";
   	    	}
   	    echo "</td>";
   	    echo "<td>";
   	    	if($row["CAD_familie_kontrolliert"]==0){
   	    		echo "Nicht geprüft";
   	    	}
   	    	else{
   	    		if($row["CAD_familie_kontrolliert"]==1){
	   	    		echo "Freigegeben";
	   	    	}
				else{
	   	    		echo "Überarbeiten";
	   	    	}
   	    	}
   	    echo "</td>";

		echo "<td>";
   	    	if($row["CAD_dwg_vorhanden"]==1){
   	    		echo "Ja";
   	    	}
   	    	else{
   	    		echo "Nein";
   	    	}
   	    echo "</td>";
	    echo "<td class='col-md-2'><textarea class='form-control' style='width: 100%;'>".$row["CAD_Kommentar"]."</textarea></td>";
	    echo "</tr>";
	    
	}
	echo "</tbody></table>";
	$mysqli ->close();
	
?>
<script>
    
	$(document).ready(function() {
		 $('#tableRoomElements').DataTable( {
			"paging": true,
	        "pagingType": "simple_numbers",
	        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}	        
	    } ); 	
	} );

</script>

</body>
</html>