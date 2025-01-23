<?php

include "_utils.php";
check_login();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>get Elements in DB</title></head>
<body>
<?php
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_elemente.Kurzbeschreibung
                        FROM tabelle_elemente
                        ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);

echo "<table class='table table-striped table-condensed' id='tableElementsInDB'  cellspacing='0' width='100%'>
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
    echo "<td>" . $row["idTABELLE_Elemente"] . "</td>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-success btn-xs' value='addElement' data-toggle='modal' data-target='#addRoomElementModal'><span class='glyphicon glyphicon-plus'></span></button></td>";
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["Bezeichnung"] . "</td>";
    echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-default btn-xs' value='changeElement' data-toggle='modal' data-target='#changeElementModal'><span class='glyphicon glyphicon-pencil'></span></button></td>";
    echo "</tr>";

}
echo "</tbody></table>";

$mysqli->close();
?>
<div class='modal fade' id='addRoomElementModal' role='dialog'>
    <div class='modal-dialog modal-sm'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Element in Raum stellen</h4>
            </div>
            <div class='modal-body' id='mbody'>Wollen Sie das Element <br>
                <div id="elID"></div>
                in den Raum stellen?
            </div>
            <div class='modal-footer'>
                <input type='button' id='addElementToRoom' class='btn btn-success btn-sm' value='Ja'
                       data-dismiss='modal'></input>
                <button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Nein</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal zum Ändern eines Elements -->
<div class='modal fade' id='changeElementModal' role='dialog'>
    <div class='modal-dialog modal-md'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Element ändern</h4>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="bezeichnung">Bezeichnung:</label>
                        <input type="text" class="form-control" id="bezeichnung" placeholder="Type"/>
                    </div>
                    <div class="form-group">
                        <label for="kurzbeschreibung">Kurzbeschreibung:</label>
                        <textarea class="form-control" rows="5" id="kurzbeschreibungModal"></textarea>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='saveElement' class='btn btn-warning btn-sm' value='Speichern'></input>
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

    </div>
</div>

<script>

    var dbAdmin = <?php echo $_SESSION["dbAdmin"] ?>
        // Tabellen formatieren
        $(document).ready(function () {
            if (dbAdmin === 1) {
                $('#tableElementsInDB').DataTable({
                    "paging": true,
                    "info": true,
                    "pagingType": "simple",
                    "lengthChange": false,
                    "pageLength": 10,
                    "columnDefs": [
                        {
                            "targets": [0],
                            "visible": false,
                            "searchable": false
                        },
                        {
                            "targets": [1],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "order": [[1, "asc"]],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
                });
            } else {
                $('#tableElementsInDB').DataTable({
                    "paging": false,
                    "columnDefs": [
                        {
                            "targets": [0],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "info": false,
                    "order": [[1, "asc"]],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    "scrollY": '20vh',
                    "scrollCollapse": true
                });
            }


            // CLICK TABELLE ELEMENTE IN DB
            var table1 = $('#tableElementsInDB').DataTable();

            $('#tableElementsInDB tbody').on('click', 'tr', function () {

                if ($(this).hasClass('info')) {
                    //$(this).removeClass('info');
                } else {
                    $("#deviceParametersInDB").hide();
                    $("#devicePrices").hide();
                    $("#deviceLieferanten").hide();
                    document.getElementById("bezeichnung").value = table1.row($(this)).data()[3];
                    document.getElementById("kurzbeschreibungModal").value = table1.row($(this)).data()[4];

                    table1.$('tr.info').removeClass('info');
                    $(this).addClass('info');
                    var elementID = table1.row($(this)).data()[0];
                    $.ajax({
                        url: "setSessionVariables.php",
                        data: {"elementID": elementID},
                        type: "GET",
                        success: function (data) {
                            $.ajax({
                                url: "getStandardElementParameters.php",
                                data: {"elementID": elementID},
                                type: "GET",
                                success: function (data) {
                                    $("#elementParametersInDB").html(data);
                                    $.ajax({
                                        url: "getElementPricesInDifferentProjects.php",
                                        data: {"elementID": elementID},
                                        type: "GET",
                                        success: function (data) {
                                            $("#elementPricesInOtherProjects").html(data);
                                            $.ajax({
                                                url: "getDevicesToElement.php",
                                                data: {"elementID": elementID},
                                                type: "GET",
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
                }
            });
        });

    //Element in Raum stellen= Dialog
    $("button[value='addElement']").click(function () {
        var elementID = this.id;

        if (elementID !== "") {
            $.ajax({
                url: "getElementToElementID.php",
                data: {"elementID": elementID},
                type: "GET",
                success: function (data) {
                    $("#elID").html(data);
                }
            });
        }
    });

    //Element in Raum stellen
    $("#addElementToRoom").click(function () {
        $.ajax({
            url: "addElementToRoom.php",
            type: "GET",
            success: function (data) {
                alert(data);
                $.ajax({
                    url: "getRoomElementsDetailed1.php",
                    type: "GET",
                    success: function (data) {
                        $("#roomElements").html(data);
                    }
                });

            }
        });
    });

    //Element speichern
    $("#saveElement").click(function () {
        var bezeichnung = $("#bezeichnung").val();
        var kurzbeschreibung = $("#kurzbeschreibungModal").val();
        if (bezeichnung !== "" && kurzbeschreibung !== "") {

            $.ajax({
                url: "saveElement.php",
                data: {"bezeichnung": bezeichnung, "kurzbeschreibung": kurzbeschreibung},
                type: "GET",
                success: function (data) {
                    $('#changeElementModal').modal('hide');
                    alert(data);

                    $.ajax({
                        url: "getElementsInDB.php",
                        type: "GET",
                        success: function (data) {
                            $("#elementsInDB").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });


</script>

</body>
</html>