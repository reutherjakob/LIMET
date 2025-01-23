<?php
include "_utils.php";
check_login();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>


<?php
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_rb_aenderung.Kurzbeschreibung,
        tabelle_rb_aenderung.Kurzbeschreibung_copy1,
        tabelle_rb_aenderung.Timestamp,
        tabelle_rb_aenderung.user,
        tabelle_rb_aenderung.Anzahl,
        tabelle_rb_aenderung.Anzahl_copy1
                FROM tabelle_rb_aenderung
                WHERE ((tabelle_rb_aenderung.id)=" . filter_input(INPUT_GET, 'roombookID') . ")
                ORDER BY tabelle_rb_aenderung.Timestamp DESC;";
$result = $mysqli->query($sql);

echo "<table class='table table-striped table-bordered table-sm' id='historyTable' cellspacing='0' width='100%'>
	<thead><tr>
	<th>Datum</th>
	<th>user</th>
	<th>Kommentar-Alt</th>
	<th>Kommentar-Neu</th>
	<th>Anzahl-Alt</th>
	<th>Anzahl-Neu</th>
	</tr></thead>
	<tbody>";


while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td >" . $row["Timestamp"] . "</td>";
    echo "<td >" . $row["user"] . "</td>";
    echo "<td >" . $row["Kurzbeschreibung"] . "</td>";
    echo "<td >" . $row["Kurzbeschreibung_copy1"] . "</td>";
    echo "<td >" . $row["Anzahl"] . "</td>";
    echo "<td >" . $row["Anzahl_copy1"] . "</td>";
    echo "</tr>";

}
echo "</tbody></table>";
$mysqli->close();
?>


<script>

    //$(document).ready(function(){
    $("#historyTable").DataTable({
        "paging": false,
        "order": [[0, "desc"]],
        "searching": true,
        "info": false,
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
    });
    //} );

</script>

</body>
</html>