<?php
// 26 FX 
require_once '_utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$sql = "SELECT
            tabelle_elemente.idTABELLE_Elemente, 
            tabelle_elemente.ElementID, 
            tabelle_elemente.Bezeichnung, 
            tabelle_elemente.Kurzbeschreibung
        FROM tabelle_elemente
        ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);

echo "<table class='table table-striped table-condensed' id='tableElementsInDB'   >
        <thead><tr>
        <th>ID</th>
        <th></th>
        <th>ElementID</th>
        <th>Element</th>
        <th>Beschreibung</th>
        <th></th>
        </tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . h($row["idTABELLE_Elemente"]) . "</td>";
    echo "<td><button type='button' id='" . h($row["idTABELLE_Elemente"]) . "' class='btn btn-success btn-sm' value='addElement' data-bs-toggle='modal' data-bs-target='#addRoomElementModal'><span class='glyphicon glyphicon-plus'></span></button></td>";
    echo "<td>" . h($row["ElementID"]) . "</td>";
    echo "<td>" . h($row["Bezeichnung"]) . "</td>";
    echo "<td>" . h($row["Kurzbeschreibung"]) . "</td>";
    echo "<td><button type='button' id='" . h($row["idTABELLE_Elemente"]) . "' class='btn btn-default btn-sm' value='changeElement' data-bs-toggle='modal' data-bs-target='#changeElementModal'><span class='glyphicon glyphicon-pencil'></span></button></td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();

include "addRoomElementModal.html";
?>

<script src="_utils/_utils.js"></script>
<script src="addElementToRoom.js"></script>
<script type='text/javascript' src="_utils/_utils.js"></script>
<script>
    var tableElementsInDB;
    $(document).ready(function () {

        tableElementsInDB = new DataTable('#tableElementsInDB', {
            paging: true,
            info: true,
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [1],
                    visible: false,
                    searchable: false
                }
            ],
            order: [[1, "asc"]],
            language: {
                url: "/_utils/de-DE.json",
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: 'info',
                bottomEnd: 'paging'
            }
        });


        $('#tableElementsInDB tbody').on('click', 'tr', function () {
            $("#deviceParametersInDB").hide();
            $("#devicePrices").hide();
            $("#deviceLieferanten").hide();
            document.getElementById("bezeichnung").value = tableElementsInDB.row($(this)).data()[3];
            document.getElementById("kurzbeschreibungModal").value = tableElementsInDB.row($(this)).data()[4];
            tableElementsInDB.$('tr.info').removeClass('info');
            $(this).addClass('info');
            let elementID = tableElementsInDB.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"elementID": elementID},
                type: "POST",
                success: function (data) {
                    $.ajax({
                        url: "getStandardElementParameters.php",
                        data: {"elementID": elementID},
                        type: "POST",
                        success: function (data) {
                            $("#elementParametersInDB").html(data);
                            $.ajax({
                                url: "getElementPricesInDifferentProjects.php",
                                data: {"elementID": elementID},
                                type: "POST",
                                success: function (data) {
                                    $("#elementPricesInOtherProjects").html(data);
                                    $.ajax({
                                        url: "getDevicesToElement.php",
                                        data: {"elementID": elementID},
                                        type: "POST",
                                        success: function (data) {
                                            $("#devicesInDB").html(data);
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });

        });
    });

</script>
