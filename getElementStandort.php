<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title>
    <style>
        /* Place this in your page CSS or inside a `<style>` tag */
        .select2-container {
            z-index: 2050 !important; /* Bootstrap modal is 1050, so this must be higher */
        }

        .select2-container--bootstrap-5 .select2-results__options {
            max-height: 300px;
            overflow-y: auto;
        }

        #standortElement {
            width: 100% !important;
        }

    </style>

</head>

<body>


<?php
require_once 'utils/_utils.php';
init_page_serversides();
$mysqli = utils_connect_sql();


$sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Geschoss, tabelle_räume.`Raumbereich Nutzer`,
               tabelle_varianten.Variante, tabelle_verwendungselemente.id_Standortelement
        FROM tabelle_verwendungselemente
        INNER JOIN (tabelle_varianten
            INNER JOIN (tabelle_räume
                INNER JOIN tabelle_räume_has_tabelle_elemente
                    ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)
            ON tabelle_verwendungselemente.id_Standortelement = tabelle_räume_has_tabelle_elemente.id
        WHERE tabelle_verwendungselemente.id_Verwendungselement = ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


echo "<div class='table-responsive'><table class='table table-striped table-sm' id='tableElementStandortdaten'  >
	<thead><tr>
        <th></th>
        <th>Variante</th>
	<th>Raumnr</th>
	<th>Raumbezeichnung</th>
	<th>Geschoss</th>
	<th>Raumbereich Nutzer</th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["id_Standortelement"] . "' class='btn btn-danger btn-sm' value='deleteStandortElement'><i class='fas fa-minus-circle'></i></button></td>";
    echo "<td>" . $row["Variante"] . "</td>";
    echo "<td>" . $row["Raumnr"] . "</td>";
    echo "<td>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td>" . $row["Geschoss"] . "</td>";
    echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
    echo "</tr>";

}
echo "</tbody></table></div>";
//echo "<input type='button' id='addStandortElementModalButton' class='btn btn-success btn-sm' value='Standortelement hinzufügen' data-bs-toggle='modal' data-bs-target='#addStandortElementModal'></input>";
echo "<button type='button' id='addStandortElementModalButton' class='btn ml-4 mt-2 btn-success btn-sm' value='Standortelement hinzufügen' data-bs-toggle='modal' data-bs-target='#addStandortElementModal'><i class='fas fa-plus-square'></i></button>";


?>
<!-- Modal zum Hinzufügen eines Standortelements -->
<div class='modal fade' id='addStandortElementModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-lg'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Standortelement hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <?php
                    $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, 
                   tabelle_räume.Raumnr, 
                   tabelle_räume.Raumbezeichnung, 
                   tabelle_räume.Geschoss, 
                   tabelle_räume.`Raumbereich Nutzer`, 
                   tabelle_räume_has_tabelle_elemente.Anzahl, 
                   tabelle_varianten.Variante, 
                   tabelle_bestandsdaten.Inventarnummer
            FROM tabelle_bestandsdaten 
            RIGHT JOIN (
                tabelle_varianten 
                INNER JOIN (
                    tabelle_räume 
                    INNER JOIN tabelle_räume_has_tabelle_elemente 
                        ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                ) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            ) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id
            WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
              AND tabelle_räume_has_tabelle_elemente.Standort = 1 
              AND tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = ?";


                    if ($stmt = $mysqli->prepare($sql)) {
                        $projectID = (int)$_SESSION["projectID"];
                        $elementID = (int)filter_input(INPUT_GET, 'elementID', FILTER_VALIDATE_INT);
                        if ($elementID === false || $elementID === null) {
                            echo "<p>Invalid elementID provided.</p>";
                        } else {
                            $stmt->bind_param('ii', $projectID, $elementID);
                            if ($stmt->execute()) {
                                $result = $stmt->get_result();

                                echo "<div class='form-group'>
                        <label for='standortElement'>Standortelement:</label>                           
                        <select name='standortElement' class='form-control form-control-sm' id='standortElement'>
                            <option value='0' selected>Standortelement auswählen!</option>";

                                while ($row = $result->fetch_assoc()) {

                                    $optionValue = htmlspecialchars($row["id"]??'');
                                    $raumbereich = htmlspecialchars($row["Raumbereich Nutzer"]??'');
                                    $raumnr = htmlspecialchars($row["Raumnr"]??'');
                                    $raumbez = htmlspecialchars($row["Raumbezeichnung"]??'');
                                    $anzahl = htmlspecialchars($row["Anzahl"]??'');
                                    $variante = htmlspecialchars($row["Variante"]??'');
                                    $inventarnr = htmlspecialchars($row["Inventarnummer"]??'');

                                    echo "<option value='{$optionValue}'>
                            Raumbereich: {$raumbereich} - Raumnr: {$raumnr} - Raum: {$raumbez} - Stk: {$anzahl} - Variante: {$variante} - Inventarnummer: {$inventarnr}
                          </option>";
                                }
                                echo "</select>
                    </div>";
                            } else {
                                echo "<p>Failed to execute query.</p>";
                            }
                            $stmt->close();
                        }
                    } else {
                        echo "<p>Failed to prepare query.</p>";
                    }
                    ?>
                </form>

            </div>
            <div class='modal-footer'>
                <input type='button' id='addStandortElement' class='btn btn-success btn-sm' value='Hinzufügen'></input>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

    </div>
</div>
<?php
$mysqli->close();
?>


<script>

    var id = <?php echo filter_input(INPUT_GET, 'id') ?>;
    var elementID = <?php echo filter_input(INPUT_GET, 'elementID') ?>;

    $(document).ready(function () {
        $("#tableElementStandortdaten").DataTable({
            paging: false,
            searching: false,
            info: false,
            columnDefs: [
                {
                    targets: 0,
                    searchable: false,
                    orderable: false
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
            },
            scrollY: "20vh",
            scrollCollapse: true
        });

        $('#addStandortElementModal').on('shown.bs.modal', function () {
            $('#standortElement').select2({
                placeholder: "Standortelement auswählen!",
                width: '100%',
                allowClear: true,
                dropdownParent: $('#addStandortElementModal')  // **Attach dropdown inside modal**
            });
        });


        //Standortelement hinzufügen
        $("#addStandortElement").click(function () {
            var standortElement = $("#standortElement").val();
            if (standortElement !== "0") {
                $.ajax({
                    url: "addStandortElement.php",
                    data: {"standortElement": standortElement, "id": id},
                    type: "GET",
                    success: function (data) {
                        $('#addStandortElementModal').modal('hide');
                        alert(data);

                        $.ajax({
                            url: "getElementStandort.php",
                            data: {"id": id, "elementID": elementID},
                            type: "GET",
                            success: function (data) {
                                $("#elementVerwendung").html(data);
                            }
                        });
                    }
                });
            } else {
                alert("Bitte Standortelement auswählen!");
            }

        });

        //Standortelement löschen
        $("button[value='deleteStandortElement']").click(function () {
            var standortID = this.id;
            if (standortID !== "") {
                $.ajax({
                    url: "deleteStandortElement.php",
                    data: {"standortID": standortID, "verwendungID": id},
                    type: "GET",
                    success: function (data) {
                        makeToaster("Element Standort e2ntfernt" ,true);
                        $.ajax({
                            url: "getElementStandort.php",
                            data: {"id": id, "elementID": elementID},
                            type: "GET",
                            success: function (data) {
                                $("#elementVerwendung").html(data);
                            }
                        });

                    }
                });
            }
        });
    })


</script>

</body>
</html>