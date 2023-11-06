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
        
        if(filter_input(INPUT_GET, 'room')=='0'){
            $roomID = "NULL";
        }
        else{
            $roomID = filter_input(INPUT_GET, 'room');
        }
        
        
        if(filter_input(INPUT_GET, 'los')=='0'){
            $losID = "NULL";
        }
        else{
            $losID = filter_input(INPUT_GET, 'los');
        }
        
        $sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerke`
                (`tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe`,
                `tabelle_räume_idTABELLE_Räume`,
                `tabelle_lose_extern_idtabelle_Lose_Extern`,
                `Ersteller`,
                `Erstellungszeit`,
                `Vermerktext`,
                `Bearbeitungsstatus`,
                `Faelligkeit`,
                `Vermerkart`)
                VALUES
                (".filter_input(INPUT_GET, 'untergruppenID').",
                ".$roomID.",
                ".$losID.",
                '".$_SESSION["username"]."',
                '".date("Y-m-d H:i:s")."',
                '".filter_input(INPUT_GET, 'vermerkText')."',
                '".filter_input(INPUT_GET, 'vermerkStatus')."',
                '".filter_input(INPUT_GET, 'faelligkeitDatum')."',
                '".filter_input(INPUT_GET, 'vermerkTyp')."');";

	if ($mysqli->query($sql) === TRUE) {
            echo "Vermerk hinzugefügt!";
	} 
	else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
