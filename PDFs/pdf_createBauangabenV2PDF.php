<?php
#2025done
require_once '../_utils/_utils.php';
check_login();

require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
include "_pdf_createBericht_utils.php";

$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
if ($_SESSION["projectPlanungsphase"] == "Vorentwurf") {
    $_SESSION["PDFTITEL"] = "Medizintechnische Vorbemessungsangaben";
} else {
    $_SESSION["PDFTITEL"] = "Medizintechnische Bauangaben";
}
$_SESSION["PDFHeaderSubtext"] = "Projekt: " . $_SESSION["projectName"] . " - PPH: " . $_SESSION["projectPlanungsphase"];

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "MT-Bauangaben");
$mysqli = utils_connect_sql();


// =====================================================================
// Layout-Hilfsfunktionen
// Grundproblem bisher: MultiCell(..., ln=0) lässt den Cursor oben rechts
// neben der Zelle stehen; ein anschließendes Ln()/Ln(1) springt nur eine
// Zeile (bzw. 1 mm) weiter. Mehrzeilige Anmerkungen wurden dadurch vom
// nachfolgenden Inhalt überschrieben. Die Funktionen unten setzen den
// Cursor nach jeder Zeile explizit unter die höchste Zelle.
// =====================================================================

/** Höhe des Hinweises "Fortsetzung auf der nächsten Seite". */
const RB_NOTE_HEIGHT = 6;

/** Unterste nutzbare Y-Position der Seite. */
function rb_maxY($pdf)
{
    return min(270, $pdf->getPageHeight() - $pdf->getBreakMargin());
}

/** Nutzbare Höhe einer ganzen Seite. */
function rb_usableHeight($pdf)
{
    $m = $pdf->getMargins();
    return rb_maxY($pdf) - $m['top'];
}

/** Neue Seite, falls $h nicht mehr auf die aktuelle Seite passt. */
function rb_ensureSpace($pdf, $h)
{
    if ($pdf->GetY() + $h >= rb_maxY($pdf)) {
        $pdf->AddPage();
    }
}

/**
 * Schreibt eine Tabellenzeile aus mehreren Zellen nebeneinander.
 * $cells = [[Breite, Text, Ausrichtung], ...]
 * Die Zeilenhöhe richtet sich nach der höchsten Zelle; danach steht der
 * Cursor sauber unter der Zeile am linken Rand.
 */
function rb_row($pdf, array $cells, $fill = 0, $border = 0)
{
    $h = 0;
    foreach ($cells as $c) {
        $h = max($h, $pdf->getStringHeight($c[0], (string)$c[1], false, true, '', 1));
    }
    if ($h <= rb_usableHeight($pdf)) {
        rb_ensureSpace($pdf, $h);
    }
    $y0 = $pdf->GetY();
    $page0 = $pdf->getPage();
    foreach ($cells as $c) {
        $pdf->MultiCell($c[0], $h, (string)$c[1], $border, $c[2], $fill, 0);
    }
    // Nur wenn kein Seitenumbruch innerhalb der Zeile passiert ist, exakt positionieren
    if ($pdf->getPage() == $page0) {
        $pdf->SetY($y0 + $h);
    } else {
        $pdf->Ln(1);
    }
}

/** Beschriftung (rechtsbündig) vor einem Wert. */
function rb_label($pdf, $w, $text)
{
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell($w, 6, $text, 0, 'R', 0, 0);
}

/** Häkchen (grün) bzw. Kreuz je nach Wert ('0' = nein). */
function rb_check($pdf, $w, $value)
{
    $pdf->SetFont('zapfdingbats', '', 10);
    if ($value === '0') {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell($w, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
    } else {
        $pdf->SetTextColor(0, 255, 0); // grün
        $pdf->MultiCell($w, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
    }
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
}

/** Beendet eine Zeile aus einzeiligen Zellen (Höhe 6). */
function rb_endLine($pdf, $default = 0)
{
    if ($default > 0) {
        $pdf->Ln($default);
    } else {
        $pdf->Ln();
    }
}

/**
 * Anmerkung: Label links (40 mm), Text rechts (140 mm).
 * Der Text fließt bei Bedarf über Seitenumbrüche; danach steht der Cursor
 * unter dem Text.
 */
function rb_anmerkung($pdf, $text, $label = "Anmerkung:")
{
    if ($text === null || trim((string)$text) === '' || trim((string)$text) === 'keine Angaben') {
        rb_endLine($pdf, 1);
        return;
    }
    rb_endLine($pdf);
    $text = br2nl($text);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $h = $pdf->getStringHeight(140, $text, false, true, '', 1);
    // Kurze Anmerkungen nicht trennen; sehr lange dürfen über die Seite fließen
    rb_ensureSpace($pdf, ($h <= rb_usableHeight($pdf)) ? $h : 12);
    $pdf->MultiCell(40, 6, $label, 0, 'R', 0, 0);
    $pdf->MultiCell(140, 0, $text, 0, 'L', 0, 0);   // ln=1 -> Cursor unter den Text
    rb_endLine($pdf);
}

/** Trennlinie über die volle Breite. */
function rb_separator($pdf)
{
    $pdf->Cell(180, 1, '', 'B', 1);
}

/** Abschnittsüberschrift; vermeidet allein stehende Überschriften am Seitenende. */
function rb_section($pdf, $title, $minFollowing = 0)
{
    rb_ensureSpace($pdf, 8 + $minFollowing);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(50, 8, $title, 0, 'L', 0, 0);
    //if ($title == "Bau-Statik: ") {
    //    return;
    //}
    $pdf->Ln();
}


// -----------------Variantenparameter Info laden----------------------------
$sql = "SELECT tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter , tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . (int)$_SESSION["projectID"] . ") AND tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter!=85)
ORDER BY tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie;";

$result1 = $mysqli->query($sql);
$variantenInfos = array();
while ($row = $result1->fetch_assoc()) {
    $variantenInfos[] = array(
        'VarID' => $row['tabelle_Varianten_idtabelle_Varianten'],
        'elementID' => $row['tabelle_elemente_idTABELLE_Elemente'],
        'Wert' => $row['Wert'],
        'Einheit' => $row['Einheit'],
        'Kategorie' => $row['Kategorie'],
        'Bezeichnung' => $row['Bezeichnung'],
    );
}


// RaumIDs laden über GET (als Integer, verhindert SQL-Injection)
$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = array_filter(array_map('intval', explode(",", (string)$roomIDs)));


foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');

    // Raumdaten laden ----------------------------------
    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe,
            tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, tabelle_räume.GMP,
            tabelle_räume.ISO, tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`,
            tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe,
            tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`,
            tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Allgemeine Hygieneklasse`
            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
            WHERE (((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . "));";

    $result2 = $mysqli->query($sql);
    while ($row = $result2->fetch_assoc()) {
        // ---------------- Kopfdaten ----------------
        $pdf->SetFont('helvetica', 'B', 10);
        rb_row($pdf, [[100, "Raum: " . $row['Raumbezeichnung'], 'L'], [80, "Nummer: " . $row['Raumnr'], 'L']]);
        $pdf->SetFont('helvetica', '', 10);
        rb_row($pdf, [[100, "Bereich: " . $row['Raumbereich Nutzer'], 'L'], [80, "Geschoss: " . $row['Geschoss'], 'L']]);
        rb_row($pdf, [[100, "Raumfläche: " . $row['Nutzfläche'] . " m2", 'L']]);
        rb_row($pdf, [[100, "Projekt: " . $row['Projektname'], 'L'], [80, "Bauteil: " . $row['Bauabschnitt'], 'L']]);
        rb_row($pdf, [[100, "Projektstatus: " . $row['Bezeichnung'], 'L'], [80, "Bauetappe: " . $row['Bauetappe'], 'L']], 0, 'B');

        // ---------------- Allgemein ----------------
        $y0 = $pdf->GetY();
        rb_label($pdf, 40, "Strahlenanwendung: ");
        if ($row['Strahlenanwendung'] === '0' || $row['Strahlenanwendung'] === '1') {
            rb_check($pdf, 60, $row['Strahlenanwendung']);
        } else {
            $pdf->MultiCell(60, 6, "Quasi stationär", 0, 'L', 0, 0);
        }
        // Rauminfo kann mehrzeilig sein -> Cursor unter die höhere Zelle setzen
        $pdf->SetFont('helvetica', '', 10);
        $rauminfo = "Rauminfo: " . $row['Allgemeine Hygieneklasse'];
        $hInfo = max(6, $pdf->getStringHeight(80, $rauminfo, false, true, '', 1));
        $pdf->MultiCell(80, $hInfo, $rauminfo, 0, 'L', 0, 0);
        $pdf->SetY($y0 + $hInfo);

        rb_label($pdf, 40, "Laseranwendung: ");
        rb_check($pdf, 40, $row['Laseranwendung']);
        rb_endLine($pdf);

        rb_label($pdf, 40, "Abdunkelbarkeit: ");
        rb_check($pdf, 40, $row['Abdunkelbarkeit']);
        rb_endLine($pdf, 1);
        // Geräte-Anmerkung wird bewusst nicht ausgegeben

        // ---------------- Elektro ----------------
        rb_separator($pdf);
        rb_section($pdf, "Elektro: ");
        rb_label($pdf, 40, "ÖVE E8101:");
        $pdf->MultiCell(10, 6, " " . $row['Anwendungsgruppe'], 0, 'L', 0, 0);
        rb_label($pdf, 20, "AV: ");
        rb_check($pdf, 10, $row['AV']);
        rb_label($pdf, 20, "SV: ");
        rb_check($pdf, 10, $row['SV']);
        rb_label($pdf, 20, "ZSV: ");
        rb_check($pdf, 10, $row['ZSV']);
        rb_label($pdf, 20, "USV: ");
        rb_check($pdf, 10, $row['USV']);
        rb_endLine($pdf);

        rb_label($pdf, 40, "FB ÖNORM B5220:");
        $pdf->MultiCell(20, 6, " " . $row['Fussboden OENORM B5220'], 0, 'L', 0, 0);
        rb_label($pdf, 10, "IT: ");
        rb_check($pdf, 10, $row['IT Anbindung']);

        rb_anmerkung($pdf, $row['Anmerkung Elektro']);

        // ---------------- Haustechnik ----------------
        rb_separator($pdf);
        rb_section($pdf, "Haustechnik: ");
        rb_label($pdf, 40, "H6020:");
        $pdf->MultiCell(10, 6, " " . $row['H6020'], 0, 'L', 0, 0);

        rb_anmerkung($pdf, $row['Anmerkung HKLS']);

        // ---------------- Med-Gas ----------------
        rb_separator($pdf);
        rb_section($pdf, "Med-Gas: ", 18);
        rb_label($pdf, 40, "1 Kreis O2: ");
        rb_check($pdf, 10, $row['1 Kreis O2']);
        rb_label($pdf, 20, "2 Kreis O2: ");
        rb_check($pdf, 10, $row['2 Kreis O2']);
        rb_label($pdf, 20, "1 Kreis VA: ");
        rb_check($pdf, 10, $row['1 Kreis Va']);
        rb_label($pdf, 20, "2 Kreis VA: ");
        rb_check($pdf, 10, $row['2 Kreis Va']);
        rb_endLine($pdf);

        rb_ensureSpace($pdf, 6);
        rb_label($pdf, 40, "1 Kreis DL5: ");
        rb_check($pdf, 8, $row['1 Kreis DL-5']);
        rb_label($pdf, 22, "2 Kreis DL5: ");
        rb_check($pdf, 10, $row['2 Kreis DL-5']);
        rb_label($pdf, 20, "DL10: ");
        rb_check($pdf, 10, $row['DL-10']);
        rb_label($pdf, 20, "DL-tech: ");
        rb_check($pdf, 10, $row['DL-tech']);
        rb_endLine($pdf);

        rb_ensureSpace($pdf, 6);
        rb_label($pdf, 40, "CO2: ");
        rb_check($pdf, 8, $row['CO2']);
        rb_label($pdf, 22, "N2O: ");
        rb_check($pdf, 10, $row['N2O']);
        rb_label($pdf, 20, "NGA: ");
        rb_check($pdf, 10, $row['NGA']);


        rb_anmerkung($pdf, $row['Anmerkung MedGas']);

        // ---------------- Bau-Statik ----------------
        // Nur eine Anmerkung, keine Parameter: Block entfällt, wenn leer.
        $bauStatik = trim((string)$row['Anmerkung BauStatik']);
        if ($bauStatik !== '' && $bauStatik !== 'keine Angaben') {
            $bauStatik = br2nl($bauStatik);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $hText = $pdf->getStringHeight(170, $bauStatik, false, true, '', 1);
            // Überschrift + Text zusammenhalten; sehr lange Texte dürfen weiterfließen
            rb_ensureSpace($pdf, 1 + 8 + (($hText <= rb_usableHeight($pdf) - 9) ? $hText : 12));
            rb_separator($pdf);
            rb_section($pdf, "Bau-Statik-Anmerkung:");
            $pdf->MultiCell(10, 0, "", 0, 'L', 0, 0);
            $pdf->MultiCell(170, 0, $bauStatik, 0, 'L', 0, 1);   // ln=1 -> Cursor unter den Text
            $pdf->Ln(1);
        }
    }

    // -------------------------Elemente im Raum laden--------------------------
    $sql = "SELECT tabelle_elemente.ElementID, 
       tabelle_elemente.Bezeichnung, 
       tabelle_varianten.Variante,
       tabelle_räume_has_tabelle_elemente.Anzahl,
       tabelle_räume_has_tabelle_elemente.Standort,
       tabelle_räume_has_tabelle_elemente.Verwendung,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG,
       tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten,
       tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer,
       tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr,
       tabelle_hersteller.Hersteller, tabelle_geraete.Typ,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . (int)$_SESSION["projectID"] . ") AND ((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . ") AND tabelle_räume_has_tabelle_elemente.Anzahl > 0)
    ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

    $result = $mysqli->query($sql);

    // Alle Einträge vorab einlesen (nur erste Zeile je Raumbuch-Eintrag;
    // weitere Bestandsgeräte desselben Eintrags werden nicht ausgegeben),
    // damit bekannt ist, welcher Eintrag der letzte ist.
    $elementRows = array();
    $idRoombookEntry = 0;
    while ($row = $result->fetch_assoc()) {
        if ($idRoombookEntry == $row['id']) {
            continue;
        }
        $idRoombookEntry = $row['id'];
        $elementRows[] = $row;
    }

    // Keine Elemente: Hinweis statt leerer Tabelle
    if (count($elementRows) === 0) {
        rb_ensureSpace($pdf, 1 + 8 + 6);
        rb_separator($pdf);
        rb_section($pdf, "Elemente im Raum: ");
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->SetTextColor(0, 0, 0);  $pdf->MultiCell(10, 6, "", 0, 'L', 0, 1);
        $pdf->MultiCell(180, 6, "Keine MT-Elemente im Raum vorgesehen.", 0, 'L', 0, 1);
        continue;
    }

    // Tabellenkopf ausgeben (erste Seite: mit Trennlinie davor)
    $firstBlockHeight = 0;
    if (count($elementRows) > 0) {
        $firstBlockHeight = rb_elementBlockHeight($pdf, $elementRows[0], rb_elementInfo($elementRows[0], $variantenInfos));
    }
    rb_ensureSpace($pdf, 1 + rb_elementHeaderHeight($pdf) + min($firstBlockHeight, 40) + RB_NOTE_HEIGHT);
    rb_separator($pdf);
    rb_elementHeader($pdf, false);

    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    $lastIndex = count($elementRows) - 1;
    foreach ($elementRows as $idx => $row) {
        $fill = !$fill;
        $additionalRoombookData = rb_elementInfo($row, $variantenInfos);
        $blockHeight = rb_elementBlockHeight($pdf, $row, $additionalRoombookData);

        // Elementzeile + Infozeile immer zusammen halten.
        // Außer beim letzten Eintrag muss zusätzlich Platz für den
        // Hinweis "Fortsetzung folgt" bleiben.
        $needed = $blockHeight + (($idx < $lastIndex) ? RB_NOTE_HEIGHT : 0);
        $fitsOnEmptyPage = $blockHeight <= rb_usableHeight($pdf) - rb_elementHeaderHeight($pdf) - RB_NOTE_HEIGHT;
        if ($fitsOnEmptyPage && $pdf->GetY() + $needed >= rb_maxY($pdf)) {
            rb_continuationNote($pdf);
            $pdf->AddPage();
            rb_elementHeader($pdf, true);
        }

        $pdf->SetFont('helvetica', '', 8);
        rb_row($pdf, [
            [20, $row['Gewerke_Nr'], 'C'],
            [20, $row['GHG'], 'C'],
            [20, $row['ElementID'], 'C'],
            [20, $row['Variante'], 'C'],
            [50, $row['Bezeichnung'], 'L'],
            [20, $row['Anzahl'], 'C'],
            [30, ($row['Neu/Bestand'] == 1) ? "Nein" : "Ja", 'C'],
        ], $fill);

        $pdf->SetFont('helvetica', 'I', 6);
        rb_row($pdf, [
            [20, "", 'C'],
            [20, "", 'C'],
            [20, "", 'C'],
            [20, "", 'C'],
            [50, $additionalRoombookData, 'L'],
            [20, "", 'C'],
            [30, "", 'C'],
        ], $fill);
    }
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Bauangaben'), 'I');
$_SESSION["PDFHeaderSubtext"] = "";


// =====================================================================
// Hilfsfunktionen Elementtabelle
// =====================================================================

/** Zusatzinfo-Text (Parameter, Standort, Verwendung, Kommentar, Bestand) eines Eintrags. */
function rb_elementInfo($row, $variantenInfos)
{
    $additionalRoombookData = "";
    foreach ($variantenInfos as $array) {
        if ($array['elementID'] === $row['TABELLE_Elemente_idTABELLE_Elemente']
            && $array['VarID'] === $row['tabelle_Varianten_idtabelle_Varianten']) {
            $additionalRoombookData .= "\n" . $array['Kategorie'] . "-" . $array['Bezeichnung'] . ": " . $array['Wert'] . " " . $array['Einheit'];
        }
    }
    $additionalRoombookData .= ($row['Standort'] == 1) ? "Standort: Ja  - " : "Standort: Nein  - ";
    $additionalRoombookData .= ($row['Verwendung'] == 1) ? "Verwendung: Ja  - " : "Verwendung: Nein  - ";
    if (null != ($row['Kurzbeschreibung'])) {
        $additionalRoombookData .= "\nKommentar: " . $row['Kurzbeschreibung'];
    }
    if (null != ($row['Inventarnummer'])) {
        $additionalRoombookData .= "\nBestandsgerät 1:\n     Inventarnummer: " . $row['Inventarnummer'];
    }
    if (null != ($row['Seriennummer'])) {
        $additionalRoombookData .= "\n     Seriennummer: " . $row['Seriennummer'];
    }
    if (null != ($row['Anschaffungsjahr'])) {
        $additionalRoombookData .= "\n     Anschaffungsjahr: " . $row['Anschaffungsjahr'];
    }
    if (null != ($row['Hersteller'])) {
        $additionalRoombookData .= "\n     Gerät: " . $row['Hersteller'] . " " . $row['Typ'];
    }

    return $additionalRoombookData;
}

/** Gesamthöhe von Elementzeile + Infozeile (für "nicht trennen"). */
function rb_elementBlockHeight($pdf, $row, $additionalRoombookData)
{
    $pdf->SetFont('helvetica', '', 8);
    $hMain = 0;
    foreach ([[20, $row['Gewerke_Nr']], [20, $row['GHG']], [20, $row['ElementID']], [20, $row['Variante']],
                 [50, $row['Bezeichnung']], [20, $row['Anzahl']]] as $c) {
        $hMain = max($hMain, $pdf->getStringHeight($c[0], (string)$c[1], false, true, '', 1));
    }
    $pdf->SetFont('helvetica', 'I', 6);
    $hAdd = $pdf->getStringHeight(50, $additionalRoombookData, false, true, '', 0);
    return $hMain + $hAdd;
}

/** Höhe von Überschrift + Spaltenkopf der Elementtabelle. */
function rb_elementHeaderHeight($pdf)
{
    $pdf->SetFont('helvetica', '', 10);
    return 8 + $pdf->getStringHeight(50, "ElementID", false, true, '', 1);
}

/** Überschrift + Spaltenkopf der Elementtabelle; bei Folgeseiten mit "(Fortsetzung)". */
function rb_elementHeader($pdf, $continued)
{
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $title = $continued ? "Elemente im Raum (Fortsetzung):" : "Elemente im Raum: ";
    $pdf->MultiCell(100, 8, $title, 0, 'L', 0, 1);
    rb_row($pdf, [
        [20, "Gewerk", 'C'],
        [20, "GHG", 'C'],
        [20, "ElementID", 'C'],
        [20, "Var", 'C'],
        [50, "Element", 'L'],
        [20, "Stk", 'C'],
        [30, "Bestand", 'C'],
    ], 0, 'B');
}

/** Hinweis am Seitenende, dass die Tabelle auf der nächsten Seite weitergeht. */
function rb_continuationNote($pdf)
{
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(180, RB_NOTE_HEIGHT, "Fortsetzung auf der nächsten Seite ...", 'T', 1, 'R');
}