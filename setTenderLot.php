<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

// Establish database connection
$mysqli = utils_connect_sql();

// Define input fields and their corresponding database columns
$fields = [
    'LosNr_Extern' => 'losNr',
    'LosBezeichnung_Extern' => 'losName',
    'Ausführungsbeginn' => 'losDatum',
    'Vergabesumme' => 'lotSum',
    'Vergabe_abgeschlossen' => 'lotVergabe',
    'Versand_LV' => 'lotLVSend',
    'Verfahren' => 'lotVerfahren',
    'Bearbeiter' => 'lotLVBearbeiter',
    'Notiz' => 'lotNotice',
    'Kostenanschlag' => 'kostenanschlag',
    'Budget' => 'budget',
    'tabelle_lieferant_idTABELLE_Lieferant' => 'lotAuftragnehmer'
];

// Validate lotID from session
$lotID = getPostInt('lotID',0);
if ($lotID <= 0) {
    die("Ungültige Lot-ID.");
}

// Collect and sanitize input values
$data = [];
$types = '';
$setParts = [];
$allowedFields = array_values($fields);

foreach ($fields as $dbColumn => $postField) {
    $value = filter_input(INPUT_POST, $postField, FILTER_DEFAULT); // ✅ Fixed: No deprecated constant
    $value = trim($value ?? '');


    if ($value !== null && $value !== false && $value !== '') {
        // Special handling for dates
        if (in_array($postField, ['losDatum', 'lotLVSend'])) {
            $date = DateTime::createFromFormat('d.m.Y', $value);
            if ($date) {
                $value = $date->format('Y-m-d');
            } else {
                continue; // Skip invalid dates
            }
        }

        // Skip supplier if 0
        if ($postField === 'lotAuftragnehmer' && $value == '0') {
            continue;
        }

        $data[$dbColumn] = $value;
        $setParts[] = "`$dbColumn` = ?";
        $types .= 's'; // All fields treated as strings for simplicity
    }
}

if (empty($setParts)) {
    echo "Keine Felder zum Aktualisieren.";
    $mysqli->close();
    exit;
}

// Build dynamic prepared UPDATE statement
$sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern` SET " . implode(', ', $setParts) . " WHERE `idtabelle_Lose_Extern` = ?";
$types .= 'i'; // lotID is integer
$data['lotID'] = $lotID; // Add lotID for WHERE clause

// Prepare and execute
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$params = array_values($data);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo "Los erfolgreich aktualisiert! " . $stmt->affected_rows . " Zeile(n) betroffen.";
} else {
    echo "Fehler beim Ausführen: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
