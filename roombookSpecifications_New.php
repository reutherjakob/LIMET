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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
    
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
    
    <!--<script src="https://cdn.datatables.net/1.11.6/js/dataTables.editor.min.js"></script>-->

<style>
    .btn-xs {
      height: 22px;
      padding: 2px 5px;
      font-size: 12px;
      line-height: 1.5;
      border-radius: 3px; text/css;
    }
    .spacer {
        background-color:#E5E5E5; 
        z-index: -1;
        border: transparent ;
    }
    .buttons_toggle_vis{
        background-color: rgba(100, 140, 25, 0.2); 
        color: black;
        box-shadow: 0 1px 1px 0 rgba(0,0,0,0.2), 0 0px 0px 0 rgba(0,0,0,0.10);
    }
    .shadowed{
        box-shadow: 0 10px 10px 0 rgba(0,0,0,0.2), 0 0px 0px 0 rgba(0,0,0,0.10);
    }
    .buttons_toggle_invis{
        background-color: rgba(100, 0, 25, 0.2); 
        color: black; 
    }

    .table>thead>tr>th {
        background-color: rgba(100, 140, 25, 0.2);
    }
    .pull-right {
        float: right;
    }
    .card-imagex {
        height: 50px;  
        width: auto;  
    }
    
</style>
</head>
    
    


    
<body style="height:100%">
<div class="container-fluid ">
    <div id="limet-navbar" class='shadowed'> </div> <!-- Container für Navbar -->	
    <script> window.onload = function(){$.get("navbar.html", function(data){$("#limet-navbar").html(data); $('.navbar-nav').find('li:nth-child(3)').addClass('active'); });};</script>   
      
    <div class="mt-4 card">
        <div class="card-header" id='TableCardHeader'> </div>
        <div class="card-body" id = "table_container_div">
            <table class="table table-responsive table-striped table-bordered table-sm" width ="100%" id="table_rooms" > 
                <thead> <tr></tr> </thead>
                <tbody>  </tbody>
            </table> 
        </div>
    </div>  
    
    <div class='d-flex bd-highlight'>
        <div class='mt-4 mr-2 card flex-grow-1'>
                <div class="card-header card-imagex "><b>Bauangaben</b></div>
                <div class="card-body" id="bauangaben"></div>
        </div>
        <div class="mt-4 card">
            <div class="card">
                <div class="card-header card-imagex ">
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
    
    
      

      
<script> 
    var column_clicked;
    var row_clicked;
    
    $(document).ready(function(){
        fetchDataFromServer();
        init_DataTable();
        add_buttons();
       
         if($("#roomElements").is(':hidden')){$('#diy_searcher').hide();}
        $('#table_rooms thead th:eq(0)').append(dropdownHtml);
    }); 
    
    var dropdownHtml = 
        '<select id="columnFilter">' +'<option value="">All</option>' +
        '<option value="1">Ja</option>' +'<option value="0">Nein</option>' + 
        '</select>';
    
    const buttonRanges = [
                 { name: 'RAUM', start: 6, end: 22 },
                 { name: 'HKLS', start: 23, end: 28 },
                 { name: 'ELEK', start: 29, end: 36 },
                 { name: 'MEDGAS', start: 37, end:  50}, 
                 { name: 'LAB', start: 51, end: 100 },
                 { name: 'LAB-GAS', start: 53, end: 78 },
                 { name: 'LAB-ET', start: 79, end: 86 },
                 { name: 'LAB-HT', start: 87, end:94 },
                 { name: 'LAB-H2O', start: 95, end: 100 }
             ];
    
    $("#showRoomElements").click(function() {
            if($("#roomElements").is(':hidden')){
                $(this).html("<i class='fas fa-caret-left'></i>");
                $("#additionalInfo").show();
                $('#diy_searcher').show();
            }
            else {
                $(this).html("<i class='fas fa-caret-right'></i>");
                $("#additionalInfo").hide();
                $('#diy_searcher').hide();
            }
	});
    
    $('#table_rooms tbody').on( 'click', 'tr', function () {
    
        $('#table_rooms').DataTable().$('tr.info').removeClass('info');
        raumID = $('#table_rooms').DataTable().row($(this)).data().idTABELLE_Räume;
        
        $('#diy_searcher').val('');
        
        $.ajax({
            url : "setSessionVariables.php",
            data:{"roomID":raumID},
            type: "GET",
            success: function(data){
                $("#RoomID").text(raumID);
                    $.ajax({
                        url : "getRoomSpecifications2.php",
                        type: "GET",
                        success: function(data){
                            $("#bauangaben").html(data);
                            $.ajax({
                                        url : "getRoomElementsDetailed2.php",
                                        type: "GET",
                                        success: function(data){
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
    
    $.fn.dataTable.ext.search.push( //change table based on MT-rel. filter
        function( settings, data, dataIndex ) {
            if($("#columnFilter").val()==='1'){
                return data [3] === "Ja"; 
                }
            else if($("#columnFilter").val()==='0'){
                return data [3] === "Nein";
            } else{
                return true;
            }
            });     
          
    
    function add_buttons(){
        var table = $('#table_rooms').DataTable();
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                buttonRanges.map(button => ({
                    text: button.name,
                    action: function (e, dt, node, config) {
                        toggleColumns(button.start, button.end, button.name );
                    },
                    className: 'btn buttons_toggle_vis'
                })),
                { text: '',className: 'spacer'}, 
                {
                    text: 'w/ Data',
                    className: 'buttons_toggle_vis', 
                    action: function ( e, dt, node, config ) {
                        checkAndToggleColumnsVisibility();
                    }
                },
                { text: '',className: 'spacer'},
                'copy', 'excel', 'csv', 
                { text: '',className: 'spacer'},
//                'create',
//                'edit',
//                {
//                    text: '<i class="far fa-plus-square"></i> Raum Hinzufügen',
//                    action: function ( e, dt, node, config ) {  }
//                },
                { text: '',className: 'spacer'},
                'selectAll',
                'selectNone',
                //'selectRows',
                //'selectColumns',
                //'selectCells',
                { text: '',className: 'spacer'},
                {
                    text: 'Reload',
                    action: function ( e, dt, node, config ) {
                        fetchDataFromServer();
                    }
                }
            ]}).container().appendTo($('#TableCardHeader'));
            
            //init style adquatly  in  case of tablestatus: saved (columns will stay invisible)
            const columns = table.columns().indexes();
            buttonRanges.forEach(button => {
                const isVisible = table.column(columns[button.start]).visible();
                const buttonElement = $(`.buttons_toggle_vis:contains('${button.name}')`);
                if (!isVisible) {
                    buttonElement.addClass('buttons_toggle_invis'); 
                }
            });
   }

    function toggleColumns(startColumn, endColumn, button_name) {
        const table = $('#table_rooms').DataTable();
        const columns = table.columns().indexes(); 
        var vis = !table.column(columns[endColumn]).visible()
        console.log("Toggling uppon c: ", startColumn, vis, button_name);
        for (let i = startColumn; i <= endColumn; i++) {
            table.column(columns[i]).visible(vis);
        }
        const button = $(`.buttons_toggle_vis:contains('${button_name}')`);
        if (vis) {
            button.removeClass('buttons_toggle_invis'); 
        } else {
            console.log("CSS added to");
            button.addClass('buttons_toggle_invis');  
        }
    } 

    function checkAndToggleColumnsVisibility() {
        var table = $('#table_rooms').DataTable();
            table.columns().every(function () {
                var column = this;
                var columnIndex = column.index();
                var columnName = column.header().textContent.trim();
                var hasNonEmptyCell = column.data().toArray().some(function (cellData) {
                    return cellData !== null && cellData !== undefined && cellData !== '' && cellData !== '-' && cellData !== ' '&& cellData !== '  ' && cellData !== '   '&& cellData !== '.';
                });
                if(!hasNonEmptyCell){
                    if(column.visible){
                        console.log(columnName, " ...wird ausgeblendet");
                    }else {
                        console.log(columnName, " ...wird eingeblendet");
                    }    
                column.visible(!column.visible);
            }
        }); 
    }
    
//    const editor = new DataTable().Editor({
//        fields:[
//            {title: 'Raumnr Nutzer', data: 'Raumnummer_Nutzer'},
//            {title: 'Raumbereich', data: 'Raumbereich Nutzer'}
//        ],
//        table: '#table_rooms'
//    });
//    
//    $('#table_rooms').on('click', 'tbody td:not(:first-child)', function (e) {
//        editor.inline(this);
//    });
    
    function init_DataTable() {
        $('#table_rooms').DataTable({ 
            columns: [
//                {   title: '',
//                    data: null,
//                    orderable: false,
//                    render: DataTable.render.select()
//                },
                {title: 'Projek ID', data: 'tabelle_projekte_idTABELLE_Projekte',visible: false,searchable: false},
                {title: 'Raum ID', data: 'idTABELLE_Räume',visible: false,searchable: false},
                {title: 'Funktionsstellen ID', data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',visible: false,searchable: false}, 
                {title: 'MT-rel.', data: 'MT-relevant',
                    render: function (data) {return data === '1' ? 'Ja' : 'Nein';}
                },
                {title: 'Raumnr', data: 'Raumnr'},
                {title: 'Raumbez.', data: 'Raumbezeichnung', class: 'editable-text'},
                //RAUMDETAILS
                {title: 'Funkt.R.Nr', data: 'Funktionelle Raum Nr'},
                {title: 'DIN13080', name: 'DIN13080' , data: '', defaultContent:'' },
                {title: 'Raumnr Nutzer', data: 'Raumnummer_Nutzer'},
                {title: 'Raumbereich', data: 'Raumbereich Nutzer'},  
                {title: 'Geschoss', data: 'Geschoss'},
                {title: 'Bauetappe', data: 'Bauetappe'},
                {title: 'Bauabschnitt', data: 'Bauabschnitt'},
                {title: 'Nutzfläche', data: 'Nutzfläche'},
                {title: 'Abdunkelbar', data: 'Abdunkelbarkeit'},
                {title: 'Strahlenanw.', data: 'Strahlenanwendung'},
                {title: 'Laseranw.', data: 'Laseranwendung'},
                {title: 'Allg. Hygieneklasse', data: 'Allgemeine Hygieneklasse'},
                {title: 'Raumhoehe', data: 'Raumhoehe'},
                {title: 'Raumhoehe2', data: 'Raumhoehe 2'},
                {title: 'Belichtungsfläche', data: 'Belichtungsfläche'},
                {title: 'Umfang', data: 'Umfang'},
                {title: 'Volumen', data: 'Volumen'},
                //HKLS
                {title: 'H6020', data: 'H6020'},
                {title: 'GMP', data: 'GMP'},
                {title: 'ISO', data: 'ISO'},
                {title: 'HT_Waermeabgabe_W', data: 'HT_Waermeabgabe_W'},
                {title: 'HT_Spuele_Stk', data: 'HT_Spuele_Stk'},
                {title: 'HT_Kühlwasser', data: 'HT_Kühlwasser'},
                //ELEKTRO
                {title: 'AWG', data: 'Anwendungsgruppe'},
                {title: 'AV', data: 'AV'},
                {title: 'SV', data: 'SV'},
                {title: 'ZSV', data: 'ZSV'},
                {title: 'USV', data: 'USV'},
                {title: 'IT', data: 'IT Anbindung', render: function (data) {return data === '1' ? 'Ja' : 'Nein';}},
                {title: 'ET_Anschlussleistung_W', data: 'ET_Anschlussleistung_W'},
                {title: 'ET_RJ45-Ports', data: 'ET_RJ45-Ports'},
                //MEDGASE
                {title: '1 Kreis O2', data: '1 Kreis O2'},
                {title: '2 Kreis O2', data: '2 Kreis O2'},
                {title: 'O2', data: 'O2'},
                {title: '1 Kreis Va', data: '1 Kreis Va'},
                {title: '2 Kreis Va', data: '2 Kreis Va'},
                {title: 'VA', data: 'VA'},
                {title: '1 Kreis DL-5', data: '1 Kreis DL-5'},
                {title: '2 Kreis DL-5', data: '2 Kreis DL-5'},
                {title: 'DL-5', data: 'DL-5'},
                {title: 'DL-10', data: 'DL-10'},
                {title: 'DL-tech', data: 'DL-tech'},
                {title: 'CO2', data: 'CO2'}, 
                {title: 'NGA', data: 'NGA'},
                {title: 'N2O', data: 'N2O'},
                
                // LAB
                {title: 'VEXAT_Zone', data: 'VEXAT_Zone'},
                {title: 'Laserklasse', data: 'Laserklasse'},
                
                // LAB GASE
                {title: 'H2', data: 'H2'},
                {title: 'He', data: 'He'},
                {title: 'He-RF', data: 'He-RF'},
                {title: 'Ar', data: 'Ar'},
                {title: 'N2', data: 'N2'},
                {title: 'O2_Mangel', data: 'O2_Mangel'},
                {title: 'CO2_Melder', data: 'CO2_Melder'},
                {title: 'LHe', data: 'LHe'},
                {title: 'LN l/Tag', data: 'LN l/Tag'},
                {title: 'LN', data: 'LN'},
                {title: 'N2 Reinheit', data: 'N2 Reinheit'},
                {title: 'N2 l/min', data: 'N2 l/min'},
                {title: 'Ar Reinheit', data: 'Ar Reinheit'},
                {title: 'Ar l/min', data: 'Ar l/min'},
                {title: 'He Reinheit', data: 'He Reinheit'},
                {title: 'He l/min', data: 'He l/min'},
                {title: 'H2 Reinheit', data: 'H2 Reinheit'},
                {title: 'H2 l/min', data: 'H2 l/min'},
                {title: 'DL ISO 8573', data: 'DL ISO 8573'},
                {title: 'DL l/min', data: 'DL l/min'},
                {title: 'VA l/min', data: 'VA l/min'},
                {title: 'CO2 l/min', data: 'CO2 l/min'},
                {title: 'CO2 Reinheit', data: 'CO2 Reinheit'},
                {title: 'O2 l/min', data: 'O2 l/min'},
                {title: 'O2 l/min', data: 'O2 l/min'},
                {title: 'O2 Reinheit', data: 'O2 Reinheit'},
                
                // LAB ET
                {title: 'ET_64A_3Phasig_Einzelanschluss', data: 'ET_64A_3Phasig_Einzelanschluss'},
                {title: 'ET_32A_3Phasig_Einzelanschluss', data: 'ET_32A_3Phasig_Einzelanschluss'},
                {title: 'ET_16A_3Phasig_Einzelanschluss', data: 'ET_16A_3Phasig_Einzelanschluss'},
                {title: 'ET_Digestorium_MSR_230V_SV_Stk', data: 'ET_Digestorium_MSR_230V_SV_Stk'},
                {title: 'ET_5x10mm2_Digestorium_Stk', data: 'ET_5x10mm2_Digestorium_Stk'},
                {title: 'ET_5x10mm2_USV_Stk', data: 'ET_5x10mm2_USV_Stk'},
                {title: 'ET_5x10mm2_SV_Stk', data: 'ET_5x10mm2_SV_Stk'},
                {title: 'ET_5x10mm2_AV_Stk', data: 'ET_5x10mm2_AV_Stk'},
                
                //LAB HT
                {title: 'HT_Abluft_Vakuumpumpe', data: 'HT_Abluft_Vakuumpumpe'},
                {title: 'HT_Abluft_Schweissabsaugung_Stk', data: 'HT_Abluft_Schweissabsaugung_Stk'},
                {title: 'HT_Abluft_Esse_Stk', data: 'HT_Abluft_Esse_Stk'},
                {title: 'HT_Abluft_Rauchgasabzug_Stk', data: 'HT_Abluft_Rauchgasabzug_Stk'},
                {title: 'HT_Abluft_Digestorium_Stk', data: 'HT_Abluft_Digestorium_Stk'},
                {title: 'HT_Punktabsaugung_Stk', data: 'HT_Punktabsaugung_Stk'},
                {title: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk'},
                {title: 'HT_Abluft_Sicherheitsschrank_Stk', data: 'HT_Abluft_Sicherheitsschrank_Stk'},
                
                {title: 'Wasser Qual 3 l/min', data: 'Wasser Qual 3 l/min'},
                {title: 'Wasser Qual 2 l/Tag', data: 'Wasser Qual 2 l/Tag'},
                {title: 'Wasser Qual 1 l/Tag', data: 'Wasser Qual 1 l/Tag'},
                {title: 'Wasser Qual 3', data: 'Wasser Qual 3'},
                {title: 'Wasser Qual 2', data: 'Wasser Qual 2'},
                {title: 'Wasser Qual 1', data: 'Wasser Qual 1'}
                
            ],
//            columnDefs: [
//                {
//                    
//                    //targets: 3,  // table.columns().names().indexOf('MT-relevant'), //TODO
//                    //render: function (data) {
//                    //    return data === 1 ? 'Ja' : 'Nein';
//                    //}
//                }
//            ],
            stateSave: true,
            
            paging: true,
            pagingType: "simple_numbers",
            pageLength: 10,                 
            
            order: [[ 3, "asc" ]],
            orderCellsTop: true,
            select: {style: 'os'},
            lengthChange: true,         
            info: true,
            mark:true,          
            responsive: true,
            autoWidth:true
            
//                var table = $('#table_rooms').DataTable();
//                var columnData = table.column("DIN13080:name").data(); 
//                console.log("Data in the 'DIN' column:");
//                console.log(columnData);
//            }
            
        });
        
    }
    
    function updataTable_newData(newData , clear){
        var table =  $('#table_rooms').DataTable(); 
        if(clear){table.clear();}
        table.rows.add(newData); 
        table.draw(); 
    }
    
    function fetchDataFromServer() {
        $.ajax({
            type: 'GET',
            url: 'get_rb_specs_data.php',
            dataType: 'json', 
            success: function (response) {  
                //jsonData = response; 
                console.log("Fetched Data. Updating Table now. ");     
                updataTable_newData(response, true);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }
    
</script>
     
    
</body>
</html>
