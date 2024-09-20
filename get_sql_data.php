<?php

session_start();
include '_utils.php';

function searchDatabase($dbName, $tableName, $fieldNames, $searchString) {
    $conn = new mysqli('localhost', 'username', 'password', $dbName);
    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);
    $fields = $fieldNames ? implode(", ", $fieldNames) : '*';
    $sql = "SELECT $fields FROM $tableName WHERE ";
    $conditions = [];
    foreach ($fieldNames as $field)
        $conditions[] = "$field LIKE '%$searchString%'";
    $sql .= implode(" OR ", $conditions);
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc())
            $data[] = $row;
    $conn->close();
    return $data;
}

function echoLastWord($string) {
    $words = explode(' ', trim($string));
    echo end($words);
}

function handleDuplicates($mysqli) {
    // Load data from SQL table
    $data = loadDataFromSQL($mysqli);

    // Arrays to store original and unique IDs
    $originalIds = [];
    $uniqueIds = [];
    $updatedRows = [];

    // Iterate through the data to find duplicates and adjust IDs
    foreach ($data as $row) {
   
        $raumnr = $row['Raumnr'];
        $counter = 1;
        $newId = $raumnr;
        // Adjust ID if it already exists in uniqueIds
        while (in_array($newId, $uniqueIds)) {
            $newId = $raumnr . '_' . strval($counter);
            $counter++;
        }
     
        $uniqueIds[] =  $newId;
        $updatedRows[] = ['id' => $row['idTABELLE_Räume'], 'newId' => $newId];
    }

    // Update the SQL table with unique IDs
    updateSQLTable($mysqli, $updatedRows);

    // Print validation results
    printValidation($originalIds, $uniqueIds);
}

function loadDataFromSQL($mysqli) {
    $stmt = "SELECT idTABELLE_Räume, Raumnr, Raumnummer_Nutzer 
            FROM tabelle_räume
            WHERE (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION['projectID'] . ") AND tabelle_räume.Bauabschnitt LIKE '%Haus F%' ";

    $result = $mysqli->query($stmt);
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function updateSQLTable($mysqli, $updatedRows) {
    foreach ($updatedRows as $row) {
        $stmt = $mysqli->prepare("UPDATE tabelle_räume SET Raumnummer_Nutzer = ? WHERE idTABELLE_Räume = ? AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION['projectID'] . ")");
        $id2pass =substr(  $row['newId'],-10);
        $stmt->bind_param('si', $id2pass , $row['id']);
        $stmt->execute();
        echo "Updated idTABELLE_Räume " . $row['id'] . " with new Raumnummer_Nutzer " . $row['newId'] . "<br>";
        $stmt->close();
    }
}

function printValidation($originalIds, $uniqueIds) {
    echo "Original IDs: " . implode(',<br> ', $originalIds) . "\n";
    echo "Unique IDs: " . implode(',<br> ', $uniqueIds) . "\n";
}

$mysqli = utils_connect_sql();
handleDuplicates($mysqli);
$mysqli->close();

/*
      function handleDuplicates($mysqli) {
      // Load data from SQL table
      $data = loadDataFromSQL($mysqli);

      // Arrays to store original and unique IDs
      $originalIds = [];
      $uniqueIds = [];

      // Iterate through the data to find duplicates and adjust IDs
      foreach ($data as $row) {
      $raumnr = $row['Raumnr'];
      $newId = $raumnr;
      $counter = 1;

      // Adjust ID if it already exists in uniqueIds
      while (in_array($newId, $uniqueIds)) {
      $newId = $raumnr . '_' . $counter;
      $counter++;
      }

      // Store the original and unique IDs
      $originalIds[] = $raumnr;
      $uniqueIds[] = $newId;
      }

      // Print validation results
      printValidation($originalIds, $uniqueIds);
      }

      function loadDataFromSQL($mysqli) {
      $stmt = "SELECT Raumnr
      FROM tabelle_räume
      WHERE (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION["projectID"] . ") AND tabelle_räume.Bauabschnitt LIKE '%Haus F%' ";

      $result = $mysqli->query($stmt);
      $data = array();
      while ($row = $result->fetch_assoc()) {

      $data[] = $row;
      }
      return $data;
      }


      function printValidation($originalIds, $uniqueIds) {
      echo "Original IDs: " . implode(',</br> ', $originalIds) . "\n";
      echo "Unique IDs: " . implode(',</br> ', $uniqueIds) . "\n";
      }

      $mysqli = utils_connect_sql();
      handleDuplicates($mysqli);
      $mysqli->close();
     */
    /*
      //////// -+- ALL TABLES -+-
      //$stmt  = "SELECT TABLE_NAME, COLUMN_NAME
      //FROM INFORMATION_SCHEMA.COLUMNS
      //WHERE TABLE_SCHEMA = 'LIMET_RB'";
      //
      ///////  -+- INFOS ABOUT A TABLE -+-
      //$stmt = " SELECT COLUMN_NAME AS ColumnName, DATA_TYPE AS DataType, CHARACTER_MAXIMUM_LENGTH AS CharacterLength
      //FROM INFORMATION_SCHEMA.COLUMNS
      //WHERE TABLE_NAME = 'tabelle_parameter'";
      //$stmt = "SELECT * FROM tabelle_parameter"; //WHERE name LIKE '%searchstring%';
      //$stmt = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
      //                                    tabelle_elemente.ElementID,
      //                                    tabelle_elemente.Bezeichnung,
      //                                    tabelle_räume.`Raumbereich Nutzer`,
      //                                    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
      //                                    tabelle_projekt_varianten_kosten.Kosten
      //                             FROM (tabelle_elemente
      //                                   INNER JOIN (tabelle_räume
      //                                               INNER JOIN tabelle_räume_has_tabelle_elemente
      //                                               ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
      //                                   ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
      //                             INNER JOIN tabelle_projekt_varianten_kosten
      //                             ON (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
      //                             AND (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)
      //                             WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . ")
      //                             AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`) = 0)
      //                             AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . "))
      //                             GROUP BY tabelle_elemente.ElementID,
      //                                      tabelle_elemente.Bezeichnung,
      //                                      tabelle_räume.`Raumbereich Nutzer`,
      //                                      tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
      //                                      tabelle_projekt_varianten_kosten.Kosten
      //                             ORDER BY tabelle_elemente.ElementID;";
      //$stmt = "
      //    SELECT
      //        Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
      //        tabelle_elemente.ElementID,
      //        tabelle_elemente.Bezeichnung,
      //        tabelle_räume.`Raumbereich Nutzer`,
      //        tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
      //        tabelle_projekt_varianten_kosten.Kosten,
      //        tabelle_varianten.Variante
      //    FROM
      //        tabelle_varianten
      //    INNER JOIN
      //        (
      //            (tabelle_elemente
      //            INNER JOIN
      //                (tabelle_räume
      //                INNER JOIN tabelle_räume_has_tabelle_elemente
      //                ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
      //            ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
      //        INNER JOIN tabelle_projekt_varianten_kosten
      //        ON
      //            (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)
      //            AND
      //            (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten))
      //    ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
      //    WHERE
      //        ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION["projectID"] . ")
      //            AND (tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0)
      //            AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =" . $_SESSION["projectID"] . " )
      //        )
      //    GROUP BY
      //        tabelle_elemente.ElementID,
      //        tabelle_elemente.Bezeichnung,
      //        tabelle_räume.`Raumbereich Nutzer`,
      //        tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
      //        tabelle_projekt_varianten_kosten.Kosten,
      //        tabelle_varianten.Variante
      //    ORDER BY
      //        tabelle_elemente.ElementID;";
      //echoLastWord($stmt);
      //echorow($result);
      //$data = array();
      //while ($row = $result->fetch_assoc()) {
      //    $data[] = $row;
      //}
      //echorow($data); */





    