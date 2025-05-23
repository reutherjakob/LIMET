<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<head>
    <title> Get Rooms with Element </title>
</head>
<body>

<div class="btn-group" id="hide0Wrapper">
    <input class="btn-check" type="checkbox" id="hideZeroRows">
    <label class="btn btn-outline-secondary" for="hideZeroRows">
        Hide 0
    </label>
</div>


<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

$_SESSION["variantenID"] = filter_input(INPUT_GET, 'variantenID');

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
    tabelle_räume.Bauetappe,
    tabelle_räume.Bauabschnitt,
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
	
	<th>Raum Nr.</th>
	<th>Raumbez.</th>
	<th>Raumbereich</th>
    <th>Geschoss</th>
        <th>Bauetappe</th>
            <th>Bauabschnitt</th>
    <th>Anzahl</th>
	<th>Variante</th>
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
    echo "<td data-order='" . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . "'>" . $row["id"] . "</td>";
    if ($_SESSION["projectName"] === "GCP") {
        echo "<td data-order='" . htmlspecialchars($row["Raumnummer_Nutzer"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Raumnummer_Nutzer"] . "</td>";
    } else {
        echo "<td data-order='" . htmlspecialchars($row["Raumnr"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Raumnr"] . "</td>";
    }
    echo "<td data-order='" . htmlspecialchars($row["Raumbezeichnung"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["Raumbereich Nutzer"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Raumbereich Nutzer"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["Geschoss"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Geschoss"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["Bauetappe"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Bauetappe"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["Bauabschnitt"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Bauabschnitt"] . "</td>";
    echo "<td data-order='" . intval($row["Anzahl"]) . "'><input class='form-control form-control-sm' type='text' id='amount" . $row["id"] . "' value='" . intval($row["Anzahl"]) . "' size='2'></input></td>";
    echo "<td data-order='" . htmlspecialchars($row["tabelle_Varianten_idtabelle_Varianten"] ?? "", ENT_QUOTES, 'UTF-8') . "'>
   	    	<select class='form-control form-control-sm'' id='variante" . $row["id"] . "'>";

    $options = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
    $selected = $row["tabelle_Varianten_idtabelle_Varianten"];

    foreach ($options as $index => $option) {
        $value = $index + 1;
        $isSelected = ($value == $selected) ? ' selected' : '';
        echo "<option value='$value'$isSelected>$option</option>";
    }

    echo "</select></td>";
    echo "<td data-order='" . htmlspecialchars($row["Neu/Bestand"] ?? "", ENT_QUOTES, 'UTF-8') . "'>
	    	<select class='form-control form-control-sm'' id='bestand" . $row["id"] . "'>";
    if ($row["Neu/Bestand"] == "0") {
        echo "<option value=0 selected>Ja</option>";
        echo "<option value=1>Nein</option>";
    } else {
        echo "<option value=0>Ja</option>";
        echo "<option value=1 selected>Nein</option>";
    }
    echo "</select></td>";
    echo "<td data-order='" . htmlspecialchars($row["Standort"] ?? "", ENT_QUOTES, 'UTF-8') . "'><select class='form-control form-control-sm'' id='Standort" . $row["id"] . "'>";
    if ($row["Standort"] == "0") {
        echo "<option value=0 selected>Nein</option>";
        echo "<option value=1>Ja</option>";
    } else {
        echo "<option value=0>Nein</option>";
        echo "<option value=1 selected>Ja</option>";
    }
    echo "</select></td>";
    echo "<td data-order='" . htmlspecialchars($row["Verwendung"] ?? "", ENT_QUOTES, 'UTF-8') . "'>   	    	
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
    <td data-order='" . htmlspecialchars($Kurzbeschreibung ?? "", ENT_QUOTES, 'UTF-8') . "'>
        <button type='button'
                class='btn btn-sm $buttonClass comment-btn' $dataAttr
                id='" . htmlspecialchars($row["id"] ?? "", ENT_QUOTES, 'UTF-8') . "' title='Kommentar'>
            <i class='$iconClass'></i>
        </button>
    </td>";


    echo "<td data-order=''><button type='button' id='" . $row["id"] . "' class='btn btn-warning btn-sm' value='saveElement'><i class='far fa-save'></i></button></td>";
    echo "<td data-order='" . htmlspecialchars($row["tabelle_Varianten_idtabelle_Varianten"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["tabelle_Varianten_idtabelle_Varianten"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["Standort"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Standort"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["Verwendung"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Verwendung"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["Neu/Bestand"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Neu/Bestand"] . "</td>";
    echo "<td data-order='" . intval($row["Anzahl"]) . "'>" . $row["Anzahl"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["Kurzbeschreibung"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["Kurzbeschreibung"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["ElementID"] ?? "", ENT_QUOTES, 'UTF-8') . "'>" . $row["ElementID"] . "</td>";
    echo "<td data-order='" . htmlspecialchars($row["ElementName"] ?? '', ENT_QUOTES, 'UTF-8') . "'>" . $row["ElementName"] . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";
$mysqli->close();
?>


<script src="_utils.js"></script>
<script charset="utf-8" type="module">
    import CustomPopover from './_popover.js';

    $(document).ready(function () {


        tableRoomsWithElement = new DataTable('#tableRoomsWithElement', {
            columnDefs: [
                {
                    targets: [0, 14, 15, 16, 17, 18, 19, 20, 21],
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
                        columns: [1, 2, 3, 4, 5, 6, 14, 15, 16, 17, 18, 19, 20, 21]// 12,
                    },
                    text: '<i class="fas fa-file-excel me-2"></i> Excel', // Add Font Awesome icon
                    className: 'btn btn-sm btn-outline-success bg-white', // Bootstrap small
                }
            ],
            initComplete: function () {
                $('#roomsWithAndWithoutElements .dt-buttons').attr("id", "DtBtnGroup");
                $('#hide0Wrapper').appendTo('#DtBtnGroup');
            }
        });

        let filterIndex = $.fn.dataTable.ext.search.indexOf(hideZeroFilter);
        if (filterIndex !== -1) {
            $.fn.dataTable.ext.search.splice(filterIndex, 1);
        }
        $.fn.dataTable.ext.search.push(hideZeroFilter);

        $("#hideZeroRows").on("change", function () {
            tableRoomsWithElement.draw();
        });

        CustomPopover.init('.comment-btn', {
            onSave: function (trigger, newText) {
                console.log("Custompopover: ", newText);
                trigger.dataset.description = newText;
                let id = trigger.id;
                $.ajax({
                    url: "saveRoomElementComment.php",
                    data: {
                        "comment": newText,
                        "id": id
                    },
                    type: "GET",
                    success: function (data) {
                        makeToaster(data.trim(), true);
                        $(".comment-btn[id='" + id + "']").attr('data-description', newText).data('description', newText);
                        if (newText !== "") {
                            $(".comment-btn[id='" + id + "']").removeClass('btn-outline-secondary');
                            $(".comment-btn[id='" + id + "']").addClass('btn-outline-dark');
                            $(".comment-btn[id='" + id + "']").find('i').removeClass('fa fa-comment-slash');
                            $(".comment-btn[id='" + id + "']").find('i').addClass('fa fa-comment');
                        } else {
                            $(".comment-btn[id='" + id + "']").removeClass('btn-outline-dark');
                            $(".comment-btn[id='" + id + "']").addClass('btn-outline-secondary');
                            $(".comment-btn[id='" + id + "']").find('i').removeClass('fa fa-comment');
                            $(".comment-btn[id='" + id + "']").find('i').addClass('fa fa-comment-slash');
                        }
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

    function hideZeroFilter(settings, data, dataIndex) {
        if (settings.nTable.id !== 'tableRoomsWithElement') {
            return true;
        }
        let hideZero = $("#hideZeroRows").is(":checked");
        let row = tableRoomsWithElement.row(dataIndex).node();
        let amount = $(row).find('input[id^="amount"]').val();
        let name = $(row).find('span[id^="ElementName"').val();
        amount = parseInt(amount) || 0;
        return !(hideZero && (amount === 0));
    }
</script>
</body>
</html>