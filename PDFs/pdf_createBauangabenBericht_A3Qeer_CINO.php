<?php

require_once '../utils/_utils.php';
include 'pdf_createBericht_MYPDFclass_A3Queer.php'; //require_once('../TCPDF-main/TCPDF-main/tcpdf.php'); is in class file
include '_pdf_createBericht_utils.php';
include 'pdf_createMTTabelle.php';

check_login();


function renderElectricalItems($pdf, $items, $parameter_changes, $row, $font_size, $col_map, $horizontalSpacerLN2, $block_header_w): void
{
    foreach ($items as $item) {
        if ($pdf->GetX() > 300) {
            $pdf->Ln($horizontalSpacerLN2);
            $pdf->MultiCell($block_header_w, 10, "", 0, 'L', 0, 0);
        }

        multicell_text_hightlight(
            $pdf,
            $item['col'],
            $font_size,
            $item['field'],
            $item['label'],
            $parameter_changes
        );
        if (!empty($item['show_checkbox'])) {
            hackerlA3($pdf, $font_size, $col_map['checkbox'], $row[$item['field']], "JA");
        } else {
            switch ($item['format'] ?? '') {
                case 'kify':
                    multicell_with_nr($pdf, kify($row[$item['field']]), $item['unit'], $font_size, $col_map['value']);
                    break;
                case 'number':
                    multicell_with_nr($pdf, $row[$item['field']], $item['unit'], $font_size, $col_map['value']);
                    break;
                case 'string':
                    multicell_with_str($pdf, $row[$item['field']], $col_map['value'], $item['unit']);
                    break;
                default:
                    // fallback
                    multicell_with_str($pdf, $row[$item['field']], $col_map['value'], $item['unit'] ?? '');
            }
        }
    }
}

$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);
$Änderungsdatum = getValidatedDateFromURL();

include "pdf_createBauangabenBericht_constDefinitions.php";
//     -----   FORMATTING VARIABLES    -----
$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
/** @noinspection PhpUndefinedConstantInspection */
$SB = 420 - 2 * PDF_MARGIN_LEFT;  // A4: 210 x 297 // A3: 297 x 420
$SH = 297 - $marginTop - $marginBTM; // PDF_MARGIN_FOOTER;
$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 5;
$horizontalSpacerLN3 = 8;

$font_size = 6;
$block_header_height = 10;
$block_header_w = 25;


$e_C = ($SB - $block_header_w - (($SB - $block_header_w) / 18)) / 6;
$e_C_3rd = ($e_C / 3);
$e_C_2_3rd = $e_C - $e_C_3rd;

$einzugPlus = 0; //um den text auf die Höhe der anderen Angaben zu shiften bei ANM BO


$colour_line = array(110, 150, 80);
$style_dashed = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line); //$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 6, 'color' => array(110, 150, 80)));
$style_normal = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line);

/** @noinspection PhpUndefinedConstantInspection */
$pdf = new MYPDF('L', PDF_UNIT, "A3", true, 'UTF-8', false, true);
/** @noinspection PhpUndefinedConstantInspection */
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A3", "Bauangaben");
$pdf->AddPage('L', 'A3');
$pdf->SetFillColor(0, 0, 0, 0); //$pdf->SetFillColor(244, 244, 244);
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_normal);


$mysqli = utils_connect_sql();
$isnotVorentwurf = $_SESSION["projectPlanungsphase"] !== "Vorentwurf";
foreach ($roomIDsArray as $valueOfRoomID) {

    $stmt = $mysqli->prepare("SELECT * FROM `tabelle_raeume_aenderungen` WHERE `raum_id`= ?  AND `Timestamp` > ?"); // (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
    $stmt->bind_param("is", $valueOfRoomID, $Änderungsdatum);
    $stmt->execute();
    $result = $stmt->get_result();
    $changeSqlResult = array();
    while ($row = $result->fetch_assoc()) {
        $changeSqlResult[] = $row;
    }
    $parameter_changes_t_räume = array();


    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.`Fussboden OENORM B5220`, 
    tabelle_räume.`Allgemeine Hygieneklasse`, tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, 
    tabelle_räume.GMP, tabelle_räume.ISO, tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
    tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, 
    tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`,
    tabelle_räume.HT_Waermeabgabe_W, tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_räume.`Fussboden`, ROUND(tabelle_räume.`Umfang`,2) AS Umfang, ROUND(tabelle_räume.`Volumen`,2) AS Volumen,
    tabelle_räume.`Raumhoehe`, tabelle_räume.`Raumhoehe 2`, tabelle_räume.`Belichtungsfläche`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.ET_Anschlussleistung_W, tabelle_räume.ET_Anschlussleistung_AV_W, 
    tabelle_räume.ET_Anschlussleistung_SV_W, tabelle_räume.ET_Anschlussleistung_ZSV_W, tabelle_räume.ET_Anschlussleistung_USV_W, tabelle_räume.`EL_AV Steckdosen Stk`, tabelle_räume.`EL_SV Steckdosen Stk`, tabelle_räume.`EL_ZSV Steckdosen Stk`, 
    tabelle_räume.`EL_USV Steckdosen Stk`, tabelle_räume.`ET_RJ45-Ports`, "
        . "tabelle_räume.`EL_Roentgen 16A CEE Stk`,tabelle_räume.GMP, tabelle_räume.HT_Abluft_Digestorium_Stk,tabelle_räume.HT_Notdusche, tabelle_räume.VE_Wasser, tabelle_räume.ET_16A_3Phasig_Einzelanschluss, "
        . "tabelle_räume.HT_Punktabsaugung_Stk, tabelle_räume.HT_Abluft_Sicherheitsschrank_Unterbau_Stk , tabelle_räume.HT_Abluft_Sicherheitsschrank_Stk, tabelle_räume.HT_Abluft_Vakuumpumpe,"
        . " tabelle_räume.`EL_Laser 16A CEE Stk`, tabelle_räume.`EL_Einzel-Datendose Stk`, tabelle_räume.`EL_Doppeldatendose Stk`, tabelle_räume.`EL_Bodendose Typ`, tabelle_räume.`EL_Bodendose Stk`, tabelle_räume.`EL_Beleuchtung 1 Typ`,tabelle_räume.`EL_Beleuchtung 2 Typ`, tabelle_räume.`EL_Beleuchtung 3 Typ`, tabelle_räume.`EL_Beleuchtung 4 Typ`, tabelle_räume.`EL_Beleuchtung 5 Typ`, tabelle_räume.`EL_Beleuchtung 1 Stk`, tabelle_räume.`EL_Beleuchtung 2 Stk`, tabelle_räume.`EL_Beleuchtung 3 Stk`, tabelle_räume.`EL_Beleuchtung 4 Stk`, tabelle_räume.`EL_Beleuchtung 5 Stk`, tabelle_räume.`EL_Lichtschaltung BWM JA/NEIN`, tabelle_räume.`EL_Beleuchtung dimmbar JA/NEIN`, tabelle_räume.`EL_Brandmelder Decke JA/NEIN`, tabelle_räume.`EL_Brandmelder ZwDecke JA/NEIN`, tabelle_räume.`EL_Kamera Stk`, tabelle_räume.`EL_Lautsprecher Stk`, tabelle_räume.`EL_Uhr - Wand Stk`, tabelle_räume.`EL_Uhr - Decke Stk`, tabelle_räume.`EL_Lichtruf - Terminal Stk`, tabelle_räume.`EL_Lichtruf - Steckmodul Stk`, tabelle_räume.`EL_Lichtfarbe K`, tabelle_räume.`EL_Notlicht RZL Stk`, tabelle_räume.`EL_Notlicht SL Stk`, tabelle_räume.`EL_Jalousie JA/NEIN`, tabelle_räume.`HT_Luftmenge m3/h`, CAST(REPLACE(tabelle_räume.`HT_Luftwechsel 1/h`,',','.') as decimal(10,2)) AS `HT_Luftwechsel`, tabelle_räume.`HT_Kühlung Lueftung W`, tabelle_räume.`HT_Heizlast W`, tabelle_räume.`HT_Kühllast W`, tabelle_räume.`HT_Fussbodenkühlung W`, tabelle_räume.`HT_Kühldecke W`, tabelle_räume.`HT_Fancoil W`, tabelle_räume.`HT_Summe Kühlung W`, tabelle_räume.`HT_Raumtemp Sommer °C`, tabelle_räume.`HT_Raumtemp Winter °C`, tabelle_räume.`AR_Ausstattung`, tabelle_räume.`Aufenthaltsraum` "
        . "FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen WHERE (((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . "))";

    $result_rooms = $mysqli->query($sql);
    while ($row = $result_rooms->fetch_assoc()) {
        $raumbereich = $row['Raumbereich Nutzer'];
        $pdf->SetFillColor(255, 255, 255);
        raum_header($pdf, $horizontalSpacerLN3, $SB, $row['Raumbezeichnung'], $row['Raumnr'], $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Nutzfläche'], "A3XC", $parameter_changes_t_räume, "", $row['Nutzfläche']); //utils function

        if (null != ($row['Anmerkung FunktionBO'])) {
            $outstr = format_text(clean_string(br2nl($row['Anmerkung FunktionBO'])));
            $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
            $i = ($rowHeightComment > 6) ? $horizontalSpacerLN : 0;

            block_label_queer($block_header_w, $pdf, "BO-Beschr.", $rowHeightComment + $i, $block_header_height, $SB);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->MultiCell($SB - $block_header_w, $rowHeightComment, $outstr, 0, 'L', 0, 1);

        }

//   ---------- ALLGEMEIN   ----------

//       ---------- ELEKTRO -----------

        $i = 12 + $horizontalSpacerLN + $horizontalSpacerLN2;

        $Block_height = 6 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung Elektro'], $SB) + $i;
        block_label_queer($block_header_w, $pdf, "Elektro", $Block_height, $block_header_height, $SB);
        $electricalItems = [
            [
                'field' => 'ET_Anschlussleistung_W',
                'label' => 'Raum Anschlussleistung: ca.',
                'cell_type' => 'highlight',
                'col' => $e_C,
                'show_checkbox' => false,
                'unit' => 'W',
                'format' => 'kify'
            ],
            [
                'field' => 'AV',
                'label' => 'AV: ',
                'cell_type' => 'highlight',
                'col' => $e_C_2_3rd,
                'show_checkbox' => true,
                'unit' => null,
            ],
            [
                'field' => 'SV',
                'label' => 'SV: ',
                'cell_type' => 'highlight',
                'col' => $e_C_2_3rd,
                'show_checkbox' => true,
                'unit' => null,
            ],
            [
                'field' => 'IT Anbindung',
                'label' => 'IT Anschl.: ',
                'cell_type' => 'highlight',
                'col' => $e_C_2_3rd,
                'show_checkbox' => true,
                'unit' => null,
            ],
            [
                'field' => 'ET_RJ45-Ports',
                'label' => 'RJ45-Ports: ',
                'cell_type' => 'highlight',
                'col' => $e_C_2_3rd,
                'show_checkbox' => true,
                'unit' => null,
            ],
            [
                'field' => ' ',
                'label' => ' ',
                'cell_type' => '',
                'col' => $e_C,
                'show_checkbox' => false,
                'unit' => '',
                'format' => ''
            ],
            [
                'field' => 'EL_AV Steckdosen Stk',
                'label' => 'AV SSD: ',
                'cell_type' => 'highlight',
                'col' => $e_C_2_3rd,
                'show_checkbox' => false,
                'unit' => 'Stk',
                'format' => 'number'
            ],
            [
                'field' => 'EL_SV Steckdosen Stk',
                'label' => 'SV SSD: ',
                'cell_type' => 'highlight',
                'col' => $e_C_2_3rd,
                'show_checkbox' => false,
                'unit' => 'Stk',
                'format' => 'string'
            ]
        ];

        $col_map = [
            'checkbox' => $e_C_3rd,
            'value' => $e_C_3rd
        ];

        renderElectricalItems($pdf, $electricalItems, $parameter_changes_t_räume, $row, $font_size, $col_map, $horizontalSpacerLN2, $block_header_w);
        $pdf->Ln($horizontalSpacerLN2);
        if ("" !== trim($row['Anmerkung Elektro'] ?? "")) {
            anmA3($pdf, $row['Anmerkung Elektro'], $SB, $block_header_w);
        }


//
//// ---------- HAUSTEK ---------
//
        $Block_height = 6 + $horizontalSpacerLN2 + getAnmHeight($pdf, $row['Anmerkung HKLS'], $SB);
        block_label_queer($block_header_w, $pdf, "Haustechnik", $Block_height, $block_header_height, $SB);

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "HT_Waermeabgabe_W", "Abwärme: ca.", $parameter_changes_t_räume);
        $abwrem_out = ($row['HT_Waermeabgabe_W'] === "0" || $row['HT_Waermeabgabe_W'] == 0 || $row['HT_Waermeabgabe_W'] == "-")
            ? "k.A."
            : kify($row['HT_Waermeabgabe_W']) . "W";
        multicell_with_str($pdf, $abwrem_out, $e_C_3rd, "");

        $haustechnikItems = [
            ['H6020', ': ', 'Norm'],
            ['VE_Wasser', 'Voll entsalztes Wasser:', ''],
            ['HT_Abluft_Digestorium_Stk', 'Abluft Digestorium:', 'Stk'],
            ['HT_Punktabsaugung_Stk', 'Punktabsaugung:', 'Stk'],
            ['HT_Abluft_Vakuumpumpe', 'Abluft Vakuumpumpe:', 'Stk']

        ];

        foreach ($haustechnikItems as $item) {
            if ($item[0] === 'VE_Wasser') {
                $pdf->Ln($horizontalSpacerLN2);
                $pdf->MultiCell($block_header_w + $einzugPlus, $block_header_height, "", 0, 'L', 0, 0);
            }
            if ($item[0] === 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk' || $item[0] === 'HT_Abluft_Sicherheitsschrank_Stk') {
                multicell_text_hightlight($pdf, $e_C + $e_C_3rd, $font_size, $item[0], $item[1], $parameter_changes_t_räume);
            } else if ($item[0] === "VE_Wasser") {
                multicell_text_hightlight($pdf, $e_C, $font_size, $item[0], $item[1], $parameter_changes_t_räume);
            } else {
                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, $item[0], $item[1], $parameter_changes_t_räume);
            }

            $value = ($item[0] === 'VE_Wasser') ? translate_1_to_yes($row[$item[0]]) : $row[$item[0]];

            if ($item[0] === "H6020") {
                multicell_with_str($pdf, $value, $e_C, $item[2]);
            } else if ($item[0] === "HT_Abluft_Vakuumpumpe") {
                multicell_with_str($pdf, $value, $e_C_2_3rd, $item[2]);
            } else {
                multicell_with_str($pdf, $value, $e_C_3rd, $item[2]);
            }
        }

        $pdf->Ln($horizontalSpacerLN2);
        if (trim($row['Anmerkung HKLS'] ?? "") !== "") {
            anmA3($pdf, $row['Anmerkung HKLS'], $SB, $block_header_w);
        }

        /// ----------- MEDGAS -----------

        $Block_height = 12 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung MedGas'], $SB);
        block_label_queer($block_header_w, $pdf, "Med.-Gas", $Block_height, $block_header_height, $SB);

        $medGasItems = [
            '1 Kreis O2', 'CO2', 'DL-10', 'N2' //,    '2 Kreis O2',
        ];
        foreach ($medGasItems as $item) {
            $label = str_replace(['1 Kreis ', '2 Kreis ', '-'], ['1 Kreis ', '2 Kreise ', ''], $item);
            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, $item, "$label: ", $parameter_changes_t_räume);
            hackerlA3($pdf, $font_size, $e_C_3rd, $row[$item], 1);
            $x = 0;
            //if ($item === 'DL-10') {
            //    $pdf->Ln($horizontalSpacerLN);
            //    $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            //    $x=$e_C_2_3rd;
            //}
        }

        $pdf->Ln($horizontalSpacerLN2);
        if (trim($row['Anmerkung MedGas'] ?? "") !== "") {
            anmA3($pdf, $row['Anmerkung MedGas'], $SB, $block_header_w);
        }

        ////     ------- MT Tabelle  ---------
        // -------------------------Elemente im Raum laden--------------------------
        $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
            tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
            tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $valueOfRoomID . ") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
        $resultX = $mysqli->query($sql);
        $rowcounter = 0;
        while ($row2 = $resultX->fetch_assoc()) {
            $rowcounter++;
        }
        $resultX->data_seek(0);

        if ($isnotVorentwurf && $rowcounter > 0) {
            // -----------------Projekt Elementparameter/Variantenparameter laden----------------------------
            $sql = "SELECT tabelle_parameter_kategorie.Kategorie,tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.Abkuerzung
                FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
                ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                WHERE ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte=" . $_SESSION["projectID"] . ") AND (tabelle_parameter.`Bauangaben relevant` = 1) 
                AND NOT (tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = 18 )) 
                GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
                ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
            $result1 = $mysqli->query($sql);

            // -------------------------Elemente parameter -------------------------
            $sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten,
                            tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter
                            FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)
                            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                           WHERE ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte=" . $_SESSION["projectID"] . ") AND (tabelle_parameter.`Bauangaben relevant` = 1)
                            AND NOT (tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = 18 ))
                            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";


            $result3 = $mysqli->query($sql);

            $sql = "SELECT tabelle_projekt_elementparameter_aenderungen.idtabelle_projekt_elementparameter_aenderungen, tabelle_projekt_elementparameter_aenderungen.projekt, tabelle_projekt_elementparameter_aenderungen.element, tabelle_projekt_elementparameter_aenderungen.parameter, tabelle_projekt_elementparameter_aenderungen.variante, tabelle_projekt_elementparameter_aenderungen.wert_alt, tabelle_projekt_elementparameter_aenderungen.wert_neu, tabelle_projekt_elementparameter_aenderungen.einheit_alt, tabelle_projekt_elementparameter_aenderungen.einheit_neu, tabelle_projekt_elementparameter_aenderungen.timestamp, tabelle_projekt_elementparameter_aenderungen.user
                FROM tabelle_projekt_elementparameter_aenderungen
                WHERE (((tabelle_projekt_elementparameter_aenderungen.projekt)=" . $_SESSION["projectID"] . "))
                AND tabelle_projekt_elementparameter_aenderungen.timestamp > '$Änderungsdatum'
                ORDER BY tabelle_projekt_elementparameter_aenderungen.timestamp DESC;";
            $changes = $mysqli->query($sql);
            $dataChanges = array();
            while ($row3 = $changes->fetch_assoc()) {
                $dataChanges[] = $row3;
            }
            $dataChanges = filter_old_equal_new($dataChanges);

            //$upcmn_blck_size = 10 + $rowcounter * 5;
            $upcmn_blck_size = 30;
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB);
            make_MT_details_table($pdf, $resultX, $result1, $result3, $SB, $SH, $dataChanges);

        } else if ($rowcounter > 0) {
            $upcmn_blck_size = 10 + $rowcounter / 2 * 5;
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB); //el_in_room_html_table($pdf, $resultX, 1, "A3", $SB-$block_header_w);
            $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_dashed);
            make_MT_list($pdf, $SB, $block_header_w, $rowcounter, $resultX, $style_normal, $style_dashed);

        } else {
            $pdf->Line(15, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_normal);
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB); //el_in_room_html_table($pdf, $resultX, 1, "A3", $SB-$block_header_w);
            $pdf->Multicell(0, 0, "Keine medizintechnische Ausstattung.", "", "L", 0, 0);
            $pdf->Ln();
        }
    } //sql:fetch-assoc
}// for every room

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('BAUANGABEN'), 'I');


