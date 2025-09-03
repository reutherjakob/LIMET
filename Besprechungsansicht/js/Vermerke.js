let vermerkeTable;

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
            console.log("AJAX success callback triggered", response);
            if (response.success) {
                besprechung.roomVermerkMap = {};
                console.log("Initialized roomVermerkMap");
                if (response.addedVermerke && response.addedVermerke.length > 0) {
                    console.log("Processing addedVermerke", response.addedVermerke);
                    response.addedVermerke.forEach(item => {
                        const roomID = item.raumID;
                        const vermerkID = item.vermerkID;
                        if (!besprechung.roomVermerkMap[roomID]) {
                            besprechung.roomVermerkMap[roomID] = [];
                        }
                        besprechung.roomVermerkMap[roomID].push(vermerkID);
                    });
                } else {
                    //console.log("No addedVermerke found");
                }
                if (response.skipped && response.skipped.length > 0) {
                    //     console.log("Processing" , response.skipped);
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
                //else {
                //console.log("No skipped entries found");
                //}
                //console.log("Final roomVermerkMap contents:");
                //for (const [roomID, vermerkIDs] of Object.entries(besprechung.roomVermerkMap)) {
                // console.log(`Room ID: ${roomID}, Vermerk ID: [${vermerkIDs.join(", ")}]`);
                // }
            } else {
                console.log("Response success false:", response);
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
                // reload table data
                $('#vermerkeTable').DataTable().ajax.reload(null, false);
                alert('Vermerk erfolgreich aktualisiert!');
            } else {
                alert('Fehler beim Aktualisieren des Vermerks: ' + response.message);
            }
        },
        error: function () {
            alert('Ajax Fehler beim Aktualisieren des VermePivotTableController.phprks.');
        }
    });
});
