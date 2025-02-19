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
	
	$sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Geschoss, tabelle_räume.`Raumbereich Nutzer`
			FROM tabelle_räume INNER JOIN (tabelle_verwendungselemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_verwendungselemente.id_Verwendungselement = tabelle_räume_has_tabelle_elemente.id) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
			WHERE (((tabelle_verwendungselemente.id_Standortelement)=".$_GET["id"]."));";
	
	    
	$result = $mysqli->query($sql);
	
	echo "<div class='table-responsive'><table class='table table-striped table-sm' id='tableElementVerwendungsdaten' cellspacing='0'>
	<thead><tr>
	<th>Raumnr</th>
	<th>Raumbezeichnung</th>
	<th>Geschoss</th>
	<th>Raumbereich Nutzer</th>
	</tr></thead>
	<tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["Raumnr"]."</td>";
	    echo "<td>".$row["Raumbezeichnung"]."</td>";
            echo "<td>".$row["Geschoss"]."</td>";
            echo "<td>".$row["Raumbereich Nutzer"]."</td>";
	    echo "</tr>";
	    
	}
	
	echo "</tbody></table></div>";
	
	$mysqli ->close();
	?>
	
<script>
    
	    
   $("#tableElementVerwendungsdaten").DataTable( {
		"paging": false,
		"searching": false,
		"info": false,
        //"pagingType": "simple_numbers",
        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
        "scrollY":        '20vh',
    	"scrollCollapse": true   		     
    } );


</script>

</body>
</html>