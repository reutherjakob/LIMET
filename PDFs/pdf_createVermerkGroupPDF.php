<?php
#2025done

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="document.pdf"');

require_once '../utils/_utils.php';
require_once 'pdf_createBericht_LOGO.php';
require_once '../TCPDF-main/TCPDF-main/tcpdf.php';
require_once '_pdf_createBericht_utils.php';


$document_out_title_components = "";
$gruppenID = filter_input(INPUT_GET, 'gruppenID', FILTER_VALIDATE_INT);

$projectID = isset($_SESSION["projectID"]) ? (int)$_SESSION["projectID"] : null;
if (!$gruppenID || !$projectID) {
    die('Ungültige Eingabe.');
}

class MYPDF extends TCPDF
{
    public function Header(): void
    {
        get_header_logo($this);
        $this->SetFont('helvetica', '', 10);
        $mysqli = utils_connect_sql();
        $gruppenID = filter_input(INPUT_GET, 'gruppenID', FILTER_VALIDATE_INT);
        $stmt = $mysqli->prepare(
            "SELECT tabelle_Vermerkgruppe.Gruppenname
             FROM tabelle_Vermerkgruppe
             WHERE tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe=?"
        );
        $stmt->bind_param("i", $gruppenID);
        $stmt->execute();
        $stmt->bind_result($title);
        $stmt->fetch();
        $stmt->close();
        $mysqli->close();
        $this->Cell(0, 0, $title, 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $this->Ln();
        $this->cell(0, 0, '', 'B', 0, 'L');
    }

    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Cell(0, 10, 'Seite ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

    // Load table data from file
    public function LoadData($file)
    {
        $lines = file($file);
        $data = array();
        foreach ($lines as $line) {
            $data[] = explode(';', chop($line));
        }
        return $data;
    }

    public function verteilerTable($header, $data)
    {
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.1);
        $this->SetFont('', '', '9');
        $w = array(40, 50, 35, 35, 10, 10);
        $num_headers = count($header);
        $this->Cell(0, 6, 'Teilnehmer/Verteiler:', 0, 0, 'L', 0);
        $this->Ln();
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 6, $header[$i], 1, 0, 'L', 0);
        }
        $this->Ln();
        $this->SetFillColor(244, 244, 244);
        $this->SetTextColor(0);
        $this->SetFont('', '', '8');
        $fill = 0;
        foreach ($data as $row) {
            $cellData = [
                $row['Name'] . " " . $row['Vorname'],
                $row['Mail'],
                $row['Organisation'],
                $row['Zuständigkeit'],
            ];
            $cellWidths = [$w[0], $w[1], $w[2], $w[3]];
            $lineHeight = 5; // height of one line

            // Calculate max number of lines needed per cell in row
            $maxLines = 1;
            foreach ($cellData as $i => $text) {
                $lines = $this->getNumLines($text, $cellWidths[$i]);
                if ($lines > $maxLines) {
                    $maxLines = $lines;
                }
            }
            $rowHeight = $lineHeight * $maxLines;

            // Save current X,Y position
            $x = $this->GetX();
            $y = $this->GetY();

            foreach ($cellData as $i => $text) {
                $this->MultiCell(
                    $cellWidths[$i],
                    $rowHeight,
                    $text,
                    1,
                    'L',
                    $fill,
                    0,
                    '',
                    '',
                    true,
                    0,
                    false,
                    true,
                    $rowHeight,
                    'M'
                );
                // Position cursor for next cell, same vertical start
                $this->SetXY($x + array_sum(array_slice($cellWidths, 0, $i + 1)), $y);
            }

            // Output last two columns with symbol cells, same height
            $this->SetFont('zapfdingbats', '', 8);
            $this->SetXY($x + array_sum($cellWidths), $y);
            $this->Cell(
                $w[4],
                $rowHeight,
                $row['Anwesenheit'] == '0' ? TCPDF_FONTS::unichr(54) : TCPDF_FONTS::unichr(52),
                1,
                0,
                'C',
                $fill
            );
            $this->Cell(
                $w[5],
                $rowHeight,
                $row['Verteiler'] == '0' ? TCPDF_FONTS::unichr(54) : TCPDF_FONTS::unichr(52),
                1,
                0,
                'C',
                $fill
            );

            $this->SetFont('helvetica', '', 8);
            $this->Ln($rowHeight);
            $fill = !$fill;
        }

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function topicsTable($header, $data)
    {
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.1);
        $this->SetFont('', '', '9');
        $w = array(145, 10, 25);
        $num_headers = count($header);
        $this->SetFillColor(244, 244, 244);
        $this->SetTextColor(0);
        $fill = 0;
        $untergruppenID = 0;
        foreach ($data as $row) {
            if (trim($row['Vermerktext']) !== "") {
                $this->SetFont('helvetica', '', '8');
                $betreffText = "";
                if (!empty($row['Räume'])) {
                    $raeumeArray = array_filter(array_map('trim', explode(',', $row['Räume'])));
                    $anzahl = count($raeumeArray);
                    $label = $anzahl > 1 ? 'Räume' : 'Raum';
                    $betreffText .= "Betrifft $label: " . $row['Räume'] . "\n";
                }

                if (!empty($row['LosNr_Extern'])) {
                    $betreffText .= 'Betrifft Los: ' . $row['LosNr_Extern'] . " " . $row['LosBezeichnung_Extern'] . "\n";
                }
                if ($row['Vermerkart'] === 'Bearbeitung') {
                    $textNameFälligkeit = $row['Name'] . "\n" . $row['Faelligkeit'];
                    $textNameFälligkeit .= ($row['Bearbeitungsstatus'] === 0) ? "\nOffen" : "\nErledigt";
                } else {
                    $textNameFälligkeit = "";
                }
                $rowHeight1 = $this->getStringHeight($w[0], $row['Vermerktext'], false, true, '', 1);
                $rowHeight4 = $this->getStringHeight($w[0], $betreffText, false, true, '', 1);
                $rowHeight2 = $this->getStringHeight($w[2], $textNameFälligkeit, false, true, '', 1);
                $rowHeight3 = $this->getStringHeight($w[0], $row['Untergruppennummer'] . " " . $row['Untergruppenname'], false, true, '', 1);
                $rowHeight = max($rowHeight1 + $rowHeight4, $rowHeight2);
                if ($untergruppenID != $row['idtabelle_Vermerkuntergruppe']) {
                    $y = $this->GetY();
                    if (($y + 2 * $rowHeight3 + $rowHeight) >= 270) {
                        $this->AddPage();
                    } else {
                        $this->Ln($rowHeight3);
                    }
                    $fill = 1;
                    $this->SetFont('', 'B', '9');
                    $this->MultiCell($w[0] + $w[1] + $w[2], $rowHeight3, $row['Untergruppennummer'] . ") " . $row['Untergruppenname'], 1, 'L', $fill, 0, '', '');
                    $this->Ln();
                    $this->SetFont('', 'B', '8');
                    for ($i = 0; $i < $num_headers; ++$i) {
                        $this->MultiCell($w[$i], $rowHeight3, $header[$i], 1, 'L', 0, 0, '', '');
                    }
                    $this->Ln();
                    $untergruppenID = $row['idtabelle_Vermerkuntergruppe'];
                    $fill = 0;
                }
                $y = $this->GetY();
                if (($y + $rowHeight1) >= 260) {
                    $this->AddPage();
                }
                $this->SetFont('', 'I', '7');
                $this->MultiCell($w[0], $rowHeight4, $betreffText, 'LTR', 'L', $fill, 0, '', '');
                $this->SetFont('', '', '8');


                $softHyphen = "\xC2\xAD";
                if ($row['Vermerkart'] === "Bearbeitung") {
                    $prettyText = "B";
                } else if ($row['Vermerkart'] === "Info") {
                    $prettyText = "I";
                } else if ($row['Vermerkart'] === "Freigegeben") {
                    $prettyText = "F";
                } else if ($row['Vermerkart'] === "Nutzerwunsch") {
                    $prettyText = "N";
                }
                $this->MultiCell($w[1], $rowHeight, $prettyText, 1, 'C', $fill, 0, '', '');


                if ($row['Vermerkart'] == 'Bearbeitung') {
                    $this->MultiCell($w[2], $rowHeight, $textNameFälligkeit, 1, 'L', $fill, 0, '', '');
                } else {
                    $this->MultiCell($w[2], $rowHeight, '', 1, 'L', $fill, 0, '', '');
                }
                $this->Ln($rowHeight4);
                $this->MultiCell($w[0], $rowHeight - $rowHeight4, $row['Vermerktext'], 'LRB', 'L', $fill, 1, '', '');
            }
        }
    }
}



// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
$pdf->SetTitle('Dokumentation' . $_SESSION['projectName']);
$pdf->SetSubject('');
$pdf->SetKeywords('');

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();
$pdf->SetY(21);
$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare(
    "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Verfasser, 
        DATE_FORMAT(tabelle_Vermerkgruppe.Startzeit, '%H:%i') AS Startzeit, DATE_FORMAT(tabelle_Vermerkgruppe.Endzeit, '%H:%i') AS Endzeit,
        tabelle_Vermerkgruppe.Datum, tabelle_projekte.Projektname
     FROM tabelle_Vermerkgruppe INNER JOIN tabelle_projekte
        ON tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
     WHERE tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = ?"
);
$stmt->bind_param("i", $gruppenID);
$stmt->execute();
$stmt->bind_result($gruppenname, $gruppenart, $ort, $verfasser, $startzeit, $endzeit, $datum, $projektname);
while ($stmt->fetch()) {
    $title = "Projekt: $projektname\nThema: $gruppenname\nDatum: $datum von $startzeit bis $endzeit\nOrt: $ort";
    $document_out_title_components .= $gruppenart . "_" . $gruppenname . "_";
    $rowHeight1 = $pdf->getStringHeight(180, $title, false, true, '', 1);
    $pdf->MultiCell(0, $rowHeight1, $title, 1, 'L', 0, 0, '', '', true);
}
$stmt->close();
$pdf->Ln(1);

$verteiler_table_header = array('Name', 'Mail', 'Organisation', 'Rolle', 'Anw.', 'Vert.');

$stmt = $mysqli->prepare(
    "SELECT
        tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen,
        tabelle_ansprechpersonen.Name,
        tabelle_ansprechpersonen.Vorname,
        tabelle_ansprechpersonen.Mail,
        tabelle_organisation.Organisation,
        tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.Anwesenheit,
        tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.Verteiler,
        tabelle_projektzuständigkeiten.Zuständigkeit
     FROM tabelle_organisation
        INNER JOIN tabelle_projekte_has_tabelle_ansprechpersonen
            ON tabelle_organisation.idtabelle_organisation = tabelle_projekte_has_tabelle_ansprechpersonen.tabelle_organisation_idtabelle_organisation
        INNER JOIN tabelle_ansprechpersonen
            ON tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen = tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
        INNER JOIN tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen
            ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen
        INNER JOIN tabelle_projektzuständigkeiten
            ON tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten = tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten
     WHERE tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ?
       AND tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte = ?"
);
$stmt->bind_param("ii", $gruppenID, $projectID);
$stmt->execute();
$result = $stmt->get_result();
$gruppenTeilnehmer = array();
while ($row = $result->fetch_assoc()) {
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Name'] = $row['Name'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Vorname'] = $row['Vorname'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Mail'] = $row['Mail'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Organisation'] = $row['Organisation'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Anwesenheit'] = $row['Anwesenheit'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Verteiler'] = $row['Verteiler'];
    $gruppenTeilnehmer[$row['idTABELLE_Ansprechpersonen']]['Zuständigkeit'] = $row['Zuständigkeit'];
}
$stmt->close();
$pdf->Ln();
$pdf->verteilerTable($verteiler_table_header, $gruppenTeilnehmer);

$pdf->Ln(2);
$topics_table_header = array('Text', 'Typ', 'Wer/Bis wann');

$stmt = $mysqli->prepare(
    "SELECT
        v.idtabelle_Vermerke,
        v.Vermerktext,
        v.Vermerkart,
        v.Bearbeitungsstatus,
        v.Faelligkeit,
        u.Untergruppennummer,
        u.Untergruppenname,
        GROUP_CONCAT(DISTINCT CONCAT_WS(' ', r.Raumnr, r.Raumbezeichnung) SEPARATOR ', ') AS Räume,
        GROUP_CONCAT(DISTINCT a.Name SEPARATOR ', ') AS Name,
        v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe,
        le.LosNr_Extern,
        le.LosBezeichnung_Extern
    FROM tabelle_Vermerke v
    INNER JOIN tabelle_Vermerkuntergruppe u ON u.idtabelle_Vermerkuntergruppe = v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe
    LEFT JOIN tabelle_vermerke_has_tabelle_räume vr ON v.idtabelle_Vermerke = vr.tabelle_vermerke_idTabelle_vermerke
    LEFT JOIN tabelle_räume r ON vr.tabelle_räume_idTabelle_räume = r.idTABELLE_Räume
    LEFT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen va ON v.idtabelle_Vermerke = va.tabelle_Vermerke_idtabelle_Vermerke
    LEFT JOIN tabelle_ansprechpersonen a ON va.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen = a.idTABELLE_Ansprechpersonen
    LEFT JOIN tabelle_lose_extern le ON v.tabelle_lose_extern_idtabelle_Lose_Extern = le.idtabelle_Lose_Extern
    WHERE u.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ?
    GROUP BY v.idtabelle_Vermerke
    ORDER BY u.Untergruppennummer"
);
$stmt->bind_param("i", $gruppenID);
$stmt->execute();
$result = $stmt->get_result();

$dataVermerke = [];
while ($row = $result->fetch_assoc()) {
    $vermerkID = $row['idtabelle_Vermerke'];
    $dataVermerke[$vermerkID]['idtabelle_Vermerkuntergruppe'] = $row['tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe'];
    $dataVermerke[$vermerkID]['Untergruppennummer'] = $row['Untergruppennummer'];
    $dataVermerke[$vermerkID]['Untergruppenname'] = $row['Untergruppenname'];
    $dataVermerke[$vermerkID]['Vermerktext'] = $row['Vermerktext'];
    $dataVermerke[$vermerkID]['Bearbeitungsstatus'] = (int)$row['Bearbeitungsstatus'];
    $dataVermerke[$vermerkID]['Name'] = $row['Name'];
    $dataVermerke[$vermerkID]['Faelligkeit'] = $row['Faelligkeit'];
    $dataVermerke[$vermerkID]['Vermerkart'] = $row['Vermerkart'];
    $dataVermerke[$vermerkID]['Räume'] = $row['Räume'];
    $dataVermerke[$vermerkID]['LosNr_Extern'] = $row['LosNr_Extern'];
    $dataVermerke[$vermerkID]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];
}
$stmt->close();

$pdf->topicsTable($topics_table_header, $dataVermerke);

$pdf->SetFont('helvetica', '', '6');
$pdf->Ln(2);
$outstr = "Hinweis: Sollten Einwände gegen Inhalte dieses Protokolls bestehen, so werden die Empfänger ersucht, diese Einwände im Rahmen der nächsten Besprechung mündlich oder bis spätestens 10 Tage nach Erhalt des Protokolls schriftlich vorzubringen, andernfalls wird allgemeines Einverständnis angenommen. \nDie Verteilung erfolgt ausschließlich über Email. \nVermerk Typ Legende: I - Info; B - Bearbeitung; N - Anforderung der Nutzenden \nVerfasst von:" . $verfasser;
$height = $pdf->getStringHeight(180, $outstr, false, true, '', 1);
$y = $pdf->GetY();
if (($y + $height) >= 275) {
    $pdf->AddPage();
}
$pdf->MultiCell(180, 5, $outstr, 0, 'L', 0, 1);
ob_end_clean();
$pdf->Output(getFileName($document_out_title_components), 'I');
?>
