<?php
/**
 * ============================================================================
 *  REPORT-SETS / VORLAGEN  –  VORDEFINIERTE PARAMETER-ZUSAMMENSTELLUNGEN
 * ============================================================================
 *
 *  Ein "Set" ist eine benannte Zusammenstellung aus:
 *    - einer Parameter-Auswahl (über ganze Gruppen und/oder einzelne Keys)
 *    - optionalen Standard-Optionen (format, mt_mode, mark_changes)
 *
 *  Damit bilden die bisher fest verdrahteten Berichte (A3quer, VE, ENT, ohne
 *  Labor, ...) jeweils EIN Set der Custom-Oberfläche ab. Der Nutzer wählt im
 *  Frontend eine Vorlage und bekommt sofort die passende Auswahl + Optionen;
 *  er kann danach einzeln nachjustieren.
 *
 *  Es gibt nur EINE, flache Liste von Sets ($REPORT_SETS) – keine Trennung
 *  mehr in allgemeine und projektspezifische Vorlagen. Projektbezogene
 *  Zusammenstellungen (z. B. 'projekt95') sind einfach ein Set wie jedes andere.
 *
 *  --------------------------------------------------------------------------
 *  AUFBAU EINES SETS (alle Felder außer 'id'/'label' optional):
 *    'id'           string  - eindeutige ID (stabil halten!)
 *    'label'        string  - Anzeigename in der Vorlagen-Auswahl
 *    'groups'       array   - Gruppen-IDs; ALLE auswählbaren Keys dieser Gruppen
 *    'params'       array   - zusätzliche einzelne Parameter-Keys
 *    'exclude'      array   - einzelne Keys wieder entfernen
 *    'options'      array   - ['format'=>'A3'|'A4','mt_mode'=>'none'|'list'|'details',
 *                             'mark_changes'=>bool]
 *    'labels'       array   - projektspez. Beschriftungen je Key: ['USV'=>'USV Batterie: ']
 *                             ersetzt das Standard-Label 1:1 (UI + PDF).
 *    'header'       array   - eigener Aufbau des Raum-Headers (Felder/Labels),
 *                             sonst $REPORT_DEFAULT_HEADER. Aufbau:
 *                             [ ['label'=>'Raum','value'=>'{Raumnr} - {Raumbezeichnung}'], ... ]
 *
 *  ----> Neues Set hinzufügen: einen Eintrag in $REPORT_SETS ergänzen.
 *  ----> Später editierbar machen: report_load_sets_from_db() füllen (s.u.),
 *        Rückgabe hat Vorrang vor den hier definierten Sets.
 * ============================================================================
 */

require_once __DIR__ . '/pdf_paramDefinitions.php';
$REPORT_DEFAULT_OPTIONS = [
    'mt_mode' => 'list',
    'mark_changes' => true,
    'format' => 'A3',
    // 'header_per_row' bewusst nicht gesetzt -> Generator wählt je Format.
];

// ----------------------------------------------------------------------------
$REPORT_DEFAULT_HEADER = [
    ['label' => 'Raum', 'value' => '{Raumnr} - {Raumbezeichnung}'],
    ['label' => 'Bereich', 'value' => '{Raumbereich Nutzer}'],
    ['label' => 'Geschoss', 'value' => '{Geschoss}'],
    ['label' => 'Bauetappe', 'value' => '{Bauetappe}'],
    ['label' => 'Bauteil', 'value' => '{Bauabschnitt}'],
];



$REPORT_SETS = [

    [
        'id' => 'projekt95',
        'label' => 'Projekt 95',
        'params' => [
            'Anmerkung FunktionBO',
            'Nutzfläche',
            'Raumhoehe',
            'Decke',
            'Allgemeine Hygieneklasse',


            'AV',
            'SV',
            // 'USV',
            'ET_Anschlussleistung_W',
            'ET_Anschlussleistung_AV_W',
            'ET_Anschlussleistung_SV_W',
             'Anmerkung Elektro',

            'EL_Not_Aus',
            'EL_Signaleinrichtung',
            'ET_PA_Stk',



            'HT_Waermeabgabe_W',
            'HT_Raumtemp Sommer °C',
            'HT_Raumtemp Winter °C',

            'HT_Luftwechsel 1/h',
            'HT_Spuele_Stk',
            'HT_Abwasser_Stk',
            'VE_Wasser',
             'HT_Kaltwasser',
            'HT_Kühlwasser',
            'HT_Notdusche',
            'Anmerkung HKLS',
            'VEXAT_Zone',

            'Anmerkung MedGas',
            'O2',
            'VA',
            'H2',
            'Ar',
            'LN',
            'N2',
            'DL',
            'Anmerkung BauStatik',
        ],
        'labels' => [
            '1 Kreis Va' => 'VA',
            '1 Kreis DL-5' => 'DL',
            'USV' => 'USV : ',
        ],
        'header' => [
            ['label' => 'Raum', 'value' => '{Raumnr} - {Raumbezeichnung}'],
            ['label' => 'Bereich', 'value' => '{Raumbereich Nutzer}'],
            ['label' => 'Geschoss', 'value' => '{Geschoss}'],
            ['label' => 'Bauteil', 'value' => '{Bauabschnitt}'],
            ['label' => 'Nutzfläche', 'value' => '{Nutzfläche}'],
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => false],
    ],

    //  [
  //      'id' => 'komplett',
  //      'label' => 'Bauangaben komplett (A3)',
  //      'groups' => ['bo', 'allgemein', 'architektur', 'elektro', 'haustechnik',
  //          'medgas', 'labor_gas', 'labor_et', 'labor_h2o', 'baustatik'],
  //      'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
  //  ],

  //  [
  //      'id' => 'ohne_labor',
  //      'label' => 'Bauangaben ohne Labor (A3)',
  //      'groups' => ['bo', 'allgemein', 'architektur', 'elektro', 'haustechnik',
  //          'medgas', 'baustatik'],
  //      'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
  //  ],

  //  [
  //      'id' => 'elektro_fokus',
  //      'label' => 'Elektro inkl. GLZ',
  //      'groups' => ['elektro'],
  //      'options' => ['format' => 'A4', 'mt_mode' => 'none', 'mark_changes' => false],
  //  ],

  //  [
  //      'id' => 'medgas_labor',
  //      'label' => 'Med.-Gas & Labor',
  //      'groups' => ['medgas', 'labor_gas', 'labor_et', 'labor_h2o'],
  //      'options' => ['format' => 'A3', 'mt_mode' => 'none', 'mark_changes' => false],
  //  ],

  //  [
  //      'id' => 'nur_mt',
  //      'label' => 'Nur Med.-tech. Elemente',
  //      'groups' => [],
  //      'options' => ['format' => 'A3', 'mt_mode' => 'details', 'mark_changes' => false],
  //  ],

    // ======================================================================
    //  AUTOMATISCH ERZEUGTE SETS AUS DEN BESTEHENDEN BERICHTEN DER REPORTS-SEITE
    //  (bilden je einen Alt-Bericht als Custom-Vorlage ab; Labels 'Bericht: ...')
    // ======================================================================

    // Bauangaben Neu  (aus Bericht: pdf_createBauangabenBericht_A3Qeer.php)
    [
        'id' => 'rep_bauangabenbericht_a3qeer',
        'label' => 'Bericht: BAU A3',
        'groups' => ['bo', 'baustatik'],
        'params' => [
            'Nutzfläche', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Anwendungsgruppe', 'AV', 'SV',
            'ZSV', 'USV', 'IT Anbindung',
            'ET_Anschlussleistung_W', 'ET_Anschlussleistung_AV_W', 'ET_Anschlussleistung_SV_W',
            'ET_Anschlussleistung_ZSV_W', 'ET_Anschlussleistung_USV_W', 'ET_RJ45-Ports',
            'EL_Laser 16A CEE Stk', 'EL_Roentgen 16A CEE Stk', 'Anmerkung Elektro',
            'H6020', 'HT_Waermeabgabe_W', 'HT_Raumtemp Sommer °C',
            'HT_Raumtemp Winter °C', 'HT_Abluft_Digestorium_Stk', 'HT_Abluft_Sicherheitsschrank_Stk',
            'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'HT_Punktabsaugung_Stk', 'VE_Wasser',
            'HT_Notdusche', 'Anmerkung HKLS', '1 Kreis O2',
            '1 Kreis Va', '1 Kreis DL-5', 'NGA',
            'N2O', 'CO2', 'DL-10',
            '2 Kreis O2', '2 Kreis Va', '2 Kreis DL-5',
            'DL-tech', 'Anmerkung MedGas',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Bauangaben Neu  (aus Bericht: pdf_createBauangabenBericht_A3Qeer_1.php)
    [
        'id' => 'rep_bauangabenbericht_a3qeer_1',
        'label' => 'Bericht: ohne Änderungsmarkierungen',
        'groups' => ['bo', 'baustatik'],
        'params' => [
            'Nutzfläche', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Anwendungsgruppe', 'AV', 'SV',
            'ZSV', 'USV', 'IT Anbindung',
            'ET_Anschlussleistung_W', 'ET_Anschlussleistung_AV_W', 'ET_Anschlussleistung_SV_W',
            'ET_Anschlussleistung_ZSV_W', 'ET_Anschlussleistung_USV_W', 'ET_RJ45-Ports',
            'EL_Laser 16A CEE Stk', 'EL_Roentgen 16A CEE Stk', 'Anmerkung Elektro',
            'H6020', 'HT_Waermeabgabe_W', 'HT_Raumtemp Sommer °C',
            'HT_Raumtemp Winter °C', 'HT_Abluft_Digestorium_Stk', 'HT_Abluft_Sicherheitsschrank_Stk',
            'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'HT_Punktabsaugung_Stk', 'VE_Wasser',
            'HT_Notdusche', 'Anmerkung HKLS', '1 Kreis O2',
            '1 Kreis Va', '1 Kreis DL-5', 'NGA',
            'N2O', 'CO2', 'DL-10',
            '2 Kreis O2', '2 Kreis Va', '2 Kreis DL-5',
            'DL-tech', 'Anmerkung MedGas',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => false],
    ],

    // Bauangaben Neu  (aus Bericht: pdf_createBauangabenBericht_A3Qeer_ohne_Lab_params.php)
    [
        'id' => 'rep_bauangabenbericht_a3qeer_ohne_lab_params',
        'label' => 'Bericht: ohne Lab',
        'groups' => ['bo', 'baustatik'],
        'params' => [
            'Nutzfläche', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Anwendungsgruppe', 'AV', 'SV',
            'ZSV', 'USV', 'IT Anbindung',
            'EL_AV Steckdosen Stk', 'EL_SV Steckdosen Stk', 'EL_ZSV Steckdosen Stk',
            'EL_USV Steckdosen Stk', 'ET_Anschlussleistung_W', 'ET_Anschlussleistung_AV_W',
            'ET_Anschlussleistung_SV_W', 'ET_Anschlussleistung_ZSV_W', 'ET_Anschlussleistung_USV_W',
            'ET_RJ45-Ports', 'EL_Laser 16A CEE Stk', 'EL_Roentgen 16A CEE Stk',
            'Anmerkung Elektro', 'H6020', 'HT_Waermeabgabe_W',
            'HT_Raumtemp Sommer °C', 'HT_Raumtemp Winter °C', 'HT_Abluft_Digestorium_Stk',
            'HT_Abluft_Sicherheitsschrank_Stk', 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'HT_Punktabsaugung_Stk',
            'VE_Wasser', 'HT_Notdusche', 'Anmerkung HKLS',
            '1 Kreis O2', '1 Kreis Va', '1 Kreis DL-5',
            'NGA', 'N2O', 'CO2',
            'DL-10', '2 Kreis O2', '2 Kreis Va',
            '2 Kreis DL-5', 'DL-tech', 'Anmerkung MedGas',
            'ET_16A_3Phasig_Einzelanschluss',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Bauangaben Neu  (aus Bericht: pdf_createBauangabenBericht_A3Qeer_VE.php)
    [
        'id' => 'rep_bauangabenbericht_a3qeer_ve',
        'label' => 'Bericht: VE',
        'groups' => ['bo', 'baustatik'],
        'params' => [
            'Nutzfläche', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Anwendungsgruppe', 'AV', 'SV',
            'ZSV', 'USV', 'IT Anbindung',
            'ET_Anschlussleistung_W', 'ET_Anschlussleistung_AV_W', 'ET_Anschlussleistung_SV_W',
            'ET_Anschlussleistung_ZSV_W', 'ET_Anschlussleistung_USV_W', 'EL_Roentgen 16A CEE Stk',
            'Anmerkung Elektro', 'H6020', 'HT_Waermeabgabe_W',
            'HT_Raumtemp Sommer °C', 'HT_Abluft_Digestorium_Stk', 'HT_Abluft_Sicherheitsschrank_Stk',
            'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'HT_Punktabsaugung_Stk', 'VE_Wasser',
            'HT_Notdusche', 'Anmerkung HKLS', '1 Kreis O2',
            '1 Kreis Va', '1 Kreis DL-5', 'NGA',
            'N2O', 'CO2', 'DL-10',
            '2 Kreis O2', '2 Kreis Va', '2 Kreis DL-5',
            'DL-tech', 'Anmerkung MedGas',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Bauangaben Neu  (aus Bericht: pdf_createBauangabenBericht_A3Qeer_CINO.php)
    [
        'id' => 'rep_bauangabenbericht_a3qeer_cino',
        'label' => 'Bericht: CINO',
        'groups' => ['bo'],
        'params' => [
            'Nutzfläche', 'AV', 'SV',
            'IT Anbindung', 'EL_AV Steckdosen Stk', 'EL_SV Steckdosen Stk',
            'ET_Anschlussleistung_W', 'ET_RJ45-Ports', 'Anmerkung Elektro',
            'H6020', 'HT_Waermeabgabe_W', 'HT_Abluft_Digestorium_Stk',
            'HT_Abluft_Sicherheitsschrank_Stk', 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'HT_Punktabsaugung_Stk',
            'VE_Wasser', 'Anmerkung HKLS', '1 Kreis O2',
            'CO2', 'DL-10', '2 Kreis O2',
            'Anmerkung MedGas', 'N2', 'HT_Abluft_Vakuumpumpe',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Bauangaben  (aus Bericht: pdf_createBauangabenPDF.php)
    [
        'id' => 'rep_bauangabenpdf',
        'label' => 'Bericht: PDF V1',
        'groups' => ['bo', 'baustatik'],
        'params' => [
            'Nutzfläche', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Anwendungsgruppe', 'AV', 'SV',
            'ZSV', 'USV', 'IT Anbindung',
            'Anmerkung Elektro', 'H6020', 'Anmerkung HKLS',
            '1 Kreis O2', '1 Kreis Va', '1 Kreis DL-5',
            'NGA', 'N2O', 'CO2',
            'DL-10', '2 Kreis O2', '2 Kreis Va',
            '2 Kreis DL-5', 'DL-tech', 'Anmerkung MedGas',
            'Anmerkung Geräte',
        ],
        'options' => ['format' => 'A4', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Bauangaben  (aus Bericht: pdf_createBauangabenV2PDF.php)
    [
        'id' => 'rep_bauangabenv2pdf',
        'label' => 'Bericht: PDF V2',
        'groups' => ['baustatik'],
        'params' => [
            'Nutzfläche', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Anwendungsgruppe', 'AV', 'SV',
            'ZSV', 'USV', 'IT Anbindung',
            'Anmerkung Elektro', 'H6020', 'Anmerkung HKLS',
            '1 Kreis O2', '1 Kreis Va', '1 Kreis DL-5',
            'NGA', 'N2O', 'CO2',
            'DL-10', '2 Kreis O2', '2 Kreis Va',
            '2 Kreis DL-5', 'DL-tech', 'Anmerkung MedGas',
            'Anmerkung Geräte',
        ],
        'options' => ['format' => 'A4', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Bauangaben  (aus Bericht: pdf_createBauangabenWithoutElementsPDF.php)
    [
        'id' => 'rep_bauangabenwithoutelementspdf',
        'label' => 'Bericht: ohne Elemente-PDF',
        'groups' => ['bo', 'baustatik'],
        'params' => [
            'Nutzfläche', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Anwendungsgruppe', 'AV', 'SV',
            'ZSV', 'USV', 'IT Anbindung',
            'Anmerkung Elektro', 'H6020', 'Anmerkung HKLS',
            '1 Kreis O2', '1 Kreis Va', '1 Kreis DL-5',
            'NGA', 'N2O', 'CO2',
            'DL-10', '2 Kreis O2', '2 Kreis Va',
            '2 Kreis DL-5', 'DL-tech', 'Anmerkung MedGas',
            'Anmerkung Geräte',
        ],
        'options' => ['format' => 'A4', 'mt_mode' => 'none', 'mark_changes' => true],
    ],

    // Bauangaben  (aus Bericht: pdf_createBauangabenLabPDF.php)
    [
        'id' => 'rep_bauangabenlabpdf',
        'label' => 'Bericht: Lab-PDF',
        'groups' => ['bo', 'baustatik'],
        'params' => [
            'Nutzfläche', 'Nutzfläche_Soll', 'Raumhoehe',
            'Raumhoehe 2', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'ISO', 'Strahlenanwendung', 'Laseranwendung',
            'Abdunkelbarkeit', 'Anwendungsgruppe', 'AV',
            'USV', 'IT Anbindung', 'ET_EMV_ja-nein',
            'Anmerkung Elektro', 'HT_Luftwechsel 1/h', 'Anmerkung HKLS',
            'CO2', 'DL-10', 'Anmerkung MedGas',
            'O2', 'VA', 'H2',
            'He', 'Ar', 'N2',
            'DL-5', 'Anmerkung Geräte', 'Gebaeude_Bestand',
            'RaumNr_Bestand', 'Raumhoehe_Soll', 'Raumtyp BH',
            'AR_APs', 'AR_Akustik', 'AR_AnwesendePersonen',
            'AR_Belichtung-nat', 'AR_Schwingungsklasse', 'EL_Leistungsbedarf_W_pro_m2',
            'ET_EMV', 'HT_Geraeteabluft m3/h', 'HT_Kühlwasserleistung_W',
            'HT_Waermeabgabe', 'He-RF',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Bauangaben  (aus Bericht: pdf_createBauangabenLabKompaktPDF.php)
    [
        'id' => 'rep_bauangabenlabkompaktpdf',
        'label' => 'Bericht: Lab-Kurz-PDF',
        'groups' => ['labor_h2o'],
        'params' => [
            'Nutzfläche', 'Nutzfläche_Soll', 'Raumhoehe',
            'Raumhoehe 2', 'Fussboden OENORM B5220', 'Allgemeine Hygieneklasse',
            'Strahlenanwendung', 'Laseranwendung', 'Laserklasse',
            'Abdunkelbarkeit', 'AV', 'SV',
            'USV', 'IT Anbindung', 'ET_EMV_ja-nein',
            'HT_Luftwechsel 1/h', 'HT_Kühlwasser', 'HT_Notdusche',
            'CO2', 'DL-tech', 'O2',
            'O2 l/min', 'CO2 l/min', 'VA',
            'VA l/min', 'H2', 'H2 l/min',
            'He', 'He l/min', 'Ar',
            'Ar l/min', 'LN', 'LN l/Tag',
            'N2', 'N2 l/min', 'DL l/min',
            'Raumhoehe_Soll', 'Raumtyp BH', 'AR_Belichtung-nat',
            'AR_Schwingungsklasse', 'EL_Leistungsbedarf_W_pro_m2', 'HT_Geraeteabluft m3/h',
            'HT_Kühlwasserleistung_W', 'HT_Waermeabgabe', 'Ar Reinheit',
            'CO2 Reinheit', 'CO2_Melder', 'DL ISO 8573',
            'H2 Reinheit', 'He-RF', 'N2 Reinheit',
            'O2 Reinheit', 'O2_Mangel',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Bauangaben  (aus Bericht: pdf_createBauangabenLabEntPDF.php)
    [
        'id' => 'rep_bauangabenlabentpdf',
        'label' => 'Bericht: Lab-ENT-PDF',
        'groups' => ['labor_h2o'],
        'params' => [
            'Nutzfläche', 'Raumhoehe', 'Fussboden OENORM B5220',
            'Strahlenanwendung', 'Laseranwendung', 'Laserklasse',
            'Abdunkelbarkeit', 'AV', 'SV',
            'USV', 'IT Anbindung', 'ET_RJ45-Ports',
            'ET_EMV_ja-nein', 'HT_Luftwechsel 1/h', 'HT_Abluft_Digestorium_Stk',
            'HT_Abluft_Sicherheitsschrank_Stk', 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'HT_Punktabsaugung_Stk',
            'HT_Spuele_Stk', 'HT_Notdusche', 'CO2',
            'DL-tech', 'O2', 'VA',
            'H2', 'He', 'Ar',
            'LN', 'LN l/Tag', 'N2',
            'ET_16A_3Phasig_Einzelanschluss', 'ET_32A_3Phasig_Einzelanschluss', 'ET_64A_3Phasig_Einzelanschluss',
            'Raumtyp BH', 'AR_Schwingungsklasse', 'EL_Leistungsbedarf_W_pro_m2',
            'ET_5x10mm2_AV_Stk', 'ET_5x10mm2_Digestorium_Stk', 'ET_5x10mm2_USV_Stk',
            'ET_Digestorium_MSR_230V_SV_Stk', 'ET_EMV', 'HT_Abluft_Esse_Stk',
            'HT_Abluft_Rauchgasabzug_Stk', 'HT_Abluft_Schweissabsaugung_Stk', 'HT_Abluft_Vakuumpumpe',
            'HT_Kühlwasserleistung_W', 'HT_Waermeabgabe', 'Ar Reinheit',
            'CO2 Reinheit', 'CO2_Melder', 'DL ISO 8573',
            'He Reinheit', 'He-RF', 'N2 Reinheit',
            'O2 Reinheit', 'O2_Mangel',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Einreichung  (aus Bericht: pdf_createBereicht_SAN_EINR.php)
    [
        'id' => 'rep_bereicht_san_einr',
        'label' => 'Bericht: KH SAN EINR BBE',
        'groups' => [],
        'params' => [
            'Nutzfläche', 'Fussboden OENORM B5220', 'Anwendungsgruppe',
            'H6020', 'HT_Spuele_Stk',
        ],
        'options' => ['format' => 'A4', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Einreichung  (aus Bericht: pdf_createBauangabenLabEinrPDF_1.php)
    [
        'id' => 'rep_bauangabenlabeinrpdf_1',
        'label' => 'Bericht: Lab-EIN-PDF',
        'groups' => [],
        'params' => [
            'Nutzfläche', 'Raumhoehe', 'Belichtungsfläche',
            'Volumen', 'Fussboden OENORM B5220', 'Strahlenanwendung',
            'Laseranwendung', 'Laserklasse', 'Abdunkelbarkeit',
            'Fussboden', 'Decke', 'Anmerkung AR',
            'AV', 'SV', 'USV',
            'ET_EMV_ja-nein', 'HT_Raumtemp Sommer °C', 'HT_Raumtemp Winter °C',
            'HT_Luftwechsel 1/h', 'HT_Abluft_Sicherheitsschrank_Stk', 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk',
            'HT_Notdusche', 'CO2', 'DL-tech',
            'VEXAT_Zone', 'O2', 'VA',
            'H2', 'He', 'Ar',
            'LN', 'N2', 'ET_16A_3Phasig_Einzelanschluss',
            'ET_32A_3Phasig_Einzelanschluss', 'ET_64A_3Phasig_Einzelanschluss', 'EL_Not_Aus',
            'EL_Signaleinrichtung', 'AP_Gefaehrdung', 'AP_Geistige',
            'Gaswarneinrichtung-Art', 'Spezialgase', 'Taetigkeiten',
            'AR_APs', 'AR_Schwingungsklasse', 'PHY_Akustik_Schallgrad',
            'PHY_Akustik_T500', 'EL_Beleuchtungsstaerke', 'EL_Not_Aus_Funktion',
            'ET_EMV', 'HT_Belueftung', 'HT_Entlueftung',
            'HT_Heizung', 'HT_Kaelteabgabe_Typ', 'HT_Kuehlung',
            'HT_Luftmenge m3/h', 'HT_Waermeabgabe_Typ', 'CO2_Melder',
            'He-RF', 'LHe', 'O2_Mangel',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Historische Berichte  (aus Bericht: pdf_createVBM_Bericht.php)
    [
        'id' => 'rep_vbm_bericht',
        'label' => 'Bericht: Bauang. Großgeräte',
        'groups' => ['baustatik'],
        'params' => [
            'Fussboden OENORM B5220', 'Strahlenanwendung', 'Laseranwendung',
            'AR_Statik_relevant', 'AR_Empf_Breite_cm', 'AR_Empf_Tiefe_cm',
            'AR_Empf_Hoehe_cm', 'AR_Flaechenlast_kgcm2', 'Anwendungsgruppe',
            'AV', 'SV', 'ZSV',
            'USV', 'IT Anbindung', 'ET_Anschlussleistung_W',
            'Anmerkung Elektro', 'H6020', 'HT_Waermeabgabe_W',
            'HT_Raumtemp Sommer °C', 'HT_Tempgradient_Ch', 'HT_Kühlwasser',
            'Anmerkung HKLS',
        ],
        'options' => ['format' => 'A4', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Historische Berichte  (aus Bericht: pdf_createBOPDF.php)
    [
        'id' => 'rep_bopdf',
        'label' => 'Bericht: BO-PDF',
        'groups' => ['bo'],
        'params' => [
            'Nutzfläche', 'Strahlenanwendung', 'Laseranwendung',
            'Abdunkelbarkeit', 'Anwendungsgruppe', 'H6020',
            '1 Kreis O2', '1 Kreis Va', '1 Kreis DL-5',
            'DL-10', '2 Kreis O2', '2 Kreis Va',
            '2 Kreis DL-5', 'DL-tech',
        ],
        'options' => ['format' => 'A4', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Historische Berichte  (aus Bericht: pdf_createBericht_VE_PDF.php)
    [
        'id' => 'rep_bericht_ve_pdf',
        'label' => 'Bericht: VE-Gesamt-PDF',
        'groups' => ['bo', 'baustatik'],
        'params' => [
            'Nutzfläche', 'Raumhoehe', 'Raumhoehe 2',
            'Belichtungsfläche', 'Umfang', 'Fussboden OENORM B5220',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Fussboden', 'Anwendungsgruppe', 'AV',
            'SV', 'ZSV', 'USV',
            'IT Anbindung', 'Anmerkung Elektro', 'H6020',
            'Anmerkung HKLS', '1 Kreis O2', '1 Kreis Va',
            '1 Kreis DL-5', 'NGA', 'N2O',
            'CO2', 'DL-10', '2 Kreis O2',
            '2 Kreis Va', '2 Kreis DL-5', 'DL-tech',
            'Anmerkung MedGas',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Historische Berichte  (aus Bericht: pdf_createBericht_ENT_PDF.php)
    [
        'id' => 'rep_bericht_ent_pdf',
        'label' => 'Bericht: ENT-Gesamt-PDF',
        'groups' => ['bo'],
        'params' => [
            'Nutzfläche', 'Raumhoehe', 'Raumhoehe 2',
            'Belichtungsfläche', 'Umfang', 'Volumen',
            'Strahlenanwendung', 'Laseranwendung', 'Abdunkelbarkeit',
            'Fussboden', 'Anwendungsgruppe', 'AV',
            'SV', 'ZSV', 'USV',
            'IT Anbindung', 'EL_AV Steckdosen Stk', 'EL_SV Steckdosen Stk',
            'EL_USV Steckdosen Stk', 'H6020', 'HT_Raumtemp Sommer °C',
            'HT_Raumtemp Winter °C', 'Anmerkung HKLS', '1 Kreis O2',
            '1 Kreis Va', '1 Kreis DL-5', 'NGA',
            'CO2', 'DL-10', '2 Kreis O2',
            '2 Kreis Va', '2 Kreis DL-5', 'DL-tech',
            'Anmerkung MedGas', 'Aufenthaltsraum', 'AR_Ausstattung',
            'EL_Beleuchtung 1 Stk', 'EL_Beleuchtung 1 Typ', 'EL_Beleuchtung 2 Stk',
            'EL_Beleuchtung 2 Typ', 'EL_Beleuchtung 3 Stk', 'EL_Beleuchtung 3 Typ',
            'EL_Beleuchtung 4 Stk', 'EL_Beleuchtung 4 Typ', 'EL_Beleuchtung 5 Stk',
            'EL_Beleuchtung 5 Typ', 'EL_Beleuchtung dimmbar JA/NEIN', 'EL_Bodendose Stk',
            'EL_Brandmelder Decke JA/NEIN', 'EL_Brandmelder ZwDecke JA/NEIN', 'EL_Doppeldatendose Stk',
            'EL_Einzel-Datendose Stk', 'EL_Jalousie JA/NEIN', 'EL_Kamera Stk',
            'EL_Lautsprecher Stk', 'EL_Lichtfarbe K', 'EL_Lichtruf - Steckmodul Stk',
            'EL_Lichtruf - Terminal Stk', 'EL_Lichtschaltung BWM JA/NEIN', 'EL_Notlicht RZL Stk',
            'EL_Notlicht SL Stk', 'EL_Uhr - Decke Stk', 'EL_Uhr - Wand Stk',
            'HT_Fancoil W', 'HT_Fussbodenkühlung W', 'HT_Heizlast W',
            'HT_Kühldecke W', 'HT_Kühllast W', 'HT_Kühlung Lueftung W',
            'HT_Luftmenge m3/h', 'HT_Summe Kühlung W',
        ],
        'options' => ['format' => 'A3', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Historische Berichte  (aus Bericht: pdf_createUserFormPDF.php)
    [
        'id' => 'rep_userformpdf',
        'label' => 'Bericht: Nutzer Formular',
        'groups' => ['bo'],
        'params' => [
            'Nutzfläche', 'Nutzfläche_Soll', 'Strahlenanwendung',
            'Laseranwendung', 'Abdunkelbarkeit',
        ],
        'options' => ['format' => 'A4', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

    // Historische Berichte  (aus Bericht: pdf_createBerichtKHIMA40.php)
    [
        'id' => 'rep_berichtkhima40',
        'label' => 'Bericht: KHI BT0',
        'groups' => ['bo'],
        'params' => [
            'Nutzfläche', 'Raumhoehe', 'Raumhoehe 2',
            'Umfang', 'Fussboden OENORM B5220', 'GMP',
            'Anmerkung allgemein', 'AR_AP_permanent', 'Fussboden',
            'Decke', 'Anmerkung AR', 'H6020',
            'HT_Raumtemp Sommer °C', 'HT_Raumtemp Winter °C', 'HT_Luftwechsel 1/h',
            'Anmerkung Kuechentechnik', 'Anmerkung Rohrpost', 'AR_Boden_Rutschfestigkeit',
            'AR_Bodenaufbau', 'AR_Nutzung_ON1800', 'EL_Beleuchtungsstaerke',
            'HT_Luftmenge Abluft m3/h', 'HT_Luftmenge m3/h',
        ],
        'options' => ['format' => 'A4', 'mt_mode' => 'list', 'mark_changes' => true],
    ],

];


// ============================================================================
//  FUNKTIONEN
// ============================================================================

/**
 * Auswählbare Parameter-Keys einer Gruppe (ohne Sondergruppen).
 */
function report_set_group_keys(string $groupId): array
{
    global $REPORT_PARAM_GROUPS;
    if (empty($REPORT_PARAM_GROUPS[$groupId]) || !empty($REPORT_PARAM_GROUPS[$groupId]['special'])) {
        return [];
    }
    $keys = [];
    foreach ($REPORT_PARAM_GROUPS[$groupId]['params'] as $p) {
        $keys[] = $p['key'];
    }
    return $keys;
}

/**
 * Löst ein Set in seine konkrete, deduplizierte Parameter-Key-Liste auf.
 * Reihenfolge entspricht der Definition (Gruppen-Reihenfolge, dann Einzel-Keys);
 * gegen die Whitelist auswählbarer Keys geschnitten.
 */
function report_resolve_set_params(array $set): array
{
    $keys = [];
    foreach (($set['groups'] ?? []) as $g) {
        foreach (report_set_group_keys($g) as $k) {
            $keys[] = $k;
        }
    }
    foreach (($set['params'] ?? []) as $k) {
        $keys[] = $k;
    }
    if (!empty($set['exclude'])) {
        $keys = array_diff($keys, $set['exclude']);
    }
    // Nur tatsächlich auswählbare Keys, Duplikate raus, Reihenfolge erhalten
    $selectable = report_selectable_param_keys();
    $keys = array_values(array_unique(array_filter(
        $keys,
        fn($k) => in_array($k, $selectable, true)
    )));
    return $keys;
}

/**
 * Normalisiert die Optionen eines Sets (gültige Werte erzwingen).
 */
function report_set_options(array $set): array
{
    $opts = $set['options'] ?? [];
    $out = [];
    if (isset($opts['format']) && in_array($opts['format'], ['A3', 'A4'], true)) {
        $out['format'] = $opts['format'];
    }
    if (isset($opts['mt_mode']) && in_array($opts['mt_mode'], ['none', 'list', 'details'], true)) {
        $out['mt_mode'] = $opts['mt_mode'];
    }
    if (isset($opts['mark_changes'])) {
        $out['mark_changes'] = (bool)$opts['mark_changes'];
    }
    return $out;
}

/**
 * Optionale Label-Overrides eines Sets (key => label), gegen Array abgesichert.
 */
function report_set_labels(array $set): array
{
    $labels = $set['labels'] ?? [];
    return is_array($labels) ? $labels : [];
}

/**
 * Optionaler Raum-Header eines Sets, gegen Array abgesichert.
 * Leeres Array bedeutet: Standard-Header ($REPORT_DEFAULT_HEADER) verwenden.
 */
function report_set_header(array $set): array
{
    $header = $set['header'] ?? [];
    return is_array($header) ? $header : [];
}

/**
 * Liefert alle verfügbaren Sets, jeweils bereits aufgelöst:
 *   [ ['id','label','params'=>[...keys],'options'=>[...],
 *      'labels'=>[key=>label,...],'header'=>[...]], ... ]
 *
 * Keine Projekt-Trennung, kein 'scope' mehr – eine flache Liste.
 */
function report_get_sets(): array
{
    global $REPORT_SETS;

    // DB-Hook hat Vorrang, falls befüllt (siehe report_load_sets_from_db)
    $fromDb = report_load_sets_from_db();  // DB ist immer leer, gibt noch keine Tabelle. 
    $raw = !empty($fromDb) ? $fromDb : $REPORT_SETS;

    $out = [];
    foreach ($raw as $set) {
        $out[] = [
            'id' => $set['id'],
            'label' => $set['label'],
            'params' => report_resolve_set_params($set),
            'options' => report_set_options($set),
            'labels' => report_set_labels($set),
            'header' => report_set_header($set),
        ];
    }
    return $out;
}

/**
 * Einzelnes Set (aufgelöst) anhand seiner ID holen, oder null.
 */
function report_get_set(string $setId): ?array
{
    foreach (report_get_sets() as $set) {
        if ($set['id'] === $setId) {
            return $set;
        }
    }
    return null;
}

/**
 * Hook zum späteren Laden/Speichern von Sets aus der Datenbank (Editor-Seite).
 * Erwartet – wenn befüllt – dieselbe Roh-Struktur wie $REPORT_SETS.
 * Gibt die DB-Rückgabe Vorrang vor den hier definierten Sets.
 */
function report_load_sets_from_db(): array
{
    return [];
}



function report_get_groups(array $labelOverrides = []): array
{
    global $REPORT_PARAM_GROUPS;

    $groups = $REPORT_PARAM_GROUPS;   // value copy

    if (!empty($labelOverrides)) {
        foreach ($groups as &$g) {
            foreach ($g['params'] as &$p) {
                if (isset($labelOverrides[$p['key']])) {
                    $p['label'] = $labelOverrides[$p['key']];
                }
            }
            unset($p);
        }
        unset($g);
    }
    return $groups;
}

/**
 * Standard-Raum-Header (gilt, wenn ein Set keinen eigenen 'header' mitbringt).
 */
function report_get_default_header(): array
{
    global $REPORT_DEFAULT_HEADER;
    return $REPORT_DEFAULT_HEADER;
}

/**
 * Normalisierte Standard-Optionen (mt_mode, mark_changes, format).
 */
function report_get_default_options(): array
{
    global $REPORT_DEFAULT_OPTIONS;

    $opts = $REPORT_DEFAULT_OPTIONS;

    if (!in_array($opts['mt_mode'], ['none', 'list', 'details'], true)) {
        $opts['mt_mode'] = 'list';
    }
    if (!in_array($opts['format'], ['A3', 'A4'], true)) {
        $opts['format'] = 'A3';
    }
    $opts['mark_changes'] = (bool)$opts['mark_changes'];

    return $opts;
}

function report_selectable_param_keys(): array
{
    global $REPORT_PARAM_GROUPS;
    $keys = [];
    foreach ($REPORT_PARAM_GROUPS as $group) {
        if (!empty($group['special'])) {
            continue;
        }
        foreach ($group['params'] as $p) {
            $keys[] = $p['key'];
        }
    }
    return $keys;
}