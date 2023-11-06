<?php
session_start();

if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
?>

<?php
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
	
        $sql = "INSERT INTO `LIMET_RB`.`tabelle_verwendungselemente`
                (`id_Standortelement`,
                `id_Verwendungselement`)
                VALUES
                (".filter_input(INPUT_GET, 'standortElement').",
                ".filter_input(INPUT_GET, 'id').");";
        
	if ($mysqli ->query($sql) === TRUE) {
	    echo "Standortelement hinzugefügt!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	
	$mysqli ->close();	
					
?>
