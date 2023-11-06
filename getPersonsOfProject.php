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
		$sql="SELECT tabelle_projektzuständigkeiten.Zuständigkeit, 
		tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen,
		tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, 
		tabelle_ansprechpersonen.Tel, tabelle_ansprechpersonen.Adresse, 
		tabelle_ansprechpersonen.PLZ, tabelle_ansprechpersonen.Ort, 
		tabelle_ansprechpersonen.Land, tabelle_ansprechpersonen.Mail, 
		tabelle_ansprechpersonen.Raumnr, tabelle_organisation.Organisation
		FROM tabelle_ansprechpersonen INNER JOIN (tabelle_projektzuständigkeiten INNER JOIN (tabelle_organisation INNER JOIN tabelle_projekte_has_tabelle_ansprechpersonen ON tabelle_organisation.idtabelle_organisation = tabelle_projekte_has_tabelle_ansprechpersonen.tabelle_organisation_idtabelle_organisation) ON tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten = tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten) ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen
		WHERE (((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."));";
				
		$result = $mysqli->query($sql);
		
		echo "<table class='table table-striped table-bordered table-sm' id='tablePersons'  cellspacing='0' width='100%'>
		<thead><tr>
		<th>ID</th>
		<th>Zuständigkeit</th>
		<th>Name</th>
		<th>Vorname</th>
		<th>Tel</th>
		<th>Adresse</th>
		<th>PLZ</th>
		<th>Ort</th>
		<th>Land</th>
		<th>Mail</th>
		<th>Organisation</th>
                <th>Raumnr</th>
		</tr></thead>
		<tbody>";
		
	
		while($row = $result->fetch_assoc()) {
		    echo "<tr>";
		    echo "<td>".$row["idTABELLE_Ansprechpersonen"]."</td>";
		    echo "<td>".$row["Zuständigkeit"]."</td>";
		    echo "<td>".$row["Name"]."</td>";
		    echo "<td>".$row["Vorname"]."</td>";
		    echo "<td>".$row["Tel"]."</td>";
		    echo "<td>".$row["Adresse"]."</td>";
                    echo "<td>".$row["PLZ"]."</td>";
                    echo "<td>".$row["Ort"]."</td>";
                    echo "<td>".$row["Land"]."</td>";
                    echo "<td>".$row["Mail"]."</td>";
                    echo "<td>".$row["Organisation"]."</td>";
                    echo "<td>".$row["Raumnr"]."</td>";
		    echo "</tr>";
		}
		echo "</tbody></table>";
	}
	
	$mysqli ->close();
?>

<script>
	
	var personID;
	
	// Tabelle formatieren
	$(document).ready(function(){		
		$('#tablePersons').DataTable( {
			"columnDefs": [
	            {
	                "targets": [ 0 ],
	                "visible": false,
	                "searchable": false
	            }
	        ],
                "select":true,
			"paging": true,
			"order": [[ 2, "asc" ]],
	        "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,	        
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}		     
	    } );
	    
	    
	    var table = $('#tablePersons').DataTable();
 
	    $('#tablePersons tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {	                     
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            personID = table.row( $(this) ).data()[0];	
	            $.ajax({
			        url : "getChangePersonToProjectField.php",
			        data:{"personID":personID},
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