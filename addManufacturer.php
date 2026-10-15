<?php
// 26 FX
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

if (isset($_POST['manufacturer']) && trim($_POST['manufacturer']) !== '') {
    $manufacturer = getPostString('manufacturer');

    $mysqli =utils_connect_sql();

    $stmt = $mysqli->prepare("INSERT INTO tabelle_hersteller (Hersteller) VALUES (?)");
    $stmt->bind_param("s", $manufacturer);

    if ($stmt->execute()) {
        echo "Hersteller hinzugefügt!";
        $id = $mysqli->insert_id;
    } else {
        echo "Fehler: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
} else {
    echo "Kein Hersteller übertragen!";
}
?>
