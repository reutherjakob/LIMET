<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Bestand</title>
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
</head>

    
<body style="height:100%">
 
<div class="container-fluid" >
        <div id="limet-navbar"></div> 
	<div class="mt-4 card">
                <div class="card-header">Elemente im Bestand</div>
                <div class="card-body">
                    <?php
                            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                            /* change character set to utf8 */
                            if (!$mysqli->set_charset("utf8")) {
                                printf("Error loading character set utf8: %s\n", $mysqli->error);
                                exit();
                            }

                            // Abfrage der Bestandseinträge gruppiert nach Raumbereichen
                            $sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                                    FROM tabelle_elemente INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                                    GROUP BY tabelle_räume.tabelle_projekte_idTABELLE_Projekte, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                                    HAVING (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0))
                                    ORDER BY tabelle_elemente.ElementID;";

                            $result = $mysqli->query($sql);

                            echo "<table class='table table-striped table-bordered table-sm' id='tableBestandsElemente'  cellspacing='0' width='100%'>
                            <thead><tr>
                            <th>ID</th>
                            <th>Stk</th>
                            <th>ID</th>
                            <th>Element</th>
                            <th>Raumbereich</th>
                            </tr></thead>
                            <tbody>";


                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>".$row["TABELLE_Elemente_idTABELLE_Elemente"]."</td>";
                                echo "<td>".$row["SummevonAnzahl"]."</td>";
                                echo "<td>".$row["ElementID"]."</td>";
                                echo "<td>".$row["Bezeichnung"]."</td>";
                                echo "<td>".$row["Raumbereich Nutzer"]."</td>";
                                echo "</tr>";

                            }
                            echo "</tbody></table>";

                    ?>	  
		</div>
	</div>
        <div class="mt-4 card">
                <div class="card-header">Bestandsdaten für ausgewähltes Element/Raumbereich</div>
                <div class="card-body" id="bestandsRoombook"></div>
        </div>
        
</div>
</body>
<script>

	// Tabelle formatieren
	$(document).ready(function(){		
		$('#tableBestandsElemente').DataTable( {
			"columnDefs": [
                            {
                                "targets": [ 0 ],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        "select": true,
			"paging": true,
			"searching": true,
			"info": true,
			"order": [[ 1, "asc" ]],
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
                             
	    } );	    	  
		
		
            // CLICK TABELLE 
	    var table1 = $('#tableBestandsElemente').DataTable();
            
	    $('#tableBestandsElemente tbody').on( 'click', 'tr', function () {
			
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table1.$('tr.info').removeClass('info');
	            $(this).addClass('info');
                    
	            var elementID  = table1.row( $(this) ).data()[0];
                    var raumbereich  = table1.row( $(this) ).data()[4];
                    
                    $.ajax({
                        url : "getBestandWithRaumbereich.php",
                        data:{"elementID":elementID,"raumbereich":raumbereich},
                        type: "GET",
                        success: function(data){
                            $("#bestandsRoombook").html(data);
                        }
                    });
                    
	        }
	    } );	    
	});     
</script>


</html>
