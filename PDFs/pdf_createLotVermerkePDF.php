<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";
include "pdf_createBericht_LOGO.php";

class MYPDFl extends TCPDF
{
    public function Header()
    {
        get_header_logo($this);
        $this->SetFont('helvetica', '', 10);
        $mysqli = utils_connect_sql();
        $sql = "SELECT tabelle_projekte.Projektname, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern
                FROM tabelle_lose_extern INNER JOIN tabelle_projekte ON tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
                WHERE (((tabelle_lose_extern.idtabelle_Lose_Extern)=" . filter_input(INPUT_GET, 'losID') . "));";
        $result = $mysqli->query($sql);
        while ($row = $result->fetch_assoc()) {
            $title = "Vermerke zu Los " . $row['LosNr_Extern'] . " - " . $row['LosBezeichnung_Extern'];
        }
        $mysqli->close();
        $this->Cell(0, 0, $title, 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $this->Ln();
        $this->cell(0, 0, '', 'B', 0, 'L');
    }
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Cell(0, 10, 'Seite ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

    public function LoadData($file)
    {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach ($lines as $line) {
            $data[] = explode(';', chop($line));
        }
        return $data;
    }


    // Topics table
    public function topicsTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.1);
        $this->SetFont('', '', '9');
        //
        $w = array(115, 20, 25, 20);
        $num_headers = count($header);
        $this->Ln();
        $this->SetFillColor(244, 244, 244);
        $this->SetTextColor(0);


        // Data        
        $fill = 0;
        $gruppenID = 0;
        foreach ($data as $row) {
            $this->SetFont('helvetica', '', '8');

            $betreffText = "";
            if (null != ($row['Raumnr'])) {
                $betreffText = $betreffText . 'Betrifft Raum: ' . $row['Raumnr'] . " " . $row['Raumbezeichnung'] . "\n";
            }

            $rowHeight1 = $this->getStringHeight($w[0], $row['Vermerktext'], false, true, '', 1);
            $rowHeight4 = $this->getStringHeight($w[0], $betreffText, false, true, '', 1);
            $rowHeight2 = $this->getStringHeight($w[2], $row['Name'] . "/" . $row['Faelligkeit'], false, true, '', 1);
            $rowHeight3 = $this->getStringHeight($w[0], $row['Gruppenname'] . "/" . $row['Ort'] . "/" . $row['Datum'], false, true, '', 1);

            if ($rowHeight1 + $rowHeight4 > $rowHeight2) {
                $rowHeight = $rowHeight1 + $rowHeight4;
            } else {
                $rowHeight = $rowHeight2;
                $rowHeight1 = $rowHeight - $rowHeight4;
            }

            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $this->GetY();
            if (($y + $rowHeight) >= 270) {
                $this->AddPage();
            }

            if ($gruppenID != $row['idtabelle_Vermerkgruppe']) {
                $fill = 1;
                $this->SetFont('', 'B', '9');
                $this->Ln($rowHeight3);
                $this->MultiCell($w[0] + $w[1] + $w[2] + $w[3], $rowHeight3, $row['Gruppenname'] . "/" . $row['Ort'] . "/" . $row['Datum'], 1, 'L', $fill, 0, '', '');
                $this->Ln();
                $this->SetFont('', 'B', '8');
                for ($i = 0; $i < $num_headers; ++$i) {
                    $this->MultiCell($w[$i], $rowHeight3, $header[$i], 1, 'L', 0, 0, '', '');
                }
                $this->Ln();
                $gruppenID = $row['idtabelle_Vermerkgruppe'];
                $fill = 0;
            }

            $this->SetFont('', 'I', '7');
            $this->MultiCell($w[0], $rowHeight4, $betreffText, 'LTR', 'L', $fill, 0, '', '');
            $this->SetFont('', '', '8');
            $this->MultiCell($w[1], $rowHeight, $row['Vermerkart'], 1, 'L', $fill, 0, '', '');
            if ($row['Vermerkart'] == 'Bearbeitung') {
                $text = $row['Name'] . "\n" . $row['Faelligkeit'];
                $this->MultiCell($w[2], $rowHeight, $text, 1, 'L', $fill, 0, '', '');
                $this->SetFont('zapfdingbats', '', 8);
                if ($row['Bearbeitungsstatus'] == '0') {
                    $this->MultiCell($w[3], $rowHeight, TCPDF_FONTS::unichr(54), 1, 'L', $fill, 0, '', '');
                } else {
                    $this->MultiCell($w[3], $rowHeight, TCPDF_FONTS::unichr(52), 1, 'L', $fill, 0, '', '');
                }
                $this->SetFont('helvetica', '', '8');
            } else {
                $this->MultiCell($w[2], $rowHeight, '', 1, 'L', $fill, 0, '', '');
                $this->MultiCell($w[3], $rowHeight, '', 1, 'L', $fill, 0, '', '');
            }

            $this->Ln($rowHeight4);
            $this->MultiCell($w[0], $rowHeight1, $row['Vermerktext'], 'LRB', 'L', $fill, 0, '', '');
            $this->Ln();
        }

    }
}

$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$pdf = new MYPDFl(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Los Vermerke");

$mysqli = utils_connect_sql();
//Kopfdaten
$sql = "SELECT tabelle_projekte.Projektname, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern
                FROM tabelle_lose_extern INNER JOIN tabelle_projekte ON tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
                WHERE (((tabelle_lose_extern.idtabelle_Lose_Extern)=" . filter_input(INPUT_GET, 'losID') . "));";

$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {

    $title = "Projekt: " . $row['Projektname'] . "\n" . "Los: " . $row['LosNr_Extern'] . " - " . $row['LosBezeichnung_Extern'];
    $rowHeight1 = $pdf->getStringHeight(180, $title, false, true, '', 1);
    $pdf->MultiCell(0, $rowHeight1, $title, 1, 'L', 0, 0, '', '', true);
}
$pdf->Ln(5);


// ---------------------------------------------------------
// print topics
$pdf->Ln();

$topics_table_header = array('Text', 'Typ', 'Wer/Bis wann', 'Status');

// data loading
$sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.idtabelle_Vermerke, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe
FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern)=" . filter_input(INPUT_GET, 'losID') . "))
ORDER BY tabelle_Vermerkgruppe.Datum DESC , tabelle_Vermerke.Erstellungszeit DESC;";


$result = $mysqli->query($sql);
$dataVermerke = array();
while ($row = $result->fetch_assoc()) {
    $dataVermerke[$row['idtabelle_Vermerke']]['Vermerktext'] = $row['Vermerktext'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Name'] = $row['Name'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Vorname'] = $row['Vorname'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Faelligkeit'] = $row['Faelligkeit'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Vermerkart'] = $row['Vermerkart'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Raumnr'] = $row['Raumnr'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Raumbezeichnung'] = $row['Raumbezeichnung'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Bearbeitungsstatus'] = $row['Bearbeitungsstatus'];
    $dataVermerke[$row['idtabelle_Vermerke']]['idtabelle_Vermerkgruppe'] = $row['idtabelle_Vermerkgruppe'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Gruppenname'] = $row['Gruppenname'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Ort'] = $row['Ort'];
    $dataVermerke[$row['idtabelle_Vermerke']]['Datum'] = $row['Datum'];
}
$pdf->topicsTable($topics_table_header, $dataVermerke);

ob_end_clean();
$mysqli->close();
$pdf->Output(getFileName(  'Lose_Vermerke'), 'I');

//============================================================+
// END OF FILE
//============================================================+

