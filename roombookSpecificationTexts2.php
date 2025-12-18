<?php
// 25 FX
require_once 'utils/_utils.php';
init_page_serversides("", "x");

$updateMessage = '';
$defaultAnmerkungFeld = '';
$defaultRaumbereich = '';
$defaultElementKeyword = '';
$defaultRaumbezeichnung = '';
$defaultTextToAdd = '';

$anmerkungFields = [
    'Anmerkung BauStatik',
    'Anmerkung Elektro',
    'Anmerkung Geräte',
    'Anmerkung HKLS'
];

// Step 1: User submits filters and preview matches
if (isset($_POST['preview_rooms'])) {
    $mysqli = utils_connect_sql();
    if ($mysqli->connect_errno) {
        $updateMessage = 'Fehler bei der Verbindung zur Datenbank: ' . $mysqli->connect_error;
    } else {
        $anmerkungFieldInput = $_POST['anmerkungFeld'] ?? $defaultAnmerkungFeld;
        $raumbereichInput = trim($_POST['raumbereich'] ?? $defaultRaumbereich);
        $elementInput = trim($_POST['elementKeyword'] ?? $defaultElementKeyword);
        $raumbezeichnungInput = trim($_POST['raumbezeichnung'] ?? $defaultRaumbezeichnung);
        $textToAddInput = trim($_POST['textToAdd'] ?? $defaultTextToAdd);
        if (mb_strlen($textToAddInput) > 500) {
            $textToAddInput = mb_substr($textToAddInput, 0, 500);
        }

        if (!in_array($anmerkungFieldInput, $anmerkungFields)) {
            $updateMessage = 'Ungültiges Anmerkungsfeld ausgewählt.';
        } else {
            $query = "SELECT DISTINCT r.idTABELLE_Räume, CONCAT(Raumnr, ' ', Raumbezeichnung) AS Raumbezeichnung, e.Bezeichnung AS ElementName, r.`$anmerkungFieldInput` AS AnmerkungText
                      FROM tabelle_räume r
                      LEFT JOIN tabelle_räume_has_tabelle_elemente re ON r.idTABELLE_Räume = re.TABELLE_Räume_idTABELLE_Räume
                      LEFT JOIN tabelle_elemente e ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                      WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                        AND r.Entfallen = 0
                        AND r.`Raumbereich Nutzer` LIKE ?
                        AND r.Raumbezeichnung LIKE ?
                        AND (
                            e.Bezeichnung LIKE ?
                            OR e.ElementID LIKE ?
                          )
                        AND (re.Anzahl IS NULL OR re.Anzahl > 0)
                      ORDER BY r.idTABELLE_Räume";

            $projectID = $_SESSION['projectID'];
            $raumSql = '%' . $raumbereichInput . '%';
            $rbSql = '%' . $raumbezeichnungInput . '%';
            $elSql = '%' . $elementInput . '%';

            $stmt = $mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param('issss', $projectID, $raumSql, $rbSql, $elSql, $elSql);
                $stmt->execute();
                $result = $stmt->get_result();
                $rooms = [];
                while ($row = $result->fetch_assoc()) {
                    $id = $row['idTABELLE_Räume'];
                    if (!isset($rooms[$id])) {
                        $rooms[$id] = [
                            'Raumbezeichnung' => $row['Raumbezeichnung'],
                            'Elements' => [],
                            'AnmerkungText' => $row['AnmerkungText'] ?? ''
                        ];
                    }
                    if ($row['ElementName']) {
                        $rooms[$id]['Elements'][] = $row['ElementName'];
                    }
                }
                $stmt->close();

                if (count($rooms) > 0) {
                    $_SESSION['to_update'] = [
                        'rooms' => $rooms,
                        'anmerkungField' => $anmerkungFieldInput,
                        'textToAdd' => $textToAddInput
                    ];
                } else {
                    $_SESSION['to_update'] = null;
                    $updateMessage = 'Keine passenden Räume gefunden.';
                }
            } else {
                $updateMessage = 'Fehler bei der Vorbereitung der Abfrage: ' . $mysqli->error;
            }
            $mysqli->close();
        }
    }
}

if (isset($_POST['confirm_update'])) {
    if (!empty($_SESSION['to_update'])) {
        $selectedRoomIDs = isset($_POST['selectedRooms']) ? explode(',', $_POST['selectedRooms']) : [];

        if (empty($selectedRoomIDs)) {
            $updateMessage = 'Bitte wählen Sie mindestens einen Raum aus.';
        } else {
            $data = $_SESSION['to_update'];
            $rooms = $data['rooms'];
            $rooms = array_filter($rooms, function ($roomId) use ($selectedRoomIDs) {
                return in_array($roomId, $selectedRoomIDs);
            }, ARRAY_FILTER_USE_KEY);
            $anmerkungFieldInput = $data['anmerkungField'];
            $textToAddInput = $data['textToAdd'] . "\n";

            $mysqli = utils_connect_sql();
            if ($mysqli->connect_errno) {
                $updateMessage = 'Fehler bei der Verbindung zur Datenbank: ' . $mysqli->connect_error;
            } else {
                $updateQuery = "UPDATE tabelle_räume
                            SET `$anmerkungFieldInput` = CONCAT(COALESCE(`$anmerkungFieldInput`, ''), ?)
                            WHERE idTABELLE_Räume = ?";
                if ($updateStmt = $mysqli->prepare($updateQuery)) {
                    foreach ($rooms as $roomId => $roomData) {
                        $updateStmt->bind_param('si', $textToAddInput, $roomId);
                        $updateStmt->execute();
                    }
                    $updateStmt->close();
                    $updateMessage = 'Die Anmerkung wurde für ' . count($rooms) . ' Räume aktualisiert.';
                } else {
                    $updateMessage = 'Fehler bei der Vorbereitung des Update-Statements: ' . $mysqli->error;
                }
                $mysqli->close();
            }
            $_SESSION['to_update'] = null;
        }
    } else {
        $updateMessage = 'Keine Räume für das Update zum Bestätigen gefunden.';
    }
}

// Für repopulation
$anmerkungFieldVal = $_POST['anmerkungFeld'] ?? $defaultAnmerkungFeld;
$raumbereichVal = $_POST['raumbereich'] ?? $defaultRaumbereich;
$elementKeywordVal = $_POST['elementKeyword'] ?? $defaultElementKeyword;
$raumbezeichnungVal = $_POST['raumbezeichnung'] ?? $defaultRaumbezeichnung;
$textToAddVal = $_POST['textToAdd'] ?? $defaultTextToAdd;
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <title>RB-Bauangaben Anmerkungen Freitextsuche</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-3">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Anmerkung bei Raumsuche via Freitext eintragen.</strong>
                    <i class="fa fa-info-circle float-end" data-bs-toggle="tooltip" data-bs-placement="top"
                       title="Suche mit beliebigen Textteilen für Raumbereich, Element oder Raumbezeichnung. Daraufhin werden alle zutreffenden Anmerkungen der Räume aufgelistet. Anschließend ist die Auswahl bestimmter Räume möglich, die den Anmerkungstext angehängt bekommen, wenn man bestätigt."></i>
                </div>
                <div class="card-body">
                    <?php if ($updateMessage !== ''): ?>
                        <div class="alert alert-info"><?= htmlspecialchars($updateMessage) ?></div>
                    <?php endif; ?>
                    <form method="post" autocomplete="off" id="confirmUpdateForm">
                        <div class="mb-3">
                            <label for="anmerkungFeld" class="form-label sr-only">Anmerkungsfeld</label>
                            <select class="form-select" id="anmerkungFeld" name="anmerkungFeld" required>
                                <?php foreach ($anmerkungFields as $field): ?>
                                    <option value="<?= $field ?>" <?= ($anmerkungFieldVal === $field) ? 'selected' : '' ?>><?= $field ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="raumbereich" class="form-label">Raumbereich (Schlüsselwort/Teilwort)</label>
                            <input type="text" class="form-control" id="raumbereich" name="raumbereich"
                                   value="<?= htmlspecialchars($raumbereichVal) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="elementKeyword" class="form-label">Element (Schlüsselwort/Teilwort, Name oder
                                Nummer)</label>
                            <input type="text" class="form-control" id="elementKeyword" name="elementKeyword"
                                   value="<?= htmlspecialchars($elementKeywordVal) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="raumbezeichnung" class="form-label">Raumbezeichnung
                                (Schlüsselwort/Teilwort)</label>
                            <input type="text" class="form-control" id="raumbezeichnung" name="raumbezeichnung"
                                   value="<?= htmlspecialchars($raumbezeichnungVal) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="textToAdd" class="form-label">Text zur bestehenden Anmerkung hinzufügen</label>
                            <textarea class="form-control" id="textToAdd" name="textToAdd" rows="3"
                                      required <?= !empty($_SESSION['to_update']) ? 'disabled' : '' ?>><?= htmlspecialchars($textToAddVal) ?></textarea>
                        </div>
                        <?php if (!empty($_SESSION['to_update'])): ?>
                            <div class="alert alert-warning">
                                Bitte bestätigen Sie, dass Sie die Anmerkung in den gefundenen Räumen ändern möchten.
                            </div>
                            <button type="submit" name="confirm_update" class="btn btn-danger">Änderung bestätigen und
                                ausführen
                            </button>
                            <button type="submit" name="cancel_update" class="btn btn-secondary ms-2">Abbrechen</button>
                        <?php else: ?>
                            <button type="submit" name="preview_rooms" class="btn btn-primary">Räume prüfen und
                                anzeigen
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($_SESSION['to_update']['rooms'])): ?>
                            <input type="hidden" name="selectedRooms" id="selectedRoomsInput">
                        <?php endif; ?>
                    </form>

                    <?php
                    if (isset($_POST['cancel_update'])) {
                        $_SESSION['to_update'] = null;
                        echo '<meta http-equiv="refresh" content="0">';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="card h-100">
                <div class="card-header">
                    Übersicht der betroffenen Räume und bestehende Anmerkungen
                    <div class="float-end border-light">
                        <label for="toggle-all-rooms">Alle Räume</label>
                        <input type="checkbox" id="toggle-all-rooms">
                        <i class="fa fa-info-circle ms-4" data-bs-toggle="tooltip" data-bs-placement="top"
                           title="Diese Übersicht zeigt alle Räume, für die die Anmerkung hinzugefügt wird. Neben den Elementen sind hier auch die bestehenden Texte des ausgewählten Anmerkungsfeldes sichtbar."></i>

                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($_SESSION['to_update']['rooms'])): ?>
                        <table id="roomsTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>Raumbezeichnung</th>
                                <th>Element(e)</th>
                                <th>Bestehende Anmerkungen</th>
                                <th><i class="fas fa-edit"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($_SESSION['to_update']['rooms'] as $roomId => $roomData): ?>
                                <tr>
                                    <td><?= htmlspecialchars($roomData['Raumbezeichnung'] ?? '') ?></td>
                                    <td><?= htmlspecialchars(implode(', ', $roomData['Elements']) ?? '') ?></td>
                                    <td>
                                        <pre style="white-space: pre-wrap; font-family: inherit;"><?= htmlspecialchars($roomData['AnmerkungText']) ?></pre>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="update-room-checkbox"
                                               data-roomid="<?= (int)$roomId ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Keine Räume zum Anzeigen. Bitte führen Sie eine Suche durch.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-muted" style="font-size: 0.8rem;">
                    Die bestehenden Anmerkungen bleiben unverändert. Der Anmerkungstext wird angehängt.
                    Bitte prüfen Sie diese vor der Ausführung der Änderung.
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script>
    $('#toggle-all-rooms').on('change', function () {
        $('.update-room-checkbox').prop('checked', this.checked);
    });

    $(document).ready(function () {
        $('#roomsTable').DataTable({
            language: {url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json"},
            pageLength: -1,
            paging: false,
            layout: {
                bottomStart: ['pageLength', 'search'],
                bottomEnd: ['info', 'paging'],
                topStart: null,
                topEnd: null
            }
        });

        $('#confirmUpdateForm').on('submit', function (e) {
            var selected = [];
            $('.update-room-checkbox:checked').each(function () {
                selected.push($(this).data('roomid'));
            });
            $('#selectedRoomsInput').val(selected.join(','));
        });

        $('#roomsTable').on('submit', function (e) {
            var table = $('#roomsTable').DataTable();
            var selected = [];
            table.rows().every(function () {
                var $checkbox = $(this.node()).find('.update-room-checkbox');
                if ($checkbox.prop('checked')) {
                    selected.push($checkbox.data('roomid'));
                }
            });
            $('#selectedRoomsInput').val(selected.join(','));
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
