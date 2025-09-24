<?php
session_start();
//if (!isset($_SESSION['user_id'])) {
//    header("Location: index.html");
//    exit;
//}
//header('X-Frame-Options: DENY');
//header('X-Content-Type-Options: nosniff');
//header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net");

require 'db.php';
$stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Restrict access
//if ($role  === "user") {
//    exit;
//}

require_once("form_fields_forNutzergruppeX.php");// Array of all form fields with types and labels s
function renderYesNoSelect($name, $label, $selected = null)
{
    $yesSelected = ($selected === "1") ? "selected" : "";
    $noSelected = ($selected === "0") ? "selected" : "";
    echo "<label>$label: ";
    echo "<select name=\"$name\">";
    echo "<option value=\"0\" $noSelected>Nein</option>";
    echo "<option value=\"1\" $yesSelected>Ja</option>";
    echo "</select>";
    echo "</label><br>\n";
}

// Helper function to render normal text input
function renderTextInput($name, $label, $value = "", $required = false)
{
    $req = $required ? 'required' : '';
    echo "<label>$label: <input type=\"text\" name=\"$name\" value=\"$value\" $req></label><br>\n";
}

// Helper function to render enum/select input with options
function renderSelect($name, $label, $options, $selected = null)
{
    echo "<label>$label: <select name=\"$name\">";
    foreach ($options as $val => $display) {
        $sel = ($selected !== null && $selected == $val) ? "selected" : "";
        echo "<option value=\"$val\" $sel>$display</option>";
    }
    echo "</select></label><br>\n";
}


// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to DB (replace with your DB login)
    $pdo = new PDO('mysql:host=localhost;dbname=YOURDB;charset=utf8', 'user', 'password');

    $fields = array_column($formFields, 'name');
    $placeholders = implode(',', array_fill(0, count($fields), '?'));

    // Build insert query dynamically
    $sql = "INSERT INTO room_requirements (" . implode(',', $fields) . ") VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);

    // Collect values from POST, default 0 or "" for undefined yes/no fields
    $values = [];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $values[] = $_POST[$field];
        } else {
            // Default for yesno fields to 0, else empty string
            $type = null;
            foreach ($formFields as $f) {
                if ($f['name'] === $field) {
                    $type = $f['type'];
                    break;
                }
            }
            $values[] = ($type === 'yesno' || $type === 'select') ? "0" : "";
        }
    }

    if ($stmt->execute($values)) {
        echo "<p style='color:green;'>Daten erfolgreich gespeichert.</p>";
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "<p style='color:red;'>Fehler: " . htmlspecialchars($errorInfo[2]) . "</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Raumanforderungen Formular</title>
    <script>
        function validateForm() {
            // Example: Make sure required text fields are not empty
            let requiredFields = ['roomname', 'username'];
            for (let field of requiredFields) {
                let el = document.forms["raumForm"][field];
                if (!el.value.trim()) {
                    alert("Bitte füllen Sie das Feld '" + field + "' aus.");
                    el.focus();
                    return false;
                }
            }
            return true;
        }
    </script>
</head>
<body>
<h2>Raumanforderungen erfassen</h2>
<form name="raumForm" method="post" onsubmit="return validateForm();">
    <?php
    foreach ($formFields as $field) {
        switch ($field['type']) {
            case 'yesno':
                renderYesNoSelect($field['name'], $field['label'], $_POST[$field['name']] ?? null);
                break;
            case 'text':
                renderTextInput($field['name'], $field['label'], $_POST[$field['name']] ?? "", $field['required'] ?? false);
                break;
            case 'select':
                renderSelect($field['name'], $field['label'], $field['options'], $_POST[$field['name']] ?? null);
                break;
        }
    }
    ?>
    <input type="submit" value="Absenden">
</form>
</body>
</html>
