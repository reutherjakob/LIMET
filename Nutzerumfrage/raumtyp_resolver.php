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


    if (($raumtyp['n2'] ?? '0') === '1') $hidden[] = 'N2';
    if (($raumtyp['dl'] ?? '0') === '1') $hidden[] = 'DL';


    if (($raumtyp['temp_nicht_erfragen'] ?? '0') === '1') $hidden[] = 'raumtemp';
    if (($raumtyp['temp_nicht_erfragen'] ?? '0') === '1') $hidden[] = 'luftf';

    // Sondergase ausblenden, wenn Raumtyp keine Sondergase benötigt
    if (($raumtyp['sondergase'] ?? '') === 'Keine') {
        $hidden[] = 'spezialgas';
    }

    $lagerIds = ['26', '27', '28', '29', '30', '31'];
    if (in_array((string)($raumtyp['id'] ?? ''), $lagerIds)) {
        $hidden[] = 'abluftwaescher';
        $hidden[] = 'N2';
        $hidden[] = 'DL';
        $hidden[] = 'Vakuum';
    }

    $archivIds = ['32', '33'];
    if (in_array((string)($raumtyp['id'] ?? ''), $archivIds)) {
        $hidden[] = 'abluftwaescher';
        $hidden[] = 'N2';
        $hidden[] = 'DL';
        $hidden[] = 'Vakuum';
    }

    $bueroIds = ['34', '35'];
    if (in_array((string)($raumtyp['id'] ?? ''), $bueroIds)) {
        $hidden[] = 'abluftwaescher';
        $hidden[] = 'N2';
        $hidden[] = 'DL';
        $hidden[] = 'Vakuum';
    }

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
                $field['options']['Iod'] = 'Iod Filter';
            }
        }

        if (($field['name'] ?? '') === 'spezialgas') {
            if (($raumtyp['id'] ?? '') === '21') {
                $field['options']['Fluess_N2'] = 'Flüssiger Stickstoff';
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