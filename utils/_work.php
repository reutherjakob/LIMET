<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Projekte</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>


</head>

<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides("No Redirect");

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


?>

<body>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-center">
            <i class="me-2 ms-2 fas fa-dice"> </i>
            <i class="me-2 ms-2 fas fa-book-dead"> </i>
            <i class="me-2 ms-2 fas fa-ring"> </i>
            <i class="me-2 ms-2 fab fa-jedi-order"></i>
            <i class="me-2 ms-2 fas fa-award"></i>
            <i class="me-2 ms-2 fas fa-bomb"></i>
            <i class="me-2 ms-2 fas fa-pastafarianism"></i>
            <i class="me-2 ms-2 fas fa-ankh"></i>

        </div>

        <div class="card-body">
            <?php

            echo '<div class="container">';
            echo '<h2>Session-Variablen</h2>';
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

            ?>

        </div>

        <div class="card-footer">
            <?php
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
</body>

