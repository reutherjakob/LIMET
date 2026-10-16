<?php
require_once '_utils/_utils.php';
include "_utils/_format_money.php";
init_page_serversides();
require_once '_utils/_csrf.php';


// ---------------------------------------------------------------
// Kumulation: an / aus (URL) + Merkmale, nach denen getrennt wird (Session)
// ---------------------------------------------------------------
$kumuliert = isset($_GET['kumuliert']) && $_GET['kumuliert'] === '1';

// Merkmale, nach denen der User "getrennt halten" kann (Element ist immer getrennt)
$GRUPPEN_OPTIONEN = [
    'bereich'  => 'Raumbereich',
    'variante' => 'Variante',
    'geschoss' => 'Geschoss',
    'be'       => 'Bauetappe (BE)',
    'ba'       => 'Bauabschnitt (BA)',
    'bestand'  => 'Bestand',
    'los'      => 'Los-Nr',
    'budget'   => 'Budget',
];
$GRUPPEN_STANDARD = ['bereich', 'variante', 'geschoss', 'ba'];
$SESSION_KEY_GRUPPEN = 'rb_liste_kumulation_gruppen';

if (isset($_GET['gruppe'])) {
    // Auswahl aus URL übernehmen (nur erlaubte Werte) und für die Sitzung merken
    $gewaehlt = array_values(array_intersect(
        array_keys($GRUPPEN_OPTIONEN),
        array_filter(explode(',', (string)$_GET['gruppe']))
    ));
    $_SESSION[$SESSION_KEY_GRUPPEN] = $gewaehlt;
} elseif (isset($_SESSION[$SESSION_KEY_GRUPPEN]) && is_array($_SESSION[$SESSION_KEY_GRUPPEN])) {
    $gewaehlt = $_SESSION[$SESSION_KEY_GRUPPEN];
} else {
    $gewaehlt = $GRUPPEN_STANDARD;
}

$excelfilename = buildExcelFilename($kumuliert ? 'Elementliste_kumuliert' : 'Elementliste');

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Liste</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf" content="<?= h(csrf_token()) ?>">


    <link rel="icon" href="/Logos/iphone_favicon.png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="_utils/style.css" type="text/css" media="screen"/>


</head>

<body style="height:100%">
<div class="container-fluid">
    <div id="limet-navbar"></div>
    <div class="mt-2 card">
        <div class="card-header">
            <div class="row g-2">
                <div class="col-12 col-xl-5 d-flex flex-wrap align-items-center gap-3">
                    <b>Elemente im Projekt</b>

                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox"
                               id="chkKumuliert" <?= $kumuliert ? 'checked' : '' ?>>
                        <label class="form-check-label" for="chkKumuliert">Kumuliert</label>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="btnGruppen" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                aria-expanded="false" <?= $kumuliert ? '' : 'disabled' ?>>
                            <i class="fas fa-layer-group"></i> Getrennt nach
                            <span class="badge bg-secondary"><?= count($gewaehlt) + 1 ?></span>
                        </button>
                        <div class="dropdown-menu p-3">
                            <div class="small text-muted mb-2">Eigene Zeile je unterschiedlichem Wert:</div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="grp_element" checked disabled>
                                <label class="form-check-label" for="grp_element">Element <span class="text-muted small">(immer)</span></label>
                            </div>
                            <?php foreach ($GRUPPEN_OPTIONEN as $key => $label): ?>
                                <div class="form-check">
                                    <input class="form-check-input chk-gruppe" type="checkbox"
                                           id="grp_<?= h($key) ?>" value="<?= h($key) ?>"
                                        <?= in_array($key, $gewaehlt, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-nowrap" for="grp_<?= h($key) ?>"><?= h($label) ?></label>
                                </div>
                            <?php endforeach; ?>
                            <div class="d-flex gap-2 mt-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnGruppenStandard">Standard</button>
                                <button type="button" class="btn btn-sm btn-primary flex-fill" id="btnGruppenAnwenden">Anwenden</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                            data-bs-target="#infoKumulationModal" title="Wie funktioniert die Kumulation?">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>
                <div class="col-12 col-xl-7 d-flex align-items-center justify-content-end" id="dt-header-container"></div>
            </div>
        </div>
        <div class="card-body" id="elementLots">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT  tabelle_räume.Raumnr,
                            tabelle_räume.idTABELLE_Räume,
                            tabelle_räume.Raumbezeichnung,
                            tabelle_räume.`Raumbereich Nutzer`,
                            tabelle_räume.Raumnummer_Nutzer,
                            tabelle_räume.Geschoss,
                            tabelle_räume.Bauetappe,
                            tabelle_räume.Bauabschnitt,
                            tabelle_räume_has_tabelle_elemente.id as rhe_id,
                            tabelle_räume_has_tabelle_elemente.Anzahl,
                            tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
                            tabelle_räume_has_tabelle_elemente.Standort,
                            tabelle_räume_has_tabelle_elemente.Verwendung,
                            tabelle_projekt_varianten_kosten.Kosten AS EP,
                            tabelle_elemente.ElementID,
                            tabelle_elemente.Bezeichnung,
                            tabelle_varianten.Variante,
                            tabelle_varianten.idtabelle_Varianten, 
                            tabelle_projektbudgets.Budgetnummer,
                            tabelle_lose_extern.LosNr_Extern,
                            tabelle_lose_extern.LosBezeichnung_Extern,
                            tabelle_auftraggeber_gewerke.Gewerke_Nr,
                            tabelle_auftraggeber_ghg.GHG,
                            tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
                FROM (((tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projektbudgets RIGHT JOIN (tabelle_lose_extern RIGHT JOIN 
                (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente
                ON tabelle_räume.idTABELLE_Räume =
                tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_varianten_kosten
                ON (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten =
                tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) AND
                (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =
                tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND
                (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente =
                tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente))
                ON tabelle_elemente.idTABELLE_Elemente =
                tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)
                ON tabelle_varianten.idtabelle_Varianten =
                tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
                ON tabelle_lose_extern.idtabelle_Lose_Extern =
                tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                ON tabelle_projektbudgets.idtabelle_projektbudgets =
                tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets)
                ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente =
                tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND
                (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte =
                tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke
                ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke =
                tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) LEFT JOIN tabelle_auftraggeber_ghg
                ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG =
                tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG)
                LEFT JOIN tabelle_auftraggeberg_gug
                ON tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG =
                tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG
                WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
                AND Anzahl <>0";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $_SESSION["projectID"]);
            $stmt->execute();
            $result = $stmt->get_result();

            // ---------------------------------------------------------------
            // 1) Alle Zeilen normalisiert einsammeln
            // ---------------------------------------------------------------
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $ep = ((int)$row["Standort"] === 0) ? 0 : (float)$row["EP"];
                $kommentar = trim((string)($row["Kurzbeschreibung"] ?? ''));
                $rows[] = [
                    'raumnr'     => $_SESSION["projectName"] === "GCP" ? $row["Raumnummer_Nutzer"] : $row["Raumnr"],
                    'raum'       => $row["Raumbezeichnung"],
                    'bereich'    => $row["Raumbereich Nutzer"],
                    'geschoss'   => $row["Geschoss"],
                    'be'         => $row["Bauetappe"],
                    'ba'         => $row["Bauabschnitt"],
                    'elementid'  => $row["ElementID"],
                    'element'    => $row["Bezeichnung"],
                    'anzahl'     => $row["Anzahl"] + 0,
                    'variante'   => $row["Variante"],
                    'bestand'    => ($row["Neu/Bestand"] == 0 ? "Ja" : "Nein"),
                    'standort'   => $row["Standort"],
                    'verwendung' => $row["Verwendung"],
                    'ep'         => $ep,
                    'los'        => $row["LosNr_Extern"],
                    'budget'     => $row["Budgetnummer"],
                    'gewerk'     => $row["Gewerke_Nr"],
                    'ghg'        => $row["GHG"],
                    'losbez'     => $row["LosBezeichnung_Extern"],
                    'comments'   => $kommentar !== '' ? [$kommentar] : [],
                    'raw'        => $row,
                ];
            }
            $stmt->close();
            $mysqli->close();

            // ---------------------------------------------------------------
            // 2) Optional kumulieren: Element + vom User gewählte Merkmale
            // ---------------------------------------------------------------
            $MEHRERE = 'mehrere';
            if ($kumuliert) {
                // Spalten, die bei unterschiedlichen Werten "mehrere" zeigen
                $mergeKeys = ['raumnr', 'raum', 'bereich', 'geschoss', 'be', 'ba', 'element', 'variante', 'bestand',
                    'standort', 'verwendung', 'ep', 'los', 'budget', 'gewerk', 'ghg', 'losbez'];
                $groups = [];
                foreach ($rows as $r) {
                    // Standort = 0: Element wird hier nur verwendet, steht aber woanders
                    // -> wird dort (Standort = 1) gezählt, hier nicht nochmal
                    if ((int)$r['standort'] === 0) {
                        continue;
                    }

                    // Gruppenschlüssel: Element + alle gewählten Merkmale
                    $keyParts = [(string)$r['elementid']];
                    foreach ($gewaehlt as $g) {
                        $keyParts[] = (string)$r[$g];
                    }
                    $key = implode("\x1F", $keyParts);

                    if (!isset($groups[$key])) {
                        $r['raw'] = null; // im kumulierten Modus kein Bearbeiten
                        $groups[$key] = $r;
                        continue;
                    }
                    $grp = &$groups[$key];
                    $grp['anzahl'] += $r['anzahl'];
                    foreach ($mergeKeys as $k) {
                        if ((string)$grp[$k] !== (string)$r[$k]) {
                            $grp[$k] = $MEHRERE;
                        }
                    }
                    $grp['comments'] = array_values(array_unique(array_merge($grp['comments'], $r['comments'])));
                    unset($grp);
                }
                $rows = array_values($groups);
            }

            // ---------------------------------------------------------------
            // 3) Ausgabe
            // ---------------------------------------------------------------
            echo "<table class='table table-striped table-hover compact table-bordered' id='tableRoombookList'>
            <thead><tr>
                <th>Raumnr</th>
                <th>Raum</th>
                <th>Raumbereich</th>
                <th>Geschoss</th>
                <th>BE</th>
                <th>BA</th>
                <th data-export-title='ElementID'> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='ID'><i class='fas fa-fingerprint'></i></div> </th>
                <th>Element</th>
                <th>Stk</th>
                <th>Variante</th>  
                <th>Bestand</th>
                <th data-export-title='Standort'> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Standort'> <i class='fab fa-periscope '></i></div> </th>
                <th data-export-title='Verwendung'> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Verwendung'> <i class='fas fa-cogs'></i></div> </th>
                <th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Einheitspreis'> <i class='fas fa-euro-sign'></i> </div></th>
                <th data-export-title='Einheitspreis'>EP-Excel</th>
                <th>Los-Nr</th>
                <th>Budget</th>                                                                
                <th>Gewerk</th>
                <th>GHG</th>
                <th>Los Bezeichnung</th>  
                <th data-export-title='Kommentar'>  <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Kommentar'><i class='far fa-comments'></i></div></th>
                <th>   <i class='fas fa-edit'></i> </th>
            </tr>
            </thead>";
            echo "<tbody>";

            $td = fn($v) => "<td>" . h((string)$v) . "</td>";

            foreach ($rows as $r) {
                echo "<tr>";
                echo $td($r['raumnr']);
                echo $td($r['raum']);
                echo $td($r['bereich']);
                echo $td($r['geschoss']);
                echo $td($r['be']);
                echo $td($r['ba']);
                echo $td($r['elementid']);
                echo $td($r['element']);
                echo $td($r['anzahl']);
                echo $td($r['variante']);
                echo $td($r['bestand']);
                echo $td($r['standort']);
                echo $td($r['verwendung']);
                // Anzeige: deutsches Format
                echo $td(is_numeric($r['ep']) ? format_money($r['ep']) : $r['ep']);
                // Excel: Punkt als Dezimaltrenner -> Export schreibt echte Zahl (Format #,##0.00)
                echo $td(is_numeric($r['ep']) ? number_format((float)$r['ep'], 2, '.', ',') : $r['ep']);
                echo $td($r['los']);
                echo $td($r['budget']);
                echo $td($r['gewerk']);
                echo $td($r['ghg']);
                echo $td($r['losbez']);

                if (!empty($r['comments'])) {
                    echo "<td><button type='button' class='btn btn-sm btn-outline-dark' 
                        data-bs-toggle='popover' 
                        data-bs-placement='top' 
                        data-bs-content='" . htmlspecialchars(implode(' | ', $r['comments']), ENT_QUOTES) . "' 
                        title='Kommentar'>
                        <i class='fa fa-comment'></i></button></td>";
                } else {
                    echo "<td></td>";
                }

                if ($r['raw'] !== null) {
                    $row = $r['raw'];
                    echo "<td>
                        <button type='button' class='btn btn-sm btn-outline-secondary edit-entry-btn'
                            data-id='" . $row["rhe_id"] . "'
                            data-variantenid='" . $row["idtabelle_Varianten"] . "'
                            data-anzahl='" . $row["Anzahl"] . "'
                            data-bestand='" . $row["Neu/Bestand"] . "'
                            data-standort='" . $row["Standort"] . "'
                            data-verwendung='" . $row["Verwendung"] . "'
                            data-ep='" . $row["EP"] . "'
                            data-comment='" . htmlspecialchars($row["Kurzbeschreibung"] ?? '', ENT_QUOTES) . "'>
                            <i class='fas fa-edit'></i>
                        </button>
                    </td>";
                } else {
                    echo "<td></td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
            ?>
        </div>
    </div>
</div>


<!-- Info Kumulation Modal -->
<div class="modal fade" id="infoKumulationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle text-info"></i> Kumulierte Ansicht</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2"><b>Ohne Haken bei „Kumuliert“</b><br>
                    Jede Zeile ist genau ein Element in einem Raum. Nur hier können Einträge bearbeitet werden.</p>

                <p class="mb-2"><b>Mit Haken bei „Kumuliert“</b><br>
                    Gleiche Elemente werden zu einer Zeile zusammengefasst, die Stückzahlen (Stk) werden addiert.</p>

                <div class="alert alert-warning small mb-2">
                    <i class="fab fa-periscope"></i> <b>Standort = Nein</b><br>
                    Diese Einträge sind in der kumulierten Ansicht <b>nicht enthalten</b>.
                    Das Element wird in diesem Raum nur verwendet, steht aber in einem anderen Raum
                    und wird dort (Standort = Ja) bereits gezählt. So wird nichts doppelt gezählt.
                </div>

                <p class="mb-2"><b>„Getrennt nach“</b><br>
                    Hier legen Sie fest, welche Merkmale <u>nicht</u> zusammengefasst werden.
                    Für jeden unterschiedlichen Wert eines angehakten Merkmals entsteht eine eigene Zeile.
                    Das Element ist immer getrennt.</p>

                <div class="alert alert-light border small mb-2">
                    <b>Beispiel:</b> Element „Waschbecken“ steht in 3 Räumen, 2× Variante A und 1× Variante B.<br>
                    Mit „Variante“ angehakt: <b>2 Zeilen</b> (A mit Stk 2, B mit Stk 1).<br>
                    Ohne „Variante“: <b>1 Zeile</b> mit Stk 3, Variante = „mehrere“.
                </div>

                <p class="mb-2"><b>„mehrere“</b><br>
                    Steht in einer Spalte, wenn die zusammengefassten Einträge dort unterschiedliche Werte haben
                    (z.B. verschiedene Räume oder Preise).</p>

                <p class="mb-2"><b>Kommentare</b><br>
                    Alle unterschiedlichen Kommentare der Gruppe werden zusammengeführt.</p>

                <p class="mb-2"><b>Standard</b><br>
                    Getrennt nach Raumbereich, Variante, Geschoss und Bauabschnitt.
                    Ihre Auswahl bleibt für Ihre Sitzung gespeichert.</p>

                <p class="mb-0"><b>Excel</b><br>
                    Der Export enthält immer genau die aktuelle Ansicht.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Entry Modal -->
<div class="modal fade" id="editEntryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eintrag bearbeiten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="mb-3">
                    <label class="form-label">Variante</label>
                    <select class="form-select form-select-sm" id="edit_variantenID">
                        <option value="1">A</option>
                        <option value="2">B</option>
                        <option value="3">C</option>
                        <option value="4">D</option>
                        <option value="5">E</option>
                        <option value="6">F</option>
                        <option value="7">G</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Anzahl</label>
                    <input type="number" class="form-control form-control-sm" id="edit_amount">
                </div>
                <div class="mb-3">
                    <label class="form-label">Bestand (Neu?)</label>
                    <select class="form-select form-select-sm" id="edit_bestand">
                        <option value="0">Ja</option>
                        <option value="1">Nein</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Standort</label>
                    <select class="form-select form-select-sm" id="edit_standort">
                        <option value="0">Nein</option>
                        <option value="1">Ja</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Verwendung</label>
                    <select class="form-select form-select-sm" id="edit_verwendung">
                        <option value="0">Nein</option>
                        <option value="1">Ja</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kommentar</label>
                    <textarea class="form-control form-control-sm" id="edit_comment" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-sm btn-primary" id="saveEditEntry">Speichern</button>
            </div>
        </div>
    </div>
</div>

<script src="_utils/_utils.js"></script>
<script>

    const $excelfilename = <?= json_encode($excelfilename) ?>;
    const GRUPPEN_STANDARD = <?= json_encode($GRUPPEN_STANDARD) ?>;

    // Spalten-Indizes der Tabelle
    const COL_EP_DISPLAY = 13;
    const COL_EP_EXCEL = 14;
    const COL_LOSNR = 15;
    const COL_EDIT = 21;
    const COL_COUNT = 22;

    // Spalten, die in Excel landen (Anzeige-EP und Edit-Button raus)
    const EXPORT_COLUMNS = [...Array(COL_COUNT).keys()]
        .filter(i => i !== COL_EP_DISPLAY && i !== COL_EDIT);

    // 0 -> A, 25 -> Z, 26 -> AA ...
    function excelColLetter(idx) {
        let s = '';
        idx++;
        while (idx > 0) {
            const m = (idx - 1) % 26;
            s = String.fromCharCode(65 + m) + s;
            idx = Math.floor((idx - 1) / 26);
        }
        return s;
    }

    // Zahlenzellen einer Excel-Spalte in Textzellen umwandeln
    // (entspricht dem Apostroph beim Eintippen, nur ohne sichtbares Zeichen)
    function forceTextColumn(sheet, colLetter) {
        const ns = sheet.documentElement.namespaceURI;
        const re = new RegExp('^' + colLetter + '\\d+$');
        $('row c', sheet).each(function () {
            const cell = this;
            if (!re.test(cell.getAttribute('r'))) return;
            if (cell.getAttribute('t') === 'inlineStr') return; // ist schon Text
            const v = cell.getElementsByTagName('v')[0];
            if (!v) return;
            const text = v.textContent;
            cell.removeChild(v);
            cell.removeAttribute('s');
            cell.setAttribute('t', 'inlineStr');
            const is = sheet.createElementNS(ns, 'is');
            const t = sheet.createElementNS(ns, 't');
            t.textContent = text;
            is.appendChild(t);
            cell.appendChild(is);
        });
    }

    // Seite mit neuen Kumulations-Einstellungen laden
    function reloadKumulation(kumuliert, gruppen) {
        const url = new URL(window.location.href);
        if (kumuliert) {
            url.searchParams.set('kumuliert', '1');
        } else {
            url.searchParams.delete('kumuliert');
        }
        if (Array.isArray(gruppen)) {
            url.searchParams.set('gruppe', gruppen.join(','));
        }
        window.location.href = url.toString();
    }

    $(document).ready(function () {

        // Checkbox "Kumuliert" -> Seite neu laden
        $('#chkKumuliert').on('change', function () {
            reloadKumulation(this.checked);
        });

        // "Getrennt nach" übernehmen
        $('#btnGruppenAnwenden').on('click', function () {
            const gruppen = $('.chk-gruppe:checked').map(function () {
                return this.value;
            }).get();
            reloadKumulation(true, gruppen);
        });

        // Standardauswahl setzen (noch nicht anwenden)
        $('#btnGruppenStandard').on('click', function () {
            $('.chk-gruppe').each(function () {
                this.checked = GRUPPEN_STANDARD.includes(this.value);
            });
        });

        const dt = new DataTable('#tableRoombookList', {
            select: true,
            dom: "<'dt-buttons'B><'dt-search'f><'dt-info' i>rtp",
            deferRender: true,
            scroller: true,
            scrollY: '85vh',       // Höhe des sichtbaren Bereichs
            scrollCollapse: true,  // schrumpft wenn weniger Zeilen
            paging: true,

            columnDefs: [
                {
                    targets: [COL_EP_EXCEL],
                    visible: false,
                    searchable: false
                }
            ],
            order: [[2, "asc"]],

            language: {
                url: "/_utils/de-DE.json",
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '',
                    titleAttr: "Download as Excel",
                    filename: $excelfilename,
                    className: 'fas fa-file-excel btn btn-outline-success bg-light ms-2',
                    exportOptions: {
                        columns: EXPORT_COLUMNS,
                        format: {
                            header: function (data, columnIdx, node) {
                                const exportTitle = node ? node.getAttribute('data-export-title') : null;
                                return exportTitle || DataTable.Buttons.stripData(data, null);
                            },
                            body: function (data, row, column, node) {
                                // Kommentar steckt im Popover-Button
                                const match = String(data).match(/data-bs-content='([^']*)'/);
                                if (match) {
                                    return DataTable.Buttons.stripData(match[1], null);
                                }
                                return DataTable.Buttons.stripData(data, null);
                            }
                        }
                    },
                    customize: function (xlsx) {
                        const sheet = xlsx.xl.worksheets['sheet1.xml'];
                        // Los-Nr immer als Text (z.B. "1.10" bleibt "1.10")
                        forceTextColumn(sheet, excelColLetter(EXPORT_COLUMNS.indexOf(COL_LOSNR)));
                    }
                }, {
                    extend: 'searchBuilder',
                    className: 'btn btn-outline-secondary fa fa-search bg-light me-2',
                }
            ],

            initComplete: function () {
                $('#tableRoombookList_wrapper .dt-info').appendTo('#dt-header-container');
                $('#tableRoombookList_wrapper .dt-buttons').appendTo('#dt-header-container');
                $('#tableRoombookList_wrapper .dt-search').appendTo('#dt-header-container');
            }
        });


        // Popovers aktivieren
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                container: 'body',
                trigger: 'focus',
                placement: 'left'
            });
        });

        // Offene Popover schließen bei Klick außerhalb
        $(document).on('click', function (e) {
            $('[data-bs-toggle="popover"]').each(function () {
                if (
                    !$(this).is(e.target) &&
                    $(this).has(e.target).length === 0 &&
                    $('.popover').has(e.target).length === 0
                ) {
                    $(this).popover('hide');
                }
            });
        });

        // Modal öffnen & befüllen
        $(document).on('click', '.edit-entry-btn', function () {
            const btn = $(this);
            $('#edit_id').val(btn.data('id'));
            $('#edit_amount').val(btn.data('anzahl'));
            $('#edit_bestand').val(String(btn.data('bestand')));
            $('#edit_standort').val(String(btn.data('standort')));
            $('#edit_verwendung').val(String(btn.data('verwendung')));
            $('#edit_variantenID').val(String(btn.data('variantenid')));
            $('#edit_comment').val(btn.data('comment'));
            $('#editEntryModal').modal('show');
        });

        // Speichern
        $('#saveEditEntry').on('click', function () {
            const standort = $('#edit_standort').val();
            const verwendung = $('#edit_verwendung').val();
            if (standort === '0' && verwendung === '0') {
                alert('Standort und Verwendung kann nicht beide Nein sein!');
                return;
            }
            $.ajax({
                url: 'saveRoombookEntry.php',
                type: 'POST',
                data: {
                    id: $('#edit_id').val(),
                    variantenID: $('#edit_variantenID').val(),
                    amount: $('#edit_amount').val(),
                    bestand: $('#edit_bestand').val(),
                    standort: standort,
                    verwendung: verwendung,
                    comment: $('#edit_comment').val()
                },
                success(data) {
                    $('#editEntryModal').modal('hide');
                    makeToaster(data.trim(), true);

                    const id = $('#edit_id').val();
                    const anzahl = $('#edit_amount').val();
                    const bestand = $('#edit_bestand').val();
                    const standort = $('#edit_standort').val();
                    const verwendung = $('#edit_verwendung').val();
                    const variantenID = $('#edit_variantenID').val();
                    const comment = $('#edit_comment').val();
                    const varianteText = $('#edit_variantenID option:selected').text();

                    // Zeile anhand der rhe_id finden (Edit-Button mit data-id)
                    const btn = $(`.edit-entry-btn[data-id='${id}']`);

                    // data-* Attribute am Button aktualisieren für nächstes Öffnen
                    btn.data('anzahl', anzahl)
                        .data('bestand', bestand)
                        .data('standort', standort)
                        .data('verwendung', verwendung)
                        .data('variantenid', variantenID)
                        .data('comment', comment);

                    // Zellen direkt im DOM updaten (Scroller rendert DOM-Nodes direkt)
                    const cells = btn.closest('tr').find('td');
                    cells.eq(8).text(anzahl);                                // Stk
                    cells.eq(9).text(varianteText);                          // Variante
                    cells.eq(10).text(bestand === '0' ? 'Ja' : 'Nein');      // Bestand
                    cells.eq(11).text(standort);                             // Standort
                    cells.eq(12).text(verwendung);                           // Verwendung

                    // EP Anzeige (deutsch) + EP Excel (Punkt als Dezimaltrenner -> echte Zahl im Export)
                    const epNum = standort === '0' ? 0 : parseFloat(btn.data('ep'));
                    const epFormatted = epNum.toLocaleString('de-DE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    const epExcel = epNum.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    // Spalte 14 ist ausgeblendet -> über die DataTables-API setzen
                    const dtRow = dt.row(btn.closest('tr'));
                    cells.eq(COL_EP_DISPLAY).text(epFormatted);
                    dt.cell(dtRow.index(), COL_EP_EXCEL).data(epExcel);
                }
            });
        });

    });

</script>
</body>
</html>