<div class='modal fade' id='copyRoomModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Raum kopieren &ensp;</h4>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>

                <div class="form-group mb-3">
                    <div class="form-group mb-2">
                        <label for="copyRoom_raumnr"><strong>Raumnummer:</strong></label>
                        <input type="text" class="form-control form-control-sm" id="copyRoom_raumnr"
                               placeholder="Raumnummer der Kopie"/>
                    </div>

                    <label for="copyRoom_funktionsstelle"><strong>Funktionsstelle:</strong></label>
                    <select id="copyRoom_funktionsstelle" class="form-control form-control-sm">
                        <option value="">Funktionsstelle wählen</option>
                        <?php
                        require_once "utils/_utils.php";
                        check_login();

                        $mysqli = utils_connect_sql();
                        $sql = "SELECT tabelle_funktionsteilstellen.Nummer, tabelle_funktionsteilstellen.Bezeichnung AS bez3, tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
                            FROM (tabelle_funktionsteilstellen INNER JOIN tabelle_funktionsstellen ON tabelle_funktionsteilstellen.TABELLE_Funktionsstellen_idTABELLE_Funktionsstellen = tabelle_funktionsstellen.idTABELLE_Funktionsstellen) 
                            INNER JOIN tabelle_funktionsbereiche ON tabelle_funktionsstellen.TABELLE_Funktionsbereiche_idTABELLE_Funktionsbereiche = tabelle_funktionsbereiche.idTABELLE_Funktionsbereiche
                            ORDER BY Nummer;";
                        $result = $mysqli->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['idTABELLE_Funktionsteilstellen'] . "'>" . $row['Nummer'] . " - " . $row['bez3'] . "</option>";
                        }
                        $mysqli->close();
                        ?>
                    </select>
                </div>

                <div class="mb-2"><strong>Parameter kopieren:</strong></div>
                <div class="d-flex flex-wrap gap-1" id="copyRoom_categoryToggles">
                    <button class="btn btn-sm btn-success copy-cat-toggle" data-cat="R">R – Raumangaben</button>
                    <button class="btn btn-sm btn-success copy-cat-toggle" data-cat="HKLS">HKLS</button>
                    <button class="btn btn-sm btn-success copy-cat-toggle" data-cat="ET">ET</button>
                    <button class="btn btn-sm btn-success copy-cat-toggle" data-cat="AR">AR</button>
                    <button class="btn btn-sm btn-success copy-cat-toggle" data-cat="MG">MG</button>
                    <button class="btn btn-sm btn-success copy-cat-toggle" data-cat="LAB">LAB</button>
                    <button class="btn btn-sm btn-success copy-cat-toggle" data-cat="GCP">GCP</button>
                </div>
                <small class="text-muted">Abgewählte Kategorien werden nicht kopiert und auf DB-Default gesetzt.</small>

            </div>
            <div class='modal-footer'>
                <button id='executeCopyRoom' class='btn btn-warning btn-sm'>Kopieren</button>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<script>
    let copyCategories = {};

    function init_copyRoom_modal() {
        copyCategories = Object.fromEntries(
            buttonRanges
                .filter(b => ['R', 'HKLS', 'ET', 'AR', 'MG', 'LAB', 'GCP'].includes(b.name))
                .map(b => [b.name, {start: b.start, end: b.end}])
        );


        $('#copyRoom_funktionsstelle').select2({
            placeholder: "Funktionsstelle wählen",
            dropdownParent: $('#copyRoomModal')
        });

        // Toggle-Buttons: grün = aktiv, rot = inaktiv
        $(document).off('click', '.copy-cat-toggle').on('click', '.copy-cat-toggle', function () {
            $(this).toggleClass('btn-success btn-danger');
        });

        $('#executeCopyRoom').off('click').on('click', function () {
            executeCopyRoom();
        });
    }

    function executeCopyRoom() {
        let selectedRowData = table.row('.selected').data();
        if (!selectedRowData) {
            alert("Kein Raum ausgewählt!");
            return;
        }

        let fstelle = $('#copyRoom_funktionsstelle').val();
        if (!fstelle) {
            alert("Bitte eine Funktionsstelle wählen!");
            return;
        }

        // Aktive Kategorien ermitteln
        let activeCategories = [];
        $('.copy-cat-toggle.btn-success').each(function () {
            activeCategories.push($(this).data('cat'));
        });

        // requestData aufbauen: nur Pflichtfelder + aktive Kategorien
        let requestData = {};


        const alwaysFields = [
            'tabelle_projekte_idTABELLE_Projekte',
            'Raumbezeichnung',
            'MT-relevant'
        ];

        alwaysFields.forEach(f => {
            requestData[f] = selectedRowData[f];
        });

        // Neue Funktionsstelle & Raumnummer setzen
        requestData['TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen'] = fstelle;
        requestData['Raumnr'] = $('#copyRoom_raumnr').val() || selectedRowData['Raumnr'];

        columnsDefinition.forEach((col, idx) => {
            if (idx < 7) return; // interne IDs, bereits oben behandelt

            // Berechnete/nicht-speicherbare Felder überspringen
            if (['element_mask', 'Bezeichnung', 'Nummer'].includes(col.data)) return;

            let catMatch = false;
            activeCategories.forEach(cat => {
                let range = copyCategories[cat];
                if (range && idx >= range.start && idx <= range.end) catMatch = true;
            });

            if (catMatch) {
                requestData[col.data] = selectedRowData[col.data];
            }
        });

        // ID des Originals nicht mitsenden
        delete requestData['idTABELLE_R%C3%A4ume'];
        delete requestData['idTABELLE_Räume'];

        $.ajax({
            url: "addRoom_all.php",
            data: requestData,
            type: "POST",
            success: function (data) {
                $('#copyRoomModal').modal('hide');
                makeToaster("Kopiert: " + data, true);
                window.location.replace("roombookSpecifications_New.php");
            },
            error: function () {
                makeToaster("Fehler beim Kopieren!", false);
            }
        });
    }
</script>