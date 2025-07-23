<?php
require_once 'utils/_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bauangaben check</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>

    <!-- Rework 2025 CDNs -->
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
    <style>
        .checked {
            text-decoration: line-through;
            background-color: #9f9a9a !important;
        }

        /* ET: Elektro-Technik */
        .kathegorie-ET,
        .kathegorie-Raumparameter---IT-Anbindung,
        .kathegorie-Raumparameter---Leistung,
        .kathegorie-Raumparameter---Netzarten,
        .kathegorie-Raumparameter---ElementPort,
        .kathegorie-Leistung-Elemente-in-Raum--EXKL--GLZ-,
        .kathegorie-Leistung-Elemente-in-Raum--INKL--GLZ-,
        .kathegorie-Raumparameter---Leistung----INKL--GLZ-,
        .kathegorie-Raumparameter---Leistung----EXKL--GLZ-,
        .kathegorie-Raumparameter---Elemente,
        .kathegorie-Raumparameter---RG,
        .kathegorie-Raumparameter---SummevonAnschlussleistung,
        .kathegorie-Netzarten,
        [class^="kathegorie-Raumparameter---Leistung"],
        [class^="kathegorie-Raumparameter---ET-Anschlussleistung"] {
            background-color: #deefff !important; /* Light blue */
        }

        /* HT: Heizung, Lüftung, Klima, Sanitär */
        .kathegorie-HT,
        .kathegorie-Raumparameter---Abw-rme--EXKL--GLZ-,
        .kathegorie-Raumparameter---Abw-rme--INKL--GLZ-,
        .kathegorie-Raumparameter---Digestorium,
        .kathegorie-Raumparameter---Sicherheitsschrank {
            background-color: #f2e5ff !important; /* Light orange */
        }

        /* MED-GAS: Medizingase */
        .kathegorie-MED-GAS,
        .kathegorie-Raumparameter---MED-GAS,
        .kathegorie-Raumparameter---Entnahmestelle,
        .kathegorie-Raumparameter---Gasanschluss,
        .kathegorie-Raumparameter---Stativ {
            background-color: #c0fac0 !important; /* Light green */
        }

        /* Laser */
        .kathegorie-Laser,
        .kathegorie-Raumparameter---Laseranwendung,
        .kathegorie-Röntgen,
        .kathegorie-Raumparameter---Strahlenanwendung,
        .kathegorie-Raumparameter---Röntgen,
        .kathegorie-CEE,
        .kathegorie-Raumparameter---CEE,
        .kathegorie-Raumparameter---Elemente,
        .kathegorie-Raumparameter {
            background-color: #f8dfb0 !important; /* Light yellow */
        }

        [class^="kathegorie-"] {
            background-color: #f9f9f9;
        }

    </style>
</head>

<body>
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-4 card responsive">
        <div class="card-header">
            <div class="row ">
                <div class="col-xxl-6"><b>BAUANGABEN CHECK</b></div>
                <div class="col-xxl-6 d-inline-flex justify-content-end" id="CH1">

                    <button id="deleteButton" class="btn btn-sm btn-outline-dark me-1"> Markierungen löschen</button>
                    <button type="button" class="btn btn-sm btn-info me-1" onclick="show_modal('InfoModal')">
                        <i class="fas fa-question-circle"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="CB_C1" class="table-responsive">
            <table class="table compact"
                   id="table1ID">
                <thead>
                <tr>
                    <th>-</th>
                    <th>Room</th>
                    <th>Kathegorie</th>
                    <th>Problem</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
            </table>
        </div>
    </div>

</body>


<div class="modal fade" id="InfoModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">Hilfe - Bauangaben Check</h5>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <p>Checks werden nur für die ausgewählten Räume durchgeführt. Überprüfung der Bauangaben auf folgende
                    Kriterien:</p>
                <table class="table compact table-hover table-striped table-bordered border border-5 border-light"
                       id="table1ID">
                    <thead>
                    <tr>
                        <th>Kategorie</th>
                        <th>BAUANGABEN Parameter</th>
                        <th>ÜBERPRÜFUNG von…</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>ET</th>
                        <td>Raumgruppe = 1</td>
                        <td>SV = 1</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>Raumgruppe = 2</td>
                        <td> SV = 1 & ZSV = 1 & B5220 = Klasse 1</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>AV/SV/USV/ZSV #SSD > 0</td>
                        <td>Korrespondierender Netzart Parameter AV/SV/USV/ZSV muss 1 sein.</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>AV/SV/USV/ZSV Anschlussleistung</td>
                        <td>Korrespondierender Netzart Parameter AV/SV/USV/ZSV muss 1 sein.</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>ZSV Anschlussleistung > 8kW</td>
                        <td>Trafodimensionierung bedenken!</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>RJ45 > 0</td>
                        <td>Muss IT = 1</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>RJ45 = 0</td>
                        <td>Muss IT = 0</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>∑P (Anschlussleistungen je NA)</td>
                        <td>P(gesamt) muss =< ∑P(AV/SV/ZSV/USV)</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>Netz Art (AV/SV/ZSV/USV)</td>
                        <td>Ist Element Parameter präsent, muss korrespondierendes AV/SV/USV/ZSV = 1 sein. <br>
                            #SSD und Leistung analog.
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>∑Anschlussleistungen (mit und ohne Gleichzeitigkeit) je Netz</td>
                        <td>Darf Summe angegebener Gesamtleistung je Netzart nicht überschreiten</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>∑Anschlussleistungen (mit und ohne Gleichzeitigkeit) ZSV >= 8kW</td>
                        <td>Trafodimensionierung bedenken</td>
                    </tr>

                    <tr>
                        <th>HT</th>
                        <td>∑Abwärme (mit und ohne Gleichzeitigkeit)</td>
                        <td>Muss kleiner gesamt Raum Abwärme sein</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>Element Digestorium oder SSicherheitsschrank präsent</td>
                        <td>Raum muss entsprechende Abluft =1 haben.</td>
                    </tr>

                    <tr>
                        <th>MED.-GAS</th>
                        <td>2 Kreis „GAS“</td>
                        <td>Ist Parameter präsent, muss korrespondierendes 1 Kreis „GAS“ = 1 sein.</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>Entnahmestelle</td>
                        <td>Gasanschluss muss=1 sein.</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>Elemente im Raum mit einem Gas Anschluss Parameter</td>
                        <td>Gasanschluss muss im Raum sein</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>„Stativ“ mit Gasen</td>
                        <td>Benötigt Vorabsperrkasten</td>
                    </tr>
                    <tr>
                        <th>Laser</th>
                        <td>Ist ein Laser Element im Raum präsent, muss der Raumparameter Laseranwendung =1 sein.</td>
                        <td>2.56.16.x - Laser Elemente <br>
                            2.34.19.x - OP-Laser
                        </td>
                    </tr>
                    <tr>
                        <th>Röntgen</th>
                        <td>Ist eine der nebenan gelisteten IDs präsent, dann muss der Raumarameter Strahlenanwendung =
                            1 sein.
                        </td>
                        <td>
                            1.41.10.2 - Röntgenraster Wandhalterung<br>
                            1.41.12.1 - Röntgenaufnahmesystem - Deckenstativ<br>
                            1.41.12.2 - Röntgenaufnahmesystem - 3D Deckenstativ<br>
                            1.41.12.3 - Panoramaröntgensystem - Boden/Decke<br>
                            1.42.10.1 - Röntgendiagnostik - System - Durchleuchtung<br>
                            1.42.10.5 - Uroskopie - System - Durchleuchtung<br>
                            1.42.12.1 - Angiographieanlage - Radiologisch<br>
                            1.42.13.1 - Angiographieanlage - Kardiologisch - 2 Ebenen<br>
                            1.42.13.2 - Angiographieanlage - Kardiologisch - 1 Ebene<br>
                            1.42.13.3 - Unterkonstruktion Angiographieanlage - Kardiologisch<br>
                            1.46.10.1 - Mammographie - System<br>
                            1.47.10.1 - SPECT<br>
                            1.47.15.1 - SPECT/CT<br>
                            1.49.10.1 - Kontrastmittelinjektor CT - deckenmontiert<br>
                            1.71.10.1 - Linearbeschleuniger-System<br>
                            2.41.13.1 - Röntgenaufnahmegerät digital - fahrbar<br>
                            2.41.13.4 - Röntgendetektorhalterung - fahrbar<br>
                            2.42.10.1 - C-Bogen - fahrbar<br>
                        </td>
                    </tr>

                    <tr>
                        <th>CEE Anschluss</th>
                        <td>Ist eine der nebenan gelisteten IDs präsent, dann muss Raum einen CEE Anschlussparameter=1
                            haben.
                        </td>
                        <td>
                            2.41.13.1 - Röntgenaufnahmegerät digital – fahrbar<br>
                            2.42.10.1 - C-Bogen - fahrbar
                        </td>
                    </tr>

                    </tbody>
                </table>
                <p>Wenn die <strong>Gleichzeitigkeit</strong> undefiniert ist, beträgt der Standardwert 1! Sowohl
                    ET-Leistungs- als auch
                    HT-Wärmeangaben werden mit und ohne Gleichzeitigkeit überprüft und ggf. ausgegeben.</p>
                <p>Wenn der <strong>Elementparameter Netzart</strong> mehrere Angaben enthält, wird die Leistung
                    gleichmäßig auf die beiden Netzarten aufgeteilt
                    (beispielsweise Elementparameter NA "SV/ZSV": Leistung wird 50/50 aufgeteilt). </p>
                <p>Die <strong>Checkboxen</strong> in der ersten Spalte der Prüftabelle dienen nur zur Übersicht. Der
                    Browser speichert (bis zum Schließen),
                    welche Punkte auf der Liste abgehakt wurden. Diese Funktion ist kosmetisch und soll ermöglichen,
                    den Überblick zu behalten und zu kennzeichnen, welche Prüfungen ignoriert werden.</p>
                <p><strong>Feedback:</strong> &ensp;Bei Problemen, Unstimmigkeiten und Wünschen wenden Sie sich bitte an
                    das Support-Team.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>


<script src="utils/_utils.js"></script>
<script>
    function show_modal(modal_id) {
        $('#' + modal_id).modal('show');
    }

    function getRoomIdsFromCurrentUrl() {
        let urlObj = new URL(window.location.href);
        let roomIDParam = urlObj.searchParams.get("roomID");
        if (roomIDParam) {
            return roomIDParam.split(",");
        } else {
            return [];
        }
    }

    $(document).ready(function () {
        let ids = getRoomIdsFromCurrentUrl();
        $.ajax({
            url: "get_angaben_check.php",
            type: "GET",
            data: {'roomID': ids.join(',')},
            success: function (data) {
                //  console.log(data);
                const lines = data.split('\n').filter(line => line.trim() !== '');
                const roomIssues = {};
                lines.forEach(line => {
                    const parts = line.split(':::');
                    console.log(parts);
                    const ROOM = parts[0].trim().split('---')[0].replace(/<br\s*\/?>/g, " ");   //  console.log(ROOM);
                    const R_ID = parts[0].trim().split('---')[1];                               //  console.log(R_ID);
                    const kathegorie = parts[1].split('->')[0].trim();                          //  console.log(kathegorie);
                    const issue = parts[1].split('->')[1].trim();                               //  console.log(issue);
                    roomIssues[R_ID] = roomIssues[R_ID] || [];
                    roomIssues[R_ID].push({ROOM, kathegorie, issue});
                });

                const tbody = document.getElementById('tableBody');
                for (const id in roomIssues) {
                    const rows = roomIssues[id].map((item, index) => {
                        const isChecked = localStorage.getItem(`${id}-${index}`) === 'true';
                        return `<tr class="${isChecked ? 'checked' : ''}" data-id="${id}">
                            <td data-checked="${isChecked ? 1 : 0}">
                                <span style="display:none">${isChecked ? 1 : 0}</span>
                                <input class="form-check-input" type="checkbox" ${isChecked ? 'checked' : ''}>
                            </td>
                            <td>${item.ROOM}</td>
                            <td>${item.kathegorie}</td>
                            <td>${item.issue}</td>
                        </tr>`;
                    });

                    tbody.innerHTML += rows.join('');
                }

                if (tbody.innerHTML.trim() !== '') {
                    new DataTable('#table1ID', {
                        dom: ' <"TableCardHeader"f>ti',
                        language: {
                            search: "",
                            seachPlaceholder: "Suche...",
                        },
                        keys: true,
                        scrollx: true,
                        pageLength: -1,
                        compact: true,
                        savestate: true,
                        select: 'os',
                        columns: [
                            {width: '2%'},
                            {width: '25%'},
                            {width: '20%'},
                            {width: '50%'}
                        ],
                        initComplete: function () {
                            $('.dt-search label').remove();
                            $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CH1');

                        },
                        createdRow: function (row, data) {
                            const kathegorie = data[2] || data.kathegorie;
                            if (kathegorie) {
                                const cleanKathegorie = kathegorie.replace(/[^a-zA-Z0-9-]/g, '-');
                                $(row).addClass('kathegorie-' + cleanKathegorie);
                            }
                        }
                    });
                } else {
                    document.getElementById('table1ID').style.display = 'none';
                    alert("Keine Angaben zu checken? Oder bug?");
                }

                $('#table1ID').on('change', 'input[type="checkbox"]', function () {
                    const tr = $(this).closest('tr');
                    const id = tr.data('id');
                    //   console.log(id);
                    const issue = tr.find('td:nth-child(4)').text();
                    const key = `${id}-${issue}`;

                    if (this.checked) {
                        tr.addClass('checked');
                        localStorage.setItem(key, 'true');
                    } else {
                        tr.removeClass('checked');
                        localStorage.setItem(key, 'false');
                    }
                });

                $('#table1ID tr').each(function (index, row) {
                    if (index !== 0) { // Skip the header row
                        const id = $(row).data('id');
                        const issue = $(row).find('td:nth-child(4)').text();
                        const key = `${id}-${issue}`;
                        const isChecked = localStorage.getItem(key) === 'true';
                        if (isChecked) {
                            $(row).addClass('checked');
                            $(row).find('input[type="checkbox"]').prop('checked', true);
                        }
                    }
                });
            }
        });

        $('#table1ID').on('click', 'tbody tr', function (event) {
            if (!$(event.target).is('input[type="checkbox"], a')) {
                const checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            }
        });
        const deleteButton = document.getElementById('deleteButton');
        deleteButton.addEventListener('click', deleteLocalStorageItem);
    })


    function deleteLocalStorageItem() {
        localStorage.clear();
        console.log('Local storage cleared.');
    }

</script>
</html>
