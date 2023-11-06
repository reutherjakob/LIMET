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
        
        $sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerkuntergruppe`
                (`Untergruppenname`,
                `Untergruppennummer`,
                `tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe`)
                VALUES
                ('".filter_input(INPUT_GET, 'untergruppenName')."',
                '".filter_input(INPUT_GET, 'untergruppenNummer')."',
                ".filter_input(INPUT_GET, 'gruppenID').");";
        
	if ($mysqli->query($sql) === TRUE) {
            echo "Vermerkuntergruppe hinzugefügt!";
	} 
	else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
