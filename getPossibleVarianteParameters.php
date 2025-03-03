<?php
session_start();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" /></head>
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

        $sql = "SELECT tabelle_parameter.idTABELLE_Parameter, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie 
			  					FROM tabelle_parameter, tabelle_parameter_kategorie 
			  					WHERE tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie 
								AND tabelle_parameter.idTABELLE_Parameter NOT IN 
								(SELECT tabelle_parameter.idTABELLE_Parameter 
								FROM tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.TABELLE_Parameter_idTABELLE_Parameter 
								WHERE tabelle_projekt_elementparameter.TABELLE_Elemente_idTABELLE_Elemente = " . $_SESSION["elementID"] . " AND tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION["projectID"] . " AND tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = " . $_GET["variantenID"] . ") 
								ORDER BY tabelle_parameter_kategorie.Kategorie;";

        $result = $mysqli->query($sql);

        echo "<table class='table table-striped table-sm' id='tablePossibleVarianteParameters' cellspacing='0'>
						<thead><tr>
						<th>ID</th>
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
                $('#tablePossibleVarianteParameters').DataTable({
                    "paging": false,
                    "searching": true,
                    "info": false,
                    "order": [[1, "asc"]],
                    "columnDefs": [
                        {
                            "targets": [0],
                            "visible": true,
                            "searchable": false,
                            "sortable": false
                        }
                    ],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    "scrollY": '20vh',
                    "scrollCollapse": true
                });
            });

            //Parameter zu Variante hinzufügen
            $("button[value='addParameter']").click(function () {
                let variantenID = $('#variante').val();
                var id = this.id;

                if (id !== "") {
                    $.ajax({
                        url: "addParameterToVariante.php",
                        data: {"parameterID": id, "variantenID": variantenID},
                        type: "GET",
                        success: function (data) {
                            //		        	alert(data);
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

        </script>

    </body>
</html>