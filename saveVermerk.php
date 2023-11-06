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
        
        $sql = "UPDATE `LIMET_RB`.`tabelle_Vermerke`
            SET
            `tabelle_räume_idTABELLE_Räume` = ".$roomID.",
            `tabelle_lose_extern_idtabelle_Lose_Extern` = ".$losID.",       
            `Vermerktext` = '".filter_input(INPUT_GET, 'vermerkText')."',
            `Bearbeitungsstatus` = '".filter_input(INPUT_GET, 'vermerkStatus')."',
            `Vermerkart` = '".filter_input(INPUT_GET, 'vermerkTyp')."',
            `Faelligkeit` = '".filter_input(INPUT_GET, 'faelligkeitDatum')."',
            `tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe` = '".filter_input(INPUT_GET, 'untergruppenID')."'
            WHERE `idtabelle_Vermerke` = ".filter_input(INPUT_GET, 'vermerkID');
        
        
	if ($mysqli->query($sql) === TRUE) {
            echo "Vermerk aktualisiert!";
	} 
	else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

	$mysqli ->close();
?>
