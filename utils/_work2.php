<?php
require_once "_utils.php";
check_login();
$host = 'localhost';      // or your database host
$user = $_SESSION["username"];  // your DB username
$password = $_SESSION["password"]; // your DB password
$dbname = 'LIMET_RB';   // your database name
// Connect to MySQL/MariaDB
$mysqli = utils_connect_sql();

if ($mysqli->connect_error) {
    echo "U Fucked";
}

// Show connection details
echo "<h2>Database Connection Info</h2>";
echo "Host: " . htmlspecialchars($host) . "<br>";
echo "User: " . htmlspecialchars($user) . "<br>";
echo "Database: " . htmlspecialchars($dbname) . "<br>";
echo "Server info: " . htmlspecialchars($mysqli->server_info) . "<br>";
echo "Protocol version: " . htmlspecialchars($mysqli->protocol_version) . "<br>";
echo "Client info: " . htmlspecialchars($mysqli->client_info) . "<br>";
echo "Host info: " . htmlspecialchars($mysqli->host_info) . "<br>";
echo $_SESSION['projectID'];
// Show list of databases accessible with this user
echo "<h2>Databases accessible</h2>";
$result = $mysqli->query("SHOW DATABASES");
if ($result) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['Database']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error fetching databases: " . $mysqli->error;
}

// Show list of tables in current database
echo "<h2>Tables in database '$dbname'</h2>";
$result = $mysqli->query("SHOW TABLES");
if ($result) {
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error fetching tables: " . $mysqli->error;
}

$mysqli->close();
?>
