<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>RB.RaumSuche</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png"/> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>

        <style>
            /* Add any custom styles here */
        </style>
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
                    <table id="table_rooms" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <!-- Table headers will be dynamically generated -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table data will be dynamically generated -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card d-inline-flex">
                    <header class="card-header">                     </header>
                    <div class="card-body" id="additionalInfo">
                        <p id="roomElements"></p>
                        <p id="elementParameters"></p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let table; 
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
                            dom: '  <"TableCardHeader"f>t<"btm.d-flex justify-content-between"lip>   ',
                            scrollX: true,
                            scrollCollapse: true,
                            select: "os",
                            fixedColumns: {
                                start: 2
                            },
                            language: {
                                "search": "",
                                searchBuilder: {
                                    title: null,
                                    depthLimit: 2,
                                    stateSave: false,
                                }
                            },
                            keys: true,
                            order: [[3, 'asc']],
                            stateSave: false,
                            info: true,
                            paging: true,
                            pagingType: "simple_numbers",
                            pageLength: 10,
                            lengthMenu: [
                                [5, 10, 20, 50],
                                ['5 rows', '10 rows', '20 rows', '50 rows']
                            ],
                            compact: true
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error: ' + error);
                    }
                });
                $(document).on('click', '#table_rooms tbody tr', function () {
      
                    var RaumID = table.row($(this)).data()['idTABELLE_Räume'];
                    call_elements_table(RaumID);
                });
            });
            function call_elements_table(RaumID) {
                $.ajax({
                    url: "setSessionVariables.php",
                    data: {"roomID": RaumID},
                    type: "GET",
                    success: function (data) {
                        $("#RoomID").text(RaumID);
                        $.ajax({
                            url: "getRoomElementsDetailed2.php",
                            type: "GET",
                            success: function (data) {
                                var tableX = $('#myTable').DataTable();
                                tableX.destroy();
                                if (!data || data.trim() === "") {
                                    $("#roomElements").empty();
                                } else {
                                    $("#roomElements").html(data);
                                    $('#myTable').DataTable();
                                }
                                $('#elementParameters').empty();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.error("AJAX call failed: " + textStatus + ", " + errorThrown);
                            }
                        });
                    }
                });
            }



            const columnsDefinition = [// NEW FIEL? - ADD Here, In get_rb_specs_data.php and the CPY/save methods
                {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false},
                {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
                {data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', title: 'Funktionsstellen ID', visible: false, searchable: false},
                {data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit", render: function (data) {
                        return data === '1' ? 'Ja' : 'Nein';
                    }},
                {data: 'Raumbezeichnung', title: 'Raumbez.'},
                {data: 'Raumnr', title: 'Raumnr'},
                {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'},
                {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', visible: false},
                {data: 'Raumbereich Nutzer', title: 'Raumbereich', visible: false},
                {data: 'Geschoss', title: 'Geschoss', visible: false},
                {data: 'Bauetappe', title: 'Bauetappe', visible: false},
                {data: 'Bauabschnitt', title: 'Bauabschnitt', visible: false}

//    ,{data: 'Nutzfläche', title: 'Nutzfläche', visible: false, case: "num"},
//    {data: 'Abdunkelbarkeit', title: 'Abdunkelbar', visible: false, case: "bit"},
//    {data: 'Strahlenanwendung', title: 'Strahlenanw.', visible: false, case: "bit"},
//    {data: 'Laseranwendung', title: 'Laseranw.', visible: false, case: "bit"},
//    {data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse', visible: false},
//    {data: 'Raumhoehe', title: 'Raumhoehe', visible: false, case: "num"},
//    {data: 'Raumhoehe 2', title: 'Raumhoehe2', visible: false, case: "num"},
//    {data: 'Belichtungsfläche', title: 'Belichtungsfläche', visible: false, case: "num"},
//    {data: 'Umfang', title: 'Umfang', visible: false, case: "num"},
//    {data: 'Volumen', title: 'Volumen', visible: false, case: "num"},
//    //HKLS
//    {data: 'H6020', title: 'H6020', visible: false},
//    {data: 'GMP', title: 'GMP', visible: false},
//    {data: 'ISO', title: 'ISO', visible: false},
//    {data: 'HT_Waermeabgabe_W', title: 'Wärmeabgabe[W]', visible: false, case: ""},
//    {data: 'HT_Raumtemp Sommer °C', title: 'Raumtemp Sommer °C', visible: false, case: "num"},
//    {data: 'HT_Raumtemp Winter °C', title: 'Raumtemp Winter °C', visible: false, case: "num"},
//    {data: 'HT_Spuele_Stk', title: 'Spüle [Stk]', visible: false, case: "num"},
//    {data: 'HT_Kühlwasser', title: 'Kühlwasser', visible: false, case: "num"},
//    {data: 'HT_Notdusche', title: 'Notdusche', visible: false, case: "num"},
//    {data: 'HT_Tempgradient_Ch', title: 'Tempgradient C/h', visible: false, case: "num"},
//
//    //ET
//    {data: 'Anwendungsgruppe', title: 'RG', visible: false},
//    {data: 'Fussboden OENORM B5220', title: 'B5220', visible: false},
//    {data: 'AV', title: 'AV', visible: false, case: "bit"},
//    {data: 'SV', title: 'SV', visible: false, case: "bit"},
//    {data: 'ZSV', title: 'ZSV', visible: false, case: "bit"},
//    {data: 'USV', title: 'USV', visible: false, case: "bit"},
//    {data: 'EL_AV Steckdosen Stk', defaultContent: '-', title: 'AV #SSD', visible: false, case: "num"},
//    {data: 'EL_SV Steckdosen Stk', defaultContent: '-', title: 'SV #SSD', visible: false, case: "num"},
//    {data: 'EL_ZSV Steckdosen Stk', defaultContent: '-', title: 'ZSV #SSD', visible: false, case: "num"},
//    {data: 'EL_USV Steckdosen Stk', defaultContent: '-', title: 'USV #SSD', visible: false, case: "num"},
//    {data: 'EL_Roentgen 16A CEE Stk', title: 'CEE16A Röntgen', visible: false, case: "num"},
//    {data: 'EL_Laser 16A CEE Stk', defaultContent: '-', title: 'CEE16A Laser', visible: false, case: "num"},
//    {data: 'ET_Anschlussleistung_W', defaultContent: '-', title: 'Anschlussleistung Summe[W]', visible: false, case: "num"},
//    {data: 'ET_Anschlussleistung_AV_W', defaultContent: '-', title: 'Anschlussleistung AV[W]', visible: false, case: "num"},
//    {data: 'ET_Anschlussleistung_SV_W', defaultContent: '-', title: 'Anschlussleistung SV[W]', visible: false, case: "num"},
//    {data: 'ET_Anschlussleistung_ZSV_W', defaultContent: '-', title: 'Anschlussleistung ZSV[W]', visible: false, case: "num"},
//    {data: 'ET_Anschlussleistung_USV_W', defaultContent: '-', title: 'Anschlussleistung USV[W]', visible: false, case: "num"},
//    {data: 'IT Anbindung', title: 'IT', visible: false, case: "bit"},
//    {data: 'ET_RJ45-Ports', title: 'RJ45-Ports', visible: false, case: "num"},
//    {data: 'Laserklasse', title: 'Laserklasse', visible: false},
//
//    //AR 
//    {data: 'AR_AP_permanent', title: 'AR AP permanent', name: 'AR AP permanent ', visible: false, case: "bit", render: function (data) {
//            return data === '1' ? 'permanenter AP' : 'kein perma AP';
//        }},
//    {data: 'AR_Statik_relevant', title: 'AR Statik relevant', name: 'AR Statik relevant', visible: false, case: "bit", render: function (data) {
//            return data === '1' ? 'relevant' : 'nicht rel.';
//        }},
//
//    {data: 'AR_Empf_Breite_cm', defaultContent: '-', title: 'Empf. Breite [cm]', visible: false, case: "num"},
//    {data: 'AR_Empf_Tiefe_cm', defaultContent: '-', title: 'Empf. Tiefe [cm]', visible: false, case: "num"},
//    {data: 'AR_Empf_Hoehe_cm', defaultContent: '-', title: 'Empf. Höhe [cm]', visible: false, case: "num"},
//    {data: 'AR_Flaechenlast_kgcm2', defaultContent: '-', title: 'Flaechenlast [kg/cm2]', visible: false, case: "num"},
//
//    //MEDGASE
//    {data: '1 Kreis O2', title: '1_K O2', visible: false, case: "bit"},
//    {data: '2 Kreis O2', title: '2_K O2', visible: false, case: "bit"},
//    {data: 'CO2', title: 'CO2', visible: false, case: "bit"},
//    {data: '1 Kreis Va', title: '1_K Va', visible: false, case: "bit"},
//    {data: '2 Kreis Va', title: '2_K Va', visible: false, case: "bit"},
//    {data: '1 Kreis DL-5', title: '1_K DL5', visible: false, case: "bit"},
//    {data: '2 Kreis DL-5', title: '2_K DL5', visible: false, case: "bit"},
//    {data: 'DL-10', title: 'DL-10', visible: false, case: "bit"},
//    {data: 'DL-tech', title: 'DL-tech', visible: false, case: "bit"},
//    {data: 'NGA', title: 'NGA', visible: false, case: "bit"},
//    {data: 'N2O', title: 'N2O', visible: false, case: "bit"},
//    {data: 'VEXAT_Zone', title: 'VEXAT Zone', visible: false, case: "bit"},
//
//    //LABORZ
//    {data: 'O2', title: 'O2', visible: false, case: "bit"},
//    {data: 'O2 l/min', title: 'O2_l/min', visible: false, case: "num"},
//    {data: 'O2 Reinheit', title: 'O2 Reinheit', visible: false, case: ""},
//
//    {data: 'CO2 l/min', title: 'CO2_l/min', visible: false},
//    {data: 'CO2 Reinheit', title: 'CO2 Reinheit', visible: false, case: ""},
//
//    {data: 'VA', title: 'VA', visible: false, case: "bit"},
//    {data: 'VA l/min', title: 'VA_l/min', visible: false, case: "num"},
//
//    {data: 'H2', title: 'H2', visible: false, case: "bit"},
//    {data: 'H2 Reinheit', title: 'H2 Reinheit', visible: false, case: ""},
//    {data: 'H2 l/min', title: 'H2_l/min', visible: false, case: "num"},
//
//    {data: 'He', title: 'He', visible: false, case: "bit"},
//    {data: 'He Reinheit', title: 'He Reinheit', visible: false, case: ""},
//    {data: 'He l/min', title: 'He_l/min', visible: false, case: "num"},
//    {data: 'He-RF', title: 'He-RF', visible: false, case: "bit"},
//    {data: 'LHe', title: 'LHe', visible: false, case: "bit"},
//
//    {data: 'Ar', title: 'Ar', visible: false, case: "bit"},
//    {data: 'Ar Reinheit', title: 'Ar Reinheit', visible: false, case: ""},
//    {data: 'Ar l/min', title: 'Ar_l/min', visible: false, case: "num"},
//
//    {data: 'LN', title: 'LN', visible: false, case: "bit"},
//    {data: 'LN l/Tag', title: 'LN l/Tag', visible: false, case: "num"},
//
//    {data: 'N2', title: 'N2', visible: false, case: "bit"},
//    {data: 'N2 Reinheit', title: 'N2 Reinheit', visible: false, case: ""},
//    {data: 'N2 l/min', title: 'N2_l/min', visible: false, case: "num"},
//
//    {data: 'DL-5', title: 'DL-5', visible: false, case: "bit"},
//    {data: 'DL ISO 8573', title: 'DL_ISO 8573', visible: false, case: "bit"},
//    {data: 'DL l/min', title: 'DL_l/min', visible: false, case: "num"},
//
//    {data: 'Kr', title: 'Kr', visible: false, case: 'bit'},
//    {data: 'Ne', title: 'Ne', visible: false, case: 'bit'},
//    {data: 'NH3', title: 'NH3', visible: false, case: 'bit'},
//    {data: 'C2H2', title: 'C2H2', visible: false, case: 'bit'},
//    {data: 'Propan_Butan', title: 'Propan_Butan', visible: false, case: 'num'},
//    {data: 'N2H2', title: 'N2H2', visible: false, case: 'num'},
//    {data: 'Inertgas', title: 'Inertgas', visible: false, case: 'num'},
//    {data: 'Ar_CO2_Mix', title: 'AR CO2 Mix', visible: false, case: 'num'},
//    {data: 'ArCal15', title: 'ArCal15', visible: false, case: 'num'},
//
//    {data: 'O2_Mangel', title: 'O2_Mangel', visible: false, case: 'num'},
//    {data: 'CO2_Melder', title: 'CO2_Melder', visible: false, case: 'num'},
//    {data: 'NH3_Sensor', title: 'NH3_Sensor', visible: false, case: 'num'},
//    {data: 'H2_Sensor', title: 'H2_Sensor', visible: false, case: 'num'},
//
//    {data: 'O2_Sensor', title: 'O2_Sensor', visible: false, case: 'num'},
//    {data: 'Acetylen_Melder', title: 'Acetylen_Melder', visible: false, case: 'num'},
//
//    {data: 'Blitzleuchte', title: 'Blitzleuchte', visible: false, case: 'num'},
//
//    {data: 'ET_PA_Stk', title: 'ET PA Stk', visible: false, case: "num"},
//    {data: 'ET_16A_3Phasig_Einzelanschluss', title: '16A 3Ph Einzelanschluss', visible: false, case: "num"},
//    {data: 'ET_32A_3Phasig_Einzelanschluss', title: '32A 3Ph Einzelanschluss', visible: false, case: "num"},
//    {data: 'ET_64A_3Phasig_Einzelanschluss', title: '64A 3Ph Einzelanschls', visible: false, case: "num"},
//    {data: 'ET_Digestorium_MSR_230V_SV_Stk', title: 'Digestorium_MSR 230V_SV_Stk', visible: false, case: "num"},
//    {data: 'ET_5x10mm2_Digestorium_Stk', title: '5x10mm2 Digestorium_Stk', visible: false, case: "num"},
//    {data: 'ET_5x10mm2_USV_Stk', title: '5x10mm2 USV_Stk', visible: false, case: "num"},
//    {data: 'ET_5x10mm2_SV_Stk', title: '5x10mm2 SV_Stk', visible: false, case: "num"},
//    {data: 'ET_5x10mm2_AV_Stk', title: '5x10mm2 AV_Stk', visible: false, case: "num"},
//    {data: 'EL_Not_Aus_Funktion', title: 'Not Aus Funktion', visible: false, case: ""},
//    {data: 'EL_Not_Aus', title: 'Not Aus Stk', visible: false, case: 'num'},
//
//    {data: 'HT_Luftwechsel 1/h', title: 'Luftwechsel l/h', visible: false, case: "num"},
//
//    {data: 'HT_Abluft_Vakuumpumpe', title: 'Abluft Vakuumpumpe', visible: false, case: "num"},
//    {data: 'HT_Abluft_Sicherheitsschrank_Stk', title: 'Abluft Sicherheitsschrank Stk', visible: false, case: "num"},
//    {data: 'HT_Abluft_Schweissabsaugung_Stk', title: 'Abluft Schweissabsaugung_Stk', visible: false, case: "num"},
//    {data: 'HT_Abluft_Esse_Stk', title: 'Abluft Esse_Stk', visible: false, case: "num"},
//    {data: 'HT_Abluft_Rauchgasabzug_Stk', title: 'Abluft Rauchgasabzug_Stk', visible: false, case: "num"},
//    {data: 'HT_Abluft_Digestorium_Stk', title: 'Abluft Digestorium_Stk', visible: false, case: "num"},
//    {data: 'HT_Punktabsaugung_Stk', title: 'Punktabsaugung_Stk', visible: false, case: "num"},
//    {data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', title: 'Abluft Sicherheitsschrank_Unterbau_Stk', visible: false, case: "num"},
//
//    {data: 'HT_Abwasser_Stk', title: 'Abwasser Stk', visible: false, case: "num"},
//
//    {data: 'HT_Abluft_Geraete', title: 'Abluft Geräte', visible: false, case: "num"},
//
//    {data: 'VE_Wasser', title: 'VE_Wasser', visible: false, case: 'num'},
//    {data: 'HT_Warmwasser', title: 'Warmwasser', visible: false, case: "num"},
//    {data: 'HT_Kaltwasser', title: 'Kaltwasser', visible: false, case: "num"},
//    {data: 'Wasser Qual 1 l/Tag', title: 'H20_Q1 l/Tag', visible: false, case: "num"},
//    {data: 'Wasser Qual 2 l/Tag', title: 'H20_Q2 l/Tag', visible: false, case: "num"},
//    {data: 'Wasser Qual 3 l/min', title: 'H2O_Q3 l/min', visible: false, case: "num"},
//    {data: 'Wasser Qual 3', title: 'H20 Q3', visible: false, case: "bit"},
//    {data: 'Wasser Qual 2', title: 'H20 Q2', visible: false, case: "bit"},
//    {data: 'Wasser Qual 1', title: 'H20 Q1', visible: false, case: "bit"}
            ];
        </script> 
    </body>

