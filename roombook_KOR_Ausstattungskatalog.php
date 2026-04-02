<?php
require_once 'utils/_utils.php';
include "utils/_format.php";
init_page_serversides();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>KOR_Austattungskatalog</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
          rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div id="limet-navbar"></div>
    <div class="mt-2 card">
        <div class="card-header">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    <b>KOR Katalog</b>
                </div>

                <div class="col-xxl-4 col-lg-5 col-12">
                    <label for="select_raumbereiche" class="invisible"></label><select id="select_raumbereiche"
                                                                                       class="form-select form-select-sm"
                                                                                       multiple>
                        <?php
                        $mysqli_rb = utils_connect_sql();
                        $projectID_rb = (int)($_SESSION["projectID"] ?? 0);
                        $stmt_rb = $mysqli_rb->prepare("
                            SELECT DISTINCT `Raumbereich Nutzer`
                            FROM tabelle_räume
                            WHERE tabelle_projekte_idTABELLE_Projekte = ?
                              AND `Raumbereich Nutzer` IS NOT NULL
                              AND `Raumbereich Nutzer` != ''
                            ORDER BY `Raumbereich Nutzer`
                        ");
                        $stmt_rb->bind_param("i", $projectID_rb);
                        $stmt_rb->execute();
                        $raumbereiche = $stmt_rb->get_result()->fetch_all(MYSQLI_ASSOC);
                        $stmt_rb->close();
                        $mysqli_rb->close();
                        foreach ($raumbereiche as $rb):
                            ?>
                            <option value="<?= htmlspecialchars($rb['Raumbereich Nutzer'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($rb['Raumbereich Nutzer'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-dark" id="btn_alle_auswaehlen" type="button"
                            title="Alle Raumbereiche wählen">
                        <i class="fas fa-check-double"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ms-1" id="btn_auswahl_leeren" type="button"
                            title="Filter leeren (alle anzeigen)">
                        <i class="fas fa-times"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ms-1"   type="button"
                            title="       KOR Elementlistes">
                        <a class="text-success" href="/roombook_KOR_ElementeListe.php">

                        </a>
                    </button>


                </div>

                <div class="col-auto ms-auto d-flex align-items-center" id="dt-header-controls">
                    <button type="button" class="btn btn-sm btn-info text-nowrap"
                            onclick="show_modal('InfoModal')">
                        <i class="fas fa-question-circle"></i>
                    </button>
                </div>


            </div>
        </div>
        <div class="card-body p-1">
            <?php
            // ---------------------------------------------------------------
            // COLUMN DEFINITIONS
            // ---------------------------------------------------------------
            $columnDefs = [

                // --- Identity ---
                ['header' => 'ID', 'source' => 'elem', 'key' => 'idTABELLE_Elemente', 'hidden' => true],
                ['header' => 'Gewerk', 'source' => 'static', 'key' => 'GPMT'],
                ['header' => 'Bauteilelement ID', 'source' => 'elemCode', 'key' => null],
                ['header' => 'Bauteilelement Bezeichnung', 'source' => 'elem', 'key' => 'Bezeichnung'],
                ['header' => 'Bauteilelement Gruppen ID', 'source' => 'elem', 'key' => 'GewerkBezeichnung'],
                ['header' => 'Bauteilelement ID Planer', 'source' => 'elemCode', 'key' => null],
                ['header' => 'Ortsveränderlich', 'source' => 'ortsv', 'key' => null],
                ['header' => 'Versorgungseinheit', 'source' => 've', 'key' => null, 'center' => true],

                ['header' => 'Bemerkung', 'source' => 'static', 'key' => ''],

                ['header' => 'VEXAT Anforderung', 'source' => 'display', 'key' => "Explosionsschutzzone", 'center' => true, 'group' => 'Elektro',],
                // --- Elektro: Leistung ---
                ['header' => 'Leistung [W]', 'source' => 'display', 'key' => 'Nennleistung', 'suppress_unit' => true, 'group' => 'Elektro',],
                ['header' => 'Netzart', 'source' => 'display', 'key' => 'Netzart', 'group' => 'Elektro', 'hidden' => true, 'suppress_unit' => true],
                ['header' => 'Spannung [V]', 'source' => 'display', 'key' => 'Spannung', 'suppress_unit' => true, 'group' => 'Elektro', 'hidden' => true,],
                ['header' => 'Steckdosen Anz.', 'source' => 'display', 'key' => 'Steckdosen_Anzahl', 'group' => 'Elektro', 'hidden' => true, 'suppress_unit' => true],
                ['header' => 'Direktanschluss', 'source' => 'display', 'key' => 'Direktanschluss', 'group' => 'Elektro', 'hidden' => true, 'suppress_unit' => true],
                ['header' => 'Tageslastanteil', 'source' => 'display', 'key' => "Gleichzeitigkeit", 'center' => true],
                ['header' => 'Absicherung (im Gerät)', 'source' => 'static', 'key' => ''],

                // --- Elektro: 230V Direktanschluss ---
                ['header' => '230V DA AV', 'source' => 'calc_ac', 'key' => 'direkt_230_AV', 'center' => true, 'group' => '230V Direktanschluss'],
                ['header' => '230V DA SV', 'source' => 'calc_ac', 'key' => 'direkt_230_SV', 'center' => true, 'group' => '230V Direktanschluss'],
                ['header' => '230V DA ZSV', 'source' => 'calc_ac', 'key' => 'direkt_230_ZSV', 'center' => true, 'group' => '230V Direktanschluss'],
                ['header' => '230V DA USV', 'source' => 'calc_ac', 'key' => 'direkt_230_USV', 'center' => true, 'group' => '230V Direktanschluss'],

                // --- Elektro: 400V Direktanschluss ---
                ['header' => '400V DA AV', 'source' => 'calc_ac', 'key' => 'direkt_400_AV', 'center' => true, 'group' => '400V Direktanschluss'],
                ['header' => '400V DA SV', 'source' => 'calc_ac', 'key' => 'direkt_400_SV', 'center' => true, 'group' => '400V Direktanschluss'],
                ['header' => '400V DA ZSV', 'source' => 'calc_ac', 'key' => 'direkt_400_ZSV', 'center' => true, 'group' => '400V Direktanschluss'],
                ['header' => '400V DA USV', 'source' => 'calc_ac', 'key' => 'direkt_400_USV', 'center' => true, 'group' => '400V Direktanschluss'],

                // --- Elektro: 230V Steckdosen ---
                ['header' => '230V SD AV', 'source' => 'calc_ac', 'key' => 'steck_230_AV', 'center' => true, 'group' => '230V Steckdosen'],
                ['header' => '230V SD SV', 'source' => 'calc_ac', 'key' => 'steck_230_SV', 'center' => true, 'group' => '230V Steckdosen'],
                ['header' => '230V SD ZSV', 'source' => 'calc_ac', 'key' => 'steck_230_ZSV', 'center' => true, 'group' => '230V Steckdosen'],
                ['header' => '230V SD USV', 'source' => 'calc_ac', 'key' => 'steck_230_USV', 'center' => true, 'group' => '230V Steckdosen'],

                // --- Elektro: 400V Steckdosen ---
                ['header' => '400V SD AV', 'source' => 'calc_ac', 'key' => 'steck_400_AV', 'center' => true, 'group' => '400V Steckdosen'],
                ['header' => '400V SD SV', 'source' => 'calc_ac', 'key' => 'steck_400_SV', 'center' => true, 'group' => '400V Steckdosen'],
                ['header' => '400V SD ZSV', 'source' => 'calc_ac', 'key' => 'steck_400_ZSV', 'center' => true, 'group' => '400V Steckdosen'],
                ['header' => '400V SD USV', 'source' => 'calc_ac', 'key' => 'steck_400_USV', 'center' => true, 'group' => '400V Steckdosen'],

                // --- Elektro: Sonstige ---
                ['header' => '24V Anschluss', 'source' => 'calc_ac', 'key' => '24V', 'center' => true, 'group' => 'Elektro'],
                ['header' => 'Potentialausgleich', 'source' => 'calc_ac', 'key' => 'pa', 'group' => 'Elektro'],
                ['header' => 'IKT Anschluss', 'source' => 'display', 'key' => 'RJ45 Ports', 'group' => 'Elektro', 'suppress_unit' => true],
                ['header' => 'Bemerkung ET', 'source' => 'static', 'key' => ''],

                // --- Medizingas ---
                ['header' => 'O2', 'source' => 'display', 'key' => 'O2 Anschluss', 'group' => 'Medizingas', 'suppress_unit' => true],
                ['header' => 'Med. DL5', 'source' => 'display', 'key' => 'DL-5 Anschluss', 'group' => 'Medizingas', 'suppress_unit' => true],
                ['header' => 'Med. DL10', 'source' => 'display', 'key' => 'DL-10 Anschluss', 'group' => 'Medizingas', 'suppress_unit' => true],
                ['header' => 'NGA', 'source' => 'display', 'key' => 'NGA Anschluss', 'group' => 'Medizingas', 'suppress_unit' => true],
                ['header' => 'VA', 'source' => 'display', 'key' => 'VAC Anschluss', 'group' => 'Medizingas', 'suppress_unit' => true],
                ['header' => 'CO2', 'source' => 'display', 'key' => 'CO2 Anschluss', 'group' => 'Medizingas', 'suppress_unit' => true],

                // --- Tech. Druckluft ---
                ['header' => 'Tech. DL 6 bar', 'source' => 'calc_ac', 'key' => 'dl_6bar', 'center' => true, 'group' => 'Druckluft'],
                ['header' => 'Tech. DL 9 bar', 'source' => 'calc_ac', 'key' => 'dl_9bar', 'center' => true, 'group' => 'Druckluft'],
                ['header' => 'Tech. DL 12 bar', 'source' => 'calc_ac', 'key' => 'dl_12bar', 'center' => true, 'group' => 'Druckluft'],

                // --- Kaltwasser ---
                ['header' => 'KW Stadtwasser', 'source' => 'calc_wc', 'key' => 'kw_stadt_flag', 'center' => true, 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW Stadtwasser l/min', 'source' => 'calc_wc', 'key' => 'kw_stadt_strom', 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW weich <4°DH', 'source' => 'calc_wc', 'key' => 'kw_weich_flag', 'center' => true, 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW weich <4°DH l/min', 'source' => 'calc_wc', 'key' => 'kw_weich_strom', 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW weich <4°DH, 80-120 μS/cm', 'source' => 'calc_wc', 'key' => 'kw_weich_leitf_flag', 'center' => true, 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW weich <4°DH, 80-120 μS l/min', 'source' => 'calc_wc', 'key' => 'kw_weich_leitf_strom', 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW VE <0,2 μS + <10 KBE/ml', 'source' => 'calc_wc', 'key' => 'kw_ve02_flag', 'center' => true, 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW VE <0,2 μS l/min', 'source' => 'calc_wc', 'key' => 'kw_ve02_strom', 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW VE <15 μS/cm', 'source' => 'calc_wc', 'key' => 'kw_ve15_flag', 'center' => true, 'group' => 'Kaltwasser', 'suppress_unit' => true],
                ['header' => 'KW VE <15 μS/cm l/min', 'source' => 'calc_wc', 'key' => 'kw_ve15_strom', 'group' => 'Kaltwasser', 'suppress_unit' => true],

                // --- Warmwasser ---
                ['header' => 'WW Stadtwasser', 'source' => 'calc_wc', 'key' => 'ww_stadt_flag', 'center' => true, 'group' => 'Warmwasser'],
                ['header' => 'WW Stadtwasser l/min', 'source' => 'calc_wc', 'key' => 'ww_stadt_strom', 'group' => 'Warmwasser'],
                ['header' => 'WW weich <4°DH', 'source' => 'calc_wc', 'key' => 'ww_weich_flag', 'center' => true, 'group' => 'Warmwasser'],
                ['header' => 'WW weich <4°DH l/min', 'source' => 'calc_wc', 'key' => 'ww_weich_strom', 'group' => 'Warmwasser'],

                // --- Wasser allgemein ---
                ['header' => 'Fließdruck', 'source' => 'calc_wc', 'key' => 'fliessdruck', 'group' => 'Wasser'],
                ['header' => 'Direktanschluss Wasser', 'source' => 'calc_wc', 'key' => 'direkt_wasser', 'center' => true, 'group' => 'Wasser'],
                ['header' => 'Anschlussdimension [DN]', 'source' => 'calc_wc', 'key' => 'anschluss_dimension', 'group' => 'Wasser'],
                ['header' => 'Anschlusspunkt [Zoll]', 'source' => 'calc_wc', 'key' => 'anschluss_punkt', 'group' => 'Wasser'],
                ['header' => 'Rohrtrenner EN1717', 'source' => 'calc_wc', 'key' => 'rohrtrenner', 'group' => 'Wasser'],
                ['header' => 'Bemerkung Wasser', 'source' => 'static', 'key' => ''],

                // --- Abwasser ---
                ['header' => 'Abwasser Anschluss', 'source' => 'calc_sc', 'key' => 'abwasser_anschl', 'group' => 'Abwasser'],
                ['header' => 'Abwasser Strom', 'source' => 'calc_sc', 'key' => 'abwasser_strom', 'group' => 'Abwasser'],
                ['header' => 'Kondensat', 'source' => 'calc_sc', 'key' => 'kondensat_flag', 'center' => true, 'group' => 'Abwasser'],
                ['header' => 'Kondensat Wert', 'source' => 'calc_sc', 'key' => 'kondensat_wert', 'group' => 'Abwasser'],
                ['header' => 'Abwassertemperatur [°C]', 'source' => 'calc_sc', 'key' => 'abwasser_temp', 'group' => 'Abwasser'],
                ['header' => 'Abwasser fetthaltig', 'source' => 'static', 'key' => ''],

                ['header' => 'Siphon (durch GPHT)', 'source' => 'calc_sc', 'key' => 'siphon', 'center' => true, 'group' => 'Abwasser'],
                ['header' => 'Direktanschluss Abwasser', 'source' => 'static', 'key' => ''],
                ['header' => 'Anschlussdimension AW', 'source' => 'calc_sc', 'key' => 'abwasser_dimension', 'group' => 'Abwasser'],
                ['header' => 'Bemerkung Abwasser', 'source' => 'static', 'key' => ''],

                // --- Lüftung ---
                ['header' => 'Raumwärme sensibel [W]', 'source' => 'calc_sc', 'key' => 'raumwaerme_sensibel', 'group' => 'Lüftung', 'suppress_unit' => true],
                ['header' => 'Raumwärmebelastung latent [W]', 'source' => 'static', 'key' => ''],


                ['header' => 'Abluftmenge', 'source' => 'calc_sc', 'key' => 'abluft_menge', 'group' => 'Lüftung'],
                ['header' => 'Abluftdimension', 'source' => 'calc_sc', 'key' => 'abluft_dimension', 'group' => 'Lüftung'],
                ['header' => 'Direktanschluss Abluft', 'source' => 'calc_sc', 'key' => 'direkt_abluft', 'center' => true, 'group' => 'Lüftung'],
                ['header' => 'Restpressung', 'source' => 'static', 'key' => ''],


                ['header' => 'Abluft Temp >50°C', 'source' => 'calc_sc', 'key' => 'abluft_temp', 'group' => 'Lüftung'],
                ['header' => 'Abluft Luftfeuchtigkeit - Gefahr Kondensat bei Austritt', 'source' => 'static', 'key' => ''],
                ['header' => 'Abluft Sondermaterial Anforderung', 'source' => 'static', 'key' => ''],
                ['header' => 'Abluft-Wrasen', 'source' => 'static', 'key' => ''],
                ['header' => 'Bemerkung Lüftung', 'source' => 'static', 'key' => ''],
                // --- GLT ---
                ['header' => 'GLT Datenpunkt', 'source' => 'calc_sc', 'key' => 'glt_datenpunkt', 'center' => true, 'group' => 'GLT'],
                ['header' => 'Redundanzanforderung', 'source' => 'static', 'key' => ''],
                ['header' => 'Bemerkung GLT', 'source' => 'static', 'key' => ''],
                // --- Kälte ---
                ['header' => 'Kälteleistung [W]', 'source' => 'calc_sc', 'key' => 'kaelteleistung', 'group' => 'Kälte'],
                ['header' => 'Vorlauf Temp [°C]', 'source' => 'calc_sc', 'key' => 'kw_vorlauf_temp', 'group' => 'Kälte'],
                ['header' => 'Vorlauf Anschluss', 'source' => 'calc_sc', 'key' => 'kw_vorlauf_anschluss', 'group' => 'Kälte'],
                ['header' => 'Rücklauf', 'source' => 'static', 'key' => ''],
                ['header' => 'Rücklauf Anschluss', 'source' => 'calc_sc', 'key' => 'kw_ruecklauf_anschluss', 'group' => 'Kälte'],
                ['header' => 'Druckverlust [Pa]', 'source' => 'calc_sc', 'key' => 'druckverlust', 'group' => 'Kälte'],
                ['header' => 'Rücklauf', 'source' => 'static', 'key' => ''],
                // --- Architektur ---
                ['header' => 'Gewicht [kg]', 'source' => 'calc_sc', 'key' => 'gewicht', 'group' => 'Architektur'],
                ['header' => 'Vibration', 'source' => 'static', 'key' => ''],
                ['header' => 'Lärm [dB(A)]', 'source' => 'calc_sc', 'key' => 'laerm', 'group' => 'Architektur', 'suppress_unit' => true],
                ['header' => 'Punktlast abgehängt [N]', 'source' => 'calc_sc', 'key' => 'punktlast_decke', 'group' => 'Architektur', 'suppress_unit' => true],
                ['header' => 'Punktlast Boden [N]', 'source' => 'calc_sc', 'key' => 'punktlast_boden', 'group' => 'Architektur', 'suppress_unit' => true],
                ['header' => 'Bemerkung Architektur', 'source' => 'static', 'key' => ''],
            ];

            $suppress_unit_for = array_column(
                array_filter($columnDefs, fn($c) => !empty($c['suppress_unit'])),
                'key'
            );

            function show_param(array $params, string $name): string
            {
                $v = $params[$name] ?? '';
                return ($v !== '' && $v !== '0') ? htmlspecialchars($v, ENT_QUOTES, 'UTF-8') : '';
            }

            function varianteLetter(int $id): string
            {
                return $id > 0 ? chr(64 + $id) : '';
            }

            function isVersorgungseinheit(string $elementID): bool
            {
                if (str_starts_with($elementID, '1.61.')) return true;
                if (str_starts_with($elementID, '4.35.25.')) return true;
                if ($elementID === '1.35.13.2') return true;
                if ($elementID === '1.35.13.6') return true;
                return false;
            }

            function calc_anschluss_cols(array $params): array
            {
                $netzart = $params['Netzart'] ?? '';
                $spannung = $params['Spannung'] ?? '';
                $direkt = strtolower($params['Direktanschluss'] ?? '');
                $steckdosen = (int)($params['Steckdosen_Anzahl'] ?? 0);

                $netze = ['AV', 'SV', 'ZSV', 'USV'];
                $result = [];
                $isDirekt = in_array($direkt, ['ja', 'yes', '1', 'true']);

                foreach ($netze as $netz) {
                    $netzeInString = array_map('trim', explode('/', $netzart));
                    $hasNetz = in_array($netz, array_map('strtoupper', $netzeInString));
                    $result['direkt_230_' . $netz] = ($hasNetz && $isDirekt && $spannung === '230') ? 1 : '';
                    $result['direkt_400_' . $netz] = ($hasNetz && $isDirekt && $spannung === '400') ? 1 : '';
                    $result['steck_230_' . $netz] = ($hasNetz && $spannung === '230' && $steckdosen > 0) ? $steckdosen : '';
                    $result['steck_400_' . $netz] = ($hasNetz && $spannung === '400' && $steckdosen > 0) ? $steckdosen : '';
                }
                $result['24V'] = ($params['Spannung'] ?? '') == "24" ? 1 : "";

                $druckluft_anschluss = $params['Druckluftanschluss'] ?? '';
                $druckluft_druck = trim($params['Druckluft_Druck'] ?? '');
                $hasDruckluft = ($druckluft_anschluss !== '' && $druckluft_anschluss !== '0');
                $result['dl_6bar'] = ($hasDruckluft && $druckluft_druck === '6') ? 1 : '';
                $result['dl_9bar'] = ($hasDruckluft && $druckluft_druck === '9') ? 1 : '';
                $result['dl_12bar'] = ($hasDruckluft && $druckluft_druck === '12') ? 1 : '';


                $pa = strtolower(trim($params['PA'] ?? ''));
                $result['pa'] = (stripos($pa, 'ja') !== false) ? '1' : ($pa !== '' && $pa !== '0' ? $pa : '');


                return $result;
            }

            function normalize_to_base_unit(string $wert, string $einheit): array
            {
                $wert = trim($wert);
                $einheit = trim($einheit);
                $prefixes = ['G' => 1_000_000_000, 'M' => 1_000_000, 'k' => 1_000];
                $exceptions = ['kg', 'km', 'kn', 'MHz', 'GHz', 'MB', 'GB', 'kN'];
                if (is_numeric(str_replace(',', '.', $wert))) {
                    foreach ($prefixes as $prefix => $multiplier) {
                        if (str_starts_with($einheit, $prefix)) {
                            if (in_array($einheit, $exceptions, true)) break;
                            $baseUnit = substr($einheit, strlen($prefix));
                            $numericVal = (float)str_replace(',', '.', $wert);
                            return ['wert' => (string)($numericVal * $multiplier), 'einheit' => $baseUnit];
                        }
                    }
                }
                return ['wert' => $wert, 'einheit' => $einheit];
            }

            function calc_wasser_cols(array $params, array $display): array
            {
                $r = [];
                $kw_anschluss = trim($params['Kaltwasser_Anschluss'] ?? '');
                $kw_haerte = trim($params['Kaltwasser_Wasserhaerte'] ?? '');
                $kw_leitf = trim($params['Kaltwasser_Restleitfähigkeit'] ?? '');
                $kw_strom = trim($params['Kaltwasser_Strom'] ?? $display['Kaltwasser_Strom'] ?? '');
                $kw_direkt = strtolower(trim($params['Kaltwasser_Direktanschluss'] ?? ''));
                $kw_fliessdruck = trim($params['Kaltwasser_Fließdruck'] ?? '');

                $ww_anschluss = trim($params['Warmwasser_Anschluss'] ?? '');
                $ww_haerte = trim($params['Warmwasser_Wasserhaerte'] ?? '');
                $ww_strom = trim($params['Warmwasser_Strom'] ?? '');
                $ww_direkt = strtolower(trim($params['Warmwasser_Direktanschluss'] ?? ''));
                $ww_fliessdruck = trim($params['Warmwasser_Fließdruck'] ?? '');

                $lw_anschluss = trim($params['Labor_Analysewasser_Anschluss'] ?? '');
                $lw_strom = trim($params['Labor_Analysewasser_Strom'] ?? '');

                $ve_anschluss = trim($params['Voll_entsalztes Wasser_Anschluss'] ?? '');
                $ve_strom = trim($params['Voll_entsalztes Wasser_Strom'] ?? '');

                $trennung = trim($display['Trennung EN1717'] ?? '');

                $hasKW = ($kw_anschluss !== '' && $kw_anschluss !== '0' && strtolower($kw_anschluss) !== 'nein');
                $hasWW = ($ww_anschluss !== '' && $ww_anschluss !== '0' && strtolower($ww_anschluss) !== 'nein');
                $hasLW = ($lw_anschluss !== '' && $lw_anschluss !== '0' && strtolower($lw_anschluss) !== 'nein');
                $hasVE = ($ve_anschluss !== '' && $ve_anschluss !== '0' && strtolower($ve_anschluss) !== 'nein');

                $kw_haerte_num = is_numeric(str_replace(',', '.', $kw_haerte)) ? (float)str_replace(',', '.', $kw_haerte) : null;
                $kw_weich = ($kw_haerte_num !== null && $kw_haerte_num <= 4);
                $kw_haerte_ok = !$kw_weich;

                $kw_leitf_num = is_numeric(str_replace(',', '.', $kw_leitf)) ? (float)str_replace(',', '.', $kw_leitf) : null;
                $kw_leitf_in_range = ($kw_leitf_num !== null && $kw_leitf_num >= 80 && $kw_leitf_num <= 120);

                $ww_haerte_num = is_numeric(str_replace(',', '.', $ww_haerte)) ? (float)str_replace(',', '.', $ww_haerte) : null;
                $ww_weich = ($ww_haerte_num !== null && $ww_haerte_num <= 4);

                $kw_stadt = $hasKW && $kw_haerte_ok && !$kw_leitf_in_range;
                $r['kw_stadt_flag'] = $kw_stadt ? 1 : '';
                $r['kw_stadt_strom'] = $kw_stadt ? $kw_strom : '';

                $kw_weich_only = $hasKW && $kw_weich && !$kw_leitf_in_range;
                $r['kw_weich_flag'] = $kw_weich_only ? 1 : '';
                $r['kw_weich_strom'] = $kw_weich_only ? $kw_strom : '';

                $kw_weich_leitf = $hasKW && $kw_weich && $kw_leitf_in_range;
                $r['kw_weich_leitf_flag'] = $kw_weich_leitf ? 1 : '';
                $r['kw_weich_leitf_strom'] = $kw_weich_leitf ? $kw_strom : '';

                $r['kw_ve02_flag'] = $hasLW ? 1 : '';
                $r['kw_ve02_strom'] = $hasLW ? $lw_strom : '';
                $r['kw_ve15_flag'] = $hasVE ? 1 : '';
                $r['kw_ve15_strom'] = $hasVE ? $ve_strom : '';

                $ww_stadt = $hasWW && !$ww_weich;
                $r['ww_stadt_flag'] = $ww_stadt ? 1 : '';
                $r['ww_stadt_strom'] = $ww_stadt ? $ww_strom : '';
                $r['ww_weich_flag'] = ($hasWW && $ww_weich) ? 1 : '';
                $r['ww_weich_strom'] = ($hasWW && $ww_weich) ? $ww_strom : '';

                $r['fliessdruck'] = $kw_fliessdruck !== '' ? $kw_fliessdruck : $ww_fliessdruck;

                $isDirektWasser = in_array($kw_direkt, ['ja', '1', 'yes', 'true'])
                    || in_array($ww_direkt, ['ja', '1', 'yes', 'true']);
                $r['direkt_wasser'] = $isDirektWasser ? 'Ja' : '';

                $kw_anschluss_display = trim($params['Kaltwasser_Anschluss'] ?? '');
                $kw_anschluss_val = trim($params['Kaltwasser_Anschluss'] ?? '');
                $kw_einheit = trim(substr($kw_anschluss_display, strlen($kw_anschluss_val)));
                $r['anschluss_dimension'] = (stripos($kw_einheit, 'DN') !== false) ? $kw_anschluss_val : '';
                $r['anschluss_punkt'] = (str_contains($kw_einheit, '"')) ? $kw_anschluss_val : '';
                $r['rohrtrenner'] = $trennung;

                return $r;
            }

            function calc_sonstige_cols(array $params, array $display): array
            {
                $r = [];
                $r['raumwaerme_sensibel'] = trim($params['Abwärme'] ?? '');
                $r['kaelteleistung'] = trim($params['Kühlwasser_Abwärme'] ?? '');
                $r['kw_vorlauf_temp'] = trim($params['Kühlwasser_Temperatur'] ?? '');
                $r['druckverlust'] = trim($params['Kühlwasser_Druckverlust'] ?? '');
                $r['abwasser_temp'] = trim($params['Abflusstemperatur'] ?? '');
                $r['abluft_temp'] = trim($params['Ablufttemperatur'] ?? '');
                $r['gewicht'] = trim($params['(Eigen-)Gewicht'] ?? '');
                $r['laerm'] = trim($params['Lärm'] ?? '');

                $montage_ort = strtolower(trim($params['Montage_Ort'] ?? ''));
                $punktlast = trim($params['Punktlast'] ?? '');
                $r['punktlast_decke'] = (stripos($montage_ort, 'decke') !== false) ? $punktlast : '';
                $r['punktlast_boden'] = (stripos($montage_ort, 'boden') !== false) ? $punktlast : '';

                $abfluss_boden = strtolower(trim($params['Abfluss_Boden'] ?? ''));
                $abfluss_wand = strtolower(trim($params['Abfluss_Wand'] ?? ''));
                $hasAbwasser = in_array($abfluss_boden, ['ja', '1', 'yes', 'true'])
                    || in_array($abfluss_wand, ['ja', '1', 'yes', 'true']);
                $r['abwasser_anschl'] = $hasAbwasser ? 1 : '';

                $r['abwasser_strom'] = trim($params['Abflussstrom'] ?? '');
                $r['kw_vorlauf_anschluss'] = trim($params['Kühlwasser_Vorlauf_Anschluss'] ?? '');
                $r['kw_ruecklauf_anschluss'] = trim($params['Kühlwasser_Rücklauf_Anschluss'] ?? '');
                $r['abluft_menge'] = trim($params['Abluftstrom'] ?? '');
                $r['abluft_dimension'] = trim($params['Abluftdurchmesser'] ?? '');
                $r['abwasser_dimension'] = trim($params['Abflussdurchmesser'] ?? '');

                $kondensat = trim($params['Kondensat_Anschluss'] ?? '');
                $hasKondensat = ($kondensat !== '' && $kondensat !== '0' && strtolower($kondensat) !== 'nein');
                $r['kondensat_flag'] = $hasKondensat ? 1 : '';
                $r['kondensat_wert'] = $hasKondensat ? trim($display['Kondensat_Anschluss'] ?? '') : '';

                $siphon = strtolower(trim($params['Abfluss_Siphon_notwendig'] ?? ''));
                $r['siphon'] = in_array($siphon, ['ja', '1', 'yes', 'true']) ? 'Ja' : '';

                $direkt_abluft = strtolower(trim($params['Abluft_Direktanschluss'] ?? ''));
                $r['direkt_abluft'] = in_array($direkt_abluft, ['ja', '1', 'yes', 'true']) ? 'Ja' : '';

                $glt = strtolower(trim($params['GLT'] ?? ''));
                $r['glt_datenpunkt'] = in_array($glt, ['ja', '1', 'yes', 'true']) ? 'Ja' : '';

                $r['abwasser_fetthaltig'] = $r['direkt_abwasser'] = $r['bemerkung_abwasser'] = '';
                $r['raumwaerme_latent'] = $r['restpressung'] = $r['abluft_kondensat'] = '';
                $r['abluft_sondermaterial'] = $r['abluft_wrasen'] = $r['bemerkung_lueftung'] = '';
                $r['redundanz'] = $r['bemerkung_glt'] = $r['kw_ruecklauf'] = '';
                $r['bemerkung_kaelte'] = $r['vibration'] = $r['bemerkung_architektur'] = '';

                return $r;
            }

            $mysqli = utils_connect_sql();
            $projectID = (int)($_SESSION["projectID"] ?? 0);
            if (!$projectID) {
                die('<div class="alert alert-danger m-3">Kein Projekt ausgewählt.</div>');
            }

            // ── NEU: optionaler Raumbereich-Filter ──────────────────────────
            // Wird per GET übergeben: ?raumbereiche[]=OP&raumbereiche[]=ICU
            // Wenn leer → alle Elemente (bisheriges Verhalten)
            $filterRaumbereiche = [];
            if (!empty($_GET['raumbereiche']) && is_array($_GET['raumbereiche'])) {
                $filterRaumbereiche = array_filter(
                    $_GET['raumbereiche'],
                    fn($r) => is_string($r) && strlen($r) <= 100
                );
                $filterRaumbereiche = array_values($filterRaumbereiche);
            }
            // ────────────────────────────────────────────────────────────────

            // 1) Alle Elemente des Projekts laden – optional gefiltert nach Raumbereich
            if (empty($filterRaumbereiche)) {
                // Original-Query: alle Elemente des Projekts
                $sqlElements = "
                    SELECT
                        e.idTABELLE_Elemente,
                        e.ElementID,
                        e.Bezeichnung,
                        rhe.tabelle_Varianten_idtabelle_Varianten AS VarianteID,
                        CASE ag.Gewerke_Nr
                            WHEN '5B.03' THEN 'BEW'
                            WHEN '5B.04' THEN 'ORT'
                            WHEN '5B.05' THEN 'GRO'
                            ELSE ag.Gewerke_Nr
                        END AS GewerkBezeichnung,
                        MAX(rhe.Anzahl) AS Anzahl
                    FROM tabelle_elemente e
                    INNER JOIN tabelle_räume_has_tabelle_elemente rhe
                        ON rhe.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                    INNER JOIN tabelle_räume r
                        ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
                    LEFT JOIN tabelle_projekt_element_gewerk peg
                        ON  peg.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                        AND peg.tabelle_projekte_idTABELLE_Projekte = ?
                    LEFT JOIN tabelle_auftraggeber_gewerke ag
                        ON  ag.idTABELLE_Auftraggeber_Gewerke = peg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
                    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                    GROUP BY e.idTABELLE_Elemente, e.ElementID, e.Bezeichnung,
                             rhe.tabelle_Varianten_idtabelle_Varianten
                    HAVING MAX(rhe.Anzahl) > 0
                    ORDER BY e.ElementID, VarianteID";
                $stmtE = $mysqli->prepare($sqlElements);
                $stmtE->bind_param("ii", $projectID, $projectID);
            } else {
                // Gefilterte Query: nur Elemente, die in mind. einem der gewählten Raumbereiche vorkommen
                $placeholders = implode(',', array_fill(0, count($filterRaumbereiche), '?'));
                $sqlElements = "
                    SELECT
                        e.idTABELLE_Elemente,
                        e.ElementID,
                        e.Bezeichnung,
                        rhe.tabelle_Varianten_idtabelle_Varianten AS VarianteID,
                        CASE ag.Gewerke_Nr
                            WHEN '5B.03' THEN 'BEW'
                            WHEN '5B.04' THEN 'ORT'
                            WHEN '5B.05' THEN 'GRO'
                            ELSE ag.Gewerke_Nr
                        END AS GewerkBezeichnung,
                        MAX(rhe.Anzahl) AS Anzahl
                    FROM tabelle_elemente e
                    INNER JOIN tabelle_räume_has_tabelle_elemente rhe
                        ON rhe.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                    INNER JOIN tabelle_räume r
                        ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
                    LEFT JOIN tabelle_projekt_element_gewerk peg
                        ON  peg.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                        AND peg.tabelle_projekte_idTABELLE_Projekte = ?
                    LEFT JOIN tabelle_auftraggeber_gewerke ag
                        ON  ag.idTABELLE_Auftraggeber_Gewerke = peg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
                    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                      AND r.`Raumbereich Nutzer` IN ($placeholders)
                      AND r.Entfallen = 0
                    GROUP BY e.idTABELLE_Elemente, e.ElementID, e.Bezeichnung,
                             rhe.tabelle_Varianten_idtabelle_Varianten
                    HAVING MAX(rhe.Anzahl) > 0
                    ORDER BY e.ElementID, VarianteID";
                $stmtE = $mysqli->prepare($sqlElements);
                $types = 'ii' . str_repeat('s', count($filterRaumbereiche));
                $bindParams = array_merge([$projectID, $projectID], $filterRaumbereiche);
                $stmtE->bind_param($types, ...$bindParams);
            }

            $stmtE->execute();
            $resElements = $stmtE->get_result();
            $elements = [];
            while ($row = $resElements->fetch_assoc()) {
                $elements[] = $row;
            }

            // 2) Parameter laden
            $elementIDs = array_column($elements, 'idTABELLE_Elemente');
            $paramMap = [];
            $paramDisplay = [];
            if (!empty($elementIDs)) {
                $placeholders = implode(',', array_fill(0, count($elementIDs), '?'));
                $sqlParams = "
                    SELECT
                        ehp.TABELLE_Elemente_idTABELLE_Elemente AS elemID,
                        ehp.tabelle_Varianten_idtabelle_Varianten AS variantID,
                        p.Bezeichnung AS paramName,
                        ehp.Wert,
                        ehp.Einheit
                    FROM tabelle_projekt_elementparameter ehp
                    INNER JOIN tabelle_parameter p
                        ON p.idTABELLE_Parameter = ehp.tabelle_parameter_idTABELLE_Parameter
                    WHERE ehp.TABELLE_Elemente_idTABELLE_Elemente IN ($placeholders)
                    AND tabelle_projekte_idTABELLE_Projekte = ?";
                $params = [...$elementIDs, $projectID];
                $types = str_repeat('i', count($params));
                $stmtP = $mysqli->prepare($sqlParams);
                $stmtP->bind_param($types, ...$params);
                $stmtP->execute();
                $resParams = $stmtP->get_result();

                while ($prow = $resParams->fetch_assoc()) {
                    $key = $prow['paramName'];
                    $mapKey = $prow['elemID'] . '_' . $prow['variantID'];
                    ['wert' => $cleanWert, 'einheit' => $cleanEinheit] = normalize_to_base_unit(
                        $prow['Wert'],
                        $prow['Einheit'] ?? ''
                    );
                    $paramMap[$mapKey][$key] = $cleanWert;
                    if (in_array($key, $suppress_unit_for, true) || $cleanEinheit === '') {
                        $paramDisplay[$mapKey][$key] = $cleanWert;
                    } else {
                        $paramDisplay[$mapKey][$key] = $cleanWert . ' ' . $cleanEinheit;
                    }
                }
            }

            $mysqli->close();
            ?>

            <div style="overflow-x: auto;">
                <table class="table table-striped table-hover table-bordered table-sm compact"
                       id="tableElecParams">
                    <?php
                    echo "<thead><tr>";
                    foreach ($columnDefs as $col) {
                        //$hidden = !empty($col['hidden']) ? ' style="display:none"' : '';
                        echo "<th>" . htmlspecialchars($col['header'], ENT_QUOTES, 'UTF-8') . "</th>";
                    }
                    echo "</tr></thead>";

                    echo "<tbody>";
                    foreach ($elements as $elem) {
                        $eID = $elem['idTABELLE_Elemente'];
                        $varID = (int)($elem['VarianteID'] ?? 0);
                        $mapKey = $eID . '_' . $varID;
                        $params = $paramMap[$mapKey] ?? [];
                        $display = $paramDisplay[$mapKey] ?? [];

                        $elemCode = h($elem['ElementID']) . h(varianteLetter($varID));
                        $out_ortsv = ($elem['GewerkBezeichnung'] === 'BEW') ? 'Ja' : 'Nein';
                        $ve = isVersorgungseinheit($elem['ElementID']) ? 'Ja' : 'Nein';
                        $ac = calc_anschluss_cols($params);
                        $wc = calc_wasser_cols($params, $display);
                        $sc = calc_sonstige_cols($params, $display);

                        echo "<tr>";
                        foreach ($columnDefs as $col) {
                            $center = !empty($col['center']) ? ' class="text-center"' : '';
                            // $hidden = !empty($col['hidden']) ? ' style="display:none"' : '';
                            $val = match ($col['source']) {
                                'elem' => h($elem[$col['key']] ?? ''),
                                'static' => h($col['key']),
                                'elemCode' => $elemCode,
                                'ortsv' => $out_ortsv,
                                've' => $ve,
                                'display' => show_param($display, $col['key']),
                                'param' => show_param($params, $col['key']),
                                'calc_ac' => h($ac[$col['key']] ?? ''),
                                'calc_wc' => h($wc[$col['key']] ?? ''),
                                'calc_sc' => h($sc[$col['key']] ?? ''),
                                default => '',
                            };
                            echo "<td{$center}>{$val}</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody>";

                    $hiddenColIndexes = [];
                    foreach ($columnDefs as $i => $col) {
                        if (!empty($col['hidden'])) {
                            $hiddenColIndexes[] = $i;
                        }
                    }


                    ?>
                </table>
            </div>
        </div>
    </div>

    <?php include "modal_Kor_ausstattungkatalog_info.php" ?>

    <script src="utils/_utils.js"></script>
    <script>
        function show_modal(modal_id) {
            $('#' + modal_id).modal('show');
        }

        // ── NEU: aktive Raumbereiche aus URL lesen (für Select-Vorauswahl) ──
        const urlParams = new URLSearchParams(window.location.search);
        const activeRaumbereiche = urlParams.getAll('raumbereiche[]');

        $(document).ready(function () {

            // Select2 initialisieren
            $('#select_raumbereiche').select2({
                theme: 'bootstrap-5',
                placeholder: 'Alle Raumbereiche',
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });

            // Vorauswahl aus URL setzen
            if (activeRaumbereiche.length > 0) {
                $('#select_raumbereiche').val(activeRaumbereiche).trigger('change');
            }

            // Alle auswählen
            $('#btn_alle_auswaehlen').on('click', function () {
                const allVals = $('#select_raumbereiche option').map(function () {
                    return this.value;
                }).get();
                $('#select_raumbereiche').val(allVals).trigger('change');
            });

            // Auswahl leeren → alle Elemente anzeigen
            $('#btn_auswahl_leeren').on('click', function () {
                $('#select_raumbereiche').val(null).trigger('change');
            });

            // Bei Änderung: Seite mit neuem Filter neu laden
            $('#select_raumbereiche').on('change', function () {
                const selected = $(this).val() || [];
                const base = window.location.pathname;
                if (selected.length === 0) {
                    // Kein Filter → saubere URL ohne Parameter
                    window.location.href = base;
                } else {
                    const query = selected.map(v => 'raumbereiche[]=' + encodeURIComponent(v)).join('&');
                    window.location.href = base + '?' + query;
                }
            });

            // ── DataTable (unverändert) ──────────────────────────────────────
            new DataTable('#tableElecParams', {
                fixedHeader: true,
                order: [[1, 'asc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Alle']],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                    search: '',
                    searchPlaceholder: 'Suche...'
                },
                columnDefs: [
                    {targets: [0], visible: false, searchable: false},
                    {targets: <?= json_encode($hiddenColIndexes) ?>, visible: false}
                ],
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i>',
                        titleAttr: 'Excel Export',
                        className: 'btn btn-sm btn-outline-success bg-white',
                        exportOptions: {columns: ':visible'}
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fas fa-eye"></i>',
                        titleAttr: 'Spalten ein-/ausblenden',
                        className: 'btn btn-sm btn-outline-dark bg-white'
                    },
                    {
                        extend: 'searchBuilder',
                        text: '<i class="fas fa-filter"></i>',
                        titleAttr: 'Erweiterter Filter',
                        className: 'btn btn-sm btn-outline-dark bg-white'
                    }
                ],
                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: ['pageLength', 'info'],
                    bottomEnd: ['paging', 'search', 'buttons']
                },
                initComplete: function () {
                    $('#tableElecParams_wrapper .dt-buttons').appendTo('#dt-header-controls');
                    $('#tableElecParams_wrapper .dt-search label').remove();
                    $('#tableElecParams_wrapper .dt-search')
                        .children()
                        .removeClass('form-control form-control-sm')
                        .addClass('btn btn-sm btn-outline-dark ms-1')
                        .appendTo('#dt-header-controls');
                }
            });
        });
    </script>
</body>
</html>