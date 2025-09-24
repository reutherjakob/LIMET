<?php
require_once 'utils/_utils.php';
init_page_serversides("x");
check_login();
//TODO Fußboden
?>


<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB.RaumSuche</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

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


    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

</head>
<body style="height:100%">
<div id="limet-navbar" class='bla'></div>
<div id='ContainerRaumsuche' class='container-fluid'>
    <div class="card">
        <div class="card-header d-flex align-items-center text-nowrap" id='searchDbCardHeader'>
            <strong>Raumsuche</strong> &emsp;&emsp;&emsp;
            <label for="fieldSelect"></label>
            <select id="fieldSelect" class="form-select w-25">
            </select>
            <label for="searchInput"> </label>
            <input type="text" id="searchInput" class=" btn bg-white border-secondary" placeholder="Suchbegriff">
            <button id="searchButton" class="btn btn-sm btn-primary mt-1 ">Suchen</button>
        </div>

        <div class="card-body" id="cardx">
            <table id="table_rooms" class="table table-striped table-hover border border-light border-5 display">

            </table>
        </div>
    </div>
    <div class="card">
        <div class="card d-inline-flex">
            <header class="card-header">Elemente im Raum <br> <b>!ACHTUNG! Die Kostenberechnung basierend auf den
                    Preisen
                    des aktiven Projektes! Elemente, die im aktuell gewählten Projekt keinen Preis haben werden nicht
                    abgebildet. Preisbasis ist die des aktive Projektes, etc....</b>
            </header>
            <div class="card-body" id="additionalInfo">
                <p id="roomElements"></p>
                <p id="elementParameters"></p>
            </div>
        </div>
        <div class='card'>
            <header class="card-header "><b>Bauangaben</b></header>
            <div class="card-body" id="bauangaben"></div>
        </div>
    </div>
</div>

<script>
    let table;
    var RaumID;
    const fields = [
        {value: 'Raumbezeichnung', text: 'Raumbezeichnung'}
    ];
    const fieldSelect = document.getElementById('fieldSelect');
    fields.forEach(field => {
        const option = document.createElement('option');
        option.value = field.value;
        option.text = field.text;
        fieldSelect.appendChild(option);
    });
    var currentSort = {column: 0, dir: 'asc'};
    var tableRoomElements;  // tableRoomElements  && hideZeroFilter required for: getRoomELmeentsDetailed1.php
    const hideZeroFilter = function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tableRoomElements') {
            return true; // Don't filter other tables
        }
        let hideZero = $("#hideZeroRows").is(":checked");
        let row = tableRoomElements.row(dataIndex).node();
        let amount = $(row).find('input[id^="amount"]').val();
        amount = parseInt(amount) || 0;
        return !(hideZero && (amount === 0));
    }


    document.getElementById('searchButton').addEventListener('click', function () {
        const selectedField = fieldSelect.value;
        const tempsearchString = document.getElementById('searchInput').value;
        // Umlaut replacement
        const umlautMap = {
            'Ü': 'u', 'ü': 'u', 'Ö': 'o', 'ö': 'o', 'Ä': 'a', 'ä': 'a'
        };
        const searchString = tempsearchString.replace(/[ÜüÖöÄäß]/g, m => umlautMap[m]);

        if ($.fn.DataTable.isDataTable('#table_rooms')) {
            $('#table_rooms').DataTable().destroy();
            $('#table_rooms tbody').empty();
        }
        $("#roomElements, #elementParameters").empty();

        $.ajax({
            url: 'get_rooms_via_namesearch.php',
            type: 'GET',
            dataType: 'json',
            data: {field: selectedField, search: searchString},
            success: function (response) {
                table = $('#table_rooms').DataTable({
                    data: response,
                    columns: columnsDefinition,
                    scrollX: true,
                    scrollCollapse: true,
                    select: "os",
                    fixedColumns: {start: 2},
                    language: {
                        search: "",
                        searchPlaceholder: "Suche...",
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
                    },
                    keys: true,
                    order: [[3, 'asc']],
                    info: true,
                    paging: true,
                    pageLength: 20,
                    lengthMenu: [[5, 10, 20, 50], ['5 Zeilen', '10 Zeilen', '20 Zeilen', '50 Zeilen']],
                    responsive: true,
                    buttons: ['colvis', 'searchBuilder'],
                    layout: {
                        topStart: ['pageLength', 'buttons'],
                        topEnd: ['paging' , 'search','info'],
                        bottomStart: null,
                        bottomEnd: null
                    },
                    initComplete: function () {
                        table.buttons().container().appendTo('#table_rooms_wrapper .col-md-6:eq(0)');

                        $(document).on('click', '#table_rooms tbody tr', function () {
                            if (!table) return;
                            const rowData = table.row(this).data();
                            if (!rowData) return;
                            const temp = parseInt(rowData['idTABELLE_Räume'], 10);
                            if (temp !== parseInt(RaumID, 10)) {
                                RaumID = temp;
                                call_elements_table(RaumID);
                            }
                        });
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error('Error loading rooms: ' + error);
            }
        });
    });

    function call_elements_table(RaumID) {
        $("#roomElements").empty();
        $.ajax({
            url: "setSessionVariables.php",
            data: {"roomID": RaumID},
            type: "GET",
            success: function (data) {
                $("#RoomID").text(RaumID);
                $.ajax({
                    url: "getRoomElementsDetailed1.php",
                    type: "GET",
                    success: function (data) {
                        if (!data || data.trim() === "") {
                            $("#roomElements").empty();
                        } else {
                            $("#roomElements").html(data);
                            $('.btn-warning').prop('disabled', true);
                        }
                        $('#elementParameters').empty();
                        $.ajax({
                            url: "getRoomSpecifications2.php",
                            type: "GET",
                            success: function (data) {
                                $("#bauangaben").html(data);
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("AJAX call failed: " + textStatus + ", " + errorThrown);
                    }
                });
            }
        });
    }

    const columnsDefinition = [
        {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID',  searchable: false},
        {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
        {
            data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
            title: 'Funktionsstellen ID',
            visible: false,
            searchable: false
        },
        {
            data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit", render: function (data) {
                return data === 1 ? 'Ja' : 'Nein';
            }
        },
        {data: 'Raumbezeichnung', title: 'Raumbez.'},
        {data: 'Raumnr', title: 'Raumnr'},
        {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'},
        {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', visible: false},
        {data: 'Raumbereich Nutzer', title: 'Raumbereich', visible: false},
        {data: 'Geschoss', title: 'Geschoss', visible: false},
        {data: 'Bauetappe', title: 'Bauetappe', visible: false},
        {data: 'Bauabschnitt', title: 'Bauabschnitt', visible: false},
        {data: 'Nutzfläche', title: 'Nutzfläche', visible: false, case: "num"},
        {data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse', visible: false},
        {data: 'Strahlenanwendung', title: 'Strahlenanw.', visible: false, case: "bit"},
        {data: 'H6020', title: 'H6020', visible: true},
        {data: 'HT_Waermeabgabe_W', title: 'Wärmeabgabe[W]', visible: true, case: ""},
        {data: 'Anwendungsgruppe', title: 'Raum Gruppe', visible: true},
        {data: 'Fussboden OENORM B5220', title: 'B5220', visible: false},
        {data: 'AV', title: 'AV', visible: false, case: "bit"},
        {data: 'SV', title: 'SV', visible: false, case: "bit"},
        {data: 'ZSV', title: 'ZSV', visible: false, case: "bit"},
        {data: 'USV', title: 'USV', visible: false, case: "bit"},
        {
            data: 'ET_Anschlussleistung_W',
            defaultContent: '-',
            title: 'Anschlussleistung Summe[W]',
            visible: true,
            case: "num"
        },
        {
            data: 'ET_Anschlussleistung_AV_W',
            defaultContent: '-',
            title: 'Anschlussleistung AV[W]',
            visible: false,
            case: "num"
        },
        {
            data: 'ET_Anschlussleistung_SV_W',
            defaultContent: '-',
            title: 'Anschlussleistung SV[W]',
            visible: false,
            case: "num"
        },
        {
            data: 'ET_Anschlussleistung_ZSV_W',
            defaultContent: '-',
            title: 'Anschlussleistung ZSV[W]',
            visible: false,
            case: "num"
        },
        {
            data: 'ET_Anschlussleistung_USV_W',
            defaultContent: '-',
            title: 'Anschlussleistung USV[W]',
            visible: false,
            case: "num"
        },
        {
            data: 'AR_Statik_relevant',
            title: 'AR Statik relevant',
            name: 'AR Statik relevant',
            visible: false,
            case: "bit",
            render: function (data) {
                return data === '1' ? 'relevant' : 'nicht rel.';
            }
        },
        {data: '1 Kreis O2', title: '1_K O2', visible: false, case: "bit"},
        {data: '2 Kreis O2', title: '2_K O2', visible: false, case: "bit"},
        {data: 'CO2', title: 'CO2', visible: false, case: "bit"},
        {data: '1 Kreis Va', title: '1_K Va', visible: false, case: "bit"},
        {data: '2 Kreis Va', title: '2_K Va', visible: false, case: "bit"},
        {data: '1 Kreis DL-5', title: '1_K DL5', visible: false, case: "bit"},
        {data: '2 Kreis DL-5', title: '2_K DL5', visible: false, case: "bit"}
    ];

</script>
</body>

