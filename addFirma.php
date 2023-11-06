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
	

	//echo $_GET["Notiz"]." ".date('Y-m-d')." ".$_SESSION["username"]." ".$_GET["Kategorie"]." ".$_GET["roomID"];
	
	if(filter_input(INPUT_GET, 'firma') != "" && filter_input(INPUT_GET, 'lieferantTel') != "" && filter_input(INPUT_GET, 'lieferantAdresse') != "" && filter_input(INPUT_GET, 'lieferantPLZ') !== "" && filter_input(INPUT_GET, 'lieferantOrt') !== "" && filter_input(INPUT_GET, 'lieferantLand') !== ""){
		
		
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
		
		$sql = "INSERT INTO `tabelle_lieferant`
				(`Lieferant`,
				`Tel`,
				`Anschrift`,
				`PLZ`,
				`Ort`,
				`Land`)
				VALUES
				('".filter_input(INPUT_GET, 'firma')."',
				'".filter_input(INPUT_GET, 'lieferantTel')."',
				'".filter_input(INPUT_GET, 'lieferantAdresse')."',
				'".filter_input(INPUT_GET, 'lieferantPLZ')."',
				'".filter_input(INPUT_GET, 'lieferantOrt')."',
				'".filter_input(INPUT_GET, 'lieferantLand')."');";
		
		if ($mysqli->query($sql) === TRUE) {
		    echo "Lieferant hinzugefügt!";
		    $id = $mysqli->insert_id; 
		} 
		else {
		    echo "Error1: " . $sql . "<br>" . $mysqli->error;
		}
						
		$mysqli ->close();
	}
	else{
		echo "Fehler bei der Verbindung";
	}
?>
