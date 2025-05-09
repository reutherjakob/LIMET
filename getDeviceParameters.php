<?php
session_start();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
</head>
<body>
<?php
if (!isset($_SESSION["username"])) {
    echo "Bitte erst <a href=\"index.php\">einloggen</a>";
    exit;
}
?>

<?php
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');


/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}


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
    echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-outline-danger btn-sm' value='deleteParameter'><i class='fas fa-minus'></i></button></td>";
    echo "<td>" . $row["Kategorie"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td><input type='text' id='wert" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Wert"] . "'></input></td>";
    echo "<td><input type='text' id='einheit" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Einheit"] . "'></input></td>";
    echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-warning btn-sm' value='saveParameter'><i class='far fa-save'></i></button></td>";
    echo "</tr>";

}
echo "</tbody></table>";

$mysqli->close();
?>

<script>


    $('#tableDeviceParameters').DataTable({
        "paging": false,
        "searching": true,
        "info": false,
        "order": [[1, "asc"]],
        "columnDefs": [
            {
                "targets": [0],
                "visible": true,
                "searchable": false
            }
        ],
        "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
        "scrollY": '20vh',
        "scrollCollapse": true
    });

    //Parameter von Gerät entfernen
    $("button[value='deleteParameter']").click(function () {
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
    $("button[value='saveParameter']").click(function () {
        var id = this.id;
        var wert = $("#wert" + id).val();
        var einheit = $("#einheit" + id).val();

        if (id !== "") {
            $.ajax({
                url: "updateDeviceParameter.php",
                data: {"parameterID": id, "wert": wert, "einheit": einheit},
                type: "GET",
                success: function (data) {
                    alert(data);

                }
            });
        }
    });

</script>

</body>
</html>