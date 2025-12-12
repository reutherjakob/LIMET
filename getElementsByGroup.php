<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$gruppeID = getPostInt("gruppeID",0);
$stmt = $mysqli->prepare(
    "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_elemente.Kurzbeschreibung
     FROM tabelle_elemente
     WHERE tabelle_element_gruppe_idTABELLE_Element_Gruppe = ?
     ORDER BY tabelle_elemente.ElementID;"
);
$stmt->bind_param("i", $gruppeID);
$stmt->execute();
$result_el = $stmt->get_result();


$showAddButton = false;
if (isset($_SESSION['roomID'])) {
    $roomID = (int)$_SESSION['roomID'];
    $stmt = $mysqli->prepare(
        "SELECT Raumnr, Raumbezeichnung, `Raumbereich Nutzer`, Geschoss
         FROM tabelle_räume
         WHERE idTABELLE_Räume = ?;"
    );
    $stmt->bind_param("i", $roomID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $room = $result->fetch_assoc();
        $showAddButton = true;
    }
    $stmt->close();
}

echo "<table class='table table-striped table-sm table-bordered border border-light border-5' id='tableElementsInDB'   >
	<thead><tr>
	<th>ID</th>
	<th></th>  
   <th>ElementID</th>
	<th>Element</th>
	<th>Beschreibung</th>
    <th></th>
	</tr></thead><tbody>";

while ($row = $result_el->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idTABELLE_Elemente"] . "</td>";
    if ($showAddButton) {
        echo "<td><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-outline-success btn-sm' value='addElement' data-bs-toggle='modal' data-bs-target='#addRoomElementModal'><i class='fa fa-plus'></i></button></td>";
    } else {
        echo "<td> </td>";
    }
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-outline-dark btn-sm' value='changeElement' data-bs-toggle='modal' data-bs-target='#changeElementModal'><i class='fas fa-pencil-alt'></i></button></td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
include "addRoomElementModal.html";
?>


<!-- Modal Info -->
<div class='modal fade' id='infoModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Info</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='infoBody'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>OK</button>
            </div>
        </div>
    </div>
</div>

<script src="addElementToRoom.js"></script>
<script charset="utf-8">
    $(document).ready(function () {
        $("#CardHeaderElementesInDb .xxx").remove();
        new DataTable('#tableElementsInDB', {
            paging: true,
            select: true,
            columnDefs: [
                {
                    targets: [0, 5],
                    visible: false,
                    searchable: false,
                    orderable: false
                }
            ],
            info: false,
            pagingType: "full",
            pageLength: 10,
            order: [[2, "asc"]],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                topStart: "search",
                topEnd: null,
                bottomStart: "pageLength",
                bottomEnd: 'paging'
            },
            initComplete: function () {
                $("#CardHeaderElementesInDb .xxx").remove();
                $('#elementsInDB .dt-search label').remove();
                $('#elementsInDB .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#CardHeaderElementesInDb');
            }
        });


        let tableElementsInDB = $('#tableElementsInDB').DataTable();

        $('#tableElementsInDB tbody').on('click', 'tr', function () {// TODO
            if ($(this).hasClass('info')) {
            } else {
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
                $.ajax({
                    url: "getRoomsWithElement1.php",
                    data: {"elementID": elementID},
                    type: "POST",
                    success: function (data) {
                        $("#roomsWithElement").html(data);
                        $.ajax({
                            url: "getRoomsWithoutElement.php",
                            data: {"elementID": elementID},
                            type: "POST",
                            success: function (data) {
                                $("#roomsWithoutElement").html(data);
                            }
                        });
                    }
                });
            }
        });
    });

</script>