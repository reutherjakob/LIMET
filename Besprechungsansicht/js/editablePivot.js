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
        // Klick auf eine bearbeitbare Zelle öffnet das Modal
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('editable-cell')) {
                this.openEditModal(e.target);
            }
        });

        // Eingaben prüfen und Speichern-Button aktivieren/deaktivieren
        ['edit-new-amount', 'edit-change-comment', 'edit-confirm'].forEach(id => {
            document.getElementById(id).addEventListener('input', () => this.validateInputs());
            document.getElementById(id).addEventListener('change', () => this.validateInputs());
        });

        // Änderungen speichern
        document.getElementById('save-element-change').addEventListener('click', () => this.saveChanges());
    }

    openEditModal(cell) {
        this.currentCellData = {
            roomId: cell.dataset.roomId,
            elementId: cell.dataset.elementId,
            variantId: cell.dataset.variantId,
            relationId: cell.dataset.relationId || null,
            currentAmount: cell.textContent.trim()
        };

        this.loadDetails();
    }

    loadDetails() {
        fetch('../controllers/getElementDetails.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                roomId: this.currentCellData.roomId,
                elementId: this.currentCellData.elementId,
                variantId: this.currentCellData.variantId
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
                document.getElementById('edit-change-comment').value = '';
                document.getElementById('edit-confirm').checked = false;
                document.getElementById('save-element-change').disabled = true;

                // Speichere relationId für spätere Verwendung
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

        btnSave.disabled = (!newAmount || !comment || !confirmed);
    }

    saveChanges() {
        if (!confirm('Änderung wirklich speichern?')) return;

        const newAmount = document.getElementById('edit-new-amount').value.trim();
        const changeComment = document.getElementById('edit-change-comment').value.trim();
        const confirmChecked = document.getElementById('edit-confirm').checked;

        if (!newAmount || !changeComment || !confirmChecked) {
            alert('Bitte alle Pflichtfelder ausfüllen und bestätigen.');
            return;
        }

        const formData = new URLSearchParams({
            relationId: this.currentCellData.relationId || '',
            roomId: this.currentCellData.roomId,
            elementId: this.currentCellData.elementId,
            variantId: this.currentCellData.variantId,
            newAmount: newAmount,
            changeComment: changeComment,
            status: 0,
            standort: 0,
            neuBestand: 1
        });

        fetch('../controllers/updateElementAmount.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: formData.toString()
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Änderung erfolgreich gespeichert');
                    bootstrap.Modal.getInstance(document.getElementById('editElementModal')).hide();
                    this.reloadPivotTable();
                } else {
                    alert('Fehler: ' + data.message);
                }
            })
            .catch(() => alert('Fehler beim Speichern der Änderung'));
    }

    reloadPivotTable() {
        // Hier je nach Projekt-Setup Pivot neu laden, bspw. mit AJAX oder Neuladen der Seite
        // Beispiel mit AJAX:
        const container = document.getElementById('pivot-container');
        if (!container) return;
        container.innerHTML = '<div class="spinner-border" role="status"></div>';

        const form = document.getElementById('pivot-filter-form') ?? null;
        let params = '';
        if (form) {
            params = new URLSearchParams(new FormData(form)).toString();
        }
        fetch('../controllers/PivotTableController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: params
        })
            .then(res => res.text())
            .then(html => container.innerHTML = html)
            .catch(() => container.innerHTML = '<div class="alert alert-danger">Fehler beim Laden der Pivot-Tabelle</div>');
    }
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    window.editablePivot = new EditablePivot();
});
