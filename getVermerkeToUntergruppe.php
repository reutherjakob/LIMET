<!-- 13.2.25: Reworked -->

<?php
include "_utils.php";
check_login();

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_Vermerke.idtabelle_Vermerke, tabelle_Vermerke.tabelle_räume_idTABELLE_Räume, tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern, tabelle_Vermerke.Ersteller, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Faelligkeit, tabelle_räume.Raumnr, tabelle_lose_extern.LosNr_Extern
                FROM tabelle_lose_extern RIGHT JOIN (tabelle_räume RIGHT JOIN tabelle_Vermerke ON tabelle_räume.idTABELLE_Räume = tabelle_Vermerke.tabelle_räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern
                WHERE (((tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe)=" . filter_input(INPUT_GET, 'vermerkUntergruppenID') . "))
                ORDER BY tabelle_Vermerke.Erstellungszeit;";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-bordered table-responsive' id='tableVermerke'>
                <thead><tr>
                <th>ID</th>
                <th></th>
                <th>Vermerk</th>
                <th>Ersteller</th>
                <th>Fälligkeit</th>
                <th>Erstellt am</th>
                <th>Status</th>
                <th>Vermerkart</th>
                <th>Zuständigkeit</th>
                <th>LosID</th>
                <th>RaumID</th>
                <th>Los</th>
                <th>Raum</th>
                </tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['idtabelle_Vermerke'] . "</td>";
    echo "<td><button type='button' id='" . $row['idtabelle_Vermerke'] . "' class='btn btn-outline-dark btn-xs' value='changeVermerk'><i class='fas fa-pencil-alt'></i></button></td>";
    echo "<td id='vermerktText" . $row["idtabelle_Vermerke"] . "' value ='" . $row['Vermerktext'] . "'>" . $row['Vermerktext'] . "</td>";
    echo "<td>" . $row['Ersteller'] . "</td>";
    if ($row["Vermerkart"] != "Info") {
        echo "<td id='faelligkeit" . $row["idtabelle_Vermerke"] . "' value ='" . $row['Faelligkeit'] . "'>" . $row['Faelligkeit'] . "</td>";
    } else {
        echo "<td>";
        echo "</td>";
    }
    echo "<td>" . $row['Erstellungszeit'] . "</td>";
    echo "<td id='bearbeitungsstatus" . $row["idtabelle_Vermerke"] . "' value ='" . $row['Bearbeitungsstatus'] . "'>" . $row['Bearbeitungsstatus'] . "</td>";
    echo "<td id='vermerkTyp" . $row["idtabelle_Vermerke"] . "' value ='" . $row['Vermerkart'] . "'>" . $row['Vermerkart'] . "</td>";
    echo "<td><button type='button' id=" . $row['idtabelle_Vermerke'] . " class='btn btn-outline-dark btn-xs' value='showVermerkZustaendigkeit' data-bs-toggle='modal' data-bs-target='#showVermerkZustaendigkeitModal'><i class='fas fa-users'></i></button></td>";
    echo "<td id='lot" . $row["idtabelle_Vermerke"] . "' value ='" . $row['tabelle_lose_extern_idtabelle_Lose_Extern'] . "'>" . $row['tabelle_lose_extern_idtabelle_Lose_Extern'] . "</td>";
    echo "<td id='room" . $row["idtabelle_Vermerke"] . "' value ='" . $row['tabelle_räume_idTABELLE_Räume'] . "'>" . $row['tabelle_räume_idTABELLE_Räume'] . "</td>";
    echo "<td>" . $row['LosNr_Extern'] . "</td>";
    echo "<td>" . $row['Raumnr'] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
?>

<!-- Modal zum Hinzufügen/Ändern eines Vermerks -->
<div class='modal fade' id='changeVermerkModal' role='dialog'>
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Vermerkdaten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='vermerkMbody'>
                <form role="form">
                    <?php
                    $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.idTABELLE_Räume, tabelle_räume.`Raumbereich Nutzer`
                                            FROM tabelle_räume
                                            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                            ORDER BY tabelle_räume.Raumnr, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumbezeichnung;";
                    $result1 = $mysqli->query($sql);

                    echo "<div class='form-group'>
                                        <label for='room'>Raum:</label>									
                                        <select class='form-control form-control-sm' id='room' name='room'>
                                                <option value=0>Kein Raum</option>";
                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Räume"] . ">" . $row["Raumnr"] . " - " . $row["Raumbereich Nutzer"] . " - " . $row["Raumbezeichnung"] . "</option>";
                    }
                    echo "</select></div>";

                    $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lieferant.Lieferant
                                            FROM tabelle_lose_extern LEFT JOIN tabelle_lieferant ON tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant = tabelle_lieferant.idTABELLE_Lieferant
                                            WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                            ORDER BY tabelle_lose_extern.LosNr_Extern;";

                    $result1 = $mysqli->query($sql);

                    echo "<div class='form-group'>
                                        <label for='los'>Los:</label>									
                                        <select class='form-control form-control-sm' id='los' name='los'>
                                                <option value=0>Kein Los</option>";
                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idtabelle_Lose_Extern"] . ">" . $row["LosNr_Extern"] . " - " . $row["LosBezeichnung_Extern"] . " - " . $row["Lieferant"] . "</option>";
                    }
                    echo "</select><div>";

                    // Untergruppen-Abfrage für Änderung der Untergruppe
                    $sql = "SELECT tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe, tabelle_Vermerkuntergruppe.Untergruppenname, tabelle_Vermerkuntergruppe.Untergruppennummer
                                            FROM tabelle_Vermerkuntergruppe
                                            WHERE (((tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=" . filter_input(INPUT_GET, 'vermerkGruppenID') . "))
                                            ORDER BY Untergruppennummer ASC;";

                    $result1 = $mysqli->query($sql);

                    echo "<div class='form-group'>
                                        <label for='untergruppe'>Untergruppe:</label>									
                                        <select class='form-control form-control-sm' id='untergruppe' name='untergruppe'>";
                    while ($row = $result1->fetch_assoc()) {
                        if ($row["idtabelle_Vermerkuntergruppe"] == filter_input(INPUT_GET, 'vermerkUntergruppenID')) {
                            echo "<option value=" . $row["idtabelle_Vermerkuntergruppe"] . " selected>" . $row["Untergruppennummer"] . " - " . $row["Untergruppenname"] . "</option>";
                        } else {
                            echo "<option value=" . $row["idtabelle_Vermerkuntergruppe"] . ">" . $row["Untergruppennummer"] . " - " . $row["Untergruppenname"] . "</option>";
                        }
                    }
                    echo "</select>  </div>";

                    $mysqli->close();
                    ?>
                    <div class='form-group'>
                        <label for='vermerkStatus'>Status:</label>
                        <select class='form-control form-control-sm' id='vermerkStatus' name='vermerkStatus'>
                            <option value=0 selected>Offen</option>
                            <option value=1>Erledigt</option>
                        </select>
                    </div>
                    <div class='form-group'>
                        <label for='vermerkTyp'>Vermerktyp:</label>
                        <select class='form-control form-control-sm' id='vermerkTyp' name='vermerkTyp'>
                            <option value='Info' selected>Info</option>
                            <option value='Bearbeitung'>Bearbeitung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="faelligkeit">Fällig am:</label>
                        <input type="text" class="form-control form-control-sm" id="faelligkeit"
                               placeholder="jjjj.mm.tt" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="vermerkText">Text:</label>
                        <textarea class="form-control form-control-sm" rows="15" id="vermerkText"
                                  style="font-size:10pt"> </textarea>


                    </div>
                    <div class="form-group">

                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addVermerk' class='btn btn-success btn-sm' value='Hinzufügen'
                       data-bs-dismiss='modal'></input>
                <input type='button' id='saveVermerk' class='btn btn-warning btn-sm' value='Speichern'
                       data-bs-dismiss='modal'></input>
                <input type='button' id='deleteVermerk' class='btn btn-danger btn-sm' value='Löschen'
                       data-bs-dismiss='modal'></input>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal für Zustaendigkeit-->
<div class='modal fade' id='showVermerkZustaendigkeitModal' role='dialog'>
    <div class='modal-dialog modal-lg'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Zustaendigkeiten:</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>

            </div>
            <div class='modal-body' id='showZustaendigkeitenModalBody'>
                <div class="mt-4 card">
                    <div class="card-header">Eingetragene Zuständigkeit:</div>
                    <div class="card-body" id='vermerkZustaendigkeit'>
                    </div>
                </div>
                <div class="mt-4 card">
                    <div class="card-header">Mögliche Personen:</div>
                    <div class="card-body" id='possibleVermerkZustaendigkeit'>
                    </div>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' value='closeModal' data-bs-dismiss='modal'>
                    Schließen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Löschen eines Vermerks-->
<div class='modal fade' id='deleteVermerkModal' role='dialog'>
    <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Vermerk löschen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>Wollen Sie den Vermerk wirklich löschen? Sämtliche Informationen gehen
                verloren.
            </div>
            <div class='modal-footer'>
                <input type='button' id='deleteVermerkExecute' class='btn btn-danger btn-sm' value='Ja'
                       data-bs-dismiss='modal'></input>
                <button type='button' class='btn btn-success btn-sm' data-bs-dismiss='modal'>Nein</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Bild-Upload -->
<div class='modal fade' id='uploadImageModal' role='dialog'>
    <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Bild uploaden</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role='form' id="uploadForm" enctype="multipart/form-data">
                    <div class='form-group'>
                        <input type='hidden' id='vermerkID'/>
                    </div>
                    <div class='form-group'>
                        <label for='imageUpload'>Bild (.jpeg):</label>
                        <input type="file" name="imageUpload" id="imageUpload"> <br>
                        <img id="image" alt="">
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='uploadImageButton' class='btn btn-outline-dark btn-sm' value='Upload'
                       data-bs-dismiss='modal'>
            </div>
        </div>
    </div>
</div>


<script>
    /* Within DocumentationV2.php
    var vermerkID;*/

    var vermerkGruppenID = <?php echo filter_input(INPUT_GET, 'vermerkGruppenID') ?>;

    $(document).ready(function () {
        document.getElementById("buttonNewVermerk").style.visibility = "visible";
        document.getElementById("buttonNewVermerkuntergruppe").style.visibility = "visible";

        var tableVermerke = new DataTable('#tableVermerke', {
            columnDefs: [
                {
                    targets: [0, 6, 9, 10],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [1],
                    ible: true,
                    sevisarchable: false,
                    orderable: false
                }
            ], responsive: true,
            paging: true,
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 20,
            searching: true,
            info: false,
            order: [[5, 'asc']],
            compact: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json', search: ""
            },
            rowCallback: function (row, data, displayNum, displayIndex, dataIndex) {
                if (data[7] === "Bearbeitung") {
                    if (data[6] === "0") {
                        row.style.backgroundColor = '#ff8080';
                    } else {
                        row.style.backgroundColor = '#b8dc6f';
                    }
                } else {
                    row.style.backgroundColor = '#d3edf8';
                }
            }, initComplete: function () {
                search_counter = search_counter + 1;
                /* $('#dt-search-' + (search_counter - 1)).remove();                   move_dt_search("#dt-search-" + search_counter, "#CardHeaderVermerkUntergruppen"); */


            }
        });


        $('#tableVermerke tbody').on('click', 'tr', function () {
            vermerkID = tableVermerke.row($(this)).data()[0];
            document.getElementById("vermerkStatus").value = tableVermerke.row($(this)).data()[6];
            document.getElementById("vermerkText").value = tableVermerke.row($(this)).data()[2];
            document.getElementById("faelligkeit").value = tableVermerke.row($(this)).data()[4];
            document.getElementById("vermerkTyp").value = tableVermerke.row($(this)).data()[7];

            if (tableVermerke.row($(this)).data()[9] === '') {
                document.getElementById("los").value = 0;
            } else {
                document.getElementById("los").value = tableVermerke.row($(this)).data()[9];
            }
            if (tableVermerke.row($(this)).data()[10] === '') {
                document.getElementById("room").value = 0;
            } else {
                document.getElementById("room").value = tableVermerke.row($(this)).data()[10];
            }
            if (tableVermerke.row($(this)).data()[7] === "Bearbeitung") {
                $("#faelligkeit").prop('disabled', false);
            } else {
                $("#faelligkeit").prop('disabled', true);
            }
            const addImage = document.getElementById("addImage");
            if (addImage) {
                addImage.style.visibility = "visible";
            }
        });

        $('#faelligkeit').datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked",
            language: "de"
        });
    });

    //$("button[value='Neuer Vermerk']").click(function(){     
    $("#buttonNewVermerk").click(function () {
        document.getElementById("saveVermerk").style.display = "none";
        document.getElementById("deleteVermerk").style.display = "none";
        $("#untergruppe").prop('disabled', true);
        document.getElementById("addVermerk").style.display = "inline";
        $('#changeVermerkModal').modal('show');
    });

    $("#addVermerk").click(function () {
        let room = $("#room").val();
        let los = $("#los").val();
        let vermerkStatus = $("#vermerkStatus").val();
        let vermerkTyp = $("#vermerkTyp").val();
        let vermerkText = $("#vermerkText").val();
        let faelligkeitDatum = $("#faelligkeit").val();

        if (vermerkTyp === "Info") {
            faelligkeitDatum = null;
        }
        let vermerkUntergruppenID = <?php echo filter_input(INPUT_GET, 'vermerkUntergruppenID') ?>;

        if (room !== "" && los !== "" && vermerkStatus !== "" && vermerkTyp !== "" && vermerkText !== "") {
            $('#changeVermerkModal').modal('hide');
            $.ajax({
                url: "addVermerk.php",
                data: {
                    "untergruppenID": vermerkUntergruppenID,
                    "room": room,
                    "los": los,
                    "vermerkStatus": vermerkStatus,
                    "vermerkTyp": vermerkTyp,
                    "vermerkText": vermerkText,
                    "faelligkeitDatum": faelligkeitDatum
                },
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getVermerkeToUntergruppe.php",
                        data: {"vermerkUntergruppenID": vermerkUntergruppenID, "vermerkGruppenID": vermerkGruppenID},
                        type: "GET",
                        success: function (data) {
                            $("#vermerke").html(data);
                            document.getElementById('pdfPreview').src += '';
                        }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });

    $('#vermerkTyp').change(function () {
        let typ = $('#vermerkTyp').val();
        if (typ === "Bearbeitung") {
            $("#faelligkeit").prop('disabled', false);
        } else {
            $("#faelligkeit").prop('disabled', true);
        }
    });

    $("button[value='changeVermerk']").click(function () {
        // Buttons ein/ausblenden!
        document.getElementById("saveVermerk").style.display = "inline";
        document.getElementById("deleteVermerk").style.display = "inline";
        $("#untergruppe").prop('disabled', false);
        document.getElementById("addVermerk").style.display = "none";
        $('#changeVermerkModal').modal('show');
    });

    // Vermerk ändern/speichern
    $("#saveVermerk").click(function () {
        let room = $("#room").val();
        let los = $("#los").val();
        let vermerkStatus = $("#vermerkStatus").val();
        let vermerkTyp = $("#vermerkTyp").val();
        let vermerkText = $("#vermerkText").val();
        let faelligkeitDatum = $("#faelligkeit").val();
        let untergruppenID = $("#untergruppe").val();

        if (vermerkTyp === "Info") {
            faelligkeitDatum = null;
        }
        let vermerkUntergruppenID = <?php echo filter_input(INPUT_GET, 'vermerkUntergruppenID') ?>;

        if (room !== "" && los !== "" && vermerkStatus !== "" && vermerkTyp !== "" && vermerkText !== "") {
            $('#changeVermerkModal').modal('hide');
            $.ajax({
                url: "saveVermerk.php",
                data: {
                    "vermerkID": vermerkID,
                    "room": room,
                    "los": los,
                    "vermerkStatus": vermerkStatus,
                    "vermerkTyp": vermerkTyp,
                    "vermerkText": vermerkText,
                    "faelligkeitDatum": faelligkeitDatum,
                    "untergruppenID": untergruppenID
                },
                type: "GET",
                success: function (data) {
                    alert(data);
                    console.log(vermerkStatus);
                    $.ajax({
                        url: "getVermerkeToUntergruppe.php",
                        data: {"vermerkUntergruppenID": vermerkUntergruppenID, "vermerkGruppenID": vermerkGruppenID},
                        type: "GET",
                        success: function (data) {
                            $("#vermerke").html(data);
                            // Neu laden der PDF-Vorschau
                            document.getElementById('pdfPreview').src += '';
                        }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });


    // Vermerk löschen -> Modal öffnen
    $("#deleteVermerk").click(function () {
        $('#deleteVermerkModal').modal('show');
    });

    // Vermerk löschen
    $("#deleteVermerkExecute").click(function () {
        let vermerkUntergruppenID = <?php echo filter_input(INPUT_GET, 'vermerkUntergruppenID') ?>;

        $.ajax({
            url: "deleteVermerk.php",
            data: {"vermerkID": vermerkID},
            type: "GET",
            success: function (data) {
                alert(data);
                // Neu Laden der Vermerkliste
                $.ajax({
                    url: "getVermerkeToUntergruppe.php",
                    data: {"vermerkUntergruppenID": vermerkUntergruppenID, "vermerkGruppenID": vermerkGruppenID},
                    type: "GET",
                    success: function (data) {
                        $("#vermerke").html(data);
                        // Neu laden der PDF-Vorschau
                        document.getElementById('pdfPreview').src += '';
                    }
                });
            }
        });
    });

    $("button[value='showVermerkZustaendigkeit']").click(function () {
        let id = this.id;
        $.ajax({
            url: "getVermerkZustaendigkeiten.php",
            type: "GET",
            data: {"vermerkID": id},
            success: function (data) {
                $("#vermerkZustaendigkeit").html(data);
                $.ajax({
                    url: "getPossibleVermerkZustaendigkeiten.php",
                    type: "GET",
                    data: {"vermerkID": id},
                    success: function (data) {
                        $("#possibleVermerkZustaendigkeit").html(data);
                        $('#showVermerkZustaendigkeitModal').modal('show');
                    }
                });

            }
        });

    });

    $("#addImage").click(function () {
        $('#uploadImageModal').modal('show');
    });

    $("#uploadImageButton").click(function () {
        // get selected Image
        //let input = document.getElementById("imageUpload").files;
        let file = document.querySelector('#imageUpload').files[0];
        if (!file) {
            alert("Bitte Datei auswählen");
        } else {
            //define the width to resize -> 1000px
            let resize_width = 800;//without px
            //create a FileReader
            let reader = new FileReader();
            //image turned to base64-encoded Data URI.
            reader.readAsDataURL(file);
            reader.name = file.name;//get the image's name
            reader.size = file.size; //get the image's size

            //Resize the image
            reader.onload = function (event) {
                let imageResized = new Image();//create a image
                imageResized.src = event.target.result;//result is base64-encoded Data URI
                imageResized.name = event.target.name;//set name (optional)
                imageResized.size = event.target.size;//set size (optional)
                imageResized.onload = function (el) {
                    let elem = document.createElement('canvas');//create a canvas
                    //scale the image and keep aspect ratio
                    let scaleFactor = resize_width / el.target.width;
                    elem.width = resize_width;
                    elem.height = el.target.height * scaleFactor;
                    //draw in canvas
                    let ctx = elem.getContext('2d');
                    ctx.drawImage(el.target, 0, 0, elem.width, elem.height);
                    //get the base64-encoded Data URI from the resize image
                    let srcEncoded = ctx.canvas.toDataURL('image/jpeg', 1);
                    //assign it to thumb src 
                    document.querySelector('#image').src = srcEncoded;

                    document.querySelector('#images_cb').src = srcEncoded;
                    /*Now you can send "srcEncoded" to the server and
                    convert it to a png o jpg. Also can send
                    "el.target.name" that is the file's name.*/
                    let resized = document.querySelector('#image').src;
                    //let resized = document.getElementById("image").files; 
                    let formData = new FormData();
                    //formData.append("fileUpload", files[0]);
                    formData.append("fileUpload", resized);
                    formData.append("vermerkID", vermerkID);
                    let xhttp = new XMLHttpRequest();

                    // Set POST method and ajax file path
                    xhttp.open("POST", "uploadFileImage.php", true);

                    // call on request changes state
                    xhttp.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            alert(this.responseText);
                        }
                    };
                    // Send request with data
                    xhttp.send(formData);
                };
            };
        }
    });
</script>

