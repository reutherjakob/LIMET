<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
include "_format.php";
check_login();
?>
<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <style>
        .input-xs {
            height: 22px;
            padding: 2px 5px;
            font-size: 12px;
            line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
            border-radius: 3px;
        }
    </style>
    <title></title>
</head>
<body>

<?php
if (filter_input(INPUT_GET, 'id') != "") {
    $_SESSION["roombookID"] = filter_input(INPUT_GET, 'id');
}
if (filter_input(INPUT_GET, 'stk') != "") {
    $_SESSION["stk"] = filter_input(INPUT_GET, 'stk');
}


$mysqli = utils_connect_sql();

// Abfrage der Element-Geräte
$sql = "SELECT tabelle_geraete.idTABELLE_Geraete, tabelle_hersteller.Hersteller, tabelle_geraete.Typ
			FROM tabelle_räume_has_tabelle_elemente INNER JOIN (tabelle_hersteller INNER JOIN tabelle_geraete ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_geraete.TABELLE_Elemente_idTABELLE_Elemente
			WHERE (((tabelle_räume_has_tabelle_elemente.id)=" . $_SESSION["roombookID"] . "))
			ORDER BY tabelle_hersteller.Hersteller;";

$result = $mysqli->query($sql);

$possibleDevices = array();
while ($row = $result->fetch_assoc()) {
    $possibleDevices[$row['idTABELLE_Geraete']]['Hersteller'] = $row['Hersteller'];
    $possibleDevices[$row['idTABELLE_Geraete']]['Typ'] = $row['Typ'];
    $possibleDevices[$row['idTABELLE_Geraete']]['idTABELLE_Geraete'] = $row['idTABELLE_Geraete'];

}

$sql = "SELECT `tabelle_bestandsdaten`.`idtabelle_bestandsdaten`, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_bestandsdaten.`Aktueller Ort`, tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete
			FROM tabelle_bestandsdaten
			WHERE tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id=" . $_SESSION["roombookID"] . ";";

$result = $mysqli->query($sql);
$row_cnt = $result->num_rows;

//    echo " <button type='button' id='addBestandsElement' class='btn ml-4 mt-2 btn-outline-success btn-sm' value='Hinzufügen' data-bs-toggle='modal' data-bs-target='#addBestandModal'><i class='fas fa-plus'></i></button>";


echo "<div class='table-responsive'><table class='table table-striped table-bordered table-sm table-hover border border-5 border-light' id='tableElementBestandsdaten' >
	<thead><tr>
	<th>ID</th>
	<th></th>
	<th>Inventarnummer</th>
	<th>Seriennummer</th>
	<th>Anschaffungsjahr</th>
	<th>Gerät</th>
    <th>Standort aktuell</th>
	<th></th>                                                                                            
    <th>Check ob genug bestand da</th>
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idtabelle_bestandsdaten"] . "</td>";
    echo "<td><button type='button' id='" . $row["idtabelle_bestandsdaten"] . "' class='btn btn-danger btn-sm' value='deleteBestand'><i class='fas fa-minus-circle'></i></button></td>";
    echo "<td><input class='form-control form-control-sm' type='text' id='inventNr" . $row["idtabelle_bestandsdaten"] . "' value='" . $row["Inventarnummer"] . "' ></input></td>";
    echo "<td><input class='form-control form-control-sm' type='text' id='serienNr" . $row["idtabelle_bestandsdaten"] . "' value='" . $row["Seriennummer"] . "' ></input></td>";
    echo "<td><input class='form-control form-control-sm' type='text' id='yearNr" . $row["idtabelle_bestandsdaten"] . "' value='" . $row["Anschaffungsjahr"] . "' ></input></td>";
    echo "<td><select class='form-control form-control-sm' id='gereatIDSelect" . $row["idtabelle_bestandsdaten"] . "'>";
    if ($row["tabelle_geraete_idTABELLE_Geraete"] != "") {
        echo "<option value=0>Gerät wählen</option>";
        foreach ($possibleDevices as $array) {
            if ($array['idTABELLE_Geraete'] == $row["tabelle_geraete_idTABELLE_Geraete"]) {
                echo "<option selected value=" . $array['idTABELLE_Geraete'] . ">" . $array['Hersteller'] . "-" . $array['Typ'] . "</option>";
            } else {
                echo "<option value=" . $array['idTABELLE_Geraete'] . ">" . $array['Hersteller'] . "-" . $array['Typ'] . "</option>";
            }
        }
    } else {
        echo "<option value=0 selected>Gerät wählen</option>";
        foreach ($possibleDevices as $array) {
            echo "<option value=" . $array['idTABELLE_Geraete'] . ">" . $array['Hersteller'] . "-" . $array['Typ'] . "</option>";
        }
    }
    echo "</select></td>";
    echo "<td><input class='form-control form-control-sm' type='text' id='currentPlace" . $row["idtabelle_bestandsdaten"] . "' value='" . $row["Aktueller Ort"] . "' ></input></td>";
    echo "<td><button type='button' id='" . $row["idtabelle_bestandsdaten"] . "' class='btn btn-warning btn-sm' value='saveBestand'><i class='far fa-save'></i></button></td>";
    echo "<td>";
    if ($row_cnt == $_SESSION["stk"]) {
        echo "1";
    } else {
        echo "0";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</tbody></table></div>";
$mysqli->close();
?>

<!-- Modal zum Anlegen eines Bestands -->
<div class='modal fade' id='addBestandModal' role='dialog'>
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Bestand hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>

            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="invNr">Inventarnummer:</label>
                        <input type="text" class="form-control form-control-sm" id="invNr"
                               placeholder="Inventarnummer"/>
                    </div>
                    <div class="form-group">
                        <label for="year">Anschaffungsjahr:</label>
                        <input type="text" class="form-control form-control-sm" id="year"
                               placeholder="Anschaffungsjahr"/>
                    </div>
                    <div class="form-group">
                        <label for="serNr">Seriennummer:</label>
                        <input type="text" class="form-control form-control-sm" id="serNr" placeholder="Seriennummer"/>
                    </div>
                    <?php
                    echo "<div class='form-group'>
                                                      <label for='geraet'>Gerät:</label>									
                                                                      <select class='form-control form-control-sm' id='geraetNr' name='geraet'>
                                                                              <option value=0 selected>Gerät wählen</option>";
                    foreach ($possibleDevices as $array) {
                        echo "<option value=" . $array['idTABELLE_Geraete'] . ">" . $array['Hersteller'] . "-" . $array['Typ'] . "</option>";
                    }
                    echo "</select>										
                                              </div>";
                    ?>
                    <div class="form-group">
                        <label for="currentPlace">Standort aktuell:</label>
                        <input type="text" class="form-control form-control-sm" id="currentPlace"
                               placeholder="Standort"/>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addBestand' class='btn btn-success btn-sm' value='Hinzufügen'
                       data-bs-dismiss='modal'></input>
                <input type='button' id='saveBestand' class='btn btn-warning btn-sm' value='Speichern'
                       data-bs-dismiss='modal'></input>
                <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<script src="_utils.js"></script>
<script>
    var table;
    $(document).ready(function () {
        table = new DataTable("#tableElementBestandsdaten", {
            paging: false,
            ordering: false,
            searching: false,
            info: false,
            columnDefs: [
                {
                    targets: [0, 8],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [1, 7],
                    visible: true,
                    searchable: false,
                    orderable: false
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
            },
            scrollY: '20vh',
            scrollCollapse: true,
            rowCallback: function (row, data, displayNum, displayIndex, dataIndex) {
                if (data[8] === "0") {
                    $(row).css('background-color', 'rgba(100, 0, 25, 0.3)');
                } else {
                    $(row).css('background-color', 'rgba(100, 140, 25, 0.3)');
                }
            },
            initComplete: function (settings, json) {
                // Your initComplete function here
            }
        });
    });


    //Bestand hinzufügen
    $("#addBestand").click(function () {
        $("#addBestandModal").modal('hide');
        let inventarNr = $("#invNr").val();
        let anschaffungsJahr = $("#year").val();
        let serienNr = $("#serNr").val();
        let gereatID = $("#geraetNr").val();
        let currentPlace = $("#currentPlace").val();

        if (inventarNr !== "") {
            $.ajax({
                url: "addBestand.php",
                data: {
                    "inventarNr": inventarNr,
                    "anschaffungsJahr": anschaffungsJahr,
                    "serienNr": serienNr,
                    "gereatID": gereatID,
                    "currentPlace": currentPlace
                },
                type: "GET",
                success: function (data) {
                    // alert(data);
                    makeToaster(data, true);
                    $.ajax({
                        url: "getElementBestand.php",
                        type: "GET",
                        success: function (data) {
                            $("#elementBestand").html(data)
                            //$("#elementelementBestandsInLot").html(data);
                        }
                    });
                }
            });

        } else {
            alert("Bitte Inventarnummer angeben!");
        }

    });

    $("button[value='deleteBestand']").click(function () {
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "deleteBestand.php",
                data: {"bestandID": id},
                type: "GET",
                success: function (data) {
                    if (data.includes("error")) {
                        alert("Lol, hätteste gern.\nGeht aber nich... \nFrag den Jakob.");
                    } else {
                        alert(data);
                    }
                    $.ajax({
                        url: "getElementBestand.php",
                        type: "GET",
                        success: function (data) {
                            $("#elementBestand").html(data);
                        }
                    });
                },
                error: function () {
                    alert("Lol, hätteste gern.\nGeht aber nich... \nFrag den Jakob.");
                }
            });
        }
    });

    //Bestand ändern
    $("button[value='changeBestand']").click(function () {
        let id = this.id;
        $("#saveBestand").show();
        $("#addBestand").hide();
        document.getElementById("invNr").value = invent_clicked;
        document.getElementById("year").value = anschaffung_clicked;
        document.getElementById("serNr").value = serien_clicked;
        $('#addBestandModal').modal("show");
    });

    $("button[value='saveBestand']").click(function () {
        let ID = this.id;
        let geraeteIDNeu = $("#gereatIDSelect" + ID).val();
        let inventNr = $("#inventNr" + ID).val();
        let serienNr = $("#serienNr" + ID).val();
        let yearNr = $("#yearNr" + ID).val();
        let currentPlace = $("#currentPlace" + ID).val();

        if (ID !== "" && inventNr !== "") {
            $.ajax({
                url: "saveBestand.php",
                data: {
                    "inventarNr": inventNr,
                    "anschaffungsJahr": yearNr,
                    "serienNr": serienNr,
                    "bestandID": ID,
                    "geraeteID": geraeteIDNeu,
                    "currentPlace": currentPlace
                },
                type: "GET",
                success: function (data) {
                    makeToaster(data, true)
                    $.ajax({
                        url: "getElementBestand.php",
                        type: "GET",
                        success: function (data) {
                            makeToaster("Saved!", true);
                            //$("#elementBestand").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Keine Bestands-ID gefunden bzw. Keine Inventarnummer eingegeben!");
        }
    });

    $("#addBestandsElement").click(function () {
        let id = this.id;
        $("#addBestand").show();
        $("#saveBestand").hide();
    });
</script>
</body>
</html>