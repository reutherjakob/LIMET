<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>getPossibleDevParams</title></head>
<body>
<?php
include "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_parameter.idTABELLE_Parameter, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie 
			  					FROM tabelle_parameter, tabelle_parameter_kategorie 
			  					WHERE tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie 
								AND tabelle_parameter.idTABELLE_Parameter NOT IN 
								(SELECT tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter
                                                                FROM tabelle_geraete_has_tabelle_parameter
                                                                WHERE (((tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete)=" . $_SESSION["deviceID"] . "))) 
								ORDER BY tabelle_parameter_kategorie.Kategorie;";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm' id='tablePossibleDeviceParameters'>
        <thead><tr>
        <th>ID</th>
        <th>Kategorie</th>
        <th>Parameter</th>
        </tr></thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Parameter"] . "' class='btn btn-outline-success btn-sm' value='addDEVICEParameter'><i class='fas fa-plus'></i></button></td>";
    echo "<td>" . $row["Kategorie"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "</tr>";

}

echo "</tbody></table>";

$mysqli->close();
?>

<script>


    new DataTable('#tablePossibleDeviceParameters', {
        paging: false,
        searching: true,
        info: false,
        order: [[1, "asc"]],
        columnDefs: [
            {
                targets: [0],
                visible: true,
                searchable: false
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
        },
        scrollY: '20vh',
        scrollCollapse: true,
        layout: {
            topStart: null,
            topEnd: 'search',
            bottomStart: null,
            bottomEnd: null
        }, initComplete: function () {
            $('#possibleDeviceParametersCH .xxx').remove();
            $('#possibleDeviceParameters .dt-search label').remove();
            $('#possibleDeviceParameters .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx float-right").appendTo('#possibleDeviceParametersCH');
        }
    });

    //Parameter zu Gerät hinzufügen
    $("button[value='addDEVICEParameter']").click(function () {
        $('#variantenParameterCh .xxx').remove();
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "addParameterToDevice.php",
                data: {"parameterID": id},
                type: "POST",
                success: function (data) {
                    makeToaster(data, true);
                    $.ajax({
                        url: "getDeviceParameters.php",
                        type: "POST",
                        success: function (data) {
                            $("#deviceParameters").html(data);
                            $.ajax({
                                url: "getPossibleDeviceParameters.php",
                                type: "POST",
                                success: function (data) {
                                    $("#possibleDeviceParameters").html(data);
                                }
                            });

                        }
                    });
                }
            });
        }
    });


</script>

</body>
</html>