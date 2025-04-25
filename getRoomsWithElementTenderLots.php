<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<head>
    <title></title></head>
<body>


<?php
// --> REWORKED 25 <--
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

$mysqli = utils_connect_sql();
$sql = "SELECT `tabelle_lose_extern`.`idtabelle_Lose_Extern`,
			    `tabelle_lose_extern`.`LosNr_Extern`,
			    `tabelle_lose_extern`.`LosBezeichnung_Extern`,
			    `tabelle_lose_extern`.`Ausführungsbeginn`
			FROM `LIMET_RB`.`tabelle_lose_extern`
			WHERE `tabelle_lose_extern`.`tabelle_projekte_idTABELLE_Projekte`=" . $_SESSION["projectID"] . "
			ORDER BY `tabelle_lose_extern`.`LosNr_Extern`;";

$result = $mysqli->query($sql);

$lotsInProject = array();
while ($row = $result->fetch_assoc()) {
    $lotsInProject[$row['idtabelle_Lose_Extern']]['LosNr_Extern'] = $row['LosNr_Extern'];
    $lotsInProject[$row['idtabelle_Lose_Extern']]['idtabelle_Lose_Extern'] = $row['idtabelle_Lose_Extern'];
    $lotsInProject[$row['idtabelle_Lose_Extern']]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];
} ?>


<div class="row" id="batchSelectWrapper">
    <div class="form-group form-check-inline d-flex align-items-center border border-light rounded bg-light">
        <div class="col-xxl-6 d-flex justify-content-start" id="Whatever">

        </div>
        <div class="col-xxl-6 d-flex align-items-center justify-content-end">

            <select class="form-control form-control-sm mr-2 me-2" id="globalLosExtern" style="width: auto;">
                <option value="0" selected> Wähle ein Los für den Elemente Batch</option>
                <?php
                foreach ($lotsInProject as $array) {
                    echo "<option value=" . $array['idtabelle_Lose_Extern'] . ">" . $array['LosNr_Extern'] . " - " . $array['LosBezeichnung_Extern'] . "</option>";
                } ?>
            </select>
            <label for="globalLosExtern" class="me-2"></label>
            <button id="saveSelected" class="btn btn-warning  k btn-sm mr-2 me-2"><i class='far fa-save'></i> Batch
                speichern
            </button>
            <button class="btn btn-sm btn-danger" id="RemoveAllElementzFromBatch"><i class="fa fa-times"></i>
                aus Batch entfernen
            </button>
        </div>
    </div>
</div>


<?php

$raumbereich = urldecode($_GET["raumbereich"]);
$bauabschnitt = urldecode($_GET["bauabschnitt"]);
$losID = $_GET["losID"] ?? null;
$bestand = $_GET["bestand"] ?? null;
$variantenID = $_GET["variantenID"] ?? null;
$elementID = $_GET["elementID"] ?? null;
$projectID = $_SESSION["projectID"] ?? null;

// Basis-SQL
$sql = "SELECT 
    tabelle_räume_has_tabelle_elemente.id, 
    tabelle_räume.idTABELLE_Räume, 
    tabelle_räume.Raumnr, 
    tabelle_räume.Raumbezeichnung, 
    tabelle_räume.`Raumbereich Nutzer`, 
    tabelle_räume_has_tabelle_elemente.Anzahl, 
    tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
    tabelle_räume_has_tabelle_elemente.Standort, 
    tabelle_räume_has_tabelle_elemente.Verwendung, 
    tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
    tabelle_lose_extern.LosNr_Extern, 
    tabelle_varianten.Variante, 
    tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
FROM tabelle_varianten
INNER JOIN (
            tabelle_lose_extern
            RIGHT JOIN (
                tabelle_räume
                INNER JOIN tabelle_räume_has_tabelle_elemente
                ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            )
            ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
)
ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
WHERE ";

// Bedingungen dynamisch aufbauen
$conditions = [];
$params = [];
$types = "";

// LosID
if (!empty($losID)) {
    $conditions[] = "tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern = ?";
    $params[] = $losID;
    $types .= "i";
} else {
    $conditions[] = "tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern IS NULL";
}

// Raumbereich
if (!empty($raumbereich)) {
    $conditions[] = "tabelle_räume.`Raumbereich Nutzer` = ?";
    $params[] = $raumbereich;
    $types .= "s";
}

// Bestand
$conditions[] = "tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = ?";
$params[] = $bestand;
$types .= "i";

// VariantenID
$conditions[] = "tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = ?";
$params[] = $variantenID;
$types .= "i";

// ElementID
$conditions[] = "tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = ?";
$params[] = $elementID;
$types .= "i";

// ProjectID
$conditions[] = "tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?";
$params[] = $projectID;
$types .= "i";

// Bauabschnitt (LIKE)
$conditions[] = "tabelle_räume.Bauabschnitt LIKE ?";
$params[] = $bauabschnitt;
$types .= "s";

$sql .= implode(" AND ", $conditions) . " ORDER BY tabelle_räume.Raumnr";


$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

// Parameter binden, falls vorhanden
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

echo "<table class='table table-striped table-bordered border border-light border-5 table-sm' id='tableRoomsWithElementTenderLots' >
	<thead><tr>
	<th>ID</th>
        <th></th>
	<th>Anzahl</th>
	<th>Raumnummer</th>
	<th>Raumbezeichnung</th>
	<th>Raumbereich</th>
	<th>Best</th>
	<th>Stand</th>
	<th>Verw</th>
	<th>Komm</th>
	<th>LosNr</th>
	<th></th>
		<th>Batch</th>
	</tr></thead><tbody>";


while ($row = $result->fetch_assoc()) {

    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td></td>";
    echo "<td><input type='text' id='amount" . $row["id"] . "' value='" . $row["Anzahl"] . "' size='4'></td>";
    echo "<td>" . $row["Raumnr"] . "</td>";
    echo "<td>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
    echo "<td>
	    	<select class='form-control form-control-sm' id='bestand" . $row["id"] . "'>";
    if ($row["Neu/Bestand"] == "0") {
        echo "<option value=0 selected>Ja</option>";
        echo "<option value=1>Nein</option>";
    } else {
        echo "<option value=0>Ja</option>";
        echo "<option value=1 selected>Nein</option>";
    }
    echo "</select></td>";
    echo "<td>   	
                <select class='form-control form-control-sm' id='Standort" . $row["id"] . "'>";
    if ($row["Standort"] == "0") {
        echo "<option value=0 selected>Nein</option>";
        echo "<option value=1>Ja</option>";
    } else {
        echo "<option value=0>Nein</option>";
        echo "<option value=1 selected>Ja</option>";
    }
    echo "</select></td>";
    echo "<td>   	    	
                        <select class='form-control form-control-sm' id='Verwendung" . $row["id"] . "'>";
    if ($row["Verwendung"] == "0") {
        echo "<option value=0 selected>Nein</option>";
        echo "<option value=1>Ja</option>";
    } else {
        echo "<option value=0>Nein</option>";
        echo "<option value=1 selected>Ja</option>";
    }
    echo "</select></td>";
    echo "<td><textarea id='comment" . $row["id"] . "' rows='1' style='width: 100%;'>" . $row["Kurzbeschreibung"] . "</textarea></td>";
    echo "<td>
	    	<select class='form-control form-control-sm' id='losExtern" . $row["id"] . "'>";
    if ($row["tabelle_Lose_Extern_idtabelle_Lose_Extern"] != "") {
        echo "<option value=0>Los wählen</option>";
        foreach ($lotsInProject as $array) {
            if ($array['idtabelle_Lose_Extern'] == $row["tabelle_Lose_Extern_idtabelle_Lose_Extern"]) {
                echo "<option selected value=" . $array['idtabelle_Lose_Extern'] . ">" . $array['LosNr_Extern'] . " - " . $array['LosBezeichnung_Extern'] . "</option>";
            } else {
                echo "<option value=" . $array['idtabelle_Lose_Extern'] . ">" . $array['LosNr_Extern'] . " - " . $array['LosBezeichnung_Extern'] . "</option>";
            }
        }
    } else {
        echo "<option value=0 selected>Los wählen</option>";
        foreach ($lotsInProject as $array) {
            echo "<option value=" . $array['idtabelle_Lose_Extern'] . ">" . $array['LosNr_Extern'] . " - " . $array['LosBezeichnung_Extern'] . "</option>";
        }
    }
    echo "</select></td>";
    echo "<td><button type='button' id='" . $row["id"] . "' class='btn btn-warning btn-sm' value='saveElement'><i class='far fa-save'></i></button></td>";
    echo "<td><div class='form-check form-switch'>
              <input class='form-check-input batch-select' type='checkbox' id='batchSelect" . $row["id"] . "' checked>
              <label class='form-check-label' for='flexSwitchCheckChecked'> </label>
            </div></td>";
    echo "</tr>";
}
echo "</tbody></table>";

$mysqli->close();
?>

<!-- Modal zum Anzeigen bzw Speichern des Kommentars -->
<div class='modal fade' id='commentModal' role='dialog'>
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Kommentar</h4>
            </div>
            <div class='modal-body' id='mbody'>
                <form role='form'>
                    <div class='form-group'>
                        <label for='modalKurzbeschreibung'></label><textarea class='form-control' rows='5'
                                                                             id='modalKurzbeschreibung'></textarea>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-warning btn-sm' value='saveComment'>Speichern<span
                            class='glyphicon glyphicon-floppy-disk'></span></button>
            </div>
        </div>

    </div>
</div>


<!--suppress ES6ConvertVarToLetConst -->
<script charset="utf-8">
    var tableRoomsWithElementTenderLots;
    var selectedRows = [];

    $(document).ready(function () {
        $('.batch-select').prop('checked', true)
        $('.batch-select').each(function () {
            var id = $(this).attr('id').replace('batchSelect', '');
            selectedRows.push(id);
        });        // console.log(selectedRows);
        $("#saveSelected").click(function () {
            selectedRows.forEach(function (ID) {
                saveElement(ID);
            });
        });

        $(document).on('change', '.batch-select', function () {
            var id = $(this).attr('id').replace('batchSelect', '');
            if ($(this).is(':checked')) {
                if (!selectedRows.includes(id)) selectedRows.push(id);
            } else {
                selectedRows = selectedRows.filter(function (value) {
                    return value !== id;
                });
            } // console.log(selectedRows);
        });

        $('#tableRoomsWithElementTenderLots').on('draw.dt', function () {
            var table = $(this);
            table.find('.batch-select').each(function () {
                var id = $(this).attr('id').replace('batchSelect', '');
                $(this).prop('checked', selectedRows.includes(id));
                var losExtern = $('#losExtern' + id);
                if (typeof losExtern.attr('data-select2-id') !== 'undefined') {
                    losExtern.trigger('change.select2');
                }
            });
        });

        $('#RemoveAllElementzFromBatch').click(function () {
            selectedRows = [];
            $('.batch-select').prop('checked', false);
            $('.form-control[id^="losExtern"]').val(0);
        });

        $("#globalLosExtern").change(function () {
            var selectedLot = $(this).val();
            selectedRows.forEach(function (id) {
                $("#losExtern" + id).val(selectedLot);
            });
        });


        $('#roomsWithElementCardHeader .xxx').remove();
        tableRoomsWithElementTenderLots = new DataTable('#tableRoomsWithElementTenderLots', {
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                    className: 'control',
                    orderable: false,
                    targets: 1
                },
                {
                    targets: [2, 6, 7, 8, 9, 11],
                    visible: true,
                    searchable: false,
                    orderable: false
                }
            ],
            responsive: {
                details: {
                    type: 'column',
                    target: 1
                }
            },
            searching: true,
            info: true,
            order: [[3, "asc"]],
            paging: false,
            // pagingType: "simple",
            lengthChange: false,
            // pageLength: 100,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                topEnd: 'search',
                topStart: null,  //"buttons",
                bottomStart: ['paging', "pageLength"],
                bottomEnd: 'info'
            },
            initComplete: function () {
                $('.dt-btn-group').attr('id', 'myDtBtnGroup');
                $('#roomsWithElementCardHeader .xxx').remove();
                $('#roomsWithElement .dt-search label').remove();
                $('#roomsWithElement .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark float-right xxx").appendTo('#roomsWithElementCardHeader');
            }
        });


        $('#tableRoomsWithElementTenderLots tbody').on('click', 'tr', function () {
            let id = tableRoomsWithElementTenderLots.row($(this)).data()[0];
            let stk = $("#amount" + id).val();
            $.ajax({
                url: "getElementBestand.php",
                data: {"id": id, "stk": stk},
                type: "GET",
                success: function (data) {
                    $("#elementBestand").html(data);
                    $("#elementBestand").show();
                }
            });
        });

        $("button[value='saveElement']").click(function () {
            saveElement(this.id);
        });


        $("button[value='openComment']").click(function () {        //Kommentar anzeigen
            let ID = this.id;
            $.ajax({
                url: "getComment.php",
                type: "GET",
                data: {"commentID": ID},
                success: function (data) {
                    $("#modalKurzbeschreibung").html(data);
                    $('#commentModal').modal('show');
                }
            });
        });

        $("button[value='saveComment']").click(function () {
            let comment = $("#modalKurzbeschreibung").val();
            alert(comment);
            $.ajax({
                url: "saveRoombookComment.php",
                type: "GET",
                data: {"comment": comment},
                success: function (data) {
                    alert(data);
                    $('#commentModal').modal('hide');
                }
            });
        });
    });


    function saveElement(ID) {
        let amount = $("#amount" + ID).val();
        let bestand = $("#bestand" + ID).val();
        let losExtern = $("#losExtern" + ID).val();
        if (losExtern === '0') {
            makeToaster("Erst los wählen...", false);
            return;
        }
        let comment = $("#comment" + ID).val();
        let standort = $("#Standort" + ID).val();
        let verwendung = $("#Verwendung" + ID).val();
        if (standort === '0' && verwendung === '0') {
            alert("Standort und Verwendung kann nicht Nein sein!");
            return; // Stop execution if validation fails
        }
        $.ajax({   // Make an AJAX call to save the data
            url: "saveRoombookTender.php", // Server-side script to handle saving
            type: "GET", // HTTP method
            data: {
                "amount": amount,
                "bestand": bestand,
                "losExtern": losExtern,
                "roombookID": ID,
                "comment": comment,
                "standort": standort,
                "verwendung": verwendung
            },
            success: function (data) {
                makeToaster(data.trim(), (data.trim() === "Erfolgreich aktualisiert!"));
            },
            error: function (xhr, status, error) {
                console.error("Error saving data:", error);
                alert("Es gab einen Fehler beim Speichern der Daten. Bitte versuchen Sie es erneut.");
            }
        });
    }


</script>

</body>
</html>