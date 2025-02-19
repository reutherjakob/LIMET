<?php
session_start();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /></head>
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
	
	
	// Abfrage der Bestands-Element-Einträge     
        $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_bestandsdaten.`Aktueller Ort`, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.Anzahl
                FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.`Raumbereich Nutzer`)='".filter_input(INPUT_GET, 'raumbereich')."') AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=".filter_input(INPUT_GET, 'elementID')."))
                ORDER BY tabelle_räume.Raumnr;";
	
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-bordered table-sm' id='tableRoombookBestand' cellspacing='0'>
	<thead><tr>
	<th>Raumnummer</th>
	<th>Raumbezeichnung</th>
        <th>Stk im Raum</th>
	<th>Inventarnummer</th>
	<th>Seriennummer</th>
	<th>Anschaffungsjahr</th>
	<th>Gerät</th>
        <th>Standort aktuell</th>
	</tr></thead>
	<tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["Raumnr"]."</td>";
            echo "<td>".$row["Raumbezeichnung"]."</td>";
            echo "<td>".$row["Anzahl"]."</td>";
            echo "<td>".$row["Inventarnummer"]."</td>";
            echo "<td>".$row["Seriennummer"]."</td>";
            echo "<td>".$row["Anschaffungsjahr"]."</td>";
            echo "<td>".$row["Hersteller"]." - ".$row["Typ"]."</td>";     
            echo "<td>".$row["Aktueller Ort"]."</td>";
	    echo "</tr>";
	    
	}
	
	echo "</tbody></table>";
	$mysqli ->close();
	?>
		
<script>
    
    
	$(document).ready(function() {    
	   $("#tableRoombookBestand").DataTable( {
                "paging": true,
                "searching": true,
                "info": true,	
                "columnDefs": [
                    {
                        "targets": [ 2 ],
                        "visible": false,
                        "searchable": false
                    }],
	        "pagingType": "simple",
	        "lengthChange": false,
                "pageLength": 10,
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
	             
	    } );
    
    	var table = $('#tableRoombookBestand').DataTable();
 
	    $('#tableRoombookBestand tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	        }
	    } ); 
	    		
	} );
</script>

</body>
</html>