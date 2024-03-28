<?php
session_start();

if(!isset($_SESSION))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   
   exit;
   }
 
include 'data_utils.php';
//check_login();
//$xxx = get_roombook_specs_results();
//print_whole_sql($xxx);
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
          border-radius: 3px;text/css
        }
    </style>
</head>
        

    
<body style="height:100%">
<div class="container-fluid" >
    <div id="limet-navbar"></div> <!-- Container für Navbar -->		
    <div class="mt-4 card">
        <div class="card-header">Räume im Projekt</div>
        <div class="card-body">
            <!--- <button onclick="fetchDataFromServer()">Klick mich!</button> --->
            <table id="tableDataDiv" class="display" > 
                <thead>
                    <tr>
                        
                    </tr>
                </thead>
            </table> 
        </div>
    </div>  
</div>
    
    
    
<script> 
    
    window.onload = function(){
        $.get("navbar.html", function(data){
            $("#limet-navbar").html(data);
            $('.navbar-nav').find('li:nth-child(3)')
              .addClass('active');
        });
    };    
 </script>   
      
<script> 
     function updataTable(newData){
        var table =  $('#tableDataDiv').DataTable(); 
        table.clear();
        table.rows.add(newData); 
        table.draw(); 
    }
    
    function fetchDataFromServer() {
        $.ajax({
            type: 'GET',
            url: 'get_rb_specs_data.php',
            //data: { get_param: 'value' }, // Optional parameters to send to the server
            dataType: 'json', 
            success: function (response) {
                //jsonData = response; 
                //console.log(response);     
                updataTable(response);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }
    
    $(document).ready(function(){
        
        fetchDataFromServer();
        
        $('#tableDataDiv').DataTable({
            language: {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},                                          
            columns: [
                {title: 'Projek ID', data: 'tabelle_projekte_idTABELLE_Projekte',"visible": false,"searchable": false},
                {title: 'Raum ID', data: 'idTABELLE_Räume',"visible": false,"searchable": false},
                {title: 'Funktionsstellen ID', data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',"visible": false,"searchable": false},
                {title: 'Raumnr', data: 'Raumnr'},
                {title: 'Raumbezeichnung', data: 'Raumbezeichnung'},
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
                {title: 'H6020', data: 'H6020'},
                {title: 'GMP', data: 'GMP'},
                {title: 'ISO', data: 'ISO'},
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
                {title: 'H2', data: 'H2'},
                {title: 'He', data: 'He'},
                {title: 'He-RF', data: 'He-RF'},
                {title: 'Ar', data: 'Ar'},
                {title: 'N2', data: 'N2'},
                {title: 'NGA', data: 'NGA'},
                {title: 'N2O', data: 'N2O'},
                {title: 'AV', data: 'AV'},
                {title: 'SV', data: 'SV'},
                {title: 'ZSV', data: 'ZSV'},
                {title: 'USV', data: 'USV'},
                {title: 'IT Anbindung', data: 'IT Anbindung'},
                {title: 'Anwendungsgruppe', data: 'Anwendungsgruppe'},
                {title: 'Allgemeine Hygieneklasse', data: 'Allgemeine Hygieneklasse'},
                {title: 'Raumhoehe', data: 'Raumhoehe'},
                {title: 'MT-relevant', data: 'MT-relevant'},
                {title: 'Raumhoehe 2', data: 'Raumhoehe 2'},
                {title: 'Belichtungsfläche', data: 'Belichtungsfläche'},
                {title: 'Umfang', data: 'Umfang'},
                {title: 'Volumen', data: 'Volumen'},
                {title: 'ET_Anschlussleistung_W', data: 'ET_Anschlussleistung_W'},
                {title: 'HT_Waermeabgabe_W', data: 'HT_Waermeabgabe_W'},
                {title: 'VEXAT_Zone', data: 'VEXAT_Zone'},
                {title: 'HT_Abluft_Vakuumpumpe', data: 'HT_Abluft_Vakuumpumpe'},
                {title: 'HT_Abluft_Schweissabsaugung_Stk', data: 'HT_Abluft_Schweissabsaugung_Stk'},
                {title: 'HT_Abluft_Esse_Stk', data: 'HT_Abluft_Esse_Stk'},
                {title: 'HT_Abluft_Rauchgasabzug_Stk', data: 'HT_Abluft_Rauchgasabzug_Stk'},
                {title: 'HT_Abluft_Digestorium_Stk', data: 'HT_Abluft_Digestorium_Stk'},
                {title: 'HT_Punktabsaugung_Stk', data: 'HT_Punktabsaugung_Stk'},
                {title: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk'},
                {title: 'HT_Abluft_Sicherheitsschrank_Stk', data: 'HT_Abluft_Sicherheitsschrank_Stk'},
                {title: 'HT_Spuele_Stk', data: 'HT_Spuele_Stk'},
                {title: 'HT_Kühlwasser', data: 'HT_Kühlwasser'},
                {title: 'O2_Mangel', data: 'O2_Mangel'},
                {title: 'CO2_Melder', data: 'CO2_Melder'},
                {title: 'ET_RJ45-Ports', data: 'ET_RJ45-Ports'},
                {title: 'ET_64A_3Phasig_Einzelanschluss', data: 'ET_64A_3Phasig_Einzelanschluss'},
                {title: 'ET_32A_3Phasig_Einzelanschluss', data: 'ET_32A_3Phasig_Einzelanschluss'},
                {title: 'ET_16A_3Phasig_Einzelanschluss', data: 'ET_16A_3Phasig_Einzelanschluss'},
                {title: 'ET_Digestorium_MSR_230V_SV_Stk', data: 'ET_Digestorium_MSR_230V_SV_Stk'},
                {title: 'ET_5x10mm2_Digestorium_Stk', data: 'ET_5x10mm2_Digestorium_Stk'},
                {title: 'ET_5x10mm2_USV_Stk', data: 'ET_5x10mm2_USV_Stk'},
                {title: 'ET_5x10mm2_SV_Stk', data: 'ET_5x10mm2_SV_Stk'},
                {title: 'ET_5x10mm2_AV_Stk', data: 'ET_5x10mm2_AV_Stk'},
                {title: 'Wasser Qual 3 l/min', data: 'Wasser Qual 3 l/min'},
                {title: 'Wasser Qual 2 l/Tag', data: 'Wasser Qual 2 l/Tag'},
                {title: 'Wasser Qual 1 l/Tag', data: 'Wasser Qual 1 l/Tag'},
                {title: 'Wasser Qual 3', data: 'Wasser Qual 3'},
                {title: 'Wasser Qual 2', data: 'Wasser Qual 2'},
                {title: 'Wasser Qual 1', data: 'Wasser Qual 1'},
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
                {title: 'Laserklasse', data: 'Laserklasse'}
            ]
        }); 
         
    });
    
</script>
    
    
</body>
</html>
