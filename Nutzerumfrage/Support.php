<?php
global $mysqli;
require_once "../Nutzerlogin/_utils.php";

if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}
$role = init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_admin", "spargelfeld_view"]);

?>

<!DOCTYPE html>
<html lang="de" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
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
</head>

<div id="limet-navbar"></div>
<?php require_once "../Nutzerumfrage/_utils.php"; ?>

<body class="">
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-inline-flex justify-content-center">
            <h1> Support</h1>
        </div>
        <div class="card-body">
            <p>
                Für inhaltliche Fragen Kontaktieren Sie bitte Ihre geschätzte Nutzervertretung.
            </p>
            <p>
                Im Falle von Website/Login/Usability Problemen kontaktieren Sie gerne:
                W. Fuchs; <a href="mailto:fuchs@limet.at">fuchs@limet.at</a>; Tel: +431470483316
            </p>


            <!---div class="contact">
                <p>Für labortechnische Fragen kontaktieren Sie bitte: LIMET Consulting und Planung ZT GmbH<br>
                    Kaiserstraße 8/9, 1070 Wien<br>
                    E-Mail: <a href="mailto:office@limet.at">office@limet.at</a><br>
                    Tel: +43 1 470 48 33
                    <a class="text-dark" href="https://www.limet.at" target="_blank" rel="noopener"> limet.at </a>
                </p>
            </div>

            <div class="contact">
                <p>Für Fragen zur Elektroversorgung oder HKLS kontaktieren Sie bitte:<br>
                </p>
            </div --->

        </div>
    </div>

</div>
</body>
