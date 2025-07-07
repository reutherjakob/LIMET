<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

$mysqli = utils_connect_sql();

if (filter_input(INPUT_GET, 'room') == '0') {
    $roomID = "NULL";
} else {
    $roomID = filter_input(INPUT_GET, 'room');
}


if (filter_input(INPUT_GET, 'los') == '0') {
    $losID = "NULL";
} else {
    $losID = filter_input(INPUT_GET, 'los');
}

$sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerke`
                (`tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe`,
                `tabelle_räume_idTABELLE_Räume`,
                `tabelle_lose_extern_idtabelle_Lose_Extern`,
                `Ersteller`,
                `Erstellungszeit`,
                `Vermerktext`,
                `Bearbeitungsstatus`,
                `Faelligkeit`,
                `Vermerkart`)
                VALUES
                (" . filter_input(INPUT_GET, 'untergruppenID') . ",
                " . $roomID . ",
                " . $losID . ",
                '" . $_SESSION["username"] . "',
                '" . date("Y-m-d H:i:s") . "',
                '" . filter_input(INPUT_GET, 'vermerkText') . "',
                '" . filter_input(INPUT_GET, 'vermerkStatus') . "',
                '" . filter_input(INPUT_GET, 'faelligkeitDatum') . "',
                '" . filter_input(INPUT_GET, 'vermerkTyp') . "');";

if ($mysqli->query($sql) === TRUE) {
    echo "Vermerk hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();
?>
