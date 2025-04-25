<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>Get Element Groups by Gewerk </title>
</head>
<body>


<?php
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_element_gewerke.idtabelle_element_gewerke, tabelle_element_gewerke.Nummer, tabelle_element_gewerke.Gewerk
												FROM tabelle_element_gewerke
												ORDER BY tabelle_element_gewerke.Nummer;";

$result = $mysqli->query($sql);
echo "<div class='form-group row mt-1'>
 			<label class='control-label col-xxl-3' for='elementGewerk'>Gewerk</label>
			<div class='col-xxl-9'>
				<select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
while ($row = $result->fetch_assoc()) {

    if ($row["idtabelle_element_gewerke"] == $_GET["gewerkID"]) {

        echo "<option value=" . $row["idtabelle_element_gewerke"] . " selected>" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
    } else {
        echo "<option value=" . $row["idtabelle_element_gewerke"] . ">" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
    }
}
echo "</select></div></div>";

$sql = "SELECT `tabelle_element_hauptgruppe`.`idTABELLE_Element_Hauptgruppe`,
			    `tabelle_element_hauptgruppe`.`Hauptgruppe`,
			    `tabelle_element_hauptgruppe`.`Nummer`
			FROM `LIMET_RB`.`tabelle_element_hauptgruppe`
			WHERE `tabelle_element_hauptgruppe`.`tabelle_element_gewerke_idtabelle_element_gewerke` = " . $_GET["gewerkID"] . "
			ORDER BY `tabelle_element_hauptgruppe`.`Nummer`;";
$result = $mysqli->query($sql);

echo "<div class='form-group row  mt-1'>
 			<label class='control-label col-xxl-3' for='elementHauptgruppe'>Hauptgruppe</label>
			<div class='col-xxl-9'>
				<select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>";
while ($row = $result->fetch_assoc()) {
    echo "<option value=" . $row["idTABELLE_Element_Hauptgruppe"] . ">" . $row["Nummer"] . " - " . $row["Hauptgruppe"] . "</option>";
}
echo "</select></div></div>";

echo "<div class='form-group row  mt-1'>
 			<label class='control-label col-xxl-3' for='elementGruppe'>Gruppe</label>
			<div class='col-xxl-9'>
				<select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>
				</select>	
			</div>
	</div>";
$mysqli->close();
?>

<script>
    var hauptgruppeID;
    $('#elementGewerk').change(function () {
        let gewerkID = this.value;
        //onsole.log(gewerkID);
        $.ajax({
            url: "getElementGroupsByGewerk.php",
            data: {"gewerkID": gewerkID},
            type: "GET",
            success: function (data) {
                $("#elementGroups").html(data);
            }
        });
    });

    $('#elementHauptgruppe').change(function () {
        console.log("Error was ehre");
        let hauptgruppeID = this.value;

        console.log(hauptgruppeID);
        let gewerkID = $("#elementGewerk").val();
        $.ajax({
            url: "getElementGroupsByHauptgruppe.php",
            data: {"gewerkID": gewerkID, "hauptgruppeID": hauptgruppeID},
            type: "GET",
            success: function (data) {
                $("#elementGroups").html(data);
            }
        });
    });


    gewerkID = $("#elementGewerk").val();
    $.ajax({
        url: "getElementGroupsByHauptgruppe.php",
        data: {"gewerkID": gewerkID, "hauptgruppeID": 4},
        type: "GET",
        success: function (data) {
            $("#elementGroups").html(data);
        }
    });

</script>
</body>
</html>