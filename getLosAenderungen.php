<?php
require_once 'utils/_utils.php';
check_Login();
header('Content-Type: application/json; charset=utf-8');

$request = $_REQUEST;
$mysqli = utils_connect_sql();

$start = intval($request['start'] ?? 0);
$length = intval($request['length'] ?? 10);
$draw = intval($request['draw'] ?? 1);
$searchValue = trim($request['search']['value'] ?? '');


$totalSql = "SELECT COUNT(*) as total FROM tabelle_rb_aenderung 
             WHERE COALESCE(`tabelle_Lose_Intern_idtabelle_Lose_Intern`, -1) != COALESCE(`tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1`, -1) 
             OR COALESCE(`tabelle_Lose_Extern_idtabelle_Lose_Extern`, -1) != COALESCE(`tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1`, -1)";
$totalResult = mysqli_query($mysqli, $totalSql);
$totalRecords = mysqli_fetch_assoc($totalResult)['total'] ?? 0;

// 2. Gefilterte Anzahl
$whereConditions = [
    "COALESCE(`tabelle_Lose_Intern_idtabelle_Lose_Intern`, -1) != COALESCE(`tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1`, -1) 
     OR COALESCE(`tabelle_Lose_Extern_idtabelle_Lose_Extern`, -1) != COALESCE(`tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1`, -1)"
];
if (!empty($searchValue)) {
    $searchTerm = "%$searchValue%";
    $whereConditions[] = "(id LIKE '$searchTerm' OR Kurzbeschreibung LIKE '$searchTerm' OR `user` LIKE '$searchTerm')";
}
$whereClause = implode(' AND ', $whereConditions);

$filterSql = "SELECT COUNT(*) as total FROM tabelle_rb_aenderung WHERE $whereClause";
$filterResult = mysqli_query($mysqli, $filterSql);
$recordsFiltered = mysqli_fetch_assoc($filterResult)['total'] ?? 0;

// 3. Daten - KORREKTE FELDNAMEN!
$orderColumnIdx = $request['order'][0]['column'] ?? 7; // Timestamp
$orderColumnDir = $request['order'][0]['dir'] ?? 'desc';

$columns = [
    0=>'id', 1=>'id', 2=>'Kurzbeschreibung', 3=>'tabelle_Lose_Intern_idtabelle_Lose_Intern',
    4=>'tabelle_Lose_Extern_idtabelle_Lose_Extern', 5=>'Anzahl', 6=>'Anschaffung',
    7=>'Timestamp', 8=>'user', 9=>'idtabelle_rb_aenderung'
];
$orderBy = $columns[$orderColumnIdx] ?? 'Timestamp';

$dataSql = "SELECT idtabelle_rb_aenderung, id, Kurzbeschreibung, Anzahl, Anschaffung,
            tabelle_Lose_Intern_idtabelle_Lose_Intern, tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1,
            tabelle_Lose_Extern_idtabelle_Lose_Extern, tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1,
            Timestamp, `user`
            FROM tabelle_rb_aenderung WHERE $whereClause
            ORDER BY `$orderBy` $orderColumnDir LIMIT $length OFFSET $start";

$result = mysqli_query($mysqli, $dataSql) or die(mysqli_error($mysqli)); // DEBUG!

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        $row['idtabelle_rb_aenderung'], // 0 ID
        $row['id'], // 1 LOS-ID
        h($row['Kurzbeschreibung'] ?? '-'), // 2
        '-', // 3 Lose Intern (vereinfacht)
        '-', // 4 Lose Extern (vereinfacht)
        h($row['Anzahl'] ?? '-'), // 5
        h($row['Anschaffung'] ?? '-'), // 6
        date('d.m.Y H:i', strtotime($row['Timestamp'] ?? 'now')), // 7
        h($row['user'] ?? '-'), // 8
        '<button class="btn btn-sm btn-outline-primary view-details" data-id="' . $row['idtabelle_rb_aenderung'] . '">
             <i class="fas fa-eye"></i></button>' // 9
    ];
}

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
]);
?>
