<!-- 17.2.25: Reworked -->
<?php
require_once 'utils/_utils.php';
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
<div class='modal fade' id='addElementsToRoomModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-lg'>

        <div class='modal-content'>
            <div class="modal-header flex-column align-items-start">
                <div class="w-100 d-flex justify-content-between align-items-start">
                    <h5 class="modal-title" id="ElementBzeichnung"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <small class="text-muted mt-1">zu Räumen hinzufügen - Kommentar & Stückzahl angeben</small>
            </div>


            <div class='modal-body' id='mbody'>
                <div class='row'>

                    <div class='col-6'>
                        <div class="d-flex align-items-center mb-1">
                            <label for="amount" class="form-label me-1 mb-0">Stück:</label>
                            <input class="form-control form-control-sm" type="number" id="amount" value="1">
                        </div>

                        <label for='comment' class='form-label'></label>
                        <textarea class='form-control' id='comment' rows='3' placeholder="Kommentar hinzufügen ..."
                                  cols="3"></textarea>
                    </div>
                    <div class='col-6'>
                        <p id="Raumnamen"></p>
                    </div>
                </div>
            </div>
            <div class='modal-footer row'>
                <div class="d-flex justify-content-center align-items-center">
                    <div class="col-1"></div>
                    <button type="button" id="addElementToRooms"
                            class="btn btn-success btn-sm col-5 me-1 ms-1"
                            data-bs-dismiss="modal">Hinzufügen
                    </button>
                    <button type="button"
                            class="btn btn-secondary btn-sm col-5 me-1 ms-1"
                            data-bs-dismiss="modal">Schließen
                    </button>
                    <div class="col-1"></div>
                </div>

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
            pageLength:25,
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

            let row = tableRoomsWithoutElement.row(this);
            let data = row.data();
            let id = data[0];
            $(this).toggleClass('selected');
            if ($(this).hasClass('selected')) {
                if (!roomIDs.includes(id)) roomIDs.push(id);
            } else {
                roomIDs = roomIDs.filter(rid => rid !== id);
            }
            updateSelectedRoomsDisplay();
        });


        $('#addElementsToRoomModal').on('hidden.bs.modal', function () {
            $('#Raumnamen').html();
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

    function updateSelectedRoomsDisplay() {
        $('#addElements').prop('disabled', false);
        $('#ElementBzeichnung').text(elementBezeichnung);

        let selectedRooms = [];
        tableRoomsWithoutElement.rows().every(function () {
            let data = this.data();
            if (roomIDs.includes(data[0])) {
                selectedRooms.push({ raumnr: data[1], bezeichnung: data[2] });
            }
        });

        let html = "<table class='table table-sm table-bordered mb-0'><thead><tr><th>Raum</th></tr></thead><tbody>";
        selectedRooms.forEach(function (room) {
            html += "<tr><td>"+ room.raumnr + "  " + room.bezeichnung + "</td></tr>";
        });
        html += "</tbody></table>";
        $('#Raumnamen').html(html);
    }

    $("#addElementToRooms").click(function () {
        if (roomIDs.length === 0) {
            alert("Kein Raum ausgewählt!");
        } else {
            $.ajax({
                url: "addElementToMultipleRooms.php",
                type: "POST",
                data: {
                    "elementID": elementID,
                    "rooms": roomIDs,
                    "amount": $("#amount").val(),
                    "comment": $("#comment").val()
                },
                success: function (data) {
                    makeToaster(data, true);
                    //$('#addElementsToRoomModal').modal('hide');
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
        }
    });

</script>
</body>
</html>