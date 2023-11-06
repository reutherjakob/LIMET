<?php
session_start();
?>

<!DOCTYPE html>
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
	//$verbindung = mysql_connect("localhost", $_SESSION["username"], $_SESSION["password"])
	//or die("Verbindung zur Datenbank konnte nicht hergestellt werden");
	//mysql_select_db("LIMET_RB") or die ("Datenbank konnte nicht gefunden werden");
				
	$sql="SELECT view_Projekte.idTABELLE_Projekte, view_Projekte.Interne_Nr, view_Projekte.Projektname, view_Projekte.Aktiv, view_Projekte.Neubau, view_Projekte.Bettenanzahl, view_Projekte.BGF, view_Projekte.NF FROM view_Projekte INNER JOIN tabelle_planungsphasen ON view_Projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen ORDER BY view_Projekte.Interne_Nr";
	//$result = mysql_query($sql);
	$result = $mysqli->query($sql);
	
	echo "<table>
	<tr>
	<th>Projekt auswählen</th>
	<th>idTABELLE_Projekte</th>
	<th>Interne_Nr</th>
	<th>Projektname</th>
	<th>Aktiv</th>
	<th>Neubau</th>
	<th>Bettenanzahl</th>
	<th>BGF</th>
	<th>NF</th>
	</tr>";
	

	//while($row = mysql_fetch_object($result)) {
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td><form action='javascript:showRooms()'><input type='submit' id='select' value='".$row["idTABELLE_Projekte"]."'></form></td>";
	    echo "<td>".$row["idTABELLE_Projekte"]."</td>";
	    echo "<td>".$row["Interne_Nr"]."</td>";
	    echo "<td>".$row["Projektname"]."</td>";
	    echo "<td>".$row["Aktiv"]."</td>";
	    echo "<td>".$row["Neubau"]."</td>";
	    echo "<td>".$row["Bettenanzahl"]."</td>";
	    echo "<td>".$row["BGF"]."</td>";
	    echo "<td>".$row["NF"]."</td>";
	    echo "</tr>";
	    
	}
	echo "</table>";
	
?>
 

</body>
</html>