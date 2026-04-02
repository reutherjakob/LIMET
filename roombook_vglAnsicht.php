<!DOCTYPE html>
<html data-bs-theme="" xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Raumvergleich</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css"
          rel="stylesheet"/>
    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
</head>

<?php
// 25 FX 
require_once 'utils/_utils.php'; // CHECKS SESSION
init_page_serversides(); // checks Nutzerlogin
?>

<body style="height:100%">
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-inline-flex">
                    <div class="form-check col-xxl-6">
                        <input class="form-check-input track-checkbox" type="checkbox" id="checkbox1">
                        <label class="form-check-label" for="checkbox1">Weniger Vergleichsräume laden</label>
                    </div> &emsp;

                    <div class="form-check col-xxl-6 d-flex justify-content-end">
                        <button type="button" class="btn btn-info btn-sm me-2 text-dark"
                                onclick="show_modal('helpModal')">
                            <i class="fas fa-question-circle"></i></button>
                    </div>
                </div>

                <div class=" card-body">
                    <div class="row">
                        <div class="col-xxl-6" id="col1">
                            <div class="card border-success" id="card1">
                                <div class="card-header"> Räume aktuelles Projekt
                                    <button class="btn float-end grün" onclick="toggleCard('col1', 'col2', this)">
                                        <i class="fa fa-arrow-right"> </i></button>

                                    <button class="btn toggle-btn float-end grün ">
                                        <i class="fa fa-arrow-up"></i></button>

                                    <button class="btn float-end grün " onclick="toggleCard('col1', 'col2', this)"
                                            id="Hide1"><i class="fa fa-arrow-left"></i></button>
                                    <div class="d-flex align-items-center float-end" id="CardHeaderRooms"></div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-compact table-responsive table-striped table-lg"
                                           id="t_rooms"
                                           style="width: 100%"></table>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6" id="col2">
                            <div class="card " id="card2">
                                <div class="card-header"> Vergleichsräume
                                    <div class=" justify-content-end d-inline-flex" id="CardHeaderVglRooms"></div>
                                    <button class="btn float-end toggle-btn grün ">
                                        <i class="fa fa-arrow-up"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-compact table-responsive table-striped table-lg"
                                           id="t_rooms_vgl"
                                           style="width: 100%">
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="">
        <div class="card-header"> ELEMENTE
            <button class="btn float-end toggle-btn grün ">
                <i class="fa fa-arrow-up"></i>
            </button>
        </div>
        <div class="card-body" id="">
            <div class="row mt-1">
                <div class="col-xxl-6" id="col3">
                    <div class="card border-success" id="card3a">
                        <div class="card-header">
                            Elemente im Raum
                        </div>
                        <div class="card-body">
                            <p class="card-text" id="CB3"></p>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6" id="col4">
                    <div class="card " id="card4a">
                        <div class="card-header">
                            Elemente im Vergleichsraum
                        </div>
                        <div class="card-body">
                            <p class="card-text" id="CB4"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header"> Bauangaben
            <button class="btn float-end toggle-btn grün ">
                <i class="fa fa-arrow-up"></i>
            </button>
        </div>

        <div class="card-body">
            <div class="row mt-1">
                <div class="col-xxl-6">
                    <div class="card border-success" id="card3b">
                        <div class="card-header"> Bauangaben Text
                        </div>
                        <div class="card-body" id="bauangaben">
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="card" id="card4b">
                        <div class="card-header">Bauangaben Text Vgl
                        </div>
                        <div class="card-body" id="bauangaben_vgl">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="helpModalLabel">Hilfe - Raumvergleich</h5>
            </div>
            <div class="modal-body">
                <p>Vergleichen Sie Räume, deren Bauangaben und ihre Ausstattung. Wählen Sie einen Referenzraum
                    und einen
                    Vergleichsraum (selbe Funktionsstelle,
                    um Unterschiede und Ähnlichkeiten zu erkennen.</p>
                <h4>Nutzung</h4>
                <ol>
                    <li><strong>Raum auswählen:</strong> Wählen Sie links/oben einen Referenzraum.</li>
                    <li><strong>Vergleichsraum wählen:</strong> Wählen Sie rechts/darunter den Raum zum
                        Vergleichen.
                        Hier können ebenso detaillierte Bauangaben eingeblendet und verglichen werden.
                    </li>
                    <li><strong>Checkbox-Filter:</strong> Funktion weniger Laden: Selektiert gleichartige
                        Vergleichs
                        Räume bereits vorher aus und zeigt jeweils nur einen an.
                    </li>
                    <li><strong>Ausstattungsvergleich:</strong> Grün markierte Elemente sind in beiden Räumen
                        vorhanden,
                        rot hervorgehobene nur im aktuell ausgewähltem Raum.
                    </li>
                </ol>
                <h4>Feedback</h4>
                <p>Bei Problemen, Unstimmigkeiten und Wünschen wenden Sie sich bitte an das Support-Team.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<script src="roombookSpecifications_constDeclarations.js"></script>
<script src="utils/_utils.js"></script>
<script>

    function show_modal(modal_id) {
        $('#' + modal_id).modal('show');
    }

    $(document).ready(function () {
        addToggleFunctionality();
        init_t_rooms();
        // KEIN table_click hier — wird in initComplete von t_rooms registriert
    });

    let filter_init_counter = 1;
    let t_rooms;
    let t_rooms_vgl;
    let RID1;
    let RID2;
    // FIX Bug 3: Flag ob t_rooms_vgl-Listener schon registriert ist
    let vgl_click_registered = false;

    const columnsBase = [
        {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projekt ID', visible: false, searchable: false},
        {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
        {
            data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
            title: 'Funktionsstellen ID',
            visible: false,
            searchable: false
        },
        {
            data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit",
            render: d => d === '1' ? 'Ja' : 'Nein'
        },
        {data: 'Raumbezeichnung', title: 'Raumbez.'},
        {data: 'Raumnr', title: 'Raumnr'},
        {data: 'Bezeichnung', title: 'Funktionsstelle', visible: true, case: "none-edit"},
        {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'},
        {data: 'Nummer', title: 'DIN13080', visible: false, case: "none-edit"},
        {data: 'Entfallen', title: 'Entfallen', name: 'Entfallen', visible: false, case: "bit"},
        {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', visible: false},
        {data: 'Raumbereich Nutzer', title: 'Raumbereich', visible: false}
    ];
    const columnsRooms = columnsBase;
    const   columnsVgl = [
            ...columnsBase.slice(0, 3),
            {data: 'Projektname', title: 'Projekt', visible: true, searchable: true},
            ...columnsBase.slice(3)
        ];

    // FIX Bug 3: Event-Delegation nur einmal pro Tabelle registrieren
    function register_room_click() {
        $(document).on('click', '#t_rooms tr', function () {
            if (!t_rooms) return;
            const selectedRowData = t_rooms.row(this).data();
            if (!selectedRowData) return;

            const newRID1 = selectedRowData['idTABELLE_Räume'];
            if (RID1 !== newRID1) {
                RID1 = newRID1;
                const funktionsstelleId = selectedRowData['TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen'];
                init_vgl_rooms_table(funktionsstelleId);
                $('#CB4').empty();
            }

            t_rooms.rows().nodes().each(function (row) {
                $(row).removeClass('selected');
            });
            $(this).addClass('selected');

            load_room_texts(RID1, '#bauangaben');
            get_el_in_room_table(RID1, 'CB3');
        });
    }

    function register_vgl_click() {
        if (vgl_click_registered) return; // FIX Bug 3: nicht doppelt registrieren
        vgl_click_registered = true;

        $(document).on('click', '#t_rooms_vgl tr', function () {
            if (!t_rooms_vgl) return;
            const selectedRowData = t_rooms_vgl.row(this).data();
            if (!selectedRowData) return;

            RID2 = selectedRowData['idTABELLE_Räume'];

            t_rooms_vgl.rows().nodes().each(function (row) {
                $(row).removeClass('selected');
            });
            $(this).addClass('selected');

            load_room_texts(RID2, '#bauangaben_vgl');
            get_el_in_room_table(RID2, 'CB4');
        });
    }

    function load_room_texts(rid, where2putthedata) {
        $.ajax({
            url: 'setSessionVariables.php',
            data: {roomID: rid},
            type: 'POST',
            // FIX: Fehlerbehandlung hinzugefügt
            error: function () {
                console.error('setSessionVariables.php fehlgeschlagen für RID', rid);
            },
            success: function () {
                $.ajax({
                    url: 'getRoomSpecifications2.php',
                    type: 'POST',
                    error: function () {
                        $(where2putthedata).html('<p class="text-danger">Bauangaben konnten nicht geladen werden.</p>');
                    },
                    success: function (data) {
                        $(where2putthedata).html(data);
                    }
                });
            }
        });
    }

    function compareElementTables() {
        // FIX: Sicherstellen dass beide Tabellen existieren
        if (!RID1 || !RID2) return;
        const tableId1 = '#tableRoomElements' + RID1;
        const tableId2 = '#tableVglRoomElements' + RID2;
        if (!$.fn.DataTable.isDataTable(tableId1) || !$.fn.DataTable.isDataTable(tableId2)) return;

        const idsSet1 = new Set($(tableId1).DataTable().column(1).data().toArray());
        const idsSet2 = new Set($(tableId2).DataTable().column(1).data().toArray());

        $(tableId1).DataTable().rows().every(function () {
            const rowId = this.data().ElementID;
            $(this.node()).find('td').eq(1)
                .toggleClass('grün', idsSet2.has(rowId))
                .toggleClass('rot', !idsSet2.has(rowId));
        });

        $(tableId2).DataTable().rows().every(function () {
            const rowId = this.data().ElementID;
            $(this.node()).find('td').eq(1)
                .toggleClass('grün', idsSet1.has(rowId))
                .toggleClass('rot', !idsSet1.has(rowId));
        });
    }

    function get_el_in_room_table(RaumID, targetDiv) {
        $.ajax({
            url: 'get_RoomElementsData.php',
            data: {roomID: RaumID},
            type: 'POST',
            dataType: 'json',
            error: function () {
                $('#' + targetDiv).html('<p class="text-danger">Elemente konnten nicht geladen werden.</p>');
            },
            success: function (data) {
                const isVgl = (targetDiv === 'CB4');
                const tableId = isVgl ? 'tableVglRoomElements' + RaumID : 'tableRoomElements' + RaumID;

                // Alte DataTable destroyen falls vorhanden
                if ($.fn.DataTable.isDataTable('#' + tableId)) {
                    $('#' + tableId).DataTable().destroy();
                }

                $('#' + targetDiv).html(
                    "<table id='" + tableId + "' class='table table-responsive table-striped table-bordered table-sm' style='width:100%'></table>"
                );

                $('#' + tableId).DataTable({
                    data: data,
                    columns: [
                        {data: 'Bezeichnung', title: 'Element'},
                        {data: 'ElementID', title: 'ID'},
                        {data: 'Anzahl', title: 'Stück'},
                        {data: 'Variante', title: 'Var.'},
                        {data: 'Neu/Bestand', title: 'Best.', render: d => d === 1 ? 'Nein' : 'Ja'},
                        {data: 'Standort', title: 'Ort', render: d => d === 1 ? 'Ja' : 'Nein'},
                        {data: 'Verwendung', title: 'Verw.', render: d => d === 1 ? 'Ja' : 'Nein'}
                    ],
                    layout: {topStart: null, topEnd: null, bottomStart: ['info'], bottomEnd: null},
                    paging: false,
                    responsive: true,
                    language: {info: '_TOTAL_ Zeilen'},
                    scrollCollapse: true,
                    select: {style: 'single', info: false},
                    compact: true,
                    initComplete: function () {
                        if (isVgl) {
                            compareElementTables();
                        }
                    }
                });
            }
        });
    }

    function getVisibleColumns() {
        return t_rooms_vgl.columns().indexes().filter(i => t_rooms_vgl.column(i).visible()).toArray();
    }

    function init_vgl_rooms_table(value) {
        // FIX Bug 5: kein setTimeout — destroy direkt, dann sofort neu initialisieren
        if (t_rooms_vgl) {
            const visibleCols = getVisibleColumns();
            columnsVgl.forEach((col, i) => {
                col.visible = visibleCols.includes(i);
            });
            t_rooms_vgl.destroy();
            t_rooms_vgl = null;
        }

        t_rooms_vgl = new DataTable('#t_rooms_vgl', {
            ajax: {
                url: 'get_rooms_with_funktionsteilstelle.php',
                data: {value: value, RaumID: RID1, Unique: $('#checkbox1').prop('checked')},
                dataSrc: '',   // ← erwartet direkt ein Array
                type: 'POST',
                error: function () {
                    console.error('Vergleichsräume konnten nicht geladen werden.');
                }
            },
            columns: columnsVgl,
            layout: {
                topStart: null, topEnd: null,
                bottomStart: ['info', 'pageLength', 'search'],
                bottomEnd: {paging: {buttons: 3}}
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: '',
                info: '_TOTAL_ Zeilen'
            },
            pageLength: 10,
            lengthMenu: [[5, 10, 50], ['5 Zeilen', '10 Zeilen', '50 Zeilen']],
            select: {style: 'single', info: false},
            responsive: true,
            scrollCollapse: true,
            compact: true,
            initComplete: function () {
                $('#CardHeaderVglRooms').empty(); // alte Buttons entfernen

                new $.fn.dataTable.Buttons(t_rooms_vgl, {
                    buttons: [{
                        extend: 'searchBuilder', text: null,
                        className: 'btn fas fa-search', titleAttr: 'Suche konfigurieren'
                    }]
                }).container().appendTo($('#CardHeaderVglRooms'));

                new $.fn.dataTable.Buttons(t_rooms_vgl, {
                    buttons: [{
                        extend: 'colvis', text: 'Vis', columns: ':gt(5)',
                        collectionLayout: 'fixed columns', className: 'btn'
                    }]
                }).container().appendTo($('#CardHeaderVglRooms'));

                move_item('dt-search-' + filter_init_counter.toString(), 'CardHeaderVglRooms');
                filter_init_counter++;

                register_vgl_click(); // FIX Bug 3: nur einmal registrieren
            }
        });
    }

    function init_t_rooms() {
        t_rooms = new DataTable('#t_rooms', {
            ajax: {
                url: 'get_mt_relevant_room_specs.php',
                dataSrc: '',  // FIX Bug 1: dataSrc explizit setzen
                error: function () {
                    console.error('Räume konnten nicht geladen werden.');
                }
            },
            columns: columnsRooms,
            layout: {
                topStart: null, topEnd: null,
                bottomStart: ['info', 'pageLength', 'search'],
                bottomEnd: {paging: {buttons: 3}}
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: '',
                info: '_TOTAL_ Zeilen'
            },
            pageLength: 10,
            lengthMenu: [[5, 10, 50], ['5 Zeilen', '10 Zeilen', '50 Zeilen']],
            select: {style: 'single', info: false},
            responsive: true,
            scrollCollapse: true,
            compact: true,
            initComplete: function () {
                move_item('dt-search-0', 'CardHeaderRooms');
                register_room_click(); // FIX Bug 3: Listener hier registrieren
            }
        });
    }

    function addToggleFunctionality() {
        $('.toggle-btn').click(function () {
            $(this).closest('.card').find('.card-body').toggle();
            $(this).find('i').toggleClass('fa-arrow-up fa-arrow-down');
        });
    }

    function toggleCard(colId1, colId2, button) {
        const col1 = document.getElementById(colId1);
        const col2 = document.getElementById(colId2);
        const hiding = button.id && button.id.startsWith('Hide');

        if (hiding) {
            if (col1.classList.contains('col-xxl-6')) {
                col1.classList.replace('col-xxl-6', 'col-2');
                col2.classList.replace('col-xxl-6', 'col-10');
            } else if (col1.classList.contains('col-12')) {
                col1.classList.replace('col-12', 'col-xxl-6');
                col2.classList.replace('col-12', 'col-xxl-6');
            }
        } else {
            if (col1.classList.contains('col-xxl-6') && col2.classList.contains('col-xxl-6')) {
                col1.classList.replace('col-xxl-6', 'col-12');
                col2.classList.replace('col-xxl-6', 'col-12');
            } else if (col1.classList.contains('col-2')) {
                col1.classList.replace('col-2', 'col-xxl-6');
                col2.classList.replace('col-10', 'col-xxl-6');
            }
        }
        tableRedraw();
    }

    function tableRedraw() {
        ['#t_rooms', '#t_rooms_vgl'].forEach(id => {
            if ($.fn.DataTable.isDataTable(id)) $(id).DataTable().columns.adjust().draw();
        });
        // FIX Bug 4: Null-Check für RID1 und RID2
        if (RID1) {
            const id1 = '#tableRoomElements' + RID1;
            if ($.fn.DataTable.isDataTable(id1)) $(id1).DataTable().columns.adjust().draw();
        }
        if (RID2) {
            const id2 = '#tableVglRoomElements' + RID2;
            if ($.fn.DataTable.isDataTable(id2)) $(id2).DataTable().columns.adjust().draw();
        }
    }

</script>


</body>
</html>