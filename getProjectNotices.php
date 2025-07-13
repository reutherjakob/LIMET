<?php
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT n.idtabelle_notizen, n.Datum, n.Kategorie, n.User, n.Notiz_bearbeitet, 
               r.Raumnr, r.Raumbezeichnung, r.`Raumbereich Nutzer`
        FROM tabelle_räume r
        INNER JOIN tabelle_notizen n ON r.idTABELLE_Räume = n.tabelle_räume_idTABELLE_Räume
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
        ORDER BY n.Datum DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-condensed' id='tableProjectNotices'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Datum</th>
            <th>Status</th>
            <th>Kategorie</th>
            <th>User</th>
            <th>Raumbereich Nutzer</th>
            <th>Raumnr</th>
            <th>Raumbezeichnung</th>
        </tr>
    </thead>
    <tbody>";

while ($row = $result->fetch_assoc()) {
    $status = $row["Notiz_bearbeitet"] == 0 ? "Offen" : ($row["Notiz_bearbeitet"] == 1 ? "Bearbeitet" : "Info");
    echo "<tr>
        <td>" . htmlspecialchars($row["idtabelle_notizen"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["Datum"] ?? '') . "</td>
        <td>" . htmlspecialchars($status) . "</td>
        <td>" . htmlspecialchars($row["Kategorie"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["User"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["Raumbereich Nutzer"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["Raumnr"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["Raumbezeichnung"] ?? '') . "</td>
    </tr>";
}
echo "</tbody></table>";

$stmt->close();
$mysqli->close();
?>

<script>
    // ... (JavaScript code remains unchanged)
</script>
