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

$mysqli = utils_connect_sql();

//////// -+- ALL TABLES -+-
//$stmt  = "SELECT TABLE_NAME, COLUMN_NAME
//FROM INFORMATION_SCHEMA.COLUMNS
//WHERE TABLE_SCHEMA = 'LIMET_RB'"; 
//

///////  -+- INFOS ABOUT A TABLE -+-
//$stmt = " SELECT COLUMN_NAME AS ColumnName, DATA_TYPE AS DataType, CHARACTER_MAXIMUM_LENGTH AS CharacterLength
//FROM INFORMATION_SCHEMA.COLUMNS
//WHERE TABLE_NAME = 'tabelle_lose_extern'";

//SELECT *
//FROM tabelle_r
//WHERE name LIKE '%searchstring%';

$stmt = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, 
                                    tabelle_elemente.ElementID, 
                                    tabelle_elemente.Bezeichnung, 
                                    tabelle_räume.`Raumbereich Nutzer`, 
                                    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
                                    tabelle_projekt_varianten_kosten.Kosten
                             FROM (tabelle_elemente 
                                   INNER JOIN (tabelle_räume 
                                               INNER JOIN tabelle_räume_has_tabelle_elemente 
                                               ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
                                   ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                             INNER JOIN tabelle_projekt_varianten_kosten 
                             ON (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) 
                             AND (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)
                             WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . ") 
                             AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`) = 0) 
                             AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . "))
                             GROUP BY tabelle_elemente.ElementID, 
                                      tabelle_elemente.Bezeichnung, 
                                      tabelle_räume.`Raumbereich Nutzer`, 
                                      tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
                                      tabelle_projekt_varianten_kosten.Kosten
                             ORDER BY tabelle_elemente.ElementID;"; 

$stmt = "
    SELECT 
        Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, 
        tabelle_elemente.ElementID, 
        tabelle_elemente.Bezeichnung, 
        tabelle_räume.`Raumbereich Nutzer`, 
        tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
        tabelle_projekt_varianten_kosten.Kosten, 
        tabelle_varianten.Variante
    FROM 
        tabelle_varianten 
    INNER JOIN 
        (
            (tabelle_elemente 
            INNER JOIN 
                (tabelle_räume 
                INNER JOIN tabelle_räume_has_tabelle_elemente 
                ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
            ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
        INNER JOIN tabelle_projekt_varianten_kosten 
        ON 
            (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) 
            AND 
            (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)) 
    ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
    WHERE 
        ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION["projectID"] . ") 
            AND (tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0) 
            AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =" . $_SESSION["projectID"] . " )
        )
    GROUP BY 
        tabelle_elemente.ElementID, 
        tabelle_elemente.Bezeichnung, 
        tabelle_räume.`Raumbereich Nutzer`, 
        tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
        tabelle_projekt_varianten_kosten.Kosten, 
        tabelle_varianten.Variante
    ORDER BY 
        tabelle_elemente.ElementID;";


$result = $mysqli->query($stmt);

echoLastWord($stmt);
echorow($result);
$data = array();
while ($row = $result->fetch_assoc()) {
    
    $data[] = $row;
}
echorow($data);
$mysqli->close();




/* 
Aufruf von Bauangaben von Vergleichsräume:
Dazu kannst du die Abfrage der Bauangaben-Neu verwenden und TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen nach dem Wert des aktuellen Raumes suchen
Sollte dem Anwender dazu dienen, dass er einen Vergleich mit anderen Projekten machen kann
vlt automatisierter Vergleich mit Darstellung von Abweichungen?
vlt eigener Modalaufruf?
Brainstorming willkomen 
Aufruf von Elementen im Raum von Vergleichsräumen - dazu zwei mögliche Abfragen:
 
 * SELECT tabelle_räume.idTABELLE_Räume, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
FROM tabelle_projekte INNER JOIN (tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)=128))
GROUP BY tabelle_räume.idTABELLE_Räume, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung;

 * SELECT tabelle_räume.idTABELLE_Räume, tabelle_projekte.Interne_Nr,
    tabelle_projekte.Projektname, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
FROM tabelle_projekte INNER JOIN (tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume 
ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) 
ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)= ?))
GROUP BY tabelle_räume.idTABELLE_Räume, tabelle_projekte.Interne_Nr, 
tabelle_projekte.Projektname, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung;
 
FROM tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has * SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)=128))
GROUP BY tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung;

 * SELECT Count(tabelle_räume_has_tabelle_elemente.Anzahl) AS AnzahlvonAnzahl, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
FROM tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)=128))
GROUP BY tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung;
 * 
Evtl. mit Ampelsystem:
Grün - Elemente sind aktuell berücksichtigt
Gelb - Fehlen im aktuellen Projekt
  */ 