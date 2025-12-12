<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$comment = getPostString("comment");
$roombookID = $_SESSION("roombookID");

if ($comment !== "" && $roombookID !== 0) {
    $mysqli = utils_connect_sql();

    $sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
            SET `Kurzbeschreibung` = ?, `Timestamp` = ?
            WHERE `id` = ?";

    $stmt = $mysqli->prepare($sql);

    // Convert <br> to new lines if needed
    $sanitizedComment = preg_replace('/<br\s*\/?>/i', "\n", $comment);
    $timestamp = date("Y-m-d H:i:s");

    $stmt->bind_param("ssi", $sanitizedComment, $timestamp, $roombookID);

    if ($stmt->execute()) {
        echo "Erfolgreich aktualisiert!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
}
?>
