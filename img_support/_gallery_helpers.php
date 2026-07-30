<?php
/**
 * _gallery_helpers.php
 *
 * Gemeinsame Funktionen für die Projektgalerie, damit die Item-Darstellung
 * NICHT zwischen card_load_image_preview.php (Initial-Render) und
 * gallery_grid.php (AJAX-Reload) auseinanderläuft.
 *
 * Enthält:
 *   - gallery_load_relations()  : lädt Räume / Vermerke / Geräte je Bild
 *   - gallery_device_label()    : baut ein sprechendes Geräte-Label
 *   - gallery_render_item()     : rendert EIN Galerie-Item (scope: own|other)
 *
 * Alle Funktionen sind gegen Doppel-Definition geschützt.
 */

if (!function_exists('gallery_device_label')) {
    /**
     * Baut ein lesbares Label aus Hersteller + Typ (Fallback: "Gerät #ID").
     */
    function gallery_device_label(array $g): string
    {
        $label = trim(($g['Hersteller'] ?? '') . ' ' . ($g['Typ'] ?? ''));
        if ($label === '') {
            $label = 'Gerät #' . (int)($g['geraetID'] ?? 0);
        }
        return $label;
    }
}

if (!function_exists('gallery_load_relations')) {
    /**
     * Lädt zu einer Liste von Bild-IDs die zugeordneten Räume, Vermerke und Geräte.
     *
     * @return array{
     *   raum: array<int, array>, vermerk: array<int, array>, geraet: array<int, array>,
     *   allRaeume: array<int,string>, allGruppen: array<int,string>, allGeraete: array<int,string>
     * }
     */
    function gallery_load_relations(mysqli $mysqli, array $imageIDs): array
    {
        $out = [
            'raum'       => [],
            'vermerk'    => [],
            'geraet'     => [],
            'allRaeume'  => [],
            'allGruppen' => [],
            'allGeraete' => [],
        ];

        $imageIDs = array_values(array_filter(array_map('intval', $imageIDs)));
        if (empty($imageIDs)) {
            return $out;
        }

        $placeholders = implode(',', array_fill(0, count($imageIDs), '?'));
        $types        = str_repeat('i', count($imageIDs));

        // ── Räume ────────────────────────────────────────────────────────────
        $stmtR = $mysqli->prepare("
            SELECT fhr.tabelle_idfFile AS fileID,
                   r.idTABELLE_Räume   AS raumID,
                   r.Raumnr, r.Raumbezeichnung
            FROM tabelle_Files_has_tabelle_Raeume fhr
            INNER JOIN tabelle_räume r ON fhr.tabelle_idRaeume = r.idTABELLE_Räume
            WHERE fhr.tabelle_idfFile IN ($placeholders)
            ORDER BY r.Raumnr
        ");
        $stmtR->bind_param($types, ...$imageIDs);
        $stmtR->execute();
        foreach ($stmtR->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
            $out['raum'][$row['fileID']][] = $row;
            $out['allRaeume'][$row['raumID']] = $row['Raumnr'] . ' – ' . $row['Raumbezeichnung'];
        }
        $stmtR->close();
        asort($out['allRaeume']);

        // ── Vermerke ─────────────────────────────────────────────────────────
        $stmtV = $mysqli->prepare("
            SELECT fhv.tabelle_Files_idtabelle_Files AS fileID,
                   v.idtabelle_Vermerke,
                   LEFT(v.Vermerktext, 60) AS Kurztext,
                   vg.idtabelle_Vermerkgruppe,
                   vg.Gruppenname, vg.Datum
            FROM tabelle_Files_has_tabelle_Vermerke fhv
            INNER JOIN tabelle_Vermerke v
                ON fhv.tabelle_Vermerke_idtabelle_Vermerke = v.idtabelle_Vermerke
            INNER JOIN tabelle_Vermerkuntergruppe vu
                ON v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = vu.idtabelle_Vermerkuntergruppe
            INNER JOIN tabelle_Vermerkgruppe vg
                ON vu.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = vg.idtabelle_Vermerkgruppe
            WHERE fhv.tabelle_Files_idtabelle_Files IN ($placeholders)
            ORDER BY vg.Datum DESC
        ");
        $stmtV->bind_param($types, ...$imageIDs);
        $stmtV->execute();
        foreach ($stmtV->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
            $out['vermerk'][$row['fileID']][] = $row;
            $out['allGruppen'][$row['idtabelle_Vermerkgruppe']] =
                $row['Gruppenname'] . ($row['Datum'] ? ' (' . $row['Datum'] . ')' : '');
        }
        $stmtV->close();

        // ── Geräte ───────────────────────────────────────────────────────────
        $stmtG = $mysqli->prepare("
            SELECT fhg.tabelle_idFile AS fileID,
                   g.idTABELLE_Geraete AS geraetID,
                   g.Typ,
                   h.Hersteller
            FROM tabelle_Files_has_tabelle_Geraete fhg
            INNER JOIN tabelle_geraete g ON fhg.tabelle_idGeraet = g.idTABELLE_Geraete
            LEFT JOIN tabelle_hersteller h
                ON h.idtabelle_hersteller = g.tabelle_hersteller_idtabelle_hersteller
            WHERE fhg.tabelle_idFile IN ($placeholders)
            ORDER BY h.Hersteller, g.Typ
        ");
        $stmtG->bind_param($types, ...$imageIDs);
        $stmtG->execute();
        foreach ($stmtG->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
            $out['geraet'][$row['fileID']][] = $row;
            $out['allGeraete'][$row['geraetID']] = gallery_device_label($row);
        }
        $stmtG->close();
        asort($out['allGeraete']);

        return $out;
    }
}

if (!function_exists('gallery_render_item')) {
    /**
     * Rendert ein einzelnes Galerie-Item.
     *
     * @param array  $img       enthält idtabelle_Files, Name, Timestamp
     * @param array  $raeume    Räume dieses Bildes
     * @param array  $vermerke  Vermerke dieses Bildes
     * @param array  $geraete   Geräte dieses Bildes
     * @param string $scope     'own'  = aktuelles Projekt (volle Aktionen)
     *                          'other'= anderes Projekt (nur Info/Zoom/Hinzufügen)
     * @param bool   $isOwned   nur bei scope 'own': true = Ursprungsprojekt,
     *                          false = nur zugeordnet (Löschen -> aus Projekt entfernen)
     * @param string $originName bei scope 'other': Name des Ursprungsprojekts
     */
    function gallery_render_item(
        array $img,
        array $raeume,
        array $vermerke,
        array $geraete,
        string $scope = 'own',
        bool $isOwned = true,
        string $originName = ''
    ): void {
        $id         = (int)$img['idtabelle_Files'];
        $hasRoom    = !empty($raeume);
        $hasVermerk = !empty($vermerke);
        $hasGeraet  = !empty($geraete);
        $isOwn      = ($scope === 'own');

        $geraetIDs  = array_map('intval', array_column($geraete, 'geraetID'));
        $raumIDs    = array_column($raeume, 'raumID');
        $gruppenIDs = array_unique(array_column($vermerke, 'idtabelle_Vermerkgruppe'));
        ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 gallery-item"
             data-image-id="<?= $id ?>"
             data-name="<?= htmlspecialchars(strtolower($img['Name'])) ?>"
             data-timestamp="<?= htmlspecialchars($img['Timestamp']) ?>"
             data-owned="<?= $isOwn && $isOwned ? '1' : '0' ?>"
             data-raumids="<?= implode(',', $raumIDs) ?>"
             data-vermerkgruppenids="<?= implode(',', $gruppenIDs) ?>"
             data-geraetids="<?= implode(',', $geraetIDs) ?>">

            <div class="position-relative gallery-thumb-wrap">

                <?php if ($isOwn): ?>
                    <!-- Bulk Checkbox (nur eigenes Projekt) -->
                    <div class="position-absolute top-0 start-0 m-1 d-none bulk-checkbox-wrap" style="z-index:20;">
                        <input type="checkbox" class="form-check-input gallery-bulk-cb"
                               data-image-id="<?= $id ?>" style="width:1.2em;height:1.2em;">
                    </div>
                <?php endif; ?>

                <!-- Hover-Overlay -->
                <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100
                            d-flex flex-column justify-content-between p-1 pe-none"
                     style="z-index:10; opacity:0; transition:opacity .18s;
                             background:rgba(0,0,0,0.38); border-radius:.375rem;">

                    <div class="d-flex justify-content-between pe-auto">
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-secondary btn-sm proj-meta-btn p-1"
                                    data-image-id="<?= $id ?>" title="Info">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <button type="button" class="btn btn-light btn-sm proj-zoom-btn p-1"
                                    data-image-id="<?= $id ?>" title="Vergrößern">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </div>

                        <?php if ($isOwn && $isOwned): ?>
                            <button type="button" class="btn btn-danger btn-sm project-gallery-delete-btn p-1"
                                    data-image-id="<?= $id ?>" title="Löschen">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        <?php elseif ($isOwn && !$isOwned): ?>
                            <button type="button" class="btn btn-warning btn-sm proj-project-unlink-btn p-1"
                                    data-image-id="<?= $id ?>" title="Aus diesem Projekt entfernen">
                                <i class="fas fa-minus-circle"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-<?= $isOwn ? 'between' : 'end' ?> gap-1 pe-auto align-items-end">
                        <?php if (!$isOwn): ?>
                            <button type="button" class="btn btn-success btn-sm proj-add-to-project-btn p-1"
                                    data-image-id="<?= $id ?>" title="Zu diesem Projekt hinzufügen">
                                <i class="fas fa-plus me-1"></i>Übernehmen
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-light btn-sm proj-project-btn p-1"
                                    data-image-id="<?= $id ?>" title="Projekte zuordnen">
                                <i class="fas fa-folder-plus"></i>
                            </button>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-outline-light btn-sm proj-geraet-btn p-1"
                                        data-image-id="<?= $id ?>" title="Gerät zuordnen">
                                    <i class="fas fa-plug"></i>
                                </button>
                                <button type="button" class="btn btn-outline-light btn-sm proj-vermerk-btn p-1"
                                        data-image-id="<?= $id ?>" title="Vermerk zuordnen">
                                    <i class="fas fa-comment-alt"></i>
                                </button>
                                <button type="button" class="btn btn-outline-light btn-sm proj-room-btn p-1"
                                        data-image-id="<?= $id ?>" title="Raum zuordnen">
                                    <i class="fas fa-door-open"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bild -->
                <img src="https://limet-rb.com/Dokumente_RB/Images/<?= htmlspecialchars($img['Name']) ?>"
                     class="project-gallery-img img-fluid rounded w-100"
                     style="height:130px; object-fit:cover; cursor:zoom-in; display:block;"
                     alt="Projektfoto">

                <!-- Badges -->
                <div class="d-flex gap-1 flex-wrap mt-1" style="min-height:1.4rem;">
                    <?php if (!$isOwn && $originName !== ''): ?>
                        <span class="badge bg-secondary" style="font-size:.6rem;"
                              title="Ursprungsprojekt">
                            <i class="fas fa-folder"></i> <?= htmlspecialchars($originName) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($hasRoom): ?>
                        <span class="badge bg-primary" style="font-size:.6rem;"
                              title="<?= htmlspecialchars(implode(', ', array_column($raeume, 'Raumnr'))) ?>">
                            <i class="fas fa-door-open"></i> <?= count($raeume) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($hasVermerk): ?>
                        <span class="badge bg-success" style="font-size:.6rem;"
                              title="<?= htmlspecialchars(implode(', ', array_unique(array_column($vermerke, 'Gruppenname')))) ?>">
                            <i class="fas fa-comment-alt"></i> <?= count($vermerke) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($hasGeraet): ?>
                        <span class="badge bg-info text-dark" style="font-size:.6rem;"
                              title="<?= htmlspecialchars(implode(', ', array_map('gallery_device_label', $geraete))) ?>">
                            <i class="fas fa-plug"></i> <?= count($geraete) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($isOwn && !$hasRoom && !$hasVermerk && !$hasGeraet): ?>
                        <span class="badge bg-light text-muted border" style="font-size:.6rem;">
                            <i class="fas fa-unlink"></i> ohne
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
