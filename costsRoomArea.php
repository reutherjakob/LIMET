<?php
// FX 25
require_once 'utils/_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Kosten-Raumbereich</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">

    <!-- Rework 2025 CDNs -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<body style="height:100%">

<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">

    <div class="mt-4 card">
        <div class="card-header">
            <form class="form-inline d-flex text-nowrap col-xxl-6 align-items-center">
                <label class="mr-sm-2 me-2" for="selectRoomArea">Raumbereich</label>
                <select class="form-control form-control-sm mr-sm-2 w-25 " id="selectRoomArea" name="selectRoomArea">
                    <option></option>
                    <?php
                    $mysqli = utils_connect_sql();

                    $projectID = isset($_SESSION["projectID"]) ? intval($_SESSION["projectID"]) : 0;
                    $sql = "SELECT DISTINCT tabelle_räume.`Raumbereich Nutzer`
                            FROM tabelle_auftraggeberg_gug 
                            RIGHT JOIN (
                                tabelle_auftraggeber_ghg 
                                RIGHT JOIN (
                                    tabelle_auftraggeber_gewerke 
                                    RIGHT JOIN (
                                        (tabelle_räume 
                                          INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                                        ) 
                                        INNER JOIN tabelle_projekt_element_gewerk 
                                        ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
                                        AND (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)
                                    ) 
                                    ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
                                ) 
                                ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
                            ) 
                            ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
                            WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte=?
                            ORDER BY tabelle_räume.`Raumbereich Nutzer`;";
                    $stmt = $mysqli->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("i", $projectID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $stmt->close();
                    }
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row["Raumbereich Nutzer"]) . "'>" . htmlspecialchars($row["Raumbereich Nutzer"]) . "</option>";
                    }
                    ?>
                </select>
                <label class="mr-sm-2 me-2 ms-2" for="selectBestand">inkl. Bestand:</label>
                <select class="form-control form-control-sm mr-sm-2 w-25" id="selectBestand" name="selectBestand">
                    <option value="1">Ja</option>
                    <option value="0">Nein</option>
                </select>
                <button type="button" id="calculateCostsRoomArea" class="btn btn-outline-dark btn-sm">
                    <i class="far fa-play-circle"></i> Berechnen
                </button>
            </form>
        </div>
        <div class="card-body" id="costsRoomArea">
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#selectRoomArea').select2({
            width: 'resolve',
            dropdownAutoWidth: true,
            placeholder: "Raumbereich auswählen"
        });

        $("button[id='calculateCostsRoomArea']").click(function () {
            let bestandInkl = $('#selectBestand').val();
            let x = document.getElementById("selectRoomArea").selectedIndex;
            let y = document.getElementById("selectRoomArea").options;
            let roomArea = y[x].text;

            $.ajax({
                url: "getRoomAreaCosts.php",
                data: {"roomArea": roomArea, "bestandInkl": bestandInkl},
                type: "POST",
                success: function (data) {
                    $('#costsRoomArea').html(data);
                }
            });
        });
    });
</script>
</body>
</html>
