
<!DOCTYPE html >
<html lang="de">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>getRoomBos</title>
</head>
<body>
<?php

$mysqli =utils_connect_sql();

$sql = "SELECT tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten, tabelle_BO_Taetigkeiten.Taetigkeit_Deutsch, tabelle_BO_Taetigkeiten.Taetigkeit_Englisch, tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe, tabelle_BO_Untergruppe.Bezeichnung As UGBezeichnung, tabelle_BO_Untergruppe.Nummer, tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe, tabelle_BO_Hauptgruppe.Bezeichnung As HGBezeichnung, tabelle_BO_Hauptgruppe.Nummmer
			FROM tabelle_BO_Hauptgruppe INNER JOIN (tabelle_BO_Untergruppe INNER JOIN (tabelle_BO_Taetigkeiten INNER JOIN tabelle_BO_Taetigkeiten_has_tabelle_räume ON tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten = tabelle_BO_Taetigkeiten_has_tabelle_räume.tabelle_BO_Taetigkeiten_idtabelle_BO_Taetigkeiten) ON tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe = tabelle_BO_Taetigkeiten.tabelle_BO_Untergruppe_idtabelle_BO_Untergruppe) ON tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe = tabelle_BO_Untergruppe.tabelle_BO_Hauptgruppe_idtabelle_BO_Hauptgruppe
			WHERE (((tabelle_BO_Taetigkeiten_has_tabelle_räume.tabelle_räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . ")) ORDER BY tabelle_BO_Hauptgruppe.Nummmer, tabelle_BO_Untergruppe.Nummer;";

$result = $mysqli->query($sql);
$row_cnt = $result->num_rows;
if ($row_cnt > 0) {
    //Button für Modal
    echo "<input type='button' class='btn btn-info btn-sm' value='Raumdaten kopieren' id='" . $_SESSION["roomID"] . "' data-bs-toggle='modal' data-bs-target='#myModal'></input>";

}
echo "<table class='table table-striped table-condensed' id='tableRoomBOs'  >
	<thead><tr>
	<th></th>
	<th>HG/UG</th>
	<th>Tätigkeit Deutsch</th>
	<th>Tätigkeit Englisch</th>
	<th></th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["idtabelle_BO_Taetigkeiten"] . "' class='btn btn-danger btn-sm' value='deleteBO'><span class='glyphicon glyphicon-minus'></span></button></td>";
    echo "<td>" . $row["Nummer"] . " - " . $row["HGBezeichnung"] . "." . $row["UGBezeichnung"] . "</td>";
    echo "<td><input type='text' id='taetigkeit_deutsch" . $row["idtabelle_BO_Taetigkeiten"] . "' value='" . $row["Taetigkeit_Deutsch"] . "' size='50'></input></td>";
    echo "<td><input type='text' id='taetigkeit_englisch" . $row["idtabelle_BO_Taetigkeiten"] . "' value='" . $row["Taetigkeit_Englisch"] . "' size='50'></input></td>";
    echo "<td><button type='button' id='" . $row["idtabelle_BO_Taetigkeiten"] . "' class='btn btn-default btn-sm' value='saveBO'><span class='glyphicon glyphicon-floppy-disk'></span></button></td>";
    echo "</tr>";
}

echo "</tbody></table>
	
	<!-- Modal zum kopieren der BO Angaben -->
	<div class='modal fade' id='myModal' role='dialog' tabindex='-1'>
	    <div class='modal-dialog modal-lg'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
	          <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
	          <h4 class='modal-title'>BO-Angaben kopieren</h4>
	        </div>
	        <div class='modal-body' id='mbody'>
	        </div>
	        <div class='modal-footer'>
	        	<input type='button' id='copyBO' class='btn btn-info btn-sm' value='BO-Angaben kopieren'></input>
	          	<button type='button' class='btn btn-default' data-bs-dismiss='modal'>Close</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>";


$mysqli->close();
?>

<script>

    $(document).ready(function () {
        $("#tableRoomBOs").DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[1, "asc"]],
            "columnDefs": [
                {
                    "targets": [3],
                    "visible": false,
                    "searchable": false
                }
            ],
            //"pagingType": "simple_numbers",
            //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
            "scrollY": '20vh',
            "scrollCollapse": true
        });
        var table = $('#tableRoomBOs').DataTable();

        $('#tableRoomBOs tbody').on('click', 'tr', function () {
            if ($(this).hasClass('info')) {

            } else {
                table.$('tr.info').removeClass('info');
                $(this).addClass('info');
            }
        });

        //Rauminhalt kopieren für getRoomsToCopy.php
        $("#copyRoomElements").click(function () {
            roomIDs = [...new Set(roomIDs)];
            if (roomIDs.length === 0) {
                alert("Kein Raum ausgewählt!");
            } else {
                $.ajax({
                    url: "copyRoomElements.php",
                    type: "POST",
                    data: {"rooms": roomIDs},
                    success: function (data) {
                        makeToaster(data, true);
                        $("#mbodyCRE").modal('hide');
                    }
                });
            }
        });
    });


    $("button[value='deleteBO']").click(function () {
        var id = this.id;
        if (id !== "") {
            $.ajax({
                url: "deleteRoomBO.php",
                data: {"boID": id},
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getRoomBOs.php",
                        type: "POST",
                        success: function (data) {
                            $("#roomBOs").html(data);
                            $.ajax({
                                url: "getPossibleRoomBOs.php",
                                type: "POST",
                                success: function (data) {
                                    $("#possibleRoomBOs").html(data);
                                }
                            });

                        }
                    });

                }
            });
        }

    });

    $("button[value='saveBO']").click(function () {
        var id = this.id;
        var bo_deutsch = $("#taetigkeit_deutsch" + id).val();
        var bo_englisch = $("#taetigkeit_englisch" + id).val();

        if (id !== "") {
            $.ajax({
                url: "updateBO.php",
                data: {"boID": id, "bo_deutsch": bo_deutsch, "bo_englisch": bo_englisch},
                type: "POST",
                success: function (data) {
                    alert(data);
                }
            });
        }
    });

    //Bauangaben kopieren
    $("input[value='Raumdaten kopieren']").click(function () {
        var ID = this.id;
        $.ajax({
            url: "getRoomsToCopy.php",
            type: "POST",
            data: {"id": ID},
            success: function (data) {
                $("#mbody").html(data);

            }
        });

    });


</script>

</body>
</html>