<!-- 17.2.25: Reworked -->
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<head>
    <title>Get Rooms Without ELement </title></head>
<body>

<?php
$mysqli = utils_connect_sql();
$elementID = filter_input(INPUT_GET, 'elementID');

$sql = "SELECT tabelle_räume.idTABELLE_Räume,
       tabelle_räume.Raumnr,
       tabelle_räume.Raumbezeichnung,
       tabelle_räume.`Raumbereich Nutzer`,
       tabelle_räume.`MT-relevant`,
       tabelle_räume.Entfallen
FROM tabelle_räume
WHERE (((tabelle_räume.idTABELLE_Räume) Not In
        (SELECT tabelle_räume.idTABELLE_Räume
         FROM (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente
               ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
         WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) = " . $elementID . ") AND
                ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . "))))
    AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . "))
ORDER BY tabelle_räume.Raumnr;";

$result = $mysqli->query($sql);

echo " <table class='table table-responsive table-striped table-bordered table-sm table-hover border border-5 border-light' id='tableRoomsWithoutElement'>
	<thead><tr>
        <th>id</th>
	<th>Raumnummer</th>
	<th>Raumbezeichnung</th>
	<th>Raumbereich</th>
	<th>MT-relevant</th>
	<th>Entfallen</th>
	</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idTABELLE_Räume"] . "</td>";
    echo "<td>" . $row["Raumnr"] . "</td>";
    echo "<td>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
    echo "<td>" . $row["MT-relevant"] . "</td>";
    echo "<td>" . $row["Entfallen"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>

<!-- Modal zum Kopieren der Elemente -->
<div class='modal fade' id='addElementsToRoomModal' role='dialog'>
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Kommentar hinzufügen, Stückzahl angeben</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <label for='amount' class='form-label'>Stück: </label>
                <input class='form-control form-control-sm' type='number' id='amount' value='1' size='2'>
                <label for='amount' class='form-label'>Kommentar: </label>
                <label for='comment'></label><textarea class='form-control' id='comment' rows='2' cols="3"></textarea>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addElementToRooms' class='btn btn-success btn-sm' value='Hinzufügen'
                       data-bs-dismiss='modal'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Schließen</button>

            </div>
        </div>

    </div>
</div>

<script>
    var tableRoomsWithoutElement, roomIDs = [], elementID =<?php echo $elementID; ?> ;
    $(document).ready(function () {
        tableRoomsWithoutElement = $('#tableRoomsWithoutElement').DataTable({
            columnDefs: [
                {
                    targets: [0, 4, 5],
                    visible: false,
                    searchable: true
                }
            ],
            paging: true,
            pagingType: 'numbers',
            searching: true,
            info: true,
            order: [[1, "asc"]],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: ""
            },
            select: {
                style: "multi"
            },

            scrollX: false,
            scrollY: false,
            stateSave: false,
            autoWidth: false,
            fixedHeader: false,
            fixedColumns: false,
            deferRender: false,
            responsive: false,
            rowReorder: false,
            colReorder: false,
            buttons: false
        });

        $('#tableRoomsWithoutElement tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');
            if ($(this).hasClass('info')) {
                $(this).removeClass('info');
                for (let i = roomIDs.length - 1; i >= 0; i--) {
                    if (roomIDs[i] === tableRoomsWithoutElement.row($(this)).data()[0]) {
                        roomIDs.splice(i, 1);
                    }
                }
            } else {
                $(this).addClass('info');
                roomIDs.push(tableRoomsWithoutElement.row($(this)).data()[0]);
            }
        });

        $('#CardHeaderRäumeOhneElement select').remove();
        let select4 = $('<select class="me-2 ms-2 btn-outline-light"><option value="">MT-rel.</option><option value="0">0</option><option value="1" >1</option></select>');
        let select5 = $('<select class="me-2 ms-2 btn-outline-light" ><option value="">Entfallen</option><option value="0"  > 0 </option><option value="1">1</option></select>');
        $('#CardHeaderRäumeOhneElement').append(select4);
        $('#CardHeaderRäumeOhneElement').append(select5);
        select4.on('change', function () {
            let val = $(this).val();
            tableRoomsWithoutElement.column(4).search(val, true, false).draw();
        });
        select5.on('change', function () {
            let val = $(this).val();
            tableRoomsWithoutElement.column(5).search(val, true, false).draw();
        });
    });

    $("#addElementToRooms").click(function () {
        if (roomIDs.length === 0) {
            alert("Kein Raum ausgewählt!");
        } else {
            $.ajax({
                url: "addElementToMultipleRooms.php",
                type: "GET",
                data: {
                    "elementID": elementID,
                    "rooms": roomIDs,
                    "amount": $("#amount").val(),
                    "comment": $("#comment").val()
                },
                success: function (data) {
                    alert(data);
                    //$('#addElementsToRoomModal').modal('hide');
                    $.ajax({
                        url: "getRoomsWithElement.php",
                        data: {"elementID": elementID},
                        type: "GET",
                        success: function (data) {
                            $("#roomsWithElement").html(data);
                            $.ajax({
                                url: "getRoomsWithoutElement.php",
                                data: {"elementID": elementID},
                                type: "GET",
                                success: function (data) {
                                    $("#roomsWithoutElement").html(data);
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