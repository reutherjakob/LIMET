<?php
//25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT 
    tabelle_räume.tabelle_projekte_idTABELLE_Projekte, 
    tabelle_räume.idTABELLE_Räume, 
    tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, 
    tabelle_funktionsteilstellen.Nummer, 
    tabelle_funktionsteilstellen.Bezeichnung,
    tabelle_räume.`MT-relevant`, 
    tabelle_räume.Raumnr, 
    tabelle_räume.Raumbezeichnung, 
    tabelle_räume.`Funktionelle Raum Nr`, 
    tabelle_räume.Raumnummer_Nutzer, 
    tabelle_räume.`Raumbereich Nutzer`, 
    tabelle_räume.`Entfallen`
FROM 
    tabelle_räume
INNER JOIN 
    tabelle_funktionsteilstellen ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
WHERE  
    tabelle_räume.`MT-relevant` = 1  
    AND tabelle_räume.`Entfallen` = 0   
    AND tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
ORDER BY 
    tabelle_räume.Raumnr";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$mysqli->close();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

