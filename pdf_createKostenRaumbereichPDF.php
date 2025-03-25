<?php
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
include "_format.php";
check_login();

class MYPDF extends TCPDF
{

    //Page header
    public function Header()
    {
        // Logo
        if ($_SESSION["projectAusfuehrung"] === "MADER") {
            $image_file = 'Mader_Logo_neu.jpg';
            $this->Image($image_file, 15, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        } else {
            if ($_SESSION["projectAusfuehrung"] === "LIMET") {
                $image_file = 'LIMET_web.png';
                $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            } else {
                $image_file = 'LIMET_web.png';
                $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                $image_file = 'Mader_Logo_neu.jpg';
                $this->Image($image_file, 38, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            }

        }

        // Set font
        $this->SetFont('helvetica', '', 8);
        // Title        
        if ($_SESSION["projectPlanungsphase"] == "Vorentwurf") {
            $this->Cell(0, 0, 'Medizintechnische Kostenschätzung', 0, false, 'R', 0, '', 0, false, 'B', 'B');
        } else {
            $this->Cell(0, 0, 'Medizintechnische Kostenberechnung', 0, false, 'R', 0, '', 0, false, 'B', 'B');
        }
        //$this->Cell(0, 0, 'Gesamt-Kosten', 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $this->Ln();
        $this->cell(0, 0, '', 'B', 0, 'L');
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Ln();
        $tDate = date('Y-m-d');
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}


$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung');
$pdf->SetTitle('Raumbereich Kosten');
$pdf->SetSubject('MT-Kosten');
$pdf->SetKeywords('MT-Kosten');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-5, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage('L', 'A4');
$pageHeight = 254;
$w=array(45,10) ;

$mysqli = utils_connect_sql();
// data loading for header ----------------------------------
$sql = "SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
        FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
        GROUP BY tabelle_auftraggeber_gewerke.Gewerke_Nr
        ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";

$result = $mysqli->query($sql);
$gewerkeInProject = array();
while ($row = $result->fetch_assoc()) {
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['Gewerke_Nr'] = $row['Gewerke_Nr'];
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamt'] = 0;
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtNeu'] = 0;
    $gewerkeInProject[$row['idTABELLE_Auftraggeber_Gewerke']]['GewerkeSummeGesamtBestand'] = 0;
}

$pdf->MultiCell($w[0]-5, 6, "Bereich", 'B', 'L', 0, 0);
$pdf->MultiCell($w[1]+10, 6, "Geschoss", 'B', 'C', 0, 0);
$abzug = -5;
foreach ($gewerkeInProject as $rowData) {
    $pdf->MultiCell(25 + $abzug, 6, $rowData['Gewerke_Nr'], 'B', 'R', 0, 0);
    $abzug =0;
}
$pdf->MultiCell(25, 6, "Gesamt", 'B', 'R', 0, 0);
$pdf->Ln();
// ---------------------------------------------------------

// data loading Raumbereiche ----------------------------------
$pdf->SetFont('helvetica', '', 8);
$pdf->SetFillColor(244, 244, 244);

$sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
FROM tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss
ORDER BY tabelle_räume.Geschoss;";

$result = $mysqli->query($sql);
$raumbereicheInProject = array();
$i = 1;
while ($row = $result->fetch_assoc()) {
    $raumbereicheInProject[$i]['Raumbereich Nutzer'] = $row['Raumbereich Nutzer'];
    $raumbereicheInProject[$i]['Geschoss'] = $row['Geschoss'];
    $i++;
}
setlocale(LC_MONETARY, "de_DE");
$sumRaumbereich = 0;
$sumRaumbereichNeu = 0;
$sumRaumbereichBestand = 0;

$fill = 0;

// RaumBereiche laden über GET
$roomBereiche = filter_input(INPUT_GET, 'roomBereiche');
$roomBereichGeschosse = filter_input(INPUT_GET, 'roomBereichGeschosse');
$teile = explode(",", $roomBereiche);
$teileGeschosse = explode(",", $roomBereichGeschosse);
$index = 0;


foreach ($teile as $valueOfRaumBereiche) {

    foreach ($raumbereicheInProject as $rowData) {
        $y = $pdf->GetY();
        if ($y  >= $pageHeight) {
            $pdf->AddPage();
        }
        if ($rowData['Raumbereich Nutzer'] === $valueOfRaumBereiche && $rowData['Geschoss'] === $teileGeschosse[$index]) {
            $pdf->SetFont('helvetica', '', 8);
            $pdf->MultiCell($w[0], 4,  $rowData['Raumbereich Nutzer'], 0, 'L', $fill, 0);
            $pdf->MultiCell($w[1], 4, $rowData['Geschoss'], 0, 'C', $fill, 0);
            foreach ($gewerkeInProject as $key => $rowDataGewerkeInProject) {
                $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
                    FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.`Raumbereich Nutzer`)='" . $rowData['Raumbereich Nutzer'] . "') AND ((tabelle_räume.Geschoss)='" . $rowData['Geschoss'] . "') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='" . $rowDataGewerkeInProject['Gewerke_Nr'] . "') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1));";

                $result = $mysqli->query($sql);
                $row = $result->fetch_assoc();
                if (null != ($row['PP'])) {
                    $pdf->MultiCell(25, 4, format_money_report($row["PP"]), 0, 'R', $fill, 0);
                    $sumRaumbereich = $sumRaumbereich + $row['PP'];
                } else {
                    $pdf->MultiCell(25, 4, format_money_report( 0), 0, 'R', $fill, 0);
                }
                $gewerkeInProject[$key]['GewerkeSummeGesamt'] = $gewerkeInProject[$key]['GewerkeSummeGesamt'] + $row['PP'];
            }
            $pdf->MultiCell(25, 4, format_money_report($sumRaumbereich), 0, 'R', $fill, 0);
            $pdf->Ln();
            $pdf->SetFont('helvetica', 'I', 6);

            // ------------------------------------Neu ---------------------------------------------- 
            $pdf->MultiCell($w[0], 4, 'davon Neu', 0, 'R', $fill, 0);
            $pdf->MultiCell($w[1], 4, '', 0, 'C', $fill, 0);
            foreach ($gewerkeInProject as $key => $rowDataGewerkeInProject) {
                $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_neu
                        FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.`Raumbereich Nutzer`)='" . $rowData['Raumbereich Nutzer'] . "') AND ((tabelle_räume.Geschoss)='" . $rowData['Geschoss'] . "') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='" . $rowDataGewerkeInProject['Gewerke_Nr'] . "') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1);";
                $result = $mysqli->query($sql);
                $row = $result->fetch_assoc();
                if (null != ($row['PP_neu'])) {
                    $pdf->MultiCell(25, 4, format_money_report($row["PP_neu"]), 0, 'R', $fill, 0);
                    $sumRaumbereichNeu = $sumRaumbereichNeu + $row['PP_neu'];
                } else {
                    $pdf->MultiCell(25, 4, format_money_report( 0), 0, 'R', $fill, 0);
                }
                $gewerkeInProject[$key]['GewerkeSummeGesamtNeu'] = $gewerkeInProject[$key]['GewerkeSummeGesamtNeu'] + $row['PP_neu'];
            }
            $pdf->MultiCell(25, 4, format_money_report($sumRaumbereichNeu), 0, 'R', $fill, 0);
            $pdf->Ln();
            // ------------------------------------Bestand ---------------------------------------------- 
            $pdf->MultiCell($w[0], 4, 'davon Bestand', 0, 'R', $fill, 0);
            $pdf->MultiCell($w[1], 4, '', 0, 'C', $fill, 0);
            foreach ($gewerkeInProject as $key => $rowDataGewerkeInProject) {
                $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
                        FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.`Raumbereich Nutzer`)='" . $rowData['Raumbereich Nutzer'] . "') AND ((tabelle_räume.Geschoss)='" . $rowData['Geschoss'] . "') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='" . $rowDataGewerkeInProject['Gewerke_Nr'] . "') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0);";
                $result = $mysqli->query($sql);
                $row = $result->fetch_assoc();
                if (null != ($row['PP'])) {
                    $pdf->MultiCell(25, 4, format_money_report($row["PP"]), 0, 'R', $fill, 0);
                    $sumRaumbereichBestand = $sumRaumbereichBestand + $row['PP'];
                } else {
                    $pdf->MultiCell(25, 4, format_money_report( 0), 0, 'R', $fill, 0);
                }
                $gewerkeInProject[$key]['GewerkeSummeGesamtBestand'] = $gewerkeInProject[$key]['GewerkeSummeGesamtBestand'] + $row['PP'];
            }
            $pdf->MultiCell(25, 4, format_money_report($sumRaumbereichBestand), 0, 'R', $fill, 0);
            $pdf->Ln();
            $fill = !$fill;
            $sumRaumbereich = 0;
            $sumRaumbereichNeu = 0;
            $sumRaumbereichBestand = 0;
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            if (($y) >= $pageHeight) {
                $pdf->AddPage();
                // $y = 0; // should be your top margin
            }
        }
    }
    $index++;
}
// ---------------------------------------------------------
// Gesamtsumme ausgeben
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell($w[0], 4, 'Gesamt', 'T', 'L', 0, 0);
$pdf->MultiCell($w[1], 4, '', 'T', 'R', 0, 0);
$sumGesamt = 0;
foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
    $pdf->MultiCell(25, 4, format_money_report( $rowDataGewerkeInProject['GewerkeSummeGesamt']), 'T', 'R', 0, 0);
    $sumGesamt = $sumGesamt + $rowDataGewerkeInProject['GewerkeSummeGesamt'];
}
$pdf->MultiCell(25, 4, format_money_report( $sumGesamt), 'T', 'R', 0, 0);
// Neu von gesamtSumme
$pdf->Ln();
$pdf->SetFont('helvetica', 'BI', 6);
$pdf->MultiCell($w[0], 4, 'davon Neu', 0, 'R', 0, 0);
$pdf->MultiCell($w[1], 4, '', 0, 'L', 0, 0);
$sumGesamtBestand = 0;
foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
    $pdf->MultiCell(25, 4, format_money_report($rowDataGewerkeInProject['GewerkeSummeGesamtNeu']), 0, 'R', 0, 0);
    $sumGesamtBestand = $sumGesamtBestand + $rowDataGewerkeInProject['GewerkeSummeGesamtNeu'];
}
$pdf->MultiCell(25, 4, format_money_report($sumGesamtBestand), 0, 'R', 0, 0);
// Bestand von gesamtSumme
$pdf->Ln();
$pdf->SetFont('helvetica', 'BI', 6);
$pdf->MultiCell($w[0], 4, 'davon Bestand', 0, 'R', 0, 0);
$pdf->MultiCell($w[1], 4, '', 0, 'L', 0, 0);
$sumGesamtBestand = 0;
foreach ($gewerkeInProject as $rowDataGewerkeInProject) {
    $pdf->MultiCell(25, 4, format_money_report( $rowDataGewerkeInProject['GewerkeSummeGesamtBestand']), 0, 'R', 0, 0);
    $sumGesamtBestand = $sumGesamtBestand + $rowDataGewerkeInProject['GewerkeSummeGesamtBestand'];
}
$pdf->MultiCell(25, 4,format_money_report( $sumGesamtBestand), 0, 'R', 0, 0);

ob_end_clean();
$pdf->Output('xxx.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

