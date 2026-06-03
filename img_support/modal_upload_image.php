<div class="modal fade" id="uploadProjectImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-1"></i> Bild hochladen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="projDropZone" class="border border-2 rounded p-4 text-center text-muted mb-3"
                     style="border-style:dashed !important; cursor:pointer;">
                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i><br>
                    Datei hierher ziehen oder <u>klicken zum Auswählen</u>
                </div>
                <input type="file" id="projImageUpload" accept="image/*" class="d-none">
                <div id="projUploadPreviewWrapper" class="d-none text-center">
                    <img id="projUploadPreview" class="img-fluid rounded" style="max-height:200px;"
                         alt="Vorschau">
                    <p id="projUploadFileName" class="text-muted small mt-1"></p>
                </div>
                <div id="projUploadProgress" class="d-none">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-dark"
                             style="width:100%"></div>
                    </div>
                    <p class="text-center text-muted small mt-1">Wird hochgeladen...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen
                </button>
                <button type="button" id="projUploadConfirmBtn" class="btn btn-dark btn-sm" disabled>
                    <i class="fas fa-upload me-1"></i> Hochladen
                </button>
            </div>
        </div>
    </div>
</div>