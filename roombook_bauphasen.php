<?php
require_once 'utils/_utils.php';
init_page_serversides("");
$sessionProjektId = isset($_SESSION['projectID']) ? (int)$_SESSION['projectID'] : 0;
$sessionProjektName = htmlspecialchars($_SESSION['projectName'] ?? '');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Bauphasen</title>

    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div id="limet-navbar"></div>

    <!-- Table card -->
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">Bauphasen</div>
                <div class="col-6 d-flex justify-content-end">
                    <div class="justify-content-end">
                        <button class="btn btn-success" id="btnAdd">
                            <i class="fas fa-plus me-1"></i>Neue Bauphase
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0 align-middle" id="tableBauphasen">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Bauphase</th>
                    <th>Beginn</th>
                    <th>Fertigstellung</th>
                    <th>Dauer (Tage)</th>
                    <th class="text-center">Aktionen</th>
                </tr>
                </thead>
                <tbody id="tbody">
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle me-1"></i>Bitte zuerst ein Projekt auswählen.
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ============================================================
     Modal: Hinzufügen / Bearbeiten
============================================================ -->
<div class="modal fade" id="modalBauphase" tabindex="-1" aria-labelledby="modalBauphaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalBauphaseLabel">
                    <i class="fas fa-hard-hat me-2"></i><span id="modalTitle">Neue Bauphase</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId"/>

                <div class="mb-3">
                    <label for="inputBauphase" class="form-label fw-semibold">
                        Bauphase <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="inputBauphase"
                           placeholder="z. B. Rohbau, Ausbau, …" maxlength="45"/>
                    <div class="invalid-feedback">Bitte eine Bezeichnung eingeben.</div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label for="inputBeginn" class="form-label fw-semibold">
                            Beginn <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="inputBeginn"/>
                        <div class="invalid-feedback">Pflichtfeld.</div>
                    </div>
                    <div class="col-6">
                        <label for="inputFertig" class="form-label fw-semibold">
                            Fertigstellung <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="inputFertig"/>
                        <div class="invalid-feedback">Pflichtfeld / darf nicht vor Beginn liegen.</div>
                    </div>
                </div>

                <div id="modalAlert" class="mt-3 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Abbrechen
                </button>
                <button type="button" class="btn btn-primary" id="btnSave">
                    <i class="fas fa-save me-1"></i>Speichern
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     Modal: Löschen bestätigen
============================================================ -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalDeleteLabel">
                    <i class="fas fa-trash me-2"></i>Löschen bestätigen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Bauphase wirklich löschen?</p>
                <p class="fw-bold mb-0" id="deleteItemName"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnConfirmDelete">
                    <i class="fas fa-trash me-1"></i>Löschen
                </button>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    const API = 'api_bauphasen.php';
    let currentProjektId = <?= $sessionProjektId ?>;
    let deleteId = null;

    const modalBauphase = new bootstrap.Modal('#modalBauphase');
    const modalDelete = new bootstrap.Modal('#modalDelete');

    /* ── helpers ─────────────────────────────────────────── */

    function clearModalValidation() {
        $('#inputBauphase, #inputBeginn, #inputFertig').removeClass('is-invalid');
        $('#modalAlert').addClass('d-none').html('');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const [y, m, d] = dateStr.split('-');
        return `${d}.${m}.${y}`;
    }

    function daysBetween(d1, d2) {
        const ms = new Date(d2) - new Date(d1);
        return Math.round(ms / 86400000);
    }


    /* ── load Bauphasen ──────────────────────────────────── */

    function loadBauphasen() {
        if (!currentProjektId) return;

        $.get(API, {action: 'getAll', projekt_id: currentProjektId}, function (res) {

            if (!res.success) {
                makeToaster(res.message, false);
                return;
            }

            const tbody = $('#tbody');
            tbody.empty();

            if (!res.data.length) {
                tbody.html(`<tr><td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle me-1"></i>Keine Bauphasen für dieses Projekt vorhanden.
                </td></tr>`);

                return;
            }

            res.data.forEach((row, i) => {
                const days = daysBetween(row.datum_beginn, row.datum_fertigstellung);
                tbody.append(`
                    <tr>
                        <td class="text-muted small">${i + 1}</td>
                        <td><strong>${escHtml(row.bauphase)}</strong></td>
                        <td>${formatDate(row.datum_beginn)}</td>
                        <td>${formatDate(row.datum_fertigstellung)}</td>
                        <td>
                            <span class="badge bg-secondary">${days} Tage</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-outline-primary btn-sm btn-edit me-1"
                                    data-id="${row.idtabelle_bauphasen}"
                                    data-bauphase="${escAttr(row.bauphase)}"
                                    data-beginn="${row.datum_beginn}"
                                    data-fertig="${row.datum_fertigstellung}"
                                    title="Bearbeiten">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm btn-delete"
                                    data-id="${row.idtabelle_bauphasen}"
                                    data-name="${escAttr(row.bauphase)}"
                                    title="Löschen">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`);
            });


        }, 'json').fail(() => {


            makeToaster('Serverfehler beim Laden der Bauphasen.', false);
        });
    }

    function escHtml(str) {
        return $('<div>').text(str).html();
    }

    function escAttr(str) {
        return str.replace(/"/g, '&quot;');
    }

    /* ── validate modal form ─────────────────────────────── */

    function validateForm() {
        let ok = true;
        clearModalValidation();

        const bauphase = $('#inputBauphase').val().trim();
        const beginn = $('#inputBeginn').val();
        const fertig = $('#inputFertig').val();

        if (!bauphase) {
            $('#inputBauphase').addClass('is-invalid');
            ok = false;
        }
        if (!beginn) {
            $('#inputBeginn').addClass('is-invalid');
            ok = false;
        }
        if (!fertig) {
            $('#inputFertig').addClass('is-invalid');
            ok = false;
        }
        if (beginn && fertig && fertig < beginn) {
            $('#inputFertig').addClass('is-invalid');
            ok = false;
        }
        return ok;
    }

    /* ── save (add / update) ─────────────────────────────── */

    function saveBauphase() {
        if (!validateForm()) return;

        const id = $('#editId').val();
        const bauphase = $('#inputBauphase').val().trim();
        const beginn = $('#inputBeginn').val();
        const fertig = $('#inputFertig').val();

        const data = {
            action: id ? 'update' : 'add',
            bauphase,
            datum_beginn: beginn,
            datum_fertigstellung: fertig,
        };
        if (id) data.id = id;
        else data.projekt_id = currentProjektId;

        $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Speichern…');

        $.post(API, data, function (res) {
            $('#btnSave').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Speichern');
            if (res.success) {
                modalBauphase.hide();
                makeToaster(res.message, true);
                loadBauphasen();
            } else {
                makeToaster(res.message, false);
                $('#modalAlert').removeClass('d-none');
            }
        }, 'json').fail(() => {
            $('#btnSave').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Speichern');
            makeToaster('Serverfehler.', false);
            $('#modalAlert').removeClass('d-none');
        });
    }

    /* ── delete ──────────────────────────────────────────── */

    function deleteBauphase() {
        if (!deleteId) return;
        $('#btnConfirmDelete').prop('disabled', true);
        $.post(API, {action: 'delete', id: deleteId}, function (res) {
            $('#btnConfirmDelete').prop('disabled', false);
            modalDelete.hide();
            if (res.success) {
                makeToaster(res.message, true);
                loadBauphasen();
            } else {
                makeToaster(res.message, false);
            }
        }, 'json');
    }

    /* ── event bindings ──────────────────────────────────── */

    $(document).ready(function () {
        loadBauphasen();

        /* open add modal */
        $('#btnAdd').on('click', function () {
            clearModalValidation();
            $('#editId').val('');
            $('#modalTitle').text('Neue Bauphase');
            $('#inputBauphase').val('');
            $('#inputBeginn').val('');
            $('#inputFertig').val('');
            modalBauphase.show();
            setTimeout(() => $('#inputBauphase').focus(), 400);
        });

        /* open edit modal */
        $(document).on('click', '.btn-edit', function () {
            clearModalValidation();
            const btn = $(this);
            $('#editId').val(btn.data('id'));
            $('#modalTitle').text('Bauphase bearbeiten');
            $('#inputBauphase').val(btn.data('bauphase'));
            $('#inputBeginn').val(btn.data('beginn'));
            $('#inputFertig').val(btn.data('fertig'));
            modalBauphase.show();
            setTimeout(() => $('#inputBauphase').focus(), 400);
        });

        /* open delete modal */
        $(document).on('click', '.btn-delete', function () {
            deleteId = $(this).data('id');
            $('#deleteItemName').text($(this).data('name'));
            modalDelete.show();
        });

        /* save */
        $('#btnSave').on('click', saveBauphase);

        /* enter key in modal */
        $('#modalBauphase').on('keydown', function (e) {
            if (e.key === 'Enter') saveBauphase();
        });

        /* confirm delete */
        $('#btnConfirmDelete').on('click', deleteBauphase);
    });
</script>
</body>
</html>