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


    if (($raumtyp['n2'] ?? '0') === '1') $hidden[] = 'N2';
    if (($raumtyp['dl'] ?? '0') === '1') $hidden[] = 'DL';


    if (($raumtyp['temp_nicht_erfragen'] ?? '0') === '1') $hidden[] = 'raumtemp';
    if (($raumtyp['temp_nicht_erfragen'] ?? '0') === '1') $hidden[] = 'luftf';

    // Sondergase ausblenden, wenn Raumtyp keine Sondergase benötigt
    if (($raumtyp['sondergase'] ?? '') === 'Keine') {
        $hidden[] = 'spezialgas';
    }

    $hiddenByRaumtyp = [
        '1'  => ['abluftwaescher', 'raumabluft_besonders'],
        '12' => ['abluftwaescher', 'kuehlwasser'],
        '13' => ['vibrationsempfindlich_bodenstehend', 'explosionsschutz', 'abluftwaescher', 'kuehlwasser', 'raumzuluft_besonders'],
        '16' => ['abluftwaescher', 'N2', 'DL', 'Vakuum', 'vibrationsempfindlich_bodenstehend', 'raumzuluft_besonders', 'raumabluft_besonders', 'kuehlwasser', 'spezialabwasser', 'nutzwasser', 'explosionsschutz'],
        '22' => ['vibrationsempfindlich_bodenstehend', 'explosionsschutz', 'abluftwaescher', 'raumzuluft_besonders', 'raumabluft_besonders', 'kuehlwasser'],
        '23' => ['abluftwaescher', 'explosionsschutz', 'nutzwasser', 'kuehlwasser', 'spezialabwasser', 'vibrationsempfindlich_bodenstehend'],
        '24' => ['abluftwaescher', 'explosionsschutz', 'nutzwasser', 'spezialabwasser', 'raumzuluft_besonders', 'raumabluft_besonders', 'vibrationsempfindlich_bodenstehend'],
        '25' => ['vibrationsempfindlich_bodenstehend', 'explosionsschutz', 'abluftwaescher', 'raumzuluft_besonders', 'raumabluft_besonders', 'kuehlwasser', 'spezialabwasser', 'nutzwasser', 'luftf', 'raumtemp'],
        '26' => ['abluftwaescher', 'vibrationsempfindlich_bodenstehend', 'raumzuluft_besonders', 'raumabluft_besonders', 'spezialabwasser', 'nutzwasser', 'explosionsschutz'],
        '27' => ['abluftwaescher', 'explosionsschutz', 'vibrationsempfindlich_bodenstehend', 'raumzuluft_besonders', 'raumabluft_besonders', 'spezialabwasser', 'nutzwasser'],
        '28' => ['abluftwaescher', 'explosionsschutz', 'vibrationsempfindlich_bodenstehend', 'raumzuluft_besonders', 'raumabluft_besonders', 'spezialabwasser', 'nutzwasser'],
        '29' => ['abluftwaescher', 'N2', 'DL', 'Vakuum', 'vibrationsempfindlich_bodenstehend', 'raumzuluft_besonders', 'kuehlwasser', 'spezialabwasser', 'nutzwasser'],
        '30' => ['abluftwaescher', 'N2', 'DL', 'Vakuum', 'vibrationsempfindlich_bodenstehend', 'raumzuluft_besonders', 'raumabluft_besonders', 'kuehlwasser', 'spezialabwasser', 'nutzwasser', 'luftf', 'raumtemp'],
        '31' => ['abluftwaescher', 'N2', 'DL', 'Vakuum', 'vibrationsempfindlich_bodenstehend', 'kuehlwasser', 'spezialabwasser', 'nutzwasser', 'explosionsschutz'],
        '32' => ['abluftwaescher', 'N2', 'DL', 'Vakuum', 'explosionsschutz', 'vibrationsempfindlich_bodenstehend', 'raumzuluft_besonders', 'raumabluft_besonders', 'kuehlwasser', 'spezialabwasser', 'nutzwasser'],
        '33' => ['abluftwaescher', 'N2', 'DL', 'Vakuum', 'explosionsschutz', 'vibrationsempfindlich_bodenstehend', 'raumzuluft_besonders', 'raumabluft_besonders', 'kuehlwasser', 'spezialabwasser', 'nutzwasser'],
        '34' => ['abluftwaescher', 'N2', 'DL', 'Vakuum'],
        '35' => ['abluftwaescher', 'N2', 'DL', 'Vakuum'],
    ];

    $id = (string)($raumtyp['id'] ?? '');
    if (isset($hiddenByRaumtyp[$id])) {
        $hidden = array_merge($hidden, $hiddenByRaumtyp[$id]);
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

    $overrides = resolveFieldOverrides($raumtyp ?? [], $bauabschnitt, $ebene);

    $hidden = $overrides['hidden'];
    $defaults = $overrides['defaults'];
    $freetext = $overrides['freetext'];


    if (!$raumtyp) {
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

        // Säurewäscher-Optionen dynamisch an Abzüge-Anzahl des Raumtyps anpassen
        if (($field['name'] ?? '') === 'abluftwaescher') {
            $abzMax = (int)($raumtyp['abzuege_anzahl_max'] ?? 6);
            if ($abzMax === 0) {
                // Keine Abzüge → Säurewäscher ausblenden
                $field['_type_original'] = $field['type'];
                $field['type'] = 'texthidden';
                $field['default_value'] = 0;
            } else {
                // Options: immer 0 bis max
                $newOptions = [];
                for ($i = 0; $i <= $abzMax; $i++) {
                    $newOptions[$i] = (string)$i;
                }
                $field['options'] = $newOptions;
                // Default = min (User startet bei Minimum, kann aber auf 0 runter)
                $field['default_value'] = 0;
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