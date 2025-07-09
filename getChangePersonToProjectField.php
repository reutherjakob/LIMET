<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();

$mysqli = utils_connect_sql();

/**
 * Fetches all options for a select dropdown.
 */
function getSelectOptions($mysqli, $sql, $valueField, $textField, $selectedValue = null)
{
    $result = $mysqli->query($sql);
    $options = '';
    while ($row = $result->fetch_assoc()) {
        $selected = ($row[$valueField] == $selectedValue) ? 'selected' : '';
        $options .= "<option value='" . htmlspecialchars($row[$valueField]) . "' $selected>" .
            htmlspecialchars($row[$textField]) .
            "</option>";
    }
    return $options;
}

// --- Fetch person data ---
$personSql = <<<SQL
SELECT
    tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte,
    tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen,
    tabelle_ansprechpersonen.Name,
    tabelle_ansprechpersonen.Vorname,
    tabelle_ansprechpersonen.Tel,
    tabelle_ansprechpersonen.Adresse,
    tabelle_ansprechpersonen.PLZ,
    tabelle_ansprechpersonen.Ort,
    tabelle_ansprechpersonen.Land,
    tabelle_ansprechpersonen.Mail,
    tabelle_ansprechpersonen.Raumnr,
    tabelle_organisation.idtabelle_organisation,
    tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten
FROM tabelle_ansprechpersonen
INNER JOIN (
    tabelle_organisation
    INNER JOIN (
        tabelle_projektzuständigkeiten
        INNER JOIN tabelle_projekte_has_tabelle_ansprechpersonen
        ON tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten = tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten
    )
    ON tabelle_organisation.idtabelle_organisation = tabelle_projekte_has_tabelle_ansprechpersonen.tabelle_organisation_idtabelle_organisation
)
ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen
WHERE tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte = {$_SESSION["projectID"]}
  AND tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen = {$_GET["personID"]}
SQL;

$personResult = $mysqli->query($personSql);
$person = $personResult->fetch_assoc();

// --- Prepare select options ---
$zustaendigkeitOptions = getSelectOptions(
    $mysqli,
    "SELECT idTABELLE_Projektzuständigkeiten, Zuständigkeit FROM tabelle_projektzuständigkeiten ORDER BY Zuständigkeit",
    "idTABELLE_Projektzuständigkeiten",
    "Zuständigkeit",
    $person["idTABELLE_Projektzuständigkeiten"] ?? null
);

$organisationOptions = getSelectOptions(
    $mysqli,
    "SELECT idtabelle_organisation, Organisation FROM tabelle_organisation ORDER BY Organisation",
    "idtabelle_organisation",
    "Organisation",
    $person["idtabelle_organisation"] ?? null
);

// --- Define form fields ---
$formFields = [
    ['Name', 'text', $person['Name'] ?? ''],
    ['Vorname', 'text', $person['Vorname'] ?? ''],
    ['Tel', 'text', $person['Tel'] ?? ''],
    ['Adresse', 'text', $person['Adresse'] ?? ''],
    ['PLZ', 'text', $person['PLZ'] ?? ''],
    ['Ort', 'text', $person['Ort'] ?? ''],
    ['Land', 'text', $person['Land'] ?? ''],
    ['Email', 'email', $person['Mail'] ?? ''],
    ['Raumnr', 'text', $person['Raumnr'] ?? ''],
];

// --- Render form ---
?>
<form class="form-horizontal" role="form">
    <?php foreach ($formFields as [$label, $type, $value]): ?>
        <div class="form-group row">
            <label class="control-label col-xxl-2" for="<?= $label ?>"><?= $label ?></label>
            <div class="col-xxl-8">
                <input type="<?= $type ?>" class="form-control form-control-sm" id="<?= $label ?>"
                       value="<?= htmlspecialchars($value) ?>">
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Zuständigkeit Select + Add Button -->
    <div class="form-group row">
        <label class="control-label col-xxl-2" for="zustaendigkeit">Zuständigkeit</label>
        <div class="col-xxl-8">
            <div class="input-group">
                <select class="form-control form-control-sm" id="zustaendigkeit" name="zustaendigkeit">
                    <?= $zustaendigkeitOptions ?>
                </select>
                <button type="button" class="btn btn-outline-success" id="addZustaendigkeitBtn"
                        title="Zuständigkeit hinzufügen">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Organisation Select + Add Button -->
    <div class="form-group row">
        <label class="control-label col-xxl-2" for="organisation">Organisation</label>
        <div class="col-xxl-8">
            <div class="input-group">
                <select class="form-control form-control-sm" id="organisation" name="organisation">
                    <?= $organisationOptions ?>
                </select>
                <button type="button" class="btn btn-outline-success" id="addOrganisationBtn"
                        title="Organisation hinzufügen">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-check-inline">
        <button
                type="button"
                id="btn-edit"
                class="btn btn-warning btn-sm mt-2"
                data-person-id="<?= $_GET["personID"] ?>"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Personendaten ändern">
            Personendaten ändern <i class="fas fa-edit"></i>
        </button>
        <button
                type="button"
                id="btn-remove"
                class="btn btn-danger btn-sm mt-2"
                data-person-id="<?= $_GET["personID"] ?>"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Person von Projekt entfernen">
            Person von Projekt entfernen <i class="fas fa-minus"></i>
        </button>
    </div>

</form>
<?php
$mysqli->close();
?>

<script src="utils/_utils.js"></script>
<script src="addNewOrganisationAndZusändigkeit.js"></script>
<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').each(function () {
            new bootstrap.Tooltip(this);
        });

        // Save person data (delegated)
        $(document).on('click', '#btn-edit', function () {
            let personID = $(this).data("person-id");
            let data = {
                Name: $("#Name").val(),
                Vorname: $("#Vorname").val(),
                Tel: $("#Tel").val(),
                Adresse: $("#Adresse").val(),
                PLZ: $("#PLZ").val(),
                Ort: $("#Ort").val(),
                Land: $("#Land").val(),
                Email: $("#Email").val(),
                personID: personID,
                zustaendigkeit: $("#zustaendigkeit").val(),
                organisation: $("#organisation").val(),
                Raumnr: $("#Raumnr").val()
            };
            console.log(JSON.stringify(data));

            if (data.Name && data.Vorname && data.Tel) {
                $.get("savePersonProjectData.php", data, function (response) {
                    makeToaster(response,true);
                    $("#personsInProject").load("getPersonsOfProject.php");
                });
            } else {
                alert("Bitte überprüfen Sie Ihre Angaben");
            }
        });

        // Remove person from project (delegated)
        $(document).on('click', '#btn-remove', function () {
            let personID = $(this).data("person-id");
            $.get("deletePersonFromProject.php", {personID: personID}, function (response) {
                alert(response);
                $("#personsInProject").load("getPersonsOfProject.php", function () {
                    $("#personsNotInProject").load("getPersonsNotInProject.php");
                });
            }).fail(function () {
                alert("Fehler beim Entfernen der Person.");
            });
        });
    });


</script>
