<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
    if ($mysqli ->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    $mysqli->query("SET NAMES 'utf8'");

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        echo "Error loading character set utf8: " . $mysqli->error;
        exit();
    } 		 
    
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_rechnungen`
            (`tabelle_lose_extern_idtabelle_Lose_Extern`,
            `Nummer`,
            `InterneNummer`,
            `Ausstellungsdatum`,
            `Eingangsdatum`,
            `Rechnungssumme`,
            `Bearbeiter`,
            `Schlussrechnung`)
            VALUES
            (".filter_input(INPUT_GET, 'lotID').",
            '".filter_input(INPUT_GET, 'rechnungNr')."',
            '".filter_input(INPUT_GET, 'teilRechnungNr')."',
            '".filter_input(INPUT_GET, 'rechnungAusstellungsdatum')."',
            '".filter_input(INPUT_GET, 'rechnungEingangsdatum')."',
            '".filter_input(INPUT_GET, 'rechnungSum')."',
            '".filter_input(INPUT_GET, 'rechnungBearbeiter')."',
            ".filter_input(INPUT_GET, 'rechnungSchlussrechnung').");";
    
    if ($mysqli->query($sql) === TRUE) {
        $ausgabe = "Rechnung erfolgreich hinzugefügt! \n";
    } 
    else {
        $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
    }		
    
    $mysqli ->close();
    echo $ausgabe;
?>
