<?php
// roombook_befu.php
require_once "../utils/_utils.php";
init_page_serversides("No Redirect");
error_reporting(E_ALL);
ini_set('display_errors', 0);

$uploadDir = __DIR__ . '/uploads_word/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

$metaFile = $uploadDir . 'meta.json';
$files = [];
if (file_exists($metaFile)) {
    $meta = json_decode(file_get_contents($metaFile), true);
    if (is_array($meta)) {
        $files = $meta;
    }
}

function jsonExit(array $data): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Liste
if (isset($_GET['action']) && $_GET['action'] === 'list') {
    jsonExit($files);
}

// Upload
if (isset($_GET['do']) && $_GET['do'] === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['word_file']) || $_FILES['word_file']['error'] !== UPLOAD_ERR_OK) {
        jsonExit(['error' => 'Keine gültige Datei erhalten. Fehlercode: ' . $_FILES['word_file']['error']]);
    }

    $displayName = isset($_POST['template_name']) ? trim($_POST['template_name']) : '';
    if ($displayName === '') {
        jsonExit(['error' => 'Anzeigename fehlt.']);
    }

    $file = $_FILES['word_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'docx') {
        jsonExit(['error' => 'Nur .docx-Dateien erlaubt.']);
    }

    $id = uniqid('f_', true);
    $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $file['name']);
    $targetName = $id . '_' . $safeName;
    $targetPath = $uploadDir . $targetName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        jsonExit(['error' => 'Fehler beim Speichern der Datei.']);
    }

    $newEntry = [
        'id'              => $id,
        'display_name'    => $displayName,
        'file_name'       => $targetName,
        'file_size'       => filesize($targetPath),
        'upload_date'     => date('Y-m-d H:i:s'),
        'extra_text_html' => ''
    ];

    $files[] = $newEntry;
    file_put_contents($metaFile, json_encode($files, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    jsonExit(['ok' => true, 'id' => $id]);
}

// Extra-Text speichern
if (isset($_GET['do']) && $_GET['do'] === 'save_text' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $html = $_POST['extra_text_html'] ?? '';

    if ($id === '') {
        jsonExit(['error' => 'Keine Datei-ID übergeben.']);
    }

    $foundIndex = null;
    foreach ($files as $i => $f) {
        if (($f['id'] ?? '') === $id) {
            $foundIndex = $i;
            break;
        }
    }

    if ($foundIndex === null) {
        jsonExit(['error' => 'Datei nicht gefunden.']);
    }

    $files[$foundIndex]['extra_text_html'] = $html;
    $files[$foundIndex]['extra_text_updated'] = date('Y-m-d H:i:s');

    file_put_contents($metaFile, json_encode($files, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    jsonExit(['ok' => true]);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Word-Vorlagen</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.mce.com/1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>

<div id="limet-navbar"></div>
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Card 1: kommt später -->
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    Card 1 – kommt später
                </div>
                <div class="card-body">
                    <!-- Platzhalter -->
                </div>
            </div>
        </div>

        <!-- Card 2: Vorschau / Extra-Text -->
        <div class="col-3">
            <div class="card">
                <div class="card-header">
                    Extra-Text
                </div>
                <div class="card-body">
                    <div id="preview-info" class="text-muted small mb-2">
                        Wähle eine Datei, um den Extra-Text zu bearbeiten.
                    </div>

                    <textarea id="extra-text-editor"></textarea>

                    <div class="mt-2 d-flex gap-2">
                        <button id="btn-save-text" class="btn btn-primary btn-sm" disabled>
                            <i class="fas fa-save me-1"></i> Text speichern
                        </button>
                        <a id="btn-edit-page" class="btn btn-secondary btn-sm" href="#" target="_blank" style="display:none;">
                            <i class="fas fa-external-link-alt me-1"></i> Auf eigener Seite bearbeiten
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Upload & Download -->
        <div class="col-3">
            <div class="card">
                <div class="card-header">
                    Word-Dateien
                </div>
                <div class="card-body">
                    <!-- Upload -->
                    <form id="upload-form" enctype="multipart/form-data" class="mb-3">
                        <div class="mb-2">
                            <label for="word-file" class="form-label">Word-Datei hochladen (.docx)</label>
                            <input type="file" class="form-control" id="word-file" name="word_file" accept=".docx"/>
                        </div>
                        <div class="mb-2">
                            <label for="template-name" class="form-label">Anzeigename</label>
                            <input type="text" class="form-control" id="template-name" name="template_name"
                                   placeholder="z. B. Vertrag Rohling"/>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload me-1"></i> Hochladen
                        </button>
                    </form>

                    <hr class="my-3"/>

                    <!-- Liste der hochgeladenen Dateien -->
                    <div>
                        <label class="form-label">Hochgeladene Dateien</label>
                        <select id="file-select" class="form-select mb-2">
                            <option value="">– bitte wählen –</option>
                        </select>

                        <div class="d-flex gap-2">
                            <a id="btn-download" class="btn btn-success btn-sm text-white" href="#" download>
                                <i class="fas fa-download me-1"></i> Download (mit Extra-Text)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script charset="utf-8">
    $(function () {
        const BASE = '/BEFU/roombook_befu.php';
        const DOWNLOAD_SCRIPT = '/BEFU/roombook_befu_download.php';
        const TEXT_PAGE = '/BEFU/roombook_befu_text.php';

        // TinyMCE init
        tinymce.init({
            selector: '#extra-text-editor',
            height: 250,
            plugins: 'lists link table code',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });

        function loadFiles() {
            $.getJSON(BASE + '?action=list', function (data) {
                const $sel = $('#file-select');
                $sel.find('option:not(:first)').remove();

                if (!data || data.length === 0) {
                    return;
                }

                data.forEach(function (f) {
                    const opt = new Option(f.display_name || f.file_name, f.id, false, false);
                    opt.setAttribute('data-file-name', f.file_name);
                    opt.setAttribute('data-file-size', f.file_size || '');
                    opt.setAttribute('data-upload-date', f.upload_date || '');
                    opt.setAttribute('data-extra-text', f.extra_text_html || '');
                    $sel.append(opt);
                });
            });
        }

        loadFiles();

        $('#upload-form').on('submit', function (e) {
            e.preventDefault();
            const fileInput = document.getElementById('word-file');
            const nameInput = document.getElementById('template-name').value.trim();

            if (!fileInput.files.length) {
                alert('Bitte eine .docx-Datei auswählen.');
                return;
            }
            if (!nameInput) {
                alert('Bitte einen Anzeigenamen eingeben.');
                return;
            }

            const formData = new FormData();
            formData.append('word_file', fileInput.files[0]);
            formData.append('template_name', nameInput);

            $.ajax({
                url: BASE + '?do=upload',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res && res.error) {
                        alert('Fehler: ' + res.error);
                        return;
                    }
                    alert('Datei erfolgreich hochgeladen.');
                    $('#template-name').val('');
                    fileInput.value = '';
                    loadFiles();
                },
                error: function (xhr) {
                    const msg = (xhr.responseJSON && xhr.responseJSON.error)
                        ? xhr.responseJSON.error
                        : 'Fehler beim Hochladen (HTTP ' + xhr.status + ').';
                    alert(msg);
                }
            });
        });

        $('#file-select').on('change', function () {
            const selected = $(this).find('option:selected');
            const hasValue = !!selected.val();

            if (hasValue) {
                const extraText = selected.attr('data-extra-text') || '';
                tinymce.get('extra-text-editor').setContent(extraText);

                $('#btn-save-text').prop('disabled', false);
                $('#btn-edit-page')
                    .attr('href', TEXT_PAGE + '?id=' + encodeURIComponent(selected.val()))
                    .show();

                $('#btn-download')
                    .attr('href', DOWNLOAD_SCRIPT + '?id=' + encodeURIComponent(selected.val()))
                    .removeClass('disabled');
            } else {
                tinymce.get('extra-text-editor').setContent('');
                $('#btn-save-text').prop('disabled', true);
                $('#btn-edit-page').hide();
                $('#btn-download').attr('href', '#').addClass('disabled');
            }
        });

        $('#btn-save-text').on('click', function () {
            const selected = $('#file-select').find('option:selected');
            if (!selected.val()) {
                alert('Bitte eine Datei auswählen.');
                return;
            }

            const html = tinymce.get('extra-text-editor').getContent();

            $.ajax({
                url: BASE + '?do=save_text',
                type: 'POST',
                data: {
                    id: selected.val(),
                    extra_text_html: html
                },
                success: function (res) {
                    if (res && res.error) {
                        alert('Fehler: ' + res.error);
                        return;
                    }
                    alert('Text gespeichert.');
                    loadFiles(); // um data-extra-text zu aktualisieren
                },
                error: function () {
                    alert('Fehler beim Speichern des Textes.');
                }
            });
        });
    });
</script>
</body>
</html>