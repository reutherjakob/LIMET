<?php

//setting header to json
header('Content-Type: application/json');

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
	mysqli_query($mysqli, "SET NAMES 'utf8'");
	
	
	$sql = "SELECT Sum(tabelle_lose_extern.Vergabesumme) AS SummevonVergabesumme, tabelle_lieferant.Lieferant
                FROM tabelle_lieferant INNER JOIN tabelle_lose_extern ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
                WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                GROUP BY tabelle_lieferant.Lieferant
                ORDER BY Sum(tabelle_lose_extern.Vergabesumme);";      
        
        //execute query
        $result = $mysqli->query($sql);
        
        //loop through the returned data
        $data = array();      
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        //free memory associated with result
        $result->close();

        //close connection
        $mysqli->close();

        //now print the data
        print json_encode($data);
        //echo $data;
?>			
