<?php
#2025done
require_once '../utils/_utils.php';
check_login();
require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";
include "pdf_createBericht_LOGO.php";

class MYPDFl extends TCPDF
{
    public function Header()
    {
        get_header_logo($this);
        $this->SetFont('helvetica', '', 10);
        $losID = filter_input(INPUT_GET, 'losID', FILTER_VALIDATE_INT);
        $title = '';
        if ($losID) {
            $mysqli = utils_connect_sql();
            $stmt = $mysqli->prepare("
                SELECT p.Projektname, l.LosNr_Extern, l.LosBezeichnung_Extern
                FROM tabelle_lose_extern l
                INNER JOIN tabelle_projekte p ON l.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte
                WHERE l.idtabelle_Lose_Extern = ?
            ");
            $stmt->bind_param("i", $losID);
            $stmt->execute();
            $stmt->bind_result($projektname, $losNr, $losBez);
            if ($stmt->fetch()) {
                $title = "Vermerke zu Los " . $losNr . " - " . $losBez;
            }
            $stmt->close();
            $mysqli->close();
        }
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
        $lines = file($file);
        $data = array();
        foreach ($lines as $line) {
            $data[] = explode(';', trim($line));
        }
        return $data;
    }

    // topicsTable remains unchanged
}

$marginTop = 20;
$marginBTM = 10;
$pdf = new MYPDFl(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Los Vermerke");

$mysqli = utils_connect_sql();
$losID = filter_input(INPUT_GET, 'losID', FILTER_VALIDATE_INT);
$projectID = $_SESSION["projectID"] ?? null;

if (!$losID || !$projectID) {
    die("Ungültige Eingabe.");
}

// --- Projekt und Los-Kopfzeile ---
$stmt = $mysqli->prepare("
    SELECT p.Projektname, l.LosNr_Extern, l.LosBezeichnung_Extern
    FROM tabelle_lose_extern l
    INNER JOIN tabelle_projekte p ON l.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte
    WHERE l.idtabelle_Lose_Extern = ?
");
$stmt->bind_param("i", $losID);
$stmt->execute();
$stmt->bind_result($projektname, $losNr, $losBez);

while ($stmt->fetch()) {
    $title = "Projekt: $projektname\nLos: $losNr - $losBez";
    $height = $pdf->getStringHeight(180, $title);
    $pdf->MultiCell(0, $height, $title, 1, 'L', 0, 0, '', '', true);
}
$stmt->close();

$pdf->Ln(5);
$pdf->Ln();

$topics_table_header = array('Text', 'Typ', 'Wer/Bis wann', 'Status');

// --- Vermerke laden ---
$stmt = $mysqli->prepare("
    SELECT g.Gruppenname, g.Gruppenart, g.Ort, g.Datum,
           ap.Name, ap.Vorname, v.Faelligkeit, v.Vermerkart, v.Bearbeitungsstatus,
           v.Vermerktext, v.Erstellungszeit, v.idtabelle_Vermerke,
           r.Raumnr, r.Raumbezeichnung, g.idtabelle_Vermerkgruppe
    FROM tabelle_Vermerke v
    LEFT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen vhap
        ON v.idtabelle_Vermerke = vhap.tabelle_Vermerke_idtabelle_Vermerke
    LEFT JOIN tabelle_ansprechpersonen ap
        ON ap.idTABELLE_Ansprechpersonen = vhap.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen
    INNER JOIN tabelle_Vermerkuntergruppe ug
        ON v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = ug.idtabelle_Vermerkuntergruppe
    INNER JOIN tabelle_Vermerkgruppe g
        ON g.idtabelle_Vermerkgruppe = ug.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe
    LEFT JOIN tabelle_räume r
        ON v.tabelle_räume_idTABELLE_Räume = r.idTABELLE_Räume
    WHERE g.tabelle_projekte_idTABELLE_Projekte = ?
      AND v.tabelle_lose_extern_idtabelle_Lose_Extern = ?
    ORDER BY g.Datum DESC, v.Erstellungszeit DESC
");

$stmt->bind_param("ii", $projectID, $losID);
$stmt->execute();
$result = $stmt->get_result();

$dataVermerke = array();
while ($row = $result->fetch_assoc()) {
    $vid = $row['idtabelle_Vermerke'];
    $dataVermerke[$vid] = [
        'Vermerktext' => $row['Vermerktext'],
        'Name' => $row['Name'] ?? '',
        'Vorname' => $row['Vorname'] ?? '',
        'Faelligkeit' => $row['Faelligkeit'],
        'Vermerkart' => $row['Vermerkart'],
        'Raumnr' => $row['Raumnr'],
        'Raumbezeichnung' => $row['Raumbezeichnung'],
        'Bearbeitungsstatus' => $row['Bearbeitungsstatus'],
        'idtabelle_Vermerkgruppe' => $row['idtabelle_Vermerkgruppe'],
        'Gruppenname' => $row['Gruppenname'],
        'Ort' => $row['Ort'],
        'Datum' => $row['Datum']
    ];
}
$stmt->close();

$pdf->topicsTable($topics_table_header, $dataVermerke);

ob_end_clean();
$mysqli->close();
$pdf->Output(getFileName('Lose_Vermerke'), 'I');
