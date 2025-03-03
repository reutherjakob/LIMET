<?php
include '_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bauangaben check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!--- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"> --->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>

    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
    <style>
        .checked {
            text-decoration: line-through;
        }
    </style>
</head>

<body style="height:100%">
<div id="limet-navbar" class=' '></div>
<div class="container-fluid">
    <div class="mt-4 card responsive">
        <div class="card-header d-inline-flex" id="CH1">BAUANGABEN CHECK &ensp;
            <button id="deleteButton">Delete Storage</button>

            <button type="button" class="btn btn-sm ms-auto" onclick="show_modal('InfoModal')">
                <i class="fa fa-circle-info"></i>
            </button>

        </div>
        <div id="CB_C1" class="table-responsive">
            <table class="table display compact table-striped table-bordered table-sm" id="table1ID"
                   style="z-index: 1; ">
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
            <div class="modal-header ">
                <h5 class="modal-title" id="helpModalLabel">Hilfe - Bauangaben Check</h5>
            </div>
            <div class="modal-body">
                <p>Checks werden nur für die ausgewählten Räume durchgeführt. Überprüfung der Bauangaben auf folgende
                    Kriterien:</p>
                <table class="table table-striped table-bordered" id="table1ID">
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
                <p>Wenn die <strong>Gleichzeitigkeit</strong> undefiniert ist, beträgt der Standardwert 1! Sowohl ET-Leistungs- als auch
                    HT-Wärmeangaben werden mit und ohne Gleichzeitigkeit überprüft und ggf. ausgegeben.</p>
                <p>Wenn der <strong>Elementparameter Netzart</strong> mehrere Angaben enthält, wird die Leistung gleichmäßig auf die beiden Netzarten aufgeteilt
                    (beispielsweise Elementparameter NA "SV/ZSV": Leistung wird 50/50 aufgeteilt). </p>
                <p>Die <strong>Checkboxen</strong> in der ersten Spalte der Prüftabelle dienen nur zur Übersicht. Der Browser speichert (bis zum Schließen),
                    welche Punkte auf der Liste abgehakt wurden. Diese Funktion ist kosmetisch und soll ermöglichen,
                    den Überblick zu behalten und zu kennzeichnen, welche Prüfungen ignoriert werden.</p>
                <p> <strong>Feedback:</strong> &ensp;Bei Problemen, Unstimmigkeiten und Wünschen wenden Sie sich bitte an das Support-Team.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>


<script src="_utils.js"></script>
<script>
    function getRoomIdsFromCurrentUrl() {
        var urlObj = new URL(window.location.href);
        var roomIDParam = urlObj.searchParams.get("roomID");
        if (roomIDParam) {
            var roomIDs = roomIDParam.split(",");
            return roomIDs;
        } else {
            return [];
        }
    }

    $(document).ready(function () {
        ids = getRoomIdsFromCurrentUrl();
        // console.log(ids);
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
                    const ROOM = parts[0].trim().split('---')[0];
                    //  console.log(ROOM);

                    const R_ID = parts[0].trim().split('---')[1];
                    //      console.log(R_ID);

                    const kathegorie = parts[1].split('->')[0].trim();
                    //   console.log(kathegorie);

                    const issue = parts[1].split('->')[1].trim();
                    //   console.log(issue);


                    roomIssues[R_ID] = roomIssues[R_ID] || [];
                    roomIssues[R_ID].push({ROOM, kathegorie, issue});
                });

                const tbody = document.getElementById('tableBody');
                for (const id in roomIssues) {
//                                            console.log(id);
                    const rows = roomIssues[id].map((item, index) => {
                        const isChecked = localStorage.getItem(`${id}-${index}`) === 'true';
                        return `<tr class="${isChecked ? 'checked' : ''}" data-id="${id}"> <td><input type='checkbox' ${isChecked ? 'checked' : ''}></td>  <td>${item.ROOM}</td>  <td>${item.kathegorie}</td><td>${item.issue}</td>   </tr>`;
                    });

                    tbody.innerHTML += rows.join('');
                }

                if (tbody.innerHTML.trim() !== '') {

                    new DataTable('#table1ID', {
                        dom: ' <"TableCardHeader"f>ti',
                        language: {
                            "search": ""
                        },
                        keys: true,
                        scrollx: true,
                        pageLength: -1,
                        compact: true,
                        savestate: true,
                        select: 'os', // Set select to 'os'
                        columns: [
                            {width: '5%'},
                            {width: '25%'},
                            {width: '20%'},
                            {width: '50%'}
                        ]
                    });
                } else {
                    document.getElementById('table1ID').style.display = 'none';
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

                // Load the saved data from local storage
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
//                                    else {console.log("No Success");}
        });
        setTimeout(function () {
            let dt_searcher = document.getElementById("dt-search-0");
            if (dt_searcher) {

                dt_searcher.parentNode.removeChild(dt_searcher);
                dt_searcher.style.float = "right";
                document.getElementById("CH1").appendChild(dt_searcher);
            }

        }, 200);


        // Attach the function to the button click event
        const deleteButton = document.getElementById('deleteButton');
        deleteButton.addEventListener('click', deleteLocalStorageItem);
    })


    function deleteLocalStorageItem() {
        localStorage.clear();
        console.log('Local storage cleared.');
    }

</script>
</html>
