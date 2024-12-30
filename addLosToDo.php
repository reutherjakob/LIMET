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
	
	// Check connection
	if ($mysqli->connect_error) {
	    die("Connection failed: " . $mysqli->connect_error);
	}

    $sql = "INSERT INTO tabelle_lose_ToDos 
                (id_tabelle_lose_extern, 
                 id_tabelle_element, 
                 Datum, 
                 Ersteller,
                ToDo)
            VALUES 
                (".filter_input(INPUT_GET, 'losID').",
                ".filter_input(INPUT_GET, 'elementID').",
                '".filter_input(INPUT_GET, 'datum')."',
                '".$_SESSION["username"]."',
                '".filter_input(INPUT_GET, 'todo_text')."'
            );";

	if ($mysqli->query($sql) === TRUE) {
        echo "Info zu Los hinzugefügt!";
	} 
	else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	$mysqli ->close();
?>
