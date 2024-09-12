<?php
session_start();
require_once('tcpdf/tcpdf.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $device4 = $_POST['device4'];

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Device Parameters');
    $pdf->SetSubject('Device Parameters');
    $pdf->SetKeywords('TCPDF, PDF, device, parameters');

    // Add a page
    $pdf->AddPage();

    // Set content
//    $html = '<h1>Device 4 Parameters</h1>';
//    $html .= '<table border="1" cellpadding="4">';
//    $html .= '<tr><th>Parameter</th><th>Value</th></tr>';
//    foreach ($device4 as $key => $value) {
//        $html .= '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
//    }
//    $html .= '</table>';

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->multicell(0,0,"Text"); 
    // Close and output PDF document
    $pdf->Output('device_parameters.pdf', 'I');
} 
 