<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();
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
