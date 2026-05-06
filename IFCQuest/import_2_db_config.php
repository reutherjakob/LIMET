<?php
/**
 * import_2_db_config.php
 * ══════════════════════════════════════════════════════════════════
 * Zentrale Konfiguration für den Excel-Import.
 *
 * Zwei Parameter-Kategorien pro Element-Typ:
 *
 *   element_params  – bestimmen WELCHES Element gewählt wird.
 *                     Werden NICHT in die DB geschrieben.
 *                     Beispiel: Breite×Tiefe beim Labortisch.
 *
 *   variante_params – bilden den Varianten-Fingerabdruck.
 *                     Werden in tabelle_projekt_elementparameter
 *                     geschrieben. Zwei Excel-Zeilen mit identischen
 *                     variante_params → gleiche Variante, Anzahl++.
 *
 * ALLE anderen Parameter die in der DB gespeichert sind (also alles
 * was weder in element_params noch in variante_params vorkommt)
 * werden beim Import automatisch ignoriert:
 *   – nicht gelöscht, nicht überschrieben, nicht für Matching
 *   – führen zu "ambiguous" wenn sie DB-Varianten unterscheiden
 *
 * Regeln:
 *   – tabelle_varianten wird NIEMALS beschrieben, nur gelesen.
 *   – DB-Elemente deren ElementID in keinem Mapping als Ziel
 *     auftaucht gelten als "not_managed" und werden nicht angepasst.
 * ══════════════════════════════════════════════════════════════════
 */

function get_all_managed_element_ids(): array
{
    $ids = [];
    foreach (ELEMENT_MAPPING as $cfg) {
        if (isset($cfg['laengen'])) $ids = array_merge($ids, array_values($cfg['laengen']));
        if (isset($cfg['breite_tiefe'])) $ids = array_merge($ids, array_values($cfg['breite_tiefe']));
        if (isset($cfg['sondermass'])) $ids[] = $cfg['sondermass'];
        if (isset($cfg['element_id'])) $ids[] = $cfg['element_id'];
    }
    return array_unique($ids);
}

/**
 * Findet den passenden Mapping-Eintrag für einen Familiennamen.
 *   match='prefix' → Familienname enthält den Key (MZ-Typen)
 *   match='exact'  → Familienname stimmt exakt überein (feste Familien)
 */
function find_mapping(string $familie): ?array
{
    if (isset(ELEMENT_MAPPING[$familie]) && ELEMENT_MAPPING[$familie]['match'] === 'exact') {
        return ELEMENT_MAPPING[$familie];
    }
    foreach (ELEMENT_MAPPING as $key => $cfg) {
        if ($cfg['match'] === 'prefix' && str_contains($familie, $key)) {
            return $cfg;
        }
    }
    return null;
}

const MZ_STANDARD_LAENGEN = [60, 90, 120, 150, 180, 210, 240, 270];
const MZ_LAENGE_WARN_DIFF_CM = 5;

const ELEMENT_MAPPING = [

    // ── MZ-Typen: match='prefix', Familienname enthält den Key ───────────────

    '4-35-25-1' => [
        'match' => 'prefix',
        'typ' => 'laengen',
        'laengen' => [
            45 => '4.35.25.5',
            90 => '4.35.25.4',
            120 => '4.35.25.3',
            150 => '4.35.25.6',
            180 => '4.35.25.2',
            210 => '4.35.25.7',
        ],
        'sondermass' => '4.35.25.8',
        'element_params' => ['MT_LIMET_Breite'],
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
    ],

    '4-35-25-XX' => [
        'match' => 'prefix',
        'typ' => 'laengen',
        'laengen' => [
            90 => '4.35.25.11',
            120 => '4.35.25.12',
            150 => '4.35.25.13',
            180 => '4.35.25.14',
            330 => '4.35.25.20',
        ],
        'sondermass' => '4.35.25.21',
        'element_params' => ['MT_LIMET_Breite'],
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
    ],

    // Breite×Tiefe → Element-Auswahl, Höhe → Variante
    '4-35-10-1' => [
        'match' => 'prefix',
        'typ' => 'tisch',
        'breite_tiefe' => [
            '60x60' => '4.35.10.1',
            '90x60' => '4.35.10.17',
            '120x60' => '4.35.10.3',
            '150x60' => '4.35.10.4',
            '180x60' => '4.35.10.21',
            '60x75' => '4.35.10.20',
            '90x75' => '4.35.10.5',
            '120x75' => '4.35.10.6',
            '150x75' => '4.35.10.7',
            '180x75' => '4.35.10.8',
            '60x90' => '4.35.10.19',
            '90x90' => '4.35.10.9',
            '120x90' => '4.35.10.11',
            '150x90' => '4.35.10.12',
            '180x90' => '4.35.10.14',
        ],
        'sondermass' => '4.35.10.16',
        'element_params' => ['MT_LIMET_Breite', 'MT_LIMET_Tiefe'],
        'variante_params' => ['MT_LIMET_Höhe'],
    ],

    // Breite → Variante (Breite kommt aus MT_LIMET_Breite, nicht aus der Längen-Spalte)
    '4-20-80-1' => [
        'match' => 'prefix',
        'typ' => 'laengen',
        'laenge_fallback' => 'MT_LIMET_Breite', // falls $laenge leer: aus diesem param lesen
        'laengen' => [
            60 => '4.20.80.1',
            90 => '4.20.80.1',
            120 => '4.20.80.1',
        ],
        'sondermass' => '4.20.80.1',
        'element_params' => [],
        'variante_params' => ['MT_LIMET_Breite'],
    ],

    // ── Feste Familien: match='exact', exakter Namens-Match ──────────────────

    'TMO_-_LIMET_4-20-60-2 Hängeschrank Flügel DIN CrNi zweitürig' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '4.20.60.2',
        'element_params' => [],
        'variante_params' => ['MT_LIMET_Breite', 'MT_LIMET_Höhe', 'MT_LIMET_Tiefe'],
        // Alle anderen DB-Parameter (z.B. Netzart) → automatisch ignoriert
    ],

    'TMO_-_LIMET_4-35-30-2 Gefahrenstoffsicherheitsschrank - SäureLaugen doppelflügelig' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '4.35.30.2',
        'element_params' => [],
        'variante_params' => ['MT_LIMET_Breite'],
        // Netzart SV/AV ist in der DB, kommt im Modell nie vor
        // → automatisch ignoriert, aber löst "ambiguous" aus wenn
        //   mehrere Varianten sich nur dadurch unterscheiden
    ],

    '9.30.10.1 Punktabsaugung deckenmontiert' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '9.30.10.1',
        'element_params' => [],
        'variante_params' => [],
    ],
];

const PARAMETER_MAPPING = [
    'MT_LIMET_Höhe' => ['id' => 1, 'einheit' => 'm', 'bezeichnung' => 'Höhe'],
    'MT_LIMET_Tiefe' => ['id' => 3, 'einheit' => 'm', 'bezeichnung' => 'Tiefe'],
    'MT_LIMET_Breite' => ['id' => 2, 'einheit' => 'm', 'bezeichnung' => 'Breite'],
    'MT_LIMET_Anzahl Steckdosen-Auslässe' => ['id' => 153, 'einheit' => 'Stk', 'bezeichnung' => 'Steckdosen'],
    'MT_LIMET_Anzahl EDV-Auslässe' => ['id' => 127, 'einheit' => 'Stk', 'bezeichnung' => 'EDV (RJ45)'],
    'MT_LIMET_Anzahl DL-Auslässe' => ['id' => 121, 'einheit' => 'Stk', 'bezeichnung' => 'DL-5'],
    'MT_LIMET_Anzahl VA-Auslässe' => ['id' => 122, 'einheit' => 'Stk', 'bezeichnung' => 'VAC'],
    'MT_LIMET_Anzahl O2-Auslässe' => ['id' => 117, 'einheit' => 'Stk', 'bezeichnung' => 'O₂'],
    'MT_LIMET_Anzahl CO2-Auslässe' => ['id' => 126, 'einheit' => 'Stk', 'bezeichnung' => 'CO₂'],
    'MT_LIMET_Anzahl N2-Auslässe' => ['id' => 149, 'einheit' => 'Stk', 'bezeichnung' => 'N₂'],
    'MT_LIMET_Sichtbarkeit ABW' => ['id' => 159, 'einheit' => '', 'bezeichnung' => 'Abfluss Wand'],
    'MT_LIMET_Sichtbarkeit KW' => ['id' => 30, 'einheit' => '', 'bezeichnung' => 'Kaltwasser'],
    'MT_LIMET_Sichtbarkeit VE' => ['id' => 83, 'einheit' => '', 'bezeichnung' => 'VE-Wasser'],
    'MT_LIMET_Sichtbarkeit WW' => ['id' => 31, 'einheit' => '', 'bezeichnung' => 'Warmwasser'],
];