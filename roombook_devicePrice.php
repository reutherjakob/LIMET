<?php
// FX 2026
require_once 'utils/_utils.php';
include 'utils/_format.php';
init_page_serversides("No Redirect");

$mysqli = utils_connect_sql();

// ── Gerätepreise ──────────────────────────────────────────────────────────────
$sql_gp = "
    SELECT
        tp.idTABELLE_Preise,
        tp.Datum,
        tp.Quelle,
        tp.Menge,
        tp.Preis,
        tp.Nebenkosten,
        tp.Kommentar,
        tg.idTABELLE_Geraete,
        tg.GeraeteID,
        tg.Typ,
        tg.Kurzbeschreibung     AS Geraete_Kurzbeschreibung,
        th.Hersteller,
        tpr.idTABELLE_Projekte  AS projectID,
        tpr.Interne_Nr,
        tpr.Projektname,
        tl.idTABELLE_Lieferant  AS lieferantID,
        tl.Lieferant,
        te.Bezeichnung AS Elementbezeichnung
    FROM tabelle_preise tp
        LEFT JOIN tabelle_geraete tg
            ON tp.TABELLE_Geraete_idTABELLE_Geraete = tg.idTABELLE_Geraete
        LEFT JOIN tabelle_elemente te             
            ON tg.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente
        LEFT JOIN tabelle_hersteller th
            ON tg.tabelle_hersteller_idtabelle_hersteller = th.idtabelle_hersteller
        LEFT JOIN tabelle_projekte tpr
            ON tp.TABELLE_Projekte_idTABELLE_Projekte = tpr.idTABELLE_Projekte
        LEFT JOIN tabelle_lieferant tl
            ON tp.tabelle_lieferant_idTABELLE_Lieferant = tl.idTABELLE_Lieferant
    ORDER BY tp.Datum DESC
";
$stmt = $mysqli->prepare($sql_gp);
$stmt->execute();
$rows_gp = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Wartungspreise ────────────────────────────────────────────────────────────
$sql_wp = "
    SELECT
        w.idtabelle_wartungspreise,
        w.WartungspreisProJahr,
        w.Menge,
        w.Wartungsart,
        w.Info,
        w.Datum,
        tg.idTABELLE_Geraete,
        tg.GeraeteID,
        tg.Typ,
        tg.Kurzbeschreibung                         AS Geraete_Kurzbeschreibung,
        th.Hersteller,
        tl.idTABELLE_Lieferant                      AS lieferantID,
        tl.Lieferant,
        w.WartungspreisProJahr * w.Menge            AS Preis_Jahr_Menge,
        te.Bezeichnung AS Elementbezeichnung
    FROM tabelle_wartungspreise w
        INNER JOIN tabelle_geraete tg
            ON w.tabelle_geraete_idTABELLE_Geraete = tg.idTABELLE_Geraete
        LEFT JOIN tabelle_hersteller th
            ON tg.tabelle_hersteller_idtabelle_hersteller = th.idtabelle_hersteller
        LEFT JOIN tabelle_lieferant tl
            ON w.tabelle_lieferant_idTABELLE_Lieferant = tl.idTABELLE_Lieferant
        LEFT JOIN tabelle_räume_has_tabelle_elemente rhe
            ON rhe.TABELLE_Geraete_idTABELLE_Geraete = tg.idTABELLE_Geraete
        LEFT JOIN tabelle_räume tr
            ON rhe.TABELLE_Räume_idTABELLE_Räume = tr.idTABELLE_Räume
        LEFT JOIN tabelle_elemente te
            ON tg.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente
    GROUP BY
        w.idtabelle_wartungspreise,
        te.idTABELLE_Elemente
    ORDER BY w.Datum
";
$stmt = $mysqli->prepare($sql_wp);
$stmt->execute();
$rows_wp = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Projekte für Dropdown ─────────────────────────────────────────────────────
$rows_proj = $mysqli->query("SELECT idTABELLE_Projekte, Interne_Nr, Projektname FROM tabelle_projekte ORDER BY Interne_Nr")->fetch_all(MYSQLI_ASSOC);

// ── Lieferanten für Dropdown ──────────────────────────────────────────────────
$rows_lief = $mysqli->query("SELECT idTABELLE_Lieferant, Lieferant FROM tabelle_lieferant ORDER BY Lieferant")->fetch_all(MYSQLI_ASSOC);

$mysqli->close();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Preise</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
</head>
<body>

<div class="container-fluid">
    <div id="limet-navbar"></div>

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">


            <ul class="nav nav-tabs card-header-tabs mb-0" id="preisTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-geraete-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-geraete"
                            type="button" role="tab">
                        <i class="fas fa-tag me-1"></i>Gerätepreise
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-wartung-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-wartung"
                            type="button" role="tab">
                        <i class="fas fa-wrench me-1"></i>Wartungspreise
                    </button>
                </li>
            </ul>
            <div id="CardHeader-geraete" class="d-flex align-items-center"></div>
            <div id="CardHeader-wartung" class="d-flex align-items-center"></div>

        </div>

        <div class="card-body p-0">
            <div class="tab-content">

                <!-- ═══════════════════════════════════════════════
                     TAB 1 – Gerätepreise
                ═══════════════════════════════════════════════ -->
                <div class="tab-pane fade show active p-2" id="tab-geraete" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tblGeraetepreise"
                               class="table table-sm table-striped table-hover table-bordered p-0">
                            <thead class="table-dark">
                            <tr>
                                <th>Elementbezeichnung</th>
                                <th>ID</th>
                                <th>Typ</th>
                                <th>Hersteller</th>
                                <th>Gerät Beschreibung</th>
                                <th>Datum</th>
                                <th>Verfahren</th>
                                <th class="text-end">Menge</th>
                                <th class="text-end">EP</th>
                                <th class="text-end">NK/Stk</th>
                                <th>Projekt</th>
                                <th>Lieferant</th>
                                <th>Kommentar</th>
                                <th class="text-center" data-bs-toggle="tooltip" title="Bearbeiten">
                                    <i class="fa fa-pencil-alt"></i>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows_gp as $row):
                                $datum = $row['Datum'] ? date('d.m.Y', strtotime($row['Datum'])) : '–';
                                $datum_iso = $row['Datum'] ? date('Y-m-d', strtotime($row['Datum'])) : '';
                                $datum_order = $row['Datum'] ? strtotime($row['Datum']) : 0;
                                $ep = (float)($row['Preis'] ?? 0);
                                $nk = (float)($row['Nebenkosten'] ?? 0);
                                $menge = (int)($row['Menge'] ?? 0);
                                $projekt = trim(($row['Interne_Nr'] ?? '') . ' ' . ($row['Projektname'] ?? ''));
                                $quelle = htmlspecialchars($row['Quelle'] ?? '', ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr data-price-id="<?= (int)$row['idTABELLE_Preise'] ?>"
                                    data-geraete-id="<?= (int)($row['idTABELLE_Geraete'] ?? 0) ?>"
                                    data-date="<?= $datum_iso ?>"
                                    data-quelle="<?= $quelle ?>"
                                    data-menge="<?= $menge ?>"
                                    data-ep="<?= $ep ?>"
                                    data-nk="<?= $nk ?>"
                                    data-kommentar="<?= htmlspecialchars($row['Kommentar'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-project-id="<?= (int)($row['projectID'] ?? 0) ?>"
                                    data-lieferant-id="<?= (int)($row['lieferantID'] ?? 0) ?>">
                                    <td><?= htmlspecialchars($row['Elementbezeichnung'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['GeraeteID'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Typ'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Hersteller'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['Geraete_Kurzbeschreibung'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>


                                    <td data-col="datum" data-order="<?= $datum_order ?>"><?= $datum ?></td>
                                    <td data-col="quelle"><?= $quelle ?></td>
                                    <td data-col="menge" class="text-end" data-order="<?= $menge ?>"><?= number_format($menge, 0, ',', '.') ?></td>
                                    <td data-col="ep" class="text-end" data-order="<?= $ep ?>"><?= number_format($ep, 2, ',', '.') ?></td>
                                    <td data-col="nk" class="text-end" data-order="<?= $nk ?>"><?= number_format($nk, 2, ',', '.') ?></td>
                                    <td data-col="projekt"><?= htmlspecialchars($projekt ?: '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td data-col="lieferant"><?= htmlspecialchars($row['Lieferant'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td data-col="kommentar" class="text-muted small"><?= htmlspecialchars($row['Kommentar'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-dark edit-geraetepreis-btn"
                                                title="Preis ändern"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editGeraetepreisModal">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════
                     TAB 2 – Wartungspreise
                ═══════════════════════════════════════════════ -->
                <div class="tab-pane fade p-2" id="tab-wartung" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tblWartung"
                               class="table table-sm table-striped table-hover table-bordered p-0">
                            <thead class="table-dark">
                            <tr>
                                <th>Elementbezeichnung</th>
                                <th>ID</th>
                                <th>Typ</th>
                                <th>Hersteller</th>
                                <th>Gerät Beschreibung</th>
                                <th>Lieferant</th>
                                <th>Verfahren Info</th>
                                <th>Datum</th>
                                <th>Wartungsart</th>
                                <th class="text-end">Geräte Anzahl</th>
                                <th class="text-end">Preis / Jahr (1 Stk)</th>
                                <th class="text-center" data-bs-toggle="tooltip" title="Bearbeiten">
                                    <i class="fa fa-pencil-alt"></i>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows_wp as $row):
                                $menge = (int)($row['Menge'] ?? 0);
                                $preis_jahr = (float)($row['WartungspreisProJahr'] ?? 0);
                                $datum = $row['Datum'] ? date('d.m.Y', strtotime($row['Datum'])) : '–';
                                $datum_iso = $row['Datum'] ? date('Y-m-d', strtotime($row['Datum'])) : '';
                                $datum_order = $row['Datum'] ? strtotime($row['Datum']) : 0;
                                $artRaw = $row['Wartungsart'] ?? '';
                                $art = htmlspecialchars(
                                    $artRaw === '0' ? 'Betriebswartung' : 'Vollwartung',
                                    ENT_QUOTES, 'UTF-8'
                                );
                                ?>
                                <tr data-wartung-id="<?= (int)$row['idtabelle_wartungspreise'] ?>"
                                    data-geraete-id="<?= (int)($row['idTABELLE_Geraete'] ?? 0) ?>"
                                    data-date="<?= $datum_iso ?>"
                                    data-wartungsart="<?= htmlspecialchars($artRaw, ENT_QUOTES, 'UTF-8') ?>"
                                    data-info="<?= htmlspecialchars($row['Info'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-menge="<?= $menge ?>"
                                    data-preis-jahr="<?= $preis_jahr ?>"
                                    data-lieferant-id="<?= (int)($row['lieferantID'] ?? 0) ?>">
                                    <td><?= htmlspecialchars($row['Elementbezeichnung'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['GeraeteID'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Typ'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Hersteller'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['Geraete_Kurzbeschreibung'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

                                    <td data-col="lieferant"><?= htmlspecialchars($row['Lieferant'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td data-col="info" class="text-muted small"><?= htmlspecialchars($row['Info'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td data-col="datum" data-order="<?= $datum_order ?>"><?= $datum ?></td>
                                    <td data-col="wartungsart"><?= $art ?></td>
                                    <td data-col="menge" class="text-end" data-order="<?= $menge ?>"><?= number_format($menge, 0, ',', '.') ?></td>
                                    <td data-col="preis" class="text-end" data-order="<?= $preis_jahr ?>"><?= number_format($preis_jahr, 2, ',', '.') ?></td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-dark edit-wartungspreis-btn"
                                                title="Wartungspreis ändern"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editWartungspreisModal">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════════════════
     MODAL – Gerätepreis bearbeiten
════════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="editGeraetepreisModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gerätepreis ändern</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="gp_priceID">

                <div class="mb-3">
                    <label class="form-label" for="gp_date">Datum</label>
                    <input type="text" class="form-control" id="gp_date" placeholder="JJJJ-MM-TT"/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="gp_quelle">Verfahren</label>
                    <select class="form-select" id="gp_quelle">
                        <option value="" disabled>Verfahren wählen</option>
                        <option value="Direktvergabe">Direktvergabe</option>
                        <option value="Direktvergabe mit vorheriger Bekanntmachung">Direktvergabe mit vorheriger
                            Bekanntmachung
                        </option>
                        <option value="Verhandlungsverfahren ohne Bekanntmachung">Verhandlungsverfahren ohne
                            Bekanntmachung
                        </option>
                        <option value="Nicht offenes Verfahren ohne Bekanntmachung">Nicht offenes Verfahren ohne
                            Bekanntmachung
                        </option>
                        <option value="Nicht offenes Verfahren mit Bekanntmachung">Nicht offenes Verfahren mit
                            Bekanntmachung
                        </option>
                        <option value="Offenes Verfahren">Offenes Verfahren</option>
                        <option value="Verhandlungsverfahren mit Bekanntmachung">Verhandlungsverfahren mit
                            Bekanntmachung
                        </option>
                        <option value="MKF">MKF</option>
                        <option value="RV">RV</option>
                        <option value="Andere">Andere</option>
                    </select>
                    <input type="text" class="form-control mt-2" id="gp_quelleAndere"
                           placeholder="Verfahren beschreiben…" style="display:none;"/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="gp_menge">Menge</label>
                    <input type="text" class="form-control" id="gp_menge"/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="gp_ep">EP</label>
                    <input type="text" class="form-control" id="gp_ep" placeholder="Dezimaltrennzeichen: ."/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="gp_nk">NK/Stk</label>
                    <input type="text" class="form-control" id="gp_nk" placeholder="Dezimaltrennzeichen: ."/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="gp_kommentar">Kommentar</label>
                    <textarea class="form-control" id="gp_kommentar" rows="2" maxlength="255"
                              placeholder="Preisgestaltungsrelevanter Kontext…"></textarea>
                </div>
                <input type="hidden" id="gp_geraeteID">
                <div class="mb-3">
                    <label class="form-label" for="gp_project">Projekt</label>
                    <select class="form-select" id="gp_project">
                        <option value="0">Kein Projekt</option>
                        <?php foreach ($rows_proj as $p): ?>
                            <option value="<?= (int)$p['idTABELLE_Projekte'] ?>">
                                <?= htmlspecialchars($p['Interne_Nr'] . ' – ' . $p['Projektname'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="gp_lieferant">Lieferant</label>
                    <select class="form-select" id="gp_lieferant">
                        <option value="0">Lieferant auswählen</option>
                        <?php foreach ($rows_lief as $l): ?>
                            <option value="<?= (int)$l['idTABELLE_Lieferant'] ?>">
                                <?= htmlspecialchars($l['Lieferant'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-success" id="saveGeraetepreis">
                    <i class="fas fa-save me-1"></i>Änderungen speichern
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════════════════
     MODAL – Wartungspreis bearbeiten
════════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="editWartungspreisModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Wartungspreis ändern</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="wp_wartungID">
                <input type="hidden" id="wp_geraeteID">

                <div class="mb-3">
                    <label class="form-label" for="wp_date">Datum</label>
                    <input type="text" class="form-control" id="wp_date" placeholder="JJJJ-MM-TT"/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="wp_wartungsart">Wartungsart</label>
                    <select class="form-select" id="wp_wartungsart">
                        <option value="0">Betriebswartung</option>
                        <option value="1">Vollwartung</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="wp_menge">Geräte Anzahl</label>
                    <input type="number" class="form-control" id="wp_menge" min="0"/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="wp_preis">Preis / Jahr (1 Stk)</label>
                    <input type="text" class="form-control" id="wp_preis" placeholder="Dezimaltrennzeichen: ."/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="wp_info">Info / Kommentar</label>
                    <textarea class="form-control" id="wp_info" rows="2" maxlength="255"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="wp_lieferant">Lieferant</label>
                    <select class="form-select" id="wp_lieferant">
                        <option value="0">Lieferant auswählen</option>
                        <?php foreach ($rows_lief as $l): ?>
                            <option value="<?= (int)$l['idTABELLE_Lieferant'] ?>">
                                <?= htmlspecialchars($l['Lieferant'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-success" id="saveWartungspreis">
                    <i class="fas fa-save me-1"></i>Änderungen speichern
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        // ── Tooltip init ──────────────────────────────────────────────────────
        $('[data-bs-toggle="tooltip"]').tooltip();


        // ── Gerätepreise DataTable ────────────────────────────────────────────
        $('#tblGeraetepreise').DataTable({
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-outline-dark bg-white',
                    title: 'Gerätepreise',
                    exportOptions: {columns: ':not(:last-child)'}
                },
                {extend: 'colvis', className: 'btn btn-sm btn-outline-dark bg-white'}
            ],
            layout: {
                topStart: 'buttons',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: ['pageLength', 'paging']
            },
            language: {url: 'https://cdn.datatables.net/plug-ins/2.2.1/i18n/de-DE.json'},
            pageLength: 25,
            lengthMenu: [25, 50, 100, 500, -1],
            order: [[4, 'desc']],
            columnDefs: [
                {targets: [0, 4], visible: false},
                {targets: [9, 7, 8], className: 'text-end'},
                {targets: [-1], orderable: false, searchable: false}   // Edit-Spalte
            ],
            initComplete: function () {
                const w = $('#tblGeraetepreise_wrapper');
                w.find('.dt-search label').remove();
                w.find('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .appendTo('#CardHeader-geraete');
                w.find('.dt-buttons')
                    .addClass('btn-group btn-group-sm ms-1 me-1')
                    .appendTo('#CardHeader-geraete');
            }
        });

        // ── Wartungspreise DataTable ──────────────────────────────────────────
        $('#tblWartung').DataTable({
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-outline-dark bg-white',
                    title: 'Wartungspreise',
                    exportOptions: {columns: ':not(:last-child)'}
                },

                {extend: 'colvis', className: 'btn btn-sm btn-outline-dark bg-white'}
            ],
            layout: {
                topStart: 'buttons',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: ['pageLength', 'paging']
            },
            language: {url: 'https://cdn.datatables.net/plug-ins/2.2.1/i18n/de-DE.json'},
            pageLength: 25,
            lengthMenu: [25, 50, 100, 500, -1],
            order: [[0, 'asc']],
            columnDefs: [
                {targets: [0, 4], visible: false},
                {targets: [10, 9], className: 'text-end'},
                {targets: [-1], orderable: false, searchable: false}     // Edit-Spalte
            ],
            initComplete: function () {
                const w = $('#tblWartung_wrapper');
                w.find('.dt-search label').remove();
                w.find('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .appendTo('#CardHeader-wartung');
                w.find('.dt-buttons')
                    .addClass('btn-group btn-group-sm ms-1 me-1')
                    .appendTo('#CardHeader-wartung');
                $('#CardHeader-wartung').addClass('d-none');
            }
        });

        $('#preisTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            const target = $(this).data('bs-target');
            if (target === '#tab-wartung') {
                $('#CardHeader-geraete').addClass('d-none');
                $('#CardHeader-wartung').removeClass('d-none');
                $('#tblWartung').DataTable().columns.adjust().draw(false);
            } else {
                $('#CardHeader-wartung').addClass('d-none');
                $('#CardHeader-geraete').removeClass('d-none');
                $('#tblGeraetepreise').DataTable().columns.adjust().draw(false);
            }
        });


        // ══════════════════════════════════════════════════════════════════════
        // GERÄTEPREIS – Modal befüllen
        // ══════════════════════════════════════════════════════════════════════
        const bekannteVerfahren = [
            'Direktvergabe', 'Direktvergabe mit vorheriger Bekanntmachung',
            'Verhandlungsverfahren ohne Bekanntmachung', 'Nicht offenes Verfahren ohne Bekanntmachung',
            'Nicht offenes Verfahren mit Bekanntmachung', 'Offenes Verfahren',
            'Verhandlungsverfahren mit Bekanntmachung', 'MKF', 'RV', 'Andere'
        ];

        $('#gp_quelle').on('change', function () {
            if ($(this).val() === 'Andere') {
                $('#gp_quelleAndere').show().focus();
            } else {
                $('#gp_quelleAndere').hide().val('');
            }
        });

        $(document).on('click', '.edit-geraetepreis-btn', function () {
            const row = $(this).closest('tr');
            $('#gp_priceID').val(row.data('price-id'));
            $('#gp_geraeteID').val(row.data('geraete-id'));
            $('#gp_date').val(row.data('date'));

            const gespeichertesVerfahren = row.data('quelle');
            if (bekannteVerfahren.includes(gespeichertesVerfahren)) {
                $('#gp_quelle').val(gespeichertesVerfahren).trigger('change');
            } else {
                $('#gp_quelle').val('Andere').trigger('change');
                $('#gp_quelleAndere').val(gespeichertesVerfahren);
            }

            $('#gp_menge').val(row.data('menge'));
            $('#gp_ep').val(row.data('ep'));
            $('#gp_nk').val(row.data('nk'));
            $('#gp_kommentar').val(row.data('kommentar'));
            $('#gp_project').val(row.data('project-id') || '0');
            $('#gp_lieferant').val(row.data('lieferant-id') || '0');
        });

        function formatDateDE(isoDate) {
            if (!isoDate) return '–';
            const [y, m, d] = isoDate.split('-');
            return d + '.' + m + '.' + y;
        }

        $('#saveGeraetepreis').on('click', function () {
            const geraeteID = $('#gp_geraeteID').val();
            const priceID = $('#gp_priceID').val();
            const date = $('#gp_date').val();
            const quelle = $('#gp_quelle').val() === 'Andere' ? $('#gp_quelleAndere').val() : $('#gp_quelle').val();
            const menge = $('#gp_menge').val();
            const ep = $('#gp_ep').val().replace(',', '.');
            const nk = $('#gp_nk').val().replace(',', '.');
            const kommentar = $('#gp_kommentar').val();
            const project = $('#gp_project').val();
            const lieferant = $('#gp_lieferant').val();

            if (!date || !quelle || !menge || !ep || lieferant <= 0) {
                alert('Bitte alle Pflichtfelder ausfüllen!');
                return;
            }

            $.ajax({
                url: 'updateDevicePrice.php',
                type: 'POST',
                data: {priceID, geraeteID, date, quelle, menge, ep, nk, project, lieferant, preiskommentar: kommentar},
                success: function (data) {
                    bootstrap.Modal.getInstance(document.getElementById('editGeraetepreisModal')).hide();
                    makeToaster(data.trim(), true);

                    const dt = $('#tblGeraetepreise').DataTable();
                    const priceID = $('#gp_priceID').val();
                    const tr = $('tr[data-price-id="' + priceID + '"]');

                    // Update data attributes
                    tr.data('date', $('#gp_date').val());
                    tr.data('quelle', quelle);
                    tr.data('menge', menge);
                    tr.data('ep', ep);
                    tr.data('nk', nk);
                    tr.data('kommentar', kommentar);
                    tr.data('project-id', project);
                    tr.data('lieferant-id', lieferant);

                    const projektText = $('#gp_project option:selected').text().replace(' – ', ' ');
                    const lieferantText = lieferant > 0 ? $('#gp_lieferant option:selected').text() : '–';
                    const [y, m, d] = date.split('-');
                    const ts = Math.floor(new Date(y, m - 1, d).getTime() / 1000);

                    tr.find('td[data-col="datum"]').text(formatDateDE(date)).attr('data-order', ts);
                    tr.find('td[data-col="quelle"]').text(quelle);
                    tr.find('td[data-col="menge"]').text(parseInt(menge).toLocaleString('de-AT')).attr('data-order', menge);
                    tr.find('td[data-col="ep"]').text(parseFloat(ep).toLocaleString('de-AT', {minimumFractionDigits: 2})).attr('data-order', ep);
                    tr.find('td[data-col="nk"]').text(parseFloat(nk).toLocaleString('de-AT', {minimumFractionDigits: 2})).attr('data-order', nk);
                    tr.find('td[data-col="projekt"]').text(projektText || '–');
                    tr.find('td[data-col="lieferant"]').text(lieferantText);
                    tr.find('td[data-col="kommentar"]').text(kommentar);

                    dt.row(tr).invalidate().draw(false);
                },
                error: function () {
                    makeToaster('Fehler beim Speichern!', false);
                }
            });
        });


        // ══════════════════════════════════════════════════════════════════════
        // WARTUNGSPREIS – Modal befüllen
        // ══════════════════════════════════════════════════════════════════════
        $(document).on('click', '.edit-wartungspreis-btn', function () {
            const row = $(this).closest('tr');
            $('#wp_wartungID').val(row.data('wartung-id'));
            $('#wp_geraeteID').val(row.data('geraete-id'));

            $('#wp_date').val(row.data('date'));
            $('#wp_wartungsart').val(row.data('wartungsart') || '0');
            $('#wp_menge').val(row.data('menge'));
            $('#wp_preis').val(row.data('preis-jahr'));
            $('#wp_info').val(row.data('info'));
            $('#wp_lieferant').val(row.data('lieferant-id') || '0');
        });

        $('#saveWartungspreis').on('click', function () {
            const wartungID = $('#wp_wartungID').val();
            const geraeteID = $('#wp_geraeteID').val();
            const date = $('#wp_date').val();
            const art = $('#wp_wartungsart').val();
            const menge = $('#wp_menge').val();
            const preis = $('#wp_preis').val().replace(',', '.');
            const info = $('#wp_info').val();
            const lieferant = $('#wp_lieferant').val();

            if (!date || !menge || !preis) {
                alert('Bitte Datum, Menge und Preis ausfüllen!');
                return;
            }

            $.ajax({
                url: 'updateWartungspreis.php',
                type: 'POST',
                data: {wartungID, geraeteID, date, wartungsart: art, menge, preis, info, lieferant},
                success: function (data) {
                    bootstrap.Modal.getInstance(document.getElementById('editWartungspreisModal')).hide();
                    makeToaster(data.trim(), true);

                    const dt = $('#tblWartung').DataTable();
                    const tr = $('tr[data-wartung-id="' + wartungID + '"]');

                    tr.data('date', date);
                    tr.data('wartungsart', art);
                    tr.data('menge', menge);
                    tr.data('preis-jahr', preis);
                    tr.data('info', info);
                    tr.data('lieferant-id', lieferant);

                    const artText = art === '0' ? 'Betriebswartung' : 'Vollwartung';
                    const lieferantText = lieferant > 0 ? $('#wp_lieferant option:selected').text() : '–';
                    const [wy, wm, wd] = date.split('-');
                    const wts = Math.floor(new Date(wy, wm - 1, wd).getTime() / 1000);

                    tr.find('td[data-col="lieferant"]').text(lieferantText);
                    tr.find('td[data-col="info"]').text(info);
                    tr.find('td[data-col="datum"]').text(formatDateDE(date)).attr('data-order', wts);
                    tr.find('td[data-col="wartungsart"]').text(artText);
                    tr.find('td[data-col="menge"]').text(parseInt(menge).toLocaleString('de-AT')).attr('data-order', menge);
                    tr.find('td[data-col="preis"]').text(parseFloat(preis).toLocaleString('de-AT', {minimumFractionDigits: 2})).attr('data-order', preis);

                    dt.row(tr).invalidate().draw(false);
                },
                error: function () {
                    makeToaster('Fehler beim Speichern!', false);
                }
            });
        });

    });
</script>
<script src="utils/_utils.js"></script>
</body>
</html>