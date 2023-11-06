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
    
    
    $sql1 = "UPDATE `LIMET_RB`.`tabelle_lot_workflow`
        SET
        `Timestamp_Soll` = '".filter_input(INPUT_GET, 'date')."'  
        WHERE 
        `tabelle_lose_extern_idtabelle_Lose_Extern` = ".filter_input(INPUT_GET, 'lotID')." 
        AND 
        `tabelle_workflow_idtabelle_workflow` = ".filter_input(INPUT_GET, 'workflowID')." 
        AND
        `tabelle_wofklowteil_idtabelle_wofklowteil` = ".filter_input(INPUT_GET, 'workflowTeilID').";";

    if ($mysqli->query($sql1) === TRUE) {
        $ausgabe = "Soll-Datum erfolgreich aktualisiert!";
    } 
    else {
        $ausgabe = " Error: " . $sql . "<br>" . $mysqli->error;
    }

                       
    $mysqli ->close();
    echo $ausgabe;
?>
