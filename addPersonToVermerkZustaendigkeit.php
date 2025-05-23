
<?php
session_start();

function br2nl($string){
$return= str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br/>", $string);
return $return;
}

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
	if ($mysqli ->connect_error) {
	    die("Connection failed: " . $mysqli->connect_error);
	}
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 		
        
        $sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerke_has_tabelle_ansprechpersonen`
                (`tabelle_Vermerke_idtabelle_Vermerke`,
                `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen`)
                VALUES
                (".filter_input(INPUT_GET, 'vermerkID').",
                ".filter_input(INPUT_GET, 'ansprechpersonenID').");";
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Zustaendigkeit hinzugefügt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();	
					
?>
