<?php
session_start();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
<head>
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid black;
    padding: 5px;
}

th {text-align: left;}
</style>


</head>
<body>
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"login.php\">einloggen</a>";
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
					
	$sql="SELECT view_Raeume.Raumnr, view_Raeume.Raumbezeichnung, view_Raeume.Nutzfläche FROM view_Raeume";
	$result = $mysqli->query($sql);
	
	echo "<table>
	<tr>
	<th>view_Raeume.Raumnr</th>
	<th>view_Raeume.Raumbezeichnung</th>
	<th>Nutzfläche</th>
	</tr>";
	

	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["Raumnr"]."</td>";
	    echo "<td>".$row["Raumbezeichnung"]."</td>";
	    echo "<td>".$row["Nutzfläche"]."</td>";
	    echo "</tr>";
	}
	echo "</table>";
?>
 

</body>
</html>