<!-- 13.2.25: Reworked -->
<body>
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_Vermerke.idtabelle_Vermerke, tabelle_Vermerke.tabelle_räume_idTABELLE_Räume, tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern, tabelle_Vermerke.Ersteller, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Faelligkeit, tabelle_räume.Raumnr, tabelle_lose_extern.LosNr_Extern
                FROM tabelle_lose_extern RIGHT JOIN (tabelle_räume RIGHT JOIN tabelle_Vermerke ON tabelle_räume.idTABELLE_Räume = tabelle_Vermerke.tabelle_räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern
                WHERE (((tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe)=" . filter_input(INPUT_GET, 'vermerkUntergruppenID') . "))
                ORDER BY tabelle_Vermerke.Erstellungszeit;";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-bordered table-responsive border border-light border-5' id='tableVermerke'>
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
    echo "<td><button type='button' id='" . $row['idtabelle_Vermerke'] . "' class='btn btn-outline-dark btn-sm' value='changeVermerk'><i class='fas fa-pencil-alt'></i></button></td>";
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
    echo "<td><button type='button' id=" . $row['idtabelle_Vermerke'] . " class='btn btn-outline-dark btn-sm' value='showVermerkZustaendigkeit' data-bs-toggle='modal' data-bs-target='#showVermerkZustaendigkeitModal'><i class='fas fa-users'></i></button></td>";
    //
    echo "<td id='lot" . $row["idtabelle_Vermerke"] . "' value ='" . $row['tabelle_lose_extern_idtabelle_Lose_Extern'] . "'>" . $row['tabelle_lose_extern_idtabelle_Lose_Extern'] . "</td>";
    echo "<td id='room" . $row["idtabelle_Vermerke"] . "' value ='" . $row['tabelle_räume_idTABELLE_Räume'] . "'>" . $row['tabelle_räume_idTABELLE_Räume'] . "</td>";
    echo "<td>" . $row['LosNr_Extern'] . "</td>";
    echo "<td>" . $row['Raumnr'] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
?>
</body>
<!-- Modal zum Hinzufügen/Ändern eines Vermerks -->
<div class='modal fade' id='changeVermerkModal' role='dialog'>
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>

            <div class='modal-header'>
                <h4 class='modal-title'>Vermerkdaten</h4>
                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
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
                       data-bs-dismiss='modal'>
                <input type='button' id='saveVermerk' class='btn btn-warning btn-sm' value='Speichern'
                       data-bs-dismiss='modal'>
                <input type='button' id='deleteVermerk' class='btn btn-danger btn-sm' value='Löschen'>
                <button type='button' class='btn btn-close btn-sm' data-bs-dismiss='modal'> </button>
            </div>

        </div>
    </div>
</div>


<!-- Modal für Zustaendigkeit-->
<div class='modal fade' id='showVermerkZustaendigkeitModal' tabindex="-1"
     aria-labelledby="showVermerkZustaendigkeitModallabel" aria-modal="true" role="dialog"
     data-bs-keyboard="true">
    <div class='modal-dialog modal-xl'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Zustaendigkeiten</h4>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label="Close">
                </button>
            </div>
            <div class='modal-body' id='showZustaendigkeitenModalBody'>
                <div class="mt-4 card">
                    <div class="card-header d-flex align-items-center justify-content-between">Eingetragene Zuständigkeit
                        <div class="d-flex justify-content-end" id='vermerkZustaendigkeitCH'></div>
                    </div>
                    <div class="card-body" id='vermerkZustaendigkeit'></div>
                </div>
                <div class="mt-4 card">
                    <div class="card-header d-flex align-items-center justify-content-between">Mögliche Personen
                        <div class=" justify-content-end" id='possibleVermerkZustaendigkeitCH'></div>
                    </div>
                    <div class="card-body" id='possibleVermerkZustaendigkeit'></div>
                </div>
            </div>
            <div class='modal-footer'>
                <small style=" float: right; font-style: italic; font-family: cursive, 'Comic Sans MS', 'Brush Script MT', serif;">
                    Fehlt eine Person? Bei Projektbeteiligte anlegen.
                </small>
                <button type='button' class='btn btn-secondary btn-sm' value='closeModal' data-bs-dismiss='modal'>
                    Schließen
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteVermerkModal" tabindex="-1" aria-labelledby="deleteVermerkModalLabel"
     data-bs-keyboard="true"
     aria-modal="true" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteVermerkModalLabel">Vermerk löschen</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="Vmbody">
                Wollen Sie den Vermerk wirklich löschen? Sämtliche Informationen gehen verloren.
            </div>
            <div class="modal-footer">
                <button id="deleteVermerkExecute" class="btn btn-danger btn-sm" data-bs-dismiss="modal"> Ja</button>
                <button class="btn btn-success btn-sm" data-bs-dismiss="modal">Nein</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal für Bild-Upload -->
<div class='modal fade' id='uploadImageModal' role='dialog' tabindex="-1" data-bs-keyboard="true">
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


<script charset="utf-8 " type="text/javascript">
    vermerkGruppenID = <?php echo filter_input(INPUT_GET, 'vermerkGruppenID') ?>;
    /* Inititation within DocumentationV2.php; also 4:  var vermerkID;*/


    $(document).ready(function () {
        function decodeHtmlEntities(str) {
            let txt = document.createElement('textarea');
            txt.innerHTML = str;
            return txt.value;
        }


        document.getElementById("buttonNewVermerk").style.visibility = "visible";
        document.getElementById("buttonNewVermerkuntergruppe").style.visibility = "visible";
        $('#topDivSearch2').remove();
        $('#showVermerkZustaendigkeitModal').appendTo('body');


        tableVermerke = new DataTable('#tableVermerke', {
            columnDefs: [
                {
                    targets: [0, 6, 9, 10],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [1],
                    visible: true,
                    searchable: false,
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
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            dom: '<"#topDiv.top-container d-flex"<"col-md-6 justify-content-start"><"#topDivSearch2.col-md-6"f>>t<"bottom d-flex" <"col-md-6 justify-content-start"i><"col-md-6 d-flex align-items-center justify-content-end"lp>>',

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
                $('#topDivSearch2 label').remove();
                $('#topDivSearch2').removeClass("col-md-6").children().children().removeClass("form-control form-control-sm");
                $('#topDivSearch2').appendTo('#CardHeaderVermerUntergruppen').children().children().addClass("btn btn-sm btn-outline-dark");

                $('#tableVermerke tbody').on('click', 'tr', function () {
                    vermerkID = tableVermerke.row($(this)).data()[0];
                    document.getElementById("vermerkStatus").value = tableVermerke.row($(this)).data()[6];
                    document.getElementById("vermerkText").value = decodeHtmlEntities(tableVermerke.row($(this)).data()[2]);
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

            }
        });

        $('#tableVermerke tbody').on('click', "button[value='showVermerkZustaendigkeit']", function () {
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
                        }
                    });
                }
            });
        });
        $('#faelligkeit').datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked",
            language: "de"
        });
    });

    $("#buttonNewVermerk").click(function () {
        document.getElementById("saveVermerk").style.display = "none";
        document.getElementById("deleteVermerk").style.display = "none";
        $("#untergruppe").prop('disabled', true);
        document.getElementById("addVermerk").style.display = "inline";
        $('#deleteVermerkModal').modal('hide');
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
            $('#changeVermerkModal').modal('hide');
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
        document.getElementById("saveVermerk").style.display = "inline";
        document.getElementById("deleteVermerk").style.display = "inline";
        $("#untergruppe").prop('disabled', false);
        document.getElementById("addVermerk").style.display = "none";
        // $('#deleteVermerkModal').modal('hide');
        $('#changeVermerkModal').modal('show');
    });

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

    $("#deleteVermerk").click(function () {
        $('#deleteVermerkModal').modal('show');
    });

    $("#deleteVermerkExecute").click(function () {
        $('.modal-backdrop').remove();
        $(document.body).removeClass('modal-open');
        let vermerkUntergruppenID = <?php echo json_encode(filter_input(INPUT_GET, 'vermerkUntergruppenID')); ?>;
        $.ajax({
            url: "deleteVermerk.php",
            data: {"vermerkID": vermerkID},
            type: "GET",
            success: function (data) {
                alert(data);
                $.ajax({
                    url: "getVermerkeToUntergruppe.php",        // Neu Laden der Vermerkliste
                    data: {"vermerkUntergruppenID": vermerkUntergruppenID, "vermerkGruppenID": vermerkGruppenID},
                    type: "GET",
                    success: function (data) {
                        $("#vermerke").html(data);
                        document.getElementById('pdfPreview').src += '';                        // Neu laden der PDF-Vorschau
                    }
                });
            },
            error: function (data) {
                alert("Lol, hätteste gern.\nGeht aber nich... \nFrag den Jakob. \n", data);
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
                    xhttp.send(formData);
                };
            };
        }
    });
</script>

