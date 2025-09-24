<?php
function validateInput($input, $maxLen = 255): array|string|null
{
    $input = trim($input);
    if (mb_strlen($input) > $maxLen) {
        $input = mb_substr($input, 0, $maxLen);
    }
    if (preg_match('/^[\p{L}0-9\s\-\_\.\,]*$/u', $input)) {
        return $input;
    }
    return preg_replace('/[^\p{L}0-9\s\-\_\.\,]/u', '', $input);
}

if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides("No Redirect");

session_start();

$updateMessage = '';
$defaultElementName = ' ';
$defaultBereich = '  ';
$defaultAnmerkungFeld = ' ';
$defaultTextToAdd = '  ';

$anmerkungFields = [
    'Anmerkung BauStatik',
    'Anmerkung Elektro',
    'Anmerkung Gerte',
    'Anmerkung HKLS',
    'Anmerkung allgemein'
];

// Step 1: User submits filters and preview matches
if (isset($_POST['preview_rooms'])) {
    $mysqli = utils_connect_sql();
    if ($mysqli->connect_errno) {
        $updateMessage = 'Fehler bei der Verbindung zur Datenbank: ' . $mysqli->connect_error;
    } else {

        $elementNameInput = validateInput($_POST['elementName'] ?? $defaultElementName);
        $bereichInput = validateInput($_POST['raumBereich'] ?? $defaultBereich);
        $textToAddInput = trim($_POST['textToAdd'] ?? $defaultTextToAdd);
        if (mb_strlen($textToAddInput) > 500) {
            $textToAddInput = mb_substr($textToAddInput, 0, 500); // Limit length for text to add
        }
        $anmerkungFieldInput = $_POST['anmerkungFeld'] ?? $defaultAnmerkungFeld;

        if (!in_array($anmerkungFieldInput, $anmerkungFields)) {
            $updateMessage = 'Ungültiges Anmerkungsfeld ausgewählt.';
        } else {
            $elementNameLike = '%' . $elementNameInput . '%';
            $bereichLike = '%' . $bereichInput . '%';

            // Query with element names causing match per room, including current Anmerkung field text
            $query = "SELECT DISTINCT r.idTABELLE_Räume, r.Raumbezeichnung, e.Bezeichnung AS ElementName, r.`$anmerkungFieldInput` AS AnmerkungText
                      FROM tabelle_räume r
                      JOIN tabelle_räume_has_tabelle_elemente re ON r.idTABELLE_Räume = re.TABELLE_Räume_idTABELLE_Räume
                      JOIN tabelle_elemente e ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                      WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                        AND r.`Raumbereich Nutzer` LIKE ?
                        AND e.Bezeichnung LIKE ?
                        AND re.Anzahl > 0
                      ORDER BY r.idTABELLE_Räume";

            if ($stmt = $mysqli->prepare($query)) {
                $stmt->bind_param('iss', $_SESSION['projectID'], $bereichLike, $elementNameLike);
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
                    // Store info in session for confirmation step
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
        // Clear session after update
        $_SESSION['to_update'] = null;
    } else {
        $updateMessage = 'Keine Räume für das Update zum Bestätigen gefunden.';
    }
}

// Retrieved data for repopulation or to show the preview list
$elementNameVal = $_POST['elementName'] ?? $defaultElementName;
$bereichVal = $_POST['raumBereich'] ?? $defaultBereich;
$anmerkungFieldVal = $_POST['anmerkungFeld'] ?? $defaultAnmerkungFeld;
$textToAddVal = $_POST['textToAdd'] ?? $defaultTextToAdd;
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Projekte</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#roomsTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json"
                },
                pageLength: 5,
                lengthMenu: [5, 10, 25],
                columnDefs: [
                    { orderable: false, targets: 3 }
                ]
            });
        });
    </script>
</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Left card: form and preview / confirm -->
        <div class="col-8">
            <div class="card h-100">
                <div class="card-header">
                    Anmerkung für alle Räume des Raumbereiches mit jenem Element hinzufügen.
                </div>
                <div class="card-body">

                    <?php if ($updateMessage !== ''): ?>
                        <div class="alert alert-info"><?= htmlspecialchars($updateMessage) ?></div>
                    <?php endif; ?>

                    <form method="post" autocomplete="off">
                        <div class="mb-3">
                            <label for="elementName" class="form-label">Elementname (alle Elemente, die den Suchtext irgendwo enthalten haben) </label>
                            <input type="text" class="form-control" id="elementName" name="elementName" required
                                   value="<?= htmlspecialchars($elementNameVal) ?>"/>
                        </div>
                        <div class="mb-3">
                            <label for="raumBereich" class="form-label">Raumbereich (alle Raumbereich, die den Suchtext irgendwo enthalten haben)  </label>
                            <input type="text" class="form-control" id="raumBereich" name="raumBereich" required
                                   value="<?= htmlspecialchars($bereichVal) ?>"/>
                        </div>
                        <div class="mb-3">
                            <label for="anmerkungFeld" class="form-label">Feld der Anmerkung zum Aktualisieren</label>
                            <select class="form-select" id="anmerkungFeld" name="anmerkungFeld" required>
                                <?php foreach ($anmerkungFields as $field): ?>
                                    <option value="<?= htmlspecialchars($field) ?>" <?= ($anmerkungFieldVal === $field) ? 'selected' : '' ?>><?= htmlspecialchars($field) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="textToAdd" class="form-label">Text zum Anmerkung hinzufügen</label>
                            <textarea class="form-control" id="textToAdd" name="textToAdd" rows="3" required><?= htmlspecialchars($textToAddVal) ?></textarea>
                        </div>

                        <?php if (!empty($_SESSION['to_update'])): ?>
                            <div class="alert alert-warning">
                                Bitte bestätigen Sie, dass Sie die Anmerkung in den gefundenen Räumen ändern möchten.
                            </div>
                            <button type="submit" name="confirm_update" class="btn btn-danger">Änderung bestätigen und ausführen</button>
                            <button type="submit" name="cancel_update" class="btn btn-secondary ms-2">Abbrechen</button>
                        <?php else: ?>
                            <button type="submit" name="preview_rooms" class="btn btn-primary">Räume prüfen und anzeigen</button>
                        <?php endif; ?>
                    </form>

                    <?php
                    // If cancel update pressed, clear session info and refresh
                    if (isset($_POST['cancel_update'])) {
                        $_SESSION['to_update'] = null;
                        echo '<meta http-equiv="refresh" content="0">';
                    }
                    ?>

                </div>

            </div>
        </div>

        <!-- Right card: affected rooms with existing Anmerkungen in DataTable -->
        <div class="col-4">
            <div class="card h-100">
                <div class="card-header">
                    Übersicht der betroffenen Räume und bestehende Anmerkungen
                </div>
                <div class="card-body">
                    <p>Diese Übersicht zeigt alle Räume, für die die Anmerkung hinzugefügt wird. Neben den Elementen sind hier auch die bestehenden Texte des ausgewählten Anmerkungsfeldes sichtbar.</p>
                    <?php if (!empty($_SESSION['to_update']['rooms'])): ?>
                        <table id="roomsTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>Raum ID</th>
                                <th>Raumbezeichnung</th>
                                <th>Element(e)</th>
                                <th>Bestehende Anmerkungen</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($_SESSION['to_update']['rooms'] as $roomId => $roomData): ?>
                                <tr>
                                    <td><?= htmlspecialchars($roomId) ?></td>
                                    <td><?= htmlspecialchars($roomData['Raumbezeichnung']) ?></td>
                                    <td><?= htmlspecialchars(implode(', ', $roomData['Elements'])) ?></td>
                                    <td><pre style="white-space: pre-wrap; font-family: inherit;"><?= htmlspecialchars($roomData['AnmerkungText']) ?></pre></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Keine Räume zum Anzeigen. Bitte führen Sie eine Suche durch.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-muted" style="font-size: 0.8rem;">
                    Die \"Bestehenden Anmerkungen\" geben den aktuellen Datenbankinhalt des ausgewählten Anmerkungsfeldes wieder.<br />
                    Bitte prüfen Sie diese vor der Ausführung der Änderung.
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
