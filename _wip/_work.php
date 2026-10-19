<?php
require_once "_utils.php";
init_page_serversides("x");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Works In Progress</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../_utils/style.css" type="text/css" media="screen"/>
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
<body>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    Session Variablen
                </div>
                <div class="card-body">
                    <?php
                    echo '<div class="container">';

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
            </div>
            <div class="card">
                <div class="card-header"></div>
                <?php


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

        <div class="col-6">


            <div class="card">
                <div class="card-header"> Header</div>
                <div class="card-body" id="fingerprint">

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        let fingerprint = getBrowserFingerprint();
        let fingerprintDiv = $('#fingerprint');
        for (let key in fingerprint) {
            let value = fingerprint[key];
            if (Array.isArray(value)) {
                value = value.join(', ');
            }
            fingerprintDiv.append(`<p><strong>${key}:</strong> ${value}</p>`);
        }

    });

    function getBrowserFingerprint() {
        const fingerprint = {};

        fingerprint.userAgent = navigator.userAgent || "N/A";
        fingerprint.screenResolution = `${window.screen.width}x${window.screen.height}`;
        fingerprint.colorDepth = window.screen.colorDepth || "N/A";
        fingerprint.timezoneOffset = new Date().getTimezoneOffset();
        fingerprint.language = navigator.language || navigator.userLanguage || "N/A";
        fingerprint.platform = navigator.platform || "N/A";
        fingerprint.cookiesEnabled = navigator.cookieEnabled;
        fingerprint.javaEnabled = navigator.javaEnabled();
        fingerprint.doNotTrack = navigator.doNotTrack || window.doNotTrack || navigator.msDoNotTrack || "N/A";

        fingerprint.plugins = [];
        if (navigator.plugins) {
            for (let i = 0; i < navigator.plugins.length; i++) {
                fingerprint.plugins.push(navigator.plugins[i].name);
            }
        }

        // Additional user details

        // Hardware concurrency (number of logical processors)
        fingerprint.hardwareConcurrency = navigator.hardwareConcurrency || "N/A";

        // Device memory (in GB)
        fingerprint.deviceMemory = navigator.deviceMemory || "N/A";

        // Touch support
        fingerprint.maxTouchPoints = navigator.maxTouchPoints || 0;

        // WebGL renderer info
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            if (gl) {
                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                if (debugInfo) {
                    fingerprint.webglVendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL);
                    fingerprint.webglRenderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                }
            }
        } catch (e) {
            fingerprint.webglVendor = "N/A";
            fingerprint.webglRenderer = "N/A";
        }

        // Audio context fingerprint (audio fingerprinting base)
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                const context = new AudioContext();
                const oscillator = context.createOscillator();
                const analyser = context.createAnalyser();
                oscillator.connect(analyser);
                analyser.connect(context.destination);
                oscillator.start(0);
                fingerprint.audioContextSampleRate = context.sampleRate;
                oscillator.disconnect();
                analyser.disconnect();
                context.close();
            }
        } catch (e) {
            fingerprint.audioContextSampleRate = "N/A";
        }

        fingerprint.canvasFingerprint = getCanvasFingerprint();

        return fingerprint;
    }


    function getCanvasFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = "top";
            ctx.font = "14px 'Arial'";
            ctx.textBaseline = "alphabetic";
            ctx.fillStyle = "#f60";
            ctx.fillRect(125, 1, 62, 20);
            ctx.fillStyle = "#069";
            ctx.fillText("Browser fingerprint test", 2, 15);
            ctx.fillStyle = "rgba(102, 204, 0, 0.7)";
            ctx.fillText("Browser fingerprint test", 4, 17);
            // return hash or base64 of the data URL
            return canvas.toDataURL();
        } catch (e) {
            return "N/A";
        }
    }


</script>
</body>
</html>



