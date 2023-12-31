<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	
	//Elemente im Raum abfragen
	$sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, tabelle_varianten.Variante, tabelle_elemente.Bezeichnung, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete
			FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((tabelle_räume_has_tabelle_elemente LEFT JOIN tabelle_geraete ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
			WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=".$_SESSION["roomID"]."))
			ORDER BY tabelle_elemente.ElementID;";
    
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-bordered table-sm' id='tableRoomElements' cellspacing='0' width='100%'>
	<thead><tr>
	<th>ID</th>
	<th class='cols-md-1'>Stück</th>
	<th>Element</th>
	<th>Variante</th>
	<th>Bestand</th>
	<th>Standort</th>
	<th>Verwendung</th>
	<th>Kommentar</th>
	</tr></thead>
	<tbody>";
		//<th>Geräte ID</th>
	
	
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["id"]."</td>";
	    echo "<td>".$row["Anzahl"]."</td>";
	    echo "<td>".$row["ElementID"]." ".$row["Bezeichnung"]."</td>";
	    echo "<td>".$row["Variante"]."</td>";
	    echo "<td>";
   	    	if($row["Neu/Bestand"]==1){
   	    		echo "Nein";
   	    	}
   	    	else{
   	    		echo "Ja";
   	    	}
   	    echo "</td>";
	    echo "<td>";
   	    	if($row["Standort"]==1){
   	    		echo "Ja";
   	    	}
   	    	else{
   	    		echo "Nein";
   	    	}
   	    echo "</td>";
	    echo "<td>";
   	    	if($row["Verwendung"]==1){
   	    		echo "Ja";
   	    	}
   	    	else{
   	    		echo "Nein";
   	    	}
   	    echo "</td>";

	    echo "<td class='cols-md-2'><textarea id='comment".$row["id"]."' rows='1' style='width: 100%;'>".$row["Kurzbeschreibung"]."</textarea></td>";
	    echo "</tr>";
	    
	}
	
	echo "</tbody></table>";
	
	$mysqli ->close();
	?>
<script>
	
    // Element speichern
	$("input[value='Element auswählen']").click(function(){
	    var id=this.id; 
			
		 $.ajax({
	        url : "getElementParameters.php",
	        data:{"id":id},
	        type: "GET",
	        success: function(data){
	        	$("#elementParameters").html(data);
	        } 
        }); 
        
    });
   $(document).ready(function(){	
    
	   $("#tableRoomElements").DataTable( {
			"paging": false,
                        "select":true,
			"order": [[ 1, "asc" ]],
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                        //"scrollY":        '20vh',
                        //"scrollCollapse": true,
                        "columnDefs": [
        		{ "targets": [ 0 ], "visible": false, "searchable": false  }
        		]
	    } );
	    
	    var table = $('#tableRoomElements').DataTable();
 
	    $('#tableRoomElements tbody').on( 'click', 'tr', function () {
			
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            var raumbuchID = table.row( $(this) ).data()[0];
	            $.ajax({
			        url : "getElementParameters.php",
			        data:{"id":raumbuchID},
			        type: "GET",
			        success: function(data){
			        	$("#elementParameters").html(data);
			        } 
		        }); 

	        }
	    });
	} );


</script>

</body>
</html>