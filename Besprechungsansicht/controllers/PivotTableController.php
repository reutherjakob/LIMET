<?php
include_once '../../utils/_utils.php';
include_once '../models/PivotTable.php';

check_login();

header('Content-Type: text/html; charset=utf-8');

$projectID = $_SESSION['projectID'] ?? 0;
if ($projectID <= 0) {
    http_response_code(400);
    echo '<div class="alert alert-danger">Ungültige Projekt-ID</div>';
    exit;
}

$conn = utils_connect_sql();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raumbereiche = $_POST['raumbereich'] ?? [];
    if (!is_array($raumbereiche)) $raumbereiche = [$raumbereiche];

    $zusatzRaeume = $_POST['zusatzRaeume'] ?? [];
    if (!is_array($zusatzRaeume)) $zusatzRaeume = [$zusatzRaeume];

    $zusatzElemente = $_POST['zusatzElemente'] ?? [];
    if (!is_array($zusatzElemente)) $zusatzElemente = [$zusatzElemente];

    $mtRelevant = !empty($_POST['mtRelevant']);
    $entfallen = !empty($_POST['entfallen']);
    $nurMitElementen = !empty($_POST['nurMitElementen']);
    $ohneLeereElemente = !isset($_POST['ohneLeereElemente']) || (bool)$_POST['ohneLeereElemente'];
    $transponiert = !empty($_POST['transponiert']);

    try {
        $pivotModel = new PivotTable($conn, $projectID);
        $html = $pivotModel->getElementeJeRaeume(
            $raumbereiche,
            $zusatzRaeume,
            $zusatzElemente,
            $mtRelevant,
            $entfallen,
            $nurMitElementen,
            $ohneLeereElemente,
            $transponiert
        );
        echo $html;

    } catch (Exception $e) {
        http_response_code(500);
        echo "<div class='alert alert-danger'>Fehler: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    $conn->close();
    exit;
}

http_response_code(405);
echo "<div class='alert alert-danger'>Methode nicht erlaubt.</div>";
exit;
