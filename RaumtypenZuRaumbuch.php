<?php
/**
 * raumtypen_uebernahme.php  (ALLES IN EINEM FILE)
 *
 * Überträgt Angaben aus den Raumtypen (raumtypen.php / $labortypen) in
 * tabelle_räume – projektweise, anhand des Feldes `Raumtyp BH` (enthält die
 * Raumtyp-id).
 *
 * - Vorschau (alt → neu) je Zuordnung
 * - EIGENER Button pro Zuordnung; wirkt auf die ausgewählten (angehakten) Räume
 * - POST action=apply  ->  JSON-Antwort (im selben File)
 *
 * SICHERHEIT: Zielspalten stammen ausschließlich aus $GROUPS (Whitelist),
 * niemals vom Client. Werte werden per Prepared Statement gebunden.
 * Leere Raumtyp-Angaben werden übersprungen (bestehende Werte bleiben erhalten).
 */

require_once __DIR__ . '/utils/_utils.php';
require_once __DIR__ . '/Nutzerumfrage/raumtypen.php';   // definiert $labortypen


/* ═══════════════════════════════════════════════════════════════════════════
   HELFER
   ═══════════════════════════════════════════════════════════════════════════ */

function raumtyp_index(): array
{
    global $labortypen;
    static $idx = null;
    if ($idx === null) {
        $idx = [];
        foreach ($labortypen as $t) {
            if (isset($t['id'])) $idx[(string)$t['id']] = $t;
        }
    }
    return $idx;
}

/** Nutzbare Raumfläche (für m²-Benchmarks): Nutzfläche, sonst Nutzfläche_Soll. */
function room_area(array $room): ?float
{
    foreach (['Nutzfläche', 'Nutzfläche_Soll'] as $k) {
        $v = $room[$k] ?? null;
        if ($v !== null && (float)$v > 0) return (float)$v;
    }
    return null;
}

function _s($v): ?string
{
    if ($v === null) return null;
    $v = trim((string)$v);
    return $v === '' ? null : $v;
}

/** Temperatur: exakter Wortlaut von temp_min/temp_max, außer nach Erfordernis. */
function tt_temp(array $t, string $key): ?string
{
    if (($t['temp_nach_erfordernis'] ?? '0') === '1') return 'n.E.';
    return _s($t[$key] ?? null);
}

/** Druckregelung -> H6020 (varchar 20), abgekürzt. */
function tt_druck(array $t): ?string
{
    $raw = _s($t['druckregelung'] ?? null);
    if ($raw === null || $raw === '0') return null;
    $abk = strtr($raw, [
        'Unterdruck' => 'UD',
        'Überdruck' => 'ÜD',
        'ohne/mit Schleuse' => 'o./m.SL',
        'mit Schleuse' => 'm.SL',
        'ohne Schleuse' => 'o.SL',
    ]);
    $abk = trim(preg_replace('/\s+/', ' ', $abk));
    return mb_substr($abk, 0, 20);
}

/** Luftwechsel: Rate (m³/m²·h) bevorzugt, sonst kurzer Text. */
function tt_luftwechsel(array $t): ?string
{
    $rate = _s($t['luftwechsel_rate_m3_je_m2h'] ?? null);
    if ($rate !== null) return $rate;
    $txt = _s($t['luftwechsel'] ?? null);
    if ($txt !== null && mb_strlen($txt) <= 45) return $txt;
    return null;
}

/** Tempgradient: raumtypen hat KEIN echtes Gradientfeld -> Näherung aus
 *  temp_schwankung (Toleranz ± X). Numerik gerundet (Spalte ist tinyint). */
function tt_tempgradient(array $t): ?string
{
    $src = _s($t['temp_schwankung'] ?? null);
    if ($src === null) return null;
    if (preg_match('/([0-9]+(?:[.,][0-9]+)?)/', $src, $m)) {
        return (string)(int)round((float)str_replace(',', '.', $m[1]));
    }
    return null;
}

/** m²-Benchmark × Fläche -> Raumleistung (gerundet, ganzzahlig). */
function tt_mult(array $t, array $room, string $key): ?string
{
    $bench = _s($t[$key] ?? null);
    $area = room_area($room);
    if ($bench === null || $area === null) return null;
    return (string)(int)round((float)$bench * $area);
}

/** AV-Anteil der Anschlussleistung (W/Raum)
 *  = anschlussleistung(W/m²) × Fläche × AV_quotient.
 *  AV_quotient = Anteil Allgemeinversorgung (AV), 0..1. */
function tt_av_leistung(array $t, array $room): ?string
{
    $bench = _s($t['anschlussleistung'] ?? null);
    $area = room_area($room);
    $q = _s($t['AV_quotient'] ?? null);
    if ($bench === null || $area === null || $q === null) return null;
    $q = (float)str_replace(',', '.', $q);
    return (string)(int)round((float)$bench * $area * $q);
}

/** SV-Anteil (Rest) der Anschlussleistung (W/Raum)
 *  = anschlussleistung(W/m²) × Fläche × (1 − AV_quotient). */
function tt_sv_leistung(array $t, array $room): ?string
{
    $bench = _s($t['anschlussleistung'] ?? null);
    $area = room_area($room);
    $q = _s($t['AV_quotient'] ?? null);
    if ($bench === null || $area === null || $q === null) return null;
    $q = (float)str_replace(',', '.', $q);
    return (string)(int)round((float)$bench * $area * (1.0 - $q));
}

/** Abdunkelbarkeit als Bitwert: Blendschutz=1, Verdunkelung=2, beides=3, keines=0.
 *  Blendschutz zählt nur bei exaktem Wert '1' (z. B. 'fensterlos' zählt nicht). */
function tt_abdunkelbarkeit(array $t): string
{
    $b = (($t['blendschutz'] ?? '') === '1') ? 1 : 0;
    $v = (($t['verdunkelung'] ?? '') === '1') ? 2 : 0;
    return (string)($b + $v);
}

/** Digestor-/Punktabsaug-/Gefahrenschrank-Element-IDs (idTABELLE_Elemente). */
function abluft_element_ids(): array
{
    return [
        'dig' => [727, 1212, 1456, 1600, 1601, 1602, 1603, 1604, 1605, 1606,
            2056, 2057, 2058, 2059, 2060, 2061],
        'punkt' => [738, 1472, 1615],
        'schrank' => [1092, 1093, 1112, 1688, 1949],
    ];
}

/**
 * Zaehlt je Raum die verbauten Digestorien, Punktabsaugungen und Gefahrenschraenke
 * (Summe Anzahl aus tabelle_raeume_has_tabelle_elemente ueber ALLE Varianten inkl.
 * Neu+Bestand). Rueckgabe: [roomId => ['dig'=>int,'punkt'=>int,'schrank'=>int]].
 */
function element_counts_for_rooms($mysqli, array $roomIDs): array
{
    $out = [];
    $roomIDs = array_values(array_unique(array_filter(array_map('intval', $roomIDs))));
    if (!$roomIDs) return $out;

    $ids = abluft_element_ids();
    $allIDs = array_merge($ids['dig'], $ids['punkt'], $ids['schrank']);

    $phR = implode(',', array_fill(0, count($roomIDs), '?'));
    $phE = implode(',', array_fill(0, count($allIDs), '?'));
    $types = str_repeat('i', count($roomIDs) + count($allIDs));

    $stmt = $mysqli->prepare(
        "SELECT TABELLE_Räume_idTABELLE_Räume AS rid,
                TABELLE_Elemente_idTABELLE_Elemente AS eid,
                SUM(Anzahl) AS n
         FROM tabelle_räume_has_tabelle_elemente
         WHERE TABELLE_Räume_idTABELLE_Räume IN ($phR)
           AND TABELLE_Elemente_idTABELLE_Elemente IN ($phE)
         GROUP BY TABELLE_Räume_idTABELLE_Räume, TABELLE_Elemente_idTABELLE_Elemente"
    );
    $stmt->bind_param($types, ...array_merge($roomIDs, $allIDs));
    $stmt->execute();
    $res = $stmt->get_result();

    $digSet = array_flip($ids['dig']);
    $punktSet = array_flip($ids['punkt']);
    $schrankSet = array_flip($ids['schrank']);

    while ($r = $res->fetch_assoc()) {
        $rid = (int)$r['rid'];
        $eid = (int)$r['eid'];
        $n = (int)$r['n'];
        if (!isset($out[$rid])) $out[$rid] = ['dig' => 0, 'punkt' => 0, 'schrank' => 0];
        if (isset($digSet[$eid])) $out[$rid]['dig'] += $n;
        if (isset($punktSet[$eid])) $out[$rid]['punkt'] += $n;
        if (isset($schrankSet[$eid])) $out[$rid]['schrank'] += $n;
    }
    $stmt->close();
    return $out;
}

/** Element-Zählwert eines Raums (dig/punkt/schrank) als String – 0 wenn nichts verbaut. */
function ttx_cnt(array $room, string $key): string
{
    return (string)(int)($room['_cnt'][$key] ?? 0);
}

/** Fügt die Element-Zählwerte je Raum unter dem Schlüssel '_cnt' in $rooms ein. */
function inject_element_counts($mysqli, array &$rooms): void
{
    $ids = array_map(fn($r) => (int)$r['idTABELLE_Räume'], $rooms);
    $counts = element_counts_for_rooms($mysqli, $ids);
    foreach ($rooms as &$room) {
        $rid = (int)$room['idTABELLE_Räume'];
        $room['_cnt'] = $counts[$rid] ?? ['dig' => 0, 'punkt' => 0, 'schrank' => 0];
    }
    unset($room);
}


/* ═══════════════════════════════════════════════════════════════════════════
   DIE ZUORDNUNGEN  – je Eintrag = 1 Button
   'targets' : Zielspalte(n) in tabelle_räume => callable($typ, $room): ?string
   'reads'   : Klartext Quelle (nur Anzeige)
   'writes'  : Klartext Ziel   (nur Anzeige)
   'note'    : Überschreib-/Sonderhinweis (nur Anzeige)
   ═══════════════════════════════════════════════════════════════════════════ */
$GROUPS = [

    'temp' => [
        'title' => 'Temperatur Winter/Sommer',
        'reads' => 'temp_min / temp_max (bzw. temp_nach_erfordernis)',
        'writes' => '`HT_Raumtemp Winter °C`, `HT_Raumtemp Sommer °C`',
        'note' => 'Exakter Wortlaut von temp_min → Winter, temp_max → Sommer. '
            . 'Bei temp_nach_erfordernis=1 wird in BEIDE Felder „Nach Erfordernis“ geschrieben. '
            . 'Vorhandene Temperaturwerte werden überschrieben.',
        'targets' => [
            'HT_Raumtemp Winter °C' => fn($t, $r) => tt_temp($t, 'temp_min'),
            'HT_Raumtemp Sommer °C' => fn($t, $r) => tt_temp($t, 'temp_max'),
        ],
    ],

    'warmwasser' => [
        'title' => 'warmwasser',
        'reads' => 'Warmwasser (1/0)',
        'writes' => 'HT_Warmwasser',
        'note' => 'warmwasser=1 → HT_Warmwasser=1, '
            . 'Vorhandener Wert wird überschrieben.',
        'targets' => [
            'HT_Warmwasser' => fn($t, $r) => _s($t['kaltwasser'] ?? null),
        ],
    ],
    'kaltwasser' => [
        'title' => 'Kaltwasser',
        'reads' => 'Kaltwasser (1/0)',
        'writes' => 'HT_Kaltwasser',
        'note' => 'kaltwasser=1 → HT_Kaltwasser=1, '
            . 'Vorhandener Wert wird überschrieben.',
        'targets' => [
            'HT_Kaltwasser' => fn($t, $r) => _s($t['kaltwasser'] ?? null),
        ],
    ],
    'VE_Wasser' => [
        'title' => 'VE_Wasser',
        'reads' => 'VE_Wasser (1/0)',
        'writes' => 'VE_Wasser',
        'note' => 'VE_Wasser=1 → VE_Wasser=1, '
            . 'Vorhandener Wert wird überschrieben.',
        'targets' => [
            'VE_Wasser' => fn($t, $r) => _s($t['ve_wasser'] ?? null),
        ],
    ],
    'tempgradient' => [
        'title' => 'Temp-Gradient (°C/h)',
        'reads' => 'temp_schwankung  (kein echtes Gradientfeld vorhanden)',
        'writes' => 'HT_Tempgradient_Ch',
        'note' => 'ACHTUNG: In den Raumtypen gibt es KEIN Gradientfeld. Als Näherung '
            . 'wird die Zahl aus temp_schwankung (Toleranz ± X) genommen und gerundet '
            . '(Spalte ist ganzzahlig). Bitte fachlich prüfen/bestätigen.',
        'targets' => [
            'HT_Tempgradient_Ch' => fn($t, $r) => tt_tempgradient($t),
        ],
    ],

    'druck' => [
        'title' => 'Druckregelung → H6020',
        'reads' => 'druckregelung (Unter-/Überdruck, Schleuse)',
        'writes' => 'H6020 (max. 20 Zeichen, daher abgekürzt)',
        'note' => 'Abkürzung: Unterdruck→UD, Überdruck→ÜD, „mit Schleuse“→m.SL, '
            . '„ohne Schleuse“→o.SL. Ohne Druckregelung (0) wird nichts geschrieben. '
            . 'Vorhandener H6020-Wert wird überschrieben.',
        'targets' => [
            'H6020' => fn($t, $r) => tt_druck($t),
        ],
    ],

    'luftwechsel' => [
        'title' => 'Luftwechsel',
        'reads' => 'luftwechsel_rate_m3_je_m2h, sonst luftwechsel (Text)',
        'writes' => '`HT_Luftwechsel 1/h`',
        'note' => 'EINHEIT PRÜFEN: Quelle ist i.d.R. m³/m²·h, Spalte ist mit „1/h“ '
            . 'beschriftet. Ist keine Rate hinterlegt, wird der kurze Luftwechsel-Text '
            . 'übernommen (falls ≤45 Zeichen). Vorhandener Wert wird überschrieben.',
        'targets' => [
            'HT_Luftwechsel 1/h' => fn($t, $r) => tt_luftwechsel($t),
        ],
    ],

    'belichtung' => [
        'title' => 'Tageslicht → Belichtung nat.',
        'reads' => 'tagelicht_notwendig (1/0)',
        'writes' => '`AR_Belichtung-nat`',
        'note' => '0 wenn kein Tageslicht notwendig (tagelicht_notwendig≠1), sonst 1. '
            . 'Vorhandener Wert wird überschrieben.',
        'targets' => [
            'AR_Belichtung-nat' => fn($t, $r) => (($t['tagelicht_notwendig'] ?? '') === '1') ? '1' : '0',
        ],
    ],

    'abdunkelbarkeit' => [
        'title' => 'Abdunkelbarkeit (Blendschutz/Verdunkelung)',
        'reads' => 'blendschutz + verdunkelung (je 1/0)',
        'writes' => 'Abdunkelbarkeit (0/1/2/3)',
        'note' => 'Bitwert: Blendschutz=1, Verdunkelung=2, beides=3, keines=0. '
            . 'Blendschutz zählt nur bei exaktem Wert „1“ („fensterlos“ zählt NICHT). '
            . 'Vorhandener Wert wird überschrieben.',
        'targets' => [
            'Abdunkelbarkeit' => fn($t, $r) => tt_abdunkelbarkeit($t),
        ],
    ],

    'waermeabgabe' => [
        'title' => 'Wärmeabgabe (Benchmark + Raumleistung)',
        'reads' => 'waermeabgabe (W/m²)  × Nutzfläche',
        'writes' => 'HT_Waermeabgabe (W/m²), HT_Waermeabgabe_W (W/Raum)',
        'note' => 'Benchmark W/m² → HT_Waermeabgabe. Zusätzlich W/m² × Fläche → '
            . 'HT_Waermeabgabe_W (gerundet). Fläche = Nutzfläche, sonst Nutzfläche_Soll. '
            . 'Ohne Fläche wird HT_Waermeabgabe_W nicht berechnet. Werte werden überschrieben.',
        'targets' => [
            'HT_Waermeabgabe' => fn($t, $r) => _s($t['waermeabgabe'] ?? null),
            'HT_Waermeabgabe_W' => fn($t, $r) => tt_mult($t, $r, 'waermeabgabe'),
        ],
    ],

    'anschlussleistung' => [
        'title' => 'Anschlussleistung (Benchmark + Raumleistung)',
        'reads' => 'anschlussleistung (W/m²)  × Nutzfläche',
        'writes' => 'EL_Leistungsbedarf_W_pro_m2 (W/m²), ET_Anschlussleistung_W (W/Raum)',
        'note' => 'Benchmark W/m² → EL_Leistungsbedarf_W_pro_m2. Zusätzlich W/m² × Fläche → '
            . 'ET_Anschlussleistung_W (gerundet). Fläche = Nutzfläche, sonst Nutzfläche_Soll. '
            . 'Ohne Fläche wird ET_Anschlussleistung_W nicht berechnet. Werte werden überschrieben.',
        'targets' => [
            'EL_Leistungsbedarf_W_pro_m2' => fn($t, $r) => _s($t['anschlussleistung'] ?? null),
            'ET_Anschlussleistung_W' => fn($t, $r) => tt_mult($t, $r, 'anschlussleistung'),
        ],
    ],

    'av_sv_leistung' => [
        'title' => 'AV/SV-Leistung + Parameter',
        'reads' => 'anschlussleistung (W/m²) × Nutzfläche × AV_quotient',
        'writes' => 'ET_Anschlussleistung_AV_W, ET_Anschlussleistung_SV_W, AV, SV',
        'note' => 'AV_quotient = Anteil Allgemeinversorgung (AV). '
            . 'AV-Leistung = anschlussleistung × Fläche × AV_quotient → ET_Anschlussleistung_AV_W. '
            . 'SV-Leistung (Rest) = anschlussleistung × Fläche × (1 − AV_quotient) → ET_Anschlussleistung_SV_W. '
            . 'Zusätzlich Raumparameter: AV=1 wenn AV-Leistung > 0, SV=1 wenn SV-Leistung > 0 '
            . '(bei Leistung 0 oder fehlender Fläche bleibt das jeweilige Flag unverändert). '
            . 'Fläche = Nutzfläche, sonst Nutzfläche_Soll. Leistungswerte werden überschrieben.',
        'targets' => [
            'ET_Anschlussleistung_AV_W' => fn($t, $r) => tt_av_leistung($t, $r),
            'ET_Anschlussleistung_SV_W' => fn($t, $r) => tt_sv_leistung($t, $r),
            'AV' => function ($t, $r) {
                $v = tt_av_leistung($t, $r);
                return ($v !== null && (int)$v > 0) ? '1' : null;
            },
            'SV' => function ($t, $r) {
                $v = tt_sv_leistung($t, $r);
                return ($v !== null && (int)$v > 0) ? '1' : null;
            },
        ],
    ],

    'notdusche' => [
        'title' => 'Notdusche',
        'reads' => 'sicherheit_notdusche (1/0)',
        'writes' => 'HT_Notdusche (Anzahl Stk)',
        'note' => '1 = eine Notdusche, 0 = keine. Vorhandener Wert wird überschrieben.',
        'targets' => [
            'HT_Notdusche' => fn($t, $r) => _s($t['sicherheit_notdusche'] ?? null),
        ],
    ],

    'abluft_geraete' => [
        'title' => 'Abluft nach Geräteanzahl',
        'reads' => 'Anzahl Digestorien / Punktabsaugungen / Gefahrenschränke im Raum (Elemente)',
        'writes' => 'HT_Abluft_Digestorium_Stk, HT_Abluft_Sicherheitsschrank_Unterbau_Stk, HT_Punktabsaugung_Stk, HT_Abluft_Sicherheitsschrank_Stk',
        'note' => 'Zählt die im Raum verbauten Geräte (Summe Anzahl über alle Varianten inkl. Neu+Bestand). '
            . 'Digestorien → HT_Abluft_Digestorium_Stk (1 je Digestor) UND HT_Abluft_Sicherheitsschrank_Unterbau_Stk (2 je Digestor). '
            . 'Punktabsaugungen → HT_Punktabsaugung_Stk (1 je Stk). '
            . 'Gefahrenschränke → HT_Abluft_Sicherheitsschrank_Stk (1 je Stk). '
            . 'ACHTUNG: Es wird die EXAKTE Anzahl gesetzt – bei 0 Geräten wird auf 0 gesetzt (überschreibt vorhandene Werte).',
        'needs_counts' => true,
        'targets' => [
            'HT_Abluft_Digestorium_Stk' => fn($t, $r) => ttx_cnt($r, 'dig'),
            'HT_Abluft_Sicherheitsschrank_Unterbau_Stk' => fn($t, $r) => (string)(2 * (int)ttx_cnt($r, 'dig')),
            'HT_Punktabsaugung_Stk' => fn($t, $r) => ttx_cnt($r, 'punkt'),
            'HT_Abluft_Sicherheitsschrank_Stk' => fn($t, $r) => ttx_cnt($r, 'schrank'),
        ],
    ],
    'decke' => [
        'title' => 'Decke (Deckenart)',
        'reads' => 'decke (Offen/Geschlossen)',
        'writes' => 'Decke',
        'note' => '1:1-Übernahme des Decken-Textes. Vorhandener Wert wird überschrieben.',
        'targets' => [
            'Decke' => fn($t, $r) => _s($t['decke'] ?? null),
        ],
    ],
    'elektro_edv' => [
        'title' => 'IT Anschluss',
        'reads' => 'elektro_edv (1/0)',
        'writes' => 'IT Anbindung',
        'note' => '1:1-Übernahme des EDV-Textes. Vorhandener Wert wird überschrieben.',
        'targets' => [
             'IT Anbindung' => fn($t, $r) => _s($t['elektro_edv'] ?? null),
        ],
    ],
    'av_quotient_volumen' => [
        'title' => 'AV-Quotient → Volumen',
        'reads' => 'AV_quotient (0..1)',
        'writes' => 'Belichtungsfläche',
        'note' => 'ACHTUNG: Der AV-Quotient (Anteil Allgemeinversorgung, 0..1) wird 1:1 in '
            . 'die Spalte „Belichtungsfläche“ geschrieben – ein vorhandener Belichtungsfläche wird überschrieben. ',
        'targets' => [
            'Volumen' => fn($t, $r) => _s($t['AV_quotient'] ?? null),
        ],
    ],

    'notruf_aufenthaltsraum' => [
        'title' => 'Notruf → Aufenthaltsraum',
        'reads' => 'sicherheit_notruf (1/0)',
        'writes' => 'Aufenthaltsraum',
        'note' => 'sicherheit_notruf wird 1:1 nach „Aufenthaltsraum“ übernommen '
            . '(1 = ja, 0 = nein). Vorhandener Wert wird überschrieben.',
        'targets' => [
            'Aufenthaltsraum' => fn($t, $r) => _s($t['sicherheit_notruf'] ?? null),
        ],
    ],

    'erstehilfe_nga' => [
        'title' => 'Erste Hilfe → NGA',
        'reads' => 'sicherheit_erstehilfe (1/0)',
        'writes' => 'NGA',
        'note' => 'sicherheit_erstehilfe wird 1:1 nach „NGA“ übernommen '
            . '(1 = ja, 0 = nein). Vorhandener Wert wird überschrieben.',
        'targets' => [
            'NGA' => fn($t, $r) => _s($t['sicherheit_erstehilfe'] ?? null),
        ],
    ],
];

/** Alle Zielspalten (Union) – für SELECT + Whitelist. */
$ALL_TARGET_COLS = [];
foreach ($GROUPS as $g) {
    foreach (array_keys($g['targets']) as $c) $ALL_TARGET_COLS[$c] = true;
}
$ALL_TARGET_COLS = array_keys($ALL_TARGET_COLS);


/* ═══════════════════════════════════════════════════════════════════════════
   POST  action=apply  ->  JSON  (im selben File)
   ═══════════════════════════════════════════════════════════════════════════ */
if (($_POST['action'] ?? '') === 'apply') {
    ob_start();
    check_login();
    header('Content-Type: application/json');

    $projectID = (int)($_POST['projectID'] ?? $_SESSION['projectID'] ?? 0);
    $groupKey = (string)($_POST['group'] ?? '');
    $roomIDs = array_values(array_filter(array_map('intval', (array)($_POST['roomIDs'] ?? []))));

    if (!$projectID || !isset($GROUPS[$groupKey]) || !$roomIDs) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter (Projekt, Zuordnung oder Räume).']);
        exit;
    }

    $group = $GROUPS[$groupKey];
    $targets = $group['targets'];
    $idx = raumtyp_index();
    $mysqli = utils_connect_sql();

    $ph = implode(',', array_fill(0, count($roomIDs), '?'));
    $types = 'i' . str_repeat('i', count($roomIDs));
    $stmt = $mysqli->prepare("
        SELECT idTABELLE_Räume, `Raumtyp BH`, `Nutzfläche`, `Nutzfläche_Soll`
        FROM tabelle_räume
        WHERE tabelle_projekte_idTABELLE_Projekte = ?
          AND idTABELLE_Räume IN ($ph)
    ");
    $stmt->bind_param($types, $projectID, ...$roomIDs);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Element-Zählwerte (Digestor/Punktabs./Gefahrenschrank) nur laden, wenn benötigt.
    if (!empty($group['needs_counts'])) inject_element_counts($mysqli, $rows);

    $updated = 0;
    $noop = 0;
    $skipped = 0;

    foreach ($rows as $room) {
        $rid = (int)$room['idTABELLE_Räume'];
        $typ = $idx[(string)$room['Raumtyp BH']] ?? null;
        if (!$typ) {
            $skipped++;
            continue;
        }

        $set = [];
        $vals = [];
        $bt = '';
        foreach ($targets as $col => $cb) {
            $val = $cb($typ, $room);
            if ($val === null) continue;            // keine Angabe -> nicht überschreiben
            $set[] = '`' . $col . '` = ?';          // Spalte aus Whitelist
            $vals[] = (string)$val;
            $bt .= 's';
        }
        if (!$set) {
            $skipped++;
            continue;
        }

        $sql = "UPDATE tabelle_räume SET " . implode(', ', $set)
            . " WHERE idTABELLE_Räume = ? AND tabelle_projekte_idTABELLE_Projekte = ?";
        $bt .= 'ii';
        $vals[] = $rid;
        $vals[] = $projectID;

        $u = $mysqli->prepare($sql);
        $u->bind_param($bt, ...$vals);
        if ($u->execute()) {
            $u->affected_rows > 0 ? $updated++ : $noop++;
        }
        $u->close();
    }

    $mysqli->close();
    ob_end_clean();
    echo json_encode([
        'status' => 'ok',
        'group' => $groupKey,
        'updated' => $updated,
        'noop' => $noop,
        'skipped' => $skipped,
        'msg' => "„{$group['title']}“: $updated aktualisiert"
            . ($noop ? ", $noop unverändert" : '')
            . ($skipped ? ", $skipped übersprungen" : '') . '.',
    ]);
    exit;
}


/* ═══════════════════════════════════════════════════════════════════════════
   GET  ->  Seite rendern
   ═══════════════════════════════════════════════════════════════════════════ */
init_page_serversides();

$mysqli = utils_connect_sql();
$projectID = (int)($_GET['projectID'] ?? $_SESSION['projectID'] ?? 0);

$colSql = implode(', ', array_map(fn($c) => '`' . $c . '`', $ALL_TARGET_COLS));
$rooms = [];
if ($projectID) {
    $sql = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung, `Raumbereich Nutzer`,
                   `Raumtyp BH`, `Nutzfläche`, `Nutzfläche_Soll`, $colSql
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ?
              AND `Raumtyp BH` IS NOT NULL AND `Raumtyp BH` <> ''
            ORDER BY Raumnr";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $projectID);
    $stmt->execute();
    $rooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Element-Zählwerte je Raum für die Vorschau der element-basierten Zuordnung.
    inject_element_counts($mysqli, $rooms);
}
$mysqli->close();

$idx = raumtyp_index();

// Vorschauwerte vorberechnen: $preview[roomId][groupKey] = [ ['col','cur','new','chg'], ... ]
$preview = [];
$changesPerGrp = array_fill_keys(array_keys($GROUPS), 0);
$countUnknown = 0;
foreach ($rooms as $room) {
    $rid = (int)$room['idTABELLE_Räume'];
    $typ = $idx[(string)$room['Raumtyp BH']] ?? null;
    if (!$typ) $countUnknown++;
    foreach ($GROUPS as $gk => $g) {
        $cells = [];
        foreach ($g['targets'] as $col => $cb) {
            $cur = (string)($room[$col] ?? '');
            $new = $typ ? $cb($typ, $room) : null;
            $chg = ($new !== null && (string)$new !== $cur);
            if ($chg) $changesPerGrp[$gk]++;
            $cells[] = ['col' => $col, 'cur' => $cur, 'new' => $new, 'chg' => $chg];
        }
        $preview[$rid][$gk] = $cells;
    }
}

// Kompakte Gruppen-Meta für JS-Confirm
$groupMetaJs = [];
foreach ($GROUPS as $gk => $g) {
    $groupMetaJs[$gk] = [
        'title' => $g['title'],
        'reads' => $g['reads'],
        'writes' => $g['writes'],
        'note' => $g['note'],
    ];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Raumtypen → Räume übernehmen</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body>
<div class="container-fluid bg-light py-3">
    <div id="limet-navbar"></div>

    <div class="card mt-1">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <b><i class="fas fa-exchange-alt me-1"></i> Raumtypen-Angaben in Tabelle Räume übernehmen</b>
            <form class="d-flex align-items-center gap-2" method="get">
                <label class="text-muted small mb-0">Projekt-ID</label>
                <input type="number" name="projectID" value="<?= $projectID ?>"
                       class="form-control form-control-sm" style="max-width:110px;">
                <button class="btn btn-sm btn-outline-dark" type="submit">
                    <i class="fas fa-sync-alt me-1"></i> Laden
                </button>
            </form>
        </div>

        <div class="card-body">

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-secondary">Räume mit Raumtyp: <?= count($rooms) ?></span>
                <?php if ($countUnknown): ?>
                    <span class="badge bg-danger">Unbekannter Raumtyp: <?= $countUnknown ?></span>
                <?php endif; ?>
            </div>

            <!-- Was-passiert-Erörterung -->
            <div class="accordion mb-3" id="mapDocAcc">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#mapDocBody">
                            <i class="fas fa-info-circle me-2"></i> Was wird von wo nach wo gespeichert? (bitte lesen)
                        </button>
                    </h2>
                    <div id="mapDocBody" class="accordion-collapse collapse" data-bs-parent="#mapDocAcc">
                        <div class="accordion-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Zuordnung</th>
                                        <th>Liest (Raumtyp)</th>
                                        <th>Schreibt (Räume)</th>
                                        <th>Änd.</th>
                                        <th>Hinweis / Überschreiben</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($GROUPS as $gk => $g): ?>
                                        <tr>
                                            <td class="fw-semibold text-nowrap"><?= htmlspecialchars($g['title']) ?></td>
                                            <td><code class="small"><?= htmlspecialchars($g['reads']) ?></code></td>
                                            <td><code class="small"><?= htmlspecialchars($g['writes']) ?></code></td>
                                            <td>
                                                <span class="badge <?= $changesPerGrp[$gk] ? 'bg-warning text-dark' : 'bg-light text-muted border' ?>">
                                                    <?= $changesPerGrp[$gk] ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted"><?= htmlspecialchars($g['note']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$projectID): ?>
                <p class="text-muted fst-italic">Bitte eine Projekt-ID angeben.</p>
            <?php elseif (empty($rooms)): ?>
                <p class="text-muted fst-italic">
                    Keine Räume mit gesetztem Feld <code>Raumtyp BH</code> in Projekt <?= $projectID ?> gefunden.
                </p>
            <?php else: ?>

                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                    <input type="text" id="roomFilter" class="form-control form-control-sm"
                           style="max-width:240px;" placeholder="Raum filtern (Nr / Bezeichnung)…">
                    <span class="text-muted small" id="selCount"></span>
                    <span class="ms-auto text-muted small">
                        <i class="fas fa-hand-pointer me-1"></i>
                        Jeder Spalten-Button überträgt nur diese eine Zuordnung – für die
                        <b>angehakten</b> Räume.
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle" id="tbl" style="min-width:1100px;">
                        <thead class="table-light">
                        <tr>
                            <th style="width:34px;"><input type="checkbox" class="form-check-input" id="selAll" checked>
                            </th>
                            <th class="text-nowrap">Raumnr</th>
                            <th>Bezeichnung</th>
                            <th class="text-nowrap">Raumtyp BH</th>
                            <th class="text-nowrap">Fläche m²</th>
                            <?php foreach ($GROUPS as $gk => $g): ?>
                                <th class="text-nowrap" style="min-width:150px;">
                                    <div class="d-flex flex-column gap-1">
                                        <span><?= htmlspecialchars($g['title']) ?></span>
                                        <button type="button"
                                                class="btn btn-primary btn-sm apply-group-btn"
                                                data-group="<?= htmlspecialchars($gk) ?>"
                                                title="<?= htmlspecialchars($g['note']) ?>">
                                            <i class="fas fa-save me-1"></i> Übernehmen
                                        </button>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rooms as $room):
                            $rid = (int)$room['idTABELLE_Räume'];
                            $typId = (string)$room['Raumtyp BH'];
                            $typ = $idx[$typId] ?? null;
                            $area = room_area($room);
                            ?>
                            <tr class="room-row" data-room-id="<?= $rid ?>"
                                data-search="<?= htmlspecialchars(strtolower($room['Raumnr'] . ' ' . $room['Raumbezeichnung'])) ?>">
                                <td>
                                    <input type="checkbox" class="form-check-input row-cb" value="<?= $rid ?>"
                                        <?= $typ ? 'checked' : 'disabled' ?>>
                                </td>
                                <td class="text-nowrap"><?= htmlspecialchars($room['Raumnr']) ?></td>
                                <td>
                                    <?= htmlspecialchars($room['Raumbezeichnung']) ?>
                                    <?php if (!empty($room['Raumbereich Nutzer'])): ?>
                                        <span class="text-muted small d-block"><?= htmlspecialchars($room['Raumbereich Nutzer']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <?php if ($typ): ?>
                                        <span class="badge bg-secondary">#<?= htmlspecialchars($typId) ?></span>
                                        <span class="small d-block text-muted" style="max-width:180px;">
                                            <?= htmlspecialchars($typ['bezeichnung'] ?? '') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">#<?= htmlspecialchars($typId) ?> unbekannt</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap small">
                                    <?= $area !== null
                                        ? htmlspecialchars(rtrim(rtrim(number_format($area, 2, ',', ''), '0'), ','))
                                        : '<span class="text-danger">—</span>' ?>
                                </td>

                                <?php foreach ($GROUPS as $gk => $g): ?>
                                    <td>
                                        <?php foreach ($preview[$rid][$gk] as $c): ?>
                                            <div class="small <?= $c['chg'] ? '' : 'text-muted' ?>">
                                                <?php if (count($g['targets']) > 1): ?>
                                                    <span class="text-muted"
                                                          style="font-size:.7rem;"><?= htmlspecialchars($c['col']) ?>:</span>
                                                <?php endif; ?>
                                                <?php if ($c['new'] === null): ?>
                                                    <span class="fst-italic text-muted">—</span>
                                                <?php elseif ($c['chg']): ?>
                                                    <span class="text-decoration-line-through text-muted"><?= $c['cur'] === '' ? '∅' : htmlspecialchars($c['cur']) ?></span>
                                                    <i class="fas fa-arrow-right mx-1" style="font-size:.65rem;"></i>
                                                    <strong><?= htmlspecialchars($c['new']) ?></strong>
                                                <?php else: ?>
                                                    <?= $c['cur'] === '' ? '<span class="fst-italic">∅</span>' : htmlspecialchars($c['cur']) ?>
                                                    <i class="fas fa-check text-success ms-1" style="font-size:.65rem;"
                                                       title="bereits gleich"></i>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    const GROUP_META = <?= json_encode($groupMetaJs, JSON_UNESCAPED_UNICODE) ?>;
    const PROJECT_ID = <?= (int)$projectID ?>;

    $(function () {
        function applyFilter() {
            const q = ($('#roomFilter').val() || '').toLowerCase().trim();
            $('#tbl tbody tr.room-row').each(function () {
                $(this).toggle(!q || (String($(this).data('search')).includes(q)));
            });
        }

        $('#roomFilter').on('input', applyFilter);

        $('#selAll').on('change', function () {
            const on = this.checked;
            $('#tbl tbody tr.room-row:visible .row-cb:not(:disabled)').prop('checked', on);
            updateSel();
        });
        $(document).on('change', '.row-cb', updateSel);

        function updateSel() {
            $('#selCount').text($('.row-cb:checked').length + ' Raum/Räume ausgewählt');
        }

        updateSel();

        $('.apply-group-btn').on('click', function () {
            const gk = $(this).data('group');
            const meta = GROUP_META[gk] || {};
            const roomIDs = $('.row-cb:checked').map(function () {
                return this.value;
            }).get();
            if (!roomIDs.length) {
                makeToaster('Keine Räume ausgewählt.', false);
                return;
            }

            const msg = 'Zuordnung: ' + meta.title + '\n\n'
                + 'LIEST (Raumtyp):  ' + meta.reads + '\n'
                + 'SCHREIBT (Räume): ' + meta.writes + '\n\n'
                + meta.note + '\n\n'
                + 'Für ' + roomIDs.length + ' ausgewählte(n) Raum/Räume übernehmen?';
            if (!confirm(msg)) return;

            const btn = this;
            const html = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            $.ajax({
                url: 'RaumtypenZuRaumbuch.php', type: 'POST',
                data: {action: 'apply', group: gk, projectID: PROJECT_ID, roomIDs: roomIDs},
                success: function (raw) {
                    let res;
                    try {
                        res = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    } catch (e) {
                        res = {status: 'error', msg: String(raw)};
                    }
                    if (res.status === 'ok') {
                        makeToaster(res.msg || 'Übernommen.', true);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        makeToaster('Fehler: ' + (res.msg || ''), false);
                        btn.disabled = false;
                        btn.innerHTML = html;
                    }
                },
                error: function () {
                    makeToaster('Verbindungsfehler.', false);
                    btn.disabled = false;
                    btn.innerHTML = html;
                }
            });
        });
    });
</script>
</body>
</html>