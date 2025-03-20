<?php
require_once('TCPDF-main/TCPDF-main/tcpdf.php');

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

session_start();
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
$pdf->SetTitle('Gesamt Kosten');
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
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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
// set font
$pdf->SetFont('helvetica', '', 10);

if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
include "_format.php";
check_login();
$mysqli = utils_connect_sql();


// data loading Raumbereiche ----------------------------------
$pdf->SetFont('helvetica', '', 8);

$sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_projekte.Projektname, tabelle_projekte.Preisbasis, tabelle_planungsphasen.Bezeichnung
FROM (tabelle_projekte INNER JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente 
ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk 
ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte) 
AND (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente)) 
ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) 
ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) 
ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) 
ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung
ORDER BY tabelle_räume.Geschoss, tabelle_räume.`Raumbereich Nutzer`;";

$result = $mysqli->query($sql);
$raumbereicheInProject = array();
$i = 1;
while ($row = $result->fetch_assoc()) {
    $raumbereicheInProject[$i]['Geschoss'] = $row['Geschoss'];
    $raumbereicheInProject[$i]['Raumbereich Nutzer'] = $row['Raumbereich Nutzer'];
    $raumbereicheInProject[$i]['Projektname'] = $row['Projektname'];
    $raumbereicheInProject[$i]['Planungsphase'] = $row['Bezeichnung'];

    $raumbereicheInProject[$i]['Preisbasis'] = $row['Preisbasis'];
    $i++;
}

setlocale(LC_MONETARY, "de_DE");
$pdf->SetFillColor(244, 244, 244);
$fill = 0;

$sumGewerk = 0;
$sumGewerkNeu = 0;
$sumGewerkBestand = 0;
$sumRaumbereich = 0;
$sumRaumbereichBestand = 0;
$gewerk = 0;
$sumGesamtNeu=0;
$w= array(90,30,30,30,20);

foreach ($raumbereicheInProject as $rowData) {
    $pdf->AddPage('P', 'A4');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(100, 6, "Kostenberechnung nach Gewerken und GHG", 'TL', 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Preisbasis: " . $rowData['Preisbasis'], 'TR', 'L', 0, 0);
    $pdf->Ln();
    $pdf->MultiCell(100, 6, "Bereich: " . $rowData['Raumbereich Nutzer'], 'L', 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Projekt: " . $rowData['Projektname'], 'R', 'L', 0, 0);
    $pdf->Ln();
    $pdf->MultiCell(100, 6, "Geschoss: " . $rowData['Geschoss'], 'BL', 'L', 0, 0);
    $pdf->MultiCell(80, 6, "Projektphase: " . $rowData['Planungsphase'], 'BR', 'L', 0, 0);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();

    // Gewerke und GHG laden für Raumbereich und Geschoss ----------------------------------
    $sql = "SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, Sum(`Kosten`*`Anzahl`) AS PP, tabelle_auftraggeber_gewerke.Bezeichnung AS GewerkBezeichnung , tabelle_auftraggeber_ghg.Bezeichnung AS GHGBezeichnung
        FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND (tabelle_räume.`Raumbereich Nutzer` = '" . $rowData['Raumbereich Nutzer'] . "' ) AND (tabelle_räume.Geschoss = '" . $rowData['Geschoss'] . "'))
        GROUP BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG
        ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG;";

    $result = $mysqli->query($sql);

    $raumbereichData = array(); // Array zum Zwischenspeichern der Gewerke/GHG Summen

    $j = 0;
    while ($row = $result->fetch_assoc()) {
        $raumbereichData[$j]['Gewerke_Nr'] = $row["Gewerke_Nr"];
        $raumbereichData[$j]['Gewerke_Bezeichnung'] = $row["GewerkBezeichnung"];
        $raumbereichData[$j]['GHG'] = $row["GHG"];
        $raumbereichData[$j]['GHG_Bezeichnung'] = $row["GHGBezeichnung"];
        $raumbereichData[$j]['PP'] = $row["PP"];
        $j++;
    }

    $i = 0;
    foreach ($raumbereichData as $rowData2) {
        if ($rowData2["Gewerke_Nr"] !== $gewerk) {
            $pdf->SetFont('helvetica', 'B', 10);
            if ($i > 0) {
                $pdf->MultiCell(120, 4, format_money_report( $sumGewerk), 'T', 'R', 0, 0);
                $pdf->MultiCell(30, 4, format_money_report( $sumGewerkNeu), 'T', 'R', 0, 0);
                $pdf->MultiCell(30, 4, format_money_report( $sumGewerkBestand), 'T', 'R', 0, 0);
                $sumRaumbereich = $sumRaumbereich + $sumGewerk;
                $sumGesamtNeu = $sumGesamtNeu + $sumGewerkNeu;
                $sumRaumbereichBestand = $sumRaumbereichBestand + $sumGewerkBestand;
                $sumGewerk = 0;
                $sumGewerkNeu = 0;
                $sumGewerkBestand = 0;
                $pdf->Ln();
                $pdf->Ln();
            }


            $pdf->MultiCell($w[0], 6, "Gewerk " . $rowData2["Gewerke_Nr"] . " " . $rowData2["Gewerke_Bezeichnung"], 'B', 'L', 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($w[1], 6, "Kosten", 'B', 'R', 0, 0);
            $pdf->MultiCell($w[2], 6, "davon Neu", 'B', 'R', 0, 0);
            $pdf->MultiCell($w[3], 6, "davon Bestand", 'B', 'R', 0, 0);
            $pdf->Ln();
            $pdf->SetFont('helvetica', 'I', 8);
            if (empty($rowData2["GHG"])) {
                $pdf->MultiCell($w[0], 6, "ohne Zuteilung: ", '', 'L', 0, 0);
            } else {
                $pdf->MultiCell($w[0], 6, "GHG: " . $rowData2["GHG"] . " " . $rowData2["GHG_Bezeichnung"], '', 'L', 0, 0);
            }
            $pdf->MultiCell($w[1], 4, format_money_report( $rowData2["PP"]), 0, 'R', 0, 0);
        } else {
            $pdf->SetFont('helvetica', 'I', 8);

            if (empty($rowData2["GHG"])) {
                $pdf->MultiCell($w[0], 6, "ohne Zuteilung: ", '', 'L', 0, 0);
            } else {
                $pdf->MultiCell($w[0], 6, "GHG: " . $rowData2["GHG"] . " " . $rowData2["GHG_Bezeichnung"], '', 'L', 0, 0);
            }
            $pdf->MultiCell($w[1], 4, format_money_report( $rowData2["PP"]), 0, 'R', 0, 0);
        }
        // Neusumme ermitteln ----------------------------------
        if ($rowData2["GHG"] == "") {
            $sql1 = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_neu
            FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND (tabelle_räume.`Raumbereich Nutzer` = '" . $rowData['Raumbereich Nutzer'] . "' ) AND (tabelle_räume.Geschoss = '" . $rowData['Geschoss'] . "') AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='" . $rowData2["Gewerke_Nr"] . "' AND tabelle_auftraggeber_ghg.GHG IS NULL);";
        } else {
            $sql1 = "SELECT Sum(`Kosten`*`Anzahl`) AS PP_neu
                FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND (tabelle_räume.`Raumbereich Nutzer` = '" . $rowData['Raumbereich Nutzer'] . "' ) AND (tabelle_räume.Geschoss = '" . $rowData['Geschoss'] . "') AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='" . $rowData2["Gewerke_Nr"] . "' AND tabelle_auftraggeber_ghg.GHG='" . $rowData2["GHG"] . "');";
        }

        $result1 = $mysqli->query($sql1);
        $row = $result1->fetch_assoc();
        $pdf->MultiCell($w[3], 4, format_money_report( $row["PP_neu"]), 0, 'R', $fill, 0);

        $sumGewerkNeu = $sumGewerkNeu + $row["PP_neu"];
        //---------------------------------------------------------------
        // Bestandssumme ermitteln ----------------------------------
        if ($rowData2["GHG"] == "") {
            $sql1 = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
            FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND (tabelle_räume.`Raumbereich Nutzer` = '" . $rowData['Raumbereich Nutzer'] . "' ) AND (tabelle_räume.Geschoss = '" . $rowData['Geschoss'] . "') AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='" . $rowData2["Gewerke_Nr"] . "' AND tabelle_auftraggeber_ghg.GHG IS NULL);";
        } else {
            $sql1 = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
                FROM tabelle_auftraggeber_ghg RIGHT JOIN ((tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND (tabelle_räume.`Raumbereich Nutzer` = '" . $rowData['Raumbereich Nutzer'] . "' ) AND (tabelle_räume.Geschoss = '" . $rowData['Geschoss'] . "') AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0 AND tabelle_auftraggeber_gewerke.Gewerke_Nr='" . $rowData2["Gewerke_Nr"] . "' AND tabelle_auftraggeber_ghg.GHG='" . $rowData2["GHG"] . "');";
        }

        $result1 = $mysqli->query($sql1);
        $row1 = $result1->fetch_assoc();
        $pdf->MultiCell($w[3], 4, format_money_report( $row1["PP"]), 0, 'R', $fill, 0);
        //---------------------------------------------------------------
        $gewerk = $rowData2["Gewerke_Nr"];

        $i++;

        $sumGewerk = $rowData2["PP"] + $sumGewerk;
        $sumGewerkBestand = $sumGewerkBestand + $row1["PP"];

        $pdf->Ln();
    }

    // Ausgabe der letzten Summenzeile für letztes Gewerk pro raumbereich
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(120, 4, format_money_report( $sumGewerk), 'T', 'R', 0, 0);
    $pdf->MultiCell(30, 4, format_money_report( $sumGewerkNeu), 'T', 'R', 0, 0);
    $pdf->MultiCell(30, 4, format_money_report( $sumGewerkBestand), 'T', 'R', 0, 0);
    $sumRaumbereich = $sumRaumbereich + $sumGewerk;
    $sumGesamtNeu = $sumGesamtNeu + $sumGewerkNeu;
    $sumRaumbereichBestand = $sumRaumbereichBestand + $sumGewerkBestand;
    $pdf->Ln();
    $pdf->Ln();
    $pdf->MultiCell(90, 4, "GESAMT: ", 'T', 'L', 0, 0);
    $pdf->MultiCell(30, 4, format_money_report( $sumRaumbereich), 'T', 'R', 0, 0);
    $pdf->MultiCell(30, 4, format_money_report( $sumGesamtNeu), 'T', 'R', 0, 0);
    $pdf->MultiCell(30, 4, format_money_report( $sumRaumbereichBestand), 'T', 'R', 0, 0);

    $sumRaumbereich = 0;
    $sumRaumbereichBestand = 0;
    $sumGesamtNeu = 0;
    $sumGewerk = 0;
    $sumGewerkNeu = 0;
    $sumGewerkBestand = 0;
    $gewerk = 0;
}

/*
  while ($row = $result->fetch_assoc()) {
  if($row["Gewerke_Nr"] !== $gewerk){
  $pdf->SetFont('helvetica', 'B', 10);
  if($i > 0){
  $pdf->MultiCell(180, 4, format_money_report( $sumGewerk), 'T', 'R', 0, 0);
  $sumGewerk = 0;
  $pdf->Ln();
  $pdf->Ln();
  }
  $pdf->MultiCell(180, 6, "Gewerk ".$row["Gewerke_Nr"],'B', 'L', 0, 0);
  $pdf->Ln();
  $pdf->SetFont('helvetica', 'I', 8);
  $pdf->MultiCell(50, 6, "",'', 'L', 0, 0);
  if(empty($row["GHG"])){
  $pdf->MultiCell(50, 6, "ohne Zuteilung: ",'', 'L', 0, 0);
  }
  else{
  $pdf->MultiCell(50, 6, "GHG: ".$row["GHG"],'', 'L', 0, 0);
  }
  $pdf->MultiCell(50, 4, format_money_report( $row["PP"]), 0, 'R', 0, 0);
  }
  else{
  $pdf->SetFont('helvetica', 'I', 8);
  $pdf->MultiCell(50, 6, "",'', 'L', 0, 0);
  if(empty($row["GHG"])){
  $pdf->MultiCell(50, 6, "ohne Zuteilung: ",'', 'L', 0, 0);
  }
  else{
  $pdf->MultiCell(50, 6, "GHG: ".$row["GHG"],'', 'L', 0, 0);
  }
  $pdf->MultiCell(50, 4, format_money_report( $row["PP"]), 0, 'R', 0, 0);
  }
  $i++;
  $gewerk = $row["Gewerke_Nr"];
  $sumGewerk = $row["PP"] + $sumGewerk;
  $pdf->Ln();
  }

  // Letzte Gesamtsumme bilden
  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->MultiCell(180, 4, format_money_report( $sumGewerk), 'T', 'R', 0, 0);

  // ---------------------------------------------------------

  /*
  // Summen laden für Gewerke und GHG ----------------------------------
  $pdf->SetFont('helvetica', '', 8);
  $pdf->SetFillColor(244, 244, 244);
  $sumGewerkBestand = 0;

  $fill = 0;
  foreach($gewerkeInProject as $rowData) {
  $pdf->SetFont('helvetica', '', 8);
  $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
  FROM tabelle_projekt_element_gewerk INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
  WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke)=".$rowData['idTABELLE_Auftraggeber_Gewerke'].") AND ((tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG)=".$rowData['idtabelle_auftraggeber_GHG']."));";

  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $pdf->MultiCell(25, 4, format_money_report( 10), 0, 'R', $fill, 0);
  $pdf->Ln();

  foreach($gewerkeInProject as $key => $rowDataGewerkeInProject) {
  $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
  FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
  WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.`Raumbereich Nutzer`)='".$rowData['Raumbereich Nutzer']."') AND ((tabelle_räume.Geschoss)='".$rowData['Geschoss']."') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='".$rowDataGewerkeInProject['Gewerke_Nr']."') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1));";

  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  if ( null != ($row['PP'])  ){
  $pdf->MultiCell(25, 4, format_money_report( $row["PP"]), 0, 'R', $fill, 0);
  $sumRaumbereich = $sumRaumbereich + $row['PP'];
  }
  else{
  $pdf->MultiCell(25, 4,  sprintf('%01.2f', 0), 0, 'R', $fill, 0);
  }
  $gewerkeInProject[$key]['GewerkeSummeGesamt'] = $gewerkeInProject[$key]['GewerkeSummeGesamt'] + $row['PP'];
  }
  $pdf->MultiCell(25, 4, format_money_report( $sumRaumbereich),0, 'R', $fill, 0);
  $pdf->Ln();
  $pdf->SetFont('helvetica', 'I', 6);

  // ------------------------------------Bestand ----------------------------------------------
  $pdf->MultiCell(50, 4, 'davon Bestand', 0, 'R', $fill, 0);
  $pdf->MultiCell(20, 4, '', 0, 'C', $fill, 0);
  foreach($gewerkeInProject as $key => $rowDataGewerkeInProject) {
  $sql = "SELECT Sum(`Kosten`*`Anzahl`) AS PP
  FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_element_gewerk ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
  WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume.`Raumbereich Nutzer`)='".$rowData['Raumbereich Nutzer']."') AND ((tabelle_räume.Geschoss)='".$rowData['Geschoss']."') AND ((tabelle_auftraggeber_gewerke.Gewerke_Nr)='".$rowDataGewerkeInProject['Gewerke_Nr']."') AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0);";
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  if (null !=($row['PP']) ){
  $pdf->MultiCell(25, 4, format_money_report( $row["PP"]), 0, 'R', $fill, 0);
  $sumRaumbereichBestand = $sumRaumbereichBestand + $row['PP'];
  }
  else{
  $pdf->MultiCell(25, 4,  sprintf('%01.2f', 0), 0, 'R', $fill, 0);
  }
  $gewerkeInProject[$key]['GewerkeSummeGesamtBestand'] = $gewerkeInProject[$key]['GewerkeSummeGesamtBestand'] + $row['PP'];
  }
  $pdf->MultiCell(25, 4, format_money_report( $sumRaumbereichBestand),0, 'R', $fill, 0);
  $pdf->Ln();
  $fill=!$fill;
  $sumRaumbereich = 0;
  $sumRaumbereichBestand = 0;


  $x = $pdf->width;
  $y = $pdf->GetY();

  if (($y + 6) >= 190) {
  $pdf->AddPage();
  // $y = 0; // should be your top margin
  }
  }
  // ---------------------------------------------------------
  // Gesamtsumme ausgeben
  /*
  $pdf->SetFont('helvetica', 'B', 8);
  $pdf->MultiCell(50, 4, 'Gesamt', 'T', 'L', 0, 0);
  $pdf->MultiCell(20, 4, '', 'T', 'R', 0, 0);
  $sumGesamt = 0;
  foreach($gewerkeInProject as $rowDataGewerkeInProject) {
  $pdf->MultiCell(25, 4,  sprintf('%01.2f', $rowDataGewerkeInProject['GewerkeSummeGesamt']), 'T', 'R', 0, 0);
  $sumGesamt = $sumGesamt + $rowDataGewerkeInProject['GewerkeSummeGesamt'];
  }
  $pdf->MultiCell(25, 4,  sprintf('%01.2f', $sumGesamt), 'T', 'R', 0, 0);

  // Bestand von gesamtSumme-------------------------------------
  $pdf->Ln();
  $pdf->SetFont('helvetica', 'BI', 6);
  $pdf->MultiCell(50, 4, 'davon Bestand', 0, 'R', 0, 0);
  $pdf->MultiCell(20, 4, '', 0, 'L', 0, 0);
  $sumGesamtBestand = 0;
  foreach($gewerkeInProject as $rowDataGewerkeInProject) {
  $pdf->MultiCell(25, 4,  sprintf('%01.2f', $rowDataGewerkeInProject['GewerkeSummeGesamtBestand']), 0, 'R', 0, 0);
  $sumGesamtBestand = $sumGesamtBestand + $rowDataGewerkeInProject['GewerkeSummeGesamtBestand'];
  }
  $pdf->MultiCell(25, 4,  sprintf('%01.2f', $sumGesamtBestand), 0, 'R', 0, 0);

 */
// close and output PDF document

ob_end_clean();+#
$pdf->Output('Kosten_Gewerke-GHG.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

