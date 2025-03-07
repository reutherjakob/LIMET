<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();
$mysqli = utils_connect_sql();

function getOptions($sql, $valueField, $textField)
{
    global $mysqli;
    $result = $mysqli->query($sql);
    $options = "";
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='" . htmlspecialchars($row[$valueField] ?? '') . "'>" . htmlspecialchars($row[$textField] ?? '') . "</option>";
    }
    return $options;
}

$zustaendigkeitOptions = getOptions(
    "SELECT idTABELLE_Projektzuständigkeiten, Zuständigkeit FROM tabelle_projektzuständigkeiten ORDER BY Zuständigkeit",
    "idTABELLE_Projektzuständigkeiten",
    "Zuständigkeit"
);

$organisationOptions = getOptions(
    "SELECT idtabelle_organisation, Organisation FROM tabelle_organisation ORDER BY Organisation",
    "idtabelle_organisation",
    "Organisation"
);

$formFields = [
    ['Name', 'text'],
    ['Vorname', 'text'],
    ['Tel', 'tel'],
    ['Adresse', 'text'],
    ['PLZ', 'text'],
    ['Ort', 'text'],
    ['Land', 'text'],
    ['Email', 'email'],
    ['Raumnr', 'text']
];

echo "<form id='addPersonForm' class='form-horizontal' role='form' method='POST'>";

foreach ($formFields as $field) {
    echo "<div class='form-group row'>
            <label class='control-label col-xxl-2' for='{$field[0]}'>{$field[0]}</label>
            <div class='col-xxl-8'>
                <input type='{$field[1]}' class='form-control form-control-sm' id='{$field[0]}' name='{$field[0]}' required>
            </div>
          </div>";
}

echo "
<div class='form-group row'><label class='control-label col-xxl-2' for='zustaendigkeit'> Zuständigkeit</label>
    <div class='col-xxl-8'><select class='form-control form-control-sm' id='zustaendigkeit' name='zustaendigkeit'
                                  required> $zustaendigkeitOptions</select></div>
</div>
<div class='form-group row'><label class='control-label col-xxl-2' for='organisation'> Organisation</label>
    <div class='col-xxl-8'><select class='form-control form-control-sm' id='organisation' name='organisation' required>
            $organisationOptions</select></div>
</div>
<div class='form-group row'>
    <div class='col-xxl-offset-2 col-xxl-8'><input type='submit' id='addPersonToProjectButton'
                                                 class='btn btn-success btn-sm' value='Person zu Projekt hinzufügen'>
    </div></div></form > ";


$mysqli->close();
?>

<script>
    $(document).ready(function () {
        $("#addPersonForm").submit(function (e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "addPersonToProject.php",
                data: formData,
                type: "POST",
                success: function (data) {
                    alert(data);
                    $("#personsInProject").load("getPersonsOfProject.php");
                },
                error: function () {
                    alert("Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.");
                }
            });
        });
    });
</script>
