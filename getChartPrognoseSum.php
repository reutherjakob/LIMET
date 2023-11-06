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
	
	$sql = "SELECT tabelle_lose_extern.Vergabe_abgeschlossen, Sum(tabelle_lose_extern.Vergabesumme) AS SummevonVergabesumme, Sum(tabelle_lose_extern.Budget) AS SummevonBudget, Sum(tabelle_lose_extern.Vergabesumme-tabelle_lose_extern.Budget) AS Delta
                FROM tabelle_lose_extern
                WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                GROUP BY tabelle_lose_extern.Vergabe_abgeschlossen;";    
        
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
