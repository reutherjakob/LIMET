<?php
/**
 * ============================================================================
 *  ENDPOINT: Parameter-Definitionen + Vorauswahl + Default-Optionen als JSON.
 * ============================================================================
 *
 *  Antwort:
 *    {
 *      "groups": [
 *        { "id":"elektro","label":"Elektro","perRow":3,"special":false,
 *          "params":[ {"key":"...","label":"...","render":"..."}, ... ] },
 *        ...
 *      ],
 *      "preselected": [ "key1", ... ],          // angehakte Parameter-Checkboxen
 *      "defaults": { "mt_mode":"list", "mark_changes":true, "format":"A3" }
 *    }
 * ============================================================================
 */

require_once '../../utils/_utils.php';   // ggf. Pfad an deine Struktur anpassen
check_login();

require_once __DIR__ . '/pdf_paramDefinitions.php';
require_once __DIR__ . '/pdf_report_sets.php';

header('Content-Type: application/json; charset=utf-8');

// Standard-Labels; set-spezifische Label-Overrides wendet das Frontend beim
// Anwenden einer Vorlage an (die Overrides stecken je Set in defs.sets[].labels).
$groupDefs = report_get_groups();

$groups = [];
foreach ($groupDefs as $groupId => $group) {
    $params = [];
    foreach ($group['params'] as $p) {
        // UI-Beschriftung: 'ui_label' > 'label' > Key. So bleibt kein
        // Auswahlfeld leer, auch wenn das PDF-Label bewusst leer ist
        // (z. B. BO-Beschreibung, damit im PDF kein "Anmerkung:" steht).
        $uiLabel = trim((string)($p['ui_label'] ?? ''));
        if ($uiLabel === '') {
            $uiLabel = trim((string)$p['label']);
        }
        if ($uiLabel === '') {
            $uiLabel = $p['key'];
        }
        $params[] = [
            'key' => $p['key'],
            'label' => $uiLabel,
            'render' => $p['render'],
        ];
    }
    $groups[] = [
        'id' => $groupId,
        'label' => $group['label'],
        'perRow' => $group['perRow'] ?? 4,
        'special' => !empty($group['special']),
        'params' => $params,
    ];
}

echo json_encode([
    'groups' => $groups,
    'preselected' => report_selectable_param_keys(),
    'defaults' => report_get_default_options(),
    'sets' => report_get_sets(),
], JSON_UNESCAPED_UNICODE);