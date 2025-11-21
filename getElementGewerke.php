<?php
session_start();
require_once 'utils/_utils.php';
check_login();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <title> get el gewerk</title></head>
<body>
<?php
$_SESSION["elementID"] = $_GET["elementID"];
$mysqli = utils_connect_sql();

// Function to fetch Gewerk, GHG, and GUG
function fetchGewerkeData($mysqli, $projectID, $elementID)
{
    $sql = "SELECT 
                tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke AS gewerk, 
                tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG AS ghg, 
                tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG AS gug
            FROM 
                tabelle_projekt_element_gewerk
            WHERE 
                tabelle_projekte_idTABELLE_Projekte = ? 
                AND tabelle_elemente_idTABELLE_Elemente = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $projectID, $elementID);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: ['gewerk' => null, 'ghg' => null, 'gug' => null];
}

// Function to fetch Gewerk options
function fetchGewerkOptions($mysqli, $projectID)
{
    $sql = "SELECT 
                tabelle_auftraggeber_gewerke.Gewerke_Nr, 
                tabelle_auftraggeber_gewerke.Bezeichnung, 
                tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
            FROM 
                tabelle_projekte 
            INNER JOIN 
                tabelle_auftraggeber_gewerke ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes
            WHERE 
                tabelle_projekte.idTABELLE_Projekte = ?  
            ORDER BY 
                tabelle_auftraggeber_gewerke.Gewerke_Nr";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch GHG options
function fetchGHGOptions($mysqli, $gewerk)
{
    $sql = "SELECT 
                tabelle_auftraggeber_ghg.GHG, 
                tabelle_auftraggeber_ghg.Bezeichnung, 
                tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG
            FROM 
                tabelle_auftraggeber_ghg
            WHERE 
                tabelle_auftraggeber_ghg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $gewerk); // =tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch GUG options
function fetchGUGOptions($mysqli, $ghg)
{
    $sql = "SELECT 
                tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG, 
                tabelle_auftraggeberg_gug.GUG, 
                tabelle_auftraggeberg_gug.Bezeichnung
            FROM 
                tabelle_auftraggeberg_gug
            WHERE 
                tabelle_auftraggeberg_gug.tabelle_auftraggeber_GHG_idtabelle_auftraggeber_GHG = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $ghg);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch data
$gewerkeData = fetchGewerkeData($mysqli, $_SESSION['projectID'], $_SESSION['elementID']);
$gewerkOptions = fetchGewerkOptions($mysqli, $_SESSION['projectID']);
$ghgOptions = $gewerkeData['gewerk'] ? fetchGHGOptions($mysqli, $gewerkeData['gewerk']) : null;
$gugOptions = $gewerkeData['ghg'] ? fetchGUGOptions($mysqli, $gewerkeData['ghg']) : null;

// Start HTML output
?>

<div class='card'>
    <div class='card-header d-inline-flex customCardx'>
        <form class='d-flex align-items-center flex-wrap mr-2'>
            <div class='form-group d-flex align-items-center mr-2'>
                <label for='gewerk' class="mb-0">Gewerk:</label>
                <select class='form-control form-control-sm me-4 ms-1' id='gewerk'>
                    <option value='0'>Bitte auswählen</option>
                    <?php while ($row = $gewerkOptions->fetch_assoc()): ?>
                        <option value="<?= $row['idTABELLE_Auftraggeber_Gewerke'] ?>"
                            <?= ($gewerkeData['gewerk'] == $row['idTABELLE_Auftraggeber_Gewerke']) ? 'selected' : '' ?>>
                            <?= $row['Gewerke_Nr'] . ' - ' . $row['Bezeichnung'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class='form-group d-flex align-items-center mr-2'>
                <label class="mb-0" for='ghg'>GHG:</label>
                <select class='form-control form-control-sm me-4 ms-1' id='ghg'>
                    <?php if ($ghgOptions): ?>
                        <option value='0'>Bitte auswählen</option>
                        <?php while ($row = $ghgOptions->fetch_assoc()): ?>
                            <option value="<?= $row['idtabelle_auftraggeber_GHG'] ?>"
                                <?= ($gewerkeData['ghg'] == $row['idtabelle_auftraggeber_GHG']) ? 'selected' : '' ?>>
                                <?= $row['GHG'] . ' - ' . $row['Bezeichnung'] ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value='0'>Bitte Gewerk auswählen</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class='form-group d-flex align-items-center mr-2'>
                <label class="mb-0" for='gug'>GUG:</label>
                <select class='form-control form-control-sm me-4 ms-1' id='gug'>
                    <?php if ($gugOptions): ?>
                        <option value='0'>Bitte auswählen</option>
                        <?php while ($row = $gugOptions->fetch_assoc()): ?>
                            <option value="<?= $row['idtabelle_auftraggeberg_GUG'] ?>"
                                <?= ($gewerkeData['gug'] == $row['idtabelle_auftraggeberg_GUG']) ? 'selected' : '' ?>>
                                <?= $row['GUG'] . ' - ' . $row['Bezeichnung'] ?>
                            </option>
                        <?php endwhile; ?>
                    <?php elseif ($gewerkeData['gewerk']): ?>
                        <option value='0'>Bitte GHG auswählen</option>
                    <?php else: ?>
                        <option value='0'>Bitte Gewerk auswählen</option>
                    <?php endif; ?>
                </select>
            </div>

            <!--div class='form-group d-flex align-items-center mr-2'>
                <div>
                    <button type='button' id='saveElementGewerk' class='btn btn-outline-dark btn-sm me-1 '
                            value='saveElementGewerk'>
                        <i class='far fa-save'></i> Gewerk speichern
                    </button>

                    <button type='button' id='saveElementGewerk94'
                            class='btn btn-outline-dark btn-sm me-1'
                            value='saveElementGewerk2'>
                        <i class='far fa-save'></i> 94
                    </button>
                    <button type='button' id='saveElementGewerk93'
                            class='btn btn-outline-dark btn-sm me-1'
                            value='saveElementGewerk1'>
                        <i class='far fa-save'></i> 93
                    </button>
                    <button type='button' id='saveElementGewerk91' class='btn btn-outline-dark btn-sm'
                            value='saveElementGewerk6'>
                        <i class='far fa-save'></i> 91
                    </button>
                </div>
            </div--->

        </form>
    </div>
</div>


<?php
$mysqli->close();
?>


<!-- Modal Info -->
<div class='modal fade' id='infoModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-dialog-centered modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Info</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='infoBody'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>OK</button>
            </div>
        </div>

    </div>
</div>

<script src="utils/_utils.js"></script>
<script charset="utf-8">

    //GHG geändert
    $('#ghg').change(function () {
        var ghgid = $('#ghg').val();
        var gewerkid = $('#gewerk').val();
        if (gewerkid !== 0 && ghgid !== 0) {
            $.ajax({
                url: "getElementGewerkeFiltered.php",
                data: {"filterValueGHG": ghgid, "filterValueGewerke": gewerkid},
                type: "POST",
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
                type: "POST",
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
                type: "POST",
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
            type: "POST",
            success: function (data) {
                makeToaster(data.trim(), true);
            }
        });
    });

    $("button[value='saveElementGewerk1']").click(function () {
        $.ajax({
            url: "saveElementGewerk.php",
            data: {"gewerk": 1, "ghg": $('#ghg').val(), "gug": $('#gug').val()},
            type: "POST",
            success: function (data) {
                makeToaster(data.trim(), true);
            }
        });
    });

    $("button[value='saveElementGewerk6']").click(function () {
        $.ajax({
            url: "saveElementGewerk.php",
            data: {"gewerk": 6, "ghg": $('#ghg').val(), "gug": $('#gug').val()},
            type: "POST",
            success: function (data) {
                makeToaster(data.trim(), true);
            }
        });
    });


</script>
</body>
</html>