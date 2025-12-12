<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$personID = getPostInt('personID', 0);
$stmt = $mysqli->prepare("SELECT `Name`,`Vorname`,`Tel`,`Adresse`,`PLZ`,`Ort`,`Land`,`Mail` 
                         FROM `LIMET_RB`.`tabelle_ansprechpersonen` 
                         WHERE `idTABELLE_Ansprechpersonen` = ?");
$stmt->bind_param("i", $personID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$fields = [
    "Name" => "Name",
    "Vorname" => "Vorname",
    "Tel" => "Tel",
    "Adresse" => "Adresse",
    "PLZ" => "PLZ",
    "Ort" => "Ort",
    "Land" => "Land",
    "Email" => "Mail"
];
echo "<form class='form-horizontal' role='form'>";
foreach ($fields as $label => $field) {
    $value = htmlspecialchars($row[$field] ?? '', ENT_QUOTES, 'UTF-8');
    echo "<div class='form-group row'>
            <label class='control-label col-xxl-3' for='$label'>$label</label>
            <div class='col-xxl-9'>
                <input type='text' class='form-control form-control-sm' id='$label' value='$value' disabled='disabled' />
            </div>
          </div>";
}
echo "</form>";

$sql = "SELECT tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten, tabelle_projektzuständigkeiten.Zuständigkeit FROM tabelle_projektzuständigkeiten ORDER BY Zuständigkeit;";
$result = $mysqli->query($sql);

echo "<div class='form-group row'>
	 			<label class='control-label col-xxl-3' for='zustaendigkeit'>Zuständigkeit</label>
				<div class='col-xxl-8'>
					<select class='form-control form-control-sm' id='zustaendigkeit' name='selectCategory'>
						<option value=0 selected>Bitte auswählen</option>";

while ($row = $result->fetch_assoc()) {
    echo "<option value=" . $row["idTABELLE_Projektzuständigkeiten"] . ">" . $row["Zuständigkeit"] . "</option>";
}
echo "</select>	
		</div>
        <div class='col-xxl-1  m-0 p-0'>
            <button type='button' 
                    class='btn btn-outline-success' 
                    id='addZustaendigkeitBtn' title='Zustaendigkeit hinzufügen'>
                    <i class='fas fa-plus'></i>
            </button> 
        </div> 
		</div>";

$sql = "SELECT tabelle_organisation.idtabelle_organisation, tabelle_organisation.Organisation FROM tabelle_organisation ORDER BY Organisation;";
$result = $mysqli->query($sql);

echo "<div class='form-group row'>
	 			<label class='control-label col-xxl-3' for='organisation'>Organisation</label>
				<div class='col-xxl-8'>
					<select class='form-control form-control-sm' id='organisation' name='organisation'>
						<option value=0 selected>Bitte auswählen</option>";
while ($row = $result->fetch_assoc()) {
    echo "<option value=" . $row["idtabelle_organisation"] . ">" . $row["Organisation"] . "</option>";
}
echo "</select> 
        </div>
        <div class='col-xxl-1  m-0 p-0'>
            <button type='button' 
                    class='btn btn-outline-success' 
                    id='addOrganisationBtn' title='Organisation hinzufügen'>
                    <i class='fas fa-plus'></i>
            </button> 
        </div> 
		</div>
	 	<div class='form-group row'>
            <div class='col-xxl-3'></div>
              <div class='col-xxl-8'>
                <input type='button' id='" .$personID. "' class='btn btn-success btn-sm mt-1' value='Person zu Projekt hinzufügen'>
            </div>		
	 	</div>
	</form>";
$mysqli->close();
include "modal_addOrganisationAndZustaendigkeit.php";
?>

<script src="utils/_utils.js"></script>
<script src="addNewOrganisationAndZusändigkeit.js"></script>
<script>
    $("input[value='Person zu Projekt hinzufügen']").click(function () {
        let id = this.id;
        let zustaendigkeit = $("#zustaendigkeit").val();
        let organisation = $("#organisation").val();
        if (zustaendigkeit === "0") {
            alert("Keine Zuständigkeit ausgewählt!");
        } else {
            if (organisation === "0") {
                alert("Keine Organisation ausgewählt!");
            } else {
                $.ajax({
                    url: "addExistingPersonToProject.php",
                    data: {"personID": id, "zustaendigkeit": zustaendigkeit, "organisation": organisation},
                    type: "POST",
                    success: function (data) {
                        alert(data);
                        $.ajax({
                            url: "getPersonsOfProject.php",
                            type: "POST",
                            success: function (data) {
                                $("#personsInProject").html(data);
                                $.ajax({
                                    url: "getPersonsNotInProject.php",
                                    type: "POST",
                                    success: function (data) {
                                        $("#personsNotInProject").html(data);
                                    }
                                });

                            }
                        });
                    }
                });
            }
        }
    });

</script>