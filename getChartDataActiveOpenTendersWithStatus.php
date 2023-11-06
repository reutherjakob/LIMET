<?php
    //setting header to json
    header('Content-Type: application/json');

    session_start();


    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    
    $sql = "SELECT tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, tabelle_lose_extern.Vergabe_abgeschlossen AS Status, Count(tabelle_lose_extern.Vergabe_abgeschlossen) AS Counter, tabelle_projekte.idTABELLE_Projekte
            FROM tabelle_lose_extern INNER JOIN tabelle_projekte ON tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
            WHERE ((Not (tabelle_lose_extern.Vergabe_abgeschlossen)=1) AND ((tabelle_projekte.Aktiv)=1))
            GROUP BY tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, tabelle_lose_extern.Vergabe_abgeschlossen
            ORDER BY tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, Status;";

    //execute query
    $result = $mysqli->query($sql);
    
    // Perform a query, check for error
    if (!$mysqli -> query($sql)) {
      echo("Error description: " . $mysqli -> error);
    }

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
?>		
