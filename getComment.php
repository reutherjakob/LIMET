<?php
// 25 Fx
require_once 'utils/_utils.php';
check_login();

$id = getPostInt("commentID");

$mysqli = utils_connect_sql();
if ($id <> 0) {
    $sql = "SELECT tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
			FROM tabelle_räume_has_tabelle_elemente
			WHERE tabelle_räume_has_tabelle_elemente.id= ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $row = $result->fetch_assoc();
    echo br2nl($row["Kurzbeschreibung"]);
    $mysqli->close();
} else {
    echo "Failed.";
}
