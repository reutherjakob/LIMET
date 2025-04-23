<?php
    session_start();
    if(!isset($_SESSION["username"]))
    {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
    
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

    /* change character set to utf8 */


    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    $idRechnung = filter_input(INPUT_GET, 'idRechnung');        
    // destination of the file on the server
    $file_path = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Rechnungen/Rechnung_".$idRechnung.".pdf";
    echo $file_path;
    /* Delete file if its exist in folder */    
    if (file_exists($file_path)) {
        unlink($file_path);
        echo "Datei gelöscht! \n";
        
        // Abfrage der FileID
        $sql = "SELECT tabelle_rechnungen.tabelle_Files_idtabelle_Files
                FROM tabelle_rechnungen
                WHERE tabelle_rechnungen.idtabelle_rechnungen=".$idRechnung.";";
        
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        $idFile = $row["tabelle_Files_idtabelle_Files"];
        
        $sql_Update = "UPDATE `LIMET_RB`.`tabelle_rechnungen`
                    SET
                    `tabelle_Files_idtabelle_Files` = NULL
                    WHERE `idtabelle_rechnungen` = ".$idRechnung.";";

        if ($mysqli->query($sql_Update) === TRUE) {
            echo " Datei von Rechnung entfernt!";
        } 
        else {
            echo " Error: " .$mysqli->error;
        }
        
        $sql_Delete = "DELETE FROM `LIMET_RB`.`tabelle_Files`
                    WHERE
                    `idtabelle_Files` = ".$idFile.";";

        if ($mysqli->query($sql_Delete) === TRUE) {
            echo " File gelöscht!";
        } 
        else {
            echo " Error: " .$mysqli->error;
        }
      
      
    } 
    /*
    $sql = "DELETE FROM `LIMET_RB`.`tabelle_projekte_has_tabelle_ansprechpersonen`
                    WHERE `TABELLE_Projekte_idTABELLE_Projekte` = ".$_SESSION["projectID"]." AND `TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen` = ".$_GET["personID"].";";

    if ($mysqli->query($sql) === TRUE) {
        echo "Person erfolgreich von Projekt entfernt!"; 
    } 
    else {
        echo "Error1: " . $sql . "<br>" . $mysqli->error;
    }
     * 
     */

    $mysqli ->close();
?>