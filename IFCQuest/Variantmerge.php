<?php
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
init_page_serversides("No Redirect");
// TODO this does sth, but not what it should
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Varianten-Konsolidierung</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<body>
<div id="limet-navbar"></div>
<div class="page-header">
    <div class="icon-merge"><i class="fas fa-compress-arrows-alt"></i></div>
    <div>
        <h1>Varianten-Konsolidierung</h1>
        <div class="sub">Doppelte Varianten mit identischem Parameter-Fingerprint zusammenführen</div>
    </div>
</div>

<div class="toolbar">
    <button class="btn-scan" id="btn-scan" onclick="runScan()">
        <i class="fas fa-search"></i> Projekt scannen
    </button>
    <button class="btn-merge-all" id="btn-merge-all" onclick="mergeAll()">
        <i class="fas fa-compress-arrows-alt"></i> Alle zusammenführen
    </button>
    <span class="pill dup" id="pill-count" style="display:none"></span>
    <span class="pill" id="pill-info" style="display:none"></span>
</div>

<div class="main">
    <div id="state-empty">
        <div class="big-icon"><i class="fas fa-layer-group"></i></div>
        <div>Scan starten um doppelte Varianten zu finden</div>
    </div>
    <div id="state-clean">
        <div class="big-icon"><i class="fas fa-check-circle"></i></div>
        <div style="font-size:1.05rem;font-weight:700">Alles sauber!</div>
        <div style="color:var(--muted);margin-top:.5rem;font-size:.82rem">Keine doppelten Varianten gefunden.</div>
    </div>
    <div id="groups-container"></div>
</div>

<div id="toast-wrap"></div>

<script>
    const API = 'api_merge_variants.php';
    let scanData = null;  // last scan result
    let mergedGroups = new Set(); // indices of already-merged groups

    // ─────────────────────────────────────────────
    function esc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function toast(msg, ok = true) {
        const t = document.createElement('div');
        t.className = 'toast-msg ' + (ok ? 'ok' : 'err');
        t.innerHTML = `<i class="fas fa-${ok ? 'check-circle' : 'exclamation-circle'}"></i> ${esc(msg)}`;
        document.getElementById('toast-wrap').appendChild(t);
        setTimeout(() => t.remove(), 4000);
    }

    // ─────────────────────────────────────────────
    async function runScan() {
        const btn = document.getElementById('btn-scan');
        btn.innerHTML = `<span class="spinner"></span> Scanne…`;
        btn.disabled = true;
        mergedGroups.clear();

        try {
            const res = await fetch(`${API}?action=scan`);
            const data = await res.json();
            if (data.error) throw new Error(data.error);
            scanData = data;
            renderResults(data);
        } catch (e) {
            toast('Fehler: ' + e.message, false);
        } finally {
            btn.innerHTML = `<i class="fas fa-search"></i> Erneut scannen`;
            btn.disabled = false;
        }
    }

    function renderResults(data) {
        const groups = data.duplicate_groups ?? [];
        const container = document.getElementById('groups-container');
        const stateEmpty = document.getElementById('state-empty');
        const stateClean = document.getElementById('state-clean');
        const pillCount = document.getElementById('pill-count');
        const pillInfo = document.getElementById('pill-info');
        const btnAll = document.getElementById('btn-merge-all');

        container.innerHTML = '';
        stateEmpty.style.display = 'none';
        stateClean.style.display = 'none';

        if (!groups.length) {
            stateClean.style.display = 'block';
            pillCount.style.display = 'none';
            pillInfo.style.display = 'none';
            btnAll.style.display = 'none';
            return;
        }

        pillCount.textContent = `${groups.length} Duplikat-Gruppe${groups.length !== 1 ? 'n' : ''}`;
        pillCount.style.display = '';
        pillInfo.style.display = 'none';
        btnAll.style.display = 'flex';

        groups.forEach((g, idx) => {
            container.appendChild(buildGroupCard(g, idx));
        });
    }

    function buildGroupCard(g, idx) {
        const card = document.createElement('div');
        card.className = 'group-card';
        card.id = `group-${idx}`;

        // ── Header ──
        const header = document.createElement('div');
        header.className = 'group-header';
        header.innerHTML = `
    <span class="elem-code">${esc(g.elem_code)}</span>
    <span class="elem-name">${esc(g.elem_name)}</span>
    <span class="dup-count">
      <i class="fas fa-clone fa-xs"></i>
      ${g.variants.length} Varianten mit identischem Fingerprint
    </span>`;
        card.appendChild(header);

        // ── Body ──
        const body = document.createElement('div');
        body.className = 'group-body';

        // Variant grid
        const grid = document.createElement('div');
        grid.className = 'variant-grid';
        grid.innerHTML = `
    <div class="vg-head">
      <div>Variante</div>
      <div>Räume (aktiv)</div>
      <div>Parameter-Fingerprint</div>
      <div>Aktion</div>
    </div>`;

        g.variants.forEach(v => {
            const row = document.createElement('div');
            row.className = 'vg-row ' + (v.is_keep ? 'is-keep' : 'is-drop');

            const badgeCls = v.is_keep ? 'keep' : 'drop';
            const anzahlCls = v.total_anzahl > 0 ? 'has' : '';
            const actionHtml = v.is_keep
                ? `<span style="color:var(--success);font-size:.72rem"><i class="fas fa-check me-1"></i>behalten</span>`
                : `<span style="color:var(--danger);font-size:.72rem"><i class="fas fa-trash me-1"></i>entfernen</span>`;

            // Params: only show for this first variant (same for all in group)
            const paramsHtml = v.is_keep
                ? g.params.map(p =>
                    `<span class="param-tag">${esc(p.name)}: <strong>${esc(p.wert)}</strong></span>`
                ).join('')
                : `<span style="color:var(--muted);font-size:.72rem">identisch ↑</span>`;

            row.innerHTML = `
      <div><span class="var-badge ${badgeCls}">Var ${esc(v.letter)}</span></div>
      <div>
        <span class="anzahl-pill ${anzahlCls}">${v.total_anzahl > 0 ? v.total_anzahl + ' Stk in ' + v.room_count + ' Räumen' : 'keine aktiven'}</span>
      </div>
      <div><div class="params-list">${paramsHtml}</div></div>
      <div>${actionHtml}</div>`;
            grid.appendChild(row);
        });
        body.appendChild(grid);

        // Action row
        const actionRow = document.createElement('div');
        actionRow.className = 'action-row';

        const dropLetters = g.drop_letters.join(', ');
        const mergeDesc = `Var ${dropLetters} → Var ${esc(g.keep_letter)}`;

        actionRow.innerHTML = `
    <button class="btn-merge-group" id="btn-group-${idx}" onclick="mergeSingle(${idx})">
      <i class="fas fa-compress-arrows-alt"></i> Zusammenführen
    </button>
    <span class="merge-detail">
      Var <span style="color:var(--danger)">${esc(dropLetters)}</span>
      <span class="arrow">→</span>
      Var <span style="color:var(--success)">${esc(g.keep_letter)}</span>
      · alle Raum-Einträge werden umgeschrieben, danach Var ${esc(dropLetters)} gelöscht
    </span>`;
        body.appendChild(actionRow);

        card.appendChild(body);
        return card;
    }

    // ─────────────────────────────────────────────
    async function mergeSingle(idx) {
        if (!scanData) return;
        const g = scanData.duplicate_groups[idx];
        const btn = document.getElementById(`btn-group-${idx}`);
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner"></span> Läuft…`;

        await doMerge([g], () => {
            mergedGroups.add(idx);
            const card = document.getElementById(`group-${idx}`);
            card.style.opacity = '.4';
            card.style.pointerEvents = 'none';
            btn.innerHTML = `<i class="fas fa-check"></i> Erledigt`;
            btn.style.background = 'var(--muted)';
            checkAllMerged();
        }, () => {
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-compress-arrows-alt"></i> Nochmal versuchen`;
        });
    }

    async function mergeAll() {
        if (!scanData?.duplicate_groups?.length) return;
        const btn = document.getElementById('btn-merge-all');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner" style="border-top-color:#0f1117"></span> Merge läuft…`;

        const pending = scanData.duplicate_groups.filter((_, i) => !mergedGroups.has(i));
        await doMerge(pending, () => {
            toast(`${pending.length} Gruppe${pending.length !== 1 ? 'n' : ''} erfolgreich zusammengeführt`);
            runScan(); // Rescan
        }, () => {
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-compress-arrows-alt"></i> Alle zusammenführen`;
        });
    }

    async function doMerge(groups, onOk, onErr) {
        try {
            const res = await fetch(`${API}?action=merge`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'merge', groups}),
            });
            const data = await res.json();
            if (data.error) throw new Error(data.error);
            if (data.ok) {
                toast(`${data.merged} Variante${data.merged !== 1 ? 'n' : ''} zusammengeführt`);
                onOk();
            } else {
                toast(`${data.merged} OK, Fehler: ${(data.errors || []).join('; ')}`, false);
                onErr();
            }
        } catch (e) {
            toast('Fehler: ' + e.message, false);
            onErr();
        }
    }

    function checkAllMerged() {
        if (!scanData) return;
        const total = scanData.duplicate_groups.length;
        const merged = mergedGroups.size;
        const pillInfo = document.getElementById('pill-info');
        if (merged === total) {
            pillInfo.textContent = `✓ Alle ${total} Gruppen zusammengeführt`;
            pillInfo.style.display = '';
            pillInfo.style.color = 'var(--success)';
        }
    }
</script>

</body>
</html>