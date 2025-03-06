<?php
include "_utils.php";
check_login();


//echo $_GET["Notiz"]." ".date('Y-m-d')." ".$_SESSION["username"]." ".$_GET["Kategorie"]." ".$_GET["roomID"];

if (filter_input(INPUT_GET, 'firma') != "" && filter_input(INPUT_GET, 'lieferantTel') != "" && filter_input(INPUT_GET, 'lieferantAdresse') != "" && filter_input(INPUT_GET, 'lieferantPLZ') !== "" && filter_input(INPUT_GET, 'lieferantOrt') !== "" && filter_input(INPUT_GET, 'lieferantLand') !== "") {


    $mysqli = utils_connect_sql();
    $sql = "INSERT INTO `tabelle_lieferant`
				(`Lieferant`,
				`Tel`,
				`Anschrift`,
				`PLZ`,
				`Ort`,
				`Land`)
				VALUES
				('" . filter_input(INPUT_GET, 'firma') . "',
				'" . filter_input(INPUT_GET, 'lieferantTel') . "',
				'" . filter_input(INPUT_GET, 'lieferantAdresse') . "',
				'" . filter_input(INPUT_GET, 'lieferantPLZ') . "',
				'" . filter_input(INPUT_GET, 'lieferantOrt') . "',
				'" . filter_input(INPUT_GET, 'lieferantLand') . "');";

    if ($mysqli->query($sql) === TRUE) {
        echo "Lieferant hinzugefügt!";
        $id = $mysqli->insert_id;
    } else {
        echo "Error1: " . $sql . "<br>" . $mysqli->error;
    }

    $mysqli->close();
} else {
    echo "Fehler bei der Verbindung";
}
?>
