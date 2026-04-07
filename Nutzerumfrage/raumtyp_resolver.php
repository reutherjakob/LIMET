<?php
// =============================================================================
// raumtyp_resolver.php
// =============================================================================


function getRaumtypById(array $labortypen, ?string $id): ?array
{// Hilfsfunktion: Raumtyp-Datensatz per ID aus $labortypen holen
    if (!$id) return null;
    foreach ($labortypen as $rt) {
        if ((string)$rt['id'] === (string)$id) return $rt;
    }
    return null;
}


// -----------------------------------------------------------------------------
// Kern: Aus Raumtyp-Feldern ableiten was das Formular tun soll
//
// Rückgabe:
//   'hidden'   => [...feldnamen]   Felder die ausgeblendet werden (als hidden-Input
//                                  mit default_value gespeichert → DB vollständig)
//   'defaults' => [name => wert]   Vorausgefüllte Werte aus dem Raumtyp
//   'freetext' => [...feldnamen]   Felder die von select auf Freitext umgestellt werden
// -----------------------------------------------------------------------------
function resolveFieldOverrides(array $raumtyp, string $bauabschnitt = '', string $ebene = ''): array
{
    $hidden = [];
    $defaults = [];
    $freetext = [];


    $zeigeTueren = ($bauabschnitt === 'A') || ($ebene === 'UG');
    if (!$zeigeTueren) {
        $hidden[] = 'doppelfluegeltuer';
    }


    // // -------------------------------------------------------------------------
    // // GAS: N2 und DL zentral → nur abfragen wo es NICHT Standard ist
    // // n2=1 / dl=1 = zentrale Versorgung bereits vorgesehen → nicht nochmal fragen
    // // -------------------------------------------------------------------------
    // if (($raumtyp['n2'] ?? '0') === '1') $hidden[] = 'N2';
    // if (($raumtyp['dl'] ?? '0') === '1') $hidden[] = 'DL';


    if (($raumtyp['kaltwasser'] ?? '1') === '0') $hidden[] = 'kaltwasser_stundenverbrauch';
    if (($raumtyp['kaltwasser'] ?? '1') === '0') $hidden[] = 'kaltwasser_spitzenverbrauch';

// -------------------------------------------------------------------------
// ZULUFT-FILTER: Iodfilter-Option nur bei Messraum radiochemisch einblenden
// -------------------------------------------------------------------------
    $filter = $raumtyp['luftwechsel_filter'] ?? '0';
    if (stripos($filter, 'Iod') !== false) {
        $defaults['raumzuluft_besonders'] = 'Iod';
    }
    // // -------------------------------------------------------------------------
    // // BSL-Level: Default direkt aus bsl2 / bsl3
    // // -------------------------------------------------------------------------
    // if (($raumtyp['bsl3'] ?? '0') === '1') $defaults['bsl_level'] = 'BSL3';
    // elseif (($raumtyp['bsl2'] ?? '0') === '1') $defaults['bsl_level'] = 'BSL2';
    // else                                        $defaults['bsl_level'] = '0';

    // // -------------------------------------------------------------------------
    // // TEMPERATUR: Feste Werte als Default, oder Freitext wenn "nach Erfordernis"
    // // temp_nach_erfordernis=1 → Klimakammer o.ä. → kein fester Bereich
    // // -------------------------------------------------------------------------
    // if (($raumtyp['temp_nach_erfordernis'] ?? '0') === '1') {
    //     // Klimakammer: Freitext statt Select, kein vorgegebener Default
    //     $freetext[] = 'temperatur_min';
    //     $freetext[] = 'temperatur_max';
    // } else {
    //     // Normallabor: Raumtyp-Wert als vorausgewählter Default
    //     if (!empty($raumtyp['temp_min'])) $defaults['temperatur_min'] = $raumtyp['temp_min'];
    //     if (!empty($raumtyp['temp_max'])) $defaults['temperatur_max'] = $raumtyp['temp_max'];
    // }

    // // Temperaturschwankung als Default
    // if (!empty($raumtyp['temp_schwankung'])) {
    //     $defaults['temperatur_gradient'] = $raumtyp['temp_schwankung'];
    // }

    // // -------------------------------------------------------------------------
    // // LICHTSTEUERUNG: Nur relevant bei Klimakammer (temp_nach_erfordernis=1)
    // // Bei allen anderen Raumtypen ausblenden
    // // -------------------------------------------------------------------------
    // if (($raumtyp['temp_nach_erfordernis'] ?? '0') !== '1') {
    //     $hidden[] = 'lichtsteuerung';
    // }

    // // -------------------------------------------------------------------------
    // // VERDUNKELUNG: Default aus Raumtyp
    // // -------------------------------------------------------------------------
    // $defaults['verdunkelung'] = (($raumtyp['verdunkelung'] ?? '0') === '1') ? 'Ja' : 'Nein';

    // // -------------------------------------------------------------------------
    // // LUFTFEUCHTIGKEIT: Defaults aus Raumtyp
    // // -------------------------------------------------------------------------
    // if (!empty($raumtyp['luftfeuchtigkeit'])) {
    //     $defaults['luftfeuchtigkeit_besonders'] = $raumtyp['luftfeuchtigkeit'];
    // }
    // if (!empty($raumtyp['luftfeuchtigkeit_schwankungstoleranz'])) {
    //     $defaults['luftfeuchtigkeit_enge_toleranz'] = $raumtyp['luftfeuchtigkeit_schwankungstoleranz'];
    // }

    // // -------------------------------------------------------------------------
    // // WASSER: Ausblenden wenn im Raumtyp nicht vorgesehen
    // // kaltwasser=0, warmwasser=0, ve_wasser=0 → Fragen nicht stellen
    // // -------------------------------------------------------------------------
    // if (($raumtyp['kaltwasser'] ?? '0') === '0') $hidden[] = 'kaltwasser';
    // if (($raumtyp['warmwasser'] ?? '0') === '0') $hidden[] = 'warmwasser_erhoehter_bedarf';
    // if (($raumtyp['ve_wasser'] ?? '0') === '0') $hidden[] = 'VE_Wasser';

    // // -------------------------------------------------------------------------
    // // ABLUFTWÄSCHER: Nur fragen wenn Abzüge mit Wäscher im Raumtyp vorgesehen
    // // -------------------------------------------------------------------------
    // $hatAbzuege = (int)($raumtyp['abzuege_anzahl_max'] ?? 0) > 0
    //     || ($raumtyp['abzuege_abluftwaescher'] ?? '0') === '1';
    // if (!$hatAbzuege) {
    //     $hidden[] = 'abluftwaescher';
    // }

    // // -------------------------------------------------------------------------
    // // SONDERABLUFT: Default aus Raumtyp
    // // -------------------------------------------------------------------------
    // $sonderabluft = $raumtyp['sonderabluft'] ?? '0';
    // if (empty($sonderabluft) || $sonderabluft === '0') {
    //     $defaults['sonderabluft'] = 'Nein';
    // } else {
    //     // Raumtyp hat Sonderabluft → "Sonstige" als Planungsannahme,
    //     // User sieht das Feld und kann präzisieren
    //     $defaults['sonderabluft'] = 'Sonstige';
    // }

    // // -------------------------------------------------------------------------
    // // RAUMABLUFT / HEPA-Filter: aus luftwechsel_filter ableiten
    // // -------------------------------------------------------------------------
    // $filter = $raumtyp['luftwechsel_filter'] ?? '0';
    // if (empty($filter) || $filter === '0') {
    //     $defaults['raumabluft_besonders'] = 'Nein';
    // } elseif (stripos($filter, 'HEPA') !== false) {
    //     // Nur wenn HEPA im Wert vorkommt → Typ A als Planungsannahme
    //     // User sieht das Feld und kann auf B oder C anpassen
    //     $defaults['raumabluft_besonders'] = 'HEPA_A';
    // } else {
    //     // z.B. "Zuluft Iod" beim radiochemischen Messraum →
    //     // kein HEPA, also Standard "Nein", Filterung ist Zuluft-seitig
    //     $defaults['raumabluft_besonders'] = 'Nein';
    // }


    // if (stripos($raumtyp['sondergase'] ?? '', 'flüssiger Stickstoff') !== false) {
    //     $defaults['stickstoff_gas'] = 'Ja';
    // }

    return [
        'hidden' => $hidden,
        'defaults' => $defaults,
        'freetext' => $freetext,
    ];
}


// -----------------------------------------------------------------------------
// Overrides auf $formFields anwenden
//
// Wichtig: hidden-Felder bleiben als 'texthidden' im DOM mit ihrem default_value.
// So landen sie immer im Submit → DB ist vollständig befüllt, auch wenn der
// User sie nie gesehen hat.
// Beim Laden: gespeicherter DB-Wert hat Vorrang vor default_value (renderForm-Logik).
// -----------------------------------------------------------------------------
function applyRaumtypOverrides(array $formFields, ?array $raumtyp, string $bauabschnitt = '', string $ebene = ''): array
{

    // Türen-Logik läuft IMMER, unabhängig vom Raumtyp
    $overrides = resolveFieldOverrides($raumtyp ?? [], $bauabschnitt, $ebene);

    $hidden = $overrides['hidden'];
    $defaults = $overrides['defaults'];
    $freetext = $overrides['freetext'];


    if (!$raumtyp) {
        // Nur hidden anwenden (Türen), den Rest überspringen
        foreach ($formFields as &$field) {
            $name = $field['name'] ?? null;
            if (!$name) continue;
            if (in_array($name, $hidden)) {
                $field['_type_original'] = $field['type'];
                $field['type'] = 'texthidden';
            }
        }
        unset($field);
        return $formFields;
    }

    foreach ($formFields as &$field) {
        $name = $field['name'] ?? null;
        if (!$name) continue;

        // 1. Default zuerst setzen — damit hidden-Input den richtigen Wert hat
        if (isset($defaults[$name])) {
            $field['default_value'] = $defaults[$name];
        }


        // Iodfilter-Option dynamisch hinzufügen wenn Raumtyp es erfordert
        if (($field['name'] ?? '') === 'raumzuluft_besonders') {
            $filter = $raumtyp['luftwechsel_filter'] ?? '0';
            if (stripos($filter, 'Iod') !== false) {
                $field['options']['Iod'] = 'Iodfilter';
            }
        }


        // 2. Ausblenden → texthidden, Wert bleibt erhalten für DB
        if (in_array($name, $hidden)) {
            $field['_type_original'] = $field['type'];
            $field['type'] = 'texthidden';
        }

        // 3. Select → Freitext umschalten (z.B. Temperatur bei Klimakammer)
        if (in_array($name, $freetext) && isset($field['options'])) {
            $field['_type_original'] = $field['type'];
            $field['type'] = 'text';
            unset($field['options']);
        }
    }
    unset($field);

    return $formFields;
}