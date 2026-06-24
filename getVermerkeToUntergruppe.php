<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$vermerkUntergruppenID = getPostInt('vermerkUntergruppenID');
$mysqli = utils_connect_sql();
$sql = "SELECT
    v.idtabelle_Vermerke,
    v.tabelle_lose_extern_idtabelle_Lose_Extern,
    v.Ersteller,
    v.Erstellungszeit,
    v.Vermerktext,
    v.Bearbeitungsstatus,
    v.Vermerkart,
    v.Faelligkeit,
    GROUP_CONCAT(r.idTABELLE_Räume ORDER BY r.Raumnr SEPARATOR ', ') as RaumIDs,
GROUP_CONCAT(     CONCAT_WS(' - ', r.Raumnr, r.Raumbezeichnung)
    ORDER BY r.Raumnr SEPARATOR ', '
) as Raumnummern,
le.LosNr_Extern 
FROM tabelle_Vermerke v
LEFT JOIN tabelle_vermerke_has_tabelle_räume v2r ON v.idtabelle_Vermerke = v2r.tabelle_vermerke_idTabelle_vermerke
LEFT JOIN tabelle_räume r ON v2r.tabelle_räume_idTabelle_räume = r.idTABELLE_Räume
LEFT JOIN tabelle_lose_extern le ON v.tabelle_lose_extern_idtabelle_Lose_Extern = le.idtabelle_Lose_Extern
WHERE v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = ?
GROUP BY v.idtabelle_Vermerke 
ORDER BY v.Erstellungszeit";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $vermerkUntergruppenID);
$stmt->execute();
$result = $stmt->get_result();

// Preload images for all vermerke in one query to avoid N+1
$allVermerke = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$vermerkIds = array_column($allVermerke, 'idtabelle_Vermerke');
$imagesByVermerk = [];

if (!empty($vermerkIds)) {
    $placeholders = implode(',', array_fill(0, count($vermerkIds), '?'));
    $types = str_repeat('i', count($vermerkIds));
    $imgStmt = $mysqli->prepare("
        SELECT f.idtabelle_Files, f.Name, fv.tabelle_Vermerke_idtabelle_Vermerke
        FROM tabelle_Files f
        INNER JOIN tabelle_Files_has_tabelle_Vermerke fv ON f.idtabelle_Files = fv.tabelle_Files_idtabelle_Files
        WHERE fv.tabelle_Vermerke_idtabelle_Vermerke IN ($placeholders)
        ORDER BY f.Timestamp ASC
    ");
    $imgStmt->bind_param($types, ...$vermerkIds);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();
    while ($img = $imgResult->fetch_assoc()) {
        $imagesByVermerk[$img['tabelle_Vermerke_idtabelle_Vermerke']][] = $img;
    }
    $imgStmt->close();
}

// ── Spaltenstruktur ──────────────────────────────────────────────────────────
// 0  ID            (hidden)
// 1  Buttons       (Bearbeiten / Upload / Link)
// 2  Plaintext     (hidden, für DataTables rowData[2] + data-plain-text)
// 3  Vermerk       (nur Text)
// 4  Bilder        (Galerie + Upload/Link-Buttons)
// 5  Ersteller
// 6  Fälligkeit
// 7  Erstellt am
// 8  Status        (hidden)
// 9  Vermerkart
// 10 Zuständigkeit
// 11 LosID         (hidden)
// 12 RaumID        (hidden)
// 13 Los
// 14 RaumNr
// ────────────────────────────────────────────────────────────────────────────

echo "<body>";
echo "<table class='table table-striped table-bordered table-responsive border border-light border-5' id='tableVermerke'>
        <thead><tr>
        <th>ID</th>
        <th></th>
        <th>VermerkText-plain</th>
        <th>Vermerk</th>
        <th>Bilder</th>
        <th>Ersteller</th>
        <th>Fälligkeit</th>
        <th>Erstellt am</th>
        <th>Status</th>
        <th>Vermerkart</th>
        <th>Zuständigkeit</th>
        <th>LosID</th>
        <th>RaumID</th>
        <th>Los</th>
        <th>Raum Nr</th>
        </tr></thead><tbody>";

foreach ($allVermerke as $row) {
    $vid        = (int)$row['idtabelle_Vermerke'];
    $images     = $imagesByVermerk[$vid] ?? [];
    $plainText  = $row['Vermerktext'];
    $imageCount = count($images);

    // ── Galerie-Spalte: Bilder + Upload/Link-Buttons ─────────────────────────
    $galleryHtml = "<div id='vermerkGallery{$vid}' class='d-flex flex-wrap gap-1'>";
    foreach ($images as $img) {
        $imgName = htmlspecialchars($img['Name']);
        $imgID   = (int)$img['idtabelle_Files'];
        $galleryHtml .= "
            <div class='position-relative vermerk-img-wrapper' style='display:inline-block;'>
                <button type='button'
                        class='btn btn-danger btn-sm position-absolute top-0 end-0 vermerk-unlink-img'
                        data-image-id='{$imgID}' data-vermerk-id='{$vid}'
                        title='Verknüpfung entfernen'
                        style='z-index:10; padding:1px 5px; font-size:0.65rem; opacity:0.85;'>
                    <i class='fas fa-times'></i>
                </button>
                <img src='https://limet-rb.com/Dokumente_RB/Images/{$imgName}'
                     class='vermerk-gallery-img rounded border'
                     style='height:70px; width:70px; object-fit:cover; cursor:zoom-in;' alt='Bild'>
            </div>";
    }
    $galleryHtml .= "</div>";

    $imagesBadge = "";

    $uploadLinkBtns = "
        <div class='d-flex gap-1 mt-1'>
            <button type='button' class='btn btn-outline-secondary btn-sm vermerk-add-img'
                    data-vermerk-id='{$vid}' title='Neues Bild hochladen'>
                <i class='fas fa-upload'></i>
            </button>
            <button type='button' class='btn btn-outline-secondary btn-sm vermerk-link-img'
                    data-vermerk-id='{$vid}' title='Bild aus Galerie zuordnen'>
                <i class='fas fa-link'></i>
            </button>
        </div>";

    $bildCell = $galleryHtml;

    // ── Text-Spalte: nur Vermerktext ─────────────────────────────────────────
    $textCell = "<div class='vermerk-text-display'>" . nl2br(htmlspecialchars($plainText)) . "</div>";

    $plainTextAttr = htmlspecialchars($plainText, ENT_QUOTES);

    echo "<tr>";
    echo "<td>{$vid}</td>";                                              // 0 ID
    echo "<td>
        <div class='d-flex flex-column gap-1'>
            <button type='button' id='{$vid}'
                    class='btn btn-outline-dark btn-sm'
                    value='changeVermerk'
                    data-plain-text='{$plainTextAttr}'> 
                <i class='fas fa-pencil-alt'></i>
            </button>
            {$uploadLinkBtns}
        </div>
      </td>";                                        // 1 Buttons
    echo "<td></td>";                                                    // 2 Plaintext-Platzhalter (hidden)
    echo "<td id='vermerktText{$vid}'>{$textCell}</td>";                 // 3 Vermerktext
    echo "<td id='vermerkBilder{$vid}'>{$bildCell}</td>";                // 4 Bilder
    echo "<td>" . htmlspecialchars($row['Ersteller']) . "</td>";         // 5 Ersteller
    if ($row["Vermerkart"] != "Info") {                                  // 6 Fälligkeit
        echo "<td id='faelligkeit{$vid}' value='" . htmlspecialchars($row['Faelligkeit'] ?? '') . "'>"
            . htmlspecialchars($row['Faelligkeit'] ?? '') . "</td>";
    } else {
        echo "<td></td>";
    }
    echo "<td>" . htmlspecialchars($row['Erstellungszeit']) . "</td>";   // 7 Erstellt am
    echo "<td id='bearbeitungsstatus{$vid}' value='" . (int)$row['Bearbeitungsstatus'] . "'>"
        . (int)$row['Bearbeitungsstatus'] . "</td>";                      // 8 Status (hidden)
    echo "<td id='vermerkTyp{$vid}' value='" . htmlspecialchars($row['Vermerkart']) . "'>"
        . htmlspecialchars($row['Vermerkart']) . "</td>";                  // 9 Vermerkart
    echo "<td><button type='button' id={$vid} class='btn btn-outline-dark btn-sm'
               value='showVermerkZustaendigkeit'
               data-bs-toggle='modal' data-bs-target='#showVermerkZustaendigkeitModal'>
               <i class='fas fa-users'></i></button></td>";               // 10 Zuständigkeit
    echo "<td id='lot{$vid}' value='" . (int)($row['tabelle_lose_extern_idtabelle_Lose_Extern'] ?? 0) . "'>"
        . (int)($row['tabelle_lose_extern_idtabelle_Lose_Extern'] ?? 0) . "</td>"; // 11 LosID (hidden)
    echo "<td id='rooms{$vid}'>" . htmlspecialchars($row['RaumIDs'] ?? '') . "</td>"; // 12 RaumID (hidden)
    echo "<td>" . htmlspecialchars($row['LosNr_Extern'] ?? '') . "</td>";     // 13 Los
    echo "<td>" . htmlspecialchars($row['Raumnummern'] ?? '') . "</td>";      // 14 RaumNr
    echo "</tr>";
}
echo "</tbody></table>";
?>


<div class='modal fade' id='changeVermerkModal' role='dialog' tabindex="-1">
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
                                        <select class='form-control form-control-sm' id='room' name='room[]' multiple>
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
                    echo "</select></div>";
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
                            <option value='Info'>Info</option>
                            <option value='Bearbeitung'>Bearbeitung</option>
                            <option value='Nutzerwunsch'>Nutzerwunsch</option>
                            <option value='Freigegeben'>Freigegeben</option>
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
                    <div class="form-group"></div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addVermerk' class='btn btn-success btn-sm' value='Hinzufügen'
                       data-bs-dismiss='modal'>
                <input type='button' id='saveVermerk' class='btn btn-warning btn-sm' value='Speichern'
                       data-bs-dismiss='modal'>
                <input type='button' id='deleteVermerk' class='btn btn-danger btn-sm' value='Löschen'>
                <button type='button' class='btn btn-close btn-sm' data-bs-dismiss='modal'></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Zustaendigkeit -->
<div class='modal fade' id='showVermerkZustaendigkeitModal' tabindex="-1"
     aria-labelledby="showVermerkZustaendigkeitModallabel" aria-modal="true" role="dialog"
     data-bs-keyboard="true">
    <div class='modal-dialog modal-xl'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Zustaendigkeiten</h4>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label="Close"></button>
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
                <small style="float:right; font-style:italic; font-family:cursive,'Comic Sans MS','Brush Script MT',serif;">
                    Fehlt eine Person? Bei Projektbeteiligte anlegen.
                </small>
                <button type='button' class='btn btn-secondary btn-sm' value='closeModal' data-bs-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Löschen von Vermerken -->
<div class="modal fade" id="deleteVermerkModal" tabindex="-1" aria-labelledby="deleteVermerkModalLabel"
     data-bs-keyboard="true" aria-modal="true" role="dialog">
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
                <button id="deleteVermerkExecute" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Ja</button>
                <button class="btn btn-success btn-sm" data-bs-dismiss="modal">Nein</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Bild-Upload pro Vermerk -->
<div class="modal fade" id="uploadVermerkImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-1"></i> Bild hochladen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="uploadVermerkID" value="0">
                <div id="vermerkDropZone" class="border border-2 rounded p-4 text-center text-muted mb-3"
                     style="border-style:dashed !important; cursor:pointer;">
                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i><br>
                    Datei hierher ziehen oder <u>klicken zum Auswählen</u>
                </div>
                <input type="file" id="vermerkImageFileInput" accept="image/*" class="d-none">
                <div id="vermerkUploadPreviewWrapper" class="d-none text-center">
                    <img id="vermerkUploadPreview" class="img-fluid rounded" style="max-height:200px;" alt="Vorschau">
                    <p id="vermerkUploadFileName" class="text-muted small mt-1"></p>
                </div>
                <div id="vermerkUploadProgress" class="d-none">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-dark" style="width:100%"></div>
                    </div>
                    <p class="text-center text-muted small mt-1">Wird hochgeladen...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="vermerkUploadConfirmBtn" class="btn btn-dark btn-sm" disabled>
                    <i class="fas fa-upload me-1"></i> Hochladen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Löschen-Bestätigungs-Modal -->
<div class="modal fade" id="vermerkDeleteConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Verknüpfung entfernen?</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-1 text-muted small">
                Das Bild wird vom Vermerk entfernt, bleibt aber in der Projektgalerie erhalten.
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="vermerkConfirmDeleteBtn" class="btn btn-danger btn-sm">
                    <i class="fas fa-unlink me-1"></i> Entfernen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Galerie-Picker Modal -->
<div class="modal fade" id="linkImageToVermerkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-link me-1"></i> Bild aus Galerie zuordnen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="linkTargetVermerkID" value="0">
                <p class="text-muted small mb-3">Bild anklicken um es diesem Vermerk zuzuordnen.</p>
                <div id="galleryPickerGrid" class="d-flex flex-wrap gap-2">
                    <div class="text-muted fst-italic">Wird geladen...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<script charset="utf-8" type="text/javascript">
    /* Initiation within DocumentationV2.php; also: var vermerkID; */
    vermerkGruppenID      = <?php echo json_encode(filter_input(INPUT_POST, 'vermerkGruppenID')); ?>;
    vermerkUntergruppenID = <?php echo json_encode(filter_input(INPUT_POST, 'vermerkUntergruppenID')); ?>;

    $(document).ready(function () {
        $('#changeVermerkModal select').select2({
            width: '100%', placeholder: 'Bitte auswählen...', allowClear: true,
            dropdownParent: $('#changeVermerkModal')
        });
        $('#room').select2({
            multiple: true, width: '100%', placeholder: 'Raum auswählen...', allowClear: true,
            dropdownParent: $('#changeVermerkModal')
        });

        document.getElementById("buttonNewVermerk").style.visibility = "visible";
        document.getElementById("buttonNewVermerkuntergruppe").style.visibility = "visible";
        $('#topDivSearch2').remove();
        $('#showVermerkZustaendigkeitModal').appendTo('body');
        $('#uploadVermerkImageModal').appendTo('body');
        $('#vermerkDeleteConfirmModal').appendTo('body');
        $('#linkImageToVermerkModal').appendTo('body');

        // ── Viewer.js für alle Vermerk-Galerien ──────────────────────────────
        document.querySelectorAll('[id^="vermerkGallery"]').forEach(function (el) {
            initViewer(el, 'vermerk-gallery-img');
        });

        // ── DataTables ────────────────────────────────────────────────────────
        // Spaltenindizes: 0=ID 1=Btn 2=Plaintext 3=Text 4=Bilder 5=Ersteller
        //                 6=Fälligkeit 7=ErstelltAm 8=Status 9=Art 10=Zust
        //                 11=LosID 12=RaumID 13=Los 14=RaumNr
        tableVermerke = new DataTable('#tableVermerke', {
            columnDefs: [
                {targets: [0, 2, 8, 11, 12], visible: false, searchable: false},
                {targets: [1, 4],             visible: true,  searchable: false, orderable: false}
            ],
            responsive: true,
            paging: true,
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 20,
            searching: true,
            info: false,
            order: [[7, 'asc']],
            compact: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            dom: '<"#topDiv.top-container d-flex"<"col-md-6 justify-content-start"><"#topDivSearch2.col-md-6"f>>t<"bottom d-flex" <"col-md-6 justify-content-start"i><"col-md-6 d-flex align-items-center justify-content-end"lp>>',
            rowCallback: function (row, data) {
                if (data[9] === "Bearbeitung") {
                    row.style.backgroundColor = data[8] === "0" ? '#ff8080' : '#b8dc6f';
                } else {
                    row.style.backgroundColor = '#d3edf8';
                }
            },
            initComplete: function () {
                $('#topDivSearch2 label').remove();
                $('#topDivSearch2').removeClass("col-md-6").children().children().removeClass("form-control form-control-sm");
                $('#topDivSearch2').appendTo('#CardHeaderVermerUntergruppen').children().children().addClass("btn btn-sm btn-outline-dark");

                // Edit Vermerk
                $("#tableVermerke tbody").on('click', "button[value='changeVermerk']", function () {
                    let rowData = tableVermerke.row($(this).closest('tr')).data();
                    vermerkID = rowData[0];
                    $('#vermerkStatus').val(rowData[8]).trigger('change');
                    $('#vermerkText').val($(this).data('plain-text') || '');
                    $('#faelligkeit').val(rowData[6]);
                    let typ = rowData[9];
                    $("#vermerkTyp").val(typ).trigger('change');
                    $("#faelligkeit").prop('disabled', typ !== "Bearbeitung" && typ !== "B");
                    $('#los').val(rowData[11] || 0).trigger('change');
                    let roomArray = (rowData[12] || '').split(',').map(id => id.trim());
                    $('#room').val(roomArray).trigger('change');
                    $('#changeVermerkModal').modal('show');
                });
            }
        });

        // ── Zuständigkeit ─────────────────────────────────────────────────────
        $('#tableVermerke tbody').on('click', "button[value='showVermerkZustaendigkeit']", function () {
            let id = this.id;
            $.ajax({
                url: "getVermerkZustaendigkeiten.php", type: "POST", data: {"vermerkID": id},
                success: function (data) {
                    $("#vermerkZustaendigkeit").html(data);
                    $.ajax({
                        url: "getPossibleVermerkZustaendigkeiten.php", type: "POST", data: {"vermerkID": id},
                        success: function (data) { $("#possibleVermerkZustaendigkeit").html(data); }
                    });
                }
            });
        });

        $('#faelligkeit').datepicker({
            format: "yyyy-mm-dd", calendarWeeks: true, autoclose: true, todayBtn: "linked", language: "de"
        });

        // ── Upload: Bild pro Vermerk ──────────────────────────────────────────
        $('#tableVermerke tbody').on('click', '.vermerk-add-img', function () {
            let vid = $(this).data('vermerk-id');
            $('#uploadVermerkID').val(vid);
            document.getElementById('vermerkImageFileInput').value = '';
            document.getElementById('vermerkUploadPreviewWrapper').classList.add('d-none');
            document.getElementById('vermerkUploadProgress').classList.add('d-none');
            document.getElementById('vermerkUploadConfirmBtn').disabled = true;
            document.getElementById('vermerkDropZone').style.display = '';
            $('#uploadVermerkImageModal').modal('show');
        });

        $('#uploadVermerkImageModal').on('hidden.bs.modal', function () {
            document.getElementById('vermerkImageFileInput').value = '';
            document.getElementById('vermerkUploadPreviewWrapper').classList.add('d-none');
            document.getElementById('vermerkUploadProgress').classList.add('d-none');
            document.getElementById('vermerkUploadConfirmBtn').disabled = true;
        });

        const vermerkDropZone = document.getElementById('vermerkDropZone');
        vermerkDropZone.addEventListener('click', () => document.getElementById('vermerkImageFileInput').click());
        vermerkDropZone.addEventListener('dragover', e => { e.preventDefault(); vermerkDropZone.classList.add('bg-light'); });
        vermerkDropZone.addEventListener('dragleave', () => vermerkDropZone.classList.remove('bg-light'));
        vermerkDropZone.addEventListener('drop', e => {
            e.preventDefault(); vermerkDropZone.classList.remove('bg-light');
            if (e.dataTransfer.files[0]) _handleVermerkFile(e.dataTransfer.files[0]);
        });
        document.getElementById('vermerkImageFileInput').addEventListener('change', function () {
            if (this.files[0]) _handleVermerkFile(this.files[0]);
        });

        function _handleVermerkFile(file) {
            document.getElementById('vermerkUploadFileName').textContent = file.name;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('vermerkUploadPreview').src = e.target.result;
                document.getElementById('vermerkUploadPreviewWrapper').classList.remove('d-none');
                document.getElementById('vermerkUploadConfirmBtn').disabled = false;
                document.getElementById('vermerkDropZone').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        $('#vermerkUploadConfirmBtn').on('click', function () {
            const previewSrc = document.getElementById('vermerkUploadPreview').src;
            if (!previewSrc || previewSrc === window.location.href) return;
            const vid = $('#uploadVermerkID').val();
            const img = new Image();
            img.src = previewSrc;
            img.onload = function () {
                const canvas = document.createElement('canvas');
                const scale  = 800 / img.width;
                canvas.width  = 800;
                canvas.height = img.height * scale;
                canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                const encoded = canvas.toDataURL('image/jpeg', 0.9);
                document.getElementById('vermerkUploadProgress').classList.remove('d-none');
                document.getElementById('vermerkUploadConfirmBtn').disabled = true;
                $.ajax({
                    url: 'uploadFileImage.php', type: 'POST',
                    data: {fileUpload: encoded, vermerkID: vid},
                    success: function (raw) {
                        $('#uploadVermerkImageModal').modal('hide');
                        const res = parseResponse(raw);
                        if (res.status === 'ok') {
                            reloadVermerkImages(vid);
                            if (typeof reloadProjectGallery === 'function') reloadProjectGallery();
                            makeToaster('Bild erfolgreich hochgeladen!', true);
                        } else {
                            makeToaster('Fehler: ' + (res.msg || raw), false);
                            document.getElementById('vermerkUploadProgress').classList.add('d-none');
                            document.getElementById('vermerkUploadConfirmBtn').disabled = false;
                        }
                    },
                    error: function () {
                        makeToaster('Upload fehlgeschlagen!', false);
                        document.getElementById('vermerkUploadProgress').classList.add('d-none');
                        document.getElementById('vermerkUploadConfirmBtn').disabled = false;
                    }
                });
            };
        });

        // ── Verknüpfung entfernen (rotes X) ──────────────────────────────────
        let pendingDeleteImageID  = null;
        let pendingDeleteVermerkID = null;

        $(document).on('click', '.vermerk-unlink-img', function (e) {
            e.stopPropagation();
            pendingDeleteImageID   = $(this).data('image-id');
            pendingDeleteVermerkID = $(this).data('vermerk-id');
            $('#vermerkDeleteConfirmModal').modal('show');
        });

        $('#vermerkConfirmDeleteBtn').on('click', function () {
            if (!pendingDeleteImageID) return;
            const imageID = pendingDeleteImageID;
            const vid     = pendingDeleteVermerkID;
            $('#vermerkDeleteConfirmModal').modal('hide');
            // FIX B3: unlinkImageFromVermerk.php now returns JSON
            $.ajax({
                url: 'unlinkImageFromVermerk.php', type: 'POST',
                data: {imageID: imageID, vermerkID: vid},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status === 'ok') {
                        reloadVermerkImages(vid);
                        makeToaster('Bild vom Vermerk entfernt.', true);
                    } else {
                        makeToaster('Fehler: ' + (res.msg || raw), false);
                    }
                },
                error: () => makeToaster('Entfernen fehlgeschlagen!', false)
            });
        });

        $('#vermerkDeleteConfirmModal').on('shown.bs.modal', () => $('#vermerkConfirmDeleteBtn').focus());
        $('#vermerkDeleteConfirmModal').on('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); $('#vermerkConfirmDeleteBtn').trigger('click'); }
        });

        // ── Aus Galerie zuordnen ──────────────────────────────────────────────
        $('#tableVermerke tbody').on('click', '.vermerk-link-img', function () {
            const vid = $(this).data('vermerk-id');
            $('#linkTargetVermerkID').val(vid);
            $('#galleryPickerGrid').html('<div class="text-muted fst-italic">Wird geladen...</div>');
            $('#linkImageToVermerkModal').modal('show');
            $.ajax({
                url: 'img_support/getProjectImages.php', type: 'POST',
                success: function (raw) {
                    const images = parseResponse(raw);
                    if (!Array.isArray(images) || !images.length) {
                        $('#galleryPickerGrid').html('<p class="text-muted fst-italic">Noch keine Bilder in der Projektgalerie.</p>');
                        return;
                    }
                    $('#galleryPickerGrid').html(images.map(img => `
                        <div class="position-relative gallery-pick-item" style="cursor:pointer; display:inline-block;"
                             data-image-id="${img.idtabelle_Files}" title="Diesem Vermerk zuordnen">
                            <img src="https://limet-rb.com/Dokumente_RB/Images/${img.Name}"
                                 class="rounded border" style="height:100px; width:100px; object-fit:cover;">
                            <div class="position-absolute bottom-0 start-0 end-0 text-center"
                                 style="background:rgba(0,0,0,0.45); color:#fff; font-size:0.6rem; padding:2px; border-radius:0 0 4px 4px;">
                                Zuordnen
                            </div>
                        </div>`).join(''));
                }
            });
        });

        // FIX B3: linkImageToVermerk.php now returns JSON
        $(document).on('click', '.gallery-pick-item', function () {
            const imageID  = $(this).data('image-id');
            const vid      = $('#linkTargetVermerkID').val();
            const $item    = $(this);
            $.ajax({
                url: 'linkImageToVermerk.php', type: 'POST',
                data: {imageID: imageID, vermerkID: vid},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status === 'ok') {
                        makeToaster('Bild zugeordnet!', true);
                        reloadVermerkImages(vid);
                        $item.find('img').addClass('border-success border-3').delay(1500).queue(function (next) {
                            $(this).removeClass('border-success border-3'); next();
                        });
                    } else if (res.status === 'already_linked') {
                        makeToaster('Bild ist bereits zugeordnet.', false);
                    } else {
                        makeToaster('Fehler: ' + (res.msg || ''), false);
                    }
                },
                error: () => makeToaster('Zuordnung fehlgeschlagen!', false)
            });
        });

    }); // end document.ready


    // ── Thumbnails für einen Vermerk neu laden ────────────────────────────────
    // Galerie lebt jetzt in einer eigenen <td id="vermerkBilder{vid}">
    function reloadVermerkImages(vid) {
        $.ajax({
            url: 'getImagesForVermerk.php', type: 'POST',
            data: {vermerkID: vid},
            success: function (html) {
                // Neue Galerie aus Response extrahieren
                const tmp = document.createElement('div');
                tmp.innerHTML = html || `<div id="vermerkGallery${vid}" class="d-flex flex-wrap gap-1"></div>`;
                const newGallery = tmp.firstChild;

                // Bestehende Galerie ersetzen
                const oldGallery = document.getElementById('vermerkGallery' + vid);
                if (oldGallery) oldGallery.replaceWith(newGallery);


                // Viewer.js neu init für diese Galerie
                if (newGallery) initViewer(newGallery, 'vermerk-gallery-img');
            }
        });
    }


    // ── Buttons NewVermerk ────────────────────────────────────────────────────
    $("#buttonNewVermerk").click(function () {
        document.getElementById("saveVermerk").style.display   = "none";
        document.getElementById("deleteVermerk").style.display = "none";
        document.getElementById("addVermerk").style.display    = "inline";
        $('#deleteVermerkModal').modal('hide');
        $('#changeVermerkModal').modal('show');
    });

    $("#addVermerk").click(function () {
        let rooms           = $("#room").val();
        let los             = $("#los").val();
        let vermerkStatus   = $("#vermerkStatus").val();
        let vermerkTyp      = $("#vermerkTyp").val();
        let vermerkText     = $("#vermerkText").val();
        let faelligkeitDatum = $("#faelligkeit").val();
        if (vermerkTyp === "Info") faelligkeitDatum = null;
        if (rooms !== "" && los !== "" && vermerkStatus !== "" && vermerkTyp !== "" && vermerkText !== "") {
            $('#changeVermerkModal').modal('hide');
            $.ajax({
                url: "addVermerk.php",
                data: {"untergruppenID": vermerkUntergruppenID, "room": rooms, "los": los,
                    "vermerkStatus": vermerkStatus, "vermerkTyp": vermerkTyp,
                    "vermerkText": vermerkText, "faelligkeitDatum": faelligkeitDatum},
                type: "POST",
                success: function (data) {
                    makeToaster(data, true);
                    $.ajax({
                        url: "getVermerkeToUntergruppe.php",
                        data: {"vermerkUntergruppenID": vermerkUntergruppenID, "vermerkGruppenID": vermerkGruppenID},
                        type: "POST",
                        success: function (data) { $("#vermerke").html(data); document.getElementById('pdfPreview').src += ''; }
                    });
                }
            });
        } else {
            makeToaster("Bitte alle Felder ausfüllen!", false);
            $('#changeVermerkModal').modal('hide');
        }
    });

    $('#vermerkTyp').change(function () {
        $("#faelligkeit").prop('disabled', $(this).val() !== "Bearbeitung");
    });

    $("button[value='changeVermerk']").click(function () {
        document.getElementById("saveVermerk").style.display   = "inline";
        document.getElementById("deleteVermerk").style.display = "inline";
        document.getElementById("addVermerk").style.display    = "none";
        let rowData = tableVermerke.row($(this).closest('tr')).data();
        $('#vermerkStatus').val(rowData[8]).trigger('change');
        $('#changeVermerkModal').modal('show');
    });

    $("#saveVermerk").click(function () {
        let rooms            = $("#room").val();
        let los              = $("#los").val();
        let vermerkStatus    = $("#vermerkStatus").val();
        let vermerkTyp       = $("#vermerkTyp").val();
        let vermerkText      = $("#vermerkText").val();
        let faelligkeitDatum = $("#faelligkeit").val();
        if (vermerkTyp === "Info") faelligkeitDatum = null;
        if (rooms !== "" && los !== "" && vermerkStatus !== "" && vermerkTyp !== "" && vermerkText !== "" && vermerkTyp !== null) {
            $('#changeVermerkModal').modal('hide');
            $.ajax({
                url: "saveVermerk.php",
                data: {"vermerkID": vermerkID, "room": rooms, "los": los,
                    "vermerkStatus": vermerkStatus, "vermerkTyp": vermerkTyp,
                    "vermerkText": vermerkText, "faelligkeitDatum": faelligkeitDatum,
                    "untergruppenID": vermerkUntergruppenID},
                type: "POST",
                success: function (data) {
                    makeToaster(data, true);
                    $.ajax({
                        url: "getVermerkeToUntergruppe.php",
                        data: {"vermerkUntergruppenID": vermerkUntergruppenID, "vermerkGruppenID": vermerkGruppenID},
                        type: "POST",
                        success: function (data) { $("#vermerke").html(data); document.getElementById('pdfPreview').src += ''; }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });

    $("#deleteVermerk").click(function () { $('#deleteVermerkModal').modal('show'); });

    $("#deleteVermerkExecute").click(function () {
        $('.modal-backdrop').remove();
        $(document.body).removeClass('modal-open');
        $.ajax({
            url: "deleteVermerk.php", data: {"vermerkID": vermerkID}, type: "POST",
            success: function (data) {
                alert(data);
                $.ajax({
                    url: "getVermerkeToUntergruppe.php",
                    data: {"vermerkUntergruppenID": vermerkUntergruppenID, "vermerkGruppenID": vermerkGruppenID},
                    type: "POST",
                    success: function (data) { $("#vermerke").html(data); document.getElementById('pdfPreview').src += ''; }
                });
            },
            error: function (data) { alert("Frag 1 Dev.", data); }
        });
    });

</script>