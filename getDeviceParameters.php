<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title>
</head>
<body>
<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung, tabelle_geraete_has_tabelle_parameter.Wert, tabelle_geraete_has_tabelle_parameter.Einheit, tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter, tabelle_geraete_has_tabelle_parameter.tabelle_parameter_idTABELLE_Parameter
                    FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_geraete_has_tabelle_parameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                    WHERE (((tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete)=" . $_SESSION["deviceID"] . "));";


$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm' id='tableDeviceParameters'  >
        <thead><tr>
        <th>ID</th>
        <th>Kategorie</th>
        <th>Parameter</th>
        <th>Wert</th>
        <th>Einheit</th>
        <th></th>
        </tr></thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-outline-danger btn-sm' value='deleteDEVICEParameter'><i class='fas fa-minus'></i></button></td>";
    echo "<td>" . $row["Kategorie"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td><input type='text' id='wert" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Wert"] . "'></td>";
    echo "<td><input type='text' id='einheit" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Einheit"] . "'></td>";
    echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-warning btn-sm' value='saveDEVICEParameter'><i class='far fa-save'></i></button></td>";
    echo "</tr>";

}
echo "</tbody></table>";

$mysqli->close();
?>

<script>


    new DataTable('#tableDeviceParameters', {
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
            topEnd: null,
            bottomStart: null,
            bottomEnd: 'search'
        },
        initComplete: function () {
            $('#CardHeaderGeräteParameter .xxx').remove();
            $('#GeräteparameterCard .dt-search label').remove();
            $('#GeräteparameterCard .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#CardHeaderGeräteParameter');
        }
    });


    //Parameter von Gerät entfernen
    $("button[value='deleteDEVICEParameter']").click(function () {
        if (confirm("Parameter wirklich löschen?")) {
            var id = this.id;
            if (id !== "") {
                $.ajax({
                    url: "deleteParameterFromDevice.php",
                    data: {"parameterID": id},
                    type: "GET",
                    success: function (data) {
                        alert(data);
                        $.ajax({
                            url: "getDeviceParameters.php",
                            type: "GET",
                            success: function (data) {

                                $("#deviceParameters").html(data);
                                $.ajax({
                                    url: "getPossibleDeviceParameters.php",
                                    type: "GET",
                                    success: function (data) {
                                        $("#possibleDeviceParameters").html(data);
                                    }
                                });

                            }
                        });
                    }
                });
            }
        }
    });

    //Parameter ändern
    $("button[value='saveDEVICEParameter']").click(function () {
        var id = this.id;
        var wert = $("#wert" + id).val();
        var einheit = $("#einheit" + id).val();

        if (id !== "") {
            $.ajax({
                url: "updateDeviceParameter.php",
                data: {"parameterID": id, "wert": wert, "einheit": einheit},
                type: "GET",
                success: function (data) {
                    makeToaster(data, true);

                }
            });
        }
    });

</script>

</body>
</html>