<?php
global $mysqli, $formFields;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
$role = init_page(["internal_rb_user", "spargefeld_ext_users"]);

require_once("form_fields_forNutzergruppe1.php"); // Array $formFields
require_once("../Nutzerlogin/csrf.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$roomId = $_POST['raumid'] ?? null;
$roomname = $_POST['roomname'] ?? null;
error_log("Received roomId: " . var_export($roomId, true));

// Funktion um Formular zu rendern aus vorheriger Antwort:
function renderForm(array $formFields, array $userData = []): void
{
    global $mysqli, $roomname;


    echo '<form method="post" action=""><div class="card-header d-flex align-items-center justify-content-between" > 
            <strong> Labortechnische Raumanforderung </strong>';


    echo '<div class="d-flex align-items-center justify-content-end"> <button type="submit" class="btn btn-outline-success"> <i class="far fa-save"></i> Anforderungen speichern</button>
          </div> 
           </div> <div class="card-body px-2 py-2">    </div>';


    echo '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';


    foreach ($formFields as $field) {
        $name = $field['name'];
        $label = $field['label'];
        $type = $field['type'];
        $kathegorie = $field['kathegorie'] ?? '';
        $defaultValue = '';

        if (is_array($field['default_value'] ?? false)) {
            if ($roomname && isset($field['default_value'][$roomname])) {
                $defaultValue = $field['default_value'][$roomname];
            }
        } else {
            $defaultValue = $field['default_value'] ?? '';
        }

        $options = $field['options'] ?? [];
        $value = $userData[$name] ?? $defaultValue;
        $info = $field['info'] ?? '';
        switch ($type) {

            case 'KathegorieDropdowner':
                echo '<div class="mb-1 d-flex align-items-center">';
                echo '<label  for="Raumkathegorie" class="col-6 ms-2 form-label rechtsbuendig">
                      <strong>  Raumkathegorie?  </strong>';
                if ($info) {
                    echo " <button class='btn btn-sm bg-white rounded'
                                  data-bs-toggle='popover'
                                  data-bs-content='{$info}'>
                                    <i class='fas fa-info-circle'></i>
                                </button>";
                }
                echo ' </label>
                       <div class="col-5"> 
                        <select id="Raumkathegorie" class="ms-2 form-select "  name="raumkathegorie">
                            <option value="">Raumkathegorie wählen</option>';

                $sql = "SELECT tabelle_räume.Raumbezeichnung, idTABELLE_Räume, `Raumbereich Nutzer`
                        FROM tabelle_räume
                        where tabelle_projekte_idTABELLE_Projekte = 3";

                $result = $mysqli->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $checked = "";
                    if ($roomname && !str_contains($row['Raumbereich Nutzer'], $roomname)) continue;
                    if ($roomname == "Waschküche" || $roomname == "Wägeraum") {
                        $checked = "selected";
                    }
                    echo "<option value=' " . $row['idTABELLE_Räume'] . "' " . $checked . "  >" . $row['Raumbezeichnung'] . "</option>";
                }

                echo '</select> </div> </div>';
                break;

            case 'text':
                echo "<div class='mb-1 {$kathegorie} d-flex align-items-center'>";
                echo "<label for='{$name}' class='form-label col-6 ms-2 me-2 rechtsbuendig'><strong>{$label}</strong></label>";
                echo "<div class='col-5'>  <input class='form-control flex-grow-1' type='text' name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'> </div>";
                break;

            case 'texthidden':
                echo "<div class='{$kathegorie}'>";
                echo "<label for='{$name}' class='sr-only ms-2 me-2 rechtsbuendig' >{$label}:</label>";
                echo "<div class='col-9'><input class='form-control' type='hidden' name='{$name}' id='{$name}'  value='" . htmlspecialchars($value) . "'></div>";
                break;

            case 'text_non_editable':
                echo "<div class='{$kathegorie} d-flex align-items-center'>";
                echo "<strong class='col-6 rechtsbuendig'>{$label}:</strong>";
                echo "<input class='form-control ms-2' type='text' name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'readonly>";
                break;


            case 'textarea':
                echo "<div class='mb-1 {$kathegorie} row align-items-start'>";
                echo "<label for='{$name}' class='form-label col-6  ms-2 me-2 rechtsbuendig'>{$label}:</label>";
                echo "<div class='col-9'>";
                echo "<textarea class='form-control' name='{$name}' id='{$name}' rows='1' placeholder='{$label}'>" . htmlspecialchars($value) . "</textarea>";
                echo "</div>";
                break;


            case 'yesno':
                $isYes = ($value == 1 || $value === '1');
                $btnClass = $isYes ? 'btn btn-outline-success' : 'btn btn-outline-primary';
                $btnText = $isYes ? ' Ja ' : 'Nein';
                echo "<div class='mb-1 {$kathegorie} d-flex align-items-center'>";
                echo "<label for='{$name}_toggle' class='form-label col-6 ms-2 me-2 rechtsbuendig'><strong>{$label}";
                if ($info) {
                    echo " <button class='btn btn-sm bg-white rounded'
                                  data-bs-toggle='popover'
                                  data-bs-content='{$info}'>
                                    <i class='fas fa-info-circle'></i>
                                </button>";
                }
                echo " </strong></label>";
                echo "<button type='button' class='{$btnClass} text-nowrap' id='{$name}_toggle' style='width: 4vw;'>{$btnText}</button>";
                echo "<input type='hidden' name='{$name}' id='{$name}' value='" . ($isYes ? '1' : '0') . "'>";
                echo "</div>";
                break;


            case 'yesno_checkbox':
                echo "<div class=' mb-1  {$kathegorie} d-flex justify-content-start '>";
                echo "<label for='{$name}' class='form-label   col-6 ms-2 me-2 rechtsbuendig'> <strong> {$label}</strong></label>";
                $checked = ($value == "1" || $value === 1) ? "checked" : "";
                echo "<div class='form-check'>";
                echo "<input class='form-check-input' type='checkbox' name='{$name}' id='{$name}' value='1' {$checked}>";
                echo "<label class='sr-only' for='{$name}'></label>";
                echo "</div>";
                break;


            case 'select_dropdown':
                echo "<div class='mb-1 {$kathegorie} d-flex justify-content-start'>";
                echo "<label for='{$name}' class='form-label  me-2 ms-2  col-6 rechtsbuendig'><strong> {$label}</strong></label>";
                echo "<select class='form-select' name='{$name}' id='{$name}'>";
                foreach ($options as $optValue => $optLabel) {
                    $selected = ($optValue == $value) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($optValue) . "' {$selected}>" . htmlspecialchars($optLabel) . "</option>";
                }
                echo "</select>";
                break;

            case 'select':
                echo "<div class='mb-1 {$kathegorie} d-flex align-items-center flex-wrap'>";
                echo "<label class='form-label  me-2 ms-2  col-6 rechtsbuendig' ><strong>{$label}</strong>";
                if ($info) {
                    echo " <button class='btn btn-sm bg-white rounded'
                                  data-bs-toggle='popover'
                                  data-bs-content='{$info}'>
                                    <i class='fas fa-info-circle'></i>
                                </button>";
                }
                echo "</label>
                        <div class='btn-group' role='group' aria-label='{$label}'>";
                foreach ($options as $optValue => $optLabel) {
                    $checked = ($optValue == $value) ? "checked" : "";
                    $btnId = "{$name}_{$optValue}";
                    echo "<input type='radio' class='btn-check' name='{$name}' id='{$btnId}' value='" . htmlspecialchars($optValue) . "' {$checked} autocomplete='off'>
            <label class='btn btn-outline-primary me-1 mb-1' for='{$btnId}'>" . htmlspecialchars($optLabel) . "</label>        ";
                }
                echo "</div>";
                echo "</div>";
                break;


            default:
                echo "<input class='form-control' type='text' name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'>";
        }
        echo "</div>";
    }


    echo '</form> </div>';
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

    $(document).ready(function () {

        reinitPopovers()
        document.addEventListener('click', function (event) {
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                const popover = bootstrap.Popover.getInstance(el);
                if (!popover) return;

                const popoverElement = document.querySelector('.popover');
                // Close when click is outside both the trigger and the popover
                if (popoverElement && !el.contains(event.target) && !popoverElement.contains(event.target)) {
                    popover.hide();
                }
            });
        });


        $('button[id$="_toggle"]').each(function () {
            var btn = $(this);
            btn.on('click', function () {
                var hiddenInput = $('#' + btn.attr('id').replace('_toggle', ''));
                if (!hiddenInput.length) return;
                var isYes = hiddenInput.val() === '1';
                if (isYes) {
                    btn.removeClass('btn-outline-success').addClass('btn-outline-primary').text('Nein');
                    hiddenInput.val('0');
                } else {
                    btn.removeClass('btn-outline-primary').addClass('btn-outline-success').text(' Ja ');
                    hiddenInput.val('1');
                }
            });
        });
    });


    function loadFormData(roomId) {
        $.post('load_room_data_userinputs.php', {roomId: roomId}, function (response) {
            if (response.error) {
                alert('Fehler: ' + response.error);
                return;
            }
            if (response.newRoom) {
                return;
            }
            const data = response.data;

            const skipFields = ['raumnr', 'roomname', 'raumbereich_nutzer', 'ebene', 'nf'];

            for (const key in data) {
                if (skipFields.includes(key)) continue;

                const el = $('[name="' + key + '"]');
                if (!el.length) continue;

                const value = data[key];

                // Handle yesno buttons: a button + hidden input pattern
                const toggleBtn = $('#' + key + '_toggle');
                if (toggleBtn.length) {
                    // Set hidden input value and button text/class
                    if (value === 1 || value === '1') {
                        toggleBtn.removeClass('btn-outline-primary').addClass('btn-outline-success').text(' Ja ');
                        el.val('1');
                    } else {
                        toggleBtn.removeClass('btn-outline-success').addClass('btn-outline-primary').text('Nein');
                        el.val('0');
                    }
                    continue; // skip normal input
                }

                // Handle radio buttons (btn-check)
                if (el.filter(':radio').length) {
                    el.filter('[value="' + value + '"]').prop('checked', true);
                    continue;
                }

                // Handle checkbox inputs
                if (el.is(':checkbox')) {
                    el.prop('checked', value === 1 || value === '1');
                    continue;
                }

                // Select inputs
                if (el.is('select')) {
                    el.val(value).trigger('change');
                    continue;
                }

                el.val(value);
            }
        }, 'json');
    }


    $('form').on('submit', function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        //console.log(formData);
        $.post('save_room_data.php', formData, function (response) {
            alert(response); // z.B. "Data successfully updated."
        }).fail(function () {
            alert('Fehler beim Speichern.');
        });
    });


</script>
</html>
