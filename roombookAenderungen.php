<!DOCTYPE html>
<html lang="de">
<head>
    <title>Raumänderungen</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-2.2.1/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
</head>
<body>

<div id="limet-navbar"></div>
<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <h4 class="col-xxl-6">Räume </h4>
                <div class=" col-xxl-6 d-flex align-items-center justify-content-end">
                    <label for="start_date_raumänderungen">Von</label><input type="date" class="me-2 ms-2"
                                                                             id="start_date_raumänderungen"
                                                                             name="start_date">
                    <label for="end_date_raumänderungen">Bis</label><input type="date" class="me-2 ms-2"
                                                                           id="end_date_raumänderungen"
                                                                           name="end_date">
                    <button type="button" class="btn btn-info float-end" data-bs-toggle="modal"
                            data-bs-target="#infoModal">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>
            </div>

        </div>
        <div class="card-body">
            <table id="roomTable" class="table table-striped table-hover border border-5 border-5">
                <thead>
                <tr>
                    <th>Raumnr</th>
                    <th>Raumbezeichnung</th>
                    <th>Letzte Änderung</th>
                    <th>Raumnummer_Nutzer</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // 25 FX
                if (!function_exists('utils_connect_sql')) {
                    include "utils/_utils.php";
                }
                init_page_serversides("", "x");
                $mysqli = utils_connect_sql();
                $projectID = getPostInt("projectID", (int)$_SESSION["projectID"]);
                $stmt = $mysqli->prepare("
                        SELECT r.idTABELLE_Räume, r.Raumnr, r.Raumbezeichnung, r.Raumnummer_Nutzer,
                               MAX(a.Timestamp) AS last_change
                        FROM tabelle_räume r
                        LEFT JOIN tabelle_raeume_aenderungen a ON r.idTABELLE_Räume = a.raum_id
                        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                        GROUP BY r.idTABELLE_Räume
                        ORDER BY last_change DESC
                    ");
                $stmt->bind_param("i", $projectID);
                $stmt->execute();
                $rooms = $stmt->get_result();

                while ($room = $rooms->fetch_assoc()):
                    ?>
                    <tr data-room-id="<?= htmlspecialchars($room['idTABELLE_Räume']) ?>"
                        data-room-nr=" <?= htmlspecialchars($room['Raumnr']) ?>"
                        data-room-name="<?= htmlspecialchars($room['Raumbezeichnung']) ?>">
                        <td><?= htmlspecialchars($room['Raumnr']) ?></td>
                        <td><?= htmlspecialchars($room['Raumbezeichnung']) ?></td>
                        <td><?= $room['last_change'] ? date('d.m.Y H:i', strtotime($room['last_change'])) : 'Keine Änderungen' ?></td>
                        <td><?= htmlspecialchars($room['Raumnummer_Nutzer']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card collapse" id="changesCard">
        <div class="card-header bg-secondary text-white">
            <div class="row">
                <div class="col-6 col-xxl-6"> Änderungshistorie
                    <div id="PlaceholderForRoomIdentification"></div>
                </div>
                <div class="col-6 col-xxl-6 d-flex align-items-center justify-content-end">
                    <label for="start_date">Von </label><input type="date" id="start_date" name="start_date">
                    <label for="end_date">Bis </label><input type="date" id="end_date" name="end_date">
                </div>
            </div>
        </div>
        <div class="card-body" id="changesContent"></div>
    </div>

    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Raumänderungsverfolgung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>
                <div class="modal-body">
                    <p>Achtung, nicht alle Parameteränderungen werden aufgezeichnet! Derzeit NUR Änderungen der
                        folgenden
                        Parameter:</p>
                    <ul>
                        <li><strong>Raumidentifikation:</strong>
                            <ul>
                                <li>Raum ID</li>
                                <li>Raumnummer</li>
                                <li>Raumbezeichnung</li>
                                <li>Funktionelle Raumnummer</li>
                                <li>Funktionsteilstelle</li>
                                <li>Raumbereich Nutzer</li>
                                <li>Raumtyp BH</li>
                                <li>RaumnrBestand</li>
                                <li>GebaeudeBestand</li>
                            </ul>
                        </li>
                        <li><strong>Allgemeine Attribute:</strong>
                            <ul>
                                <li>Anmerkung Allgemein</li>
                                <li>Nutzfläche</li>
                                <li>Raumhöhe</li>
                                <li>Raumhöhe 2</li>
                                <li>Raumhöhe Soll</li>
                                <li>Bauphase</li>
                            </ul>
                        </li>
                        <li><strong>Technische Spezifikationen:</strong>
                            <ul>
                                <li>Abdunkelung</li>
                                <li>Strahlung</li>
                                <li>Laser</li>
                                <li>H620</li>
                                <li>GMP</li>
                                <li>ISO</li>
                                <li>Belichtungsfläche</li>
                                <li>Umfang</li>
                                <li>Volumen</li>
                                <li>Allgemeine Hygieneklasse</li>
                                <li>FB OENORM B5220</li>
                                <li>Akustik</li>
                                <li>EMV & EMV Ja/Nein</li>
                                <li>Schwingungsklasse</li>
                            </ul>
                        </li>
                        <li><strong>Medienversorgung:</strong>
                            <ul>
                                <li>1 Kreis O₂, 2 Kreis O₂ (und deren Kopien)</li>
                                <li>1 Kreis Va, 2 Kreis Va (und deren Kopien)</li>
                                <li>1 Kreis DL-5, 2 Kreis DL-5 (und deren Kopien)</li>
                                <li>DL-10 (und Kopie)</li>
                                <li>DL-tech (und Kopie)</li>
                                <li>CO₂ (und Kopie)</li>
                                <li>NGA (und Kopie)</li>
                                <li>N2O (und Kopie)</li>
                                <li>O2</li>
                                <li>VA</li>
                                <li>DL-5</li>
                                <li>H2</li>
                                <li>He</li>
                                <li>He-RF</li>
                                <li>Ar</li>
                                <li>N2</li>
                            </ul>
                        </li>
                        <li><strong>Elektrische und IT-Infrastruktur:</strong>
                            <ul>
                                <li>AV, SV, ZSV, USV (Boolesche Werte und Steckdosenanzahl)</li>
                                <li>IT-Anbindung</li>
                                <li>RJ45-Ports</li>
                                <li>Doppeldatendose Stk</li>
                                <li>Einzel-Datendose Stk</li>
                                <li>Bodendose Typ</li>
                                <li>Bodendose Stk</li>
                                <li>Kamera Stk</li>
                                <li>Lautsprecher Stk</li>
                                <li>Uhr - Wand Stk</li>
                                <li>Uhr - Decke Stk</li>
                                <li>Notlicht RZL Stk</li>
                                <li>Notlicht SL Stk</li>
                                <li>Lichtruf-Terminal Stk</li>
                                <li>Lichtruf-Steckmodul Stk</li>
                                <li>Lichtfarbe K</li>
                                <li>Jalousien</li>
                                <li>Lichtschaltung BWM</li>
                                <li>Beleuchtung dimmbar</li>
                                <li>Brandmelder Decke</li>
                                <li>Brandmelder ZwDecke</li>
                                <li>Roentgen 16A Stk</li>
                                <li>Laser 16A Stk</li>
                                <li>Leistungsbedarf W/m2</li>
                                <li>Anschlussleistung Gesamt (und für AV, SV, ZSV, USV separat)</li>
                            </ul>
                        </li>
                        <li><strong>HLK-Systeme:</strong>
                            <ul>
                                <li>Summe Kühlung W</li>
                                <li>Luftmenge m3/h</li>
                                <li>Luftwechsel 1/h</li>
                                <li>Kühlung Lüftung W</li>
                                <li>Heizlast W</li>
                                <li>Kühllast W</li>
                                <li>Fussbodenkühlung W</li>
                                <li>Kühldecke W</li>
                                <li>Fancoil W</li>
                                <li>Raumtemp Sommer °C</li>
                                <li>Raumtemp Winter °C</li>
                                <li>Notdusche</li>
                                <li>Wärmeabgabe</li>
                                <li>Wärmeabgabe W</li>
                                <li>Geräteabluft m3/h</li>
                                <li>Kühlwasserleistung W</li>
                            </ul>
                        </li>
                        <li><strong>Raumkonfiguration</strong>
                            <ul>
                                <li>AnwesendePers</li>
                                <li>Belichtung-nat</li>
                                <li>Aufenthaltsraum</li>
                            </ul>
                        </li>
                        <li><strong>Ausstattung:</strong>
                            <ul>
                                <li>AR_Ausstattung</li>
                            </ul>
                        </li>
                        <li><strong>Beleuchtung:</strong>
                            <ul>
                                <li>EL_Beleuchtung 1-5 Typ</li>
                                <li>EL_Beleuchtung 1-5 Stk</li>
                            </ul>
                        </li>
                        <li><strong>Notizen:</strong>
                            <ul>
                                <li>Anwendungsgruppe</li>
                                <li>Anmerkung MedGas</li>
                                <li>Anmerkung Elektro</li>
                                <li>Anmerkung HKLS</li>
                                <li>Anmerkung Geräte</li>
                                <li>Anmerkung FunktionBO</li>
                                <li>Anmerkung BauStatik</li>
                            </ul>
                        </li>
                        <li><strong>AR:</strong>
                            <ul>
                                <li>AR_APs</li>
                                <li>Schwingungsklasse</li>
                                <li>Akustik</li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Verstanden</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.2.1/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            const today = new Date().toISOString().split('T')[0];
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 300);
            const defaultStart = thirtyDaysAgo.toISOString().split('T')[0];

            $('#start_date_raumänderungen').val(defaultStart);
            $('#end_date_raumänderungen').val(today);
            $('#start_date').val(defaultStart);
            $('#end_date').val(today);

            const roomTable = $('#roomTable').DataTable({
                language: {url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'},
                order: [[2, 'asc']],
                select: "single",
                mark: true,
                columnDefs: [{
                    targets: 2, // Letzte Änderung Spalte
                    searchable: true
                }]
            });

            // ROW CLICK
            $('#roomTable tbody').on('click', 'tr', function () {
                $('#roomTable tbody tr').removeClass('focusedRow');
                $(this).addClass('focusedRow');
                const roomId = $(this).data('room-id');
                const roomName = $(this).data('room-name');
                const roomNR = $(this).data('room-nr');
                $('#PlaceholderForRoomIdentification').text(roomName + ' ' + roomNR);
                loadRoomChanges(roomId);
                $('#changesCard').collapse('show');
            });

            // 🔥 KORRIGIERTE FILTER FUNKTION
            function filterRooms(startDate, endDate) {
                //console.log('🔍 Filter:', startDate, 'bis', endDate);
                const startDateObj = new Date(startDate + 'T00:00:00');
                const endDateObj = new Date(endDate + 'T23:59:59');
                roomTable.columns(2).search(
                    function (value, searchIndex, rowData) {
                        if (value === 'Keine Änderungen') return false;

                        try {
                            // "31.01.2026 14:30" → Date Object
                            const [datePart, timePart] = value.split(' ');
                            const [day, month, year] = datePart.split('.');
                            const [hour, minute] = timePart.split(':');

                            const rowDate = new Date(year, month - 1, day, hour, minute);

                            return rowDate >= startDateObj && rowDate <= endDateObj;
                        } catch (e) {
                            console.error('Filter Parse Error:', value, e);
                            return false;
                        }
                    },
                    true,  // regex
                    false  // smart → AUS!
                ).draw();
            }


            $('#start_date, #end_date').on('change', function () {
                const selectedRow = $('#roomTable tbody tr.focusedRow');
                if (selectedRow.length) {
                    const roomId = selectedRow.data('room-id');
                    loadRoomChanges(roomId);

                }
            });


            // AJAX LOAD CHANGES
            function loadRoomChanges(roomId) {
                //console.log('📥 Loading room:', roomId, $('#start_date').val(), $('#end_date').val());
                $('#changesContent').html('<div class="text-center my-4"><div class="spinner-border" role="status"><span class="visually-hidden">Laden...</span></div></div>');

                $.ajax({
                    type: 'POST',
                    url: 'get_room_changes.php',
                    data: {
                        roomId: roomId,
                        startDate: $('#start_date').val() || '',
                        endDate: $('#end_date').val() || ''
                    },
                    success: function (response) {
                        //console.log('✅ AJAX OK:', response.substring(0, 200));
                        $('#changesContent').html(response);
                    },
                    error: function (xhr, status) {
                        //console.error('❌ AJAX Error:', xhr.responseText);
                        $('#changesContent').html(`
                    <div class="alert alert-danger">
                        <strong>Fehler:</strong> ${status}<br>
                        <code>${xhr.responseText.substring(0, 500)}</code>
                    </div>
                `);
                    }
                });
            }

            setTimeout(() => filterRooms(defaultStart, today), 100);
        });

    </script>
</body>
</html>
