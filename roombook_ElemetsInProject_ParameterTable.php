<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Element Parameter Tabelle</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
</head>

<?php
// 25 FX
require_once 'utils/_utils.php';
init_page_serversides("", "x");
?>


<body>
<div class="container-fluid">
    <div id="limet-navbar"></div>
    <div class=' mt-1 card col-12'>
        <div style="height: 50px" class="card-header d-inline-flex  align-content-start"
             id="elemetsParamsTableCardHeader">
            <label class="form-check-label"> <u> Element-Parameter im Projekt </u> </label>
        </div>
        <div class="card-body " id="elemetsParamsTableCard">
            <p id="elemetsParamsTable">
        </div>
    </div>
</div>
<script>
    var K2R = ["1", "2", "3", "12", "17"];

    const checkboxData = [
        {label: ' ELEK', value: '2'},
        {label: ' GEOM', value: '1'},
        {label: ' HKLS', value: '3'},
        {label: ' MGAS', value: '12'},
        {label: ' MSR', value: '17'}
    ];

    let dataTable;

    function init_checkboxes4selectingKathegories() {
        checkboxData.forEach((data, index) => {
            let checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = 'checkbox' + index;
            checkbox.value = data.value;
            checkbox.className = 'form-check-input';
            checkbox.checked = true;
            let label = document.createElement('label');
            label.htmlFor = checkbox.id;
            label.className = 'form-check-label ms-1 me-3'; // spacing for layout
            label.appendChild(document.createTextNode(data.label));
            let div = document.querySelector('#elemetsParamsTableCardHeader');
            div.appendChild(checkbox);
            div.appendChild(label);
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    if (!K2R.includes(this.value)) {
                        K2R.push(this.value);
                    }
                } else {
                    K2R = K2R.filter(value => value !== this.value);
                }
                loadElementsParamTable();
            });
        });
    }

    function loadElementsParamTable() {
        // Optional: Destroy previous DataTable if exists
        if ($.fn.DataTable.isDataTable('#elementsTable')) {
            dataTable.destroy();
            $('#elementsTable').remove();
        }
        $('#elemetsParamsTable').empty();

        const url = 'getRoomElementsInProjectParameterData.php?K2Return=' + encodeURIComponent(JSON.stringify(K2R));

        $.getJSON(url, function (data) {
            if (!data.length) {
                $('#elemetsParamsTable').html('<p>Keine Daten gefunden.</p>');
                return;
            }

            // Build columns dynamically: fixed columns + dynamic parameter columns from first room's element keys
            const fixedCols = [
                {title: 'Raum ID', data: 'roomID', visible: false},
                // {title: 'Raumbezeichnung', data: 'Raumbezeichnung'},
                // {title: 'Raumnr', data: 'Raumnr'},
                // {title: 'MTrelevant', data: 'MTrelevant'},
                // {title: 'Bauabschnitt', data: 'Bauabschnitt'},
                // {title: 'Geschoss', data: 'Geschoss'},
                {title: "<th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='ElementID'><i class='fas fa-fingerprint'></i></div> </th> " , data: 'ElementID'},
                {title: 'Bezeichnung', data: 'Bezeichnung'},
                {title: 'Var', data: 'Variante'},
                {title: 'Neu/Bestand', data: 'Neu/Bestand'},
                {title: "<th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Standort'> <i class='fab fa-periscope '></i></div> </th>",data: 'Standort'},
                {title: 'SummevonAnzahl', data: 'SummevonAnzahl'}
            ];

            // Extract all dynamic parameter keys from the first element of the first room
            let paramCols = [];
            const firstRoom = data[0];
            if (firstRoom.elements && firstRoom.elements.length) {
                const exampleElem = firstRoom.elements[0];
                // Filter out known fixed keys to get only parameter keys
                const paramKeys = Object.keys(exampleElem).filter(k => ![
                    'ElementID', 'Bezeichnung', 'Variante', 'Neu/Bestand', 'Standort', 'SummevonAnzahl',
                    'TABELLE_Elemente_idTABELLE_Elemente', 'tabelle_Varianten_idtabelle_Varianten'
                ].includes(k));
                paramCols = paramKeys.map(key => ({title: key, data: key}));
            }

            // Combine columns
            const columns = fixedCols.concat(paramCols);

            // Flatten data: create one row per element with roomID
            const tableData = [];
            data.forEach(room => {
                const roomID = room.roomID;
                room.elements.forEach(element => {
                    element.roomID = roomID; // add roomID to element data
                    tableData.push(element);
                });
            });

            // Create table element and append to container
            const tableHtml = $('<table id="elementsTable" class="table table-striped table-bordered" style="width:100%"></table>');
            $('#elemetsParamsTable').append(tableHtml);

            // Init DataTable
            dataTable = $('#elementsTable').DataTable({
                data: tableData,
                columns: columns,
                scrollX: true,
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
                }
            });
        });
    }

    $(document).ready(function () {
        init_checkboxes4selectingKathegories();
        loadElementsParamTable();
    });

</script>
</body>
</html>

