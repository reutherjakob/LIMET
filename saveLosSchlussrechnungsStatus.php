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
    
    $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern`
            SET
            `Schlussgerechnet` = ".filter_input(INPUT_GET, 'schlussgerechnet')."
            WHERE `idtabelle_Lose_Extern` = ".filter_input(INPUT_GET, 'lotID').";";
    
    if ($mysqli->query($sql) === TRUE) {
        $ausgabe = "Gewerk erfolgreich aktualisiert! \n";
    } 
    else {
        $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
    }		
    
    $mysqli ->close();
    echo $ausgabe;
?>
