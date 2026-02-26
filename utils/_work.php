<?php

require_once "_utils.php";
init_page_serversides();

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Works In Progress</title>
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
        <div class="col-6">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    <h6 class="mb-0">Icon-Legende (limet-navbar)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                            <tr>
                                <th>Icon</th>
                                <th>Code</th>
                                <th>Bedeutung (DE/EN)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><i class="fas fa-tools text-warning"></i></td>
                                <td><code>fas fa-tools</code></td>
                                <td>Wartung / Maintenance</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-hard-hat text-danger"></i></td>
                                <td><code>fas fa-hard-hat</code></td>
                                <td>Baustelle / Construction</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-clock text-info"></i></td>
                                <td><code>fas fa-clock</code></td>
                                <td>In Arbeit / In Progress</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-list text-primary"></i></td>
                                <td><code>fas fa-list</code></td>
                                <td>Aufgabenliste / Task List</td>
                            </tr>
                            <tr>
                                <td><i class="far fa-sticky-note text-secondary"></i></td>
                                <td><code>far fa-sticky-note</code></td>
                                <td>Notiz / Note</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-code-branch text-success"></i></td>
                                <td><code>fas fa-code-branch</code></td>
                                <td>Workflow / Workflow</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-fingerprint text-info"></i></td>
                                <td><code>fas fa-fingerprint</code></td>
                                <td>ID / ID</td>
                            </tr>
                            <tr>
                                <td><i class="fab fa-periscope text-warning"></i></td>
                                <td><code>fab fa-periscope</code></td>
                                <td>Standort / Location</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-euro-sign text-primary"></i></td>
                                <td><code>fas fa-euro-sign</code></td>
                                <td>Kosten / Costs</td>
                            </tr>
                            <tr>
                                <td><i class="far fa-comments text-muted"></i></td>
                                <td><code>far fa-comments</code></td>
                                <td>Kommentar / Comment</td>
                            </tr>
                            <tr>
                                <td><i class="far fa-calendar-alt text-info"></i></td>
                                <td><code>far fa-calendar-alt</code></td>
                                <td>Termin / Schedule</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-cog text-secondary"></i></td>
                                <td><code>fas fa-cog</code></td>
                                <td>Einstellungen / Settings</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-check-circle text-success"></i></td>
                                <td><code>fas fa-check-circle</code></td>
                                <td>Fertig / Completed</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-sync-alt text-muted"></i></td>
                                <td><code>fas fa-sync-alt</code></td>
                                <td>Aktualisieren / Refresh</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 small text-muted">
                        <i class="fas fa-font me-1"></i> FontAwesome 5.9.0
                    </div>
                </div>
            </div>
        </div>


        <div class="col-6 ">
            <div class="card">
                <div class="card-header"></div>
                <?php
                echo '<div class="container">';
                echo '<div>Session-Variablen</div>';
                if (!empty($_SESSION)) {
                    echo '<table class="table table-bordered"><thead><tr><th>Schlüssel</th><th>Wert</th></tr></thead><tbody>';
                    foreach ($_SESSION as $key => $value) {
                        echo '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars(print_r($value, true)) . '</td></tr>';
                    }

                    echo '</tbody></table>';
                } else {
                    echo '<p>Keine Sessions gefunden.</p>';
                }
                echo '</div>';


                function getSubDirectories($dir)
                {
                    $subDirs = [];
// Get directories in $dir
                    foreach (glob($dir . '/*', GLOB_ONLYDIR) as $directory) {
                        $subDirs[] = $directory;
// Recursively add subdirectories
                        $subDirs = array_merge($subDirs, getSubDirectories($directory));
                    }
                    return $subDirs;
                }

                function listDirs($dir)
                {
                    $result = [];
                    $handle = opendir($dir);
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != '.' && $entry != '..' && is_dir($dir . '/' . $entry)) {
                            $result[] = $dir . '/' . $entry;
                            $result = array_merge($result, listDirs($dir . '/' . $entry));
                        }
                    }
                    closedir($handle);
                    return $result;
                }


                echo $_SERVER['DOCUMENT_ROOT'] . "<br>";
                echo __DIR__ . "<br>";
                echo preg_replace("!{$_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']) . "<br>";
                $docRoot = $_SERVER['DOCUMENT_ROOT'];
                $subDirs = listDirs($docRoot);
                echo '<pre>' . print_r($subDirs, true) . '</pre>';
                $docRoot = $_SERVER['DOCUMENT_ROOT'];
                $subPaths = getSubDirectories($docRoot);
                echo "Get Subs: <br>";
                echo '<pre>' . print_r($docRoot, true) . '</pre>';
                ?>

            </div>
        </div>
    </div>
</div>


<i class="me-2 ms-2 fas fa-dice"> </i>
<i class="me-2 ms-2 fas fa-book-dead"> </i>
<i class="me-2 ms-2 fas fa-ring"> </i>
<i class="me-2 ms-2 fab fa-jedi-order"></i>
<i class="me-2 ms-2 fas fa-award"></i>
<i class="me-2 ms-2 fas fa-bomb"></i>
<i class="me-2 ms-2 fas fa-pastafarianism"></i>
<i class="me-2 ms-2 fas fa-ankh"></i>
<i class="me-2 ms-2 fas fa-toilet"></i>
<i class="me-2 ms-2 fas fa-cannabis"></i>

<i class="me-2 ms-2 fas fa-plug"> </i>
<i class="me-2 ms-2 fas fa-luggage-cart"></i>
<i class="me-2 ms-2 fas fa-heartbeat"></i>
<i class="fas fa-street-view"></i>