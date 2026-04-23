// ══════════════════════════════════════════════════════════════════
// API-Endpunkte & globale Konstanten
// ══════════════════════════════════════════════════════════════════

const API_CHECK_ROOMS = '../IFCQuest/api_check_rooms.php';
const API_COMPARE     = '../IFCQuest/api_compare_elements.php';
const API_SYNC        = '../IFCQuest/api_sync_elements.php';
const PREVIEW_ROWS    = 50;

// ══════════════════════════════════════════════════════════════════
// Hilfsfunktionen
// ══════════════════════════════════════════════════════════════════

function esc(str) {
    return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function showCard(id) {
    const el = document.getElementById(id);
    el.style.display = 'block';
    el.classList.add('fade-in');
}

function hideCard(id) { document.getElementById(id).style.display = 'none'; }
function hideEl(id)   { document.getElementById(id).style.display = 'none'; }

function alertApiError(xhr) {
    if (xhr.responseJSON?.error) alert('Fehler: ' + xhr.responseJSON.error);
    else alert('Server-Antwort:\n\n' + (xhr.responseText || '').substring(0, 400));
}

function apiPost(url, data) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url, method: 'POST', contentType: 'application/json',
            data: JSON.stringify(data),
            success: resolve,
            error: xhr => reject(new Error(xhr.responseJSON?.error || xhr.responseText || 'API-Fehler')),
        });
    });
}

function copyUnmapped() {
    const badges = document.querySelectorAll('#unmapped-list .badge');
    const text   = [...badges].map(b => `${b.dataset.familie}\t${b.dataset.laenge}`).join('\n');
    navigator.clipboard.writeText(text)
        .then(() => alert('Nicht gemappte Familien in Zwischenablage kopiert.'))
        .catch(() => alert('Kopieren fehlgeschlagen.'));
}
