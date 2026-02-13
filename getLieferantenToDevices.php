<?php
// 25 FX
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$deviceID = isset($_SESSION["deviceID"]) ? intval($_SESSION["deviceID"]) : 0;
if ($deviceID <= 0) {
    die("Invalid device ID");
}

$sql = "
    SELECT 
        tabelle_lieferant.idTABELLE_Lieferant, 
        tabelle_lieferant.Lieferant, 
        tabelle_lieferant.Land, 
        tabelle_lieferant.Ort
    FROM tabelle_geraete_has_tabelle_lieferant
    INNER JOIN tabelle_lieferant 
        ON tabelle_geraete_has_tabelle_lieferant.tabelle_lieferant_idTABELLE_Lieferant = tabelle_lieferant.idTABELLE_Lieferant
    WHERE tabelle_geraete_has_tabelle_lieferant.tabelle_geraete_idTABELLE_Geraete = ?
    ORDER BY tabelle_lieferant.Lieferant;
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $deviceID);
$stmt->execute();
$result = $stmt->get_result();


echo "<table class='table table-striped table-sm' id='tableDeviceLieferanten' >
	<thead><tr>
	<th></th> 
	<th>Lieferant</th>
	<th>Land</th>
	<th>Ort</th>
        <th></th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Lieferant"] . "' class='btn btn-outline-danger btn-sm' value='deleteLieferant'><i class='fas fa-minus'></i></button></td>";
    echo "<td>" . $row["Lieferant"] . "</td>";
    echo "<td>" . $row["Land"] . "</td>";
    echo "<td>" . $row["Ort"] . "</td>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Lieferant"] . "' class='btn btn-outline-dark btn-sm' value='showLieferantContacts' data-bs-toggle='modal' 
            data-bs-target='#showLieferantContactsModal'><i class='fas fa-users'></i></button></td>";
    echo "</tr>";
}
echo "</tbody></table>";
echo "
<div class='col-12 d-flex justify-content-end'>
    <button type='button'
            id='addLieferantModalButton'
            class='btn btn-sm btn-success mt-2'
            value='Lieferant hinzufügen'
            data-bs-toggle='modal'
            data-bs-target='#addLieferantModal'>
            <i class='fas fa-plus'></i> 
        Lieferant hinzufügen
    </button>
</div>";

include_once("modal_addLieferant.php");
?>


<!-- Modal zum Anzeigen der Lieferantenmitarbeiter-->
<div class='modal fade' id='showLieferantContactsModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-xl'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Lieferantenkontakte</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <div id="data"></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        new DataTable('#tableDeviceLieferanten', {
            columns: [
                {searchable: false, orderable: false},
                {orderable: true},
                {orderable: true},
                {orderable: true},
                {searchable: false, orderable: false}
            ],
            paging: false,
            searching: false,
            info: false,
            order: [[1, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: null
            }
        });


        document.getElementById('idlieferant').addEventListener('change', function () {
            if (this.value === 'new') {
                window.location.href = 'firmenkontakte.php';
            }
        });

    });

    //Lieferant zu Geraet hinzufügen
    $("#addLieferant").click(function () {
        let lieferantenID = $("#idlieferant").val();
        if (lieferantenID !== "0") {
            $.ajax({
                url: "addLieferantToDevice.php",
                data: {"lieferantenID": lieferantenID},
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getLieferantenToDevices.php",
                        type: "POST",
                        success: function (data) {
                            $("#deviceLieferanten").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Kein Lieferant ausgewählt!");
        }
    });

    //Lieferant von Gerät löschen
    $("button[value='deleteLieferant']").click(function () {
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "deleteLieferantFromDevice.php",
                data: {"lieferantID": id},
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getLieferantenToDevices.php",
                        type: "POST",
                        success: function (data) {
                            $("#deviceLieferanten").html(data);
                        }
                    });
                }
            });
        }
    });

    //Lieferantenkontakte anzeigen
    $("button[value='showLieferantContacts']").click(function () {
        let id = this.id;
        // console.log(id);
        if (id !== "") {
            $.ajax({
                url: "getPersonsOfLieferant.php",
                data: {"lieferantID": id},
                type: "POST",
                success: function (data) {
                    $("#data").html(data);
                }
            });
        }
    });


</script>

</body>
</html>