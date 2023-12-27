<?php
session_start();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Ausschreibungskalender</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="favicon.icon">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>



<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.1/b-2.3.3/b-html5-2.3.3/sl-1.5.0/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.13.1/b-2.3.3/b-html5-2.3.3/sl-1.5.0/datatables.min.js"></script>


<!--DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

<!--Bootstrap Toggle -->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

 <style>

.btn-xs {
  height: 22px;
  padding: 2px 5px;
  font-size: 12px;
  line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
  border-radius: 3px;
}

</style>
 
</head>

<body style="height:100%" id="bodyTenderLots">
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }

?>

<div class="container-fluid" >
    <div id="limet-navbar"></div> <!-- Container für Navbar Aufruf über onLoad -->		
    <div class='row'>
        <div class='col-sm-12'>
            <div class="mt-4 card">
                <div class="card-body"> 
                    <ul class="nav nav-tabs">
                        <?php
                            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                            /* change character set to utf8 */
                            if (!$mysqli->set_charset("utf8")) {
                                printf("Error loading character set utf8: %s\n", $mysqli->error);
                                exit();
                            }
                            $sql = "SELECT tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow, tabelle_workflow.Name
                                    FROM tabelle_workflow INNER JOIN (tabelle_lose_extern INNER JOIN tabelle_lot_workflow ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern) ON tabelle_workflow.idtabelle_workflow = tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow
                                    WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                    GROUP BY tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow, tabelle_workflow.Name;";
                            $result = $mysqli->query($sql);
                            $counter = 1;
                            $workflows = array();
                            while($row = $result->fetch_assoc()) {
                                if ($counter == 1){
                                    echo "<li class='nav-item'>";
                                    echo "<a class='nav-link active' href='#tab-".$row["tabelle_workflow_idtabelle_workflow"]."' data-toggle='tab'>".$row["Name"]."</a>";
                                    echo "</li>";                                    
                                }
                                else{
                                    echo "<li class='nav-item'>";
                                    echo "<a class='nav-link' href='#tab-".$row["tabelle_workflow_idtabelle_workflow"]."' data-toggle='tab'>".$row["Name"]."</a>";
                                    echo "</li>";
                                }
                                $workflows[$counter]=$row["tabelle_workflow_idtabelle_workflow"];
                                $counter++;
                            }
                        ?>
                    </ul>
                    <div class="tab-content">
                        <?php
                            $counter = 1;
                            foreach($workflows as $workFlow) {
                                if ($counter == 1){
                                    echo"<div class='tab-pane active' id='tab-".$workFlow."'>";
                                }
                                else{
                                    echo"<div class='tab-pane' id='tab-".$workFlow."'>";
                                }
                                // -----------------Workflowteile eines Workflows laden----------------------------                          
                                $sql = "SELECT tabelle_workflowteil.idtabelle_wofklowteil, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, tabelle_workflowteil.aufgabe, tabelle_workflow_has_tabelle_wofklowteil.TageMinDanach
                                        FROM tabelle_workflowteil INNER JOIN tabelle_workflow_has_tabelle_wofklowteil ON tabelle_workflowteil.idtabelle_wofklowteil = tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil
                                        WHERE (((tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow)=".$workFlow."))
                                        ORDER BY tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer;";

                                $result1 = $mysqli->query($sql);
                                $workflowTeile = array();

                                while ($row = $result1->fetch_assoc()) {
                                    $workflowTeile[$row['idtabelle_wofklowteil']]['idtabelle_wofklowteil'] = $row['idtabelle_wofklowteil'];
                                    $workflowTeile[$row['idtabelle_wofklowteil']]['Reihenfolgennummer'] = $row['Reihenfolgennummer'];    
                                    $workflowTeile[$row['idtabelle_wofklowteil']]['aufgabe'] = $row['aufgabe'];  
                                    $workflowTeile[$row['idtabelle_wofklowteil']]['TageMinDanach'] = $row['TageMinDanach'];  
                                }
                                //-----------------------------------------------------------------------------------                                                                                                                                                                                       
                                
                                echo "<table id='table_".$workFlow."' class='table table-striped table-bordered table-sm' cellspacing='0' width='100%'>
                                <thead><tr>
                                <th rowspan='2'>lotID</th>
                                <th rowspan='2'>Nummer</th>
                                <th rowspan='2'>Bezeichnung</th>
                                <th rowspan='2'>Verfahren</th>
                                <th rowspan='2'>Status</th>
                                <th rowspan='2'></th>";

                                foreach($workflowTeile as $array) {                 
                                    echo "<th colspan='3'>".$array['Reihenfolgennummer']."-".$array['aufgabe']."</th>";                               
                                }                                                       
                                echo "</tr>
                                <tr>";
                                $counterWorkFlowTeile = 0;    
                                foreach($workflowTeile as $array) {                 
                                    echo "<th>Soll-Datum</th>
                                    <th>Ist-Datum</th>";
                                    $counterWorkFlowTeile++;
                                    if($counterWorkFlowTeile < count($workflowTeile)){
                                        echo "<th>Abstand</th>"; 
                                    }                                
                                }  
                                echo  "</tr></thead><tbody>";                                                               

                                $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Verfahren, tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil, DATE_FORMAT(DATE(tabelle_lot_workflow.Timestamp_Ist), '%Y-%m-%d') as ISTDATE, DATE_FORMAT(DATE(tabelle_lot_workflow.Timestamp_Soll), '%Y-%m-%d') as SOLLDATE, tabelle_lot_workflow.Abgeschlossen, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow
                                                FROM tabelle_workflow_has_tabelle_wofklowteil INNER JOIN (tabelle_lose_extern INNER JOIN tabelle_lot_workflow ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern) ON (tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow = tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow) AND (tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil)
                                                WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow)=".$workFlow."))
                                        ORDER BY tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer;";

                                $result = $mysqli->query($sql);     
                                $idLot = 0;
                                $sollDatumAlt = "0000-00-00";


                                while($row = $result->fetch_assoc()) { 
                                    if($idLot != $row["idtabelle_Lose_Extern"]){
                                        if($idLot != 0){
                                            echo "</tr>";
                                        }
                                        echo "<tr>";
                                        echo "<td>".$row["idtabelle_Lose_Extern"]."</td>";                                               
                                        echo "<td>".$row["LosNr_Extern"]."</td>";
                                        echo "<td>".$row["LosBezeichnung_Extern"]."</td>";
                                        echo "<td>".$row["Verfahren"]."</td>";
                                        echo "<td align='center'>";
                                            switch ($row["Vergabe_abgeschlossen"]) {
                                                case 0:
                                                    //echo "<b><font color='red'>&#10007;</font></b>";
                                                    echo "<span class='badge badge-pill badge-danger'>Offen</span>";
                                                    break;
                                                case 1:
                                                    //echo "<b><font color='green'>&#10003;</font></b>";
                                                    echo "<span class='badge badge-pill badge-success'>Fertig</span>";
                                                    break;
                                                case 2:
                                                    //echo "<b><font color='blue'>&#8776;</font></b>";
                                                    echo "<span class='badge badge-pill badge-primary'>Wartend</span>";
                                                    break;
                                            }									
                                        echo "</td>";                                         
                                        echo "<td><button type='button' id='".$row["idtabelle_Lose_Extern"]."' class='btn btn-outline-dark btn-xs float-right' value='calculateDates' data-toggle='modal' data-target='#claculateDatesModal'>Berechnen <i class='far fa-calendar-check'></i></button>"
                                                . "</td>";
                                        if($row["SOLLDATE"] == "0000-00-00"){
                                            echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm' size='10' id='SOLLDATE-".$row["idtabelle_Lose_Extern"]."-".$row["tabelle_wofklowteil_idtabelle_wofklowteil"]."-".$row["tabelle_workflow_idtabelle_workflow"]."'/>-"                                            
                                            . "<button type='button' name='save_solldate' id='SAVE-SOLLDATE,".$row["idtabelle_Lose_Extern"].",".$row["tabelle_wofklowteil_idtabelle_wofklowteil"].",".$row["tabelle_workflow_idtabelle_workflow"]."' class='btn btn-outline-dark btn-xs'><i class='far fa-save'></i></button>"
                                            . "</form>"
                                            . "</td>";
                                        }
                                        else{
                                            echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm' size='10' id='SOLLDATE-".$row["idtabelle_Lose_Extern"]."-".$row["tabelle_wofklowteil_idtabelle_wofklowteil"]."-".$row["tabelle_workflow_idtabelle_workflow"]."' value='".$row["SOLLDATE"]."'/>"
                                            . "<button type='button' name='save_solldate' id='SAVE-SOLLDATE,".$row["idtabelle_Lose_Extern"].",".$row["tabelle_wofklowteil_idtabelle_wofklowteil"].",".$row["tabelle_workflow_idtabelle_workflow"]."' class='btn btn-outline-dark btn-xs'><i class='far fa-save'></i></button>"
                                            . "</form>"
                                            . "<span style='display:none'>".$row["SOLLDATE"]."</span></td>";
                                        }
                                        if($row["ISTDATE"] == "0000-00-00"){
                                            echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm' size='10' id='ISTDATE-".$row["idtabelle_Lose_Extern"]."-".$row["tabelle_wofklowteil_idtabelle_wofklowteil"]."-".$row["tabelle_workflow_idtabelle_workflow"]."'/>"
                                            . "<button type='button' name='save_istdate' id='SAVE-ISTDATE,".$row["idtabelle_Lose_Extern"].",".$row["tabelle_wofklowteil_idtabelle_wofklowteil"].",".$row["tabelle_workflow_idtabelle_workflow"]."' class='btn btn-outline-dark btn-xs'><i class='far fa-save'></i></button>"
                                            . "</form></td>";
                                        }
                                        else{
                                            echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_istdate' class='form-control form-control-sm' size='10' id='ISTDATE-".$row["idtabelle_Lose_Extern"]."-".$row["tabelle_wofklowteil_idtabelle_wofklowteil"]."-".$row["tabelle_workflow_idtabelle_workflow"]."' value='".$row["ISTDATE"]."'/>"
                                            . "<button type='button' name='save_istdate' id='SAVE-ISTDATE,".$row["idtabelle_Lose_Extern"].",".$row["tabelle_wofklowteil_idtabelle_wofklowteil"].",".$row["tabelle_workflow_idtabelle_workflow"]."' class='btn btn-outline-dark btn-xs'><i class='far fa-save'></i></button>"
                                            . "</form>"
                                            . "<span style='display:none'>".$row["ISTDATE"]."</span></td>";
                                        } 	                                    
                                    }
                                    else{
                                        $daysBetween = round((strtotime($row["SOLLDATE"]) - strtotime($sollDatumAlt))/(60*60*24));
                                        if($daysBetween >= $sollAbstandDanach){
                                            echo "<td style='text-align:center'><span class='badge badge-pill badge-success'>".$daysBetween."</span> / <span class='badge badge-pill badge-secondary'>".$sollAbstandDanach."</span></td>"; 
                                        }
                                        else{
                                            echo "<td style='text-align:center'><span class='badge badge-pill badge-danger'>".$daysBetween."</span> / <span class='badge badge-pill badge-secondary'>".$sollAbstandDanach."</span></td>"; 
                                        }
                                        if($row["SOLLDATE"] == "0000-00-00"){
                                            echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm' size='10' id='SOLLDATE-".$row["idtabelle_Lose_Extern"]."-".$row["tabelle_wofklowteil_idtabelle_wofklowteil"]."-".$row["tabelle_workflow_idtabelle_workflow"]."'/>"
                                            . "<button type='button' name='save_solldate' id='SAVE-SOLLDATE,".$row["idtabelle_Lose_Extern"].",".$row["tabelle_wofklowteil_idtabelle_wofklowteil"].",".$row["tabelle_workflow_idtabelle_workflow"]."' class='btn btn-outline-dark btn-xs'><i class='far fa-save'></i></button>"
                                            . "</form></td>";
                                        }
                                        else{
                                            echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm' size='10' id='SOLLDATE-".$row["idtabelle_Lose_Extern"]."-".$row["tabelle_wofklowteil_idtabelle_wofklowteil"]."-".$row["tabelle_workflow_idtabelle_workflow"]."' value='".$row["SOLLDATE"]."'/>"
                                            . "<button type='button' name='save_solldate' id='SAVE-SOLLDATE,".$row["idtabelle_Lose_Extern"].",".$row["tabelle_wofklowteil_idtabelle_wofklowteil"].",".$row["tabelle_workflow_idtabelle_workflow"]."' class='btn btn-outline-dark btn-xs'><i class='far fa-save'></i></button>"
                                            . "</form>"
                                            . "<span style='display:none'>".$row["SOLLDATE"]."</span></td>";
                                        }
                                        if($row["ISTDATE"] == "0000-00-00"){
                                            echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_solldate' class='form-control form-control-sm' size='10' id='ISTDATE-".$row["idtabelle_Lose_Extern"]."-".$row["tabelle_wofklowteil_idtabelle_wofklowteil"]."-".$row["tabelle_workflow_idtabelle_workflow"]."'/>"
                                            . "<button type='button' name='save_istdate' id='SAVE-ISTDATE,".$row["idtabelle_Lose_Extern"].",".$row["tabelle_wofklowteil_idtabelle_wofklowteil"].",".$row["tabelle_workflow_idtabelle_workflow"]."' class='btn btn-outline-dark btn-xs'><i class='far fa-save'></i></button>"
                                            . "</form></td>";
                                        }
                                        else{
                                            echo "<td><form class='form-inline'>"
                                            . "<input type='text' name='input_istdate' class='form-control form-control-sm' size='10' id='ISTDATE-".$row["idtabelle_Lose_Extern"]."-".$row["tabelle_wofklowteil_idtabelle_wofklowteil"]."-".$row["tabelle_workflow_idtabelle_workflow"]."' value='".$row["ISTDATE"]."'/>"
                                            . "<button type='button' name='save_istdate' id='SAVE-ISTDATE,".$row["idtabelle_Lose_Extern"].",".$row["tabelle_wofklowteil_idtabelle_wofklowteil"].",".$row["tabelle_workflow_idtabelle_workflow"]."' class='btn btn-outline-dark btn-xs'><i class='far fa-save'></i></button>"
                                            . "</form>"
                                            . "<span style='display:none'>".$row["ISTDATE"]."</span></td>";
                                        }  
                                    }
                                    $idLot = $row["idtabelle_Lose_Extern"];
                                    $sollDatumAlt = $row["SOLLDATE"];
                                    $sollAbstandDanach = $workflowTeile[$row["tabelle_wofklowteil_idtabelle_wofklowteil"]]['TageMinDanach'];
                                }
                                echo "</tr>";     
                                echo "</tbody></table>";
                                echo "</div>";    
                                $counter++;
                            }  
                            $mysqli ->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-sm-6'>  
            <div class="mt-4 card">
                <div class="card-header">Bauphasen im Los                        
                </div>
                <div class="card-body" id="lotBauphasen">                        
                </div>
            </div> 
        </div>
    </div>
</div>    
</body>
    
    <div class='modal fade' id='claculateDatesModal' role='dialog'>
        <div class='modal-dialog modal-md'>
            <div class='modal-content'>
                <div class='modal-header'>	          
                  <h4 class='modal-title'>Daten automatisch berechnen</h4>
                  <button type='button' class='close' data-dismiss='modal'>&times;</button>
                </div>
                <div class='modal-body' id='mbody'>Wollen Sie die Soll-Daten automatisiert berechnen und bestehende Werte überschreiben?
                </div>
                <div class='modal-footer'>
                    <input type='button' id='updateTenderWorkflowDates' class='btn btn-success btn-sm' value='Ja' data-dismiss='modal'></input>
                    <button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Nein</button>
                </div>
            </div>
        </div>
    </div>
<script>   
    //Load Navbar onLoad
    window.onload = function(){
        $.get("navbar.html", function(data){
            $("#limet-navbar").html(data);
            $('.navbar-nav').find('li:nth-child(3)')
              .addClass('active');
        });
    };
    
    $(document).ready(function () {
        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
        
        $('table.table').DataTable( {
            "select": true,
            "searching": true,
            "paging": false,
            "lengthChange": false,
            "order": [[ 1, "asc" ]],
            "orderMulti": true,
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [ 5 ],
                    "searchable": false,
                    "sortable": false
                }
            ],
            dom: 'Bfrtip',
            "buttons": [
                'excel'
                /*
                {extend:'excel',exportOptions: {format: {
                    body: function ( data, row, column, node ) {            
                        //check if type is input using jquery
                        return $(data).is("form") ?
                        $(data).find('input:text').val():
                        data;
                    }
                }
                }}
                */
            ],
            stateSave: true
        } );
        
        var table = $('table.table').DataTable();
        $('table.table tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('info') ) {
            }	            
            else {   
                lotId = table.row( $(this) ).data()[0];
                $.ajax({
                    url : "getBauphasenToLot.php",
                    data:{"lotID":lotId},
                    type: "GET",
                    success: function(data){
                        $("#lotBauphasen").html(data);                            	
                    }
                });	

            }
        } );
        
        
        $("input[name='input_solldate']").datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked",
            daysOfWeekDisabled: [0,6]
        });
        

    });    
    
    //Soll-Daten automatisiert updaten
    $("#updateTenderWorkflowDates").click(function(){
        $.ajax({
            url : "updateTenderWorkflowDates.php",
            data:{"lotID":lotId},
            type: "GET",
            success: function(data){
                alert(data);
                location.reload(); 
            }
        });                        
    });
    
    //Soll-Datum einzeln updaten
    $("button[name='save_solldate']").click(function(){
        var ID = this.id;
        var newString = ID.split(",");
        var date = $('input[id=SOLLDATE-'+newString[1]+'-'+newString[2]+'-'+newString[3]+']').val();
        
        $.ajax({
            url : "updateTenderWorkflowDate.php",
            data:{"lotID":newString[1],"workflowTeilID":newString[2],"workflowID":newString[3],"date":date},
            type: "GET",
            success: function(data){
                alert(data);
            }
        });                                                
    });
    
    //Ist-Datum einzeln updaten
    $("button[name='save_istdate']").click(function(){
        var ID = this.id;
        var newString = ID.split(",");
        var date = $('input[id=ISTDATE-'+newString[1]+'-'+newString[2]+'-'+newString[3]+']').val();
        
        $.ajax({
            url : "updateTenderWorkflowDateIST.php",
            data:{"lotID":newString[1],"workflowTeilID":newString[2],"workflowID":newString[3],"date":date},
            type: "GET",
            success: function(data){
                alert(data);
            }
        });                                                
    });
    
    //Soll-Daten automatisiert updaten
    $("#updateTenderWorkflowDates").click(function(){
        $.ajax({
            url : "updateTenderWorkflowDates.php",
            data:{"lotID":lotId},
            type: "GET",
            success: function(data){
                alert(data);
                location.reload(); 
            }
        });                        
    });
    
</script>


</html>
