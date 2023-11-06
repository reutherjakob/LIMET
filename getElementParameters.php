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
    $sql= "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit
		FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN (tabelle_projekt_elementparameter INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
		WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.id)=".$_GET["id"]."))
		ORDER BY tabelle_parameter_kategorie.Kategorie;";			
		    
	$result = $mysqli->query($sql);
	
	echo "<div class='table-responsive'><table class='table table-striped table-sm' id='tableElementParameters' cellspacing='0'>
	<thead><tr>
	<th>Parameter</th>
	<th>Wert</th>
	<th>Einheit</th>
	</tr></thead>
	<tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["Bezeichnung"]."</td>";
	    echo "<td>".$row["Wert"]."</td>";
		echo "<td>".$row["Einheit"]."</td>";
	    echo "</tr>";
	    
	}
	
	echo "</tbody></table></div>";
	
	$mysqli ->close();
	?>
	
<script>
    
	    
   $("#tableElementParameters").DataTable( {
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