<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Geräteliste</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
</head>
<body style="height:100%">
<div id="limet-navbar" class=''></div>
<div class="container-fluid">
    <div class="mt-4 card responsive">
        <div class="card-header" id="CH1">Geräteliste</div>
        <div id="CB1" class="card-body table-responsive">
            <?php
            include '_utils.php';
            init_page_serversides();
            $mysqli = utils_connect_sql();
            $sql = "SELECT 
                    tabelle_geraete.GeraeteID, 
                    tabelle_hersteller.Hersteller, 
                    tabelle_geraete.Typ, 
                    tabelle_geraete.Kurzbeschreibung, 
                    tabelle_geraete.idTABELLE_Geraete,  
                    tabelle_elemente.Bezeichnung as Elementbezeichnung
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

            echo "<table class='table table-striped table-bordered table-sm' id='tableDevices'>
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
                    echo "<td><button type='button' id='" . $row["idTABELLE_Geraete"] . "' class='btn btn-outline-dark btn-sm' value='changeDevice' data-toggle='modal' data-target='#changeDeviceModal'><i class='fas fa-pencil-alt'></i></button></td>";
                    echo "</tr>";
                }
            }
            echo "</tbody></table>";
            ?>
        </div>
    </div>
</div>

<!-- Modal zum Anlegen eines Gerätes -->
<div class='modal fade' id='changeDeviceModal' role='dialog'>
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Gerät hinzufügen/bearbeiten</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
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
                                        data-toggle='modal' data-target='#addManufacturerModal'>
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
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Anlegen eines Herstellers -->
<div class='modal fade' id='addManufacturerModal' role='dialog'>
    <div class='modal-dialog modal-sm'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Hersteller hinzufügen</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyAddManufacturerModal'>
                <div class="form-group">
                    <label for="manufacturer">Hersteller:</label>
                    <input type="text" class="form-control form-control-sm" id="manufacturer" placeholder="Hersteller"/>
                </div>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addManufacturer' class='btn btn-success btn-sm' value='Hinzufügen'>
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Schließen</button>
            </div>
        </div>
    </div>
</div>

<script src="_utils.js"></script>
<script>
    let table;
    let deviceID;

    $(document).ready(function () {
        table = new DataTable('#tableDevices', {
            responsive: true,
            dom: '<"row"<"col-lg-12 col-md-6"f>> <"row"<"col-lg-12"tr>> <"row"<"col-md-2"i><"col-md-6"l><"col-md-4"p>>',
            paging: true,
            pageLength: 25,
            columnDefs: [{
                targets: [4],
                visible: false,
                searchable: false
            }],
            searching: true,
            ordering: true,
            info: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries"
            },
            initComplete: function () {
                $('#dt-search-0').appendTo('#CH1');
            }
        });
    });

    $('#tableDevices tbody').on('click', 'tr', function () {
        let rowData = table.row($(this)).data();
        deviceID = rowData[4];
        $('#hersteller').val(rowData[1]);
        console.log(" Row Data [1] ", rowData[1]);
        $('#type').val(rowData[2]);
        $('#kurzbeschreibung').val(rowData[3]);
    });


    $("button[value='changeDevice']").click(function () {
        $('#addDevice').hide();
        let rowData = table.row($(this).closest('tr')).data();

        $('#type').val(rowData[2]);
        $('#kurzbeschreibung').val(rowData[3]);
        setTimeout(function () {
            updateHerstellerDropdown(rowData[1]);
        }, 100)
    });

    //    $('#changeDeviceModal').on('shown.bs.modal', function () { });

    function updateHerstellerDropdown(selectedHersteller) {
        console.log(selectedHersteller);
        let optionValue = $('#hersteller option').filter(function () {
            return $(this).text() === selectedHersteller;
        }).val();
        console.log(optionValue);
        $('#hersteller').val(optionValue).trigger('change');
    }

    $("#addDevice").click(function () {
        let hersteller = $("#hersteller").val();
        let type = $("#type").val();
        let kurzbeschreibung = $("#kurzbeschreibung").val();

        if (hersteller !== "" && type !== "" && kurzbeschreibung !== "") {
            $('#changeDeviceModal').modal('hide');
            $.ajax({
                url: "addDevice.php",
                data: {"hersteller": hersteller, "type": type, "kurzbeschreibung": kurzbeschreibung},
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
                type: "GET",
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
