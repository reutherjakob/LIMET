<?php
global $mysqli, $formFields;
require_once("form_fields_forNutzergruppeX.php"); // Array $formFields
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
init_page(["internal_rb_user", "spargefeld_ext_users"]);
require_once("../Nutzerlogin/csrf.php");

 if (!isset($_POST['csrf_token']) || !csrf_check($_POST['csrf_token'])) {
     die('Ungültiges CSRF-Token');
 }

// Define log file path
//$logFile = __DIR__ . '/sql_debug_log.txt';

header('Content-Type: text/plain');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method.";
    exit;
}

if (!$mysqli) {
    echo "Database connection error.";
    exit;
}

$mysqli->set_charset('utf8mb4');

// Feldnamen aus $formFields extrahieren
$fields = [];
foreach ($formFields as $field) {
    $fields[] = $field['name'];
}

$values = [];
foreach ($fields as $field) {
    if ($field === 'username') {
        $values[] = (string)($_SESSION['user_name'] ?? '');
    } else {
        if (isset($_POST[$field])) {
            $values[] = (string)$_POST[$field];
        } else {
            $isYesNo = false;
            foreach ($formFields as $ff) {
                if ($ff['name'] === $field && $ff['type'] === 'yesno') {
                    $isYesNo = true;
                    break;
                }
            }
            $values[] = $isYesNo ? '0' : '';
        }
    }
}

// roomID ermitteln
$roomID = $_POST['roomID'] ?? null;
if ($roomID === null) {
    echo "Missing roomID.";
    exit;
}

// Existenz prüfen
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM tabelle_room_requirements_from_user WHERE roomID = ?");
if (!$stmt) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    exit;
}
$stmt->bind_param('s', $roomID);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// Function to log SQL and values
function logQuery($sql, $params, $logFile)
{
    $logEntry = "[" . date('Y-m-d H:i:s') . "]\n";
    $logEntry .= "SQL: $sql\n";
    $logEntry .= "Params: " . print_r($params, true);
    $logEntry .= str_repeat("-", 50) . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function logToFile($message)
{
    $logFile = __DIR__ . '/sql_debug.log';  // Log file in the same directory as the script
    $timeStamp = date('Y-m-d H:i:s');
    // Append the message with timestamp followed by newline
    file_put_contents($logFile, "[$timeStamp] $message\n", FILE_APPEND);
}

function logFieldValues(array $fields, array $values)
{
    $pairs = [];
    foreach ($fields as $index => $fieldName) {
        // Avoid out-of-bounds if arrays differ in length
        $value = $values[$index] ?? '(missing)';
        // Escape or sanitize value to safely log (optional)
        $pairs[] = "$fieldName = " . var_export($value, true);
    }
    $logMessage = implode(", ", $pairs);
    logToFile($logMessage);
}


if ($count > 0) {    // UPDATE
    $setParts = [];
    foreach ($fields as $field) {
        if ($field !== 'roomID') {
            $setParts[] = "$field = ?";
        }
    }
    $sql = "UPDATE tabelle_room_requirements_from_user SET " . implode(', ', $setParts) . " WHERE roomID = ?";

    // Build values for logging (same order as bind)
    $updateValues = [];
    foreach ($fields as $field) {
        if ($field !== 'roomID') {
            if ($field === 'username') {
                $updateValues[] = (string)($_SESSION['user_name'] ?? '');
            } else {
                $updateValues[] = (string)($_POST[$field] ?? '');
            }
        }
    }
    $updateValues[] = (string)$roomID;

    // Log before execution
    //  logQuery($sql, $updateValues, $logFile);
  //  logToFile("UPDATE query: $sql");
  //  logFieldValues(array_filter($fields, fn($f) => $f !== 'roomID'), array_slice($updateValues, 0, -1)); // All update fields except roomID
  //  logToFile("roomID = " . end($updateValues));

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        exit;
    }

    $typeString = str_repeat('s', count($updateValues));
    $stmt->bind_param($typeString, ...$updateValues);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Data successfully updated.";
        } else {
            echo "No rows updated.";
        }
    } else {
        echo "Update failed: (" . $stmt->errno . ") " . $stmt->error;
    }
} else {
    // INSERT
    $placeholders = implode(',', array_fill(0, count($fields), '?'));
    $sql = "INSERT INTO tabelle_room_requirements_from_user (" . implode(', ', $fields) . ") VALUES ($placeholders)";

    // Log before execution
    // logQuery($sql, $values, $logFile);
    //logToFile("UPDATE query: $sql");
    //logFieldValues(array_filter($fields, fn($f) => $f !== 'roomID'), array_slice($values, 0, -1)); // All update fields except roomID
    //logToFile("roomID = " . end($values));

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        exit;
    }

    $typeString = str_repeat('s', count($values));
    $stmt->bind_param($typeString, ...$values);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Data successfully inserted.";
        } else {
            echo "No rows inserted.";
        }
    } else {
        echo "Insert failed: (" . $stmt->errno . ") " . $stmt->error;
    }
}
$stmt->close();
?>
