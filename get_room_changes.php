<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}

if (!isset($_POST['roomId']) || empty($_POST['roomId'])) {
    die("Ungültige Raum-ID");
}

$mysqli = utils_connect_sql();

$roomId = $mysqli->real_escape_string($_POST['roomId']);
$startDate = $_POST['startDate'] ?? null;
$endDate = $_POST['endDate'] ?? null;

// Prepare SQL query
$query = "SELECT * FROM tabelle_raeume_aenderungen WHERE raum_id = '$roomId'";
$conditions = [];

// Add date range condition if both dates are provided
if (!empty($startDate) && !empty($endDate)) {
    $conditions[] = "Timestamp BETWEEN '$startDate' AND '$endDate'";
}

// Append conditions to the query
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY Timestamp DESC";

// Execute query
$result = $mysqli->query($query);

$output = [];// Define a mapping array for suffixes and their corresponding new field labels
$suffixMapping = [
    '_alt' => '_neu',
    '_copy1' => '', // For _copy1, the old field has no suffix
    'alt' => 'neu' // Assuming 'alt' should be treated like '_alt'
];

while ($row = $result->fetch_assoc()) {
    $changes = [];
    $timestamp = date('d.m.Y H:i', strtotime($row['Timestamp']));
    $user = htmlspecialchars($row['user']);

    // Dynamic field comparison
    foreach ($row as $field => $value) {
        foreach ($suffixMapping as $suffix => $newSuffix) {


            if (str_ends_with($field, $suffix)) {
                $baseField = substr($field, 0, -strlen($suffix)); // Remove the suffix
                if ($newSuffix === '') { // For _copy1, use the base field as the old field
                    $oldVal = $row[$baseField] ?? null;
                    $newVal = $row[$field] ?? null;

                } else { // For _alt or alt, use the base field with _neu as the new field
                    $newField = $baseField . $newSuffix;
                    $oldVal = $row[$field] ?? null;
                    $newVal = $row[$newField] ?? null;
                }

                if ($oldVal != $newVal) {
                    $changes[] = sprintf(
                        "<strong>%s:</strong> %s →
 %s",
                        htmlspecialchars(str_replace('_', ' ', $baseField)),
                        htmlspecialchars( br2nl($oldVal) ?? 'N/A'),
                        htmlspecialchars( br2nl($newVal) ?? 'N/A')
                    );
                }
            }
        }
    }

    if (!empty($changes)) {
        $output[] = "<div class='change-entry mb-3'>
            <div class='change-header text-muted small'>$timestamp - $user</div>
            <div class='change-details'>".implode('<br>',   ($changes))."</div>
        </div><hr>";
    }
}

echo !empty($output)
    ? implode('', $output)
    : "<div class='alert alert-info'>Keine Änderungen gefunden</div>";

