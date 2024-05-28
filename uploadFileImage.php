<?php           
    session_start();
    if(!isset($_SESSION["username"]))
    {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
    
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
        exit();
    } 
    
    // get File from POST
    $image = $_POST['fileUpload'];
    // get vermerkID from POST
    //$vermerkID = filter_input(INPUT_POST, 'vermerkID');
    // set directory on SERVER
    $target_dir = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Images/Image_".$_SESSION["projectID"]."_"; //Dateiname beginnt mit Image_#ProjektID_
    //replacing some characters from the base64
    $image = str_replace('data:image/jpeg;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    //decoding the base64
    $data = base64_decode($image);    
    //generating and unique name (or write manually one name)
    $unique = uniqid();        
    //setting the path together
    $file = $target_dir.$unique.'.jpeg';
    //putting all the content into a file
    $success = file_put_contents($file, $data);
    
    $filename = "Image_".$_SESSION["projectID"]."_".$unique.".jpeg";
    
    if ($success != false) {
        echo " Bild erfolgreich hochgeladen \n";
        
        // Datenbankeintrag erstellen
        $sql_insert = "INSERT INTO `LIMET_RB`.`tabelle_Files`
                    (`tabelle_projekte_idTABELLE_Projekte`,
                    `tabelle_filetype_id`,                    
                    `Timestamp`,
                    `Name`)
                    VALUES
                    (".$_SESSION["projectID"].",
                    1,
                    now(),
                    '".$filename."');";
        
        if (mysqli_query($mysqli, $sql_insert)) {
            echo " Bild in Datenbank ergänzt!  \n" . $unique. " \n" . $filename;
            /*
            $id = $mysqli->insert_id; 
            
            $sql_insert2 = "INSERT INTO `LIMET_RB`.`tabelle_Files_has_tabelle_Vermerke`
                            (`tabelle_Files_idtabelle_Files`,
                            `tabelle_Vermerke_idtabelle_Vermerke`)
                            VALUES
                            (".$id.",
                             ".$vermerkID.");";
                
            if ($mysqli->query($sql_insert2) === TRUE) {
                echo " Vermerk um Bilddateu ergänzt!";
            } 
            else {
                echo " Error: " .$mysqli->error;
            }*/	
        }
        else{
            echo " Error: " .$mysqli->error;
        }
    } 
    else {
        echo " Fehler beim Bild-Upload!";
    }    

    $mysqli ->close();
?>