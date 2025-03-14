<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<head>
    <title> Get Rooms with Element </title>
</head>
<body>
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT
    tabelle_räume_has_tabelle_elemente.id,
    tabelle_räume.idTABELLE_Räume,
    tabelle_räume.Raumnr,
    tabelle_räume.Raumnummer_Nutzer,
    tabelle_räume.Raumbezeichnung,
    tabelle_räume.`Raumbereich Nutzer`,
    tabelle_räume_has_tabelle_elemente.Anzahl,
    tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
    tabelle_räume_has_tabelle_elemente.Standort,
    tabelle_räume_has_tabelle_elemente.Verwendung,
    tabelle_räume_has_tabelle_elemente.Kurzbeschreibung,
    tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
    tabelle_räume.Geschoss,
    tabelle_elemente.ElementID,
    tabelle_elemente.Bezeichnung AS ElementName

FROM
    tabelle_räume
        INNER JOIN
    tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
        INNER JOIN
    tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
WHERE ( ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=" . filter_input(INPUT_GET, 'bestand') . ") 
AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=" . filter_input(INPUT_GET, 'variantenID') . ") 
AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=" . filter_input(INPUT_GET, 'elementID') . ") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
ORDER BY tabelle_räume.Raumnr;";

$result = $mysqli->query($sql);
echo "<table class='table table-striped table-bordered table-sm  table-hover border border-light border-5' id='tableRoomsWithElement'>
	<thead><tr>
        <th>ID</th>
	<th>Anzahl</th>
	<th>Variante</th>
	<th>Raum Nr.</th>
	<th>Raumbez.</th>
	<th>Raumbereich</th>
    <th>Geschoss</th>
	<th>Bestand</th>
	<th>Standort</th>
	<th>Verwendung</th>
	<th></th>
        <th></th>
        <th>MT-Variante</th>
        <th>MT-Standort</th>
        <th>MT-Verwendung</th>
        <th>MT-Bestand</th>
        <th>MT-Anzahl</th>
        <th>MT-Kommentar</th>
         <th>Element-ID</th>
          <th>Element-Bezeichnung</th>
	</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {

    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td><input class='form-control form-control-sm' type='text' id='amount" . $row["id"] . "' value='" . intval($row["Anzahl"]) . "' size='2'></input></td>";
    echo "<td>
   	    	<select class='form-control form-control-sm'' id='variante" . $row["id"] . "'>";

    $options = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
    $selected = $row["tabelle_Varianten_idtabelle_Varianten"];

    foreach ($options as $index => $option) {
        $value = $index + 1;
        $isSelected = ($value == $selected) ? ' selected' : '';
        echo "<option value='$value'$isSelected>$option</option>";
    }

    echo "</select></td>";
    if ($_SESSION["projectName"] === "GCP") {
        echo "<td>" . $row["Raumnummer_Nutzer"] . "</td>";
    } else {
        echo "<td>" . $row["Raumnr"] . "</td>";
    }

    echo "<td>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
    echo "<td>" . $row["Geschoss"] . "</td>";
    echo "<td>
	    	<select class='form-control form-control-sm'' id='bestand" . $row["id"] . "'>";
    if ($row["Neu/Bestand"] == "0") {
        echo "<option value=0 selected>Ja</option>";
        echo "<option value=1>Nein</option>";
    } else {
        echo "<option value=0>Ja</option>";
        echo "<option value=1 selected>Nein</option>";
    }
    echo "</select></td>";
    echo "<td><select class='form-control form-control-sm'' id='Standort" . $row["id"] . "'>";
    if ($row["Standort"] == "0") {
        echo "<option value=0 selected>Nein</option>";
        echo "<option value=1>Ja</option>";
    } else {
        echo "<option value=0>Nein</option>";
        echo "<option value=1 selected>Ja</option>";
    }
    echo "</select></td>";
    echo "<td>   	    	
                        <select class='form-control form-control-sm'' id='Verwendung" . $row["id"] . "'>";
    if ($row["Verwendung"] == "0") {
        echo "<option value=0 selected>Nein</option>";
        echo "<option value=1>Ja</option>";
    } else {
        echo "<option value=0>Nein</option>";
        echo "<option value=1 selected>Ja</option>";
    }
    echo "</select></td>";


    $Kurzbeschreibung = trim($row["Kurzbeschreibung"] ?? "");
    $buttonClass = $Kurzbeschreibung === "" ? "btn-outline-secondary" : "btn-outline-dark";
    $iconClass = $Kurzbeschreibung === "" ? "fa fa-comment-slash" : "fa fa-comment";
    $dataAttr = $Kurzbeschreibung === "" ? "data-description=''" : "data-description='" . htmlspecialchars($Kurzbeschreibung, ENT_QUOTES, 'UTF-8') . "'";
    echo " 
    <td>
        <button type='button'
                class='btn btn-sm $buttonClass comment-btn' $dataAttr
                id='" . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . "' title='Kommentar'>
            <i class='$iconClass'></i>
        </button>
    </td>";


    echo "<td><button type='button' id='" . $row["id"] . "' class='btn btn-warning btn-sm' value='saveElement'><i class='far fa-save'></i></button></td>";
    echo "<td>" . $row["tabelle_Varianten_idtabelle_Varianten"] . "</td>";
    echo "<td>" . $row["Standort"] . "</td>";
    echo "<td>" . $row["Verwendung"] . "</td>";
    echo "<td>" . $row["Neu/Bestand"] . "</td>";
    echo "<td>" . $row["Anzahl"] . "</td>";
    echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["ElementName"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>

<div class='modal fade' id='myModal' role='dialog'>
    <div class='modal-dialog'>

        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Kommentar</h4>
            </div>
            <div class='modal-body' id='mbody'>
                <div class='modal-body-inner'></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-bs-dismiss='modal'>Close</button>
            </div>
        </div>
        Fsave
    </div>
</div>

<script src="_utils.js"></script>
<script charset="utf-8" type="module">
    import CustomPopover from './_popover.js';

    var tableRoomsWithElement;

    $(document).ready(function () {
        tableRoomsWithElement = new DataTable('#tableRoomsWithElement', {
            columnDefs: [
                {
                    targets: [0, 12, 13, 14, 15, 16, 17, 18, 19],
                    visible: false,
                    searchable: false
                }
            ],
            paging: false,
            searching: true,
            info: false,
            order: [[1, 'asc']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.0/i18n/de-DE.json',
                search: ""
            },
            layout: {
                topStart: null,
                topEnd: ['buttons', 'search'],
                bottomStart: [],
                bottomEnd: []
            },
            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [3, 4, 5, 12, 13, 14, 15, 16, 17, 18, 19]
                    },
                    text: '<i class="fas fa-file-excel"></i> Excel', // Add Font Awesome icon
                    className: 'btn btn-sm btn-light btn-outline-success', // Bootstrap small
                }
            ]
        });

        $('#tableRoomsWithElement tbody').on('click', 'tr', function () {
            tableRoomsWithElement.$('tr.info').removeClass('info');
            $(this).addClass('info');
        });


        CustomPopover.init('.comment-btn', {
            onSave: function (trigger, newText) {
                trigger.dataset.description = newText;
                let row = tableRoomsWithElement.row($(trigger).closest('tr'));
                let data = row.data();
                data[17] = newText; // Update column 17 (0-indexed)
                row.data(data).draw(false); // Update the row data without redrawing the table
                // send an AJAX request to save the new text
                let id = trigger.id;   // = tabelle_räume_has_tabelle_elemente.id
                $.ajax({
                    url: "saveRoomElementComment.php",
                    data: {
                        "comment": newText,
                        "id": id
                    },
                    type: "GET",
                    success: function (data) {
                        makeToaster(data.trim(), true);
                        $(".comment-btn[id='" + id + "']").removeClass('btn-outline-secondary');
                        $(".comment-btn[id='" + id + "']").addClass('btn-outline-dark');
                        $(".comment-btn[id='" + id + "']").find('i').removeClass('fa fa-comment-slash');
                        $(".comment-btn[id='" + id + "']").find('i').addClass('fa fa-comment');
                        $(".comment-btn[id='" + id + "']").attr('data-description', newText).data('description', newText);
                    }
                });
            }
        });

    });

    $("button[value='saveElement']").click(function () {
        let id = this.id;
        let comment = $(".comment-btn[id='" + id + "']").attr('data-description');
        // console.log(comment);
        let amount = $("#amount" + id).val();
        let variantenID = $("#variante" + id).val();
        let bestand = $("#bestand" + id).val();
        let standort = $("#Standort" + id).val();
        let verwendung = $("#Verwendung" + id).val();
        if (standort === '0' && verwendung === '0') {
            alert("Standort und Verwendung kann nicht Nein sein!");
        } else {
            $.ajax({
                url: "saveRoombookEntry.php",
                data: {
                    "comment": comment,
                    "id": id,
                    "amount": amount,
                    "variantenID": variantenID,
                    "bestand": bestand,
                    "standort": standort,
                    "verwendung": verwendung
                },
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