<div class="modal fade" id="projImageVermerkModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-comment-alt me-1"></i> Vermerke zuordnen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="projVermerkModalImageID">

                <p class="text-muted small mb-2">Aktuell verknüpfte Vermerke:</p>
                <div id="projVermerkCurrentList" class="mb-3 d-flex flex-wrap gap-1">
                    <span class="text-muted fst-italic small">Lädt…</span>
                </div>

                <hr class="my-2">

                <p class="text-muted small mb-1">Vermerk hinzufügen:</p>

                <!-- Suchfeld -->
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="projVermerkSearch" class="form-control form-control-sm"
                           placeholder="Suche nach Gruppe, Untergruppe oder Vermerktext…">
                </div>

                <!-- Vermerk-Liste als scrollbare Select-ähnliche Liste -->
                <div id="projVermerkPickerList"
                     style="max-height:280px; overflow-y:auto; border:1px solid #dee2e6; border-radius:6px;">
                    <div class="text-muted fst-italic small p-2">Wird geladen…</div>
                </div>

                <!-- Ausgewählter Vermerk -->
                <div id="projVermerkSelectedInfo" class="d-none mt-2 p-2 rounded border border-success bg-light small">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    <span id="projVermerkSelectedLabel"></span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Schließen</button>
                <button type="button" id="projVermerkLinkConfirmBtn" class="btn btn-success btn-sm" disabled>
                    <i class="fas fa-plus me-1"></i> Verknüpfen
                </button>
            </div>
        </div>
    </div>
</div>