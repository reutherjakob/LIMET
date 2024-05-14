<?php
session_start();
include '_utils.php';
init_page_serversides();

	$mysqli = utils_connect_sql();
	/* change character set to utf8 */
	

	$sql = "DELETE FROM `LIMET_RB`.`tabelle_bestandsdaten`
			WHERE `tabelle_bestandsdaten`.`idtabelle_bestandsdaten` = ".$_GET["bestandID"].";";

	if ($mysqli ->query($sql) === TRUE) {
	    echo "Bestand gel�scht!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	
	$mysqli ->close();	
					
?>
