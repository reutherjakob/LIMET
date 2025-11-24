<?php
global $mysqli;
require_once "../Nutzerlogin/_utils.php";
if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}
$role = init_page(["internal_rb_user", "spargefeld_ext_users", "spargefeld_admin"]);


?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
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
    <title>Kontakt & Impressum - LIMET ZT GmbH</title>


</head>
<body>
<div id="limet-navbar"></div>
<?php require_once "../Nutzerumfrage/_utils.php"; ?>

<div class="d-flex justify-content-center align-items-center">
    <div class="card mx-5 col-6 text-center">
        <div class="card-header">
            <h1> Kontakt & Impressum</h1>
        </div>
        <div class="card-body">
            <h4>Impressum</h4>
            <p>LIMET Consulting und Planung ZT GmbH<br>
                Kaiserstraße 8/9, 1070 Wien, Österreich</p>
            <p><strong>Firmenbuchnummer:</strong> FN 123456a<br>
                <strong>Firmenbuchgericht:</strong> Handelsgericht Wien<br>
                <strong>UID-Nummer:</strong> ATU12345678<br>
                <strong>Unternehmensgegenstand:</strong> Ziviltechnische Beratung, Planung und Projektmanagement</p>
            <p>Die Gesellschaft ist Mitglied der Kammer der Ziviltechnikerinnen und Ziviltechniker.</p>

            <h4>Kontakt</h4>
            <div class="contact-info">
                <p><strong>Telefon:</strong> +43 1 470 48 33</p>
                <p><strong>E-Mail:</strong> <a href="mailto:office@limet.at">office@limet.at</a></p>
                <p><strong>Adresse:</strong> Kaiserstraße 8/9, 1070 Wien</p>
                <a class="text-dark" href="https://www.limet.at" target="_blank" rel="noopener"> © 2025 LIMET Consulting und Planung ZT GmbH.</a>
            </div>


        </div>
    </div>
</div>
</body>
</html>
