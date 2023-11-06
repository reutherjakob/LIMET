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

    //Raumdaten updaten
    $elementIDs = $_GET["elements"];
    $ausgabe = "";
    foreach ($elementIDs as $valueOfElementID) {
        $sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
                SET
                `Lieferdatum` = '".filter_input(INPUT_GET, 'lieferdatum')."' WHERE `id` = ".$valueOfElementID.";";            

        if ($mysqli->query($sql) === TRUE) {
            $ausgabe = $ausgabe . "Element ".$valueOfElementID." erfolgreich aktualisiert! \n";
        } 
        else {
            $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }				
    $mysqli ->close();
    echo $ausgabe;
?>
