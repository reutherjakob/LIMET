<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
require 'vendor/autoload.php'; // Include PhpSpreadsheet autoloader (make sure this path is correct)

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$mysqli = utils_connect_sql();

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
            tabelle_räume.`Raumbereich Nutzer`,
            costs.Kosten
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
        LEFT JOIN (
            SELECT 
                tabelle_projekt_varianten_kosten.Kosten,
                tabelle_räume_has_tabelle_elemente.id AS element_id
            FROM tabelle_projekt_varianten_kosten
            INNER JOIN tabelle_räume_has_tabelle_elemente
            ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            WHERE tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = {$_SESSION['projectID']}
        ) AS costs
        ON tabelle_räume_has_tabelle_elemente.id = costs.element_id
        WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = {$_SESSION['projectID']}
        AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0 
        AND tabelle_räume_has_tabelle_elemente.Standort = 1
        ORDER BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr;";

$result = $mysqli->query($sql);

// Check if the query returned any results
if ($result && $result->num_rows > 0) {
    // Create a new spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the header row
    $sheet->setCellValue('A1', 'Raumbereich Nutzer');
    $sheet->setCellValue('B1', 'Raumnr');
    $sheet->setCellValue('C1', 'Raumbezeichnung');
    $sheet->setCellValue('D1', 'Element-ID');
    $sheet->setCellValue('E1', 'Bezeichnung');
    $sheet->setCellValue('F1', 'Gerät');
    $sheet->setCellValue('G1', 'Inventarnummer');
    $sheet->setCellValue('H1', 'Seriennummer');
    $sheet->setCellValue('I1', 'Anschaffungsjahr');
    $sheet->setCellValue('J1', 'Aktueller Ort');
    $sheet->setCellValue('K1', 'Kosten');

    // Set the row counter
    $rowCounter = 2;

    // Loop through the result set
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A$rowCounter", $row['Raumbereich Nutzer']);
        $sheet->setCellValue("B$rowCounter", $row['Raumnr']);
        $sheet->setCellValue("C$rowCounter", $row['Raumbezeichnung']);
        $sheet->setCellValue("D$rowCounter", $row['ElementID']);
        $sheet->setCellValue("E$rowCounter", $row['Bezeichnung']);
        $sheet->setCellValue("F$rowCounter", $row['Hersteller'] . ' - ' . $row['Typ']);
        $sheet->setCellValue("G$rowCounter", $row['Inventarnummer']);
        $sheet->setCellValue("H$rowCounter", $row['Seriennummer']);
        $sheet->setCellValue("I$rowCounter", $row['Anschaffungsjahr']);
        $sheet->setCellValue("J$rowCounter", $row['Aktueller Ort']);
        $sheet->setCellValue("K$rowCounter", $row['Kosten']);
        $rowCounter++;
    }

    // Set the response headers to output the file as Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="data_export.xlsx"');
    header('Cache-Control: max-age=0');

    // Create Excel file and write it to the output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
} else {
    echo "No data found.";
}

$mysqli->close();



