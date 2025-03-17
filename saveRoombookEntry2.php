<?php
session_start();
?>

<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
    if ($mysqli ->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        echo "Error loading character set utf8: " . $mysqli->error;
        exit();
    } 


    $sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
                    SET
                    `Anzahl` = '".filter_input(INPUT_GET, 'amount')."',
                    `Kurzbeschreibung` = '".filter_input(INPUT_GET, 'comment')."',
                    `Timestamp` = '".date("Y-m-d H:i:s")."'
                    WHERE `id` = ".filter_input(INPUT_GET, 'id').";";

    if ($mysqli ->query($sql) === TRUE) {
        echo "Raumbucheintrag erfolgreich aktualisiert!";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }

    $mysqli ->close();						
?>
