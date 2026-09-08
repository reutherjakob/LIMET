<?php
/**
 * ============================================================================
 *  ZENTRALE PARAMETER-DEFINITION FÜR CUSTOM-BERICHTE  (single source of truth)
 * ============================================================================
 *
 *  Diese Datei definiert ALLE für Custom-Berichte verfügbaren Parameter.
 *  Sie wird sowohl vom UI (über getReportParamDefinitions.php) als auch vom
 *  PDF-Generator verwendet. Das UI rendert die Gruppen DYNAMISCH – ein neuer
 *  Parameter hier erscheint also automatisch im Auswahl-Frontend UND im PDF.
 *
 *  ----> Neuen Parameter hinzufügen:
 *        Im passenden Gruppen-Block ein weiteres Array ergänzen. Fertig.
 *
 *  ----> Neue Gruppe hinzufügen:
 *        Einen weiteren Eintrag in $REPORT_PARAM_GROUPS ergänzen
 *        (label, perRow, params). Bei Bedarf 'special' => true für
 *        Sondergruppen, die nicht als Checkbox-Block erscheinen.
 *
 *  Struktur eines Parameters:
 *    'key'      string  - SQL-Spaltenname in tabelle_räume (bzw. virtueller Key)
 *    'label'    string  - Beschriftung im PDF (links vor dem Wert)
 *    'ui_label' string  - (optional) Beschriftung im Auswahl-Frontend, falls sie
 *                         vom PDF-'label' abweichen soll. Nötig, wenn 'label'
 *                         leer ist (sonst bliebe das Auswahlfeld ohne Text).
 *                         Fallback im UI: ui_label > label > key.
 *    'render'   string  - Render-Typ (siehe unten)
 *    'unit'     string  - (optional) Einheit hinter dem Wert
 *    'true_val' mixed   - (optional) Vergleichswert für 'hackerl' (Default "JA")
 *    'col'      bool    - (optional, Default true) echte SQL-Spalte?
 *    'span'     int     - (optional, Default 1) Breite in Grundspalten.
 *                         Größere Werte geben der WERT-Zelle mehr Platz und
 *                         brechen das Raster passend um. Beispiel: ein langer
 *                         Wert wie "Hygieneklasse: Richtlinie MA LL 14" mit
 *                         'span' => 2 bleibt einzeilig statt umzubrechen, kurze
 *                         Parameter ("AV: x") bleiben mit span=1 kompakt.
 *    'ln_after' bool    - (optional) erzwingt einen Zeilenumbruch NACH diesem
 *                         Parameter (manuelle Rastersteuerung wie im A3queer-
 *                         Bericht), unabhängig von span/perRow.
 *    'cell'     float   - (optional) ABSOLUTE Breite Label-Zelle (Vielfaches e_C);
 *                         überschreibt die span-/Ratio-Berechnung.
 *    'str_cell' float   - (optional) ABSOLUTE Breite Wert-Zelle (Vielfaches e_C);
 *                         überschreibt die span-/Ratio-Berechnung.
 *
 *  Render-Typen (Darstellung -> pdf_report_render.php::cb_render_value):
 *    'str'       - Text 1:1
 *    'nr'        - Zahl + Einheit ("-" wenn 0)
 *    'kify'      - Zahl k-formatiert + Einheit ("-" wenn "0")
 *    'kify_ka'   - wie kify, aber "keine Angabe" wenn 0
 *    'hackerl'   - Häkchen/Kreuz (true_val/JA/1)
 *    'strahlen'  - Spezialdarstellung Strahlenanwendung
 *    'janein'    - 1 -> "Ja", sonst "Nein"
 *    'anm'       - Anmerkungsblock über volle Breite
 *    'power_glz' - virtuell: Raumleistung der Elemente inkl. Gleichzeitigkeit
 *                  (AV/SV/ZSV/USV/ohne Zuordnung). Werte werden NICHT als
 *                  SQL-Spalte geladen, sondern vom Hauptskript über
 *                  report_fetch_room_power_glz() ermittelt und in $row injiziert.
 *                  -> immer 'col' => false setzen.
 *    'mt_table'  - virtuell: Med.-tech.-Tabelle (Sondergruppe)
 *
 *  perRow: wie viele Grundspalten (span=1) pro Rasterzeile nebeneinander stehen.
 * ============================================================================
 */

$REPORT_PARAM_GROUPS = [

    // ---------------------------------------------------------------- BO-Beschr.
    'bo' => [
        'label' => 'BO-Beschr.',
        'perRow' => 1,
        'params' => [
            ['key' => 'Anmerkung FunktionBO', 'label' => '', 'ui_label' => 'BO-Beschreibung', 'render' => 'anm'],
        ],
    ],

    // ---------------------------------------------------------------- Raum / Allgemein
    'allgemein' => [
        'label' => 'Allgemein',
        'perRow' => 4,
        'params' => [
            ['key' => 'Nutzfläche', 'label' => 'Fläche: ', 'render' => 'nr', 'unit' => 'm2'],
            ['key' => 'Nutzfläche_Soll', 'label' => 'Fläche Soll: ', 'render' => 'nr', 'unit' => 'm2'],
            ['key' => 'Raumhoehe', 'label' => 'Raumhöhe: ', 'render' => 'str', 'unit' => 'm'],
            ['key' => 'Raumhoehe 2', 'label' => 'Raumhöhe 2: ', 'render' => 'str', 'unit' => 'm'],
            ['key' => 'Belichtungsfläche', 'label' => 'Belichtungsfl.: ', 'render' => 'nr', 'unit' => 'm2'],
            ['key' => 'Umfang', 'label' => 'Umfang: ', 'render' => 'nr', 'unit' => 'm'],
            ['key' => 'Volumen', 'label' => 'Volumen: ', 'render' => 'nr', 'unit' => 'm3'],
            ['key' => 'Fussboden OENORM B5220', 'label' => 'Ö NORM B5220: ', 'render' => 'str'],
            // Beispiel für 'span': lange Antworten bekommen mehr Platz (sonst Umbruch)
            ['key' => 'Allgemeine Hygieneklasse', 'label' => 'Hygieneklasse: ', 'render' => 'str', 'span' => 2],
            ['key' => 'GMP', 'label' => 'GMP: ', 'render' => 'str'],
            ['key' => 'ISO', 'label' => 'ISO: ', 'render' => 'str'],
            ['key' => 'Strahlenanwendung', 'label' => 'Strahlenanw.: ', 'render' => 'strahlen'],
            ['key' => 'Laseranwendung', 'label' => 'Laseranw.: ', 'render' => 'hackerl', 'true_val' => 'JA'],
            ['key' => 'Laserklasse', 'label' => 'Laserklasse: ', 'render' => 'str'],
            ['key' => 'Abdunkelbarkeit', 'label' => 'Abdunkelbarkeit: ', 'render' => 'hackerl', 'true_val' => 'JA'],

            ['key' => 'Anmerkung allgemein', 'label' => 'Anmerkung: ', 'render' => 'anm'],



            ['key' => 'AR_Statik_relevant', 'label' => 'Statik relevant: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'AR_AP_permanent', 'label' => 'Perm. Arbeitsplatz: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'AR_Empf_Breite_cm', 'label' => 'Empf. Breite: ', 'render' => 'nr', 'unit' => 'cm'],
            ['key' => 'AR_Empf_Tiefe_cm', 'label' => 'Empf. Tiefe: ', 'render' => 'nr', 'unit' => 'cm'],
            ['key' => 'AR_Empf_Hoehe_cm', 'label' => 'Empf. Höhe: ', 'render' => 'nr', 'unit' => 'cm'],
            ['key' => 'AR_Flaechenlast_kgcm2', 'label' => 'Flächenlast: ', 'render' => 'nr', 'unit' => 'kg/cm2'],
            ['key' => 'Fussboden', 'label' => 'Fußboden: ', 'render' => 'str'],
            ['key' => 'Decke', 'label' => 'Decke: ', 'render' => 'str'],

            ['key' => 'Anmerkung AR', 'label' => 'Anmerkung:', 'render' => 'anm'],
        ],
    ],

    // ---------------------------------------------------------------- Architektur / Statik
    // 'architektur' => [
    //     'label' => 'Architektur',
    //     'perRow' => 4,
    //     'params' => [
//
    //     ],
    // ],

    // ---------------------------------------------------------------- Elektro
    'elektro' => [
        'label' => 'Elektro',
        'perRow' => 4,
        'params' => [
            ['key' => 'Anwendungsgruppe', 'label' => 'ÖVE E8101: ', 'render' => 'str'],
            ['key' => 'AV', 'label' => 'AV: ', 'render' => 'hackerl'],
            ['key' => 'SV', 'label' => 'SV: ', 'render' => 'hackerl'],
            ['key' => 'ZSV', 'label' => 'ZSV: ', 'render' => 'hackerl'],
            ['key' => 'USV', 'label' => 'USV: ', 'render' => 'hackerl'],
            ['key' => 'IT Anbindung', 'label' => 'IT Anschl.: ', 'render' => 'hackerl'],

            ['key' => 'EL_AV Steckdosen Stk', 'label' => 'AV Steckdosen: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'EL_SV Steckdosen Stk', 'label' => 'SV Steckdosen: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'EL_ZSV Steckdosen Stk', 'label' => 'ZSV Steckdosen: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'EL_USV Steckdosen Stk', 'label' => 'USV Steckdosen: ', 'render' => 'nr', 'unit' => 'Stk'],

            ['key' => 'ET_Anschlussleistung_W', 'label' => 'Raum Anschlussl. ohne Glz: ', 'render' => 'kify', 'unit' => 'W'],
            ['key' => 'ET_Anschlussleistung_AV_W', 'label' => 'AV (Rauml.): ', 'render' => 'kify', 'unit' => 'W'],
            ['key' => 'ET_Anschlussleistung_SV_W', 'label' => 'SV (Rauml.): ', 'render' => 'kify', 'unit' => 'W'],
            ['key' => 'ET_Anschlussleistung_ZSV_W', 'label' => 'ZSV (Rauml.): ', 'render' => 'kify', 'unit' => 'W'],
            ['key' => 'ET_Anschlussleistung_USV_W', 'label' => 'USV (Rauml.): ', 'render' => 'kify', 'unit' => 'W'],
            ['key' => 'ET_RJ45-Ports', 'label' => 'RJ45-Ports: ', 'render' => 'nr', 'unit' => 'Stk'],

            ['key' => 'EL_Laser 16A CEE Stk', 'label' => 'CEE16A Laser: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Roentgen 16A CEE Stk', 'label' => 'CEE16A Röntgen: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Laser 32A Stk', 'label' => '32A Laser: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'ET_EMV_ja-nein', 'label' => 'EMV: ', 'render' => 'hackerl', 'true_val' => 1],

            // Virtuell: Raumleistung der Elemente inkl. Gleichzeitigkeit (GLZ).
            // col => false  -> keine SQL-Spalte; die Werte werden vom Hauptskript
            // über report_fetch_room_power_glz() geladen und in $row injiziert.
            ['key' => '__RAUMLEISTUNG_GLZ__', 'label' => 'Leistungen inkl. GLZ', 'render' => 'power_glz', 'col' => false],

            ['key' => 'Anmerkung Elektro', 'label' => 'Anmerkung:', 'render' => 'anm'],
        ],
    ],

    // ---------------------------------------------------------------- Haustechnik
    'haustechnik' => [
        'label' => 'Haustechnik',
        'perRow' => 4,
        'params' => [
            ['key' => 'H6020', 'label' => 'H6020: ', 'render' => 'str'],
            ['key' => 'HT_Waermeabgabe_W', 'label' => 'Abwärme MT: ', 'render' => 'kify_ka', 'unit' => 'W'],
            ['key' => 'HT_Raumtemp Sommer °C', 'label' => 'Max. Raumtemp.: ', 'render' => 'str', 'unit' => '°C'],
            ['key' => 'HT_Raumtemp Winter °C', 'label' => 'Min. Raumtemp.: ', 'render' => 'str', 'unit' => '°C'],
            ['key' => 'HT_Tempgradient_Ch', 'label' => 'Tempgradient: ', 'render' => 'nr', 'unit' => '°C/h'],
            ['key' => 'HT_Luftwechsel 1/h', 'label' => 'Luftwechsel: ', 'render' => 'nr', 'unit' => '1/h'],

            ['key' => 'HT_Abluft_Digestorium_Stk', 'label' => 'Abluft Digestor/Werkbank: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'HT_Abluft_Sicherheitsschrank_Stk', 'label' => 'Abluft Sicherheitsschrank: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'label' => 'Abluft Sich.schrank Unterbau: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'HT_Punktabsaugung_Stk', 'label' => 'Punktabsaugung: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'HT_Spuele_Stk', 'label' => 'Spüle: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'HT_Abwasser_Stk', 'label' => 'Abwasser: ', 'render' => 'nr', 'unit' => 'Stk'],

            ['key' => 'VE_Wasser', 'label' => 'VE Wasser: ', 'render' => 'janein'],
            ['key' => 'HT_Warmwasser', 'label' => 'Warmwasser: ', 'render' => 'str'],
            ['key' => 'HT_Kaltwasser', 'label' => 'Kaltwasser: ', 'render' => 'str'],
            ['key' => 'HT_Kühlwasser', 'label' => 'Kühlwasser: ', 'render' => 'str'],
            ['key' => 'HT_Notdusche', 'label' => 'Notdusche: ', 'render' => 'str'],

            ['key' => 'Anmerkung HKLS', 'label' => 'Anmerkung:', 'render' => 'anm'],
        ],
    ],

    // ---------------------------------------------------------------- Med.-Gas
    'medgas' => [
        'label' => 'Med.-Gas',
        'perRow' => 6,
        'params' => [
            ['key' => '1 Kreis O2', 'label' => '1 Kreis   O2: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => '1 Kreis Va', 'label' => '1 Kreis   Va: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => '1 Kreis DL-5', 'label' => '1 Kreis   DL5: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'NGA', 'label' => 'NGA: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'N2O', 'label' => 'N2O: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'CO2', 'label' => 'CO2: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'DL-10', 'label' => 'DL10: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => '2 Kreis O2', 'label' => '2 Kreise O2: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => '2 Kreis Va', 'label' => '2 Kreise Va: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => '2 Kreis DL-5', 'label' => '2 Kreise DL5: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'DL-tech', 'label' => 'DLtech: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'VEXAT_Zone', 'label' => 'VEXAT Zone: ', 'render' => 'str'],

            ['key' => 'Anmerkung MedGas', 'label' => 'Anmerkung:', 'render' => 'anm'],
        ],
    ],

    // ---------------------------------------------------------------- Labor-Gas
    'labor_gas' => [
        'label' => 'Labor-Gas',
        'perRow' => 8,
        'params' => [
            ['key' => 'O2', 'label' => 'O2: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'O2 l/min', 'label' => 'O2: ', 'render' => 'nr', 'unit' => 'l/min'],
            ['key' => 'CO2 l/min', 'label' => 'CO2: ', 'render' => 'nr', 'unit' => 'l/min'],
            ['key' => 'VA', 'label' => 'VA: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'VA l/min', 'label' => 'VA: ', 'render' => 'nr', 'unit' => 'l/min'],
            ['key' => 'H2', 'label' => 'H2: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'H2 l/min', 'label' => 'H2: ', 'render' => 'nr', 'unit' => 'l/min'],
            ['key' => 'He', 'label' => 'He: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'He l/min', 'label' => 'He: ', 'render' => 'nr', 'unit' => 'l/min'],
            ['key' => 'Ar', 'label' => 'Ar: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'Ar l/min', 'label' => 'Ar: ', 'render' => 'nr', 'unit' => 'l/min'],
            ['key' => 'LN', 'label' => 'LN: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'LN l/Tag', 'label' => 'LN: ', 'render' => 'nr', 'unit' => 'l/Tag'],
            ['key' => 'N2', 'label' => 'N2: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'N2 l/min', 'label' => 'N2: ', 'render' => 'nr', 'unit' => 'l/min'],
            ['key' => 'DL-5', 'label' => 'DL-5: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'DL l/min', 'label' => 'DL: ', 'render' => 'nr', 'unit' => 'l/min'],
        ],
    ],

    // ---------------------------------------------------------------- Labor-Elektro
    'labor_et' => [
        'label' => 'Labor-Elektro',
        'perRow' => 4,
        'params' => [
            ['key' => 'ET_16A_3Phasig_Einzelanschluss', 'label' => '16A 3Ph Einzelanschl.: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'ET_32A_3Phasig_Einzelanschluss', 'label' => '32A 3Ph Einzelanschl.: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'ET_64A_3Phasig_Einzelanschluss', 'label' => '64A 3Ph Einzelanschl.: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'EL_Not_Aus', 'label' => 'Not-Aus: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'EL_Signaleinrichtung', 'label' => 'Signaleinrichtung: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'ET_PA_Stk', 'label' => 'PA: ', 'render' => 'nr', 'unit' => 'Stk'],
        ],
    ],

    // ---------------------------------------------------------------- Labor-Wasser
    'labor_h2o' => [
        'label' => 'Labor-Wasser',
        'perRow' => 4,
        'params' => [
            ['key' => 'Wasser Qual 1', 'label' => 'Wasser Q1: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'Wasser Qual 1 l/Tag', 'label' => 'Q1: ', 'render' => 'nr', 'unit' => 'l/Tag'],
            ['key' => 'Wasser Qual 2', 'label' => 'Wasser Q2: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'Wasser Qual 2 l/Tag', 'label' => 'Q2: ', 'render' => 'nr', 'unit' => 'l/Tag'],
            ['key' => 'Wasser Qual 3', 'label' => 'Wasser Q3: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'Wasser Qual 3 l/min', 'label' => 'Q3: ', 'render' => 'nr', 'unit' => 'l/min'],
        ],
    ],

    // ---------------------------------------------------------------- BauStatik
    'baustatik' => [
        'label' => 'Baustatik',
        'perRow' => 1,
        'params' => [
            ['key' => 'Anmerkung BauStatik', 'label' => 'Anmerkung:', 'render' => 'anm'],
        ],
    ],

    // ========================================================================
    //  ADDITIVE PARAMETER-GRUPPEN (aktiviert) — bilden weitere räume-Spalten
    //  ab, damit die Vorlagen/Sets die alten Berichte vollständig reproduzieren.
    // ========================================================================

    'allgemein_extra' => [
        'label' => 'Allgemein (Detail)',
        'perRow' => 4,
        'params' => [
            ['key' => 'AP_Gefaehrdung', 'label' => 'Gefaehrdung: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'AP_Geistige', 'label' => 'Geistige: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'Anmerkung Geräte', 'label' => '', 'ui_label' => 'Anmerkung Geräte', 'render' => 'anm'],
            ['key' => 'Anmerkung Kuechentechnik', 'label' => '', 'ui_label' => 'Anmerkung Kuechentechnik', 'render' => 'anm'],
            ['key' => 'Anmerkung Rohrpost', 'label' => '', 'ui_label' => 'Anmerkung Rohrpost', 'render' => 'anm'],
            ['key' => 'Aufenthaltsraum', 'label' => 'Aufenthaltsraum: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'Gaswarneinrichtung-Art', 'label' => 'Gaswarneinrichtung-Art: ', 'render' => 'str'],
            ['key' => 'Gebaeude_Bestand', 'label' => 'Gebaeude Bestand: ', 'render' => 'str'],
            ['key' => 'RaumNr_Bestand', 'label' => 'RaumNr Bestand: ', 'render' => 'str'],
            ['key' => 'Raumhoehe_Soll', 'label' => 'Raumhoehe Soll: ', 'render' => 'str'],
            ['key' => 'Raumtyp BH', 'label' => 'Raumtyp BH: ', 'render' => 'str'],
            ['key' => 'Spezialgase', 'label' => 'Spezialgase: ', 'render' => 'str'],
            ['key' => 'Taetigkeiten', 'label' => 'Taetigkeiten: ', 'render' => 'str'],
        ],
    ],

    // ---------------------------------------------------------------- Architektur (Detail)
    'architektur_detail' => [
        'label' => 'Architektur (Detail)',
        'perRow' => 4,
        'params' => [
            ['key' => 'AR_APs', 'label' => 'APs: ', 'render' => 'nr'],
            ['key' => 'AR_Akustik', 'label' => 'Akustik: ', 'render' => 'str'],
            ['key' => 'AR_AnwesendePersonen', 'label' => 'AnwesendePersonen: ', 'render' => 'nr'],
            ['key' => 'AR_Ausstattung', 'label' => 'Ausstattung: ', 'render' => 'str'],
            ['key' => 'AR_Belichtung-nat', 'label' => 'Belichtung-nat: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'AR_Boden_Rutschfestigkeit', 'label' => 'Boden Rutschfestigkeit: ', 'render' => 'str'],
            ['key' => 'AR_Bodenaufbau', 'label' => 'Bodenaufbau: ', 'render' => 'str'],
            ['key' => 'AR_Nutzung_ON1800', 'label' => 'Nutzung ON1800: ', 'render' => 'str'],
            ['key' => 'AR_Schwingungsklasse', 'label' => 'Schwingungsklasse: ', 'render' => 'str'],
            ['key' => 'PHY_Akustik_Schallgrad', 'label' => 'Akustik Schallgrad: ', 'render' => 'str'],
            ['key' => 'PHY_Akustik_T500', 'label' => 'Akustik T500: ', 'render' => 'nr', 'unit' => 's'],
        ],
    ],

    // ---------------------------------------------------------------- Elektro-Ausstattung
    'elektro_ausstattung' => [
        'label' => 'Elektro-Ausstattung',
        'perRow' => 4,
        'params' => [
            ['key' => 'EL_Beleuchtung 1 Stk', 'label' => 'Beleuchtung 1 Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Beleuchtung 1 Typ', 'label' => 'Beleuchtung 1 Typ: ', 'render' => 'str'],
            ['key' => 'EL_Beleuchtung 2 Stk', 'label' => 'Beleuchtung 2 Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Beleuchtung 2 Typ', 'label' => 'Beleuchtung 2 Typ: ', 'render' => 'str'],
            ['key' => 'EL_Beleuchtung 3 Stk', 'label' => 'Beleuchtung 3 Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Beleuchtung 3 Typ', 'label' => 'Beleuchtung 3 Typ: ', 'render' => 'str'],
            ['key' => 'EL_Beleuchtung 4 Stk', 'label' => 'Beleuchtung 4 Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Beleuchtung 4 Typ', 'label' => 'Beleuchtung 4 Typ: ', 'render' => 'str'],
            ['key' => 'EL_Beleuchtung 5 Stk', 'label' => 'Beleuchtung 5 Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Beleuchtung 5 Typ', 'label' => 'Beleuchtung 5 Typ: ', 'render' => 'str'],
            ['key' => 'EL_Beleuchtung dimmbar JA/NEIN', 'label' => 'Beleuchtung dimmbar: ', 'render' => 'hackerl', 'true_val' => 'JA'],
            ['key' => 'EL_Beleuchtungsstaerke', 'label' => 'Beleuchtungsstaerke: ', 'render' => 'nr', 'unit' => 'lx'],
            ['key' => 'EL_Bodendose Stk', 'label' => 'Bodendose Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Brandmelder Decke JA/NEIN', 'label' => 'Brandmelder Decke: ', 'render' => 'hackerl', 'true_val' => 'JA'],
            ['key' => 'EL_Brandmelder ZwDecke JA/NEIN', 'label' => 'Brandmelder ZwDecke: ', 'render' => 'hackerl', 'true_val' => 'JA'],
            ['key' => 'EL_Doppeldatendose Stk', 'label' => 'Doppeldatendose Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Einzel-Datendose Stk', 'label' => 'Einzel-Datendose Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Jalousie JA/NEIN', 'label' => 'Jalousie: ', 'render' => 'hackerl', 'true_val' => 'JA'],
            ['key' => 'EL_Kamera Stk', 'label' => 'Kamera Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Lautsprecher Stk', 'label' => 'Lautsprecher Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Leistungsbedarf_W_pro_m2', 'label' => 'Leistungsbedarf W pro m2: ', 'render' => 'nr', 'unit' => 'W/m2'],
            ['key' => 'EL_Lichtfarbe K', 'label' => 'Lichtfarbe K: ', 'render' => 'str'],
            ['key' => 'EL_Lichtruf - Steckmodul Stk', 'label' => 'Lichtruf - Steckmodul Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Lichtruf - Terminal Stk', 'label' => 'Lichtruf - Terminal Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Lichtschaltung BWM JA/NEIN', 'label' => 'Lichtschaltung BWM: ', 'render' => 'hackerl', 'true_val' => 'JA'],
            ['key' => 'EL_Not_Aus_Funktion', 'label' => 'Not Aus Funktion: ', 'render' => 'str'],
            ['key' => 'EL_Notlicht RZL Stk', 'label' => 'Notlicht RZL Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Notlicht SL Stk', 'label' => 'Notlicht SL Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Uhr - Decke Stk', 'label' => 'Uhr - Decke Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'EL_Uhr - Wand Stk', 'label' => 'Uhr - Wand Stk: ', 'render' => 'str', 'unit' => 'Stk'],
            ['key' => 'ET_5x10mm2_AV_Stk', 'label' => '5x10mm2 AV Stk: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'ET_5x10mm2_Digestorium_Stk', 'label' => '5x10mm2 Digestorium Stk: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'ET_5x10mm2_USV_Stk', 'label' => '5x10mm2 USV Stk: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'ET_Digestorium_MSR_230V_SV_Stk', 'label' => 'Digestorium MSR 230V SV Stk: ', 'render' => 'nr', 'unit' => 'Stk'],
            ['key' => 'ET_EMV', 'label' => 'EMV: ', 'render' => 'str'],
        ],
    ],

    // ---------------------------------------------------------------- HKLS-Lasten & Detail
    'hkls_lasten' => [
        'label' => 'HKLS-Lasten & Detail',
        'perRow' => 4,
        'params' => [
            ['key' => 'HT_Abluft_Esse_Stk', 'label' => 'Abluft Esse Stk: ', 'render' => 'hackerl', 'unit' => 'Stk', 'true_val' => 1],
            ['key' => 'HT_Abluft_Rauchgasabzug_Stk', 'label' => 'Abluft Rauchgasabzug Stk: ', 'render' => 'hackerl', 'unit' => 'Stk', 'true_val' => 1],
            ['key' => 'HT_Abluft_Schweissabsaugung_Stk', 'label' => 'Abluft Schweissabsaugung Stk: ', 'render' => 'hackerl', 'unit' => 'Stk', 'true_val' => 1],
            ['key' => 'HT_Abluft_Vakuumpumpe', 'label' => 'Abluft Vakuumpumpe: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'HT_Belueftung', 'label' => 'Belueftung: ', 'render' => 'str'],
            ['key' => 'HT_Entlueftung', 'label' => 'Entlueftung: ', 'render' => 'str'],
            ['key' => 'HT_Fancoil W', 'label' => 'Fancoil W: ', 'render' => 'str', 'unit' => 'W'],
            ['key' => 'HT_Fussbodenkühlung W', 'label' => 'Fussbodenkühlung W: ', 'render' => 'str', 'unit' => 'W'],
            ['key' => 'HT_Geraeteabluft m3/h', 'label' => 'Geraeteabluft m3/h: ', 'render' => 'nr', 'unit' => 'm3/h'],
            ['key' => 'HT_Heizlast W', 'label' => 'Heizlast W: ', 'render' => 'str', 'unit' => 'W'],
            ['key' => 'HT_Heizung', 'label' => 'Heizung: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'HT_Kaelteabgabe_Typ', 'label' => 'Kaelteabgabe Typ: ', 'render' => 'str'],
            ['key' => 'HT_Kuehlung', 'label' => 'Kuehlung: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'HT_Kühldecke W', 'label' => 'Kühldecke W: ', 'render' => 'str', 'unit' => 'W'],
            ['key' => 'HT_Kühllast W', 'label' => 'Kühllast W: ', 'render' => 'str', 'unit' => 'W'],
            ['key' => 'HT_Kühlung Lueftung W', 'label' => 'Kühlung Lueftung W: ', 'render' => 'str', 'unit' => 'W'],
            ['key' => 'HT_Kühlwasserleistung_W', 'label' => 'Kühlwasserleistung W: ', 'render' => 'kify_ka', 'unit' => 'W'],
            ['key' => 'HT_Luftmenge Abluft m3/h', 'label' => 'Luftmenge Abluft m3/h: ', 'render' => 'nr', 'unit' => 'm3/h'],
            ['key' => 'HT_Luftmenge m3/h', 'label' => 'Luftmenge m3/h: ', 'render' => 'str', 'unit' => 'm3/h'],
            ['key' => 'HT_Summe Kühlung W', 'label' => 'Summe Kühlung W: ', 'render' => 'str', 'unit' => 'W'],
            ['key' => 'HT_Waermeabgabe', 'label' => 'Waermeabgabe: ', 'render' => 'nr'],
            ['key' => 'HT_Waermeabgabe_Typ', 'label' => 'Waermeabgabe Typ: ', 'render' => 'str'],
        ],
    ],

    // ---------------------------------------------------------------- Labor-Gas (Detail)
    'labor_gas_detail' => [
        'label' => 'Labor-Gas (Detail)',
        'perRow' => 6,
        'params' => [
            ['key' => 'Ar Reinheit', 'label' => 'Ar Reinheit: ', 'render' => 'str'],
            ['key' => 'CO2 Reinheit', 'label' => 'CO2 Reinheit: ', 'render' => 'str'],
            ['key' => 'CO2_Melder', 'label' => 'CO2 Melder: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'DL ISO 8573', 'label' => 'DL ISO 8573: ', 'render' => 'str'],
            ['key' => 'H2 Reinheit', 'label' => 'H2 Reinheit: ', 'render' => 'str'],
            ['key' => 'He Reinheit', 'label' => 'He Reinheit: ', 'render' => 'str'],
            ['key' => 'He-RF', 'label' => 'He-RF: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'LHe', 'label' => 'LHe: ', 'render' => 'hackerl', 'true_val' => 1],
            ['key' => 'N2 Reinheit', 'label' => 'N2 Reinheit: ', 'render' => 'str'],
            ['key' => 'O2 Reinheit', 'label' => 'O2 Reinheit: ', 'render' => 'str'],
            ['key' => 'O2_Mangel', 'label' => 'O2 Mangel: ', 'render' => 'hackerl', 'true_val' => 1],
        ],
    ],

    // ---------------------------------------------------------------- Med.-tech. (Sondergruppe)
    // Wird NICHT als Checkbox-Block dargestellt, sondern über die Modus-Auswahl
    // (keine / nur Auflistung / mit Parametern) gesteuert.
    'medtech' => [
        'label' => 'Med.-tech.',
        'perRow' => 1,
        'special' => true,
        'params' => [
            ['key' => '__MT_TABELLE__', 'label' => 'Medizintechnische Elemente (Tabelle)', 'render' => 'mt_table', 'col' => false],
        ],
    ],
];

// ============================================================================
//  HILFSFUNKTIONEN
// ============================================================================


function report_all_params(): array
{
    global $REPORT_PARAM_GROUPS;
    $out = [];
    foreach ($REPORT_PARAM_GROUPS as $groupId => $group) {
        foreach ($group['params'] as $p) {
            $p['group'] = $groupId;
            $out[] = $p;
        }
    }
    return $out;
}



/**
 * Echte SQL-Spalten unter einer Menge von Keys.
 * Virtuelle Parameter (col => false) werden ausgelassen, Duplikate entfernt.
 * (Manche Keys – z. B. 'Anmerkung MedGas' – kommen in mehreren Gruppen vor;
 *  array_unique sorgt dafür, dass die Spalte trotzdem nur einmal selektiert wird.)
 */
function report_sql_columns_for(array $selectedKeys): array
{
    $cols = [];
    foreach (report_all_params() as $p) {
        $isCol = $p['col'] ?? true;
        if ($isCol && in_array($p['key'], $selectedKeys, true)) {
            $cols[] = $p['key'];
        }
    }
    return array_values(array_unique($cols));
}