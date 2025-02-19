<?php
include "_utils.php";
session_start();
check_login();
$mysqli = utils_connect_sql();

function getGewerkOptions($mysqli, $projectID, $selectedGewerk) {
    $sql = "SELECT Gewerke_Nr, Bezeichnung, idTABELLE_Auftraggeber_Gewerke
            FROM tabelle_projekte 
            INNER JOIN tabelle_auftraggeber_gewerke ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes
            WHERE tabelle_projekte.idTABELLE_Projekte = ?
            ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = "<option value='0'>Bitte auswählen</option>";
    while ($row = $result->fetch_assoc()) {
        $selected = ($selectedGewerk == $row["idTABELLE_Auftraggeber_Gewerke"]) ? "selected" : "";
        $options .= "<option $selected value='{$row["idTABELLE_Auftraggeber_Gewerke"]}'>{$row["Gewerke_Nr"]} - {$row["Bezeichnung"]}</option>";
    }
    return $options;
}

function getGHGOptions($mysqli, $gewerkID, $selectedGHG) {
    $sql = "SELECT GHG, Bezeichnung, idtabelle_auftraggeber_GHG
            FROM tabelle_auftraggeber_ghg
            WHERE tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $gewerkID);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = "<option value='0'>Bitte auswählen</option>";
    while ($row = $result->fetch_assoc()) {
        $selected = ($selectedGHG == $row["idtabelle_auftraggeber_GHG"]) ? "selected" : "";
        $options .= "<option $selected value='{$row["idtabelle_auftraggeber_GHG"]}'>{$row["GHG"]} - {$row["Bezeichnung"]}</option>";
    }
    return $options;
}

function getGUGOptions($mysqli, $ghgID, $selectedGUG) {
    $sql = "SELECT idtabelle_auftraggeberg_GUG, GUG, Bezeichnung
            FROM tabelle_auftraggeberg_gug
            WHERE tabelle_auftraggeber_GHG_idtabelle_auftraggeber_GHG = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $ghgID);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = "<option value='0'>Bitte auswählen</option>";
    while ($row = $result->fetch_assoc()) {
        $selected = ($selectedGUG == $row["idtabelle_auftraggeberg_GUG"]) ? "selected" : "";
        $options .= "<option $selected value='{$row["idtabelle_auftraggeberg_GUG"]}'>{$row["GUG"]} - {$row["Bezeichnung"]}</option>";
    }
    return $options;
}

$filterValueGewerke = $_GET["filterValueGewerke"] ?? null;
$filterValueGHG = $_GET["filterValueGHG"] ?? null;
$filterValueGUG = $_GET["filterValueGUG"] ?? null;
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Element Gewerke</title>
</head>
<body>
<div class='col-12'>
    <div class='card'>
        <div class='card-body'>
            <form class='d-flex align-items-center flex-wrap'>
                <div class='form-group mr-2'>
                    <label for='gewerk'>Gewerk</label>
                    <select class='form-control form-control-sm' id='gewerk'>
                        <?php echo getGewerkOptions($mysqli, $_SESSION["projectID"], $filterValueGewerke); ?>
                    </select>
                </div>

                <div class='form-group mr-2'>
                    <label for='ghg'>GHG</label>
                    <select class='form-control form-control-sm' id='ghg'>
                        <?php
                        if ($filterValueGewerke) {
                            echo getGHGOptions($mysqli, $filterValueGewerke, $filterValueGHG);
                        } else {
                            echo "<option value='0'>Bitte Gewerk auswählen</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class='form-group mr-2'>
                    <label for='gug'>GUG</label>
                    <select class='form-control form-control-sm' id='gug'>
                        <?php
                        if ($filterValueGHG) {
                            echo getGUGOptions($mysqli, $filterValueGHG, $filterValueGUG);
                        } else {
                            echo "<option value='0'>Bitte GHG auswählen</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class='form-group'>
                    <label>&nbsp;</label>
                    <div>
                        <button type='button' id='saveElementGewerk' class='btn btn-outline-dark btn-sm' value='saveElementGewerk'>
                            <i class='far fa-save'></i> Gewerk speichern
                        </button>
                        <button type='button' id='saveElementGewerk94' class='btn btn-outline-dark btn-sm' value='saveElementGewerk2'>
                            <i class='far fa-save'></i> OrtsVeränderlich speichern
                        </button>
                        <button type='button' id='saveElementGewerk93' class='btn btn-outline-dark btn-sm' value='saveElementGewerk1'>
                            <i class='far fa-save'></i> OrtsFest speichern
                        </button>
                        <button type='button' id='saveElementGewerk91' class='btn btn-outline-dark btn-sm' value='saveElementGewerk6'>
                            <i class='far fa-save'></i> MetalMöbel speichern
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="_utils.js"></script>
<script>
    $('#ghg').change(function(){
        var ghgid = $('#ghg').val();
        var gewerkid = $('#gewerk').val();
        if(gewerkid !== '0' && ghgid !== '0'){
            $.ajax({
                url: "getElementGewerkeFiltered.php",
                data: {"filterValueGHG": ghgid, "filterValueGewerke": gewerkid},
                type: "GET",
                success: function(data){
                    $("#elementGewerk").html(data);
                }
            });
        }
    });

    $('#gewerk').change(function(){
        var gewerkid = $('#gewerk').val();
        if(gewerkid !== '0'){
            $.ajax({
                url: "getElementGewerkeFiltered.php",
                data: {"filterValueGewerke": gewerkid},
                type: "GET",
                success: function(data){
                    $("#elementGewerk").html(data);
                }
            });
        }
    });

    $("#saveElementGewerk").click(function(){
        if($('#gewerk').val() === "0"){
            makeToaster("Kein Gewerk ausgewählt!", false);
        } else {
            $.ajax({
                url: "saveElementGewerk.php",
                data: {
                    "gewerk": $('#gewerk').val(),
                    "ghg": $('#ghg').val(),
                    "gug": $('#gug').val()
                },
                type: "GET",
                success: function(data){
                    makeToaster(data.trim(), true);
                }
            });
        }
    });

    $("#saveElementGewerk94").click(function(){
        $.ajax({
            url: "saveElementGewerk.php",
            data: {"gewerk": 2, "ghg": $('#ghg').val(), "gug": $('#gug').val()},
            type: "GET",
            success: function(data){
                makeToaster(data.trim(), true);
            }
        });
    });

    $("#saveElementGewerk93").click(function(){
        $.ajax({
            url: "saveElementGewerk.php",
            data: {"gewerk": 1, "ghg": $('#ghg').val(), "gug": $('#gug').val()},
            type: "GET",
            success: function(data){
                makeToaster(data.trim(), true);
            }
        });
    });

    $("#saveElementGewerk91").click(function(){
        $.ajax({
            url: "saveElementGewerk.php",
            data: {"gewerk": 6, "ghg": $('#ghg').val(), "gug": $('#gug').val()},
            type: "GET",
            success: function(data){
                makeToaster(data.trim(), true);
            }
        });
    });
</script>
</body>
</html>

<?php $mysqli->close(); ?>
