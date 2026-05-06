<?php
global $mysqli, $formFields, $labortypen;
include "../Nutzerlogin/db.php";
require_once "../Nutzerlogin/_utils.php";
$role = init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_view", "spargelfeld_admin"]);

require_once "form_fields_forNutzergruppe1.php"; // Array $formFields
require_once 'raumtyp_resolver.php';
require_once "../Nutzerumfrage/raumtypen.php"; // lädt $labortypen

error_reporting(E_ALL);
ini_set('display_errors', 1);


$roomId = $_POST['roomID'] ?? null;
$roomname = $_POST['roomname'] ?? null;
$raumtyp_id = $_POST['raumkategorie'] ?? null;
$bauabschnitt = $_POST['bauabschnitt'] ?? null;
$ebene = $_POST['ebene'] ?? null;


$raumtyp = getRaumtypById($labortypen, $raumtyp_id);
$formFields = applyRaumtypOverrides($formFields, $raumtyp, $bauabschnitt ?? '', $ebene ?? '');


function renderForm(array $formFields, array $userData = [], string $role = ''): void
{
    global $roomname;
    echo '<form id="roomParameterForm"><div class="card-header d-flex align-items-center justify-content-between" > 
            <strong> Labortechnische Raumanforderung </strong>';

    echo '<div class="d-flex align-items-center justify-content-end">';
    if ($role !== 'spargelfeld_view') {
        echo '<button type="submit" id="saveBtn" class="btn btn-success">
            <i class="far fa-save"></i> Anforderungen speichern
          </button>';
    }
    echo '</div>
          </div> 
          <div class="card-body px-2 py-2">     </div>';

    foreach ($formFields as $field) {
        $name = $field['name'];
        $label = $field['label'];
        $type = $field['type'];
        $kategorie = $field['kategorie'] ?? '';
        $defaultValue = '';

        if (is_array($field['default_value'] ?? false)) {
            if ($roomname && isset($field['default_value'][$roomname])) {
                $defaultValue = $field['default_value'][$roomname];
            }
        } else {
            $defaultValue = $field['default_value'] ?? '';
        }

        $options = $field['options'] ?? [];
        $value = $userData[$name] ?? $defaultValue;
        $info = $field['info'] ?? '';
        switch ($type) {
            case 'text':
                echo "<div class='mb-2 {$kategorie} d-flex align-items-center'>";
                echo "<label for='{$name}' class='form-label col-6 ms-2 me-2 rechtsbuendig'><strong>{$label}</strong>";
                if ($info) {
                    echo " <button class='btn btn-sm bg-white rounded'
                                  data-bs-toggle='popover'
                                  data-bs-content='{$info}'>
                                    <i class='fas fa-info-circle'></i>
                                </button>";
                }
                echo "</label>";
                echo "<div class='col-6'>  <input class='form-control flex-grow-1' type='text' name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'> </div></div>";
                break;

            case 'texthidden':
                echo "<div class='{$kategorie}'>";
                echo "<label for='{$name}' class='sr-only ms-2 me-2 rechtsbuendig' >{$label}:</label>";
                echo "<div class='col-9'><input class='form-control' type='hidden' name='{$name}' id='{$name}'  value='" . htmlspecialchars($value) . "'></div>";
                break;

            case 'text_non_editable':
                echo "<div class='mb-2 {$kategorie} d-flex align-items-center'>";
                echo "<label for='{$name}' class='form-label col-6 ms-2 me-2 rechtsbuendig'><strong>{$label}</strong>";
                if ($info) {
                    echo " <button class='btn btn-sm bg-white rounded'
                                  data-bs-toggle='popover'
                                  data-bs-content='{$info}'>
                                    <i class='fas fa-info-circle'></i>
                                </button>";
                }
                echo "</label>";
                echo "<div class='col-4'>  <input readonly class='form-control flex-grow-1 fw-bold border-white' type='text' name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'> </div></div>";
                break;

            case 'yesno':
                $defaultVal = $field['default_value'] ?? 'Nein';
                $isDefaultYes = ($defaultVal === 'Ja' || $defaultVal === '1' || $defaultVal == 1);

                if ($value === 'unbekannt' ) {
                    $btnClass = 'btn btn-outline-secondary';
                    $btnText = 'unbekannt';
                    $hiddenValue = 'unbekannt';
                } elseif ($value == 1 || $value === '1' || $value === 'Ja') {
                    $btnClass = 'btn btn-outline-success';
                    $btnText = 'Ja';
                    $hiddenValue = '1';
                } else {
                    $btnClass = 'btn btn-outline-primary';
                    $btnText = 'Nein';
                    $hiddenValue = '0';
                }

                $hasComment = !empty($field['optional_comment_label']);
                $commentLabel = $field['optional_comment_label'] ?? 'Kommentar:';
                $commentName = $name . '_kommentar';
                $commentValue = htmlspecialchars($userData[$commentName] ?? '');
                $isDefaultValue = ($hiddenValue === ($isDefaultYes ? '1' : '0'));
                $showComment = $hasComment && !$isDefaultValue && $hiddenValue !== 'unbekannt';
                $showIfVal = $isDefaultYes ? '0' : '1';

                echo "<div class='mb-2 {$kategorie}'>";
                echo "  <div class='d-flex align-items-center'>";
                echo "    <label for='{$name}_toggle' class='form-label col-6 ms-2 me-2 rechtsbuendig'><strong>{$label}";
                if ($info) {
                    echo " <button class='btn btn-sm bg-white rounded'
          data-bs-toggle='popover'
          data-bs-content='{$info}'>
            <i class='fas fa-info-circle'></i>
        </button>";
                }
                echo "    </strong></label>";
                echo "    <button type='button' class='{$btnClass} text-nowrap' id='{$name}_toggle'
                data-yesno-toggle='1'
                data-default-yes='" . ($isDefaultYes ? '1' : '0') . "'
                style='min-width: 4vw;'>{$btnText}</button>";
                echo "    <input type='hidden' name='{$name}' id='{$name}' value='{$hiddenValue}'>";
                echo "  </div>";

                if ($hasComment) {
                    $wrapClass = $showComment ? 'd-flex align-items-center mt-1' : 'align-items-center mt-1';
                    $displayStyle = $showComment ? '' : 'display:none;';
                    echo "  <div class='{$wrapClass}' id='{$name}_kommentar_wrap' data-show-if='{$showIfVal}' style='{$displayStyle}'>";
                    echo "    <label class='form-label col-6 ms-2 me-2 rechtsbuendig text-muted'><small>Kommentar:</small></label>";
                    echo "    <div class='col-5'><input class='form-control form-control-sm' type='text' name='{$commentName}' id='{$commentName}' value='{$commentValue}' placeholder='{$commentLabel}'></div>";
                    echo "  </div>";
                }
                echo "</div>";
                break;

            case 'multiselect':
                $selectedValues = !empty($value) ? array_map('trim', explode(',', $value)) : [];
                echo "<div class='mb-2 {$kategorie} d-flex align-items-center'>";
                echo "<label class='form-label ms-2 col-6 rechtsbuendig flex-shrink-0'><strong>{$label}</strong>";
                if ($info) {
                    echo " <button class='btn btn-sm bg-white'
                              data-bs-toggle='popover'
                              data-bs-content='{$info}'>
                                <i class='fas fa-info-circle'></i>
                            </button>";
                }
                echo "</label>";

                echo "<div class='ms-2 d-flex flex-wrap gap-1' id='{$name}_group'>";
                foreach ($options as $optValue => $optLabel) {
                    $isChecked = in_array((string)$optValue, $selectedValues);
                    $checkboxId = "{$name}_opt_" . preg_replace('/[^a-zA-Z0-9]/', '_', $optValue);
                    echo "<input class='btn-check' type='checkbox'
                        name='{$name}[]' id='{$checkboxId}'
                        value='" . htmlspecialchars($optValue) . "'
                        autocomplete='off'
                        " . ($isChecked ? 'checked' : '') . ">";
                    echo "<label class='btn btn-outline-primary' for='{$checkboxId}'>" . htmlspecialchars($optLabel) . "</label>";
                }
                echo "<input type='hidden' name='{$name}_sentinel' value='1'>";
                echo "</div></div>";
                break;

            case 'select':
                $hasComment = !empty($field['optional_comment_label']);
                $commentLabel = $field['optional_comment_label'] ?? 'Kommentar:';
                $commentName = $name . '_kommentar';
                $commentValue = htmlspecialchars($userData[$commentName] ?? '');
                $defaultVal = is_array($field['default_value'] ?? '') ? '' : ($field['default_value'] ?? '');
                $isDefault = ((string)$value === (string)$defaultVal);
                $showComment = $hasComment && !$isDefault;

                echo "<div class='mb-2 {$kategorie}'>";
                echo "<div class='d-flex align-items-center'>";
                echo "<label class='form-label me-2 col-6 rechtsbuendig flex-shrink-0'><strong>{$label}</strong>";
                if ($info) {
                    echo " <button class='btn btn-sm bg-white'
                      data-bs-toggle='popover' 
                      data-bs-content='{$info}'>
                        <i class='fas fa-info-circle'></i>
                    </button>";
                }
                echo "</label>";
                echo "<div class='ms-2 d-flex flex-wrap gap-1' role='group' aria-label='{$label}'>";
                foreach ($options as $optValue => $optLabel) {
                    $checked = ($optValue == $value) ? "checked" : "";
                    $btnId = "{$name}_{$optValue}";
                    echo "<input type='radio' class='btn-check' name='{$name}' id='{$btnId}' value='" . htmlspecialchars($optValue) . "' {$checked} autocomplete='off' data-select-comment-target='{$name}'>";
                    echo "<label class='btn btn-outline-primary' for='{$btnId}'>" . htmlspecialchars($optLabel) . "</label>";
                }
                echo "</div></div>";

                if ($hasComment) {
                    $wrapClass = $showComment ? 'd-flex align-items-center mt-1' : 'align-items-center mt-1';
                    $displayStyle = $showComment ? '' : 'display:none;';
                    echo "<div class='{$wrapClass}' id='{$name}_kommentar_wrap' data-default-val='" . htmlspecialchars($defaultVal) . "' style='{$displayStyle}'>";
                    echo "  <label class='form-label col-6 ms-2 me-2 rechtsbuendig text-muted'><small>Kommentar:</small></label>";
                    echo "  <div class='col-5'><input class='form-control form-control-sm' type='text' name='{$commentName}' id='{$commentName}' value='{$commentValue}' placeholder='{$commentLabel}'></div>";
                    echo "</div>";
                }
                echo "</div>";
                break;

            case 'number':
                echo "<div class='mb-2 {$kategorie} d-flex align-items-center'>";
                echo "<label for='{$name}' class='form-label col-6 ms-2 me-2 rechtsbuendig'><strong>{$label}</strong>";
                if ($info) {
                    echo " <button class='btn btn-sm bg-white rounded'
                      data-bs-toggle='popover'
                      data-bs-content='{$info}'>
                        <i class='fas fa-info-circle'></i>
                    </button>";
                }
                echo "</label>";
                echo "<div class='col-5'><input class='form-control flex-grow-1' type='text' inputmode='decimal' placeholder='Bitte Nummer angeben.'
                        name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'
                        oninput=\"this.value = this.value.replace(/[^0-9.,]/g, '')\"></div></div>";
                break;

            default:
                echo "<input class='form-control' type='text' name='{$name}' id='{$name}' value='" . htmlspecialchars($value) . "'>";
        }
    }
    echo '</form> </div>';
}

$userValues = [];
if ($roomId) {
    $stmt = $mysqli->prepare("SELECT * FROM tabelle_room_requirements_from_user WHERE roomID = ?");
    if ($stmt) {
        $stmt->bind_param('i', $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userValues = $result->fetch_assoc() ?: [];
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Raumanforderungen Formular</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
</head>
<body class="container mt-4">


<?php renderForm($formFields, $userValues, $role); ?>
</body>
<script>
    $(document).ready(function () {
        document.addEventListener('click', function (event) {
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                const popover = bootstrap.Popover.getInstance(el);
                if (!popover) return;
                const popoverElement = document.querySelector('.popover');
                if (popoverElement && !el.contains(event.target) && !popoverElement.contains(event.target)) {
                    popover.hide();
                }
            });
        });

        // Yesno: Nein → Ja → ? → Nein → ...
        document.querySelectorAll('[data-yesno-toggle]').forEach(function (btn) {
            const fieldName = btn.id.replace('_toggle', '');
            const hidden = document.getElementById(fieldName);
            const isDefaultYes = btn.dataset.defaultYes === '1';
            const kommentarWrap = document.getElementById(fieldName + '_kommentar_wrap');


            const states = isDefaultYes
                ? ['1', '0', 'unbekannt']
                : ['0', '1', 'unbekannt'];

            btn.addEventListener('click', function () {
                const current = hidden.value;
                const currentIndex = states.indexOf(current);
                const nextIndex = (currentIndex + 1) % states.length;
                const next = states[nextIndex];

                hidden.value = next;

                if (next === '1') {
                    btn.textContent = 'Ja';
                    btn.className = 'btn btn-outline-success text-nowrap';
                } else if (next === '0') {
                    btn.textContent = 'Nein';
                    btn.className = 'btn btn-outline-primary text-nowrap';
                } else {
                    btn.textContent = 'unbekannt';
                    btn.className = 'btn btn-outline-secondary text-nowrap';
                }

                if (kommentarWrap) {
                    const showIfVal = kommentarWrap.dataset.showIf;
                    const shouldShow = next === showIfVal;
                    kommentarWrap.style.display = shouldShow ? '' : 'none';
                    kommentarWrap.className = shouldShow
                        ? 'd-flex align-items-center mt-1'
                        : 'align-items-center mt-1';
                }
            });
        });
    });
</script>
</html>
