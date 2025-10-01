<?php
global $mysqli;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
init_page(["internal_rb_user", "spargefeld_ext_users"]);
require_once("form_fields_forNutzergruppeX.php"); // Array mit Formfeldern, Typen, Labels usw.

error_reporting(E_ALL);
ini_set('display_errors', 1);
$roomId = $_POST['raumid'] ?? null;
error_log("Received roomId: " . var_export($roomId, true));
//echo "Room ID: " . htmlspecialchars($roomId) . "<br>";

$savedData = [];
if ($roomId !== null) {
    $stmt = $mysqli->prepare("SELECT * FROM tabelle_room_requirements_from_user WHERE roomID = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $mysqli->error);
        echo "Error preparing statement.<br>";
    } else {
        $stmt->bind_param("i", $roomId);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            echo "Error executing statement.<br>";
        } else {
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                if ($row) {
                    $savedData = $row;
                    error_log("Fetched savedData: " . var_export($savedData, true));
                } else {
                    error_log("No data found for roomID " . $roomId);
                    // No data found, so savedData remains empty to render defaults
                }
                $result->free();
            } else {
                error_log("get_result failed: " . $stmt->error);
                echo "Error retrieving results.<br>";
            }
        }
        $stmt->close();
    }
} else {
    error_log("No roomId provided in POST data.");
    echo "No room ID specified.<br>";
}

function renderRow($field, $postVal, $savedData)
{
    $name = $field['name'];
    $label = htmlspecialchars($field['label']);
    $type = $field['type'];
    $required = $field['required'] ?? false;

    // Determine value to use:
    // If savedData exists for this field and POST does NOT have a value, use savedData
    // else if POST has a non-empty value, use POST (to preserve user input)
    // else use default value
    if (isset($savedData[$name]) && (!isset($_POST[$name]) || $_POST[$name] === '')) {
        $val = $savedData[$name];
    } elseif (isset($_POST[$name]) && $_POST[$name] !== '') {
        $val = $_POST[$name];
    } else {
        $val = $field['default_value'] ?? '';
    }
    $valEscaped = htmlspecialchars($val);

    echo "<!-- Rendering field: $name, value used: " . htmlspecialchars($val) . " -->\n";

    // Comment field related to this input field
    $extraId = $name . "_comment";

    // Same logic for comment field values
    if (isset($savedData[$extraId]) && (!isset($_POST[$extraId]) || $_POST[$extraId] === '')) {
        $extraVal = $savedData[$extraId];
    } elseif (isset($_POST[$extraId]) && $_POST[$extraId] !== '') {
        $extraVal = $_POST[$extraId];
    } else {
        $extraVal = '';
    }
    $extraValEscaped = htmlspecialchars($extraVal);

    echo "<tr>";
    echo "<td><label for='{$name}'>{$label}</label></td>";
    echo "<td>";

    switch ($type) {
        case 'yesno':
            $yesSelected = ($val === "1") ? "selected" : "";
            $noSelected = ($val === "0") ? "selected" : "";
            echo "<select id='{$name}' name='{$name}' class='form-select form-select-sm'>";
            echo "<option value='0' $noSelected>Nein</option>";
            echo "<option value='1' $yesSelected>Ja</option>";
            echo "</select>";
            break;

        case 'select':
            echo "<select id='{$name}' name='{$name}' class='form-select form-select-sm'>";
            // Loop over options
            foreach ($field['options'] as $valOpt => $display) {
                $sel = ($val !== null && $val == $valOpt) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($valOpt) . "' $sel>" . htmlspecialchars($display) . "</option>";
            }
            echo "</select>";
            break;

        case 'text':
        default:
            $req = $required ? "required" : "";
            echo "<input type='text' id='{$name}' name='{$name}' class='form-control form-control-sm' value='{$valEscaped}' $req>";
            break;
    }
    echo "</td>";

    if ($type !== 'text') {
        echo "<td>";
        echo "<input type='text' id='{$extraId}' name='{$extraId}' class='form-control form-control-sm' placeholder='Kommentar' value='{$extraValEscaped}'>";
        echo "</td>";
    } else {
        echo "<td></td>";
    }
    echo "</tr>";
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Raumanforderungen Formular</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script>
        $(function () {
            <?php foreach ($formFields as $field):
            $name = $field['name'];
            $type = $field['type'];
            $default = in_array($type, ['yesno', 'select']) ? "0" : "";
            ?>
            $('#<?= $name ?>').on('change input', function () {
                var val = $(this).val();
                if (val !== '<?= $default ?>') {
                    $('#<?= $name ?>_comment').show();
                } else {
                    $('#<?= $name ?>_comment').hide().val('');
                }
            });

            // Trigger at load time so comments of default values are hidden if empty
            $('#<?= $name ?>').trigger('change');
            <?php endforeach; ?>
        });
    </script>
</head>
<body>
<form name="raumForm" method="post">
    <input type="hidden" name="roomID" value="<?= htmlspecialchars($roomId) ?>">
    <table class="table table-sm compact table-borderless table-striped px-1 " id="tableNutzeranforderungen">
        <thead>
        <tr>
            <th>Raumparameter</th>
            <th>Wert</th>
            <th>Kommentar</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($formFields as $field) {
            renderRow($field, $_POST[$field['name']] ?? null, $savedData);
        }
        ?>
        </tbody>
    </table>
    <button id="submitAnforderungen" type="submit" name="submit" class="btn btn-primary col-12 text-nowrap">
        <i class="fas fa-share-square"></i> Raumanforderung Absenden
    </button>
</form>

<script>
    $(document).ready(function () {
        var tableId = '#tableNutzeranforderungen';
        try {
            if ($.fn.dataTable.isDataTable(tableId)) {
                $(tableId).DataTable().clear().destroy();
            }
        } catch (e) {
            console.log(e);
        }
        $(tableId).DataTable({
            paging: false,
            autoWidth: false,
            ordering: false,
            info: false,
            searching: false,
            columnDefs: [
                {targets: '_all', width: '33.33%'}
            ]
        });


        $('form[name="raumForm"]').on('submit', function (e) {
            e.preventDefault(); // prevent default form submission
            var formData = $(this).serialize();
            $.ajax({
                url: 'save.php',  // URL to the PHP script that saves the data
                type: 'POST',
                data: formData,
                dataType: 'text',
                success: function (response) {
                    alert("Server response: " + response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert("Error saving data: " + textStatus + ' - ' + errorThrown);
                }
            });
        });
    });
</script>
</body>
</html>
