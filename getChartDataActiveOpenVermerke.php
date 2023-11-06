<?php
    //setting header to json
    header('Content-Type: application/json');

    session_start();


    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    
    $sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, Count(tabelle_Vermerke.Bearbeitungsstatus) AS VermerkeGesamt,
            SUM(tabelle_Vermerke.Faelligkeit > date_format( curdate(), '%d.%m.%Y')) AS VermerkeUeberf, SUM(tabelle_Vermerke.Faelligkeit <= date_format( curdate(), '%d.%m.%Y')) AS VermerkeOffen
            FROM tabelle_projekte INNER JOIN ((tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) INNER JOIN tabelle_Vermerke ON tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe = tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe) ON tabelle_projekte.idTABELLE_Projekte = tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte
            WHERE (((tabelle_projekte.Aktiv)=1) AND ((tabelle_Vermerke.Vermerkart)='Bearbeitung') AND ((tabelle_Vermerke.Bearbeitungsstatus)=0))
            GROUP BY tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname;";

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
