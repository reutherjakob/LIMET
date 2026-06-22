<?php
require_once "_utils.php";
init_page_serversides("x");
function renderIconTable(array $icons): string
{
    foreach ($icons as $row) {
        $html .= '<tr>';
        $html .= '<td>' . $row['icon'] . '</td>';
        $html .= '<td>' . htmlspecialchars($row['bedeutung'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '</tr>';
    }

    return $html;
}

$icons = [
    ['icon' => '<i class="fas fa-sync-alt"></i>', 'bedeutung' => 'Aktualisieren'],
    ['icon' => '<i class="fas fa-file-excel"></i>', 'bedeutung' => 'Download als Excel'],
    ['icon' => "<i class='far fa-file-pdf'></i>", 'bedeutung' => 'Download als PDF'],
    ['icon' => '<i class="fas fa-cog "></i>', 'bedeutung' => 'Einstellungen'],
    ['icon' => '<i class="fas fa-plus-square"></i> <i class="fas fa-plus"></i>', 'bedeutung' => 'Hinzufügen/Neu anlegen'],
    ['icon' => '<i class="fas fa-fingerprint"></i>', 'bedeutung' => 'ID'],
    ['icon' => '<i class="far fa-comments"></i> <i class="far fa-comment"></i>', 'bedeutung' => 'Kommentar'],
    ['icon' => '<i class="fas fa-euro-sign"></i>', 'bedeutung' => 'Kosten/Preis'],
    ['icon' => '<i class="far fa-sticky-note "></i>', 'bedeutung' => 'Notiz'],
    ['icon' => '<i class="far fa-save"></i> <i class="fas fa-save"></i>', 'bedeutung' => 'Speichern'],
    ['icon' => '<i class="fab fa-periscope "></i>', 'bedeutung' => 'Standort'],
    ['icon' => '<i class="far fa-calendar-alt "></i>', 'bedeutung' => 'Termin/-kalender'],
    ['icon' => '<i class="fas fa-code-branch "></i>', 'bedeutung' => 'Workflow'],
    ['icon' => '<i class="fas fa-history"></i>', 'bedeutung' => 'Zeitlicher Verlauf/ Änderungen'],
];
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Icon Legende</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
</head>

<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-center fs-1">
                        <strong>
                            <i class="fas fa-info"></i>
                            <i class="far fa-copyright"></i>
                            <i class="fas fa-ring"></i>
                            <i class="fas fa-shekel-sign"></i>
                            <i class="fab fa-stripe-s"></i></strong>
                    </div>
                    <div class="text-muted fs-6 d-flex justify-content-center">
                        <i class="fas fa-lira-sign"></i>egende <i class="fas fa-equals"></i> Work in Progress
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                            <tr>
                                <th>Icon</th>
                                <th>Bedeutung</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php echo renderIconTable($icons);
                            ?>

                            </tbody>
                        </table>
                    </div>


                </div>
                <div class="card-footer ">
                    <p class="d-flex justify-content-between text-muted">
                        <i class="fab fa-sass"></i>
                        <i class="fas fa-paint-roller"> </i>
                        <i class="fas fa-skull-crossbones"></i>
                        <i class="fab fa-gripfire"></i>
                        <i class="fas fa-pencil-ruler"></i>
                        <i class="fas fa-drafting-compass"></i>
                        <i class="fas fa-beer"></i>
                        <i class="fas fa-chess"></i>
                        <i class="fas fa-dice-d20"></i>
                        <i class="fas fa-hand-peace"></i>
                        <i class="fas fa-fist-raised"></i>
                        <i class="fas fa-fire-extinguisher"></i>
                        <i class="fas fa-spray-can"></i>
                        <i class="fas fa-magic"></i>
                        <i class="fas fa-quidditch"></i>
                        <i class="fas fa-rainbow"></i>
                        <i class="fas fa-ring"></i>
                        <i class="fas fa-film"></i>
                        <i class="fas fa-award"></i>
                        <i class="fas fa-heart-broken"></i>
                        <i class="fas fa-icicles"></i>
                        <i class="fab fa-octopus-deploy"></i>
                        <i class="fas fa-hand-middle-finger"></i>
                        <i class="fas fa-grin-tongue-wink"></i>
                        <i class="fab fa-sith"></i>
                        <i class="fas fa-tools"></i>
                        <i class="fas fa-filter"></i>
                        <i class="fas fa-cogs"></i>
                        <i class="fab fa-sass"></i>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>