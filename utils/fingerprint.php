<?php
// 25 FX
require_once '../utils/_utils.php';
init_page_serversides("x");
?>

<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>User Fingerprint </title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>

<div id="limet-navbar"></div>

<div class="container-fluid" id="KONTÄNER">
    <div class="card">
        <div class="card-header"> Header</div>
        <div class="card-body" id="fingerprint">

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
            ctx.fillRect(125,1,62,20);
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
