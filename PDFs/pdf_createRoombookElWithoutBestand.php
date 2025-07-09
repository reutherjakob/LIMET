<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Raumbuch-MT");

$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage('P', 'A4');
$mysqli = utils_connect_sql();
$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);
$widths = array(0, 20, 20, 20, 90, 10, 20);
foreach ($teile as $valueOfRoomID) {
    // Elemente im Raum laden
    $sql3 = "SELECT tabelle_elemente.ElementID,
       tabelle_elemente.Bezeichnung,
       tabelle_varianten.Variante,
       tabelle_räume_has_tabelle_elemente.Anzahl,
       tabelle_räume_has_tabelle_elemente.Standort,
       tabelle_räume_has_tabelle_elemente.Verwendung,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_auftraggeber_gewerke.Gewerke_Nr,
       tabelle_auftraggeber_ghg.GHG,
       tabelle_auftraggeberg_gug.GUG,
       tabelle_projekt_varianten_kosten.Kosten,
       tabelle_räume_has_tabelle_elemente.id,
       tabelle_bestandsdaten.Inventarnummer,
       tabelle_bestandsdaten.Seriennummer,
       tabelle_bestandsdaten.Anschaffungsjahr,
       tabelle_hersteller.Hersteller,
       tabelle_geraete.Typ,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
FROM tabelle_hersteller
         RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente
                                                                                                                                                                                                                                                                                                                                                       ON tabelle_elemente.idTABELLE_Elemente =
                                                                                                                                                                                                                                                                                                                                                          tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
                                                                                                                                                                                                                                                                                                                         ON tabelle_varianten.idtabelle_Varianten =
                                                                                                                                                                                                                                                                                                                            tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)
                                                                                                                                                                                                                                                                                               ON tabelle_räume.idTABELLE_Räume =
                                                                                                                                                                                                                                                                                                  tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                                                                                                                                                                                                                                  ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =
                                                                                                                                                                                                                                                      tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND
                                                                                                                                                                                                                                                     (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente =
                                                                                                                                                                                                                                                      tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND
                                                                                                                                                                                                                                                     (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten =
                                                                                                                                                                                                                                                      tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten))
                                                                                                                                                                                                       ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte =
                                                                                                                                                                                                           tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND
                                                                                                                                                                                                          (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente =
                                                                                                                                                                                                           tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente))
                                                                                                                                                                 ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG =
                                                                                                                                                                    tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG)
                                                                                                                            ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG =
                                                                                                                               tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG)
                                                                                   ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke =
                                                                                      tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke)
                                                 ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id =
                                                    tabelle_räume_has_tabelle_elemente.id)
                     ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete)
                    ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . ") AND
       ((tabelle_räume.idTABELLE_Räume) = " . $valueOfRoomID . ") AND tabelle_räume_has_tabelle_elemente.Anzahl > 0)
ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

    $sql2 = "SELECT tabelle_räume.Raumnr,
       tabelle_räume.Raumbezeichnung,
       tabelle_projekte.Projektname,
       tabelle_planungsphasen.Bezeichnung,
       tabelle_räume.`Raumbereich Nutzer`,
       tabelle_räume.Nutzfläche,
       tabelle_räume.Bauetappe,
       tabelle_räume.Bauabschnitt,
       tabelle_räume.Geschoss,
       `tabelle_räume`.`Anmerkung FunktionBO`
FROM tabelle_planungsphasen
         INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume
                     ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                    ON tabelle_planungsphasen.idTABELLE_Planungsphasen =
                       tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
WHERE (((tabelle_räume.idTABELLE_Räume) = " . $valueOfRoomID . "));";

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
    }
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0);
    $rowHeightFirstLine = $pdf->getStringHeight($widths[4] + $widths[1], "ElementID", false, true, '', 1);
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
            $rowHeightMainLine = $pdf->getStringHeight(50, $row['Bezeichnung'], false, true, '', 1);
            check4newpage($pdf, $rowHeightMainLine);
            $pdf->MultiCell($widths[2] + $widths[0], $rowHeightMainLine, $row['ElementID'], 0, 'C', $fill, 0);
            $pdf->MultiCell($widths[3], $rowHeightMainLine, $row['Variante'], 0, 'C', $fill, 0);
            $pdf->MultiCell($widths[5], $rowHeightMainLine, $row['Anzahl'], 0, 'C', $fill, 0);
            if ($row['Neu/Bestand'] == 1) {
                $pdf->MultiCell($widths[6], $rowHeightMainLine, "nein", 0, 'C', $fill, 0);
            } else {
                $pdf->MultiCell($widths[6], $rowHeightMainLine, "ja", 0, 'C', $fill, 0);
            }
            $pdf->MultiCell($widths[4] + $widths[1], $rowHeightMainLine, $row['Bezeichnung'], 0, 'L', $fill, 0);
            $idRoombookEntry = $row['id'];
            $pdf->Ln();
        } else {
            $bestandsCounter++;
        }

    }
    $pdf->Ln();
}


$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Raumbuch-Elementliste'), 'I');
$_SESSION["PDFHeaderSubtext"]="";
