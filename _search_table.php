<?php
session_start();
include '_utils.php'; // Assuming this contains your utility functions for connecting to the database

$mysqli = utils_connect_sql(); // Connect to the database
 
function fetch_last_entries_hardcoded($mysqli) { 
    // SQL query to fetch data where 'raumbezeichnung_alt' matches 'SAXS'
    $sql = "SELECT  * FROM tabelle_raeume_aenderungen WHERE raumbezeichnung_alt LIKE 'SAXS'";
    
    $result = $mysqli->query($sql);
    
    // Fetch the data as an associative array
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;  
}

// Fetch the hardcoded data
$last_entries = fetch_last_entries_hardcoded($mysqli);

// Close the database connection
$mysqli->close();

// Display the fetched data in a table format
if (!empty($last_entries)) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>";
    // Assuming you want to display all columns, dynamically output the table headers
    foreach (array_keys($last_entries[0]) as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
    echo "</tr>";
    
    // Output the table rows
    foreach ($last_entries as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No entries found.";
}
?>
