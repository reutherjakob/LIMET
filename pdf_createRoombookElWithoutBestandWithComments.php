<?php
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
include "_pdf_utils.php";
check_login();

class MYPDF extends TCPDF
{

    public function Header()
    {
        if ($this->numpages > 1) {
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
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 0, 'Medizintechnisches Raumbuch', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->cell(0, 0, '', 'B', 0, 'L');
        } else {
            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
            if (!$mysqli->set_charset("utf8")) {
                printf("Error loading character set utf8: %s\n", $mysqli->error);
                exit();
            }
            $roomIDs = filter_input(INPUT_GET, 'roomID');
            $teile = explode(",", $roomIDs);

            $sql = "SELECT tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`
                    FROM tabelle_räume INNER JOIN (tabelle_planungsphasen INNER JOIN tabelle_projekte ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen) ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte ";
            $i = 0;
            foreach ($teile as $valueOfRoomID) {
                if ($i == 0) {
                    $sql = $sql . "WHERE tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
                } else {
                    $sql = $sql . "OR tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
                }
                $i++;
            }
            $sql = $sql . "GROUP BY tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer` ORDER BY tabelle_räume.`Raumbereich Nutzer`;";
            $result = $mysqli->query($sql);
            $raumInfos = array();
            $raumInfosCounter = 0;
            while ($row = $result->fetch_assoc()) {
                $raumInfos[$raumInfosCounter]['Projektname'] = $row['Projektname'];
                $raumInfos[$raumInfosCounter]['Planungsphase'] = $row['Bezeichnung'];
                $raumInfos[$raumInfosCounter]['Raumbereich'] = $row['Raumbereich Nutzer'];
                $raumInfosCounter = $raumInfosCounter + 1;
            }
            $mysqli->close();
            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(50);
            $this->Cell(0, 0, $raumInfos[0]['Projektname'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell(0, 0, $raumInfos[0]['Planungsphase'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Ln();
            $this->Cell(0, 0, 'Medizintechnisches Raumbuch', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Ln();
            $this->Cell(0, 0, 'Funktionsstellen: ', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $raumInfosCounter = 0;

            $this->SetFont('helvetica', 'B', 12);

            foreach ($raumInfos as $valueOfRaumInfos) {
                $this->Cell(0, 0, $raumInfos[$raumInfosCounter]['Raumbereich'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
                $this->Ln();
                $raumInfosCounter++;
            }

            $this->Cell(0, 0, "Stand: " . date('Y-m-d'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
            $this->SetFont('helvetica', '', 6);
            if ($_SESSION["projectAusfuehrung"] === "MADER") {
                $image_file = 'Mader_Logo_neu.jpg';
                $this->Image($image_file, 145, 40, 50, 15, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            } else {
                if ($_SESSION["projectAusfuehrung"] === "LIMET") {
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 110, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                } else {
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 110, 40, 30, 13, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $image_file = 'Mader_Logo_neu.jpg';
                    $this->Image($image_file, 145, 41, 50, 13, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $this->SetY(60);
                    $this->SetX(110);
                    $this->Cell(0, 0, "ARGE LIMET-MADER", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "Zwerggase 6/1", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "8010 Graz", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Ln();
                    $this->Cell(0, 0, "Tel: +43 1 470 48 33 Dipl.-Ing. Jens Liebmann, MBA", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "Tel: +43 650 523 27 38 Dipl.-Ing. Peter Mader", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Ln();
                    $this->Cell(0, 0, "UID ATU 69334945", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "IBAN AT90 2081 5208 0067 8128", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "BIC STSPAT2GXXX", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                }
            }
        }
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Ln();
        $tDate = date('Y-m-d');
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LIMET Consulting und Planung für Medizintechnik');
$pdf->SetTitle('Raumbuch-MT-Elementliste');
$pdf->SetSubject('Raumbuch-MT-Elementliste');
$pdf->SetKeywords('Raumbuch-MT-Elementliste');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage('P', 'A4');
$pdf->AddPage('P', 'A4');
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
ORDER BY tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie;";

$result1 = $mysqli->query($sql);
$variantenInfos = array();
$variantenInfosCounter = 0;

while ($row = $result1->fetch_assoc()) {
    $variantenInfos[$variantenInfosCounter]['VarID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $variantenInfos[$variantenInfosCounter]['elementID'] = $row['tabelle_elemente_idTABELLE_Elemente'];
    $variantenInfos[$variantenInfosCounter]['Wert'] = $row['Wert'];
    $variantenInfos[$variantenInfosCounter]['Einheit'] = $row['Einheit'];
    $variantenInfos[$variantenInfosCounter]['Kategorie'] = $row['Kategorie'];
    $variantenInfos[$variantenInfosCounter]['Bezeichnung'] = $row['Bezeichnung'];
    $variantenInfosCounter = $variantenInfosCounter + 1;
}



$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);
$widths = array(0, 20, 20, 20, 90, 10, 20);


foreach ($teile as $valueOfRoomID) {
    // Elemente im Raum laden
    $sql3 = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, 
       tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG,
       tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer,
       tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . ") AND tabelle_räume_has_tabelle_elemente.Anzahl >0)
    ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

    $sql2 = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung,  tabelle_projekte.Projektname,
       tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, 
       tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.Geschoss, `tabelle_räume`.`Anmerkung FunktionBO`
                FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
                WHERE (((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . "));";

    if (!check4newpage($pdf, 50)) {
        $pdf->SetFillColor(200, 210, 200);
        $pdf->SetFont('helvetica', '', 1);
        $pdf->MultiCell(180, 2, "", "BT", 'L', 1, 1);
        $pdf->Ln();
        $pdf->SetFillColor(00, 00, 00);
        $pdf->SetFont('helvetica', '', 10);
    }

    $result2 = $mysqli->query($sql2);
    while ($row = $result2->fetch_assoc()) {
        $LN = 5;
        $pdf->SetFont('helvetica', 'B', 10);


        $pdf->MultiCell(100, 6, "Raum: " . $row['Raumbezeichnung'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: " . $row['Raumnr'], 0, 'L', 0, 0);
        if ($pdf->getStringHeight(100, "Raum: " . $row['Raumbezeichnung'], false, true, '', 1) > 6) {
            $pdf->Ln($LN);
        }
        $pdf->Ln($LN);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 6, "Bereich: " . $row['Raumbereich Nutzer'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Geschoss: " . $row['Geschoss'], 0, 'L', 0, 0);
        if ($pdf->getStringHeight(100, "Raum: " . $row['Raumbereich Nutzer'], false, true, '', 1) > 6) {
            $pdf->Ln($LN);
        }
        $pdf->Ln($LN);
        $pdf->MultiCell(100, 6, "Raumfläche: " . $row['Nutzfläche'] . " m2", 0, 'L', 0, 0);
        $pdf->MultiCell(100, 6, "Projektstatus: " . $row['Bezeichnung'], '', 'L', 0, 0);
        $pdf->Ln($LN);
        $pdf->MultiCell(100, 6, "Projekt: " . $row['Projektname'], "B", 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: " . $row['Bauabschnitt'], "B", 'L', 0, 0);

        $pdf->SetFont('helvetica', '', 8);
        //if (null != ($row['Anmerkung FunktionBO'])) {
        //    $pdf->Ln();
        //    $rowHeightComment = $pdf->getStringHeight(150, br2nl($row['Anmerkung FunktionBO']), false, true, '', 1);
        //    $pdf->MultiCell(30, $rowHeightComment, "Funktion BO:", 'B', 'L', 0, 0);
        //    $pdf->MultiCell(150, $rowHeightComment, br2nl($row['Anmerkung FunktionBO']), 'B', 'L', 0, 0);
        //}
    }
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0);
    $rowHeightFirstLine = $pdf->getStringHeight(50, "ElementID", false, true, '', 1);
    // $pdf->MultiCell($widths[0], $rowHeightFirstLine, "Gewerk", 'B', 'C', 0, 0);
    //$pdf->MultiCell($widths[1], $rowHeightFirstLine, "GHG", 'B', 'C', 0, 0);
    $pdf->MultiCell($widths[2] + $widths[0], $rowHeightFirstLine, "ElementID", 'B', 'C', 0, 0);
    $pdf->MultiCell($widths[3], $rowHeightFirstLine, "Var", 'B', 'C', 0, 0);
    $pdf->MultiCell($widths[5], $rowHeightFirstLine, "Stk", 'B', 'C', 0, 0);
    $pdf->MultiCell($widths[6], $rowHeightFirstLine, "Bestand", 'B', 'C', 0, 0);

    $pdf->MultiCell($widths[4] + $widths[1], $rowHeightFirstLine, "Element", 'B', 'L', 0, 1);


    $result = $mysqli->query($sql3);
    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    $idRoombookEntry = 0;
    $bestandsCounter = 1;

    while ($row = $result->fetch_assoc()) {
        if ($idRoombookEntry != $row['id']) {
            $fill = !$fill;
            $bestandsCounter = 1;
            $pdf->SetFont('helvetica', '', 8);
            $rowHeightMainLine = $pdf->getStringHeight($widths[4] + $widths[1], $row['Bezeichnung'], false, true, '', 1);
            check4newpage($pdf, $rowHeightMainLine);
            //$pdf->MultiCell($widths[0], $rowHeightMainLine, $row['Gewerke_Nr'], 0, 'C', $fill, 0);
            // $pdf->MultiCell($widths[1], $rowHeightMainLine, $row['GHG'], 0, 'C', $fill, 0);
            $pdf->MultiCell($widths[2] + $widths[0], $rowHeightMainLine, $row['ElementID'], 0, 'C', $fill, 0);
            $pdf->MultiCell($widths[3], $rowHeightMainLine, $row['Variante'], 0, 'C', $fill, 0);
            $pdf->MultiCell($widths[5], $rowHeightMainLine, $row['Anzahl'], 0, 'C', $fill, 0);

            if ($row['Neu/Bestand'] == 1) {
                $pdf->MultiCell($widths[6], $rowHeightMainLine, "nein", 0, 'C', $fill, 0);
            } else {
                $pdf->MultiCell($widths[6], $rowHeightMainLine, "ja", 0, 'C', $fill, 0);
            }
            $pdf->MultiCell($widths[4] + $widths[1], $rowHeightMainLine, $row['Bezeichnung'], 0, 'L', $fill, 0);


            if ($row['Kurzbeschreibung'] != "") {
                $pdf->SetFont('helvetica', 'I', 8);
                $pdf->Ln();
                $rowHeight = $pdf->getStringHeight($widths[4] + $widths[5] + $widths[6], $row['Kurzbeschreibung'], false, true, '', 1);
                $pdf->MultiCell($widths[0] + $widths[3] + $widths[1] + $widths[2], $rowHeight, "Kommentar: ", 0, 'R', $fill, 0);
                $pdf->MultiCell($widths[4] + $widths[5] + $widths[6], $rowHeight, $row['Kurzbeschreibung'], 0, 'L', $fill, 0);
                $pdf->SetFont('helvetica', '', 8);
            }
            $idRoombookEntry = $row['id'];
            $pdf->Ln();
        } else {
            $bestandsCounter++;
        }

    }
    $pdf->Ln();
}
ob_end_clean();
$pdf->Output( getFileName('Raumbuch-Elementliste'), 'I');
