<?php
/**
 * import_2_db_config.php
 * ══════════════════════════════════════════════════════════════════
 * Zentrale Konfiguration für den Import.
 *
 * Pro Typ zwei Parameter-Listen:
 *   variante_params  – bilden den Varianten-Fingerprint und werden in
 *                      tabelle_projekt_elementparameter geschrieben.
 *                      Zwei Zeilen mit identischen variante_params →
 *                      gleiche Variante, Anzahl wird erhöht.
 *   info_params      – werden ebenfalls importiert, aber NICHT für
 *                      das Varianten-Matching verwendet (z.B. Breite
 *                      bei MZ — ist bereits im Element kodiert).
 *
 * tabelle_varianten wird NIEMALS beschrieben, nur gelesen.
 * ══════════════════════════════════════════════════════════════════
 */

// ──────────────────────────────────────────────────────────────────
// 1. MZ-TYPEN: Revit-Familie-Substring → Element + Parameter
//    Reihenfolge ist entscheidend — erster Treffer gewinnt.
// ──────────────────────────────────────────────────────────────────
const MZ_FAMILIE_MAPPING = [

    // ── Medienzelle bodenstehend ───────────────────────────────────
    '4-35-25-1' => [
        'typ'     => 'laengen',
        'laengen' => [
            45  => '4.35.25.5',
            90  => '4.35.25.4',
            120 => '4.35.25.3',
            150 => '4.35.25.6',
            180 => '4.35.25.2',
            210 => '4.35.25.7',
        ],
        'sondermass'     => '4.35.25.8',
        // Anschlüsse bilden die Variante — gleiche Kombination = gleiche Variante
        'variante_params' => [
            'MT_LIMET_Anzahl Steckdosen-Auslässe',
            'MT_LIMET_Anzahl EDV-Auslässe',
            'MT_LIMET_Anzahl DL-Auslässe',
            'MT_LIMET_Anzahl VA-Auslässe',
            'MT_LIMET_Anzahl O2-Auslässe',
            'MT_LIMET_Anzahl CO2-Auslässe',
            'MT_LIMET_Anzahl N2-Auslässe',
            'MT_LIMET_Sichtbarkeit ABW',
            'MT_LIMET_Sichtbarkeit KW',
            'MT_LIMET_Sichtbarkeit VE',
            'MT_LIMET_Sichtbarkeit WW',
        ],
        // Breite steckt bereits im gematchten Element → kein Varianten-Param
        'info_params' => ['MT_LIMET_Breite'],
    ],

    // ── Medienampel deckenmontiert ─────────────────────────────────
    '4-35-25-XX' => [
        'typ'     => 'laengen',
        'laengen' => [
            90  => '4.35.25.11',
            120 => '4.35.25.12',
            150 => '4.35.25.13',
            180 => '4.35.25.14',
            330 => '4.35.25.20',
        ],
        'sondermass'      => '4.35.25.21',
        'variante_params' => [
            'MT_LIMET_Anzahl Steckdosen-Auslässe',
            'MT_LIMET_Anzahl EDV-Auslässe',
            'MT_LIMET_Anzahl DL-Auslässe',
            'MT_LIMET_Anzahl VA-Auslässe',
            'MT_LIMET_Anzahl O2-Auslässe',
            'MT_LIMET_Anzahl CO2-Auslässe',
            'MT_LIMET_Anzahl N2-Auslässe',
            'MT_LIMET_Sichtbarkeit ABW',
            'MT_LIMET_Sichtbarkeit KW',
            'MT_LIMET_Sichtbarkeit VE',
            'MT_LIMET_Sichtbarkeit WW',
        ],
        'info_params' => ['MT_LIMET_Breite'],
    ],

    // ── Labortisch: Matching über Breite × Tiefe ──────────────────
    // Breite+Tiefe bestimmen das Element (z.B. 4.35.10.11 = 120×90).
    // Nur die Höhe ist variabel → einziger Varianten-Parameter.
    '4-35-10-1' => [
        'typ'          => 'tisch',
        'breite_tiefe' => [
            '60x60'  => '4.35.10.1',
            '90x60'  => '4.35.10.17',
            '120x60' => '4.35.10.3',
            '150x60' => '4.35.10.4',
            '180x60' => '4.35.10.21',
            '60x75'  => '4.35.10.20',
            '90x75'  => '4.35.10.5',
            '120x75' => '4.35.10.6',
            '150x75' => '4.35.10.7',
            '180x75' => '4.35.10.8',
            '60x90'  => '4.35.10.19',
            '90x90'  => '4.35.10.9',
            '120x90' => '4.35.10.11',
            '150x90' => '4.35.10.12',
            '180x90' => '4.35.10.14',
        ],
        'sondermass'      => '4.35.10.16',
        'variante_params' => ['MT_LIMET_Höhe'],
        'info_params'     => ['MT_LIMET_Breite', 'MT_LIMET_Tiefe'],
    ],

    // ── Hochregal lfm: Tiefe bildet die Variante ──────────────────
    '4-20-80-1' => [
        'typ'     => 'laengen',
        'laengen' => [
            90  => '4.20.80.1',
            120 => '4.20.80.1',
        ],
        'sondermass'      => '4.20.80.1',
        'variante_params' => ['MT_LIMET_Tiefe'],
        'info_params'     => ['MT_LIMET_Breite'],
    ],
];

const MZ_STANDARD_LAENGEN    = [60, 90, 120, 150, 180, 210, 240, 270];
const MZ_LAENGE_WARN_DIFF_CM = 5;

// ──────────────────────────────────────────────────────────────────
// 2. DIREKTE MAPPINGS: exakter Familienname → Element + Parameter
// ──────────────────────────────────────────────────────────────────
const FAMILIE_MAPPING = [

    'TMO_-_LIMET_4-20-60-2 Hängeschrank Flügel DIN CrNi zweitürig' => [
        'element_id'      => '4.20.60.2',
        // Höhe+Tiefe sind variabel → bilden die Variante
        'variante_params' => ['MT_LIMET_Höhe', 'MT_LIMET_Tiefe'],
        'info_params'     => ['MT_LIMET_Breite'],
    ],

    'TMO_-_LIMET_4-35-30-2 Gefahrenstoffsicherheitsschrank - SäureLaugen doppelflügelig' => [
        'element_id'      => '4.35.30.2',
        // Keine variablen Parameter → immer Variante A
        'variante_params' => [],
        'info_params'     => ['MT_LIMET_Breite'],
    ],

    '9.30.10.1 Punktabsaugung deckenmontiert' => [
        'element_id'      => '9.30.10.1',
        'variante_params' => [],
        'info_params'     => [],
    ],
];

// ──────────────────────────────────────────────────────────────────
// 3. PARAMETER-MAPPING: Excel-Spaltenname → DB-Parameter
//    Alle Parameter die überhaupt vorkommen können.
//    Welche davon pro Typ relevant sind, steuern variante_params
//    und info_params in den Mappings oben.
// ──────────────────────────────────────────────────────────────────
const PARAMETER_MAPPING = [
    'MT_LIMET_Höhe'                       => ['id' =>   1, 'einheit' => 'm',   'bezeichnung' => 'Höhe'],
    'MT_LIMET_Tiefe'                       => ['id' =>   3, 'einheit' => 'm',   'bezeichnung' => 'Tiefe'],
    'MT_LIMET_Breite'                      => ['id' =>   2, 'einheit' => 'm',   'bezeichnung' => 'Breite'],
    'MT_LIMET_Anzahl Steckdosen-Auslässe'  => ['id' => 153, 'einheit' => 'Stk', 'bezeichnung' => 'Steckdosen'],
    'MT_LIMET_Anzahl EDV-Auslässe'         => ['id' => 127, 'einheit' => 'Stk', 'bezeichnung' => 'EDV (RJ45)'],
    'MT_LIMET_Anzahl DL-Auslässe'          => ['id' => 121, 'einheit' => 'Stk', 'bezeichnung' => 'DL-5'],
    'MT_LIMET_Anzahl VA-Auslässe'          => ['id' => 122, 'einheit' => 'Stk', 'bezeichnung' => 'VAC'],
    'MT_LIMET_Anzahl O2-Auslässe'          => ['id' => 117, 'einheit' => 'Stk', 'bezeichnung' => 'O₂'],
    'MT_LIMET_Anzahl CO2-Auslässe'         => ['id' => 126, 'einheit' => 'Stk', 'bezeichnung' => 'CO₂'],
    'MT_LIMET_Anzahl N2-Auslässe'          => ['id' => 149, 'einheit' => 'Stk', 'bezeichnung' => 'N₂'],
    'MT_LIMET_Sichtbarkeit ABW'            => ['id' => 159, 'einheit' => '',    'bezeichnung' => 'Abfluss Wand'],
    'MT_LIMET_Sichtbarkeit KW'             => ['id' =>  30, 'einheit' => '',    'bezeichnung' => 'Kaltwasser'],
    'MT_LIMET_Sichtbarkeit VE'             => ['id' =>  83, 'einheit' => '',    'bezeichnung' => 'VE-Wasser'],
    'MT_LIMET_Sichtbarkeit WW'             => ['id' =>  31, 'einheit' => '',    'bezeichnung' => 'Warmwasser'],
];