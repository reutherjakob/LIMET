<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>KHI-Export</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>

<?php
require_once 'utils/_utils.php';
init_page_serversides();
?>

<body>
<div id="limet-navbar"></div>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <b><i class="fas fa-file-excel me-2"></i> GPMT-Export (Raumbuch → Revit)</b>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Erzeugt das Export-File mit den <strong>aktuellen Daten aus der Datenbank</strong>
                        (Sheets <em>Quest</em> + <em>RESULTAT</em>). Bauabschnitt wählen und herunterladen.
                    </p>

                    <label class="form-label small text-muted mb-1">Bauabschnitte</label>
                    <div class="mb-3 d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input bt-check" type="checkbox" value="BT1" id="btBT1" checked>
                            <label class="form-check-label" for="btBT1">BT1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input bt-check" type="checkbox" value="BT2" id="btBT2" checked>
                            <label class="form-check-label" for="btBT2">BT2</label>
                        </div>
                    </div>

                    <button type="button" id="downloadBtn" class="btn btn-dark">
                        <i class="fas fa-download me-1"></i> Export herunterladen
                    </button>
                    <span id="dlHint" class="text-muted small ms-2"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    $('#downloadBtn').click(function () {
        const bt = $('.bt-check:checked').map((_, el) => el.value).get();
        if (bt.length === 0) {
            if (typeof makeToaster === 'function') makeToaster('Bitte mindestens einen Bauabschnitt wählen.', false);
            return;
        }
        $('#dlHint').text('Wird erzeugt…');
        // Download via versteckten iframe -> kein Seitenwechsel, Fehler bleiben sichtbar
        const url = 'get_khi_export.php?bt=' + encodeURIComponent(bt.join(','));
        window.location = url;
        setTimeout(() => $('#dlHint').text(''), 4000);
    });
</script>
</body>
</html>