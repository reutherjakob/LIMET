<?php

// TODO: Ideen: Standartisierte Texte für best. El.; Standartisierte Syntax;  je El. könnte dann Text geladen und bearbeitet. werden.


//function validateInput($input, $maxLen = 255): array|string|null
//{
//    $input = trim($input);
//    if (mb_strlen($input) > $maxLen) {
//        $input = mb_substr($input, 0, $maxLen);
//    }
//    if (preg_match('/^[\p{L}0-9\s\-\_\.\,]*$/u', $input)) {
//        return $input;
//    }
//    return preg_replace('/[^\p{L}0-9\s\-\_\.\,]/u', '', $input);
//}

require_once 'utils/_utils.php';
init_page_serversides("", "x");

$updateMessage = '';
$defaultElementID = ''; // Elementauswahl jetzt mit ID
$defaultBereich = [];
$defaultAnmerkungFeld = '';
$defaultTextToAdd = '';

$anmerkungFields = [
    'Anmerkung BauStatik',
    'Anmerkung Elektro',
    'Anmerkung Gerte',
    'Anmerkung HKLS',
    'Anmerkung allgemein'
];

// Hole Element-Optionen
$elementOptions = [];
$mysqli = utils_connect_sql();
$res = $mysqli->query("SELECT idTABELLE_Elemente, Bezeichnung, ElementID FROM tabelle_elemente ORDER BY Bezeichnung");
while ($row = $res->fetch_assoc()) {
    $elementOptions[] = [
        'id' => $row['idTABELLE_Elemente'],
        'text' => $row['ElementID'] . ' ' . $row['Bezeichnung']
    ];
}
$res->free();

// Hole Raumbereich-Optionen
$raumbereichOptions = [];
$sqlAreas = "SELECT DISTINCT `Raumbereich Nutzer` FROM tabelle_räume WHERE tabelle_projekte_idTABELLE_Projekte = ? AND Entfallen = 0 ORDER BY `Raumbereich Nutzer`";
$stmt = $mysqli->prepare($sqlAreas);
$stmt->bind_param('i', $_SESSION['projectID']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $raumbereichOptions[] = $row['Raumbereich Nutzer'];
}
$stmt->close();
$mysqli->close();

// Step 1: User submits filters and preview matches
if (isset($_POST['preview_rooms'])) {
    $mysqli = utils_connect_sql();
    if ($mysqli->connect_errno) {
        $updateMessage = 'Fehler bei der Verbindung zur Datenbank: ' . $mysqli->connect_error;
    } else {
        // NEU: Auswahl statt Text
        $elementIDInput = intval($_POST['elementSelect'] ?? $defaultElementID);
        $bereichInputArr = $_POST['raumbereich'] ?? $defaultBereich; // Array
        $textToAddInput = trim($_POST['textToAdd'] ?? $defaultTextToAdd);
        if (mb_strlen($textToAddInput) > 500) {
            $textToAddInput = mb_substr($textToAddInput, 0, 500); // Limit length for text to add
        }
        $anmerkungFieldInput = $_POST['anmerkungFeld'] ?? $defaultAnmerkungFeld;

        if (!in_array($anmerkungFieldInput, $anmerkungFields)) {
            $updateMessage = 'Ungültiges Anmerkungsfeld ausgewählt.';
        } elseif ($elementIDInput <= 0) {
            $updateMessage = 'Bitte ein Element auswählen!';
        } elseif (empty($bereichInputArr)) {
            $updateMessage = 'Bitte mindestens einen Raumbereich auswählen!';
        } else {
            // Dynamisches Bereich-Statement
            $bereichWhere = implode(' OR ', array_fill(0, count($bereichInputArr), "r.`Raumbereich Nutzer` = ?"));
            $query = "SELECT DISTINCT r.idTABELLE_Räume, r.Raumbezeichnung, e.Bezeichnung AS ElementName, r.`$anmerkungFieldInput` AS AnmerkungText
                      FROM tabelle_räume r
                      JOIN tabelle_räume_has_tabelle_elemente re ON r.idTABELLE_Räume = re.TABELLE_Räume_idTABELLE_Räume
                      JOIN tabelle_elemente e ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                      WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                        AND re.TABELLE_Elemente_idTABELLE_Elemente = ?
                        AND ($bereichWhere)
                        AND re.Anzahl > 0
                      ORDER BY r.idTABELLE_Räume";

            $types = 'ii' . str_repeat('s', count($bereichInputArr));
            $params = array_merge([$_SESSION['projectID'], $elementIDInput], $bereichInputArr);

            $stmt = $mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
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
                    $rooms[$id]['Elements'][] = $row['ElementName'];
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

// Step 2: User confirms update, apply changes
if (isset($_POST['confirm_update'])) {
    if (!empty($_SESSION['to_update'])) {
        $data = $_SESSION['to_update'];
        $rooms = $data['rooms'];
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
    } else {
        $updateMessage = 'Keine Räume für das Update zum Bestätigen gefunden.';
    }
}

// Für repopulation
$elementIDVal = $_POST['elementSelect'] ?? $defaultElementID;
$bereichVal = $_POST['raumbereich'] ?? $defaultBereich;
$anmerkungFieldVal = $_POST['anmerkungFeld'] ?? $defaultAnmerkungFeld;
$textToAddVal = $_POST['textToAdd'] ?? $defaultTextToAdd;
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Bauangaben Anmerkungen</title>
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
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
<div id="limet-navbar"></div>
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-4">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Anmerkung für alle Räume des Raumbereiches mit bestimmten Element eintragen.</strong>
                    <i class="fa fa-info-circle float-end" data-bs-toggle="tooltip" data-bs-placement="top"
                       title="Anmerkung Text wird allen Räumen mit Element hinzugefügt."></i>
                </div>
                <div class="card-body">
                    <?php if ($updateMessage !== ''): ?>
                        <div class="alert alert-info"><?= htmlspecialchars($updateMessage) ?></div>
                    <?php endif; ?>
                    <form method="post" autocomplete="off">
                        <div class="mb-3">
                            <label for="anmerkungFeld" class="sr-only"> Anmerkung zum Aktualisieren</label>
                            <select class="form-select" id="anmerkungFeld" name="anmerkungFeld" required>
                                <?php foreach ($anmerkungFields as $field): ?>
                                    <option value="<?= htmlspecialchars($field) ?>" <?= ($anmerkungFieldVal === $field) ? 'selected' : '' ?>><?= htmlspecialchars($field) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="elementSelect">Element auswählen</label>
                            <select id="elementSelect" name="elementSelect" class="form-select select2" required>
                                <option value="">Bitte wählen...</option>
                                <?php foreach ($elementOptions as $opt): ?>
                                    <option value="<?= htmlspecialchars($opt['id']) ?>" <?= ($elementIDVal == $opt['id']) ? 'selected' : '' ?>><?= htmlspecialchars($opt['text']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="raumbereich">Raumbereich auswählen</label>
                            <select id="raumbereich" name="raumbereich[]" class="form-select select2" required>
                                <?php foreach ($raumbereichOptions as $option): ?>
                                    <?php if ($option): // only output if not empty/not false?>
                                        <option value="<?= htmlspecialchars($option) ?>"
                                            <?= (is_array($bereichVal) && in_array($option, $bereichVal)) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($option) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="textToAdd" class="form-label">Text zur bestehenden Anmerkung hinzufügen</label>
                            <textarea class="form-control" id="textToAdd" name="textToAdd" rows="3" required
        <?= !empty($_SESSION['to_update']) ? 'disabled' : '' ?>><?= htmlspecialchars($textToAddVal) ?></textarea>
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

        <div class="col-8">
            <div class="card h-100">
                <div class="card-header">
                    Übersicht der betroffenen Räume und bestehende Anmerkungen
                    <i class="fa fa-info-circle float-end" data-bs-toggle="tooltip" data-bs-placement="top"
                       title="Diese Übersicht zeigt alle Räume, für die die Anmerkung hinzugefügt wird. Neben den Elementen sind hier auch die bestehenden Texte des ausgewählten Anmerkungsfeldes sichtbar."></i>
                </div>
                <div class="card-body">
                    <?php if (!empty($_SESSION['to_update']['rooms'])): ?>
                        <table id="roomsTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>Raumbezeichnung</th>
                                <th>Element(e)</th>
                                <th>Bestehende Anmerkungen</th>
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
    $(document).ready(function () {
        $('#roomsTable').DataTable({
            language: {url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json"},
            pageLength: 5,
            lengthMenu: [5, 10, 25],
            layout: {
                bottomStart: ['pageLength', 'search'],
                bottomEnd: ['info', 'paging'],
                topStart: null,
                topEnd: null
            }
        });
        $('.select2').select2({
            placeholder: function () {
                if (this[0].hasAttribute('multiple')) {
                    return 'Raumbereich auswählen';
                }
                return 'Element auswählen';
            },
            allowClear: true,
            width: 'resolve'
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
