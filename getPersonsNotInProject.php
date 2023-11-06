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
		$sql="select * from tabelle_ansprechpersonen a where a.idTABELLE_Ansprechpersonen 
		not in (select a1.idTABELLE_Ansprechpersonen from tabelle_ansprechpersonen a1 inner join tabelle_projekte_has_tabelle_ansprechpersonen 
		ap on a1.idTABELLE_Ansprechpersonen = ap.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen 
		where ap.TABELLE_Projekte_idTABELLE_Projekte = ".$_SESSION["projectID"]." ORDER BY a.Name);";
						
		$result = $mysqli->query($sql);
		
		echo "<table class='table table-striped table-bordered table-sm' id='tablePersonsNotInProject'  cellspacing='0' width='100%'>
		<thead><tr>
		<th>ID</th>
		<th>Name</th>
		<th>Vorname</th>
		<th>Tel</th>
		<th>Adresse</th>
		<th>PLZ</th>
		<th>Ort</th>
		<th>Land</th>
		<th>Mail</th>
		</tr></thead>
		<tbody>";
		
	
		while($row = $result->fetch_assoc()) {
		    echo "<tr>";
		    echo "<td>".$row["idTABELLE_Ansprechpersonen"]."</td>";
		    echo "<td>".$row["Name"]."</td>";
		    echo "<td>".$row["Vorname"]."</td>";
		    echo "<td>".$row["Tel"]."</td>";
		    echo "<td>".$row["Adresse"]."</td>";
                    echo "<td>".$row["PLZ"]."</td>";
                    echo "<td>".$row["Ort"]."</td>";
                    echo "<td>".$row["Land"]."</td>";
                    echo "<td>".$row["Mail"]."</td>";
		    echo "</tr>";
		}
		echo "</tbody></table>";
	}
	
	$mysqli ->close();
?>

<script>

	// Tabelle formatieren
	$(document).ready(function(){		
		$('#tablePersonsNotInProject').DataTable( {
			"columnDefs": [
	            {
	                "targets": [ 0 ],
	                "visible": false,
	                "searchable": false
	            }
	        ],
                "select": true,
                "paging": true,
                "order": [[ 1, "asc" ]],
	        "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,	       
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}		     
	    } );
		
		var table = $('#tablePersonsNotInProject').DataTable();
 
	    $('#tablePersonsNotInProject tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	                     
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');	
		           
				 $.ajax({
			        url : "getAddPersonToProjectField.php",
			        data:{"personID":table.row( $(this) ).data()[0]},
			        type: "GET",
			        success: function(data){
			            $("#addPersonToProject").html(data);
			        } 
		        });
           		
	        }
	    } );

	});
			
</script>


</body>
</html>