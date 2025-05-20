<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
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
<div class='form-group inline row'><label class='control-label col-xxl-2' for='organisation'> Organisation</label>
    <div class='col-xxl-7'>
    <select class='form-control form-control-sm' id='organisation' name='organisation' required>  $organisationOptions</select>  
    </div>
      <div class='col-xxl-1'>
<button type='button' class='btn btn-outline-primary btn-sm form-control ' id='addOrganisationBtn' title='Organisation hinzufügen'>
     +</button> </div>
<div class='form-group row'>
    <div class='col-xxl-offset-2 col-xxl-8 mt-2'><input type='submit' id='addPersonToProjectButton'
                                                 class='btn btn-success btn-sm' value='Person zu Projekt hinzufügen'>
    </div></div></form > ";
;

$mysqli->close();
?>

<!-- Add Organisation Modal -->
<div class="modal fade" id="addOrganisationModal" tabindex="-1" aria-labelledby="addOrganisationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrganisationModalLabel">Neue Organisation hinzufügen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="newOrganisationName" placeholder="Organisationsname">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-success" id="saveOrganisationBtn">Speichern</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        // Show modal on button click
        $("#addOrganisationBtn").click(function () {
            $("#newOrganisationName").val('');
            $("#addOrganisationModal").modal('show');
        });

        // Save new organisation via AJAX
        $("#saveOrganisationBtn").click(function () {
            let orgName = $("#newOrganisationName").val().trim();
            if (orgName === "") {
                alert("Bitte geben Sie einen Organisationsnamen ein.");
                return;
            }
            $.ajax({
                url: "saveOrganisation.php",
                type: "POST",
                data: { name: orgName },
                success: function (response) {
                    // Assuming response is the new organisation ID and name as JSON
                    try {
                        var data = JSON.parse(response);
                        if (data.success) {
                            // Add new option to select and select it
                            var newOption = $("<option>")
                                .val(data.id)
                                .text(data.name)
                                .prop("selected", true);
                            $("#organisation").append(newOption);
                            $("#addOrganisationModal").modal('hide');
                        } else {
                            alert(data.error || "Fehler beim Hinzufügen der Organisation.");
                        }
                    } catch (e) {
                        alert("Fehler beim Verarbeiten der Antwort.");
                    }
                },
                error: function () {
                    alert("Fehler beim Speichern der Organisation.");
                }
            });
        });

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
