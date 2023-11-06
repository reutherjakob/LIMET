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
	
        if(filter_input(INPUT_GET, 'gruppenFortsetzung') != 0){
            echo "Gruppenfortsetzung gewählt!";
        }
        else{
            $sql = "UPDATE `LIMET_RB`.`tabelle_Vermerkgruppe`
                    SET
                    `Gruppenname` = '".filter_input(INPUT_GET, 'gruppenName')."',
                    `Gruppenart` = '".filter_input(INPUT_GET, 'gruppenart')."',
                    `Ort` = '".filter_input(INPUT_GET, 'gruppenOrt')."',
                    `Verfasser` = '".filter_input(INPUT_GET, 'gruppenVerfasser')."',
                    `Startzeit` = '".filter_input(INPUT_GET, 'gruppenStart')."',
                    `Endzeit` = '".filter_input(INPUT_GET, 'gruppenEnde')."',
                    `Datum` = '".filter_input(INPUT_GET, 'gruppenDatum')."'
                    WHERE `idtabelle_Vermerkgruppe` = '".filter_input(INPUT_GET, 'gruppenID')."';";
            
        }        
        
	if ($mysqli->query($sql) === TRUE) {
            echo "Vermerkgruppe aktualisiert!";
	} 
	else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
