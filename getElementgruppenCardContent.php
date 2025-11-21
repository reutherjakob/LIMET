<?php
// 25FX
require_once 'utils/_utils.php';
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_element_gewerke.idtabelle_element_gewerke, 
       tabelle_element_gewerke.Nummer, tabelle_element_gewerke.Gewerk
        FROM tabelle_element_gewerke
        ORDER BY tabelle_element_gewerke.Nummer;";

$result = $mysqli->query($sql);
echo "<div class='form-group row mt-1'>
<label class='control-label col-xxl-3' for='elementGewerk'>Gewerk</label>
<div class='col-xxl-9'>
<select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
$first = true;
while ($row = $result->fetch_assoc()) {
    echo "<option value='" . $row["idtabelle_element_gewerke"] . "'"
        . ">" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
}
echo "</select></div></div>
<div class='form-group row mt-1'>
        <label class='control-label col-xxl-3' for='elementHauptgruppe'>Hauptgr.</label>
        <div class='col-xxl-9'>
            <select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>
                <option selected>Gewerk auswählen</option>
            </select>	
        </div>
</div>";

echo "<div class='form-group row mt-1'>
    <label class='control-label col-xxl-3' for='elementGruppe'>Gruppe</label>
    <div class='col-xxl-9'>
        <select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>
            <option selected>Gewerk auswählen</option>
        </select>	
    </div>
</div>";

$mysqli->close();
echo " <script>

    $.ajax({
        url: 'getElementGroupsByGewerk.php',
        data: {'gewerkID': 3},
        type: 'POST',
        success: function (data) {
            $('#elementGroups').html(data);
        }
    });
    
    $('#ResetElementGroups').on('click', function () {
        $('#elementsInDB').html('');
        $('#elementGroups').html('');
        setTimeout( function () { 
            $.ajax({
                url: 'getElementGroupsByGewerk.php',
                data: {'gewerkID': 3},
                type: 'POST',
                success: function (data) {
                    $('#elementGroups').html(data);
                    $.ajax({
                        url: 'getElementsInDbCardBodyContent.php',
                        type: 'POST',
                        success: function (data) {
                            $('#elementsInDB').html(data);
                            init_table_elementsinDB();
                        }
                    });
                }
            });
        },100);
    });

</script>";
