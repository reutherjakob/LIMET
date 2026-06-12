    /**
     * projectGallery.js
     * Shared JS for project image gallery used in imageGallery.php and documentationV2.php.
     *
     * Requires: jQuery, Bootstrap 5, Viewer.js, makeToaster()
     *
     * Fixes applied:
     *   - B1: Removed dead $('#projprojImageRoomModal') handler (typo, triple "proj")
     *   - B2: Upload success now checks res.status === 'ok' (JSON), not data.trim() === 'OK'
     *   - B3: linkImageToVermerk / unlinkImageFromVermerk now consistently return JSON
     *   - D1: Extracted from documentationV2.php + imageGallery.php (was duplicated ~250 lines each)
     *   - D3: parseResponse() helper removes repetitive try/catch pattern
     *   - D4: initViewer() helper removes duplicated Viewer.js config
     */
    
    // ── Helpers ──────────────────────────────────────────────────────────────────
    const _base = (document.querySelector('script[src*="projectGallery.js"]')?.src || '')
        .includes('img_support') ? '' : 'img_support/';
    
    
    function parseResponse(raw) {
        try {
            return typeof raw === 'string' ? JSON.parse(raw) : raw;
        } catch (e) {
            return {status: 'error', msg: String(raw)};
        }
    }
    
    function initViewer(element, filterClass) {
        if (!element) return;
        // alte Instanz zerstören, sonst stapeln sich Viewer bei jedem Filter-Update
        if (element._viewer) {
            element._viewer.destroy();
            element._viewer = null;
        }
        const viewer = new Viewer(element, {
            toolbar: {
                zoomIn: 1,
                zoomOut: 1,
                oneToOne: 1,
                reset: 1,
                prev: 1,
                play: 0,
                next: 1,
                rotateLeft: 1,
                rotateRight: 1
            },
            title: false, tooltip: false, navbar: true,
            filter(image) {
                return image.classList.contains(filterClass);
            }
        });
        element._viewer = viewer;
        return viewer;
    }
    
    // ── Gallery reload (AJAX, no full page reload) ────────────────────────────────
    
    function reloadProjectGallery() {
        $.ajax({
            url: _base + 'getProjectImages.php', type: 'POST',
            success: function (data) {
                const images = parseResponse(data);
                if (!Array.isArray(images) && images.status !== undefined) return;
                const imgs = Array.isArray(images) ? images : [];
    
                const gallery = document.getElementById('projectGallery');
                const hint = document.getElementById('galleryEmptyHint');
                if (!gallery) return;
    
                if (!imgs.length) {
                    gallery.innerHTML = '';
                    if (hint) hint.classList.remove('d-none');
                    return;
                }
                if (hint) hint.classList.add('d-none');
    
                gallery.innerHTML = imgs.map(img => `
                    <div class="position-relative" style="display:inline-block;">
                        <div class="position-absolute top-0 end-0 m-1 d-flex gap-1" style="z-index:10;">
                            <button type="button" class="btn btn-secondary btn-sm proj-meta-btn"
                                    data-image-id="${img.idtabelle_Files}" title="Metadaten anzeigen" style="opacity:0.85;">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm proj-vermerk-btn"
                                    data-image-id="${img.idtabelle_Files}" title="Vermerk zuordnen"
                                    style="opacity:0.85; background:rgba(255,255,255,0.85);">
                                <i class="fas fa-comment-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm proj-room-btn"
                                    data-image-id="${img.idtabelle_Files}" title="Raum zuordnen"
                                    style="opacity:0.85; background:rgba(255,255,255,0.85);">
                                <i class="fas fa-door-open"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm project-gallery-delete-btn"
                                    data-image-id="${img.idtabelle_Files}" title="Bild löschen" style="opacity:0.85;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <img src="https://limet-rb.com/Dokumente_RB/Images/${img.Name}"
                             class="project-gallery-img rounded"
                             style="height:160px; width:160px; object-fit:cover; cursor:zoom-in;" alt="Projektfoto">
                    </div>`).join('');
    
                initViewer(gallery, 'project-gallery-img');
            }
        });
    }
    
    // ── Upload Modal ──────────────────────────────────────────────────────────────
    
    $(document).ready(function () {
    
        $('#addProjectImage').on('click', () => $('#uploadProjectImageModal').modal('show'));
    
        $('#uploadProjectImageModal').on('hidden.bs.modal', function () {
            document.getElementById('projImageUpload').value = '';
            document.getElementById('projUploadPreviewWrapper').classList.add('d-none');
            document.getElementById('projUploadProgress').classList.add('d-none');
            document.getElementById('projUploadConfirmBtn').disabled = true;
            document.getElementById('projDropZone').style.display = '';
        });
    
        const projDropZone = document.getElementById('projDropZone');
        if (projDropZone) {
            projDropZone.addEventListener('click', () => document.getElementById('projImageUpload').click());
            projDropZone.addEventListener('dragover', e => {
                e.preventDefault();
                projDropZone.classList.add('bg-light');
            });
            projDropZone.addEventListener('dragleave', () => projDropZone.classList.remove('bg-light'));
            projDropZone.addEventListener('drop', e => {
                e.preventDefault();
                projDropZone.classList.remove('bg-light');
                if (e.dataTransfer.files[0]) _handleProjFile(e.dataTransfer.files[0]);
            });
        }
    
        const projFileInput = document.getElementById('projImageUpload');
        if (projFileInput) {
            projFileInput.addEventListener('change', function () {
                if (this.files[0]) _handleProjFile(this.files[0]);
            });
        }
    
        function _handleProjFile(file) {
            document.getElementById('projUploadFileName').textContent = file.name;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('projUploadPreview').src = e.target.result;
                document.getElementById('projUploadPreviewWrapper').classList.remove('d-none');
                document.getElementById('projUploadConfirmBtn').disabled = false;
                document.getElementById('projDropZone').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    
        // FIX B2: check res.status === 'ok', not data.trim() === 'OK'
        $('#projUploadConfirmBtn').on('click', function () {
            const previewSrc = document.getElementById('projUploadPreview').src;
            if (!previewSrc || previewSrc === window.location.href) return;
    
            const img = new Image();
            img.src = previewSrc;
            img.onload = function () {
                const MAX = 1200; // längste Kante in px – nach Bedarf anpassen
                let w = img.width, h = img.height;

                // nur verkleinern, nie vergrößern
                if (w > MAX || h > MAX) {
                    const ratio = Math.min(MAX / w, MAX / h);
                    w = Math.round(w * ratio);
                    h = Math.round(h * ratio);
                }

                const canvas = document.createElement('canvas');
                canvas.width  = w;
                canvas.height = h;
                canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                const encoded = canvas.toDataURL('image/jpeg', 0.9);

                document.getElementById('projUploadProgress').classList.remove('d-none');
                document.getElementById('projUploadConfirmBtn').disabled = true;

                $.ajax({
                    url: _base + 'uploadFileImage.php', type: 'POST',
                    data: {fileUpload: encoded},
                    success: function (raw) {
                        const res = parseResponse(raw);
                        $('#uploadProjectImageModal').modal('hide');
                        if (res.status === 'ok') {
                            makeToaster('Bild hochgeladen!', true);
                            reloadProjectGallery();
                        } else {
                            makeToaster('Fehler: ' + (res.msg || raw), false);
                            document.getElementById('projUploadProgress').classList.add('d-none');
                            document.getElementById('projUploadConfirmBtn').disabled = false;
                        }
                    },
                    error: function () {
                        makeToaster('Upload fehlgeschlagen!', false);
                        document.getElementById('projUploadProgress').classList.add('d-none');
                        document.getElementById('projUploadConfirmBtn').disabled = false;
                    }
                });
            };
        });
    
        // ── Delete ────────────────────────────────────────────────────────────────
    
        let pendingProjDeleteId = null;
    
        $(document).on('click', '.project-gallery-delete-btn', function (e) {
            e.stopPropagation();
            pendingProjDeleteId = $(this).data('image-id');
    
            $.ajax({
                url: _base + 'deleteImage.php', type: 'POST',
                data: {imageID: pendingProjDeleteId},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status === 'linked') {
                        document.getElementById('projDeleteConfirmBody').innerHTML =
                            '<i class="fas fa-exclamation-circle text-warning me-1"></i><strong>' + res.msg + '</strong>';
                        document.getElementById('projConfirmDeleteBtn').disabled = true;
                        $('#projDeleteConfirmModal').modal('show');
                    } else if (res.status === 'ok') {
                        // Kein Modal nötig — direkt löschen (war nicht verknüpft)
                        makeToaster('Bild gelöscht.', true);
                        reloadProjectGallery();
                    } else {
                        document.getElementById('projDeleteConfirmBody').textContent = 'Das Bild wird unwiderruflich gelöscht.';
                        document.getElementById('projConfirmDeleteBtn').disabled = false;
                        $('#projDeleteConfirmModal').modal('show');
                    }
                }
            });
        });
    
        $('#projConfirmDeleteBtn').on('click', function () {
            if (!pendingProjDeleteId || $(this).prop('disabled')) return;
            const id = pendingProjDeleteId;
            $('#projDeleteConfirmModal').modal('hide');
            $.ajax({
                url: _base + 'deleteImage.php', type: 'POST',
                data: {imageID: id},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status === 'ok') {
                        makeToaster('Bild gelöscht.', true);
                        reloadProjectGallery();
                    } else {
                        makeToaster(res.msg || 'Fehler beim Löschen.', false);
                    }
                },
                error: () => makeToaster('Löschen fehlgeschlagen!', false)
            });
        });
    
        $('#projDeleteConfirmModal').on('hidden.bs.modal', function () {
            document.getElementById('projConfirmDeleteBtn').disabled = false;
            pendingProjDeleteId = null;
        });
        $('#projDeleteConfirmModal').on('shown.bs.modal', () => $('#projConfirmDeleteBtn').focus());
        $('#projDeleteConfirmModal').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#projConfirmDeleteBtn').trigger('click');
            }
        });
    
        // ── Meta Modal ────────────────────────────────────────────────────────────
    
        $(document).on('click', '.proj-meta-btn', function (e) {
            e.stopPropagation();
            const imageID = $(this).data('image-id');
            document.getElementById('projImageMetaBody').innerHTML =
                '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>';
            $('#projImageMetaModal').modal('show');
    
            $.ajax({
                url: _base + 'getImageMeta.php', type: 'POST',
                data: {imageID},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status !== 'ok') {
                        document.getElementById('projImageMetaBody').innerHTML =
                            '<p class="text-danger small">Fehler beim Laden der Metadaten.</p>';
                        return;
                    }
                    const raeumeHtml = res.raeume.length
                        ? res.raeume.map(r => `<span class="badge bg-secondary me-1">${r.Raumnr} ${r.Raumbezeichnung}</span>`).join('')
                        : '<span class="text-muted fst-italic small">Keine Räume zugeordnet</span>';
    
                    const vermerkeHtml = res.vermerke.length
                        ? '<ul class="list-unstyled mb-0">' + res.vermerke.map(v =>
                        `<li class="small"><i class="fas fa-comment-alt text-muted me-1"></i>
                             <strong>${v.Gruppenname}</strong> (${v.Datum}) – ${v.Kurztext}${v.Kurztext.length >= 80 ? '…' : ''}</li>`
                    ).join('') + '</ul>'
                        : '<span class="text-muted fst-italic small">Kein Vermerk verknüpft</span>';
    
                    document.getElementById('projImageMetaBody').innerHTML = `
                        <table class="table table-sm table-borderless mb-0"><tbody>
                            <tr><th class="text-muted small ps-0">Hochgeladen</th><td class="small">${res.timestamp}</td></tr>
                            <tr><th class="text-muted small ps-0">Projekt</th><td class="small">${res.projekt}</td></tr>
                            <tr><th class="text-muted small ps-0">Räume</th><td>${raeumeHtml}</td></tr>
                            <tr><th class="text-muted small ps-0">Vermerke</th><td>${vermerkeHtml}</td></tr>
                        </tbody></table>`;
                },
                error: () => {
                    document.getElementById('projImageMetaBody').innerHTML = '<p class="text-danger small">Verbindungsfehler.</p>';
                }
            });
        });
    
        // ── Room Modal ────────────────────────────────────────────────────────────
    
        $(document).on('click', '.proj-room-btn', function (e) {
            e.stopPropagation();
            const imageID = $(this).data('image-id');
            document.getElementById('projRoomModalImageID').value = imageID;
            document.getElementById('projRoomPickerSelect').value = '';
            document.getElementById('projRoomLinkConfirmBtn').disabled = true;
            _projLoadCurrentRooms(imageID);
            $('#projImageRoomModal').modal('show');
        });
    
        function _projLoadCurrentRooms(imageID) {
            document.getElementById('projRoomCurrentList').innerHTML =
                '<span class="text-muted fst-italic small">Lädt…</span>';
            $.ajax({
                url: _base + 'getImageMeta.php', type: 'POST',
                data: {imageID},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status !== 'ok' || !res.raeume.length) {
                        document.getElementById('projRoomCurrentList').innerHTML =
                            '<span class="text-muted fst-italic small">Keine Räume zugeordnet</span>';
                        return;
                    }
                    document.getElementById('projRoomCurrentList').innerHTML = res.raeume.map(r => `
                        <span class="badge bg-secondary d-inline-flex align-items-center gap-1">
                            ${r.Raumnr} ${r.Raumbezeichnung}
                            <button type="button" class="btn-close btn-close-white btn-sm proj-room-unlink-btn"
                                    data-image-id="${imageID}" data-raum-id="${r.raumID}"
                                    style="font-size:0.6em;" title="Verknüpfung entfernen" aria-label="Entfernen"></button>
                        </span>`).join('');
                }
            });
        }
    
        $('#projRoomPickerSelect').on('change', function () {
            document.getElementById('projRoomLinkConfirmBtn').disabled = !$(this).val();
        });
    
        $('#projRoomLinkConfirmBtn').on('click', function () {
            const imageID = document.getElementById('projRoomModalImageID').value;
            const raumID = document.getElementById('projRoomPickerSelect').value;
            if (!imageID || !raumID) return;
            $(this).prop('disabled', true);
            $.ajax({
                url: _base + 'linkImageToRoom.php', type: 'POST',
                data: {imageID, raumID},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status === 'ok') {
                        makeToaster('Raum verknüpft.', true);
                        document.getElementById('projRoomPickerSelect').value = '';
                        _projLoadCurrentRooms(imageID);
                    } else {
                        makeToaster('Fehler: ' + (res.msg || ''), false);
                    }
                    document.getElementById('projRoomLinkConfirmBtn').disabled = true;
                },
                error: () => makeToaster('Verbindungsfehler.', false)
            });
        });
    
        $(document).on('click', '.proj-room-unlink-btn', function (e) {
            e.stopPropagation();
            const imageID = $(this).data('image-id');
            const raumID = $(this).data('raum-id');
            $.ajax({
                url: _base + 'unlinkImageFromRoom.php', type: 'POST',
                data: {imageID, raumID},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status === 'ok') {
                        makeToaster('Raumverknüpfung entfernt.', true);
                        _projLoadCurrentRooms(imageID);
                    } else {
                        makeToaster('Fehler: ' + (res.msg || ''), false);
                    }
                }
            });
        });
    
        $('#projImageRoomModal').on('hidden.bs.modal', function () {
            document.getElementById('projRoomModalImageID').value = '';
            document.getElementById('projRoomPickerSelect').value = '';
            document.getElementById('projRoomLinkConfirmBtn').disabled = true;
        });
    
        // ── Vermerk Modal ─────────────────────────────────────────────────────────
    
        // Cache für geladene Vermerkliste
        let _vermerkPickerData = null;
        let _selectedVermerkID = null;
    
        $(document).on('click', '.proj-vermerk-btn', function (e) {
            e.stopPropagation();
            const imageID = $(this).data('image-id');
            document.getElementById('projVermerkModalImageID').value = imageID;
            _selectedVermerkID = null;
            document.getElementById('projVermerkLinkConfirmBtn').disabled = true;
            document.getElementById('projVermerkSearch').value = '';
            document.getElementById('projVermerkSelectedInfo').classList.add('d-none');
            _projLoadCurrentVermerke(imageID);
            _projLoadVermerkPicker();
            $('#projImageVermerkModal').modal('show');
        });
    
        function _projLoadCurrentVermerke(imageID) {
            document.getElementById('projVermerkCurrentList').innerHTML =
                '<span class="text-muted fst-italic small">Lädt…</span>';
            $.ajax({
                url: _base + 'getImageMeta.php', type: 'POST', data: {imageID},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status !== 'ok' || !res.vermerke.length) {
                        document.getElementById('projVermerkCurrentList').innerHTML =
                            '<span class="text-muted fst-italic small">Keine Vermerke verknüpft</span>';
                        return;
                    }
                    document.getElementById('projVermerkCurrentList').innerHTML = res.vermerke.map(v => `
                        <span class="badge bg-dark d-inline-flex align-items-center gap-1">
                            <i class="fas fa-comment-alt me-1" style="font-size:0.7em;"></i>
                            ${v.Gruppenname} – ${v.Kurztext.substring(0, 30)}…
                            <button type="button" class="btn-close btn-close-white btn-sm proj-vermerk-unlink-btn"
                                    data-image-id="${imageID}" data-vermerk-id="${v.idtabelle_Vermerke}"
                                    style="font-size:0.6em;" aria-label="Entfernen"></button>
                        </span>`).join('');
                }
            });
        }
    
        function _projLoadVermerkPicker(filterText) {
            const list = document.getElementById('projVermerkPickerList');
    
            // Ersten Aufruf: Daten laden und cachen
            if (!_vermerkPickerData) {
                list.innerHTML = '<div class="text-muted fst-italic small p-2">Wird geladen…</div>';
                $.ajax({
                    url: '../getVermerkeForProject.php', type: 'POST',
                    success: function (raw) {
                        const res = parseResponse(raw);
                        if (res.status !== 'ok') {
                            list.innerHTML = '<div class="text-danger small p-2">Fehler beim Laden.</div>';
                            return;
                        }
                        _vermerkPickerData = res.vermerke;
                        _renderVermerkPicker(filterText || '');
                    }
                });
            } else {
                _renderVermerkPicker(filterText || '');
            }
        }
    
        function _renderVermerkPicker(filterText) {
            const list = document.getElementById('projVermerkPickerList');
            const lower = filterText.toLowerCase();
            const data = _vermerkPickerData || [];
    
            const filtered = lower
                ? data.filter(v =>
                    (v.Gruppenname || '').toLowerCase().includes(lower) ||
                    (v.Untergruppenname || '').toLowerCase().includes(lower) ||
                    (v.Kurztext || '').toLowerCase().includes(lower))
                : data;
    
            if (!filtered.length) {
                list.innerHTML = '<div class="text-muted fst-italic small p-2">Keine Vermerke gefunden.</div>';
                return;
            }
    
            // Gruppieren nach Vermerkgruppe
            const byGroup = {};
            filtered.forEach(v => {
                const key = v.idtabelle_Vermerkgruppe;
                if (!byGroup[key]) byGroup[key] = {name: v.Gruppenname, datum: v.Datum, items: []};
                byGroup[key].items.push(v);
            });
    
            let html = '';
            Object.values(byGroup).forEach(group => {
                html += `<div class="px-2 py-1 text-muted" style="font-size:11px; font-weight:600; background:var(--bs-light, #f8f9fa); border-bottom:1px solid #dee2e6; position:sticky; top:0;">
                            <i class="fas fa-folder me-1"></i>${_escHtml(group.name)}
                            <span class="ms-1 fw-normal">${group.datum || ''}</span>
                         </div>`;
    
                // Untergruppen
                const byUg = {};
                group.items.forEach(v => {
                    const uk = v.Untergruppenname;
                    if (!byUg[uk]) byUg[uk] = [];
                    byUg[uk].push(v);
                });
    
                Object.entries(byUg).forEach(([ugName, items]) => {
                    html += `<div class="px-3 py-1 text-muted" style="font-size:11px; background:var(--bs-white, #fff); border-bottom:1px solid #f0f0f0;">
                                <i class="fas fa-chevron-right me-1" style="font-size:9px;"></i>${_escHtml(ugName)}
                             </div>`;
                    items.forEach(v => {
                        const isSelected = _selectedVermerkID == v.idtabelle_Vermerke;
                        html += `<div class="px-4 py-2 proj-vermerk-pick-item d-flex align-items-start gap-2"
                                      data-vermerk-id="${v.idtabelle_Vermerke}"
                                      style="cursor:pointer; border-bottom:1px solid #f5f5f5; font-size:13px;
                                             ${isSelected ? 'background:#d1e7dd;' : ''}">
                                    <span class="badge bg-secondary mt-1" style="font-size:10px; min-width:28px;">#${v.idtabelle_Vermerke}</span>
                                    <span>${_escHtml(v.Kurztext)}${v.Kurztext.length >= 80 ? '…' : ''}</span>
                                 </div>`;
                    });
                });
            });
    
            list.innerHTML = html;
        }
    
        function _escHtml(str) {
            const d = document.createElement('div');
            d.appendChild(document.createTextNode(str || ''));
            return d.innerHTML;
        }
    
        // Klick auf Vermerk-Listeneintrag
        $(document).on('click', '.proj-vermerk-pick-item', function () {
            _selectedVermerkID = $(this).data('vermerk-id');
            const text = $(this).find('span:last-child').text();
    
            // Highlight
            $('.proj-vermerk-pick-item').css('background', '');
            $(this).css('background', '#d1e7dd');
    
            // Info anzeigen
            document.getElementById('projVermerkSelectedLabel').textContent = '#' + _selectedVermerkID + ' – ' + text;
            document.getElementById('projVermerkSelectedInfo').classList.remove('d-none');
            document.getElementById('projVermerkLinkConfirmBtn').disabled = false;
        });
    
        // Suche
        $('#projVermerkSearch').on('input', function () {
            _selectedVermerkID = null;
            document.getElementById('projVermerkLinkConfirmBtn').disabled = true;
            document.getElementById('projVermerkSelectedInfo').classList.add('d-none');
            _projLoadVermerkPicker($(this).val());
        });
    
        $('#projVermerkLinkConfirmBtn').on('click', function () {
            const imageID = document.getElementById('projVermerkModalImageID').value;
            if (!imageID || !_selectedVermerkID) return;
            $(this).prop('disabled', true);
            $.ajax({
                url: _base + 'linkImageToVermerk.php', type: 'POST',
                data: {imageID, vermerkID: _selectedVermerkID},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status === 'ok') {
                        makeToaster('Vermerk verknüpft.', true);
                        _selectedVermerkID = null;
                        document.getElementById('projVermerkSelectedInfo').classList.add('d-none');
                        document.getElementById('projVermerkSearch').value = '';
                        _projLoadCurrentVermerke(imageID);
                        _renderVermerkPicker('');
                    } else if (res.status === 'already_linked') {
                        makeToaster('Vermerk ist bereits zugeordnet.', false);
                    } else {
                        makeToaster('Fehler: ' + (res.msg || ''), false);
                    }
                    document.getElementById('projVermerkLinkConfirmBtn').disabled = true;
                },
                error: () => makeToaster('Verbindungsfehler.', false)
            });
        });
    
        $(document).on('click', '.proj-vermerk-unlink-btn', function (e) {
            e.stopPropagation();
            const imageID = $(this).data('image-id');
            const vermerkID = $(this).data('vermerk-id');
            $.ajax({
                url: _base + 'unlinkImageFromVermerk.php', type: 'POST',
                data: {imageID, vermerkID},
                success: function (raw) {
                    const res = parseResponse(raw);
                    if (res.status === 'ok') {
                        makeToaster('Vermerkverknüpfung entfernt.', true);
                        _projLoadCurrentVermerke(imageID);
                    } else {
                        makeToaster('Fehler: ' + (res.msg || ''), false);
                    }
                }
            });
        });
    
        $('#projImageVermerkModal').on('hidden.bs.modal', function () {
            document.getElementById('projVermerkModalImageID').value = '';
            document.getElementById('projVermerkLinkConfirmBtn').disabled = true;
            document.getElementById('projVermerkSelectedInfo').classList.add('d-none');
            _selectedVermerkID = null;
            // Cache leeren damit beim nächsten Öffnen frische Daten kommen
            _vermerkPickerData = null;
        });
    
        // ── Viewer.js init für initiale Galerie ───────────────────────────────────
        const initialGallery = document.getElementById('projectGallery');
        if (initialGallery) {
            initViewer(initialGallery, 'project-gallery-img');
        }
    
    
        // ── Filter & Sort ─────────────────────────────────────────────────────────
    
        function applyGalleryFilter() {
            const search = (document.getElementById('gallerySearch') || {value: ''}).value.toLowerCase().trim();
            const raumVal = (document.getElementById('galleryRaumFilter') || {value: ''}).value;
            const vermerkVal = (document.getElementById('galleryVermerkFilter') || {value: ''}).value;
            const sortVal = (document.getElementById('gallerySortSelect') || {value: 'newest'}).value;
            const resetBtn = document.getElementById('galleryResetFilter');
            const hasFilter = search || raumVal || vermerkVal || sortVal !== 'newest';
            if (resetBtn) resetBtn.classList.toggle('d-none', !hasFilter);
    
            let items = [...document.querySelectorAll('#projectGallery .gallery-item')];
    
            items.sort((a, b) => {
                if (sortVal === 'newest') return (b.dataset.timestamp || '').localeCompare(a.dataset.timestamp || '');
                if (sortVal === 'oldest') return (a.dataset.timestamp || '').localeCompare(b.dataset.timestamp || '');
                if (sortVal === 'name') return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                return 0;
            });
            const gallery = document.getElementById('projectGallery');
            if (gallery) items.forEach(el => gallery.appendChild(el));
    
            let visible = 0;
            items.forEach(el => {
                const name = el.dataset.name || '';
                const raumIDs = el.dataset.raumids ? el.dataset.raumids.split(',').map(Number) : [];
                const gruppenIDs = el.dataset.vermerkgruppenids ? el.dataset.vermerkgruppenids.split(',').map(Number) : [];
                let show = true;
                if (search && !name.includes(search)) show = false;
                if (raumVal === '__none__') {
                    if (raumIDs.length > 0) show = false;
                } else if (raumVal) {
                    if (!raumIDs.includes(parseInt(raumVal))) show = false;
                }
                if (vermerkVal === '__none__') {
                    if (gruppenIDs.length > 0) show = false;
                } else if (vermerkVal) {
                    if (!gruppenIDs.includes(parseInt(vermerkVal))) show = false;
                }
                el.style.display = show ? '' : 'none';
                if (show) visible++;
            });
    
            const countInfo = document.getElementById('galleryCountInfo');
            const cntBadge = document.getElementById('galleryCntBadge');
            const emptyHint = document.getElementById('galleryEmptyHint');
            const noResHint = document.getElementById('galleryNoResultHint');
            if (countInfo) countInfo.textContent = visible + ' von ' + items.length + ' Bild' + (items.length !== 1 ? 'ern' : '');
            if (cntBadge) cntBadge.textContent = visible;
            if (emptyHint) emptyHint.classList.toggle('d-none', items.length > 0);
            if (noResHint) noResHint.classList.toggle('d-none', visible > 0 || items.length === 0);
    
            if (gallery) initViewer(gallery, 'project-gallery-img');
        }
    
    
        const resetBtn = document.getElementById('galleryResetFilter');
        if (resetBtn) resetBtn.addEventListener('click', function () {
            ['galleryRaumFilter', 'galleryVermerkFilter'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            const sort = document.getElementById('gallerySortSelect');
            if (sort) sort.value = 'newest';
            applyGalleryFilter();
        });
        ['galleryRaumFilter', 'galleryVermerkFilter', 'gallerySortSelect'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', applyGalleryFilter);
        });
        applyGalleryFilter();
    
        // ── Bulk-Modus ────────────────────────────────────────────────────────────
    
        let _bulkMode = false;
        let _selectedIDs = new Set();
    
        function _setBulkMode(on) {
            _bulkMode = on;
            _selectedIDs.clear();
            document.querySelectorAll('.bulk-checkbox-wrap').forEach(el => el.classList.toggle('d-none', !on));
            document.querySelectorAll('.gallery-item').forEach(el => {
                el.classList.toggle('bulk-mode', on);
                el.classList.remove('bulk-selected');
            });
            document.querySelectorAll('.gallery-bulk-cb').forEach(cb => cb.checked = false);
            const bulkActions = document.getElementById('bulkActions');
            const bulkToggle = document.getElementById('bulkToggleBtn');
            if (bulkActions) bulkActions.classList.toggle('d-none', !on);
            if (bulkToggle) bulkToggle.classList.toggle('d-none', on);
            _updateBulkBtns();
        }
    
        function _updateBulkBtns() {
            const n = _selectedIDs.size;
            const lbl = document.getElementById('bulkCountLabel');
            const rb = document.getElementById('bulkRoomBtn');
            const db = document.getElementById('bulkDeleteBtn');
            if (lbl) lbl.textContent = n + ' gewählt';
            if (rb) rb.disabled = n === 0;
            if (db) db.disabled = n === 0;
        }
    
        const bulkToggleBtn = document.getElementById('bulkToggleBtn');
        const bulkCancelBtn = document.getElementById('bulkCancelBtn');
        const bulkSelectAllBtn = document.getElementById('bulkSelectAllBtn');
        if (bulkToggleBtn) bulkToggleBtn.addEventListener('click', () => _setBulkMode(true));
        if (bulkCancelBtn) bulkCancelBtn.addEventListener('click', () => _setBulkMode(false));
        if (bulkSelectAllBtn) bulkSelectAllBtn.addEventListener('click', function () {
            const visible = [...document.querySelectorAll('#projectGallery .gallery-item')]
                .filter(el => el.style.display !== 'none');
            const allSel = visible.every(el => _selectedIDs.has(parseInt(el.dataset.imageId)));
            visible.forEach(el => {
                const id = parseInt(el.dataset.imageId);
                const cb = el.querySelector('.gallery-bulk-cb');
                if (allSel) {
                    _selectedIDs.delete(id);
                    el.classList.remove('bulk-selected');
                    if (cb) cb.checked = false;
                } else {
                    _selectedIDs.add(id);
                    el.classList.add('bulk-selected');
                    if (cb) cb.checked = true;
                }
            });
            _updateBulkBtns();
        });
    
        const galleryEl = document.getElementById('projectGallery');
        if (galleryEl) {
            galleryEl.addEventListener('click', function (e) {
                if (!_bulkMode) return;
                const item = e.target.closest('.gallery-item');
                if (!item || e.target.closest('button')) return;
                const id = parseInt(item.dataset.imageId);
                const cb = item.querySelector('.gallery-bulk-cb');
                if (_selectedIDs.has(id)) {
                    _selectedIDs.delete(id);
                    item.classList.remove('bulk-selected');
                    if (cb) cb.checked = false;
                } else {
                    _selectedIDs.add(id);
                    item.classList.add('bulk-selected');
                    if (cb) cb.checked = true;
                }
                _updateBulkBtns();
            });
        }
    
        // Bulk → Raum zuordnen
        const bulkRoomBtn = document.getElementById('bulkRoomBtn');
        if (bulkRoomBtn) bulkRoomBtn.addEventListener('click', function () {
            const cnt = document.getElementById('bulkRoomCount');
            const sel = document.getElementById('bulkRoomSelect');
            const cfm = document.getElementById('bulkRoomConfirmBtn');
            if (cnt) cnt.textContent = _selectedIDs.size;
            if (sel) sel.value = '';
            if (cfm) cfm.disabled = true;
            new bootstrap.Modal(document.getElementById('bulkRoomModal')).show();
        });
        const bulkRoomSelect = document.getElementById('bulkRoomSelect');
        if (bulkRoomSelect) bulkRoomSelect.addEventListener('change', function () {
            const cfm = document.getElementById('bulkRoomConfirmBtn');
            if (cfm) cfm.disabled = !this.value;
        });
        const bulkRoomConfirmBtn = document.getElementById('bulkRoomConfirmBtn');
        if (bulkRoomConfirmBtn) bulkRoomConfirmBtn.addEventListener('click', function () {
            const raumID = (document.getElementById('bulkRoomSelect') || {}).value;
            if (!raumID || _selectedIDs.size === 0) return;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            Promise.all([..._selectedIDs].map(imgID =>
                fetch(_base + 'linkImageToRoom.php', {
                    method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'imageID=' + imgID + '&raumID=' + raumID
                }).then(r => r.json())
            )).then(results => {
                bootstrap.Modal.getInstance(document.getElementById('bulkRoomModal')).hide();
                const ok = results.filter(r => r.status === 'ok').length;
                makeToaster(ok + ' Bild' + (ok !== 1 ? 'er' : '') + ' mit Raum verknüpft.', true);
                _setBulkMode(false);
                setTimeout(() => location.reload(), 600);
            }).catch(() => {
                makeToaster('Fehler beim Bulk-Zuordnen.', false);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus me-1"></i> Zuordnen';
            });
        });
    
        // Bulk → Löschen
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        if (bulkDeleteBtn) bulkDeleteBtn.addEventListener('click', function () {
            const body = document.getElementById('bulkDeleteBody');
            if (body) body.textContent = _selectedIDs.size + ' Bild' + (_selectedIDs.size !== 1 ? 'er' : '') + ' werden unwiderruflich gelöscht.';
            new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
        });
        const bulkDeleteConfirmBtn = document.getElementById('bulkDeleteConfirmBtn');
        if (bulkDeleteConfirmBtn) bulkDeleteConfirmBtn.addEventListener('click', function () {
            if (_selectedIDs.size === 0) return;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            Promise.all([..._selectedIDs].map(imgID =>
                fetch(_base + 'deleteImage.php', {
                    method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'imageID=' + imgID
                }).then(r => r.json())
            )).then(results => {
                bootstrap.Modal.getInstance(document.getElementById('bulkDeleteModal')).hide();
                const ok = results.filter(r => r.status === 'ok').length;
                const linked = results.filter(r => r.status === 'linked').length;
                let msg = ok + ' Bild' + (ok !== 1 ? 'er' : '') + ' gelöscht.';
                if (linked > 0) msg += ' ' + linked + ' übersprungen (in Vermerken verknüpft).';
                makeToaster(msg, ok > 0);
                _setBulkMode(false);
                setTimeout(() => location.reload(), 600);
            }).catch(() => {
                makeToaster('Fehler beim Bulk-Löschen.', false);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt me-1"></i> Löschen';
            });
        });
    
        $(document).on('click', '.proj-zoom-btn', function (e) {
            e.stopPropagation();
            const wrap = this.closest('.gallery-thumb-wrap');
            const img = wrap ? wrap.querySelector('.project-gallery-img') : null;
            const gallery = document.getElementById('projectGallery');
            if (!img || !gallery || !gallery._viewer) return;
            const imgs = [...gallery.querySelectorAll('.project-gallery-img')];
            const idx = imgs.indexOf(img);
            if (idx >= 0) gallery._viewer.view(idx);
        });
    }); // end document.ready