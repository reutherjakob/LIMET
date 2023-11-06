<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
    if ($mysqli ->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $mysqli->query("SET NAMES 'utf8'");

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        echo "Error loading character set utf8: " . $mysqli->error;
        exit();
    } 		
        
    $sql = "UPDATE `LIMET_RB`.`tabelle_rechnungen`
            SET
            `Nummer` = '".filter_input(INPUT_GET, 'rechnungNr')."',
            `InterneNummer` = '".filter_input(INPUT_GET, 'teilRechnungNr')."',
            `Ausstellungsdatum` = '".filter_input(INPUT_GET, 'rechnungAusstellungsdatum')."',
            `Eingangsdatum` = '".filter_input(INPUT_GET, 'rechnungEingangsdatum')."',
            `Rechnungssumme` = '".filter_input(INPUT_GET, 'rechnungSum')."',
            `Bearbeiter` = '".filter_input(INPUT_GET, 'rechnungBearbeiter')."',
            `Schlussrechnung` = ".filter_input(INPUT_GET, 'rechnungSchlussrechnung')."
            WHERE `idtabelle_rechnungen` = '".filter_input(INPUT_GET, 'rechnungID')."';";      
    
    if ($mysqli->query($sql) === TRUE) {
        $ausgabe = "Rechnung erfolgreich aktualisiert! \n";
    } 
    else {
        $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
    }		
    
    $mysqli ->close();
    echo $ausgabe;
?>
