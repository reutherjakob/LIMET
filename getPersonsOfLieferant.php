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
	// Check connection
	if ($mysqli->connect_error) {
	    die("Connection failed: " . $mysqli->connect_error);
	}
	else{
		// Abfrage der Lieferanten-Kontakte
                $sql="SELECT tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_ansprechpersonen.Tel, tabelle_ansprechpersonen.Adresse, tabelle_ansprechpersonen.PLZ, tabelle_ansprechpersonen.Ort, tabelle_ansprechpersonen.Land, tabelle_ansprechpersonen.Mail, tabelle_abteilung.Abteilung
                        FROM tabelle_abteilung INNER JOIN tabelle_ansprechpersonen ON tabelle_abteilung.idtabelle_abteilung = tabelle_ansprechpersonen.tabelle_abteilung_idtabelle_abteilung
                        WHERE (((tabelle_ansprechpersonen.tabelle_lieferant_idTABELLE_Lieferant)=".filter_input(INPUT_GET, 'lieferantID')."));";						
                $result = $mysqli->query($sql);

                echo "<table class='table table-striped table-bordered table-sm' id='tablePersonsOfLieferant'  cellspacing='0' width='100%'>
                <thead><tr>
                <th>ID</th>
                <th>Name</th>
                <th>Vorname</th>
                <th>Tel</th>
                <th>Mail</th>
                <th>Adresse</th>
                <th>PLZ</th>
                <th>Ort</th>
                <th>Land</th>
                <th>Abteilung</th>
                </tr></thead><tbody>";


                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row["idTABELLE_Ansprechpersonen"]."</td>";
                    echo "<td>".$row["Name"]."</td>";
                    echo "<td>".$row["Vorname"]."</td>";
                    echo "<td>".$row["Tel"]."</td>";
                    echo "<td>".$row["Mail"]."</td>";
                    echo "<td>".$row["Adresse"]."</td>";
                    echo "<td>".$row["PLZ"]."</td>";
                    echo "<td>".$row["Ort"]."</td>";
                    echo "<td>".$row["Land"]."</td>";
                    echo "<td>".$row["Abteilung"]."</td>";                    
                    echo "</tr>";

                }
                echo "</tbody></table>";	
	}
	
	
?>
    
    
<script>
	
	// Tabelle formatieren
	$(document).ready(function(){		
		$('#tablePersonsOfLieferant').DataTable( {
			"columnDefs": [
	            {
	                "targets": [ 0 ],
	                "visible": false,
	                "searchable": false
	            }
	        ],
			"paging": false,
                        "searching": false,
                        "info": true,
			"order": [[ 1, "asc" ]],
	        "pagingType": "simple_numbers",
	        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}		     
	    } );	    		
	});
	
			
</script>


</body>
</html>