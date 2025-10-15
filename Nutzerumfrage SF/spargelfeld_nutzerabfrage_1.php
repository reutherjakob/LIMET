<?php
global $mysqli, $formFields;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
init_page(["internal_rb_user", "spargefeld_ext_users"]);
require_once("form_fields_forNutzergruppeX.php"); // Array $formFields
require_once("../Nutzerlogin/csrf.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$roomId = $_POST['raumid'] ?? null;
error_log("Received roomId: " . var_export($roomId, true));

// Funktion um Formular zu rendern aus vorheriger Antwort:
function renderForm(array $formFields, array $userData = []): void
{
    echo '<form method="post" action="">';
    echo '<div class="col-12 d-flex justify-content-end "> <button type="submit" class="btn btn-success">Alle Anforderungen speichern</button> </div>';
    echo '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';

    foreach ($formFields as $field) {
        $name = $field['name'];
        $label = $field['label'];
        $type = $field['type'];
        $kathegorie = $field['kathegorie'] ?? '';
        $defaultValue = $field['default_value'] ?? '';
        $options = $field['options'] ?? [];

        $value = $userData[$name] ?? $defaultValue;

        switch ($type) {
            case 'text':
                echo "<div class='mb-1 {$kathegorie} d-flex align-items-center'>";
                echo "<label for='{$name}' class='form-label'><strong>{$label}</strong></label>";
                echo "<input class='form-control flex-grow-1' type='text' name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'>";
                break;

            case 'texthidden':
                echo "<div class='{$kathegorie}'>";
                echo "<label for='{$name}' class='sr-only'>{$label}:</label>";
                echo "<input class='form-control' type='hidden' name='{$name}' id='{$name}' rows='1'  value='" . htmlspecialchars($value) . "'>";
                break;

            case 'textarea':
                echo "<div class='mb-1 {$kathegorie} row align-items-start'>";
                echo "<label for='{$name}' class='form-label col-auto text-nowrap'>{$label}:</label>";
                echo "<div class='col'>";
                echo "<textarea class='form-control' name='{$name}' id='{$name}' rows='1' placeholder='{$label}'>" . htmlspecialchars($value) . "</textarea>";
                echo "</div>";
                break;

            case 'yesno':
                echo "<div class=' mb-1  {$kathegorie} d-flex justify-content-start '>";
                echo "<label for='{$name}' class='form-label text-nowrap me-2'> <strong> {$label}</strong></label>";
                $checked = ($value == "1" || $value === 1) ? "checked" : "";
                echo "<div class='form-check'>";
                echo "<input class='form-check-input' type='checkbox' name='{$name}' id='{$name}' value='1' {$checked}>";
                echo "<label class='sr-only' for='{$name}'></label>";
                echo "</div>";
                break;

            case 'select':
                echo "<div class='mb-1 {$kathegorie} d-flex justify-content-start'>";
                echo "<label for='{$name}' class='form-label text-nowrap me-2'><strong> {$label}</strong></label>";
                echo "<select class='form-select' name='{$name}' id='{$name}'>";
                foreach ($options as $optValue => $optLabel) {
                    $selected = ($optValue == $value) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($optValue) . "' {$selected}>" . htmlspecialchars($optLabel) . "</option>";
                }
                echo "</select>";
                break;

            default:
                echo "<input class='form-control' type='text' name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'>";
        }
        echo "</div>";
    }


    echo '</form>';
}

$userValues = [];
if ($roomId) {
    $stmt = $mysqli->prepare("SELECT * FROM tabelle_room_requirements_from_user WHERE roomID = ?");
    if ($stmt) {
        $stmt->bind_param('i', $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userValues = $result->fetch_assoc() ?: [];
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Raumanforderungen Formular</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
</head>
<body class="container mt-4">


<?php renderForm($formFields, $userValues); ?>
</body>
<script>
    function loadFormData(roomId) {
        $.post('load_room_data.php', {roomId: roomId}, function (response) {
            if (response.error) {
                alert('Fehler: ' + response.error);
                return;
            }
            const data = response.data;

            const skipFields = ['raumnr', 'roomname', 'raumbereich_nutzer', 'ebene', 'nf'];

            // Für jedes Feld im empfangenen Datenobjekt den Wert im Formular setzen,
            // außer wenn dieses Feld in skipFields ist
            for (const key in data) {
                if (skipFields.includes(key)) continue;

                const el = $('[name="' + key + '"]');
                if (!el.length) continue;
                const value = data[key];

                if (el.is(':checkbox')) {
                    el.prop('checked', value === 1);
                } else if (el.is('select')) {
                    el.val(value);
                } else {
                    el.val(value);
                }
            }
        }, 'json');
    }


    $('form').on('submit', function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.post('save_room_data.php', formData, function (response) {
            alert(response); // z.B. "Data successfully updated."
        }).fail(function () {
            alert('Fehler beim Speichern.');
        });
    });


</script>
</html>
