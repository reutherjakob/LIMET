<?php
require_once "../Nutzerlogin/_utils.php";
// init_page(["users", "spargefeld_ext_users"]);
//header('X-Frame-Options: DENY');
//header('X-Content-Type-Options: nosniff');
require_once("form_fields_forNutzergruppeX.php"); // Array mit Formfeldern, Typen, Labels usw.

$roomId = $_GET['raumid'] ?? $_POST['raumid'] ?? null;

$savedData = [];
if ($roomId !== null) {
    $stmt = $mysqli->prepare("SELECT fieldname, fieldvalue FROM your_table WHERE raumid = ?");
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $result = $stmt->get_result();
    // assuming table is structured with rows of fieldname, fieldvalue per room
    while ($row = $result->fetch_assoc()) {
        $savedData[$row['fieldname']] = $row['fieldvalue'];
    }
    $stmt->close();
}


function renderRow($field, $postVal)
{
    $name = $field['name'];
    $label = htmlspecialchars($field['label']);
    $type = $field['type'];
    $required = $field['required'] ?? false;


    $value = htmlspecialchars($postVal ?? '');


    // ID für das optionale Zusatzfeld
    $extraId = $name . "_extra";

    echo "<tr>";
    echo "<td><label for='{$name}'>{$label}</label></td>";
    echo "<td>";

    // Hauptfeld rendern
    switch ($type) {
        case 'yesno':
            $yesSelected = ($postVal === "1") ? "selected" : "";
            $noSelected = ($postVal === "0") ? "selected" : "";
            echo "<select id='{$name}' name='{$name}' class='form-select form-select-sm'>";
            echo "<option value='0' $noSelected>Nein</option>";
            echo "<option value='1' $yesSelected>Ja</option>";
            echo "</select>";
            break;

        case 'select':
            echo "<select id='{$name}' name='{$name}' class='form-select  form-select-sm'>";
            foreach ($field['options'] as $val => $display) {
                $sel = ($postVal !== null && $postVal == $val) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($val) . "' $sel>" . htmlspecialchars($display) . "</option>";
            }
            echo "</select>";
            break;

        case 'text':
        default:
            $req = $required ? "required" : "";
            echo "<input type='text' id='{$name}' name='{$name}' class='form-control  form-control-sm' value='{$value}' $req>";
            break;
    }

    echo "</td>";
    echo "<td>";
    echo "<input type='text' id='{$extraId}' name='{$extraId}' class='form-control  form-control-sm d-none' placeholder='Kommentar'>";
    echo "</td>";
    echo "</tr>";
}


?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Raumanforderungen Formular</title>
    <script>
        $(function () {
            // Für jedes Hauptfeld prüfen, ob Eingabewert vom Default abweicht,
            // dann Zusatzfeld einblenden. Definition der Defaultwerte (anpassen je Feldtyp)
            <?php foreach ($formFields as $field):
            $name = $field['name'];
            $type = $field['type'];
            // Defaultwerte
            if ($type === "yesno" || $type === "select") {
                $default = "0";
            } else {
                $default = "";
            }
            ?>
            $('#<?= $name ?>').on('change input', function () {
                var val = $(this).val();
                if (val !== '<?= $default ?>') {
                    $('#<?= $name ?>_extra').removeClass('d-none');
                } else {
                    $('#<?= $name ?>_extra').addClass('d-none').val('');
                }
            });
            <?php endforeach; ?>
        });
    </script>
</head>
<body class="">
<form name="raumForm" method="post" onsubmit="return validateForm();">
    <table class="table table-sm compact table-borderless table-striped px-1 py-1" id="tableNutzeranforderungen">
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
            $postVal = $_POST[$field['name']] ?? $savedData[$field['name']] ?? null;
            renderRow($field, $postVal);
        }
        ?>
        ?>
        </tbody>
    </table>

    <button id="submitAnforderungen" type="submit" value="Raumanforderung Absenden"
            class="btn btn-primary btn-sm text-nowrap"><i
                class="fas fa-share-square"></i> Raumanforderung Absenden
    </button>
</form>

<script>
    $(document).ready(function () {
        var tableId = '#tableNutzeranforderungen';

        // Check if DataTable is already initialized on this element
        if ($.fn.dataTable.isDataTable(tableId)) {
            // If initialized, destroy it first before re-initializing
            $(tableId).DataTable().clear().destroy();
        }

        $(tableId).DataTable({
            paging: false,

            autoWidth: false, // disable automatic width calculation
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: null
            }, ordering: false,
            columnDefs: [
                {targets: '_all', width: '33.33%'} // equal width for all columns (3 columns)
            ]
        });

        $('#cardHeaderRaumanforderungen').append($('#submitAnforderungen').show());
    });


    function validateForm() {
        let requiredFields = <?php
            $reqFields = [];
            foreach ($formFields as $f) {
                if (!empty($f['required'])) {
                    $reqFields[] = $f['name'];
                }
            }
            echo json_encode($reqFields);
            ?>;
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
</body>
</html>
