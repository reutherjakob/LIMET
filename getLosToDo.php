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

	$sql = "SELECT tabelle_lose_ToDos.ToDo
			FROM tabelle_lose_ToDos
			WHERE tabelle_lose_ToDos.id_tabelle_lose_ToDos=".filter_input(INPUT_GET, 'ID').";";

	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	echo $row["ToDo"];
	$mysqli ->close();
?>			
