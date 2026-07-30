<?php
/**
 * card_load_image_preview.php – Projektgalerie (Single Source of Truth)
 *
 * Features:
 *  - Karte 1: Fotos DIESES Projekts (Ursprungsprojekt ODER zugeordnet)
 *  - Karte 2: Fotos ANDERER Projekte (mit "zu Projekt übernehmen")
 *  - Filter: Raum, Vermerk-Gruppe, Gerät, "ohne Zuordnung", Freitext
 *  - Sortierung: Neueste / Älteste / Name A→Z
 *  - Hover-Overlay mit Aktions-Buttons (Info, Zoom, Löschen/Entfernen,
 *    Raum, Vermerk, Gerät, Projekt-Zuordnung)
 *  - Badges: Raum-, Vermerk-, Geräte-Anzahl
 *  - Bulk-Modus (nur eigenes Projekt): Raum zuordnen, Löschen
 *  - reloadProjectGallery() als globale Funktion (JS)
 */
global $mysqli;
$projectID = (int)($projectID ?? $_SESSION['projectID'] ?? 0);

require_once __DIR__ . '/_gallery_helpers.php';

// ── Bilder DIESES Projekts (Ursprung ODER zugeordnet) ────────────────────────
$stmt = $mysqli->prepare("
    SELECT f.`idtabelle_Files`, f.`Name`, f.`Timestamp`,
           f.`tabelle_projekte_idTABELLE_Projekte` AS originID
    FROM `LIMET_RB`.`tabelle_Files` f
    WHERE f.`tabelle_filetype_id` = 1
      AND (
            f.`tabelle_projekte_idTABELLE_Projekte` = ?
         OR EXISTS (
                SELECT 1 FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Projekte` fp
                WHERE fp.`tabelle_Files_idtabelle_Files` = f.`idtabelle_Files`
                  AND fp.`tabelle_projekte_idTABELLE_Projekte` = ?
            )
      )
    ORDER BY f.`Timestamp` DESC
");
$stmt->bind_param('ii', $projectID, $projectID);
$stmt->execute();
$ownImages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Bilder ANDERER Projekte (nicht im aktuellen Projekt) ─────────────────────
$stmt = $mysqli->prepare("
    SELECT f.`idtabelle_Files`, f.`Name`, f.`Timestamp`,
           f.`tabelle_projekte_idTABELLE_Projekte` AS originID,
           p.`Projektname`
    FROM `LIMET_RB`.`tabelle_Files` f
    LEFT JOIN `LIMET_RB`.`tabelle_projekte` p
        ON p.`idTABELLE_Projekte` = f.`tabelle_projekte_idTABELLE_Projekte`
    WHERE f.`tabelle_filetype_id` = 1
      AND f.`tabelle_projekte_idTABELLE_Projekte` <> ?
      AND NOT EXISTS (
            SELECT 1 FROM `LIMET_RB`.`tabelle_Files_has_tabelle_Projekte` fp
            WHERE fp.`tabelle_Files_idtabelle_Files` = f.`idtabelle_Files`
              AND fp.`tabelle_projekte_idTABELLE_Projekte` = ?
        )
    ORDER BY p.`Projektname`, f.`Timestamp` DESC
");
$stmt->bind_param('ii', $projectID, $projectID);
$stmt->execute();
$otherImages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Relationen laden (Räume/Vermerke/Geräte) ─────────────────────────────────
$ownRel   = gallery_load_relations($mysqli, array_column($ownImages,   'idtabelle_Files'));
$otherRel = gallery_load_relations($mysqli, array_column($otherImages, 'idtabelle_Files'));

// Filter-Dropdowns basieren auf den Bildern DIESES Projekts
$allRaeume  = $ownRel['allRaeume'];
$allGruppen = $ownRel['allGruppen'];
$allGeraete = $ownRel['allGeraete'];
?>

<!-- ══════════════════════════════════════════════════════════════════════════
     KARTE 1 – Fotos dieses Projekts
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="mt-1 card" id="projGalleryCard">

    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <b><i class="fas fa-images me-1"></i> Projektfotos
                <span class="badge bg-secondary ms-1" id="galleryCntBadge"><?= count($ownImages) ?></span>
            </b>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <button type="button" id="bulkToggleBtn" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-check-square me-1"></i> Auswählen
                </button>
                <div id="bulkActions" class="d-none d-flex gap-1 align-items-center">
                    <span class="text-muted small me-1" id="bulkCountLabel">0 gewählt</span>
                    <button type="button" id="bulkRoomBtn" class="btn btn-outline-primary btn-sm" disabled>
                        <i class="fas fa-door-open me-1"></i> Raum
                    </button>
                    <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm" disabled>
                        <i class="fas fa-trash-alt me-1"></i> Löschen
                    </button>
                    <button type="button" id="bulkSelectAllBtn" class="btn btn-outline-dark btn-sm">Alle</button>
                    <button type="button" id="bulkCancelBtn" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="button" id="addProjectImage" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-plus"></i> Bild hinzufügen
                </button>
            </div>
        </div>

        <!-- Filter-Toolbar -->
        <div class="mt-2 d-flex flex-wrap gap-2 align-items-center" id="galleryFilterBar">
            <select id="galleryRaumFilter" class="form-select form-select-sm" style="max-width:200px;">
                <option value="">Alle Räume</option>
                <option value="__none__">— Kein Raum zugeordnet</option>
                <?php foreach ($allRaeume as $rId => $rLabel): ?>
                    <option value="<?= (int)$rId ?>"><?= htmlspecialchars($rLabel) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="galleryVermerkFilter" class="form-select form-select-sm" style="max-width:220px;">
                <option value="">Alle Vermerke</option>
                <option value="__none__">— Kein Vermerk zugeordnet</option>
                <?php foreach ($allGruppen as $gId => $gLabel): ?>
                    <option value="<?= (int)$gId ?>"><?= htmlspecialchars($gLabel) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="galleryGeraetFilter" class="form-select form-select-sm" style="max-width:220px;">
                <option value="">Alle Geräte</option>
                <option value="__none__">— Kein Gerät zugeordnet</option>
                <?php foreach ($allGeraete as $gId => $gLabel): ?>
                    <option value="<?= (int)$gId ?>"><?= htmlspecialchars($gLabel) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="gallerySortSelect" class="form-select form-select-sm" style="max-width:160px;">
                <option value="newest">Neueste zuerst</option>
                <option value="oldest">Älteste zuerst</option>
                <option value="name">Name A→Z</option>
            </select>
            <button type="button" id="galleryResetFilter" class="btn btn-outline-secondary btn-sm d-none">
                <i class="fas fa-times me-1"></i> Filter zurücksetzen
            </button>
        </div>
    </div>

    <div class="card-body">
        <p class="text-muted fst-italic <?= empty($ownImages) ? '' : 'd-none' ?>" id="galleryEmptyHint">
            Noch keine Fotos vorhanden.
        </p>
        <p class="text-muted fst-italic d-none small" id="galleryNoResultHint">
            <i class="fas fa-filter me-1"></i> Keine Bilder entsprechen den Filterkriterien.
        </p>

        <div id="projectGallery" class="row g-2 gallery-grid">
            <?php foreach ($ownImages as $img):
                $id      = (int)$img['idtabelle_Files'];
                $isOwned = ((int)$img['originID'] === $projectID);
                gallery_render_item(
                    $img,
                    $ownRel['raum'][$id]    ?? [],
                    $ownRel['vermerk'][$id] ?? [],
                    $ownRel['geraet'][$id]  ?? [],
                    'own',
                    $isOwned
                );
            endforeach; ?>
        </div>

        <div class="mt-2 text-muted small" id="galleryCountInfo">
            <?= count($ownImages) ?> Bild<?= count($ownImages) !== 1 ? 'er' : '' ?>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     KARTE 2 – Fotos anderer Projekte
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="mt-3 card" id="projGalleryOtherCard">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <b><i class="fas fa-photo-video me-1"></i> Fotos anderer Projekte
                <span class="badge bg-secondary ms-1" id="galleryOtherCntBadge"><?= count($otherImages) ?></span>
            </b>
            <div class="input-group input-group-sm" style="max-width:280px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="galleryOtherSearch" class="form-control form-control-sm"
                       placeholder="Suche (Dateiname / Projekt)…">
            </div>
        </div>
        <p class="text-muted small mb-0 mt-1">
            <i class="fas fa-info-circle me-1"></i>
            Bilder aus anderen Projekten – mit <i class="fas fa-plus"></i> „Übernehmen"
            zusätzlich diesem Projekt zuordnen.
        </p>
    </div>
    <div class="card-body">
        <p class="text-muted fst-italic <?= empty($otherImages) ? '' : 'd-none' ?>" id="galleryOtherEmptyHint">
            Keine Fotos in anderen Projekten vorhanden.
        </p>
        <p class="text-muted fst-italic d-none small" id="galleryOtherNoResultHint">
            <i class="fas fa-filter me-1"></i> Keine Bilder entsprechen der Suche.
        </p>

        <div id="projectGalleryOther" class="row g-2 gallery-grid">
            <?php foreach ($otherImages as $img):
                $id = (int)$img['idtabelle_Files'];
                gallery_render_item(
                    $img,
                    $otherRel['raum'][$id]    ?? [],
                    $otherRel['vermerk'][$id] ?? [],
                    $otherRel['geraet'][$id]  ?? [],
                    'other',
                    false,
                    (string)($img['Projektname'] ?? '')
                );
            endforeach; ?>
        </div>

        <div class="mt-2 text-muted small" id="galleryOtherCountInfo">
            <?= count($otherImages) ?> Bild<?= count($otherImages) !== 1 ? 'er' : '' ?>
        </div>
    </div>
</div>

<!-- ── Bulk-Raum-Modal ──────────────────────────────────────────────────────── -->
<div class="modal fade" id="bulkRoomModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-door-open me-1"></i> Raum für ausgewählte Bilder</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">
                    Raum wird allen <strong id="bulkRoomCount">0</strong> ausgewählten Bildern zugeordnet.
                </p>
                <select id="bulkRoomSelect" class="form-select form-select-sm">
                    <option value="">— Raum wählen —</option>
                    <?php
                    $stmtRAll = $mysqli->prepare("
                        SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung, `Raumbereich Nutzer`
                        FROM tabelle_räume
                        WHERE tabelle_projekte_idTABELLE_Projekte = ?
                        ORDER BY Raumnr
                    ");
                    $stmtRAll->bind_param('i', $projectID);
                    $stmtRAll->execute();
                    foreach ($stmtRAll->get_result()->fetch_all(MYSQLI_ASSOC) as $r): ?>
                        <option value="<?= (int)$r['idTABELLE_Räume'] ?>">
                            <?= htmlspecialchars($r['Raumnr'] . ' – ' . ($r['Raumbereich Nutzer'] ?? '') . ' – ' . $r['Raumbezeichnung']) ?>
                        </option>
                    <?php endforeach;
                    $stmtRAll->close(); ?>
                </select>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="bulkRoomConfirmBtn" class="btn btn-primary btn-sm" disabled>
                    <i class="fas fa-plus me-1"></i> Zuordnen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Bulk-Löschen-Modal ───────────────────────────────────────────────────── -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i> Bilder löschen?
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-1 text-muted small" id="bulkDeleteBody">
                Alle ausgewählten Bilder werden unwiderruflich gelöscht.
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" id="bulkDeleteConfirmBtn" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt me-1"></i> Löschen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Styles ────────────────────────────────────────────────────────────────── -->
<style>
    .gallery-thumb-wrap:hover .gallery-overlay,
    .gallery-thumb-wrap:focus-within .gallery-overlay {
        opacity: 1 !important;
    }

    .gallery-item.bulk-selected .gallery-thumb-wrap {
        outline: 3px solid #0d6efd;
        border-radius: .375rem;
    }

    .gallery-item.bulk-mode .gallery-thumb-wrap {
        cursor: pointer;
    }
</style>
