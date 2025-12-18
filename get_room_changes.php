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
$types = "s";

if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND Timestamp BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
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
    $user = htmlspecialchars($row['user']);

    foreach ($row as $field => $value) {
        foreach ($suffixMapping as $suffix => $newSuffix) {
            if (str_ends_with($field, $suffix)) {
                $baseField = substr($field, 0, -strlen($suffix));
                if ($newSuffix === '') {
                    $oldVal = $row[$baseField] ?? null;
                    $newVal = $row[$field] ?? null;
                } else {
                    $newField = $baseField . $newSuffix;
                    $oldVal = $row[$field] ?? null;
                    $newVal = $row[$newField] ?? null;
                }
                if ($oldVal != $newVal) {
                    $changes[] = sprintf(
                        "<strong>%s:</strong> %s → %s",
                        htmlspecialchars(str_replace('_', ' ', $baseField)),
                        htmlspecialchars(br2nl($oldVal) ?? 'N/A'),
                        htmlspecialchars(br2nl($newVal) ?? 'N/A')
                    );
                }
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
