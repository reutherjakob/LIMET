<?php
include_once("utils/_utils.php");
check_login();

$deviceID = isset($_SESSION["deviceID"]) ? intval($_SESSION["deviceID"]) : 0;
$lieferantenID = filter_input(INPUT_POST, 'lieferantenID', FILTER_VALIDATE_INT);

if ($deviceID > 0 && $lieferantenID > 0) {
    $mysqli = utils_connect_sql();

    // Prepare and execute the insert to link device and lieferant
    $sql = "INSERT INTO LIMET_RB.tabelle_geraete_has_tabelle_lieferant 
            (tabelle_geraete_idTABELLE_Geraete, tabelle_lieferant_idTABELLE_Lieferant)
            VALUES (?, ?)";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => "Prepare failed: " . $mysqli->error]);
        exit;
    }

    $stmt->bind_param("ii", $deviceID, $lieferantenID);

    if ($stmt->execute()) {


        echo json_encode(['id' => $lieferantenID]);

    } else {
        http_response_code(500);
        echo json_encode(['error' => "Insert failed: " . $stmt->error]);
    }

    $stmt->close();

    $mysqli->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => "Ungültige Parameter oder Session"]);
}
