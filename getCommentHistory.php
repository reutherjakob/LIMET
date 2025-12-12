<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$roombookID = getPostInt('roombookID');

$stmt = $mysqli->prepare("
    SELECT 
        Kurzbeschreibung,
        Kurzbeschreibung_copy1,
        Timestamp,
        user,
        Anzahl,
        Anzahl_copy1
    FROM tabelle_rb_aenderung
    WHERE id = ?
      AND (
          Kurzbeschreibung <> Kurzbeschreibung_copy1
          OR Anzahl <> Anzahl_copy1
          OR (Kurzbeschreibung IS NULL AND Kurzbeschreibung_copy1 IS NOT NULL)
          OR (Kurzbeschreibung IS NOT NULL AND Kurzbeschreibung_copy1 IS NULL)
          OR (Anzahl IS NULL AND Anzahl_copy1 IS NOT NULL)
          OR (Anzahl IS NOT NULL AND Anzahl_copy1 IS NULL)
      )
    ORDER BY Timestamp DESC");

$stmt->bind_param("i", $roombookID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='historyTable'>
    <thead><tr>
    <th>Datum</th>
    <th>user</th>
    <th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Kommentar'><i class='far fa-comments'></i></div>-Alt</th>
    <th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Kommentar'><i class='far fa-comments'>-Neu</i></div></th>
    <th>Anzahl-Alt</th>
    <th>Anzahl-Neu</th>
    </tr></thead>
    <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["Timestamp"] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["user"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Kurzbeschreibung"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Kurzbeschreibung_copy1"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Anzahl"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Anzahl_copy1"]?? '') . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
$stmt->close();
$mysqli->close();
