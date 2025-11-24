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

<body>

<div id="limet-navbar"></div>
<?php require_once "../Nutzerumfrage/_utils.php"; ?>


<div class="card mx-5">
    <div class="card-header">
        <h1> Datenschutzerklärung</h1>
    </div>
    <div class="card-body">

        <p>Um Ihnen ein bestmögliches Erlebnis auf unserer Website zu ermöglichen, erfassen wir wenige personenbezogene
            Daten, die für den technischen Ablauf benötigt werden. Datenschutz ist uns wichtig, daher speichern wir
            diese Daten nicht und verarbeiten sie nur, wenn es zwingend notwendig ist und ausschließlich im Rahmen der
            gesetzlichen Bestimmungen (DSGVO, DSG 2000, TKG 2003).</p>

        <p>Personenbezogene Daten nach DSGVO Art. 4 sind solche, die sich auf eine natürliche Person beziehen (Name,
            Adresse) oder einen Rückschluss auf eine Person zulassen (z.B. die IP-Adresse).</p>

        <h4>Rechtsgrundlage</h4>
        <p>In der DSGVO Art. 6 wird beschrieben, unter welchen Bedingungen die Verarbeitung personenbezogener Daten
            rechtmäßig ist. Diese beinhalten unter anderem folgende: </p>
        <ul> Die betroffene Person hat ihre Einwilligung zu der Verarbeitung gegeben. Diese Bedingung ist bspw. durch
            die Zustimmung der Cookies gegeben.
        </ul>
        <ul> Die Verarbeitung ist für die Erfüllung eines Vertrags oder zu Durchführung vorvertraglicher Maßnahmen
            erforderlich. Darunter fällt die Nutzung unserer Website. Diese stellt eine Dienstleistung an die
            BesucherInnen dar und für die Erfüllung dieser ist die Verarbeitung einiger weniger Daten notwendig.
        </ul>
        <p> Bezüglich Ihrer personenbezogenen Daten haben Sie grundsätzlich die Rechte auf Auskunft, Berichtigung,
            Löschung,
            Einschränkung der Verarbeitung, Datenübertragbarkeit sowie Widerspruch (Art. 15 bis 21 DSGVO).
            Beruht die Rechtmäßigkeit einer Datenverarbeitung auf Ihrer Einwilligung, so haben Sie das Recht, die
            Einwilligung jederzeit zu widerrufen. Durch den Widerruf wird die Rechtmäßigkeit der Verarbeitung, die auf
            Basis
            der Einwilligung bis zum Zeitpunkt des Widerrufs erfolgt ist, nicht berührt (Art. 7 Abs. 3 DSGVO). </p>


        <h3>Welche Daten werden erhoben und zu welchem Zweck?</h3>
        <h4>IP-Adressen bei Logins</h4>
        <p>Bei Anmeldeversuchen werden temporär die verwendete IP-Adresse und der Zeitpunkt gespeichert, um Systeme vor
            Angriffen zu schützen. Diese Daten werden nach einem Jahr gelöscht.</p>

        <h4>Webserver-Logfiles</h4>
        <p>Unser Webhosting-Anbieter erhebt bei jedem Aufruf unserer Website sogenannte „Webserver-Logfiles“ (kurz
            Logfiles).
            Diese werden aus Gründen der Betriebssicherheit, zur Fehlerbehebung und zur Erstellung von
            Zugriffsstatistiken erhoben.
            Sie enthalten die aufgerufene Seite (limet-rb.at),
            den verwendeten Browser inkl. Version, das verwendete Betriebssystem, die zuvor besuchte Seite,
            den Hostnamen und die IP-Adresse des zugreifenden Rechners sowie die Uhrzeit der Serveranfrage.
            Unser Webhosting-Anbieter speichert diese Daten und löscht sie dann automatisch. Solange sie gespeichert
            sind, werden sie DSGVO-konform behandelt.
        </p>

        <h4>Cookies</h4>
        <p>Nach dem Telekommunikationsgesetz (kurz TKG) hat jede Person ein Recht auf Aufklärung über gespeicherte
            Cookies, ihren Verwendungszweck und die Dauer der Verarbeitung. Das trifft auch zu, wenn es sich dabei nicht
            um personenbezogene Daten handelt.
            Wir möchten die Erfassung von persönlichen Daten auf ein Minimum reduzieren. Aus diesem Grund haben wir
            unsere Website so aufgebaut, dass kaum Cookies benötigt werden. Sie können unsere Seite auch komplett ohne
            die Speicherung von Cookies verwenden, allerdings führt das ggf. zu einer leicht eingeschränkten
            Funktionalität. Wir möchten Sie außerdem darauf aufmerksam machen, dass Sie jederzeit der Verwendung von
            Cookies widersprechen sowie bereits gespeicherte Cookies in Ihren Browser-Einstellungen löschen können.
            Um die Verwendung der erfassten Cookies so transparent wie möglich zu machen, gehen wir im Folgenden auf die
            von uns erfassten Cookies ein und erklären ihre Funktion:
        </p>
        <h5> PHPSESSID</h5>
        <p> Dieses Cookie ist für einige PHP-Applikationen technisch notwendig und dient dem Wiederzuerkennen der
            Benutzenden.
            Wird dieser Cookie deaktiviert, kommt es zu leichten Einschränkungen bei Diensten, die auf PHP basieren.
            Wird bei Sitzungsende gelöscht (wenn die Seite verlassen oder geschlossen wird).
        </p>

        <h4>Kontaktaufnahme</h4>
        <p>Wenn Sie Kontakt mit uns aufnehmen, erfordert dies eine Datenübertragung Ihrerseits (Telefonnummer bei einem
            Anruf, E-Mail-Adresse etc.). Die Verarbeitung Ihrer Daten ist in diesem Fall notwendig, um Ihre Anfrage zu
            beantworten und anschließende Folgefragen oder Anliegen zu bearbeiten. Mit der Kontaktaufnahme stimmen Sie
            der Verarbeitung Ihrer Daten also zu. Selbstverständlich geben wir diese Daten nicht ohne Ihre Einwilligung
            an Dritte weiter.
            Telefonnummern werden mit jedem neuen Jahresbeginn automatisch aus unserem System gelöscht, Ihre
            Telefonnummer wird also spätestens nach einem Jahr gelöscht. Sie haben das Recht, jederzeit die Löschung zu
            beantragen. Wir werden uns umgehend darum kümmern, machen Sie aber darauf aufmerksam, dass eine
            Kontaktaufnahme von unserer Seite dann nicht mehr möglich ist.
        </p>

        <h4>Widerspruch, Löschung, Änderung</h4>
        <p> Wenn Sie eine Verletzung Ihrer Datenschutzrechte vermuten, können Sie sich an die zuständige Datenschutzbehörde (www.dsb.gv.at) wenden.</p>
        <p>Sie können jederzeit Widerspruch einlegen, Daten löschen oder ändern lassen. Kontaktieren Sie uns dazu
            über:</p>
        <div class="contact">
            <p>LIMET Consulting und Planung ZT GmbH<br>
                Kaiserstraße 8/9, 1070 Wien<br>
                E-Mail: <a href="mailto:office@limet.at">office@limet.at</a><br>
                Tel: +43 1 470 48 33
                <a class="text-dark" href="https://www.limet.at" target="_blank" rel="noopener"> limet.at </a>
            </p>
            </p>
        </div>
    </div>
</div>

</body>
</html>

