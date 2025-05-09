<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title></head>
<body>

<?php

include "_utils.php";
check_login();

$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_parameter.idTABELLE_Parameter, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie 
        FROM tabelle_parameter, tabelle_parameter_kategorie 
        WHERE tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie 
        AND tabelle_parameter.idTABELLE_Parameter NOT IN 
        (SELECT tabelle_parameter.idTABELLE_Parameter 
        FROM tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.TABELLE_Parameter_idTABELLE_Parameter 
        WHERE tabelle_projekt_elementparameter.TABELLE_Elemente_idTABELLE_Elemente = " . $_SESSION["elementID"] . " AND tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION["projectID"] . " 
        AND tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = " . $_GET["variantenID"] . ") 
        ORDER BY tabelle_parameter_kategorie.Kategorie;";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm table-hover table-bordered border border-5 border-light' id='tablePossibleElementParameters'>
						<thead><tr>
                        <th> <i class='fas fa-plus'></i> </th>
						<th>Kategorie</th>
						<th>Parameter</th>
						</tr></thead>
						<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Parameter"] . "' class='btn btn-outline-success btn-sm' value='addParameter'><i class='fas fa-plus'></i></button></td>";
    echo "<td>" . $row["Kategorie"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";

$mysqli->close();
?>
<script src="_utils.js"></script>

<script>

    $(document).ready(function () {
        tablePossibleElementParameters = null;
        tablePossibleElementParameters = $('#tablePossibleElementParameters').DataTable({
            select: true,
            searching: true,
            info: false,
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
                $('#mglParameterCardHeader .xxx').remove();
                $('#possibleVariantenParameter .dt-search label').remove();
                $('#possibleVariantenParameter .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#mglParameterCardHeader');
            }
        });
    });


    //Parameter zu Variante hinzufügen
    $("button[value='addParameter']").click(function () {
        $('#variantenParameterCh .xxx').remove();
        let variantenID = $('#variante').val();
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "addParameterToVariante.php",
                data: {"parameterID": id, "variantenID": variantenID},
                type: "GET",
                success: function (data) {
                    makeToaster(data, data.trim().substring(0, 4) === "Para");
                    $.ajax({
                        url: "getVarianteParameters.php",
                        data: {"variantenID": variantenID},
                        type: "GET",
                        success: function (data) {
                            $('#variantenParameterCh .xxx').remove();
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
</script>
</body>
</html>