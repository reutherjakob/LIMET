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
																	
	$sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_projekt_varianten_kosten.Kosten*Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS PP,tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
			FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_varianten INNER JOIN (tabelle_lose_extern RIGHT JOIN ((tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
			WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
			GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
			ORDER BY tabelle_elemente.ElementID;";
	
	$result = $mysqli->query($sql);
							
	echo "<table class='table table-striped table-bordered nowrap table-condensed' id='tableElementsInProject'  cellspacing='0' width='100%'>
		<thead><tr>
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
		</tr>
		</thead>
		<!--<tfoot>
                    <tr>
                        <th colspan='11' style='text-align:right'>Schätzsumme inkl. Bestand:</th>
                        <th></th>
                    </tr>
                </tfoot>
                -->
		<tbody>";
		
		while($row = $result->fetch_assoc()) {
		    echo "<tr>";
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
		    setlocale(LC_MONETARY,"de_DE");
		    echo "<td>".$row["Kosten"]."</td>";	
		    echo "<td>".$row["PP"]."</td>";	
		    echo "<td>".$row["LosNr_Extern"]."</td>";	
		    echo "<td>".$row["LosBezeichnung_Extern"]."</td>";	
		    echo "<td>".$row["Ausführungsbeginn"]."</td>";	
		    echo "</tr>";
		    						    
		}
		echo "</tbody></table>";
		$mysqli ->close();
	?>
	
<script>
    
    var searchV = '<?php echo $_GET["searchValue"] ;?>';    
    
	$(document).ready(function() {
		$('#tableElementsInProject').DataTable( {
			"paging": true,
			"order": [[ 5 , "asc" ]],
			"columnDefs": [
	            {
	                "targets": [ 0 ],
	                "visible": false,
	                "searchable": false
	            },
	             {
	                "targets": [ 1 ],
	                "visible": false,
	                "searchable": false
	            },
				{
	                "targets": [ 2 ],
	                "visible": false,
	                "searchable": false
	            },
				{
	                "targets": [ 3 ],
	                "visible": false,
	                "searchable": false 
	        ], 
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
	        //"scrollY":        '20vh',
	    	//"scrollCollapse": true,
	    	"search": {search:searchV},
                "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
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
		                .column( 11 )
		                .data()
		                .reduce( function (a, b) {
		                    return intVal(a) + intVal(b);
		                }, 0 );
		 
		            // Total over this page
		            pageTotal = api
		                .column( 11, { page: 'current'} )
		                .data()
		                .reduce( function (a, b) {
		                    return intVal(a) + intVal(b);
		                }, 0 );
		 
		            // Update footer
		            $( api.column( 11 ).footer() ).html(
		                '€ '+pageTotal +' ( € '+ total +' total)'
		            );
		        }	  	 	        			     
	    } );
	    
	    var table = $('#tableElementsInProject').DataTable();
 
	    $('#tableElementsInProject tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            var elementID = table.row( $(this) ).data()[0];	
	            var variantenID = table.row( $(this) ).data()[1];	
	            var losID = table.row( $(this) ).data()[2];	
	            var bestand  = table.row( $(this) ).data()[3];	
	            var raumbereich  = table.row( $(this) ).data()[8];

				
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
        
        /*
        $(document).ready(function() {
            if(ext === '0'){
		$('#tableElementsInProject').DataTable( {
			"paging": true,
			"order": [[ 6, "asc" ]],
			"columnDefs": [
                            {
                                "targets": [ 1,2,3,4,15 ],
                                "visible": false,
                                "searchable": false
                            },
                            {
                                className: 'control',
                                orderable: false,
                                targets:   0
                            }

	        ],
                "responsive": {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                "search": {search:searchV},
	        "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
	        //"scrollY":        '20vh',
	    	//"scrollCollapse": true,
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
                                
		        }	   	 
                } );
            }
            else{
                $('#tableElementsInProject').DataTable( {
			"paging": true,
			"order": [[ 6, "asc" ]],
			"columnDefs": [
	            {
	                "targets": [ 1,2,3,4,11,12,15 ],
	                "visible": false,
	                "searchable": false
	            },
                    {
                        className: 'control',
                        orderable: false,
                        targets:   0
                    }

	        ],
                "responsive": {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
	        "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
	        //"scrollY":        '20vh',
	    	//"scrollCollapse": true	   	 
                } );
            }
	    
	    var table = $('#tableElementsInProject').DataTable();
 
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
        */

</script>

</body>
</html>