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
        // gruppe-Typ: element_id steckt im gruppe-Eintrag selbst
        if (($cfg['typ'] ?? '') === 'gruppe' && isset($cfg['element_id'])) $ids[] = $cfg['element_id'];
    }
    return array_unique($ids);
}

/**
 * Findet den passenden Mapping-Eintrag für einen Familiennamen.
 *   match='prefix' → Familienname enthält den Key (MZ-Typen)
 *   match='exact'  → Familienname stimmt exakt überein (feste Familien)
 *   typ='gruppe'   → mehrere Familien bilden zusammen ein Element;
 *                    alle Familien der Gruppe matchen auf denselben Eintrag.
 *                    'param_quelle' bestimmt welche Familie die Maße liefert,
 *                    alle anderen sind 'secondary' (zählen 0×).
 */
function find_mapping(string $familie): ?array
{
    // Direkt-Match (exact oder gruppe)
    if (isset(ELEMENT_MAPPING[$familie]) && ELEMENT_MAPPING[$familie]['match'] === 'exact') {
        return ELEMENT_MAPPING[$familie];
    }
    // Gruppe: alle Familien-Einträge durchsuchen
    foreach (ELEMENT_MAPPING as $cfg) {
        if (($cfg['typ'] ?? '') === 'gruppe' && in_array($familie, $cfg['familien'], true)) {
            return $cfg;
        }
    }
    // Prefix-Match
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
            60 => '4.35.25.27',
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
            60 => '4.35.25.32',
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

    '9.30.10.1 Punktabsaugung deckenmontiert' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '9.30.10.1',
        'element_params' => [],
        'variante_params' => [],
    ],

    'TMO_-_LIMET_9-30-10-1 Punktabsaugung deckenmontiert' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '9.30.10.1',
        'element_params' => [],
        'variante_params' => [],
    ],


    // Hinweis: TMO_-_LIMET_4-20-30-1 Spülbecken DIN CrNi ist Teil der
    // GRUPPE_Spuelenverbau_Labor (siehe unten) und ist dort die
    // LEITFAMILIE (zählt 1× je Becken, liefert Becken-Maße).
    // ── Gruppen-Typ: mehrere Familien = 1 DB-Element ─────────────────────────
    // 'leitfamilien'    = Familien die je 1× zählen (eine Zeile = eine Instanz).
    // 'begleitfamilien' = { familie → { pool, fallback } }:
    //     pro Leit-Instanz wird aus jedem Pool max. 1 Begleiter konsumiert
    //     (Auswahl nach geringster Breitendifferenz zur Leit-Instanz).
    //     Überzählige Begleiter werden über ihren 'fallback'-Eintrag als
    //     eigenständige Elemente importiert.
    // 'param_quellen'   = { familie → variante_params[] die diese Familie liefert }.
    // Familien ohne begleitfamilien-Eintrag und ohne Leitstatus zählen 0×
    // und liefern nur ggf. ihre param_quellen-Params (Altverhalten).

    // Spülenverbau Labormöbel (4.35.20.1):
    //
    //   REGEL: je Spülbecken existiert genau EINE Spüle im Raum.
    //   → Leitfamilie = Spülbecken (zählt 1×, liefert Becken-Maße).
    //
    //   Unterbau & Arbeitsplatte sind "Begleitfamilien":
    //   Pro Spülbecken wird aus jedem Pool max. 1 Stück KONSUMIERT
    //   (Auswahl: geringste Breitendifferenz zum Spülbecken).
    //   Konsumiertes Stück zählt 0×, liefert aber seine Params
    //   (Unterbau → Verbau-Breite/Tiefe).
    //
    //   ÜBERZÄHLIGE Unterbauten/Arbeitsplatten gehören NICHT zur Spüle
    //   und fallen auf ihr 'fallback'-Mapping zurück (eigenständiges
    //   Element, z.B. 4.20.20.3 Unterbau DIN - Flügel CrNi lfm).
    //   Beide Unterbau-Varianten (ein-/zweitürig) teilen sich den
    //   Pool 'unterbau' → zusammen max. 1 pro Becken.

    'GRUPPE_Spuelenverbau_Labor' => [
        'match'   => 'gruppe',
        'typ'     => 'gruppe',
        'familien' => [
            'TMO_-_LIMET_4-20-20-3 Unterbau - Flügel DIN CrNi zweitürig',
            'TMO_-_LIMET_4-20-20-3 Unterbau - Flügel DIN CrNi eintürig',
            'TMO_-_LIMET_4-20-40-1 Arbeitsplatte DIN CrNi',
            'TMO_-_LIMET_4-20-30-1 Spülbecken DIN CrNi',
        ],
        // Leitfamilie = Spülbecken: Anzahl Spülen = Anzahl Spülbecken
        'leitfamilien' => [
            'TMO_-_LIMET_4-20-30-1 Spülbecken DIN CrNi',
        ],
        // Begleitfamilien: pro Leit-Instanz max. 1 pro 'pool' konsumiert,
        // Überschuss → 'fallback' (Key in ELEMENT_MAPPING, match='fallback')
        'begleitfamilien' => [
            'TMO_-_LIMET_4-20-20-3 Unterbau - Flügel DIN CrNi zweitürig' => [
                'pool' => 'unterbau', 'fallback' => 'FALLBACK_Unterbau_Fluegel_CrNi',
            ],
            'TMO_-_LIMET_4-20-20-3 Unterbau - Flügel DIN CrNi eintürig' => [
                'pool' => 'unterbau', 'fallback' => 'FALLBACK_Unterbau_Fluegel_CrNi',
            ],
            'TMO_-_LIMET_4-20-40-1 Arbeitsplatte DIN CrNi' => [
                'pool' => 'arbeitsplatte', 'fallback' => 'FALLBACK_Arbeitsplatte_DIN_CrNi',
            ],
        ],
        'param_quellen' => [
            'TMO_-_LIMET_4-20-20-3 Unterbau - Flügel DIN CrNi zweitürig' => ['MT_LIMET_Breite', 'MT_LIMET_Tiefe'],
            'TMO_-_LIMET_4-20-20-3 Unterbau - Flügel DIN CrNi eintürig'  => ['MT_LIMET_Breite', 'MT_LIMET_Tiefe'],
            'TMO_-_LIMET_4-20-30-1 Spülbecken DIN CrNi'                  => ['MT_LIMET_Spuelbecken_Breite', 'MT_LIMET_Spuelbecken_Tiefe', 'MT_LIMET_Spuelbecken_Hoehe'],
        ],
        'element_id' => '4.35.20.1',
        'element_params' => [],
        'variante_params' => [
            'MT_LIMET_Breite',
            'MT_LIMET_Tiefe',
            'MT_LIMET_Spuelbecken_Breite',
            'MT_LIMET_Spuelbecken_Tiefe',
            'MT_LIMET_Spuelbecken_Hoehe',
        ],
    ],

    // ── Fallback-Mappings für überzählige Gruppen-Begleiter ──────────
    // match='fallback' → werden von find_mapping() NIE direkt gematcht,
    // nur explizit über 'begleitfamilien.fallback' referenziert.
    // element_id zählt trotzdem als "managed".

    // Unterbau ohne Spüle → eigenständiger Unterschrank (lfm, Breite als Variante)
    'FALLBACK_Unterbau_Fluegel_CrNi' => [
        'match' => 'fallback',
        'typ' => 'fixed',
        'element_id' => '4.20.20.3', // Unterbau DIN - Flügel CrNi lfm
        'element_params' => [],
        'variante_params' => ['MT_LIMET_Breite'],
    ],

    // Arbeitsplatte ohne Spüle → eigenständige Arbeitsplatte (lfm)
    'FALLBACK_Arbeitsplatte_DIN_CrNi' => [
        'match' => 'fallback',
        'typ' => 'fixed',
        'element_id' => '4.20.40.1', // Arbeitsplatte DIN CrNi lfm
        'element_params' => [],
        'variante_params' => ['MT_LIMET_Breite'],
    ],

    // Augendusche — DB: 2.33.12.1 ("Augendusche")
    // Fixes Element, keine Dimensionsvarianten
    'TMM_-_LIMET_2-33-12-1 Augendusche' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '2.33.12.1',
        'element_params' => [],
        'variante_params' => [],
    ],

    // Gefahrenstoffsicherheitsschrank brennbare Flüssigkeiten — DB: 4.35.30.3
    // Zwei verschiedene Revit-Familiennamen für dasselbe Element, Breite als Variante
    'TMO_-_LIMET_4-35-30-3 Gefahrenstoffsicherheitsschrank - brennbare Flüssigkeiten doppelflügelig' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '4.35.30.3',
        'element_params' => [],
        'variante_params' => ['MT_LIMET_Breite'],
    ],

    'TMO_-_LIMET_4-35-30-3 Laborschrank brennbare Flüssigkeiten' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '4.35.30.3',
        'element_params' => [],
        'variante_params' => ['MT_LIMET_Breite'],
    ],


    // Labortiefkühlschrank — Auswahl per Breite×Höhe aus MT_LIMET_Breite / MT_LIMET_Höhe.
    // Typ 'tisch' mit dim2_param=MT_LIMET_Höhe statt Tiefe.
    // Breite kommt aus der Länge-Spalte (oder MT_LIMET_Breite),
    // Höhe kommt aus der Höhe-Spalte (oder MT_LIMET_Höhe über dim2_param).
    // sondermass → 9.30.30.1 (generische, wenn keine Abmessung passt)
    '9-30-30-5' => [
        'match' => 'prefix',
        'typ' => 'tisch',
        'dim2_param' => 'MT_LIMET_Höhe',
        'dim2_label' => 'H',
        'breite_tiefe' => [
            // Schlüssel: "Breite_cm x Höhe_cm"
            '60x86' => '9.30.30.35', // Unterbau B60 H86
            '60x90' => '9.30.30.21', // freistehend B60 H90
            '65x200' => '9.30.30.22', // freistehend B65 H200
            '80x160' => '9.30.30.15', // freistehend B80 H160
            '80x200' => '9.30.30.29', // freistehend B80 H200
            '100x100' => '9.30.30.36', // -80°C B100 T70 H100
            '100x200' => '9.30.30.32', // -80°C B100 T100 H200
            '115x200' => '9.30.30.33', // -80°C B115 T100 H200
            '125x200' => '9.30.30.34', // -80°C B125 T100 H200
            '150x220' => '9.30.30.27', // freistehend B150 H220
        ],
        'sondermass' => '9.30.30.1',
        'element_params' => ['MT_LIMET_Breite', 'MT_LIMET_Höhe'],
        'variante_params' => [],
    ],

    // Abzug-Esse — fix, keine Dimensionen
    'TMO_-_LIMET_9-30-10-3 Abzug-Esse' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '9.30.10.3',
        'element_params' => [],
        'variante_params' => [],
    ],

// Wägetisch — fix, keine Dimensionen
    'TMO_-_LIMET_9-30-35-2 Wägetisch' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '9.30.35.2',
        'element_params' => [],
        'variante_params' => [],
    ],

// Mikroskopietisch hydraulisch gedämpft — fix, keine Dimensionen
    'TMO_-_LIMET_9-30-40-9 Mikroskopiertisch - hydraulisch gedämpft' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '9.30.40.9',
        'element_params' => [],
        'variante_params' => [],
    ],

// Reinraumwerkbank — fix, keine Dimensionen
    'TMO_-_LIMET_9-30-45-2 Reinraumwerkbank' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '9.30.45.2',
        'element_params' => [],
        'variante_params' => [],
    ],

// Digestorium Standard — Auswahl per Breite → 4 Elemente
// Sondermaß → 9.30.45.3 (generischer Fallback)
    'TMO_-_LIMET_9-30-45-3 Digestorium - Standard' => [
        'match' => 'exact',
        'typ' => 'laengen',
        'laengen' => [
            120 => '9.30.45.25',
            150 => '9.30.45.26',
            180 => '9.30.45.27',
            210 => '9.30.45.28',
        ],
        'sondermass' => '9.30.45.3',
        'element_params' => ['MT_LIMET_Breite'],
        'variante_params' => [],
    ],

    'TMO_LIMET_9-30-30-5 Labortiefkühlschrank' => [
        'match' => 'exact',
        'typ' => 'tisch',
        'dim2_param' => 'MT_LIMET_Höhe',
        'dim2_label' => 'H',
        'breite_tiefe' => [
            '60x86'   => '9.30.30.35',
            '60x90'   => '9.30.30.21',
            '65x200'  => '9.30.30.22',
            '80x160'  => '9.30.30.15',
            '80x200'  => '9.30.30.29',
            '100x100' => '9.30.30.36',
            '100x200' => '9.30.30.32',
            '115x200' => '9.30.30.33',
            '125x200' => '9.30.30.34',
            '150x220' => '9.30.30.27',
        ],
        'sondermass'        => '9.30.30.7',
        'no_dim_fallback'   => '9.30.30.7',   // ← neu: kein Breite/Höhe → Basis-Labortiefkühlschrank
        'element_params'    => ['MT_LIMET_Breite', 'MT_LIMET_Höhe'],
        'variante_params'   => [],
    ],
    'TMO_-_LIMET_4-20-10-10 Hochschrank - Flügel DIN CrNi eintürig' => [
        'match' => 'exact',
        'typ' => 'fixed',
        'element_id' => '4.35.15.2',
        'element_params' => [],
        'variante_params' => ['MT_LIMET_Breite'],
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
];

const PARAMETER_MAPPING = [
    'MT_LIMET_Höhe' => ['id' => 1, 'einheit' => 'm', 'bezeichnung' => 'Höhe'],
    'MT_LIMET_Tiefe' => ['id' => 2, 'einheit' => 'm', 'bezeichnung' => 'Tiefe'],
    'MT_LIMET_Breite' => ['id' => 3, 'einheit' => 'm', 'bezeichnung' => 'Breite'],
    // Spülbecken-Maße (Teil von Spülenverbau 4.35.20.1)
    // source_col = Revit-Parametername der Familie im Excel
    // id         = Ziel-Parameter-ID in der DB
    'MT_LIMET_Spuelbecken_Breite' => ['id' => 171, 'einheit' => 'm', 'bezeichnung' => 'Spülbecken Breite', 'source_col' => 'MT_LIMET_Breite'],
    'MT_LIMET_Spuelbecken_Tiefe' => ['id' => 172, 'einheit' => 'm', 'bezeichnung' => 'Spülbecken Tiefe', 'source_col' => 'MT_LIMET_Tiefe'],
    'MT_LIMET_Spuelbecken_Hoehe' => ['id' => 173, 'einheit' => 'm', 'bezeichnung' => 'Spülbecken Höhe', 'source_col' => 'MT_LIMET_Höhe'],
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