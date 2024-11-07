<?php
session_start();
include '_utils.php';
check_login();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
    <head>
    </head>
    <body>
<?php
$_SESSION["elementID"] = $_GET["elementID"];
$mysqli = utils_connect_sql();

// Fetch Gewerk, GHG, and GUG from the database
$sql = "SELECT 
            tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, 
            tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, 
            tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
        FROM 
            tabelle_projekt_element_gewerk
        WHERE 
            tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = {$_SESSION['projectID']} 
            AND tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = {$_SESSION['elementID']};";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

if ($row) {
    $gewerk = $row["tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke"];
    $ghg = $row["tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG"];
    $gug = $row["tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG"];
} else {
    // Handle the case where no records were found
    $gewerk = null;
    $ghg = null;
    $gug = null;
}

// Fetch Gewerk options
$sql = "SELECT 
            tabelle_auftraggeber_gewerke.Gewerke_Nr, 
            tabelle_auftraggeber_gewerke.Bezeichnung, 
            tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
        FROM 
            tabelle_projekte 
        INNER JOIN 
            tabelle_auftraggeber_gewerke ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes
        WHERE 
            tabelle_projekte.idTABELLE_Projekte = {$_SESSION['projectID']}
        ORDER BY 
            tabelle_auftraggeber_gewerke.Gewerke_Nr;";
$result = $mysqli->query($sql);

echo "
 
        <div class='col-8'>
            <div class='card'>
                <div class='ml-4 mt-4 card-title'>
                    <form class='form-inline'>

                        <label class='m-4' for='gewerk'>Gewerk</label>
                        <select class='form-control form-control-sm' id='gewerk'>";

if ($gewerk) {
    echo "<option value=0>Bitte auswählen</option>";
    while ($row = $result->fetch_assoc()) {
        if ($gewerk == $row["idTABELLE_Auftraggeber_Gewerke"]) {
            echo "<option selected value=" . $row["idTABELLE_Auftraggeber_Gewerke"] . ">" . $row["Gewerke_Nr"] . " - " . $row["Bezeichnung"] . "</option>";
        } else {
            echo "<option value=" . $row["idTABELLE_Auftraggeber_Gewerke"] . ">" . $row["Gewerke_Nr"] . " - " . $row["Bezeichnung"] . "</option>";
        }
    }
} else {
    echo "<option selected value=0>Bitte auswählen</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value=" . $row["idTABELLE_Auftraggeber_Gewerke"] . ">" . $row["Gewerke_Nr"] . " - " . $row["Bezeichnung"] . "</option>";
    }
}

echo "
                        </select>

                        <label class='m-4' for='ghg'>GHG</label>
                        <select class='form-control form-control-sm' id='ghg'>";

if ($gewerk) {
    $sql = "SELECT 
                tabelle_auftraggeber_ghg.GHG, 
                tabelle_auftraggeber_ghg.Bezeichnung, 
                tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG
            FROM 
                tabelle_auftraggeber_ghg
            WHERE 
                tabelle_auftraggeber_ghg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = $gewerk;";
    $result = $mysqli->query($sql);
    
    if ($ghg) {
        echo "<option value=0>Bitte auswählen</option>";
        while ($row = $result->fetch_assoc()) {
            if ($ghg == $row["idtabelle_auftraggeber_GHG"]) {
                echo "<option selected value=" . $row["idtabelle_auftraggeber_GHG"] . ">" . $row["GHG"] . " - " . $row["Bezeichnung"] . "</option>";
            } else {
                echo "<option value=" . $row["idtabelle_auftraggeber_GHG"] . ">" . $row["GHG"] . " - " . $row["Bezeichnung"] . "</option>";
            }
        }
    } else {
        echo "<option selected value=0>Bitte auswählen</option>";
        while ($row = $result->fetch_assoc()) {
            echo "<option value=" . $row["idtabelle_auftraggeber_GHG"] . ">" . $row["GHG"] . " - " . $row["Bezeichnung"] . "</option>";
        }
    }
} else {
    echo "<option selected value=0>Bitte Gewerk auswählen</option>";
}

echo "
                        </select>		

                        <label class='m-4' for='gug'>GUG</label>
                        <select class='form-control form-control-sm' id='gug'>";

if ($gewerk) {
    if ($ghg) {
        $sql = "SELECT 
                    tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG, 
                    tabelle_auftraggeberg_gug.GUG, 
                    tabelle_auftraggeberg_gug.Bezeichnung
                FROM 
                    tabelle_auftraggeberg_gug
                WHERE 
                    tabelle_auftraggeberg_gug.tabelle_auftraggeber_GHG_idtabelle_auftraggeber_GHG = $ghg;";
        $result = $mysqli->query($sql);
        
        if ($gug) {
            echo "<option value=0>Bitte auswählen</option>";
            while ($row = $result->fetch_assoc()) {
                if ($gug == $row["idtabelle_auftraggeberg_GUG"]) {
                    echo "<option selected value=" . $row["idtabelle_auftraggeberg_GUG"] . ">" . $row["GUG"] . " - " . $row["Bezeichnung"] . "</option>";
                } else {
                    echo "<option value=" . $row["idtabelle_auftraggeberg_GUG"] . ">" . $row["GUG"] . " - " . $row["Bezeichnung"] . "</option>";
                }
            }
        } else {
            echo "<option selected value=0>Bitte auswählen</option>";
            while ($row = $result->fetch_assoc()) {
                echo "<option value=" . $row["idtabelle_auftraggeberg_GUG"] . ">" . $row["GUG"] . " - " . $row["Bezeichnung"] . "</option>";
            }
        }
    } else {
        echo "<option selected value=0>Bitte GHG auswählen</option>";
    }
} else {
    echo "<option selected value=0>Bitte Gewerk auswählen</option>";
}

echo "
                        </select>
                        
                        <button type='button' id='saveElementGewerk' class='btn btn-outline-dark btn-sm ml-1' value='saveElementGewerk'>
                            <i class='far fa-save'></i> Gewerk speichern
                        </button>	
                    </form>
                </div>
            </div>
        </div>

        <div class='col-4'>   
            <button type='button' id='saveElementGewerk94' class='btn btn-outline-dark btn-sm ml-1' value='saveElementGewerk2'>
                <i class='far fa-save'></i> Gewerk 94 speichern
            </button>
            <button type='button' id='saveElementGewerk93' class='btn btn-outline-dark btn-sm ml-1' value='saveElementGewerk1'>
                <i class='far fa-save'></i> Gewerk 93 speichern
            </button>    
        </div>
 ";

$mysqli->close();
?>




        <!-- Modal Info -->
        <div class='modal fade' id='infoModal' role='dialog'>
            <div class='modal-dialog modal-dialog-centered modal-sm'>	    
                <!-- Modal content-->
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title'>Info</h4>
                        <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
                    </div>
                    <div class='modal-body' id='infoBody'>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>OK</button>
                    </div>
                </div>

            </div>
        </div>

        <script src="_utils.js"></script> 
        <script>

            //GHG geändert
            $('#ghg').change(function () {
                var ghgid = $('#ghg').val();
                var gewerkid = $('#gewerk').val();
                if (gewerkid !== 0 && ghgid !== 0) {
                    $.ajax({
                        url: "getElementGewerkeFiltered.php",
                        data: {"filterValueGHG": ghgid, "filterValueGewerke": gewerkid},
                        type: "GET",
                        success: function (data) {
                            $("#elementGewerk").html(data);
                        }
                    });
                }
            });

            //Gewerk geändert
            $('#gewerk').change(function () {
                var gewerkid = $('#gewerk').val();
                if (gewerkid !== 0) {
                    $.ajax({
                        url: "getElementGewerkeFiltered.php",
                        data: {"filterValueGewerke": gewerkid},
                        type: "GET",
                        success: function (data) {
                            $("#elementGewerk").html(data);
                        }
                    });
                }
            });

            // Gewerk speichern
            $("button[value='saveElementGewerk']").click(function () {
                if ($('#gewerk').val() === "0") {
                    $("#infoBody").html(data);
                    $('#infoModal').modal('show');
                } else {
                    console.log($('#gewerk').val());
                    $.ajax({
                        url: "saveElementGewerk.php",
                        data: {"gewerk": $('#gewerk').val(), "ghg": $('#ghg').val(), "gug": $('#gug').val()},
                        type: "GET",
                        success: function (data) {
                            //		        	$("#infoBody").html(data);	

                            makeToaster(data.trim(), true);
                            //                                $('#infoModal').modal('show'); 
                        }
                    });
                }
            });

            $("button[value='saveElementGewerk2']").click(function () {
                $.ajax({
                    url: "saveElementGewerk.php",
                    data: {"gewerk": 2, "ghg": $('#ghg').val(), "gug": $('#gug').val()},
                    type: "GET",
                    success: function (data) {
                        makeToaster(data.trim(), true);
                    }
                });
            });

            $("button[value='saveElementGewerk1']").click(function () {
                $.ajax({
                    url: "saveElementGewerk.php",
                    data: {"gewerk": 1, "ghg": $('#ghg').val(), "gug": $('#gug').val()},
                    type: "GET",
                    success: function (data) {
                        makeToaster(data.trim(), true);
                    }
                });
            });


        </script> 
    </body>
</html>