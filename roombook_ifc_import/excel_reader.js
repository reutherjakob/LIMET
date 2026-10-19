// ══════════════════════════════════════════════════════════════════
// State — Excel-Rohdaten
// ══════════════════════════════════════════════════════════════════

let allHeaders = [];   // [{label, idx}]
let parsedRows = [];   // raw data rows

// ══════════════════════════════════════════════════════════════════
// Drop-Zone & Datei-Handling
// ══════════════════════════════════════════════════════════════════

const dropZone = document.getElementById('drop-zone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    handleFile(e.dataTransfer.files[0]);
});
document.getElementById('file-input').addEventListener('change', function () {
    if (this.files[0]) handleFile(this.files[0]);
});

function handleFile(file) {
    if (!/\.(xlsx|xls)$/i.test(file.name)) { alert('Nur .xlsx und .xls Dateien.'); return; }

    const reader = new FileReader();
    reader.onload = e => {
        const wb   = XLSX.read(e.target.result, {type: 'array', cellDates: true});
        const ws   = wb.Sheets[wb.SheetNames[0]];
        const data = XLSX.utils.sheet_to_json(ws, {header: 1, defval: ''});

        allHeaders = (data[0] || []).map((h, i) => ({label: String(h).trim(), idx: i}));
        parsedRows = data.slice(1).filter(r => r.some(c => c !== ''));

        document.getElementById('file-name-display').textContent = '✓ ' + file.name;
        document.getElementById('file-name-display').style.display = 'block';

        buildAllSelects();
        buildParamColIndex();
        renderPreview();
        showCard('mapping-card');
        hideCard('validation-card');
        hideEl('compare-section');
        validationResults = {};
    };
    reader.readAsArrayBuffer(file);
}
