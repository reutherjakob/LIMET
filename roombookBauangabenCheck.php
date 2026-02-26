<?php
// 25 FX
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
                <div class="col-xxl-4"><b>BAUANGABEN CHECK</b></div>
                <div class="col-xxl-8 d-inline-flex justify-content-end" id="CH1">

                    <button id="deleteButton" class="btn btn-sm btn-outline-dark me-1 text-nowrap"> Markierungen
                        löschen
                    </button>
                    <button type="button" class="btn btn-sm btn-info me-1 text-nowrap"
                            onclick="show_modal('InfoModal')">
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

<?php require_once "modal_bauangaben_check_help.php"; ?>


<script src="utils/_utils.js"></script>
<script>
    function show_modal(modal_id) {
        $('#' + modal_id).modal('show');
    }

    function escHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getRoomIdsFromCurrentUrl() {
        const urlObj = new URL(window.location.href);
        const roomIDParam = urlObj.searchParams.get("roomID");
        if (roomIDParam) {
            return roomIDParam.split(",");
        } else {
            return [];
        }
    }

    $(document).ready(function () {
        const ids = getRoomIdsFromCurrentUrl();

        $.ajax({
            url: "get_angaben_check.php",
            type: "POST",
            data: { roomID: ids.join(',') },
            success: function (data) {
                const lines = data.split('\n').filter(line => line.trim() !== '');
                const roomIssues = {};

                lines.forEach(line => {
                    const parts = line.split(':::');
                    const ROOM = parts[0].trim().split('---')[0].replace(/<br\s*\/?>/g, " ");
                    const R_ID = parts[0].trim().split('---')[1];
                    const kathegorie = parts[1].split('->')[0].trim();
                    const issue = parts[1].split('->')[1].trim();
                    roomIssues[R_ID] = roomIssues[R_ID] || [];
                    roomIssues[R_ID].push({ ROOM, kathegorie, issue });
                });

                const $tbody = $('#tableBody');

                for (const id in roomIssues) {
                    const rows = roomIssues[id].map((item, index) => {
                        const isChecked = localStorage.getItem(`${id}-${index}`) === 'true';
                        const safeROOM = escHtml(item.ROOM);
                        const safeKathegorie = escHtml(item.kathegorie);
                        const safeIssue = escHtml(item.issue);
                        const safeId = escHtml(id);

                        return `<tr class="${isChecked ? 'checked' : ''}" data-id="${safeId}">
                            <td data-checked="${isChecked ? 1 : 0}">
                                <span style="display:none">${isChecked ? 1 : 0}</span>
                                <input class="form-check-input" type="checkbox" ${isChecked ? 'checked' : ''}>
                            </td>
                            <td>${safeROOM}</td>
                            <td>${safeKathegorie}</td>
                            <td>${safeIssue}</td>
                        </tr>`;
                    });

                    $tbody.append(rows.join(''));
                }

                if ($tbody.html().trim() !== '') {
                    new DataTable('#table1ID', {
                        dom: ' <"TableCardHeader"f>ti',
                        language: {
                            search: "",
                            seachPlaceholder: "Suche..."
                        },
                        keys: true,
                        scrollx: true,
                        pageLength: -1,
                        compact: true,
                        savestate: true,
                        select: 'os',
                        initComplete: function () {
                            $('.dt-search label').remove();
                            $('.dt-search').children()
                                .removeClass("form-control form-control-sm")
                                .addClass("btn btn-sm btn-outline-dark")
                                .appendTo('#CH1');

                            const problemCounts = {};
                            const kathegorieCounts = {};

                            this.api().rows().every(function () {
                                const data = this.data();
                                const problemText = data[3];
                                const kathegorieText = data[2];

                                problemCounts[problemText] = (problemCounts[problemText] || 0) + 1;
                                kathegorieCounts[kathegorieText] = (kathegorieCounts[kathegorieText] || 0) + 1;
                            });

                            const repeatedProblems = Object.entries(problemCounts)
                                .filter(([problem, count]) => count > 1)
                                .map(([problem]) => problem);

                            const repeatedKathegories = Object.entries(kathegorieCounts)
                                .filter(([kathe, count]) => count > 1)
                                .map(([kathe]) => kathe);

                            const $problemDropdown = $('<select class="form-select form-select-sm me-2" aria-label="Select problem"></select>');
                            $problemDropdown.append('<option value="" disabled selected>Problem wählen...</option>');
                            repeatedProblems.forEach(problem => {
                                const safe = escHtml(problem);
                                $problemDropdown.append(`<option value="${safe}">${safe}</option>`);
                            });

                            const $kathegorieDropdown = $('<select class="form-select form-select-sm me-2" aria-label="Select kathegorie"></select>');
                            $kathegorieDropdown.append('<option value="" disabled selected>Kathegorie wählen...</option>');
                            repeatedKathegories.forEach(kathe => {
                                const safe = escHtml(kathe);
                                $kathegorieDropdown.append(`<option value="${safe}">${safe}</option>`);
                            });

                            const $btnCrossAllProblems = $('<button class="btn btn-sm btn-outline-primary me-2 text-nowrap" type="button">Alle Probleme abhaken</button>');
                            const $btnCrossAllKathegories = $('<button class="btn btn-sm btn-outline-success me-2 text-nowrap" type="button">Alle Kathegorien abhaken</button>');

                            $('#CH1')
                                .prepend($btnCrossAllKathegories)
                                .prepend($kathegorieDropdown)
                                .prepend($btnCrossAllProblems)
                                .prepend($problemDropdown);

                            $btnCrossAllProblems.on('click', function () {
                                const selectedProblem = $problemDropdown.val();
                                if (!selectedProblem) {
                                    alert("Bitte wählen Sie ein Problem aus der Liste.");
                                    return;
                                }

                                $('#tableBody tr').each(function () {
                                    const $row = $(this);
                                    const problemText = $row.find('td').eq(3).text().trim();
                                    if (problemText === selectedProblem) {
                                        const $checkbox = $row.find('input.form-check-input').first();
                                        if (!$checkbox.prop('checked')) {
                                            $checkbox.prop('checked', true).trigger('change');
                                        }
                                    }
                                });
                            });

                            $btnCrossAllKathegories.on('click', function () {
                                const selectedKathe = $kathegorieDropdown.val();
                                if (!selectedKathe) {
                                    alert("Bitte wählen Sie eine Kathegorie aus der Liste.");
                                    return;
                                }

                                $('#tableBody tr').each(function () {
                                    const $row = $(this);
                                    const katheText = $row.find('td').eq(2).text().trim();
                                    if (katheText === selectedKathe) {
                                        const $checkbox = $row.find('input.form-check-input').first();
                                        if (!$checkbox.prop('checked')) {
                                            $checkbox.prop('checked', true).trigger('change');
                                        }
                                    }
                                });
                            });
                        },
                        columns: [
                            { width: '2%' },
                            { width: '25%' },
                            { width: '20%' },
                            { width: '50%' }
                        ],
                        createdRow: function (row, data) {
                            const kathegorie = data[2] || data.kathegorie;
                            if (kathegorie) {
                                const cleanKathegorie = kathegorie.replace(/[^a-zA-Z0-9-]/g, '-');
                                $(row).addClass('kathegorie-' + cleanKathegorie);
                            }
                        }
                    });
                } else {
                    $('#table1ID').hide();
                    alert("Keine Angaben zu checken? Oder bug?");
                }

                $('#table1ID').on('change', 'input[type="checkbox"]', function () {
                    const tr = $(this).closest('tr');
                    const id = tr.data('id');
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
                    if (index !== 0) {
                        const $row = $(row);
                        const id = $row.data('id');
                        const issue = $row.find('td:nth-child(4)').text();
                        const key = `${id}-${issue}`;
                        const isChecked = localStorage.getItem(key) === 'true';
                        if (isChecked) {
                            $row.addClass('checked');
                            $row.find('input[type="checkbox"]').prop('checked', true);
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

        $('#deleteButton').on('click', function() {
            localStorage.clear();
            console.log('Local storage cleared.');
        });
    });

    // Helper function (kept for potential use elsewhere)
    function deleteLocalStorageItem() {
        localStorage.clear();
        console.log('Local storage cleared.');
    }
</script>

</html>