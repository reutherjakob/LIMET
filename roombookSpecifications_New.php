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
        
    </style>
</head>
    
    


    
<body style="height:100%">
<div class="container-fluid ">
    <div id="limet-navbar" class='shadowed'> </div> <!-- Container für Navbar -->	
        <script> window.onload = function(){
                $.get("navbar.html", function(data){
                    $("#limet-navbar").html(data);
                    $('.navbar-nav').find('li:nth-child(3)')
                      .addClass('active');
                });
            };        
        </script>   
    
        
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
                <div class="card-header"><b>Bauangaben</b></div>
                <div class="card-body" id="bauangaben"></div>
        </div>
        <div class="mt-4 card">
            <div class="card">
                <div class="card-header"><button type="button" class="btn btn-outline-dark btn-xs" id="showRoomElements"><i class="fas fa-caret-left"></i></button></div>
                <div class="card-body" id ="additionalInfo">
                    <p id="roomElements">
                    <p id="elementParameters"></div>
                </div> 
            </div>        
    </div>
    
<!--    <div id="raum-aendern"> </div> 
        <script> window.onload = function(){
                    $.get("roombookChangeRoom_modal.html", function(data){
                        $("#raum-aendern").html(data);
                    }}
        </script>-->
    
</div>
    
    
      

      
<script> 
    
    
    $("#showRoomElements").click(function() {
            if($("#roomElements").is(':hidden')){
                $(this).html("<i class='fas fa-caret-left'></i>");
                $("#additionalInfo").show();
            }
            else {
                $(this).html("<i class='fas fa-caret-right'></i>");
                $("#additionalInfo").hide();
            }
	});
    
    $('#table_rooms tbody').on( 'click', 'tr', function () {
        console.log("clicked Table");
        $('#table_rooms').DataTable().$('tr.info').removeClass('info');
        raumID = $('#table_rooms').DataTable().row( $(this) ).data()[0];
        $.ajax({
            url : "setSessionVariables.php",
            data:{"roomID":raumID},
            type: "GET",
            success: function(data){
                $("#RoomID").text(raumID);
                                    $.ajax({
                                            url : "getRoomSpecifications1.php",
                                            type: "GET",
                                            success: function(data){
                                                $("#bauangaben").html(data);
                                                                $.ajax({
                                                                            url : "getRoomElementsDetailed.php",
                                                                            type: "GET",
                                                                            success: function(data){
                                                                                $("#roomElements").html(data);


                                                                            } 
                                                                    });

                                           } 
                                    });							   
                          
            } 
    });
    });
    
    $(document).ready(function(){
        fetchDataFromServer();
        init_DataTable();
        add_buttons();
        button_style_init();
        
        $('#table_rooms thead th:eq(0)').append(dropdownHtml);
        
    }); 
    
    //MT RELEVANT FILTER
    var dropdownHtml = '<select id="columnFilter">' +
                     '<option value="">All</option>' +
                     '<option value="1">Ja</option>' +
                     '<option value="0">Nein</option>' +
                     '</select>';

    
    $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                if ( settings.nTable.id !== 'table_rooms' ) {
                    return true;
                }      
                if($("#columnFilter").val()==='1'){
                    //console.log(data[3]);
                    if (data [3] === "Ja")
                    {
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    if($("#columnFilter").val()==='0'){
                        if (data [3] === "Nein")
                        {
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                    else{
                        return true;
                    }
                }
            }
    );     
    
    $('#columnFilter').change( function() {
        console.log("Redrawing");
        $('#table_rooms').DataTable().draw();

    } );
    
    const buttonRanges = [
                 { name: 'RAUM', start: 6, end: 21 },
                 { name: 'HKLS', start: 22, end: 27 },
                 { name: 'ELEK', start: 28, end: 35 },
                 { name: 'MEDGAS', start: 36, end: 49 },
                 { name: 'LAB', start: 50, end: 99 }
             ];
    
    function button_style_init() {
        const table = $('#table_rooms').DataTable();
        const columns = table.columns().indexes();
        buttonRanges.forEach(button => {
            const isVisible = table.column(columns[button.start]).visible();
            const buttonElement = $(`.buttons_toggle_vis:contains('${button.name}')`);
            if (!isVisible) {
                buttonElement.addClass('buttons_toggle_invis'); 
            }
    });
}
    
    function get_matching_button(columnIndex) {
        const matchedButton = buttonRanges.find(button => columnIndex >= button.start && columnIndex <= button.end);
        return matchedButton ? matchedButton.name : 'Unknown Button';
    }
    
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
                    text: 'Datenlose \n ausblenden',
                    className: 'buttons_toggle_vis', 
                    action: function ( e, dt, node, config ) {
                        checkAndToggleColumnsVisibility();
                    }
                },
                { text: '',className: 'spacer'},
                'copy', 'excel', 'csv', 
                
                
                { text: '',className: 'spacer'},
                {
                    text: '<i class="far fa-plus-square"></i> Raum Hinzufügen',
                    action: function ( e, dt, node, config ) {  }
                },
                { text: '',className: 'spacer'},
                'selectAll',
                'selectNone',
                'selectRows',
                'selectColumns',
                'selectCells',
                { text: '',className: 'spacer'},
                {
                    text: 'Reload',
                    action: function ( e, dt, node, config ) {
                        fetchDataFromServer();
                    }
                }
            ]}).container().appendTo($('#TableCardHeader'));
   }
   
   
    function toggleColumns(startColumn, endColumn, button_name) {
        const table = $('#table_rooms').DataTable();
        const columns = table.columns().indexes(); 
        var vis = !table.column(columns[endColumn]).visible()
        console.log("Toggling uppon c: ", startColumn, vis);
        for (let i = startColumn; i <= endColumn; i++) {
            table.column(columns[i]).visible(vis);
        }
        const buttonName = get_matching_button(startColumn); // Assuming you have a function to get the button name
        const button = $(`.buttons_toggle_vis:contains('${buttonName}')`);
        if (vis) {
            button.removeClass('buttons_toggle_invis'); 
        } else {
            console.log("CSS added to", buttonName)
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
                return cellData !== null && cellData !== undefined && cellData !== '' && cellData !== '-' && cellData !== '.';
            });
            if(!hasNonEmptyCell){
                console.log(columnName, " ...ausgeblendet");
                column.visible(!column.visible);
            }
        }); 
    }
    

    function init_DataTable() {
        $('#table_rooms').DataTable({ 
            columns: [
                {title: 'Projek ID', data: 'tabelle_projekte_idTABELLE_Projekte',visible: false,searchable: false},
                {title: 'Raum ID', data: 'idTABELLE_Räume',visible: false,searchable: false},
                {title: 'Funktionsstellen ID', data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',visible: false,searchable: false}, 
                {title: 'MT-rel.', data: 'MT-relevant',
                    render: function (data) {
                        if(typeof data !== 'string')
                        {
                            alert("Check Console");
                            console.log("Datentyp MT-rel", typeof data);
                        }
                        return data === '1' ? 'Ja' : 'Nein';
                    }
                },
                {title: 'Raumnr', data: 'Raumnr'},
                {title: 'Raumbezeichnung', data: 'Raumbezeichnung', class: 'editable-text'},
                //RAUMDETAILS
                {title: 'Funktionelle Raum Nr', data: 'Funktionelle Raum Nr'},
                {title: 'Raumnummer_Nutzer', data: 'Raumnummer_Nutzer'},
                {title: 'Raumbereich Nutzer', data: 'Raumbereich Nutzer'},  
                {title: 'Geschoss', data: 'Geschoss'},
                {title: 'Bauetappe', data: 'Bauetappe'},
                {title: 'Bauabschnitt', data: 'Bauabschnitt'},
                {title: 'Nutzfläche', data: 'Nutzfläche'},
                {title: 'Abdunkelbarkeit', data: 'Abdunkelbarkeit'},
                {title: 'Strahlenanwendung', data: 'Strahlenanwendung'},
                {title: 'Laseranwendung', data: 'Laseranwendung'},
                {title: 'Allgemeine Hygieneklasse', data: 'Allgemeine Hygieneklasse'},
                {title: 'Raumhoehe', data: 'Raumhoehe'},
                {title: 'Raumhoehe 2', data: 'Raumhoehe 2'},
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
                {title: 'AV', data: 'AV'},
                {title: 'SV', data: 'SV'},
                {title: 'ZSV', data: 'ZSV'},
                {title: 'USV', data: 'USV'},
                {title: 'IT Anbindung', data: 'IT Anbindung'},
                {title: 'Anwendungsgruppe', data: 'Anwendungsgruppe'},
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
                {title: 'Laserklasse', data: 'Laserklasse'},
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
            select: {
                style: 'true'
            },
            lengthChange: true,         
            info: true,
            mark:true,          
            responsive: true,
            autoWidth:true,       
        });
        
    }
    
    function updataTable_newData(newData){
        var table =  $('#table_rooms').DataTable(); 
        table.clear();
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
                updataTable_newData(response);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }
    
</script>
     
    
</body>
</html>
