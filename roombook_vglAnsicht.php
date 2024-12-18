<!DOCTYPE html>
<html data-bs-theme="" xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Raumvergleich</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css"
          rel="stylesheet"/>
    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
    <style>
        .dt-input {
            width: 100px;
        }

        .card-header {
            height: 50px;
        }

        .card-body {
            padding: 1px;
        }

        .btn {
            margin: 1px;
            padding: 5px;
        }
    </style>
</head>

<?php
include '_utils.php'; // CHECKS SESSION AND LOGIN
init_page_serversides();
?>

<body style="height:100%">
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-2">
            <div class="card">
                <div class="card-header border-success"> RAUM</div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header d-inline-flex justify-content-lg-left ">
                    <div class="form-check">
                        <input class="form-check-input track-checkbox" type="checkbox" id="checkbox1">
                        <label class="form-check-label" for="checkbox1">Weniger Vergleichsräume laden</label>
                    </div>
                    <!--  <div class="form-check">
                         <input class="form-check-input track-checkbox" type="checkbox" id="checkbox2">
                         <label class="form-check-label" for="checkbox2">Checkbox 2</label>
                     </div>
                     <div class="form-check">
                         <input class="form-check-input track-checkbox" type="checkbox" id="checkbox3">
                         <label class="form-check-label" for="checkbox3">Checkbox 3</label>
                     </div>
                     <div class="form-check">
                         <input class="form-check-input track-checkbox" type="checkbox" id="checkbox4">
                         <label class="form-check-label" for="checkbox4">Checkbox 4</label>
                     </div>
                     <div class="form-check">
                         <input class="form-check-input track-checkbox" type="checkbox" id="checkbox5">
                         <label class="form-check-label" for="checkbox5">Checkbox 5</label>
                     </div>  -->
                </div>
            </div>
        </div>
        <div class="col-2">
            <div class="card border-primary">
                <div class="card-header "> VERGLEICH
                    <button type="button" class="btn btn-xs" onclick="show_modal('helpModal')">
                        <i class="fa fa-circle-info"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12" id="col1">
            <div class="card border-success" id="card1">
                <div class="card-header" id="CardHeaderRooms">
                    <button class="btn float-end grün" onclick="toggleCard('col1', 'col2', this)">
                        <i class="fa fa-arrow-right"> </i></button>

                    <button class="btn toggle-btn float-end grün ">
                        <i class="fa fa-arrow-up"></i></button>

                    <button class="btn float-end grün " onclick="toggleCard('col1', 'col2', this)"
                            id="Hide1"><i class="fa fa-arrow-left"></i></button>
                </div>
                <div class="card-body">
                    <table class="table table-compact table-responsive table-striped table-lg" id="t_rooms"
                           style="width: 100%"></table>
                </div>
            </div>
        </div>
        <div class="col-12" id="col2">
            <div class="card border-primary" id="card2">
                <div class="card-header justify-content-end d-inline-flex" id="CardHeaderVglRooms">
                    <button class="btn float-end toggle-btn grün "><i
                                class="fa fa-arrow-up"></i>
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-compact table-responsive table-striped table-lg" id="t_rooms_vgl"
                           style="width: 100%"></table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="col-6" id="col3">
            <div class="card border-success" id="card3">
                <div class="card-header">
                    Elemente im Raum
                    <button class="btn float-end grün 0"
                            onclick="toggleCard('col3', 'col4', this)">
                        <i class="fa fa-arrow-right"></i></button>
                    <button class="btn float-end toggle-btn grün ">
                        <i class="fa fa-arrow-up"></i>
                    </button>
                    <button class="btn float-end grün " id="Hide2"
                            onclick="toggleCard('col3', 'col4', this)">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="card-text" id="CB3"></p>
                </div>
            </div>
        </div>
        <div class="col-6" id="col4">
            <div class="card border-primary" id="card4">
                <div class="card-header">
                    Elemente im Vergleichsraum
                    <button class="btn float-end toggle-btn grün "><i
                                class="fa fa-arrow-up"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="card-text" id="CB4"></p>
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
                <p>Vergleichen Sie Räume, deren Bauangaben und ihre Ausstattung. Wählen Sie einen Referenzraum und einen
                    Vergleichsraum (selbe Funktionsstelle,
                    um Unterschiede und Ähnlichkeiten zu erkennen.</p>
                <h4>Nutzung</h4>
                <ol>
                    <li><strong>Raum auswählen:</strong> Wählen Sie links/oben einen Referenzraum.</li>
                    <li><strong>Vergleichsraum wählen:</strong> Wählen Sie rechts/darunter den Raum zum Vergleichen.
                        Hier können ebenso detaillierte Bauangaben eingeblendet und verglichen werden.
                    </li>
                    <li><strong>Checkbox-Filter:</strong> Funktion weniger Laden: Selektiert gleichartige Vergleichs
                        Räume bereits vorher aus und zeigt jeweils nur einen an.
                    </li>
                    <li><strong>Ausstattungsvergleich:</strong> Grün markierte Elemente sind in beiden Räumen vorhanden,
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
<script src="_utils.js"></script>
<script>

    $(document).ready(function () {
        addToggleFunctionality();
        init_t_rooms();
        table_click("t_rooms", "CB3");
    });

    let filter_init_counter = 1;
    let t_rooms;
    let t_rooms_vgl;

    let RID1;
    let RID2;
    const newEntry = {data: 'Projektname', title: 'Projekt', visible: true, searchable: true};
    let cDef = [...columnsDefinition];
    cDef.unshift(newEntry);

    function table_click(table_id, taget_id_4_new_content) {
        $(document).on('click', "#" + table_id + " tr", function () {
            let selectedRowData;
            let RID1change = false;
            if (table_id === "t_rooms") {
                selectedRowData = t_rooms.row('.selected').data();
                if (selectedRowData) {
                    if (RID1 !== selectedRowData[`idTABELLE_Räume`]) {
                        RID1 = selectedRowData[`idTABELLE_Räume`];
                        RID1change = true;
                    }

                }
            }
            if (table_id === "t_rooms_vgl") {
                selectedRowData = t_rooms_vgl.row('.selected').data();
                if (selectedRowData) {
                    if (RID2 !== selectedRowData["idTABELLE_Räume"]) {
                        RID2 = selectedRowData["idTABELLE_Räume"];
                    }
                }
            }
            if (selectedRowData) {
                value = selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"]
                if (RID1change) {
                    init_vgl_rooms_table(value);
                    $('#CB4').empty();
                }
                get_el_in_room_table(selectedRowData['idTABELLE_Räume'], taget_id_4_new_content);
            }
        });
    }

    function compareElementTables() {
        let elementIdsTable1 = $(`#tableRoomElements${RID1}`).DataTable().column(1).data().toArray();
        let elementIdsTable2 = $(`#tableVglRoomElements${RID2}`).DataTable().column(1).data().toArray();

        let idsSet1 = new Set(elementIdsTable1);
        let idsSet2 = new Set(elementIdsTable2);

        $(`#tableRoomElements${RID1}`).DataTable().rows().every(function () {
            let row = this.node();
            let rowData = this.data();
            let rowId = rowData.ElementID;

            $(this.node()).find('td').each(function (index) {
                if (index === 1) {
                    if (idsSet2.has(rowId)) {
                        $(this).addClass('grün');
                    } else {
                        $(this).addClass('rot');
                    }
                }
            });
        });

        $(`#tableVglRoomElements${RID2}`).DataTable().rows().every(function () {
            let row = this.node();
            let rowData = this.data();
            let rowId = rowData.ElementID;

            $(this.node()).find('td').each(function (index) {
                if (index === 1) {
                    if (idsSet1.has(rowId)) {
                        $(this).addClass('grün');
                    } else {
                        $(this).addClass('rot');
                    }
                }
            });
        });
    }


    function get_el_in_room_table(RaumID, targetDiv) {
        $.ajax({
            url: "get_RoomElementsData.php",
            data: {"roomID": RaumID},
            type: "GET",
            dataType: "json",
            success: function (data) {
                let tableId = 'tableRoomElements' + RaumID;
                if (targetDiv === "CB4") {
                    tableId = 'tableVglRoomElements' + RaumID;
                }
                // console.log("Initiating dt 4 elements: " + tableId);
                let tableHtml = "<table id='" + tableId + "' class='table table-responsive table-striped table-bordered table-sm' style='width: 100%'></table>";
                $("#" + targetDiv).html(tableHtml);
                $('#' + tableId).DataTable({
                    data: data,
                    columns: [
                        {data: 'Bezeichnung', title: 'Element'},
                        {data: 'ElementID', title: 'ID'},
                        {data: 'Anzahl', title: 'Stück'},
                        {data: 'Variante', title: 'Var.'},
                        {
                            data: 'Neu/Bestand', title: 'Best.', render: function (data) {
                                return data === 1 ? "Nein" : "Ja";
                            }
                        },
                        {
                            data: 'Standort', title: 'Ort', render: function (data) {
                                return data === 1 ? "Ja" : "Nein";
                            }
                        },
                        {
                            data: 'Verwendung', title: 'Verw.', render: function (data) {
                                return data === 1 ? "Ja" : "Nein";
                            }
                        }
                    ],
                    layout: {
                        topStart: null,
                        topEnd: null,
                        bottomStart: ['info'],
                        bottomEnd: null
                    },
                    paging: false,
                    responsive: true,
                    language: {
                        info: "_TOTAL_ Zeilen"
                    },
                    scrollCollapse: true,
                    select: {
                        style: "single",
                        info: false
                    },
                    info: true,
                    compact: true,
                    initComplete: function () {
                        if (targetDiv === "CB4") {
                            compareElementTables();
                        }
                    }
                });
            }
        });

    }

    function getVisibleColumns() {
        var visibleColumns = [];
        t_rooms_vgl.columns().every(function () {
            if (this.visible()) {
                visibleColumns.push(this.index());
            }
        });
        return visibleColumns;
    }

    function init_vgl_rooms_table(value) {
        if (t_rooms_vgl) {
            let visibleColumns = getVisibleColumns();
            cDef.forEach(function (columnDef, index) {
                columnDef.visible = !!visibleColumns.includes(index);
            });
            console.log(cDef);
            t_rooms_vgl.buttons('.buttons-colvis').remove();
            $('.dt-buttons').remove();
            $('#dt-search-' + (filter_init_counter - 1).toString()).remove(); // Remove the old search element
            t_rooms_vgl.destroy();
        }
        setTimeout(function () {

            t_rooms_vgl = new DataTable('#t_rooms_vgl', {
                ajax: {
                    url: 'get_rooms_with_funktionsteilstelle.php',
                    data: {"value": value, "RaumID": RID1, "Unique": $('#checkbox1').prop('checked')},
                    dataSrc: ''
                },
                columns: cDef,
                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: ["info", "pageLength", 'search'],
                    bottomEnd: {
                        paging: {
                            buttons: 3
                        }
                    }
                },
                language: {
                    "search": "",
                    "info": "_TOTAL_ Zeilen",

                },
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 50],
                    ['5 rows', '10 rows', '50 rows']
                ],
                select: {
                    style: "single",
                    info: false
                },
                responsive: true,
                scrollCollapse: true,
                compact: true,

                initComplete: function () {
                    const searchbuilder = [
                        {
                            extend: 'searchBuilder',
                            text: null,
                            className: "btn fas fa-search",
                            titleAttr: "Suche konfigurieren",
                        }
                    ];
                    new $.fn.dataTable.Buttons(t_rooms_vgl, {buttons: searchbuilder}).container().appendTo($('#CardHeaderVglRooms'));

                    const buttonColumnVisbilities = [
                        {
                            extend: 'colvis',
                            text: 'Vis',
                            columns: ':gt(5)',
                            collectionLayout: 'fixed columns',
                            className: 'btn'
                        }];
                    new $.fn.dataTable.Buttons(t_rooms_vgl, {buttons: buttonColumnVisbilities}).container().appendTo($('#CardHeaderVglRooms'));
                    move_item("dt-search-" + filter_init_counter.toString(), "CardHeaderVglRooms");
                    filter_init_counter++;
                    table_click("t_rooms_vgl", "CB4");
                }
            });
        }, 20);
    }


    function init_t_rooms() {
        const columnsDefinitionShort = [// NEW FIELD? - ADD Here, In get_rb_specs_data.php and the CPY/save methods
            {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false},
            {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
            {
                data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
                title: 'Funktionsstellen ID',
                visible: false,
                searchable: false
            },
            {
                data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit", render: function (data) {
                    return data === '1' ? 'Ja' : 'Nein';
                }
            },
            {data: 'Raumbezeichnung', title: 'Raumbez.'},
            {data: 'Raumnr', title: 'Raumnr'},

            {data: "Bezeichnung", title: "Funktionsstelle", visible: true, case: "none-edit"}, //#7
            {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'},
            {data: "Nummer", title: "DIN13080", visible: false, case: "none-edit"},

            {data: "Entfallen", title: "Entfallen", name: "Entfallen", visible: false, case: "bit"},

            {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', visible: false},
            {data: 'Raumbereich Nutzer', title: 'Raumbereich', visible: false}];

        t_rooms = new DataTable('#t_rooms', {
            ajax: {
                url: 'get_mt_relevant_room_specs.php',
                dataSrc: ''
            },
            columns: columnsDefinitionShort,
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ["info", "pageLength", 'search',],
                bottomEnd: {
                    paging: {
                        buttons: 3
                    }
                }
            },
            language: {
                "search": "",
                "info": "_TOTAL_ Zeilen",

            },
            pageLength: 10,
            lengthMenu: [
                [5, 10, 50],
                ['5 rows', '10 rows', '50 rows']
            ],
            select: {
                style: "single",
                info: false
            },
            responsive: true,
            scrollCollapse: true,
            compact: true,
            initComplete: function () {
                move_item("dt-search-0", "CardHeaderRooms");
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
        //console.log(button);
        const col1 = document.getElementById(colId1);
        const col2 = document.getElementById(colId2);
        if (button.id.startsWith("Hide")) {
            if (col1.classList.contains('col-6')) {
                col1.classList.remove('col-6');
                col1.classList.add('col-2');
                col2.classList.remove('col-6');
                col2.classList.add('col-10');
            } else if (col1.classList.contains('col-12')) {
                col1.classList.remove('col-12');
                col1.classList.add('col-6');
                col2.classList.remove('col-12');
                col2.classList.add('col-6');
            }
        } else {
            if (col1.classList.contains('col-6') && col2.classList.contains('col-6')) {
                col1.classList.remove('col-6');
                col1.classList.add('col-12');
                col2.classList.remove('col-6');
                col2.classList.add('col-12');
            } else if (col1.classList.contains('col-2')) {
                col1.classList.remove('col-2');
                col1.classList.add('col-6');
                col2.classList.remove('col-10');
                col2.classList.add('col-6');
            }
        }
        tableRedraw();
    }

    function tableRedraw() {
        if ($.fn.DataTable.isDataTable('#t_rooms')) {
            $('#t_rooms').DataTable().columns.adjust().draw();
        }
        if ($.fn.DataTable.isDataTable('#t_rooms_vgl')) {
            $('#t_rooms_vgl').DataTable().columns.adjust().draw();
        }

        let tableid = 'tableRoomElements' + RID1.toString();
        if ($.fn.DataTable.isDataTable('#' + tableid)) {
            $('#' + tableid).DataTable().columns.adjust().draw();
        }
        tableid = 'tableRoomElements' + RID2.toString();
        if ($.fn.DataTable.isDataTable('#' + tableid)) {
            $('#' + tableid).DataTable().columns.adjust().draw();
        }
    }


    //    function addToggleButton(cardId) {
    //                    const card = document.getElementById(cardId);
    //                    const cardHeader = card.querySelector('.card-header');
    //                    const cardBody = card.querySelector('.card-body');
    //                    $(cardHeader).click(function () {
    //                        $(cardBody).toggle();
    //                    });
    //                }

</script>
</body>
</html>