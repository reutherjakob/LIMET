?php
session_start();
include '_utils.php';
init_page_serversides();
function fetch_data($mysqli, $sql) {
    $result = $mysqli->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}
function create_table($headers, $data, $id) {
    echo "<table class='table table-striped table-bordered table-sm' id='$id' cellspacing='0' width='100%'><thead><tr>";
    foreach ($headers as $header) {
        echo "<th>$header</th>";
    }
    echo "</tr></thead><tbody>";
    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            if ($key === 'MT-relevant') {
                echo "<td>" . ($value === '0' ? 'Nein' : 'Ja') . "</td>";
            } elseif ($key === 'Anmerkung FunktionBO' && strlen($value) > 0) {
                echo "<td><button type='button' class='btn btn-xs btn-outline-dark' id='buttonBO' value='$value' data-toggle='modal' data-target='#boModal'><i class='fa fa-comment'></i></button></td>";
            } else {
                echo "<td>$value</td>";
            }
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
}
$mysqli = utils_connect_sql();
$projectID = $_SESSION["projectID"];
$room_sql = "SELECT Raumnr, Raumbezeichnung, Nutzfläche, `Raumbereich Nutzer`, Geschoss, Bauetappe, Bauabschnitt, Raumnummer_Nutzer, `Anmerkung allgemein`, TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, idTABELLE_Räume, `MT-relevant`, `Anmerkung FunktionBO` FROM tabelle_räume WHERE tabelle_projekte_idTABELLE_Projekte=$projectID";
$rooms = fetch_data($mysqli, $room_sql);
$element_sql = "SELECT idTABELLE_Elemente, ElementID, Bezeichnung, Kurzbeschreibung FROM tabelle_elemente ORDER BY ElementID";
$elements = fetch_data($mysqli, $element_sql);
$gewerk_sql = "SELECT idtabelle_element_gewerke, Nummer, Gewerk FROM tabelle_element_gewerke ORDER BY Nummer";
$gewerke = fetch_data($mysqli, $gewerk_sql);
$mysqli->close();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"/>
<head>
    <title>RB-Detail</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    <link rel="icon" href="iphone_favicon.png"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
    <style>
    </style>
</head>
<body style="height:100%">
    <div class="container-fluid" >
        <div id="limet-navbar"></div>  
        <div class='row'>
            <div class='col-sm-8'>  
                <div class="mt-1 card">
                    <div class="card-header">Räume im Projekt
                        <label class="float-right">
                            Nur MT-relevante Räume: <input type="checkbox" id="filter_MTrelevantRooms" checked="true"> 
                        </label>
                    </div>
                    <div class="card-body"  style="overflow: auto; ">
                        <?php
                        $headers = ["ID", "Raumnr", "R.NR.Nutzer", "Raumbezeichnung", "Nutzfläche", "Raumbereich Nutzer", "Ebene", "MT-relevant", "BO"];
                        create_table($headers, $rooms, 'tableRooms');
                        ?>	
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="mt-1 card">
                    <div class="card-header">Vermerke zu Raum</div>
                    <div class="card-body" id="roomVermerke"></div>                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="mt-1 card">
                    <div class="card-header">Elemente im Raum</div>
                    <div class="card-body" id="roomElements"></div>                    
                </div>
            </div>
            <div class="col-sm-4">
                <div class="mt-1 card">
                    <div class="card-header">Variantenparameter</div>
                    <div class="card-body">
                        <div class="row" id="price"></div>
                        <div class="row" id="elementParameters"></div>
                    </div>                    
                </div>
                <div class="mt-1 card">
                    <div class="card-header">Bestandsdaten</div>
                    <div class="card-body" id="elementBestand"></div>                    
                </div>
                <div class="mt-1 card">
                    <div class="card-header">Standort/Verwendungsdaten</div>
                    <div class="card-body" id="elementVerwendung"></div>                    
                </div>
            </div>
        </div>
        <hr></hr>   
        <div class="mt-1 card">
            <div class="card-header"><button type="button" class="btn btn-outline-dark btn-xs" id="showDBElementData"><i class="fas fa-caret-right"></i></button></button>Datenbank-Elemente</div>
            <div class="card-body" style="display:none" id="DBElementData">                         
                <div class="row mt-1">
                    <div class="col-sm-6">
                        <div class="mt-1 card">
                            <div class="card-header">Elementgruppen</div>
                            <div class="card-body" id="elementGroups">
                                <?php
                                echo "<div class='form-group row'>
                                    <label class='control-label col-md-2' for='elementGewerk'>Gewerk</label>
                                    <div class='col-md-10'>
                                            <select class='form-control form-control-sm' id='elementGewerk' name='elementGewerk'>";
                                while ($row = $gewerke->fetch_assoc()) {
                                    echo "<option value=" . $row["idtabelle_element_gewerke"] . ">" . $row["Nummer"] . " - " . $row["Gewerk"] . "</option>";
                                }
                                
                                echo "</select></div></div>";
                                echo "<div class='form-group row'>
                                            <label class='control-label col-md-2' for='elementHauptgruppe'>Hauptgruppe</label>
                                            <div class='col-md-10'>
                                                    <select class='form-control form-control-sm' id='elementHauptgruppe' name='elementHauptgruppe'>
                                                            <option selected>Gewerk auswählen</option></select> </div> </div>";

                                echo "<div class='form-group row'>
                                            <label class='control-label col-md-2' for='elementGruppe'>Gruppe</label>
                                            <div class='col-md-10'>
                                                    <select class='form-control form-control-sm' id='elementGruppe' name='elementGruppe'>
                                                            <option selected>Gewerk auswählen</option></select> </div> </div>";
                                ?>
                            </div>
                        </div>
                        <div class="mt-1 card">
                            <div class="card-header">Elemente in DB</div>
                            <div class="card-body" id="elementsInDB">
                                <?php
                                $headers = ["ID", "ElementID", "Element", "Beschreibung"];
                                $tabelle_name = "tableElementsInDB";
                                create_table($headers, $element_sql, $tabelle_name);
                                ?>	

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <div class="mt-1 card">
                            <div class="card-header">Elementparameter</div>
                            <div class="card-body" id="elementParametersInDB"></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <div class="mt-1 card">
                            <div class="card-header">Elementkosten in anderen Projekten</div>
                            <div class="card-body" id="elementPricesInOtherProjects"></div>
                        </div>
                    </div>
                </div>
                <hr></hr>
                <div class="row mt-1">
                    <div class="col-md-6 col-sm-6">
                        <div class="mt-1 card">
                            <div class="card-header">Geräte</div>
                            <div class="card-body" id="devicesInDB"></div>
                        </div>					
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <div class="mt-1 card">
                            <div class="card-header">Geräteparameter</div>
                            <div class="card-body" id="deviceParametersInDB"></div>
                        </div>	
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <div class="mt-1 card">
                            <div class="card-header">Gerätepreise</div>
                            <div class="card-body" id="devicePrices"></div>
                        </div>	
                    </div>
                </div>
            </div>
        </div>        
    </div>
    <!--MODAL FÜR BO -->
    <div class='modal fade' id='boModal' role='dialog'>
        <div class='modal-dialog modal-md'>	    
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>BO-Anmerkung</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
                </div>
                <div class='modal-body' id='boModalBody'>

                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>OK</button>
                </div>
            </div>
        </div>
    </div>
    <script>
$.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableRooms') {
                return true;
            }

            if ($("#filter_MTrelevantRooms").is(':checked')) {
                if (data [7] === "Ja")
                {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
);

$(document).ready(function () {
    $("#elementParameters").hide();
    $("#elementBestand").hide();
    $("#elementVerwendung").hide();

    $('#tableRooms').DataTable({
        "select": true,
        "paging": true,
        "pagingType": "simple",
        "lengthChange": false,
        "pageLength": 10,
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "searchable": false
            }
        ],
        "order": [[1, "asc"]],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
        "mark": true
    });

    $('#tableElementsInDB').DataTable({
        "paging": true,
        "pagingType": "simple",
        "lengthChange": false,
        "pageLength": 10,
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "searchable": false
            }
        ],
        "info": false,
        "order": [[1, "asc"]],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
    });

    var table = $('#tableRooms').DataTable();

    $('#tableRooms tbody').on('click', 'tr', function () {

        if ($(this).hasClass('info')) {
        } else {
            $("#elementParameters").hide();
            $("#elementBestand").hide();
            $("#elementVerwendung").hide();

            table.$('tr.info').removeClass('info');
            $(this).addClass('info');
            var id = table.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"roomID": id},
                type: "GET",
                success: function (data) {
                    $("#RoomID").text(id);
                    $.ajax({
                        url: "getRoomVermerke.php",
                        type: "GET",
                        success: function (data) {
                            $("#roomVermerke").html(data);
                            $.ajax({
                                url: "getRoomElementsDetailed1.php",
                                type: "GET",
                                success: function (data) {
                                    $("#roomElements").html(data);
                                }
                            });
                        }
                    });
                    //}
                    //});	
                }
            });
        }
    });
    var table1 = $('#tableElementsInDB').DataTable();
    $('#tableElementsInDB tbody').on('click', 'tr', function () {
        if ($(this).hasClass('info')) {
        } else {
            table1.$('tr.info').removeClass('info');
            $(this).addClass('info');
            var elementID = table1.row($(this)).data()[0];
            $.ajax({
                url: "getStandardElementParameters.php",
                data: {"elementID": elementID},
                type: "GET",
                success: function (data) {
                    $("#elementParametersInDB").html(data);
                    $.ajax({
                        url: "getElementPricesInDifferentProjects.php",
                        data: {"elementID": elementID},
                        type: "GET",
                        success: function (data) {
                            $("#elementPricesInOtherProjects").html(data);
                            $.ajax({
                                url: "getDevicesToElement.php",
                                data: {"elementID": elementID},
                                type: "GET",
                                success: function (data) {
                                    $("#devicesInDB").html(data);
                                }
                            });

                        }
                    });

                }
            });
        }
});
$('#filter_MTrelevantRooms').change(function () {
    table.draw();
});
});
$("#showDBElementData").click(function () {
if ($("#DBElementData").is(':hidden')) {
    $(this).html("<i class='fas fa-caret-down'></i>");
    $("#DBElementData").show();
} else {
    $(this).html("<i class='fas fa-caret-right'></i>");
    $("#DBElementData").hide();
}
});
$('#elementGewerk').change(function () {
var gewerkID = this.value;
$.ajax({
    url: "getElementGroupsByGewerk.php",
    data: {"gewerkID": gewerkID},
    type: "GET",
    success: function (data) {
        $("#elementGroups").html(data);
    }
});

});
$("button[id='buttonBO']").click(function () {
$("#boModalBody").html(this.value);
});
    </script>
</body>
</html>
