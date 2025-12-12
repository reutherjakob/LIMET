<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$gewerkeID = getPostInt('gewerkID', 0);
$hptgrID = getPostInt("hauptgruppeID", 0);

if ($gewerkeID === 0 || $hptgrID === 0) {
    die("Gerweke ID oder Hauptgruppen ID = 0? ");
}
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_element_gewerke.idtabelle_element_gewerke, 
           tabelle_element_gewerke.Nummer, 
           tabelle_element_gewerke.Gewerk
        FROM tabelle_element_gewerke
        ORDER BY tabelle_element_gewerke.Nummer;";
$result = $mysqli->query($sql);

echo "<div class='form-group row  mt-1'>
        <label class='control-label col-xxl-3' for='elementGewerk'>Gewerk</label>
        <div class='col-xxl-9'>
        <select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
while ($row = $result->fetch_assoc()) {
    if ($row["idtabelle_element_gewerke"] == $gewerkeID) {
        echo "<option value=" . $row["idtabelle_element_gewerke"] . " selected>" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
    } else {
        echo "<option value=" . $row["idtabelle_element_gewerke"] . ">" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
    }
}
echo "</select>	</div>	</div>";

$sql = "SELECT `tabelle_element_hauptgruppe`.`idTABELLE_Element_Hauptgruppe`,
			    `tabelle_element_hauptgruppe`.`Hauptgruppe`,
			    `tabelle_element_hauptgruppe`.`Nummer`
			FROM `LIMET_RB`.`tabelle_element_hauptgruppe`
			WHERE `tabelle_element_hauptgruppe`.`tabelle_element_gewerke_idtabelle_element_gewerke` = ? 
			ORDER BY `tabelle_element_hauptgruppe`.`Nummer`";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $gewerkeID);
$stmt->execute();
$result = $stmt->get_result();

echo "<div class='form-group row mt-1'>
        <label class='control-label col-xxl-3' for='elementHauptgruppe'>Hauptgr.</label>
        <div class='col-xxl-9'>
        <select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>";
while ($row = $result->fetch_assoc()) {
    if ($row["idTABELLE_Element_Hauptgruppe"] == $hptgrID) {
        echo "<option value=" . $row["idTABELLE_Element_Hauptgruppe"] . " selected>" . $row["Nummer"] . " - " . $row["Hauptgruppe"] . "</option>";
    } else {
        echo "<option value=" . $row["idTABELLE_Element_Hauptgruppe"] . ">" . $row["Nummer"] . " - " . $row["Hauptgruppe"] . "</option>";
    }
}
echo "</select></div></div>";

$sql = "SELECT `tabelle_element_gruppe`.`idTABELLE_Element_Gruppe`,
		    `tabelle_element_gruppe`.`Gruppe`,
		    `tabelle_element_gruppe`.`Nummer`
		FROM `LIMET_RB`.`tabelle_element_gruppe`
		WHERE `tabelle_element_gruppe`.`tabelle_element_hauptgruppe_idTABELLE_Element_Hauptgruppe` = ?
		ORDER BY `tabelle_element_gruppe`.`Nummer`";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $hptgrID);
$stmt->execute();
$result = $stmt->get_result();
echo "<div class='form-group row mt-1'>
        <label class='control-label col-xxl-3' for='elementGruppe'>Gruppe</label>
        <div class='col-xxl-9'>
        <select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>";
echo "    <option value=0 selected>Gruppe auswählen</option>";
while ($row = $result->fetch_assoc()) {
    echo "<option value=" . $row["idTABELLE_Element_Gruppe"] . ">" . $row["Nummer"] . " - " . $row["Gruppe"] . "</option>";
}
echo "</select> </div> </div>";
$stmt->close();
$mysqli->close();
?>


<script>
    $('#elementGewerk').change(function () {
        let gewerkID = this.value;
        $.ajax({
            url: "getElementGroupsByGewerk.php",
            data: {"gewerkID": gewerkID},
            type: "POST",
            success: function (data) {
                $("#elementGroups").html(data);
            }
        });
    });

    $('#elementHauptgruppe').change(function () {
        let hauptgruppeID = this.value;
        console.log("HaupgruppernID:", hauptgruppeID);
        let gewerkID = $("#elementGewerk").val();
        $.ajax({
            url: "getElementGroupsByHauptgruppe.php",
            data: {"gewerkID": gewerkID, "hauptgruppeID": hauptgruppeID},
            type: "POST",
            success: function (data) {
                $("#elementGroups").html(data);
            }
        });
    });

    $('#elementGruppe').change(function () {
        let gruppeID = this.value;
        // console.log("GetElByHauptgr: GruppenID", gruppeID);
        if (gruppeID !== 0) {
            $.ajax({
                url: "getElementsByGroup.php",
                data: {"gruppeID": gruppeID},
                type: "POST",
                success: function (data) {
                    $("#elementsInDB").html(data);
                }
            });
        }
    });
</script>