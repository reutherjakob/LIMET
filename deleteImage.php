<?php
session_start();
?>

<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
?>

<?php
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	
	// Check connection
	if ($mysqli->connect_error) {
	    die("Connection failed: " . $mysqli->connect_error);
	}
        
        $sql = "SELECT `tabelle_Files`.`Name`
                FROM 
                    `LIMET_RB`.`tabelle_Files`
                WHERE `tabelle_Files`.`idtabelle_Files`= ".filter_input(INPUT_GET, 'imageID').";";
        $result = $mysqli->query($sql);
        while ($row = $result->fetch_assoc()) {
            $imageName = $row['Name'];
        }
        
        //Path
        $target_dir = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Images/".$imageName; 
        
        // Use unlink() function to delete a file
        if (!unlink($target_dir)) {
            echo ("Fehler beim Löschen!");
        }
        else {
            echo ("Datei gelöscht! ");
            $sqlDelete = "DELETE FROM `LIMET_RB`.`tabelle_Files`
                        WHERE `idtabelle_Files`=".filter_input(INPUT_GET, 'imageID').";";
            
            if ($mysqli->query($sqlDelete) === TRUE) {
                echo "Datenbank aktualisiert!";
            } 
            else {
                echo "Error: " . $sql . "<br>" . $mysqli->error;
            }
        }
        
	$mysqli ->close();
?>
