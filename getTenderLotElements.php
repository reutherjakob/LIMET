<?php
session_start();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
<head>
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
					
        if($_GET["lotID"] != ""){
		$_SESSION["lotID"]=$_GET["lotID"];
	}
        else{
            echo "Kein Los ausgewählt!";
        }

   
    /*$sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`
            FROM tabelle_varianten INNER JOIN ((tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE tabelle_räume_has_tabelle_elemente.id =12965
            ORDER BY tabelle_räume.Raumnr;";
     * 
     */
    $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                FROM tabelle_varianten INNER JOIN ((tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                WHERE (((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)=".$_SESSION["lotID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
                ORDER BY tabelle_räume.Raumnr;";
    
    $result = $mysqli->query($sql);
    echo "<table class='table table-striped table-bordered table-sm' id='tableLotElements1'  cellspacing='0' width='100%'>
            <thead><tr>
            <th>ID</th>
            <th>elementID</th>
            <th>variantenID</th>
            <th>Stk</th>
            <th>ID</th>
            <th>Element</th>
            <th>Variante</th>
            <th>Bestand</th>
            <th>Raumnr</th>
            <th>Raum</th>
            <th>Kommentar</th>								
            </tr></thead>           
            <tbody>";
        

            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row["id"]."</td>";
                echo "<td>".$row["TABELLE_Elemente_idTABELLE_Elemente"]."</td>";
                echo "<td>".$row["tabelle_Varianten_idtabelle_Varianten"]."</td>";
                echo "<td>".$row["Anzahl"]."</td>";
                echo "<td>".$row["ElementID"]."</td>";
                echo "<td>".$row["Bezeichnung"]."</td>";
                echo "<td>".$row["Variante"]."</td>";	
                echo "<td>";
                switch ($row["Neu/Bestand"]) {
                            case 0:
                                echo "Ja";
                                break;
                            case 1:
                                echo "Nein";
                                break;
                        }									
                echo "</td>";                                                                    
                echo "<td>".$row["Raumnr"]."</td>";									
                echo "<td>".$row["Raumbezeichnung"]."</td>";
                echo "<td><textarea id='comment".$row["id"]."' rows='1' style='width: 100%;'>".$row["Kurzbeschreibung"]."</textarea></td>";
                echo "</tr>";
            }
            
	echo "</tbody></table>";
    $mysqli ->close();
?>



<script>    
        $(document).ready(function() {
            $('#tableLotElements1').DataTable( {
                   "paging": true,
                   "select":true,
                   "columnDefs": [
                       {
                           "targets": [ 0,1,2 ],
                           "visible": false,
                           "searchable": false
                       }
                   ],
                   "searching": true,
                   "info": true,
                   "order": [[ 3, "asc" ]],
                   "pagingType": "simple",
                    "lengthChange": false,
                    "pageLength": 10,
                   "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    dom: 'Blfrtip',
                    "buttons": [
                        'excel'
                    ]
                   //"scrollY":        '40vh',
                   //"scrollCollapse": true  
	    } );
            
            var table = $('#tableLotElements1').DataTable();
 
	    $('#tableLotElements1 tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {	                     
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');	
                    var elementID = table.row( $(this) ).data()[1]; 
                    var variantenID = table.row( $(this) ).data()[2]; 
                    var id = table.row( $(this) ).data()[0];	            	            	
                    var stk = table.row( $(this) ).data()[3];
                                               
                    $.ajax({
                        url : "getVariantenParameters.php",
                        data:{"variantenID":variantenID,"elementID":elementID},
                        type: "GET",
                        success: function(data){
                            $("#elementsvariantenParameterInLot").html(data);
                            $("#elementsvariantenParameterInLot").show();
                            $.ajax({
                                        url : "getElementBestand.php",
                                        data:{"id":id,"stk":stk},
                                        type: "GET",
                                        success: function(data){
                                            $("#elementelementBestandsInLot").html(data);
                                            $("#elementelementBestandsInLot").show();
                                        } 
                                });
                        }
                    });
	        }                
	    } );
        });
        
        // PDF erzeugen
        $('#createLotElementListPDF').click(function(){                  
            window.open('/pdf_createLotElementListPDF.php');
        });

</script> 

</body>
</html>