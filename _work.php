<?php
/*
 *
CREATE TABLE_users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(255) UNIQUE NOT NULL,
password_hash VARCHAR(255) NOT NULL,
permission TINYINT NOT NULL DEFAULT 0
);

opt 1) Stay with sql login, but use new and salted encryption
opt 2) CREATE TABLE_users. Create a new user with the permission only to read the login table  -> then authenticate
opt 3) CREATE TABLE_users. Use env Variables on server to establish db connection -> then authenticate

use bcrypt/ password
*/

 //  [ ... ]


function safeRedirect($url) {
    header("Location: $url");
    exit();
}

// Function to log errors (implement this based on your logging preferences)
function logError($message) {
    error_log($message);
}

// Function to get user permissions
function get_permissions($mysqli, $username) {
    $stmt = $mysqli->prepare("SELECT permission FROM tabelle_user_permission WHERE user = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row ? (int)$row["permission"] : 0;
}

// Function to create or update user in table_users
function update_user_table($mysqli, $username, $password) {
    // First, check if an entry exists
    $check_stmt = $mysqli->prepare("SELECT username FROM table_users WHERE username = ?");
    if (!$check_stmt) {
        throw new Exception("Prepare failed for check: " . $mysqli->error);
    }

    $check_stmt->bind_param("s", $username);
    if (!$check_stmt->execute()) {
        throw new Exception("Execute failed for check: " . $check_stmt->error);
    }

    $result = $check_stmt->get_result();
    $check_stmt->close();

    // If no entry exists, insert a new one
    if ($result->num_rows === 0) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert_stmt = $mysqli->prepare("INSERT INTO table_users (username, password_hash) VALUES (?, ?)");
        if (!$insert_stmt) {
            throw new Exception("Prepare failed for insert: " . $mysqli->error);
        }

        $insert_stmt->bind_param("ss", $username, $hashed_password);
        if (!$insert_stmt->execute()) {
            throw new Exception("Execute failed for insert: " . $insert_stmt->error);
        }
        $insert_stmt->close();
    }
    // If an entry already exists, do nothing
}


// Validate POST data
$username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
$password = isset($_POST["password"]) ? $_POST["password"] : '';

if (empty($username) || empty($password)) {
    safeRedirect('index.php?error=empty_fields');
}

try {
    // Attempt to connect with the provided credentials
    $mysqli = new mysqli('localhost', $username, $password, 'LIMET_RB');

    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }

    // Connection successful, update table_users
    update_user_table($mysqli, $username, $password);

    // Store session data
    $_SESSION["username"] = $username;

    // Redirect to projects page
    safeRedirect('projects.php');

} catch (Exception $e) {
    logError($e->getMessage());
    safeRedirect('index.php?error=login_failed');
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
 // https://codeshack.io/secure-login-system-php-mysql/

/*I understand your confusion. Let's walk through this step-by-step:

    Database Connection:
    To check the table with usernames and passwords, you need a separate database connection that doesn't use the user's credentials. This is typically done using a service account or application-specific credentials.

php
$mysqli = new mysqli('localhost', 'app_user', 'app_password', 'your_database');

This connection uses predefined credentials for your application, not the user's input.

    Using the Table vs SQL Connection:
    Using a table is more secure and flexible than using the SQL connection directly:

    It allows for securely hashed passwords
    It separates authentication from database access privileges
    It provides better user management and scalability

    Secure Connection Without Username/Password:
    For a truly secure connection without exposing credentials, you have a few options:

a. Unix Socket Authentication:
If your application and MySQL are on the same server, you can use Unix socket authentication5
:

php
$mysqli = new mysqli('localhost', null, null, 'your_database', null, '/path/to/mysql.sock');

b. SSL Certificates:
Use SSL certificates for authentication:

php
$mysqli = mysqli_init();
mysqli_ssl_set($mysqli, '/path/to/client-key.pem', '/path/to/client-cert.pem', '/path/to/ca-cert.pem', null, null);
mysqli_real_connect($mysqli, 'localhost', 'user', 'password', 'database', 3306, null, MYSQLI_CLIENT_SSL);

c. Environment Variables:
Store credentials in environment variables:

php
$mysqli = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);

These methods provide more security than storing credentials directly in your code. The choice depends on your specific setup and security requirements.  */