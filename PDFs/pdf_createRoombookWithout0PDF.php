<?php
#2025done
require_once '../utils/_utils.php';
check_login();
require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "_pdf_createBericht_utils.php";
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";

$mysqli = utils_connect_sql();
$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);

$marginTop = 15; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$_SESSION["PDFTITEL"] = "Medizintechnisches Raumbuch";
$_SESSION["PDFHeaderSubtext"] = "Projekt: " . $_SESSION["projectName"] . " - PPH: " . $_SESSION["projectPlanungsphase"];
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Raumbuch-MT");

function check4newpagex($pdf, $rowHeight): bool
{
    $y = $pdf->GetY();
    if (($y + $rowHeight) >= 275) {
        $pdf->AddPage();
        $pdf->SetY(15);
        return true;
    }
    return false;
}

// Variantenparameter Info laden
//$sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente
//FROM tabelle_projekt_elementparameter
//WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)=14));";

$sql = "SELECT 
            tabelle_projekt_elementparameter.Wert, 
            tabelle_projekt_elementparameter.Einheit, 
            tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, 
            tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, 
            tabelle_parameter.Bezeichnung, 
            tabelle_parameter_kategorie.Kategorie
        FROM tabelle_parameter_kategorie 
            INNER JOIN (tabelle_parameter 
                INNER JOIN tabelle_projekt_elementparameter 
                ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
                ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
        WHERE tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ?
        ORDER BY tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie";

$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    die("Prepare fehlgeschlagen: " . $mysqli->error);
}
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result1 = $stmt->get_result();

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

foreach ($teile as $valueOfRoomID) {
    $sql = "SELECT 
    tabelle_räume.Raumnr, 
    tabelle_räume.Raumbezeichnung, 
    tabelle_projekte.Projektname,
    tabelle_planungsphasen.Bezeichnung, 
    tabelle_räume.`Raumbereich Nutzer`, 
    tabelle_räume.Nutzfläche, 
    tabelle_räume.Bauetappe,
    tabelle_räume.Bauabschnitt,
    tabelle_räume.Geschoss, 
    `tabelle_räume`.`Anmerkung FunktionBO`
        FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
        WHERE tabelle_räume.idTABELLE_Räume = ?";
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        die("Prepare fehlgeschlagen: " . $mysqli->error);
    }
    $stmt->bind_param("i", $valueOfRoomID);
    $stmt->execute();
    $result2 = $stmt->get_result();


    $pdf->AddPage('P', 'A4');
    $pdf->Ln(5);
    createRaumHeaderRaumbuch($pdf, $result2);

    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0);
    $rowHeightFirstLine = $pdf->getStringHeight(50, "ElementID", false, true, '', 1);

    $colWidths = [20, 90, 10, 15, 15, 15, 15];
    $colTitles = ["Element:ID", "Bezeichnung", "Stk", "Var", "Bestand", "Gewerk", "GHG"];
    $colAligns = ['L', 'L', 'C', 'C', 'C', 'C', 'C'];

    foreach ($colWidths as $i => $width) {
        $pdf->MultiCell($width, $rowHeightFirstLine, $colTitles[$i], 'B', $colAligns[$i], 0, 0);
    }
    $pdf->Ln();

    $sql = "SELECT 
                tabelle_elemente.ElementID, 
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
                RIGHT JOIN (tabelle_geraete 
                RIGHT JOIN (tabelle_bestandsdaten
                    RIGHT JOIN (tabelle_auftraggeber_gewerke
                        RIGHT JOIN (tabelle_auftraggeber_ghg
                            RIGHT JOIN (tabelle_auftraggeberg_gug 
                                RIGHT JOIN (tabelle_projekt_element_gewerk 
                                    RIGHT JOIN (tabelle_projekt_varianten_kosten 
                                        INNER JOIN (tabelle_räume 
                                            INNER JOIN (tabelle_varianten 
                                                INNER JOIN (tabelle_elemente 
                                                    INNER JOIN tabelle_räume_has_tabelle_elemente 
                                                    ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                                                ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)
                                            ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
                                        ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) 
                                               AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                                               AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten))
                                    ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) 
                                           AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente))
                                ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) 
                            ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) 
                        ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke)
                    ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) 
                    ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) 
                    ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
            WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
              AND tabelle_räume.idTABELLE_Räume = ?
              AND tabelle_räume_has_tabelle_elemente.Anzahl > 0
            ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, 
                     tabelle_auftraggeber_ghg.GHG, 
                     tabelle_elemente.ElementID, 
                     tabelle_varianten.Variante, 
                     tabelle_räume_has_tabelle_elemente.id";
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        die("Prepare fehlgeschlagen: " . $mysqli->error);
    }
    $stmt->bind_param("ii", $_SESSION["projectID"], $valueOfRoomID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    $idRoombookEntry = 0;
    $bestandsCounter = 1;

    while ($row = $result->fetch_assoc()) {

        if ($idRoombookEntry != $row['id']) {

            $fill = !$fill;
            $bestandsCounter = 1;
            $pdf->SetFont('helvetica', '', 8);
            $rowHeightMainLine = $pdf->getStringHeight($colWidths[1], $row['Bezeichnung'], false, true, '', 1);
            if (check4newpagex($pdf, $rowHeightMainLine)) {
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Ln(6);
                foreach ($colWidths as $i => $width) {
                    $pdf->MultiCell($width, $rowHeightFirstLine, $colTitles[$i], 'B', $colAligns[$i], 0, 0);
                }
                $pdf->Ln();
            }
            $bestand = ($row['Neu/Bestand'] == 1) ? "Nein" : "Ja";
            $rowValues = [
                $row['ElementID'],   // 0
                $row['Bezeichnung'], // 1
                $row['Anzahl'],      // 2
                $row['Variante'],    // 3
                $bestand,            // 4
                $row['Gewerke_Nr'],  // 5
                $row['GHG'],         // 6
            ];
            foreach ($rowValues as $i => $value) {
                $pdf->MultiCell($colWidths[$i], $rowHeightMainLine, $value, 0, $colAligns[$i], $fill, 0);
            }
            $pdf->Ln(1);
            foreach ($rowValues as $i => $value) {
                $pdf->MultiCell($colWidths[$i], 2, "", 0, $colAligns[$i], $fill, 0);
            }
            $additionalRoombookData = "";
            /*foreach ($variantenInfos as $array) {
                // ---------------Ausgabe der Varianten-Parameter---------------------------
                if($array['elementID']===$row['TABELLE_Elemente_idTABELLE_Elemente']){                 
                    if($array['VarID']===$row['tabelle_Varianten_idtabelle_Varianten']){                     
                        $additionalRoombookData = $additionalRoombookData."\n".$array['Kategorie']."-".$array['Bezeichnung'].": ".$array['Wert']." ".$array['Einheit'];
                    }                            }            } */

            if ($row['Standort'] == 1) {
                $additionalRoombookData = $additionalRoombookData . "Standort: Ja";
            } else {
                $additionalRoombookData = $additionalRoombookData . "Standort: Nein";
            }
            if ($row['Verwendung'] == 1) {
                $additionalRoombookData = $additionalRoombookData . " - Verwendung: Ja";
            } else {
                $additionalRoombookData = $additionalRoombookData . " - Verwendung: Nein";
            }
            if (null != ($row['Kurzbeschreibung'])) {
                $additionalRoombookData = $additionalRoombookData . "\nKommentar: " . $row['Kurzbeschreibung'];
            }
            if (null != ($row['Inventarnummer'])) {
                $additionalRoombookData = $additionalRoombookData . "\nBestandsgerät " . $bestandsCounter . ": Inventarnummer: " . $row['Inventarnummer'];
            }
            if (null != ($row['Seriennummer'])) {
                $additionalRoombookData = $additionalRoombookData . " - Seriennummer: " . $row['Seriennummer'];
            }
            if (null != ($row['Anschaffungsjahr'])) {
                $additionalRoombookData = $additionalRoombookData . " - Anschaffungsjahr: " . $row['Anschaffungsjahr'];
            }
            if (null != ($row['Hersteller'])) {
                $additionalRoombookData = $additionalRoombookData . " - Gerät: " . $row['Hersteller'] . " " . $row['Typ'];
            }

            if (null != ($additionalRoombookData)) {
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 6);
                $rowHeight = $pdf->getStringHeight($colWidths[1], $additionalRoombookData, false, true, '', 1);
                if (check4newpagex($pdf, $rowHeight)) {
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->Ln(6);
                    foreach ($colWidths as $i => $width) {
                        $pdf->MultiCell($width, $rowHeightFirstLine, $colTitles[$i], 'B', $colAligns[$i], 0, 0);
                    }
                    $pdf->Ln();
                }

                $pdf->SetFont('helvetica', '', 6);
                //  $pdf->MultiCell($colWidths[0] + $colWidths[1] + $colWidths[2] + $colWidths[3] + $colWidths[4] + $colWidths[5] + $colWidths[6], 2, "", 0, 'C', $fill, 0);
                $pdf->MultiCell($colWidths[0], $rowHeight, "", 0, 'C', $fill, 0);
                $pdf->MultiCell($colWidths[1], $rowHeight, $additionalRoombookData, 0, 'L', $fill, 0);
                $pdf->MultiCell($colWidths[2] + $colWidths[3] + $colWidths[4] + $colWidths[5] + $colWidths[6], $rowHeight, "", 0, 'C', $fill, 0);
                $bestandsCounter++;

            }
            $pdf->Ln();

            $idRoombookEntry = $row['id'];
        } else {
            $pdf->SetFont('helvetica', 'I', 6);
            $additionalRoombookExtraData = "";
            if (null != ($row['Inventarnummer'])) {
                $additionalRoombookExtraData = $additionalRoombookExtraData . "Bestandsgerät " . $bestandsCounter . ": Inventarnummer: " . $row['Inventarnummer'];
            }
            if (null != ($row['Seriennummer'])) {
                $additionalRoombookExtraData = $additionalRoombookExtraData . " - Seriennummer: " . $row['Seriennummer'];
            }
            if (null != ($row['Anschaffungsjahr'])) {
                $additionalRoombookExtraData = $additionalRoombookExtraData . " - Anschaffungsjahr: " . $row['Anschaffungsjahr'];
            }
            if (null != ($row['Hersteller'])) {
                $additionalRoombookExtraData = $additionalRoombookExtraData . " - Gerät: " . $row['Hersteller'] . " " . $row['Typ'];
            }
            $pdf->SetFont('helvetica', '', 6);
            $rowHeight = $pdf->getStringHeight($colWidths[1], $additionalRoombookExtraData, false, true, '', 1);
            if (check4newpagex($pdf, $rowHeight)) {
                $pdf->SetFont('helvetica', '', 10);
                foreach ($colWidths as $i => $width) {
                    $pdf->MultiCell($width, $rowHeightFirstLine, $colTitles[$i], 'B', $colAligns[$i], 0, 0);
                }
                $pdf->Ln();
            }
            $pdf->SetFont('helvetica', '', 6);
            $pdf->MultiCell($colWidths[0], $rowHeight, "", 0, 'C', $fill, 0);
            $pdf->MultiCell($colWidths[1], $rowHeight, $additionalRoombookExtraData, 0, 'L', $fill, 0);
            $pdf->MultiCell($colWidths[2] + $colWidths[3] + $colWidths[4] + $colWidths[5] + $colWidths[6], $rowHeight, "", 0, 'C', $fill, 0);
            $bestandsCounter++;
            $pdf->Ln();
        }

    }
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName("Raumbuch"), 'I');
$_SESSION["PDFHeaderSubtext"] = "";