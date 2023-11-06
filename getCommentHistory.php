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
	
        $sql = "SELECT tabelle_rb_aenderung.Kurzbeschreibung, tabelle_rb_aenderung.Kurzbeschreibung_copy1, tabelle_rb_aenderung.Timestamp, tabelle_rb_aenderung.user
                FROM tabelle_rb_aenderung
                WHERE (((tabelle_rb_aenderung.id)=".filter_input(INPUT_GET, 'roombookID')."))
                ORDER BY tabelle_rb_aenderung.Timestamp DESC;";
        $result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-bordered table-sm' id='historyTable' cellspacing='0' width='100%'>
	<thead><tr>
	<th>Datum</th>
	<th>user</th>
	<th>Alt</th>
	<th>Neu</th>
	</tr></thead>
	<tbody>";
	
	
	
	while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td >".$row["Timestamp"]."</td>";
            echo "<td >".$row["user"]."</td>";
	    echo "<td >".$row["Kurzbeschreibung"]."</td>";
	    echo "<td >".$row["Kurzbeschreibung_copy1"]."</td>";
	    echo "</tr>";
	    
	}
	echo "</tbody></table>";
	$mysqli ->close();
	?>
	
	
	
	
<script>	
   
    //$(document).ready(function(){   		
        $("#historyTable").DataTable( {
             "paging": false,
             "order": [[ 0, "desc" ]],
             "searching": true,
             "info": false,
             "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}             			
         } );
    //} );    
	 
</script>

</body>
</html>