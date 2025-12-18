<?php
// 25 FX
include "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$lieferdatum = getPostDate('lieferdatum');
$elementIDs  = isset($_POST['elements']) && is_array($_POST['elements']) ? $_POST['elements'] : [];

$ausgabe = "";
$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
    SET `Lieferdatum` = ?
    WHERE `id` = ?
");

foreach ($elementIDs as $valueOfElementID) {
    $elementIdInt = (int)$valueOfElementID;
    $stmt->bind_param("si", $lieferdatum, $elementIdInt);

    if ($stmt->execute()) {
        $ausgabe .= "Element " . $elementIdInt . " erfolgreich aktualisiert! \n";
    } else {
        $ausgabe = "Error: " . $stmt->error;
        break;
    }
}

$stmt->close();
$mysqli->close();
echo $ausgabe;