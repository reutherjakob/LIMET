<?php
//25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$roomId = getPostInt('roomId');
$startDate = $_POST['startDate'] ?? null;
$endDate = $_POST['endDate'] ?? null;

$query = "SELECT * FROM tabelle_raeume_aenderungen WHERE raum_id = ?";
$params = [$roomId];
$types = "i";

if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND Timestamp BETWEEN ? AND ?";
    $params[] = $startDate . ' 00:00:00';
    $params[] = $endDate . ' 23:59:59';
    $types .= "ss";
}

$query .= " ORDER BY Timestamp DESC";


$stmt = $mysqli->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$output = [];
$suffixMapping = [
    '_alt' => '_neu',
    '_copy1' => '',
    'alt' => 'neu'
];


while ($row = $result->fetch_assoc()) {
    $changes = [];
    $timestamp = date('d.m.Y H:i', strtotime($row['Timestamp']));
    $user = htmlspecialchars($row['user'] ?? 'Unbekannt');

    foreach ($row as $field => $value) {
        if (strpos($field, '_alt') !== false || strpos($field, '_copy1') !== false) {
            $baseField = preg_replace('/_(alt|copy1|_alt|_copy1)$/', '', $field);
            $oldVal = $row[$baseField . '_alt'] ?? $row[$field] ?? null;
            $newVal = $row[$baseField . '_neu'] ?? $row[$baseField] ?? null;

            if ($oldVal != $newVal && ($oldVal !== null || $newVal !== null)) {
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
            <div class='change-details'>" . implode('<br>', $changes) . "</div>
        </div><hr>";
    }
}

echo !empty($output)
    ? implode('', $output)
    : "<div class='alert alert-info'>Keine Änderungen gefunden</div>";

$stmt->close();
$mysqli->close();
?>