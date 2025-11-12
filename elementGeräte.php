<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Geräteliste</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>

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


</head>
<body style="height:100%">
<div id="limet-navbar" class=''></div>
<div class="container-fluid">
    <div class="mt-4 card responsive">
        <div class="card-header">
            <div class="row">
                <div class="col-xxl-6">Geräteliste
                </div>
                <div class="col-xxl-6 d-flex justify-content-end" id="CH1">
                </div>
            </div>
        </div>
        <div id="CB1" class="card-body table-responsive">
            <?php
            if (!function_exists('utils_connect_sql')) {
                include "utils/_utils.php";
            }
            init_page_serversides("x");
            $mysqli = utils_connect_sql();
            $sql = "SELECT   
                tabelle_elemente.Bezeichnung as Elementbezeichnung,
                    tabelle_geraete.GeraeteID, 
                    tabelle_hersteller.Hersteller, 
                    tabelle_hersteller.idtabelle_hersteller,
                    tabelle_geraete.Typ, 
                    tabelle_geraete.Kurzbeschreibung, 
                    tabelle_geraete.idTABELLE_Geraete
                FROM 
                    tabelle_geraete
                INNER JOIN 
                    tabelle_hersteller 
                    ON tabelle_geraete.tabelle_hersteller_idtabelle_hersteller = tabelle_hersteller.idtabelle_hersteller
                INNER JOIN 
                    tabelle_elemente 
                    ON tabelle_geraete.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
                ORDER BY 
                    tabelle_geraete.GeraeteID DESC";

            $result = $mysqli->query($sql);

            echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableDevices'>
                      <thead><tr>";
            $firstRow = $result->fetch_assoc();
            if ($firstRow) {
                foreach ($firstRow as $column => $value) {
                    echo "<th>" . ($column) . "</th>";
                }
                echo "</tr></thead><tbody>";
                echo "<tr>";
                foreach ($firstRow as $value) {
                    echo "<td>" . $value . "</td>";
                }
                echo "</tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . ($value) . "</td>";
                    }
                    echo "<td><button type='button' id='" . $row["idTABELLE_Geraete"] . "' class='btn btn-outline-dark btn-sm' value='changeDevice' data-bs-toggle='modal' data-bs-target='#changeDeviceModal'><i class='fas fa-pencil-alt'></i></button></td>";
                    echo "</tr>";
                }
            }
            echo "</tbody></table>";
            ?>
        </div>
    </div>
</div>

<!-- Modal zum Anlegen eines Gerätes -->
<div class='modal fade' id='changeDeviceModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Gerät hinzufügen/bearbeiten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class='form-group'>
                        <label for='hersteller'>Hersteller:</label>
                        <div class="input-group">
                            <select class='form-control form-control-sm' id='hersteller' name='hersteller'
                                    data-current-hersteller=''>
                                <?php
                                $sql = "SELECT `tabelle_hersteller`.`idtabelle_hersteller`, `tabelle_hersteller`.`Hersteller`
                                            FROM `LIMET_RB`.`tabelle_hersteller`
                                            ORDER BY `tabelle_hersteller`.`Hersteller`;";
                                $result = $mysqli->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row["idtabelle_hersteller"] . "'>" . $row["Hersteller"] . "</option>";
                                }
                                ?>
                            </select>
                            <div class="input-group-append">
                                <button type='button' id='openAddManufacturer' class='btn btn-outline-secondary'
                                        data-bs-toggle='modal' data-bs-target='#addManufacturerModal'>
                                    <i class='far fa-plus-square'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <input type="text" class="form-control form-control-sm" id="type" placeholder="Type"/>
                    </div>
                    <div class="form-group">
                        <label for="kurzbeschreibung">Kurzbeschreibung:</label>
                        <textarea class="form-control form-control-sm" rows="5" id="kurzbeschreibung"></textarea>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addDevice' class='btn btn-success btn-sm' value='Hinzufügen'>
                <input type='button' id='saveDevice' class='btn btn-warning btn-sm' value='Speichern'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Anlegen eines Herstellers -->
<div class='modal fade' id='addManufacturerModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-sm'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Hersteller hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyAddManufacturerModal'>
                <div class="form-group">
                    <label for="manufacturer">Hersteller:</label>
                    <input type="text" class="form-control form-control-sm" id="manufacturer" placeholder="Hersteller"/>
                </div>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addManufacturer' class='btn btn-success btn-sm' value='Hinzufügen'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    let table;
    var deviceID;

    $(document).ready(function () {
        table = new DataTable('#tableDevices', {
            responsive: true,
            dom: '<"row"<"col-xxl-12 col-xxl-6"f>> <"row"<"col-xxl-12"tr>> <"row"<"col-xxl-2"i><"col-xxl-6"l><"col-xxl-4"p>>',
            paging: true,
            pageLength: 25,
            columnDefs: [{
                targets: [3, 6],
                visible: false,
                searchable: false
            }],
            searching: true,
            ordering: true,
            info: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Suche...",
                lengthMenu: "Show _MENU_ entries"
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CH1');
            }
        });
    });

    $("button[value='changeDevice']").click(function () {
        $('#addDevice').hide();
        let rowData = table.row($(this).closest('tr')).data();
        let manufacturerID = rowData[3];
        console.log("Raw:V ", rowData);
        $('#hersteller').val(manufacturerID).trigger('change');
        $('#type').val(rowData[4]);
        $('#kurzbeschreibung').val(rowData[5]);
    });


    $("#addDevice").click(function () {
        let hersteller = $("#hersteller").val();
        let type = $("#type").val();
        let kurzbeschreibung = $("#kurzbeschreibung").val();
        if (hersteller !== "" && type !== "" && kurzbeschreibung !== "") {
            $('#changeDeviceModal').modal('hide');
            $.ajax({
                url: "addDevice.php",
                data: {"hersteller": hersteller, "type": type, "kurzbeschreibung": kurzbeschreibung},
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDevicesToElement.php",
                        type: "GET",
                        success: function (data) {
                            $("#devicesInDB").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });


    $("#saveDevice").click(function () {
        let hersteller = $("#hersteller").val();
        let type = $("#type").val();
        let kurzbeschreibung = $("#kurzbeschreibung").val();

        if (hersteller !== "" && type !== "" && kurzbeschreibung !== "") {
            $('#changeDeviceModal').modal('hide');
            $.ajax({
                url: "saveDevice.php",
                data: {
                    "deviceID": deviceID,
                    "hersteller": hersteller,
                    "type": type,
                    "kurzbeschreibung": kurzbeschreibung
                },
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDevicesToElement.php",
                        type: "GET",
                        success: function (data) {
                            $("#devicesInDB").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });


    $("#addManufacturer").click(function () {
        let manufacturer = $("#manufacturer").val();
        if (manufacturer !== "") {
            $('#addManufacturerModal').modal('hide');
            $('#changeDeviceModal').modal('hide');
            $.ajax({
                url: "addManufacturer.php",
                data: {"manufacturer": manufacturer},
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDevicesToElement.php",
                        data: {"elementID": ""},
                        type: "GET",
                        success: function (data) {
                            $("#devicesInDB").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte geben Sie einen Hersteller ein!");
        }
    });
</script>
</body>
</html>
