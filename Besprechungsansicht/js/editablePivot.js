class EditablePivot {
    constructor() {
        this.currentCellData = null;
        this.bindEvents();

        const modalElement = document.getElementById('editElementModal');
        modalElement.addEventListener('hidden.bs.modal', () => {
            this.currentCellData = null;
            document.getElementById('edit-new-amount').value = '';
            document.getElementById('edit-change-comment').value = '';
            document.getElementById('edit-confirm').checked = false;
            document.getElementById('save-element-change').disabled = true;
            document.getElementById('edit-raum-info').value = '';
            document.getElementById('edit-element-info').value = '';
            document.getElementById('edit-current-amount').value = '';
        });
    }


    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('editable-cell')) {
                this.openEditModal(e.target);
            }
        });
        ['edit-new-amount', 'edit-change-comment', 'edit-confirm'].forEach(id => {            // Eingaben prüfen und Speichern-Button aktivieren/deaktivieren
            document.getElementById(id).addEventListener('input', () => this.validateInputs());
            document.getElementById(id).addEventListener('change', () => this.validateInputs());
        });
        document.getElementById('save-element-change').addEventListener('click', () => this.saveChanges());
    }

    openEditModal(cell) {
        this.currentCellData = {
            roomId: cell.dataset.roomId,
            elementId: cell.dataset.elementId,
            variantId: cell.dataset.variantId,
            relationId: cell.dataset.relationId || null,
            currentAmount: cell.textContent.trim(),
            bestand: cell.dataset.bestand
        };
        this.loadDetails();
    }

    loadDetails() {
        // consolidateMultipleElementsperRoom();
        fetch('../controllers/PivotTableController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: "getElementDetails",
                roomId: this.currentCellData.roomId,
                elementId: this.currentCellData.elementId,
                variantId: this.currentCellData.variantId,
                bestand:this.currentCellData.bestand
            })
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Fehler beim Laden der Daten.');
                const d = data.data;
                document.getElementById('edit-raum-info').value = `${d.Raumnr} - ${d.Raumbezeichnung}`;
                document.getElementById('edit-element-info').value = `${d.ElementID} ${d.Bezeichnung}`;
                document.getElementById('edit-current-amount').value = d.Anzahl ?? this.currentCellData.currentAmount;
                document.getElementById('edit-new-amount').value = d.Anzahl ?? this.currentCellData.currentAmount;
                document.getElementById('edit-element-comments').value = d.element_comments || '';
                document.getElementById('edit-neuBestand').value = d.neuBestand ?? '1';
                document.getElementById('edit-variant-name').value = d.variantId || '1';
                document.getElementById('edit-change-comment').value = '';
                document.getElementById('edit-confirm').checked = false;
                document.getElementById('save-element-change').disabled = true;

                if (d.relationId === 0) {  //History Modal
                    document.getElementById("show-history-btn").style.display = "none";
                } else {
                    document.getElementById("show-history-btn").style.display = "block";
                    document.getElementById("show-history-btn").value = d.relationId;
                    document.getElementById('ElementName4Header').innerHTML = `${d.ElementID} ${d.Bezeichnung}`;

                }
                this.currentCellData.relationId = d.relationId;
            })
            .catch(err => alert(err.message));
        new bootstrap.Modal(document.getElementById('editElementModal')).show();
    }

    validateInputs() {
        const newAmount = document.getElementById('edit-new-amount').value.trim();
        const comment = document.getElementById('edit-change-comment').value.trim();
        const confirmed = document.getElementById('edit-confirm').checked;
        const btnSave = document.getElementById('save-element-change');
        btnSave.disabled = (!newAmount || !confirmed); //|| !comment
    }

    saveChanges() {
        const newAmount = document.getElementById('edit-new-amount').value.trim();
        const changeComment = document.getElementById('edit-change-comment').value.trim();
        const confirmChecked = document.getElementById('edit-confirm').checked;
        const elementkommentar = document.getElementById('edit-element-comments').value.trim();
        // WORKS: console.log("Elementkomemntar: ", elementkommentar);
        if (!newAmount || !confirmChecked) { // !changeComment ||
            alert('Bitte alle Pflichtfelder ausfüllen und bestätigen.');
            return;
        }

        const varID = document.getElementById('edit-variant-name').value;
        const statuss = document.getElementById('edit-status').value;
        const bestand = document.getElementById("edit-neuBestand").value;
        //const raumkommentar = document.getElementById("edit-room-comments").value.trim();
        // console.log("RaUM: ", this.currentCellData.roomId, "VermerkID Des Raumes:", besprechung.roomVermerkMap[this.currentCellData.roomId], " Var: ", varID);

        const formData = new URLSearchParams({
            relationId: this.currentCellData.relationId || '',
            roomId: this.currentCellData.roomId,
            elementId: this.currentCellData.elementId,
            variantId: varID,
            newAmount: newAmount,
            changeComment: changeComment,
            status: statuss,
            neuBestand: bestand,
            bestand_alt: this.currentCellData.bestand,
            elementKommentar: elementkommentar,
            besprechungsid: besprechung.id,
            vermerkID: besprechung.roomVermerkMap[this.currentCellData.roomId],
            //raumkomemntar: raumkommentar
            // standort: 1,
        });
        console.log("EditTable Form Data: ", formData);
        fetch('../controllers/updateElementRoomVermerk.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: formData.toString()
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    makeToaster('Änderung erfolgreich gespeichert', true);
                    bootstrap.Modal.getInstance(document.getElementById('editElementModal')).hide();

                    this.reloadPivotTable();
                    if (typeof (refreshPDF) === "function") {
                        refreshPDF();
                        if (typeof (getVermerke) === "function") {
                            getVermerke();
                        }
                    }
                } else {
                    alert('Fehler: ' + data.message);
                }
            })
            .catch(() => alert('Fehler beim Speichern der Änderung'));
    }


    reloadPivotTable() {
        loadPivotTable();
    }

}

// Init
document.addEventListener('DOMContentLoaded', () => {
    window.editablePivot = new EditablePivot();
});
