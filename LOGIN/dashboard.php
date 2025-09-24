<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// require 'db.php'; // must define and connect $mysqli
// 
// // Check user role - adapt as needed
// $stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
// $stmt->bind_param("i", $_SESSION['user_id']);
// $stmt->execute();
// $stmt->bind_result($role);
// $stmt->fetch();
// $stmt->close();
// 
// require your form fields array
require_once("form_fields_forNutzergruppeX.php"); // $formFields array

function renderYesNoSelect($name, $label, $selected = null) {
    echo "<label>$label: <select name=\"$name\">";
    echo "<option value=\"0\"" . (($selected==="0")?" selected":"") . ">Nein</option>";
    echo "<option value=\"1\"" . (($selected==="1")?" selected":"") . ">Ja</option>";
    echo "</select></label><br>\n";
}
function renderTextInput($name, $label, $value = "", $required = false) {
    $req = $required ? "required" : "";
    $val = htmlspecialchars($value);
    echo "<label>$label: <input type=\"text\" name=\"$name\" value=\"$val\" $req></label><br>\n";
}
function renderSelect($name, $label, $options, $selected = null) {
    echo "<label>$label: <select name=\"$name\">";
    foreach ($options as $val => $disp) {
        $sel = ($selected == $val) ? " selected" : "";
        echo "<option value=\"$val\"$sel>$disp</option>";
    }
    echo "</select></label><br>\n";
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $fields = array_column($formFields, 'name');
//     $placeholders = implode(',', array_fill(0, count($fields), '?'));
//     $fieldList = implode(',', $fields);
//
//     $types = '';
//     $values = [];
//
//     foreach ($fields as $field) {
//         $val = $_POST[$field] ?? null;
//         $type = null;
//         foreach ($formFields as $f) {
//             if ($f['name'] === $field) { $type = $f['type']; break; }
//         }
//         if ($val === null) {
//             $val = ($type==='yesno' || $type==='select') ? "0" : "";
//         }
//         $values[] = $val;
//         $types .= ($type==='yesno' || $type==='select') ? 'i' : 's';
//     }
//
//     $stmt = $mysqli->prepare("INSERT INTO room_requirements ($fieldList) VALUES ($placeholders)");
//     if (!$stmt) {
//         die("Prepare failed: " . $mysqli->error);
//     }
//
//     // bind params dynamically
//     $bind_names[] = $types;
//     for ($i=0; $i<count($values); $i++) {
//         $bind_name = 'bind'.$i;
//         $$bind_name = $values[$i];
//         $bind_names[] = &$$bind_name;
//     }
//     call_user_func_array([$stmt,'bind_param'], $bind_names);
//
//     if ($stmt->execute()) {
//         echo "<p style='color:green;'>Daten erfolgreich gespeichert.</p>";
//     } else {
//         echo "<p style='color:red;'>Fehler: " . htmlspecialchars($stmt->error) . "</p>";
//     }
//     $stmt->close();
// }
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Raumanforderungen Formular</title>
    <script>
        function validateForm() {
            let requiredFields = ["roomname","username"];
            for (let f of requiredFields) {
                let el = document.forms["raumForm"][f];
                if (!el.value.trim()) {
                    alert("Bitte füllen Sie das Feld '" + f + "' aus.");
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
        $val = $_POST[$field['name']] ?? null;
        switch ($field['type']) {
            case 'yesno': renderYesNoSelect($field['name'], $field['label'], $val); break;
            case 'text': renderTextInput($field['name'], $field['label'], $val ?? "", $field['required'] ?? false); break;
            case 'select': renderSelect($field['name'], $field['label'], $field['options'], $val); break;
        }
    }
    ?>
    <input type="submit" value="Absenden">
</form>
</body>
</html>
