<?php
// Start the session
session_start();

// Connect to the database
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set the character set to UTF-8
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

// Query to retrieve data
$sql = "SELECT 
            tabelle_elemente.ElementID, 
            tabelle_elemente.Bezeichnung, 
            tabelle_räume_has_tabelle_elemente.id, 
            tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
            tabelle_bestandsdaten.Inventarnummer, 
            tabelle_bestandsdaten.Seriennummer, 
            tabelle_bestandsdaten.Anschaffungsjahr, 
            tabelle_bestandsdaten.`Aktueller Ort`, 
            tabelle_geraete.Typ, 
            tabelle_hersteller.Hersteller, 
            tabelle_räume.Raumnr, 
            tabelle_räume.Raumbezeichnung, 
            tabelle_räume.`Raumbereich Nutzer`
        FROM tabelle_hersteller 
        RIGHT JOIN (tabelle_geraete 
        RIGHT JOIN (tabelle_bestandsdaten 
        INNER JOIN (tabelle_elemente 
        INNER JOIN (tabelle_räume 
        INNER JOIN tabelle_räume_has_tabelle_elemente 
        ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
        ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
        ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) 
        ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) 
        ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
        WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = {$_SESSION['projectID']}
        AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0 
        AND tabelle_räume_has_tabelle_elemente.Standort = 1
        ORDER BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr;";

$result = $mysqli->query($sql);

// Check if the query returned any results
if ($result && $result->num_rows > 0) {
    // Set headers for the CSV file
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data_export.csv');

    // Open output stream for writing
    $output = fopen('php://output', 'w');

    // Output the column headings
    fputcsv($output, [
        'Raumbereich Nutzer',
        'Raumnr',
        'Raumbezeichnung',
        'Element-ID',
        'Bezeichnung',
        'Gerät',
        'Inventarnummer',
        'Seriennummer',
        'Anschaffungsjahr',
        'Aktueller Ort'
    ]);

    // Loop through the result set
    while ($row = $result->fetch_assoc()) {
        // Write the data to CSV
        fputcsv($output, [
            $row['Raumbereich Nutzer']."; ",
            $row['Raumnr']."; ",
            $row['Raumbezeichnung']."; ",
            $row['ElementID']."; ",
            $row['Bezeichnung']."; ",
            $row['Hersteller'] . ' - ' . $row['Typ']."; ",
            $row['Inventarnummer']."; ",
            $row['Seriennummer']."; ",
            $row['Anschaffungsjahr']."; ",
            $row['Aktueller Ort']."; "
        ]);
    }

    // Flush output to ensure all data is sent
    fflush($output);

    // Close output stream
    fclose($output);
} else {
    echo "No data found.";
}

// Close the database connection
$mysqli->close();
?>
