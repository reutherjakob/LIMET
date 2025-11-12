let vermerkeTable;


function getVermerke() {
    //console.log("Getting Vermerke.");
    try {
        if ($.fn.dataTable.isDataTable('#vermerkeTable')) {
            vermerkeTable.ajax.reload();
        } else {
            vermerkeTable = $('#vermerke').html('<table id="vermerkeTable" class="table table-striped table-bordered" style="width:100%"></table>').find('table').DataTable({
                ajax: {
                    url: "../controllers/VermerkeController.php",
                    type: "POST",
                    data: {
                        action: "getVermerkeToGruppe",
                        vermerkgruppe_id: besprechung.id
                    },
                    dataSrc: 'data'
                },
                columns: [
                    {title: "ID", data: "ID", visible: false},
                    {title: "R.Bez.", data: "RBZ"},
                    {
                        title: "Vermerktext",
                        data: "Vermerktext",
                        render: function (data) {
                            return data ? data.replace(/\n/g, '<br>') : '';
                        }
                    },
                    {
                        title: "Edit",
                        data: null,
                        orderable: false,
                        render: function (data, type, row) {
                            return `<button class="btn btn-sm btn-outline-primary editVermerkBtn" data-id="${row.ID}" data-text="${row.Vermerktext}"><i class="fas fa-edit"></i></button>`;
                        }
                    }
                ],
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
                },
                lengthChange: false,
                pageLength: -1,
                searching: false,
                info: false,
                initComplete: function () {
                    setTimeout(() => {
                        $(document).on('click', '.editVermerkBtn', function () {
                            const id = $(this).data('id');
                            let text = $(this).data('text');
                            text = text ? text.replace(/<br\s*\/?>/gi, "\n") : '';
                            $('#editVermerkID').val(id);
                            $('#editVermerkText').val(text);
                            $('#editVermerkModal').modal('show');
                        });
                    }, 300);
                }
            });
        }
    } catch (e) {
        console.log("GetVermerke(): ", e);
    }
}

function addUntergruppePerRaumbereich() {
    const selectedRaumbereiche = $('#raumbereich').val();
    if (!selectedRaumbereiche || selectedRaumbereiche.length === 0 || besprechung.id === 0) return;
    //  console.log("Besprechung ist geöffnet", selectedRaumbereiche, besprechung.id);
    $.ajax({
        url: '../controllers/VermerkuntergruppeController.php',
        method: 'POST',
        data: {
            vermerkgruppe_id: besprechung.id, // pass only vermerkgruppe ID as needed
            raumbereiche: selectedRaumbereiche,  // array of names
            action: "addUntergruppen"
        },
        success: function (response) {
            if (response.success) {
                if (response.created.length > 0) {
                    makeToaster("Neue Untergruppe(n) erstellt: " + response.created.map(c => c.name).join(", "), true);
                }
                if (response.skipped.length > 0) {
                    if (response.created.length === 0) {
                        makeToaster("Gruppe(n) '" + response.skipped.join(", ") + "' existiert/ieren bereits. Erstelle keine Duplikate.", true);
                    } else {
                        console.log("Einige Gruppen existierten bereits und wurden nicht dupliziert:", response.skipped);
                    }
                }
            } else {
                alert("Fehler beim Erstellen der Untergruppe: " + (response.message || "Unbekannter Fehler"));
            }
        },
        error: function () {
            alert("Serverfehler bei der Untergruppenerstellung.");
        }
    });
}

function addDefaultVermerkeForEachRommInArea(vermerkgruppeId, raumbereiche) {
    if (!besprechung.id || !Array.isArray(raumbereiche) || raumbereiche.length === 0) {
        makeToaster("Bitte Vermerkgruppe und mindestens einen Raumbereich wählen .  " + besprechung.id + "   " + raumbereiche, true);
        return;
    }
    $.ajax({
        url: '../controllers/VermerkeController.php',
        method: 'POST',
        data: {
            vermerkgruppe_id: besprechung.id,
            raumbereiche: raumbereiche,
            action: "addVermerkforEachRoom"
        },
        success: function (response) {
            console.log("addVermerkforEachRoom Sucess:", besprechung.roomVermerkMap);
            if (response.success) {
                if (!besprechung.roomVermerkMap) {
                    besprechung.roomVermerkMap = {};
                }
                if (response.addedVermerke && response.addedVermerke.length > 0) {
                    response.addedVermerke.forEach(item => {
                        const roomID = item.raumID;
                        const vermerkID = item.vermerkID;
                        if (!besprechung.roomVermerkMap[roomID]) {
                            besprechung.roomVermerkMap[roomID] = [];
                        }
                        besprechung.roomVermerkMap[roomID].push(vermerkID);
                    });
                }
                if (response.skipped && response.skipped.length > 0) {
                    response.skipped.forEach(item => {
                        const roomID = item.raumID;
                        const vermerkID = item.vermerkID;
                        if (vermerkID && vermerkID !== 0) {
                            if (!besprechung.roomVermerkMap[roomID]) {
                                besprechung.roomVermerkMap[roomID] = [];
                            }
                            if (!besprechung.roomVermerkMap[roomID].includes(vermerkID)) {
                                besprechung.roomVermerkMap[roomID].push(vermerkID);
                            }
                        }
                    });
                }
                console.log("addVermerkforEachRoom Sucess:", besprechung.roomVermerkMap);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX error:", status, error);
        }
    });
}


$('#editVermerkForm').on('submit', function (e) {
    e.preventDefault();
    const id = $('#editVermerkID').val();
    const newText = $('#editVermerkText').val();

    $.ajax({
        url: '../controllers/VermerkeController.php',
        type: 'POST',
        data: {
            action: 'updateVermerkText',
            idtabelle_Vermerke: id,
            Vermerktext: newText
        },
        success: function (response) {
            if (response.success) {
                $('#editVermerkModal').modal('hide');
                $('#vermerkeTable').DataTable().ajax.reload(null, false);
                alert('Vermerk erfolgreich aktualisiert!');
                setTimeout(function () {
                    refreshPDF();
                }, 500)

            } else {
                alert('Fehler beim Aktualisieren des Vermerks: ' + response.message);
            }
        },
        error: function () {
            alert('Ajax Fehler beim Aktualisieren des VermePivotTableController.phprks.');
        }
    });
});
