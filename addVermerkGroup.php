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
            $sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerkgruppe`
                    (`Gruppenname`,
                    `Gruppenart`,
                    `Ort`,
                    `Verfasser`,
                    `Startzeit`,
                    `Endzeit`,
                    `Datum`,
                    `tabelle_projekte_idTABELLE_Projekte`)
                    VALUES
                    ('".filter_input(INPUT_GET, 'gruppenName')."',
                    '".filter_input(INPUT_GET, 'gruppenart')."',
                    '".filter_input(INPUT_GET, 'gruppenOrt')."',
                    '".filter_input(INPUT_GET, 'gruppenVerfasser')."',
                    '".filter_input(INPUT_GET, 'gruppenStart')."',
                    '".filter_input(INPUT_GET, 'gruppenEnde')."',
                    '".filter_input(INPUT_GET, 'gruppenDatum')."',
                    ".$_SESSION["projectID"].");";
        }        
        
	if ($mysqli->query($sql) === TRUE) {
            echo "Vermerkgruppe hinzugefügt!";
	} 
	else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
