<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }

if (!isset($_GET['roomId']) || empty($_GET['roomId'])) {
    die("Ungültige Raum-ID");
}

$mysqli = utils_connect_sql();

$roomId = $mysqli->real_escape_string($_GET['roomId']);

// Get all changes for the room
$result = $mysqli->query("
    SELECT *
    FROM tabelle_raeume_aenderungen 
    WHERE raum_id = '$roomId'
    ORDER BY Timestamp DESC
");

$output = [];
while ($row = $result->fetch_assoc()) {
    $changes = [];
    $timestamp = date('d.m.Y H:i', strtotime($row['Timestamp']));
    $user = htmlspecialchars($row['user']);

    // Dynamic field comparison
    foreach ($row as $field => $value) {
        if (str_ends_with($field, '_alt')) {
            $baseField = substr($field, 0, -4);
            $newField = $baseField.'_neu';

            $oldVal = $row[$field] ?? null;
            $newVal = $row[$newField] ?? null;

            if ($oldVal != $newVal) {
                $changes[] = sprintf(
                    "<strong>%s:</strong> %s → %s",
                    htmlspecialchars(str_replace('_', ' ', $baseField)),
                    htmlspecialchars($oldVal ?? 'N/A'),
                    htmlspecialchars($newVal ?? 'N/A')
                );
            }
        }
    }

    if (!empty($changes)) {
        $output[] = "<div class='change-entry mb-3'>
            <div class='change-header text-muted small'>$timestamp - $user</div>
            <div class='change-details'>".implode('<br>', $changes)."</div>
        </div><hr>";
    }
}

echo !empty($output)
    ? implode('', $output)
    : "<div class='alert alert-info'>Keine Änderungen gefunden</div>";
