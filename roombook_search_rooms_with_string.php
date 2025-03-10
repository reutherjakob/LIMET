<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
init_page_serversides("x");
?>

<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB.RaumSuche</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png"/>

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
        <div class="card-header d-flex align-items-center" id='searchDbCardHeader0'>Raumsuche</div>
        <div class="card-header d-flex align-items-center" id='searchDbCardHeader'>
            <label for="fieldSelect">Select Field:</label>
            <select id="fieldSelect" class="form-select">
            </select>
            <label for="searchInput"> Search String:</label>
            <input type="text" id="searchInput" class="form-control">

            <button id="searchButton" class="btn btn-primary mt-1">Search</button>
        </div>
        <div class="card-body" id="cardx">
            <table id="table_rooms" class="table table-striped table-hover border border-light border-5 display" >

            </table>
        </div>
    </div>
    <div class="card">
        <div class="card d-inline-flex">
            <header class="card-header"><b> Elemente im Raum </b> &emsp;(Kostenberechnung basierend auf den Daten des aktiven
                Projektes!)
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

    document.getElementById('searchButton').addEventListener('click', function () {
        const selectedField = document.getElementById('fieldSelect').value;
        const searchString = document.getElementById('searchInput').value;
        if ($.fn.DataTable.isDataTable('#table_rooms')) {
            $('#table_rooms').DataTable().destroy();
            $('#table_rooms tbody').empty();
        }
        $("#roomElements").empty();
        $.ajax({
            url: 'get_rooms_via_namesearch.php',
            type: 'GET',
            data: {
                field: selectedField,
                search: searchString
            },
            success: function (response) {
                table = $('#table_rooms').DataTable({
                    data: response,
                    columns: columnsDefinition,
                    layout: {
                        topStart: {
                            searchBuilder: {
                                depthLimit: 3
                            },
                            buttons: ['colvis']
                        },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: {
                            paging: 'simple_numbers'
                        }
                    },
                    scrollX: true,
                    scrollCollapse: true,
                    select: "os",
                    fixedColumns: {
                        start: 2
                    },
                    language: {
                        search: "",
                        searchPlaceholder: "Suche...",
                        searchBuilder: {
                            title: null,
                            button: {
                                0: '',
                                _: ' (%d)'
                            },
                        }
                    },
                    keys: true,
                    order: [[3, 'asc']],
                    stateSave: false,
                    info: true,
                    paging: true,
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 20, 50],
                        ['5 Zeilen', '10 Zeilen', '20 Zeilen', '50 Zeilen']
                    ],
                    compact: true,
                    stripeClasses: ['odd', 'even'],
                    responsive: true
                });
            },
            error: function (xhr, status, error) {
                console.error('Error: ' + error);
            }
        });

        $(document).on('click', '#table_rooms tbody tr', function () {
            let temp = parseInt(table.row($(this)).data()['idTABELLE_Räume'], 10);
            if (temp !== parseInt(RaumID, 10)) {
                RaumID = table.row($(this)).data()['idTABELLE_Räume'];
                call_elements_table(RaumID);
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


    const columnsDefinition = [// NEW FIEL? - ADD Here, In get_rb_specs_data.php and the CPY/save methods
        {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', xvisible: false, searchable: false},
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
        {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', xvisible: false},
        {data: 'Raumbereich Nutzer', title: 'Raumbereich', xvisible: false},
        {data: 'Geschoss', title: 'Geschoss', xvisible: false},
        {data: 'Bauetappe', title: 'Bauetappe', xvisible: false},
        {data: 'Bauabschnitt', title: 'Bauabschnitt', xvisible: false},

        {data: 'Nutzfläche', title: 'Nutzfläche', xvisible: false, case: "num"},
//    {data: 'Abdunkelbarkeit', title: 'Abdunkelbar', xvisible: false, case: "bit"},
        {data: 'Strahlenanwendung', title: 'Strahlenanw.', xvisible: false, case: "bit"},
//    {data: 'Laseranwendung', title: 'Laseranw.', xvisible: false, case: "bit"},
//    {data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse', xvisible: false},
//    {data: 'Raumhoehe', title: 'Raumhoehe', xvisible: false, case: "num"},
//    {data: 'Raumhoehe 2', title: 'Raumhoehe2', xvisible: false, case: "num"},
//    {data: 'Belichtungsfläche', title: 'Belichtungsfläche', xvisible: false, case: "num"},
//    {data: 'Umfang', title: 'Umfang', xvisible: false, case: "num"},
//    {data: 'Volumen', title: 'Volumen', xvisible: false, case: "num"},
//    //HKLS
        {data: 'H6020', title: 'H6020', xvisible: false},
//    {data: 'GMP', title: 'GMP', xvisible: false},
//    {data: 'ISO', title: 'ISO', xvisible: false},
        {data: 'HT_Waermeabgabe_W', title: 'Wärmeabgabe[W]', xvisible: false, case: ""},
//    {data: 'HT_Raumtemp Sommer °C', title: 'Raumtemp Sommer °C', xvisible: false, case: "num"},
//    {data: 'HT_Raumtemp Winter °C', title: 'Raumtemp Winter °C', xvisible: false, case: "num"},
//    {data: 'HT_Spuele_Stk', title: 'Spüle [Stk]', xvisible: false, case: "num"},
//    {data: 'HT_Kühlwasser', title: 'Kühlwasser', xvisible: false, case: "num"},
//    {data: 'HT_Notdusche', title: 'Notdusche', xvisible: false, case: "num"},
//    {data: 'HT_Tempgradient_Ch', title: 'Tempgradient C/h', xvisible: false, case: "num"},
//
//    //ET
        {data: 'Anwendungsgruppe', title: 'Raum Gruppe', xvisible: false},
//    {data: 'Fussboden OENORM B5220', title: 'B5220', xvisible: false},
        {data: 'AV', title: 'AV', xvisible: false, case: "bit"},
        {data: 'SV', title: 'SV', xvisible: false, case: "bit"},
        {data: 'ZSV', title: 'ZSV', xvisible: false, case: "bit"},
        {data: 'USV', title: 'USV', xvisible: false, case: "bit"},
//    {data: 'EL_AV Steckdosen Stk', defaultContent: '-', title: 'AV #SSD', xvisible: false, case: "num"},
//    {data: 'EL_SV Steckdosen Stk', defaultContent: '-', title: 'SV #SSD', xvisible: false, case: "num"},
//    {data: 'EL_ZSV Steckdosen Stk', defaultContent: '-', title: 'ZSV #SSD', xvisible: false, case: "num"},
//    {data: 'EL_USV Steckdosen Stk', defaultContent: '-', title: 'USV #SSD', xvisible: false, case: "num"},
//    {data: 'EL_Roentgen 16A CEE Stk', title: 'CEE16A Röntgen', xvisible: false, case: "num"},
//    {data: 'EL_Laser 16A CEE Stk', defaultContent: '-', title: 'CEE16A Laser', xvisible: false, case: "num"},
        {
            data: 'ET_Anschlussleistung_W',
            defaultContent: '-',
            title: 'Anschlussleistung Summe[W]',
            xvisible: false,
            case: "num"
        },
        {
            data: 'ET_Anschlussleistung_AV_W',
            defaultContent: '-',
            title: 'Anschlussleistung AV[W]',
            xvisible: false,
            case: "num"
        },
        {
            data: 'ET_Anschlussleistung_SV_W',
            defaultContent: '-',
            title: 'Anschlussleistung SV[W]',
            xvisible: false,
            case: "num"
        },
        {
            data: 'ET_Anschlussleistung_ZSV_W',
            defaultContent: '-',
            title: 'Anschlussleistung ZSV[W]',
            xvisible: false,
            case: "num"
        },
        {
            data: 'ET_Anschlussleistung_USV_W',
            defaultContent: '-',
            title: 'Anschlussleistung USV[W]',
            xvisible: false,
            case: "num"
        },
//    {data: 'IT Anbindung', title: 'IT', xvisible: false, case: "bit"},
//    {data: 'ET_RJ45-Ports', title: 'RJ45-Ports', xvisible: false, case: "num"},
//    {data: 'Laserklasse', title: 'Laserklasse', xvisible: false},
//
//    //AR 
//    {data: 'AR_AP_permanent', title: 'AR AP permanent', name: 'AR AP permanent ', xvisible: false, case: "bit", render: function (data) {
//            return data === '1' ? 'permanenter AP' : 'kein perma AP';
//        }},
        {
            data: 'AR_Statik_relevant',
            title: 'AR Statik relevant',
            name: 'AR Statik relevant',
            xvisible: false,
            case: "bit",
            render: function (data) {
                return data === '1' ? 'relevant' : 'nicht rel.';
            }
        },
//
//    {data: 'AR_Empf_Breite_cm', defaultContent: '-', title: 'Empf. Breite [cm]', xvisible: false, case: "num"},
//    {data: 'AR_Empf_Tiefe_cm', defaultContent: '-', title: 'Empf. Tiefe [cm]', xvisible: false, case: "num"},
//    {data: 'AR_Empf_Hoehe_cm', defaultContent: '-', title: 'Empf. Höhe [cm]', xvisible: false, case: "num"},
//    {data: 'AR_Flaechenlast_kgcm2', defaultContent: '-', title: 'Flaechenlast [kg/cm2]', xvisible: false, case: "num"},
//
//    //MEDGASE
        {data: '1 Kreis O2', title: '1_K O2', xvisible: false, case: "bit"},
        {data: '2 Kreis O2', title: '2_K O2', xvisible: false, case: "bit"},
        {data: 'CO2', title: 'CO2', xvisible: false, case: "bit"},
        {data: '1 Kreis Va', title: '1_K Va', xvisible: false, case: "bit"},
        {data: '2 Kreis Va', title: '2_K Va', xvisible: false, case: "bit"},
        {data: '1 Kreis DL-5', title: '1_K DL5', xvisible: false, case: "bit"},
        {data: '2 Kreis DL-5', title: '2_K DL5', xvisible: false, case: "bit"},
//    {data: 'DL-10', title: 'DL-10', xvisible: false, case: "bit"},
//    {data: 'DL-tech', title: 'DL-tech', xvisible: false, case: "bit"},
//    {data: 'NGA', title: 'NGA', xvisible: false, case: "bit"},
//    {data: 'N2O', title: 'N2O', xvisible: false, case: "bit"},
//    {data: 'VEXAT_Zone', title: 'VEXAT Zone', xvisible: false, case: "bit"},
//
//    //LABORZ
//    {data: 'O2', title: 'O2', xvisible: false, case: "bit"},
//    {data: 'O2 l/min', title: 'O2_l/min', xvisible: false, case: "num"},
//    {data: 'O2 Reinheit', title: 'O2 Reinheit', xvisible: false, case: ""},
//
//    {data: 'CO2 l/min', title: 'CO2_l/min', xvisible: false},
//    {data: 'CO2 Reinheit', title: 'CO2 Reinheit', xvisible: false, case: ""},
//
//    {data: 'VA', title: 'VA', xvisible: false, case: "bit"},
//    {data: 'VA l/min', title: 'VA_l/min', xvisible: false, case: "num"},
//
//    {data: 'H2', title: 'H2', xvisible: false, case: "bit"},
//    {data: 'H2 Reinheit', title: 'H2 Reinheit', xvisible: false, case: ""},
//    {data: 'H2 l/min', title: 'H2_l/min', xvisible: false, case: "num"},
//
//    {data: 'He', title: 'He', xvisible: false, case: "bit"},
//    {data: 'He Reinheit', title: 'He Reinheit', xvisible: false, case: ""},
//    {data: 'He l/min', title: 'He_l/min', xvisible: false, case: "num"},
//    {data: 'He-RF', title: 'He-RF', xvisible: false, case: "bit"},
//    {data: 'LHe', title: 'LHe', xvisible: false, case: "bit"},
//
//    {data: 'Ar', title: 'Ar', xvisible: false, case: "bit"},
//    {data: 'Ar Reinheit', title: 'Ar Reinheit', xvisible: false, case: ""},
//    {data: 'Ar l/min', title: 'Ar_l/min', xvisible: false, case: "num"},
//
//    {data: 'LN', title: 'LN', xvisible: false, case: "bit"},
//    {data: 'LN l/Tag', title: 'LN l/Tag', xvisible: false, case: "num"},
//
//    {data: 'N2', title: 'N2', xvisible: false, case: "bit"},
//    {data: 'N2 Reinheit', title: 'N2 Reinheit', xvisible: false, case: ""},
//    {data: 'N2 l/min', title: 'N2_l/min', xvisible: false, case: "num"},
//
//    {data: 'DL-5', title: 'DL-5', xvisible: false, case: "bit"},
//    {data: 'DL ISO 8573', title: 'DL_ISO 8573', xvisible: false, case: "bit"},
//    {data: 'DL l/min', title: 'DL_l/min', xvisible: false, case: "num"},
//
//    {data: 'Kr', title: 'Kr', xvisible: false, case: 'bit'},
//    {data: 'Ne', title: 'Ne', xvisible: false, case: 'bit'},
//    {data: 'NH3', title: 'NH3', xvisible: false, case: 'bit'},
//    {data: 'C2H2', title: 'C2H2', xvisible: false, case: 'bit'},
//    {data: 'Propan_Butan', title: 'Propan_Butan', xvisible: false, case: 'num'},
//    {data: 'N2H2', title: 'N2H2', xvisible: false, case: 'num'},
//    {data: 'Inertgas', title: 'Inertgas', xvisible: false, case: 'num'},
//    {data: 'Ar_CO2_Mix', title: 'AR CO2 Mix', xvisible: false, case: 'num'},
//    {data: 'ArCal15', title: 'ArCal15', xvisible: false, case: 'num'},
//
//    {data: 'O2_Mangel', title: 'O2_Mangel', xvisible: false, case: 'num'},
//    {data: 'CO2_Melder', title: 'CO2_Melder', xvisible: false, case: 'num'},
//    {data: 'NH3_Sensor', title: 'NH3_Sensor', xvisible: false, case: 'num'},
//    {data: 'H2_Sensor', title: 'H2_Sensor', xvisible: false, case: 'num'},
//
//    {data: 'O2_Sensor', title: 'O2_Sensor', xvisible: false, case: 'num'},
//    {data: 'Acetylen_Melder', title: 'Acetylen_Melder', xvisible: false, case: 'num'},
//
//    {data: 'Blitzleuchte', title: 'Blitzleuchte', xvisible: false, case: 'num'},
//
//    {data: 'ET_PA_Stk', title: 'ET PA Stk', xvisible: false, case: "num"},
//    {data: 'ET_16A_3Phasig_Einzelanschluss', title: '16A 3Ph Einzelanschluss', xvisible: false, case: "num"},
//    {data: 'ET_32A_3Phasig_Einzelanschluss', title: '32A 3Ph Einzelanschluss', xvisible: false, case: "num"},
//    {data: 'ET_64A_3Phasig_Einzelanschluss', title: '64A 3Ph Einzelanschls', xvisible: false, case: "num"},
//    {data: 'ET_Digestorium_MSR_230V_SV_Stk', title: 'Digestorium_MSR 230V_SV_Stk', xvisible: false, case: "num"},
//    {data: 'ET_5x10mm2_Digestorium_Stk', title: '5x10mm2 Digestorium_Stk', xvisible: false, case: "num"},
//    {data: 'ET_5x10mm2_USV_Stk', title: '5x10mm2 USV_Stk', xvisible: false, case: "num"},
//    {data: 'ET_5x10mm2_SV_Stk', title: '5x10mm2 SV_Stk', xvisible: false, case: "num"},
//    {data: 'ET_5x10mm2_AV_Stk', title: '5x10mm2 AV_Stk', xvisible: false, case: "num"},
//    {data: 'EL_Not_Aus_Funktion', title: 'Not Aus Funktion', xvisible: false, case: ""},
//    {data: 'EL_Not_Aus', title: 'Not Aus Stk', xvisible: false, case: 'num'},
//
//    {data: 'HT_Luftwechsel 1/h', title: 'Luftwechsel l/h', xvisible: false, case: "num"},
//
//    {data: 'HT_Abluft_Vakuumpumpe', title: 'Abluft Vakuumpumpe', xvisible: false, case: "num"},
//    {data: 'HT_Abluft_Sicherheitsschrank_Stk', title: 'Abluft Sicherheitsschrank Stk', xvisible: false, case: "num"},
//    {data: 'HT_Abluft_Schweissabsaugung_Stk', title: 'Abluft Schweissabsaugung_Stk', xvisible: false, case: "num"},
//    {data: 'HT_Abluft_Esse_Stk', title: 'Abluft Esse_Stk', xvisible: false, case: "num"},
//    {data: 'HT_Abluft_Rauchgasabzug_Stk', title: 'Abluft Rauchgasabzug_Stk', xvisible: false, case: "num"},
//    {data: 'HT_Abluft_Digestorium_Stk', title: 'Abluft Digestorium_Stk', xvisible: false, case: "num"},
//    {data: 'HT_Punktabsaugung_Stk', title: 'Punktabsaugung_Stk', xvisible: false, case: "num"},
//    {data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', title: 'Abluft Sicherheitsschrank_Unterbau_Stk', xvisible: false, case: "num"},
//
//    {data: 'HT_Abwasser_Stk', title: 'Abwasser Stk', xvisible: false, case: "num"},
//
//    {data: 'HT_Abluft_Geraete', title: 'Abluft Geräte', xvisible: false, case: "num"},
//
//    {data: 'VE_Wasser', title: 'VE_Wasser', xvisible: false, case: 'num'},
//    {data: 'HT_Warmwasser', title: 'Warmwasser', xvisible: false, case: "num"},
//    {data: 'HT_Kaltwasser', title: 'Kaltwasser', xvisible: false, case: "num"},
//    {data: 'Wasser Qual 1 l/Tag', title: 'H20_Q1 l/Tag', xvisible: false, case: "num"},
//    {data: 'Wasser Qual 2 l/Tag', title: 'H20_Q2 l/Tag', xvisible: false, case: "num"},
//    {data: 'Wasser Qual 3 l/min', title: 'H2O_Q3 l/min', xvisible: false, case: "num"},
//    {data: 'Wasser Qual 3', title: 'H20 Q3', xvisible: false, case: "bit"},
//    {data: 'Wasser Qual 2', title: 'H20 Q2', xvisible: false, case: "bit"},
//    {data: 'Wasser Qual 1', title: 'H20 Q1', xvisible: false, case: "bit"}
    ];
</script>
</body>

