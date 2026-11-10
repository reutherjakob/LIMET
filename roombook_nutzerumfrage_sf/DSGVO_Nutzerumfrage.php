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

<div class="card mx-5">
    <div class="card-header">
        Website von
        <strong class="text-success"> LIMET Consulting und Planung ZT GmbH</strong>
    </div>
    <div class="card-body">
        <div class="contact">
            <p>Technischer Support: Website/Login/Usability Probleme? Kontaktieren Sie gerne: W. Fuchs;
                <a href="mailto:fuchs@limet.at" class="text-success">fuchs@limet.at </a>; Tel: +431470483316
            </p>
            <p> Firmenkontakt: <br> Kaiserstraße 8/9, 1070 Wien<br>
                E-Mail: <a class="text-success" href="mailto:office@limet.at">office@limet.at</a><br>
                Tel: +43 1 470 48 33
                <a class="text-success" href="https://www.limet.at" target="_blank"> limet.at </a>
            </p>
        </div>
    </div>
</div>


<div class="card mx-5">
    <div class="card-header">
        <h4> Datenschutzerklärung</h4>
    </div>
    <div class="card-body">
        <p>
            Um Ihnen ein bestmögliches Erlebnis auf unserer Website zu ermöglichen, erfassen wir bewusst
            keine personenbezogenen Daten und allgemein nur jene Daten, die für den technischen Ablauf benötigt werden.
            Da Datenschutz ein uns
            wichtiges Anliegen ist, speichern wir jenseits der technisch notwendigen Daten nichts und verarbeiten ihre
            Daten nur dann, wenn es zwingend notwendig ist und dann ausschließlich im Rahmen der gesetzlichen
            Bestimmungen (DSGVO, DSG, TKG 2003). </p>

        <p>
            Personenbezogene Daten nach DSGVO Art. 4 sind solche, die sich auf eine natürliche Person beziehen (Name,
            Adresse) oder einen Rückschluss auf eine Person zulassen (z.B. die IP-Adresse). Nach der
            Datenschutz-Grundverordnung (EU) 2016/679 (folgend kurz als „DSGVO“) hat jede Person das Recht auf Schutz
            bei der Verarbeitung personenbezogener Daten. Im weiWteren Verlauf werden gemäß DSGVO folgende Fragen so
            verständlich und einfach wie möglich geklärt: </p>
        <ul>
            <li><strong>Welche Daten werden erhoben und zu welchem Zweck werden sie verarbeitet?</strong><br>
                Prinzipiell werden lediglich die notwendigsten Daten von uns ausschließlich für festgelegte,
                eindeutige und legitime Zwecke erhoben und werden nicht weitergegeben.
                Weitere Details sind unten gelistet.
            </li>
            <li><strong>Wer verarbeitet die Daten?</strong><br>
                Wir verarbeiten die Daten selbst bzw. die Hosting Provider verarbeiten die von Ihnen aufgezeichneten
                Daten
            </li>
            <li><strong>Wie kann ein Widerspruch eingelegt/ eine Löschung/ Änderung beantragt werden?</strong><br>
                Ein Widerspruch, Löschung oder Berichtigung wird formlos beim Verantwortlichen beantragt; bei Erfolg
                muss der Verantwortliche die Verarbeitung einstellen und Daten löschen, sofern keine vorrangigen Gründe
                vorliegen.
            </li>
            <li><strong>Wie lange werden die jeweiligen Daten gespeichert?</strong><br>Daten werden nur so lange
                gespeichert, wie sie für die Verarbeitungszwecke erforderlich sind.
            </li>
            <li><strong>Auf welcher Rechtsgrundlage basiert die Verarbeitung der Daten?</strong><br>Die Verarbeitung
                basiert auf Einwilligung, Vertragserfüllung, gesetzlicher Pflicht, öffentlichem Interesse oder
                berechtigten Interessen gemäß Art. 6 DSGVO.
            </li>

        </ul>


        <h3>Welche Daten werden erhoben und zu welchem Zweck?</h3>
        <h4>IP-Adressen bei Logins</h4>
        <p>
            Zur Absicherung des Logins der Website werden bei Anmeldeversuchen temporär die verwendete IP-Adresse
            sowie der Zeitpunkt des Zugriffs gespeichert.
            Diese Verarbeitung erfolgt, um technische Sicherheitsfunktionen wie
            den Schutz unserer Systeme vor z.B. Brute-Force-Attacken zu ermöglichen.
            Da wir u.a. pseudonymisierte Zugangsdaten vergeben, welche von diversen Personen genutzt werden könnten,
            ist
            die Erfassung der IP-Adressen ebenso relevant, um die Dokumentation und Nachvollziehbarkeit der Änderung
            von
            projektrelevanten Daten zu gewährleisten.
            Die in diesem Zusammenhang gespeicherte personenbezogene IP-Adresse wird nur für diesen berechtigten
            Dokumentations- und Sicherheitszweck verwendet, nicht mit anderen Datenquellen zusammengeführt und nach
            Ablauf des entsprechenden Projektes gelöscht.

        </p>

        <h4>Webserver-Logfiles</h4>
        <p>
            Unser Webhosting-Anbieter erhebt bei jedem Aufruf unserer Website sogenannte „Webserver-Logfiles“ (kurz
            Logfiles). Diese werden aus Gründen der Betriebssicherheit, zur Fehlerbehebung und zur Erstellung von
            Zugriffsstatistiken erhoben. Sie enthalten die aufgerufene Seite (limet-rb.at), den verwendeten Browser
            inkl. Version, das verwendete Betriebssystem, die zuvor besuchte Seite, den Hostnamen und die IP-Adresse
            des
            zugreifenden Rechners sowie die Uhrzeit der Serveranfrage.
            Unser Webhosting-Anbieter (Plesk) speichert diese Daten und löscht sie dann automatisch. Solange sie
            gespeichert sind, werden sie DSGVO-konform behandelt.
        </p>

        <h4>Cookies</h4>
        <p>
            Wir möchten die Erfassung von persönlichen Daten auf ein Minimum reduzieren. Deshalb
            werden von unserer Seite keine eigenen Cookies implementiert. Die Seite verwendet
            lediglich einen technisch notwendigen Session-Cookie (PHPSESSID), der automatisch
            durch die verwendete PHP-Servertechnologie gesetzt wird.
            Dieses Cookie ist für einige PHP-Applikationen technisch notwendig und dient dem Wiedererkennen der
            Benutzenden. Wird dieser Cookie deaktiviert, kommt es zu leichten Einschränkungen bei Diensten, die auf
            PHP basieren. Wird bei Sitzungsende gelöscht (wenn die Seite verlassen oder geschlossen wird).
            Sie können die Seite auch komplett ohne
            die Speicherung von Cookies verwenden, möglicherweise führt das aber zu einer leicht eingeschränkten
            Funktionalität.
        </p>


        <h4>Rechtsgrundlage</h4>
        <p>
            In der DSGVO Art. 6 wird beschrieben, unter welchen Bedingungen die Verarbeitung personenbezogener Daten
            rechtmäßig ist. Diese beinhalten unter anderem folgende: </p>
        <ul>
            <li> Die betroffene Person hat ihre Einwilligung zu der Verarbeitung gegeben.
                Dies gilt etwa für die Nutzung des technisch notwendigen Session-Cookies (PHPSESSID).
            </li>
            <li> Die Verarbeitung ist für die Erfüllung eines Vertrags oder zu Durchführung vorvertraglicher Maßnahmen
                erforderlich. Darunter fällt die Nutzung unserer Website. Diese stellt eine Dienstleistung an die
                BesucherInnen dar und für die Erfüllung dieser ist die Verarbeitung einiger weniger Daten notwendig.
            </li>
        </ul>
        <p>
            Bezüglich Ihrer personenbezogenen Daten haben Sie grundsätzlich die Rechte auf Auskunft, Berichtigung,
            Löschung, Einschränkung der Verarbeitung, Datenübertragbarkeit sowie Widerspruch (Art. 15 bis 21 DSGVO).
            Beruht die Rechtmäßigkeit einer Datenverarbeitung auf Ihrer Einwilligung, so haben Sie das Recht, die
            Einwilligung jederzeit zu widerrufen. Durch den Widerruf wird die Rechtmäßigkeit der Verarbeitung, die
            auf
            Basis der Einwilligung bis zum Zeitpunkt des Widerrufs erfolgt ist, nicht berührt (Art. 7 Abs. 3
            DSGVO).
        </p>

        <h4>Kontaktaufnahme</h4>
        <p>
            Wenn Sie Kontakt mit uns aufnehmen, erfordert dies eine Datenübertragung Ihrerseits (Telefonnummer bei
            einem
            Anruf, E-Mail-Adresse etc.). Die Verarbeitung Ihrer Daten ist in diesem Fall notwendig, um Ihre Anfrage
            zu
            beantworten und anschließende Folgefragen oder Anliegen zu bearbeiten. Mit der Kontaktaufnahme stimmen
            Sie
            der Verarbeitung Ihrer Daten also zu. Selbstverständlich geben wir diese Daten nicht ohne Ihre
            Einwilligung
            an Dritte weiter.
            Telefonnummern werden mit jedem neuen Jahresbeginn automatisch aus unserem System gelöscht, Ihre
            Telefonnummer wird also spätestens nach einem Jahr gelöscht. Sie haben das Recht, jederzeit die Löschung
            zu
            beantragen. Wir werden uns umgehend darum kümmern, machen Sie aber darauf aufmerksam, dass eine
            Kontaktaufnahme von unserer Seite dann nicht mehr möglich ist.
        </p>


    </div>
</div>

</body>
</html>

