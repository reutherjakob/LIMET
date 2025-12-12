<?php
require_once "_utils.php";
check_login();

// Connect to MySQL/MariaDB
$mysqli = utils_connect_sql();

//if ($mysqli->connect_error) {
//    echo "U Fucked";
//}

//// Show connection details
//echo "<h2>Database Connection Info</h2>";
//echo "Host: " . htmlspecialchars($host) . "<br>";
//echo "User: " . htmlspecialchars($user) . "<br>";
//echo "Database: " . htmlspecialchars($dbname) . "<br>";
//echo "Server info: " . htmlspecialchars($mysqli->server_info) . "<br>";
//echo "Protocol version: " . htmlspecialchars($mysqli->protocol_version) . "<br>";
//echo "Client info: " . htmlspecialchars($mysqli->client_info) . "<br>";
//echo "Host info: " . htmlspecialchars($mysqli->host_info) . "<br>";
//echo $_SESSION['projectID'];
//// Show list of databases accessible with this user
//echo "<h2>Databases accessible</h2>";
//$result = $mysqli->query("SHOW DATABASES");
//if ($result) {
//    echo "<ul>";
//    while ($row = $result->fetch_assoc()) {
//        echo "<li>" . htmlspecialchars($row['Database']) . "</li>";
//    }
//    echo "</ul>";
//} else {
//    echo "Error fetching databases: " . $mysqli->error;
//}

//// Show list of tables in current database
//echo "<h2>Tables in database '$dbname'</h2>";
//$result = $mysqli->query("SHOW TABLES");
//if ($result) {
//    echo "<ul>";
//    while ($row = $result->fetch_array()) {
//        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
//    }
//    echo "</ul>";
//} else {
//    echo "Error fetching tables: " . $mysqli->error;
//}
//$mysqli->close();





$raumID =  202;
$projectID = $_SESSION["projectID"];

$mysqli = utils_connect_sql();

$sql = "SELECT 
    netzart,
    ROUND(SUM(gesamt_leistung_w), 2) AS gesamtleistung_w,
    COUNT(*) AS elemente_anzahl
FROM (
    SELECT 
        re.TABELLE_Räume_idTABELLE_Räume AS raum_id,
        e.Bezeichnung AS element,
        v.Variante,
        SUM(re.Anzahl) AS anzahl,
        
        -- Leistung Parameter 18
        COALESCE(NULLIF(pep_leistung.Wert, ''), 0) * 1.0 AS leistung,
        pep_leistung.Einheit,
        
        -- Gleichzeitigkeit Parameter 133  
        COALESCE(NULLIF(pep_gleich.Wert, ''), 100) * 1.0 AS gleichzeitigkeit,
        
        -- Netzart Parameter 82
        COALESCE(NULLIF(pep_netz.Wert, ''), 'Unbekannt') AS netzart,
        
        -- Berechnung pro Element
        SUM(re.Anzahl) * COALESCE(NULLIF(pep_leistung.Wert, ''), 0) * 1.0 * 
        (COALESCE(NULLIF(pep_gleich.Wert, ''), 100)) AS gesamt_leistung_w
        
    FROM tabelle_räume_has_tabelle_elemente re
        INNER JOIN tabelle_elemente e ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
        INNER JOIN tabelle_varianten v ON re.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
        
        -- Leistung (Param 18)
        LEFT JOIN tabelle_projekt_elementparameter pep_leistung ON 
            pep_leistung.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten 
            AND pep_leistung.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
            AND pep_leistung.tabelle_projekte_idTABELLE_Projekte = ?
            AND pep_leistung.tabelle_parameter_idTABELLE_Parameter = 18
            
        -- Gleichzeitigkeit (Param 133)
        LEFT JOIN tabelle_projekt_elementparameter pep_gleich ON 
            pep_gleich.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten 
            AND pep_gleich.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
            AND pep_gleich.tabelle_projekte_idTABELLE_Projekte = ?
            AND pep_gleich.tabelle_parameter_idTABELLE_Parameter = 133
            
        -- Netzart (Param 82)  
        LEFT JOIN tabelle_projekt_elementparameter pep_netz ON 
            pep_netz.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten 
            AND pep_netz.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
            AND pep_netz.tabelle_projekte_idTABELLE_Projekte = ?
            AND pep_netz.tabelle_parameter_idTABELLE_Parameter = 82
            
    WHERE re.TABELLE_Räume_idTABELLE_Räume = ?
        AND re.Verwendung = 1
    GROUP BY 
        re.TABELLE_Räume_idTABELLE_Räume, 
        e.idTABELLE_Elemente, 
        v.idtabelle_Varianten,
        pep_leistung.Wert, 
        pep_leistung.Einheit, 
        pep_gleich.Wert, 
        pep_netz.Wert
    HAVING anzahl > 0 AND leistung > 0
) AS element_leistungen
GROUP BY netzart
ORDER BY 
    CASE netzart 
        WHEN 'AV' THEN 1 
        WHEN 'SV' THEN 2 
        WHEN 'ZSV' THEN 3 
        WHEN 'USV' THEN 4 
        ELSE 5 
    END;
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('iiii', $projectID,$projectID,$projectID, $raumID);
$stmt->execute();
$result = $stmt->get_result();
 
echo "<h2>Raumleistung Raum ID: $raumID</h2>";
echo "<table border='1'>";
echo "<tr><th>Netzart</th><th>Gesamtleistung [W]</th><th>Elemente</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['netzart']}</td>";
    echo "<td>" . number_format($row['gesamtleistung_w'], 2) . "</td>";
    echo "<td>{$row['elemente_anzahl']}</td>";
    echo "</tr>";
}
echo "</table>";

$mysqli->close();
?>
