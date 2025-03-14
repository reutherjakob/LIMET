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


<div class="row">
    <div class="form-group form-check-inline d-flex align-items-center border border-light rounded bg-light">

        <div class="col-6 d-flex align-items-center justify-content-start">
            <label for="globalLosExtern" class="me-2"> Für ALLE Elemente im Batch</label>
            <select class="form-control form-control-sm mr-2 me-2" id="globalLosExtern" style="width: auto;">
                <option value="0" selected> Wähle ein Los</option>
                <?php
                foreach ($lotsInProject as $array) {
                    echo "<option value=" . $array['idtabelle_Lose_Extern'] . ">" . $array['LosNr_Extern'] . " - " . $array['LosBezeichnung_Extern'] . "</option>";
                } ?>
            </select>
            <button id="saveSelected" class="btn btn-warning  k btn-sm mr-2 me-2"><i class='far fa-save'></i></button>
        </div>
        <div class="col-6 d-flex justify-content-end" id="Whatever"></div>
    </div>

</div>


<?php
$raumbereich = urldecode($_GET["raumbereich"]);
if ($_GET["losID"] != "") {
    if ($raumbereich != "") {
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_lose_extern.LosNr_Extern, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                            FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_geraete.idTABELLE_Geraete = tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                            WHERE ( ((tabelle_räume.`Raumbereich Nutzer`)='" . $raumbereich . "') AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=" . $_GET["bestand"] . ") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)=" . $_GET["losID"] . ") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=" . $_GET["variantenID"] . ") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=" . $_GET["elementID"] . ") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                            ORDER BY tabelle_räume.Raumnr;";
    } else {
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_lose_extern.LosNr_Extern, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                            FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_geraete.idTABELLE_Geraete = tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                            WHERE ( ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=" . $_GET["bestand"] . ") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)=" . $_GET["losID"] . ") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=" . $_GET["variantenID"] . ") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=" . $_GET["elementID"] . ") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                            ORDER BY tabelle_räume.Raumnr;";
    }
} else {
    if ($raumbereich != "") {
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_lose_extern.LosNr_Extern, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                                    FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_geraete.idTABELLE_Geraete = tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                                    WHERE ( ((tabelle_räume.`Raumbereich Nutzer`)='" . $raumbereich . "') AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=" . $_GET["bestand"] . ") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) IS NULL) AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=" . $_GET["variantenID"] . ") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=" . $_GET["elementID"] . ") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                    ORDER BY tabelle_räume.Raumnr;";
    } else {
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_lose_extern.LosNr_Extern, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                                    FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_geraete.idTABELLE_Geraete = tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                                    WHERE ( ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=" . $_GET["bestand"] . ") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) IS NULL) AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=" . $_GET["variantenID"] . ") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=" . $_GET["elementID"] . ") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                    ORDER BY tabelle_räume.Raumnr;";

    }
}


$result = $mysqli->query($sql);

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
    echo "<td><input type='checkbox'  class='batch-select' id='batchSelect" . $row["id"] . "'checked></td>";
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

    var tableRoomsWithElementTenderLots;
    $(document).ready(function () {
        $('.dt-search').remove();
        tableRoomsWithElementTenderLots = new DataTable('#tableRoomsWithElementTenderLots', {
            paging: true,
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
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                topEnd: 'search',
                topStart: null,
                bottomStart: 'paging',
                bottomEnd: 'info'
            },


            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#Whatever');
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

        $("#globalLosExtern").change(function () {
            var selectedLot = $(this).val();
            $(".batch-select:checked").each(function () {
                var id = $(this).attr('id').replace('batchSelect', '');
                $("#losExtern" + id).val(selectedLot);
            });
            console.log(selectedLot);
        });


        $("#saveSelected").click(function () {
            $(".batch-select:checked").each(function () {
                var ID = $(this).attr('id').replace('batchSelect', '');
                console.log("Selected LOT", ID);
                saveElement(ID);
            });
        });


        $("button[value='saveElement']").click(function () {
            saveElement(this.id);
        });

        //Kommentar anzeigen
        $("button[value='openComment']").click(function () {
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


</script>

</body>
</html>