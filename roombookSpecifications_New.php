<?php
session_start();
include 'data_utils.php';
check_login();
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">   
    <head>
        <title>RB-Bauangaben</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

        <link href="datatables.min.css" rel="stylesheet">     
        <script type="text/javascript" src="datatables.min.js"></script> 
       
        <style> 
        .btn_vis{
            background-color: rgba(100, 140, 25, 0.2)!important;
            color: black;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,0.2), 0 0px 0px 0 rgba(0,0,0,0.10);
        }
        .btn_invis{
            background-color: rgba(100, 0, 25, 0.2)!important;
            color: black;
        }
        .card-header_size {
            height: 50px;
            width: auto;
        }
        .table>thead>tr>th {
            background-color: rgba(100, 140, 25, 0.15);
        }
        .none {
            background-color: #FFFFFF !important; 
            height: 1px !important; 
            width: 1px !important;
            z-index: -1;
        }
        </style>
    
</head> 

<body style="height:100%">
<div class="container-fluid ">
    <div id="limet-navbar" class=''> </div> 
    <div class="mt-4 card">   
        <div class="card-header" id='TableCardHeader'></div>
        <div class="card-body" id = "table_container_div">
            <table class="table table-responsive table-striped table-bordered table-sm" width ="100%" id="table_rooms" > 
                <thead class= <tr></tr> </thead>
                <tbody>  </tbody>
            </table> 
        </div>
    </div>      
    <div class='d-flex bd-highlight'>
        <div class='mt-4 mr-2 card flex-grow-1'>
            <div class="card-header card-header_size"><b>Bauangaben</b></div>
            <div class="card-body" id="bauangaben"></div>
        </div>      
        <div class="mt-4 card">
            <div class="card">
                <div class="card-header card-header_size">
                    <button type="button" class="btn btn-outline-dark btn-xs" id="showRoomElements"><i class="fas fa-caret-left"></i></button> 
                    <input type="text" class ="pull-right" id="diy_searcher" placeholder="Search...">
                </div>
                <div class="card-body " id ="additionalInfo">
                    <p id="roomElements">
                    <p id="elementParameters"></div>
            </div> 
        </div>        
    </div>
</div>

<script src="roombookSpecifications_constDeclarations.js"></script> 
<script>
var editor;
var table; 
var dropdownHtml ='<select id="columnFilter">' + '<option value="">All</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';

$(document).ready(function () { 
    init_editor();
    init_dt();
    init_table_click();
    init_showRoomElements_btn();
    init_btn_4_dt();
    add_MT_rel_filter();
    
    
});

 


function init_dt(){
    table = new DataTable('#table_rooms', {
        ajax: {
            url: 'get_rb_specs_data.php',
            dataSrc: ''
//           ,type: 'POST'
        },
        columns: columnsDefinition,
        layout: {
            topEnd: ['search', 'info']
        },
        order: [[3, 'asc']],
        stateSave: true,  //disables section column???
        select: {
            style: 'os' //,selector: 'td:first-child'
        },
        paging: true,
        pagingType: "simple_numbers",
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100, { label: 'All', value: -1 }],
        autoWidth: true,
        fixedColumns:true,
//         serverSide: true
//        fixedColumns: {
//                start: 3
//            },
//        scrollCollapse: true,
//        scrollY: 300,
//        "bAutoWidth": false,
//        aoColumns: myColumns,
////        scrollCollapse: true,
////        scrolly: 300,
////        scrollx: 300,
//        initComplete: onDataTableInitComplete
    });

}

function onDataTableInitComplete(){
    //    console.log("Datatable init complete");
    table.on('click', 'tbody td:not(:first-child)', function (e) {
        editor.inline(this,  { submit: 'allIfChanged' }); 
    });
    
    //
    //best Solution so far for problems with fixed header 
//    $('#table_rooms').scroll(function() {
//    if($(".fixedHeader-floating").is(":visible") ) {
//        $(".fixedHeader-floating").scrollLeft( $(this).scrollLeft() );
//    }}); 
}

function add_MT_rel_filter(){
    
    $('#table_rooms thead th:eq(0)').append(dropdownHtml); //to be set eqquivalent to the column index 
    $('#columnFilter').change(function() { 
        var filterValue = $(this).val();
//        console.log("Filter value:", filterValue); 
//        console.log("Before search:", table.column('MT-relevant:name').data());
        table.column('MT-relevant:name').search(filterValue).draw();
//        console.log("After search:", table.column('MT-relevant:name').data());
    });
}

function init_btn_4_dt() {
    let spacer = {
                    extend: 'spacer',
                    style: 'bar'
                };
    new $.fn.dataTable.Buttons(table, {
        buttons: [
            buttonRanges.map(button => ({
                    text: button.name,
                    className:'btn_vis',
                    action: function (e, dt, node, config) {
                        toggleColumns(dt, button.start , button.end , button.name); // -1 cause i deleted non working first column
                    }
                })),
            spacer,
            {
                text: 'w/ Data',
                className:'btn_vis',
                action: function (e, dt, node, config) {
                    checkAndToggleColumnsVisibility(dt);
                }
            },  
            spacer,
            'copy', 
            'excel', 
            'csv',
            spacer,
            { extend: 'create', editor: editor, 
                formButtons: [
                    {label: 'Show All', action:function(){this.show();}},
                    {label: 'Show Simple', action: function() { 
                            this.hide($.map(fieldDeclaration, function(item){ return item.name;  }));
                            this.show(['MT-relevant','Raumbezeichnung', 'Raumnr' ,'Raumbereich Nutzer', 'H6020']);
                        }
                    },
                    {label: 'Cancel', fn: function () { this.close();}},
                    'Save'
                ],
                formTitle: "Create new Entry"
            },
            { extend: 'edit', editor: editor,
                formButtons: [
                    {label: 'Show All', action:function(){this.show();}},
                    {label: 'Show Simple', action: function() { 
                            this.hide($.map(fieldDeclaration, function(item){ return item.name;  }));
                            this.show(['MT-relevant','Raumbezeichnung', 'Raumnr' ,'Raumbereich Nutzer', 'H6020']);
                    }},
                    {label: 'Cancel',fn: function () { this.close(); }, className: 'btn btn_invis'},
                    {label:'Save', fn: function () {this.submit();}, className: 'btn btn_vis'}
                ],
                formTitle: function ( editor, dt ) {
                    var rowData = dt.row({selected:true}).data();
                    return 'Editing data for '+rowData.Raumbezeichnung;
                }
            },
            { extend: 'remove', editor: editor },
            spacer,
            'selectAll',
            'selectNone' //, 'selectRows',//'selectColumns','selectCells'
        ]}).container().appendTo($('#TableCardHeader'));
    
    if ($("#roomElements").is(':hidden')) {
        $('#diy_searcher').hide();
    }
    const columns = table.columns().indexes();
    buttonRanges.forEach(button => {
        const isVisible = table.column(columns[button.start]).visible();
        const buttonElement = $(`.btn_vis:contains('${button.name}')`);
        if (!isVisible) {buttonElement.addClass('btn_invis');}
    });
}

function toggleColumns(table, startColumn, endColumn, button_name) {
    const columns = table.columns().indexes();
    var vis = !table.column(columns[endColumn]).visible();
//    console.log("Toggling uppon c: ", startColumn, vis, button_name);
    for (let i = startColumn; i <= endColumn; i++) {
        table.column(columns[i]).visible(vis);
//        console.log("Column", i);
    }
    const button = $(`.btn_vis:contains('${button_name}')`);
    if (vis) {
        button.removeClass('btn_invis');
    } else {
//        console.log("CSS added to", button_name);
        button.addClass('btn_invis');
    }
}

function checkAndToggleColumnsVisibility() {
    table.columns().every(function () {
        var column = this;
        var columnIndex = column.index();
        var columnName = column.header().textContent.trim();
        var hasNonEmptyCell = column.data().toArray().some(function (cellData) {
            return cellData !== null && cellData !== undefined && cellData !== '' && cellData !== '-' && cellData !== ' ' && cellData !== '  ' && cellData !== '   ' && cellData !== '.';
        });
        if (!hasNonEmptyCell) {
            if (column.visible) {
                console.log(columnName, " ...wird ausgeblendet");
            } else {
                console.log(columnName, " ...wird eingeblendet");
            }
            column.visible(!column.visible);
        }
    });
}

function init_showRoomElements_btn(){
    $("#showRoomElements").click(function () {
        if ($("#roomElements").is(':hidden')) {
            $(this).html("<i class='fas fa-caret-left'></i>");
            $("#additionalInfo").show();
            $('#diy_searcher').show();
        } else {
            $(this).html("<i class='fas fa-caret-right'></i>");
            $("#additionalInfo").hide();
            $('#diy_searcher').hide();
        }
    });
}

function init_editor(){
    editor = new DataTable.Editor({
        ajax: {
            url: 'get_rb_specs_data.php',
            dataSrc: ''    
        },
        table: '#table_rooms',
        idSrc: 'Raumnr',
        fields:  fieldDeclaration
        
    });
    console.log("Editor Loaded");
}

function init_table_click(){
    $('#table_rooms tbody').on('click', 'tr', function () {
        table.$('tr.info').removeClass('info');
        raumID = $('#table_rooms').DataTable().row($(this)).data().idTABELLE_Räume;
        $('#diy_searcher').val('');
        $.ajax({
            url: "setSessionVariables.php",
            data: {"roomID": raumID},
            type: "GET",
            success: function (data) {
                $("#RoomID").text(raumID);
                $.ajax({
                    url: "getRoomSpecifications2.php",
                    type: "GET",
                    success: function (data) {
                        $("#bauangaben").html(data);
                        $.ajax({
                            url: "getRoomElementsDetailed2.php",
                            type: "GET",
                            success: function (data) {
                                $("#roomElements").html(data);
                                $('#diy_searcher').on('keyup', function () {
                                    $('#tableRoomElements').DataTable().search(this.value).draw();
                                });
                            }
                        });
                    }
                });
            }
        });
    });
}

</script>
</body> 
</html>
