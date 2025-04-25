<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>getVarianteParameters</title>
</head>
<body>
<?php
include "_utils.php";
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_parameter_kategorie.Kategorie, tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter
								FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
								WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente)=" . $_SESSION["elementID"] . ") AND ((tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten)=" . $_GET["variantenID"] . "))
								ORDER BY tabelle_parameter_kategorie.Kategorie ASC, tabelle_parameter.Bezeichnung ASC;";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm table-hover table-bordered border border-light border-5' id='tableElementParameters'>
	<thead><tr>
	<th></th>
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
    echo "<td><input type='text' id='wert" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Wert"] . "' size='25'></input></td>";
    echo "<td><input type='text' id='einheit" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Einheit"] . "' size='25'></input></td>";
    echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-warning btn-sm' value='saveParameter'><i class='far fa-save'></i></button></td>";
    echo "</tr>";
}

echo "</tbody></table>";

$mysqli->close();
?>
<script src="_utils.js"></script>
<script>

    $(document).ready(function () {
        $('#tableElementParameters').DataTable({
            select: true,
            searching: true,
            info: true,
            order: [[1, 'asc']],
            columnDefs: [
                {
                    targets: [0],
                    visible: true,
                    searchable: false,
                    sortable: false
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['info', 'search'],
                bottomEnd: ['paging', 'pageLength']
            },
            scrollX: true,
            initComplete: function () {
              //  $('#variantenParameterCh .xxx').remove();
              //  $('#variantenParameter .dt-search label').remove();
              //  $('#variantenParameter .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#variantenParameterCH');
            }
        });
    });

    //Parameter von Variante entfernen
    $("button[value='deleteParameter']").click(function () {
        let variantenID = $('#variante').val();
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "deleteParameterFromVariante.php",
                data: {"parameterID": id, "variantenID": variantenID},
                type: "GET",
                success: function (data) {
                    //  alert(data);
                    makeToaster(data.trim(), true);
                    $.ajax({
                        url: "getVarianteParameters.php",
                        data: {"variantenID": variantenID},
                        type: "GET",
                        success: function (data) {
                            $("#variantenParameter").html(data);
                            $.ajax({
                                url: "getPossibleVarianteParameters.php",
                                data: {"variantenID": variantenID},
                                type: "GET",
                                success: function (data) {
                                    $("#possibleVariantenParameter").html(data);
                                }
                            });
                        }
                    });
                }
            });
        }
    });

    // Parameter ändern bzw speichern
    $("button[value='saveParameter']").click(function () {
        let id = this.id;
        let wert = $("#wert" + id).val();
        let einheit = $("#einheit" + id).val();
        let variantenID = $('#variante').val();
        if (id !== "") {
            $.ajax({
                url: "updateParameter.php",
                data: {"parameterID": id, "wert": wert, "einheit": einheit, "variantenID": variantenID},
                type: "GET",
                success: function (data) {
                    makeToaster(data.trim(), true);
                }
            });
        }

    });

</script>
</body>
</html>