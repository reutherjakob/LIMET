<?php
require_once "utils/_utils.php";
init_page_serversides("x");
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Firmenkontakte</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
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
    <style> /* Make sure Select2 dropdown appears above Bootstrap modal */
        .select2-container {
            z-index: 1060 !important; /* slightly higher than Bootstrap modal backdrop */
        }

        /* Also target the actual dropdown for Select2 (the dropdown elements) */
        .select2-dropdown {
            z-index: 1061 !important;
        }

        /* Optional: When used inside modal, the dropdown might need higher z-index */
        .modal .select2-container {
            z-index: 1070 !important;
        }

        .modal .select2-dropdown {
            z-index: 1071 !important;
        }
    </style>
</head>
<body style="height:100%">
<!-- Rework 2025 -->
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            Lieferantenkontakte
            <div class="d-flex align-items-center" id="cardHeader1">
                <input type='button' id='addContactModalButton' class='btn btn-success btn-sm me-1'
                       value='Lieferantenkontakt hinzufügen' data-bs-toggle='modal'
                       data-bs-target='#addContactModal'>
            </div>
        </div>
        <div class="card-body">
            <?php
            // 25 FX
            $mysqli = utils_connect_sql();
            $sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, 
                       tabelle_ansprechpersonen.Vorname, tabelle_ansprechpersonen.Tel, 
                       tabelle_ansprechpersonen.Adresse, tabelle_ansprechpersonen.PLZ,
                       tabelle_ansprechpersonen.Ort, tabelle_ansprechpersonen.Land, 
                       tabelle_ansprechpersonen.Mail,  tabelle_abteilung.Abteilung,
                       tabelle_lieferant.Lieferant, tabelle_lieferant.idTABELLE_Lieferant,
                       tabelle_abteilung.idtabelle_abteilung, tabelle_ansprechpersonen.Gebietsbereich
            FROM tabelle_abteilung INNER JOIN (tabelle_lieferant INNER JOIN tabelle_ansprechpersonen 
                ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_ansprechpersonen.tabelle_lieferant_idTABELLE_Lieferant)
                ON tabelle_abteilung.idtabelle_abteilung = tabelle_ansprechpersonen.tabelle_abteilung_idtabelle_abteilung;";
            $result = $mysqli->query($sql);

            echo "<table class='table table-striped table-bordered  table-sm' id='tableLieferantenKontakte'>
                        <thead><tr>
                        <th>ID</th>
                        <th></th>
                        <th>Name</th>
                        <th>Vorname</th>
                        <th>Tel</th>
                        <th>Mail</th>
                        <th>Adresses</th>
                        <th>PLZ</th>
                        <th>Ort</th>
                        <th>Land</th>
                        <th>Lieferant</th>
                        <th>Abteilung</th>
                        <th>Gebiet</th>
                        <th></th>
                        <th></th>
                        <th></th>                                    
                        </tr></thead><tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["idTABELLE_Ansprechpersonen"] . "</td>";
                echo "<td><button type='button' id='" . $row["idTABELLE_Ansprechpersonen"] . "' class='btn btn-outline-dark btn-sm' value='addressCard' data-bs-toggle='modal' data-bs-target='#showAddressCard'><i class='far fa-address-card'></i></button></td>";
                echo "<td>" . $row["Name"] . "</td>";
                echo "<td>" . $row["Vorname"] . "</td>";
                echo "<td>" . $row["Tel"] . "</td>";
                echo "<td>" . $row["Mail"] . "</td>";
                echo "<td>" . $row["Adresse"] . "</td>";
                echo "<td>" . $row["PLZ"] . "</td>";
                echo "<td>" . $row["Ort"] . "</td>";
                echo "<td>" . $row["Land"] . "</td>";
                echo "<td>" . $row["Lieferant"] . "</td>";
                echo "<td>" . $row["Abteilung"] . "</td>";
                echo "<td>" . $row["Gebietsbereich"] . "</td>";
                echo "<td><button type='button' id='" . $row["idTABELLE_Ansprechpersonen"] . "' class='btn btn-outline-dark btn-sm' value='changeContact' data-bs-toggle='modal' data-bs-target='#addContactModal'><i class='fa fa-pencil-alt'></i></button></td>";
                echo "<td>" . $row["idTABELLE_Lieferant"] . "</td>";
                echo "<td>" . $row["idtabelle_abteilung"] . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            ?>
        </div>
    </div>
    <div class='mt-1 row'>
        <div class='col-xxl-8'>
            <div class="mt-4 card">
                <div class="card-header d-flex justify-content-between">
                    Lieferanten
                    <div class="d-flex align-items-center" id="cardHeader2">
                        <button type='button' id='addLieferantButton' class='btn btn-success btn-sm me-1  text-nowrap'
                                value='addLieferant' data-bs-toggle='modal' data-bs-target='#changeLieferantModal'>
                            Lieferant hinzufügen <i class='far fa-plus-square'></i></button>
                    </div>
                </div>

                <div class="card-body">
                    <?php
                    $mysqli = utils_connect_sql();
                    $sql = "SELECT  tabelle_lieferant.idTABELLE_Lieferant,
                                    tabelle_lieferant.Lieferant,
                                    tabelle_lieferant.Tel,
                                    tabelle_lieferant.Anschrift,
                                    tabelle_lieferant.PLZ,
                                    tabelle_lieferant.Ort,
                                    tabelle_lieferant.Land
                                FROM tabelle_lieferant;";
                    $result = $mysqli->query($sql);

                    echo "<table class='table table-striped table-bordered nowrap table-sm' id='tableLieferantenUnternehmen'>
                        <thead><tr>
                        <th>ID</th>
                          <th> </th>
                        <th>Lieferant</th>
                        <th>Tel</th>
                        <th>Adresse</th>
                        <th>PLZ</th>
                        <th>Ort</th>
                        <th>Land</th>                                  
                        <th></th>       
                        </tr></thead><tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["idTABELLE_Lieferant"] . "</td>";
                        echo "<td></td>";
                        echo "<td>" . $row["Lieferant"] . "</td>";
                        echo "<td>" . $row["Tel"] . "</td>";
                        echo "<td>" . $row["Anschrift"] . "</td>";
                        echo "<td>" . $row["PLZ"] . "</td>";
                        echo "<td>" . $row["Ort"] . "</td>";
                        echo "<td>" . $row["Land"] . "</td>";
                        echo "<td><button type='button' id='" . $row["idTABELLE_Lieferant"] . "' class='btn btn-outline-dark btn-sm' value='changeLieferant' data-bs-toggle='modal' data-bs-target='#changeLieferantModal'><i class='fa fa-pencil-alt'></i></button></td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    ?>
                </div>
            </div>
        </div>
        <div class='col-xxl-4'>
            <div class="mt-4 card">
                <div class="card-header">Lieferantenumsaetze</div>
                <div class="card-body" id="lieferantenumsaetze">
                </div>
            </div>
        </div>

        <!-- Modal zum Anzeigen der Visitenkarte -->
        <div class='modal fade' id='showAddressCard' role='dialog' tabindex="-1">
            <div class='modal-dialog modal-sm'>

                <!-- Modal content-->
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title'>Kontaktdaten</h4>
                        <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
                    </div>
                    <div class='modal-body' id='mbody'>
                        <address class="m-t-md">
                            <strong><label class='control-label' id="cardName"></label></strong><br>
                            <label class='control-label' id="cardLieferant"></label><br>
                            <label class='control-label' id="cardAddress"></label><br>
                            <label class='control-label' id="cardPlace"></label><br>
                            <abbr title="Phone">T: </abbr><label class='control-label' id="cardTel"></label><br>
                            <abbr title="Mail">M: </abbr><label class='control-label' id="cardMail"></label><br>
                        </address>
                    </div>
                    <div class='modal-footer'>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "modal_LieferantenKontaktHinzufuegen.php";
?>

<!-- Modal zum Anzeigen der Visitenkarte -->
<div class='modal fade' id='showAddressCard' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-sm'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Kontaktdaten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <address class="m-t-md">
                    <strong><label class='control-label' id="cardName"></label></strong><br>
                    <label class='control-label' id="cardLieferant"></label><br>
                    <label class='control-label' id="cardAddress"></label><br>
                    <label class='control-label' id="cardPlace"></label><br>
                    <abbr title="Phone">T: </abbr><label class='control-label' id="cardTel"></label><br>
                    <abbr title="Mail">M: </abbr><label class='control-label' id="cardMail"></label><br>
                </address>
            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id='changeLieferantModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Lieferant</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <input type='hidden' id='lieferantID'>
                <form role="form">
                    <div class='form-group'>
                        <label for='firma'>Lieferant</label>
                        <input type='text' class='form-control form-control-sm' id='firma'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantTel'>Tel</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantTel'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantAdresse'>Adresse</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantAdresse'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantPLZ'>PLZ</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantPLZ'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantOrt'>Ort</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantOrt'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantLand'>Land</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantLand'>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <div class='modal-footer'>
                    <input type='button' id='addLieferant' class='btn btn-success btn-sm me-1'
                           value='Hinzufügen'>
                    <input type='button' id='saveLieferant' class='btn btn-warning btn-sm me-1'
                           value='Speichern'>
                    <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

</body>

<script charset="utf-8">
    var ansprechID;
    var tableLieferantenKontakte, tableLieferantenUnternehmen;

    $(document).ready(function () {
        tableLieferantenKontakte = $('#tableLieferantenKontakte').DataTable({
            columnDefs: [
                {
                    targets: [0, 14, 15],
                    visible: false,
                    searchable: false
                }
            ],
            select: true,
            paging: true,
            searching: true,
            info: true,
            order: [[2, 'asc']],
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 10,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                searchPlaceholder: '',
                search: ""
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['search', 'buttons'],
                bottomEnd: ['info', 'paging']
            },
            buttons: [
                'copy', 'excel'
            ],
            initComplete: function () {
                $('#dt-search-0').appendTo("#cardHeader1");
                $('.dt-buttons .btn').addClass("btn-sm me-1")
                tableLieferantenKontakte.buttons().container().removeClass("flex-wrap").prependTo("#cardHeader1");
            }
        });

// Second DataTable

        $('#tableLieferantenKontakte tbody').on('click', 'tr', function () {
            ansprechID = tableLieferantenKontakte.row($(this)).data()[0];
            document.getElementById("lieferantenName").value = tableLieferantenKontakte.row($(this)).data()[2];
            document.getElementById("lieferantenVorname").value = tableLieferantenKontakte.row($(this)).data()[3];
            document.getElementById("lieferantenTel").value = tableLieferantenKontakte.row($(this)).data()[4];
            document.getElementById("lieferantenAdresse").value = tableLieferantenKontakte.row($(this)).data()[6];
            document.getElementById("lieferantenPLZ").value = tableLieferantenKontakte.row($(this)).data()[7];
            document.getElementById("lieferantenOrt").value = tableLieferantenKontakte.row($(this)).data()[8];
            document.getElementById("lieferantenLand").value = tableLieferantenKontakte.row($(this)).data()[9];
            document.getElementById("lieferantenEmail").value = tableLieferantenKontakte.row($(this)).data()[5];
            document.getElementById("lieferant").value = tableLieferantenKontakte.row($(this)).data()[14];
            document.getElementById("abteilung").value = tableLieferantenKontakte.row($(this)).data()[15];
            document.getElementById("lieferantenGebiet").value = tableLieferantenKontakte.row($(this)).data()[12];

            //  Setzen der Visitenkarteninformation
            document.getElementById("cardName").innerHTML = tableLieferantenKontakte.row($(this)).data()[2] + " " + tableLieferantenKontakte.row($(this)).data()[3];
            document.getElementById("cardLieferant").innerHTML = tableLieferantenKontakte.row($(this)).data()[10];
            document.getElementById("cardTel").innerHTML = tableLieferantenKontakte.row($(this)).data()[4];
            document.getElementById("cardMail").innerHTML = tableLieferantenKontakte.row($(this)).data()[5];
            document.getElementById("cardAddress").innerHTML = tableLieferantenKontakte.row($(this)).data()[6];
            document.getElementById("cardPlace").innerHTML = tableLieferantenKontakte.row($(this)).data()[7] + ", " + tableLieferantenKontakte.row($(this)).data()[8];
        });

        $('#tableLieferantenUnternehmen tbody').on('click', 'tr', function () {
            $.ajax({
                url: "getLieferantenUmsaetze.php",
                data: {"lieferantenID": tableLieferantenUnternehmen.row($(this)).data()[0]},
                type: "POST",
                success: function (data) {
                    $("#lieferantenumsaetze").html(data);
                }
            });
        });

        $("#addLieferantenKontakt").click(function () {
            let Name = $("#lieferantenName").val();
            let Vorname = $("#lieferantenVorname").val();
            let Tel = $("#lieferantenTel").val();
            let Adresse = $("#lieferantenAdresse").val();
            let PLZ = $("#lieferantenPLZ").val();
            let Ort = $("#lieferantenOrt").val();
            let Land = $("#lieferantenLand").val();
            let Email = $("#lieferantenEmail").val();
            let lieferant = $("#lieferant").val();
            let abteilung = $("#abteilung").val();
            let gebiet = $("#lieferantenGebiet").val();
            if (Name.length > 0 && Vorname.length > 0 && Tel.length > 0) {
                $('#addContactModal').modal('hide');
                $.ajax({
                    url: "addLieferant.php",
                    data: {
                        "Name": Name,
                        "Vorname": Vorname,
                        "Tel": Tel,
                        "Adresse": Adresse,
                        "PLZ": PLZ,
                        "Ort": Ort,
                        "Land": Land,
                        "Email": Email,
                        "lieferant": lieferant,
                        "abteilung": abteilung,
                        "gebiet": gebiet
                    },
                    type: "POST",
                    success: function (data) {
                        alert(data);
                        $.ajax({
                            url: "getLieferantenPersonen.php",
                            data: {
                                "ansprechID": ansprechID,
                                "Name": Name,
                                "Vorname": Vorname,
                                "Tel": Tel,
                                "Adresse": Adresse,
                                "PLZ": PLZ,
                                "Ort": Ort,
                                "Land": Land,
                                "Email": Email,
                                "lieferant": lieferant,
                                "abteilung": abteilung,
                                "gebiet": gebiet
                            },
                            type: "POST",
                            success: function (data) {
                                $("#lieferanten").html(data);

                            }
                        });

                    }
                });
            } else {
                alert("Bitte überprüfen Sie Ihre Angaben! Name, Vorname und Tel ist Pflicht!");
            }
        });


        $("#saveLieferantenKontakt").click(function () {
            let Name = $("#lieferantenName").val();
            let Vorname = $("#lieferantenVorname").val();
            let Tel = $("#lieferantenTel").val();
            let Adresse = $("#lieferantenAdresse").val();
            let PLZ = $("#lieferantenPLZ").val();
            let Ort = $("#lieferantenOrt").val();
            let Land = $("#lieferantenLand").val();
            let Email = $("#lieferantenEmail").val();
            let lieferant = $("#lieferant").val();
            let abteilung = $("#abteilung").val();
            let gebiet = $("#lieferantenGebiet").val();
            if (Name.length > 0 && Vorname.length > 0 && Tel.length > 0) {
                $('#addContactModal').modal('hide');
                $.ajax({
                    url: "saveLieferantenKontakt.php",
                    data: {
                        "ansprechID": ansprechID,
                        "Name": Name,
                        "Vorname": Vorname,
                        "Tel": Tel,
                        "Adresse": Adresse,
                        "PLZ": PLZ,
                        "Ort": Ort,
                        "Land": Land,
                        "Email": Email,
                        "lieferant": lieferant,
                        "abteilung": abteilung,
                        "gebiet": gebiet
                    },
                    type: "POST",
                    success: function (data) {
                        alert(data);
                        $.ajax({
                            url: "getLieferantenPersonen.php",
                            type: "POST",
                            success: function (data) {
                                $("#lieferanten").html(data);

                            }
                        });
                    }
                });
            } else {
                alert("Bitte überprüfen Sie Ihre Angaben! Name, Vorname und Tel ist Pflicht!");
            }
        });

        $("#addContactModalButton").click(function () {
            // Clear all fields first
            document.getElementById("lieferantenName").value = "";
            document.getElementById("lieferantenVorname").value = "";
            document.getElementById("lieferantenTel").value = "";
            document.getElementById("lieferantenAdresse").value = "";
            document.getElementById("lieferantenPLZ").value = "";
            document.getElementById("lieferantenOrt").value = "";
            document.getElementById("lieferantenLand").value = "";
            document.getElementById("lieferantenEmail").value = "";
            document.getElementById("lieferantenGebiet").value = "";

            // Hide save button, show add button
            document.getElementById("saveLieferantenKontakt").style.display = "none";
            document.getElementById("addLieferantenKontakt").style.display = "inline";

            // Get selected row of LieferantenUnternehmen table
            var selectedRow = tableLieferantenUnternehmen.row({selected: true});
            if (selectedRow.node()) {
                var rowData = selectedRow.data();
                // Assuming rowData mapping:
                // [0] = ID (hidden)
                // [2] = Lieferant name
                // [3] = Tel
                // [4] = Adresse
                // [5] = PLZ
                // [6] = Ort
                // [7] = Land

                // Prefill related input fields in the modal
                // Here, Lieferant ID should be set in the Lieferant dropdown as well if needed
                $("#lieferant").val(rowData[0]); // Set Lieferant ID in dropdown
                document.getElementById("lieferantenAdresse").value = rowData[4];
                document.getElementById("lieferantenPLZ").value = rowData[5];
                document.getElementById("lieferantenOrt").value = rowData[6];
                document.getElementById("lieferantenLand").value = rowData[7];
                document.getElementById("lieferantenTel").value = rowData[3];
            }
        });

        $("button[value='changeContact']").click(function () {
            document.getElementById("addLieferantenKontakt").style.display = "none";
            document.getElementById("saveLieferantenKontakt").style.display = "inline";
        });

        $("#addLieferant").click(function () {
            let firma = $("#firma").val();
            console.log(firma);
            let lieferantTel = $("#lieferantTel").val();
            let lieferantAdresse = $("#lieferantAdresse").val();
            let lieferantPLZ = $("#lieferantPLZ").val();
            let lieferantOrt = $("#lieferantOrt").val();
            let lieferantLand = $("#lieferantLand").val();
            if (firma !== "" && lieferantTel !== "" && lieferantAdresse !== "" && lieferantPLZ !== "" && lieferantOrt !== "" && lieferantLand !== "") {
                $('#changeLieferantModal').modal('hide');
                $.ajax({
                    url: "addFirma.php",
                    data: {
                        "firma": firma,
                        "lieferantTel": lieferantTel,
                        "lieferantAdresse": lieferantAdresse,
                        "lieferantPLZ": lieferantPLZ,
                        "lieferantOrt": lieferantOrt,
                        "lieferantLand": lieferantLand
                    },
                    type: "POST",
                    success: function (data) {
                        alert(data);
                        location.reload();
                    }
                });
            } else {
                alert("Bitte alle Felder ausfüllen!");
            }
        });

        tableLieferantenUnternehmen = $('#tableLieferantenUnternehmen').DataTable({
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                    className: 'control',
                    orderable: false,
                    targets: 1
                }
            ],
            select: true,
            paging: true,
            searching: true,
            info: true,
            order: [[2, 'asc']],
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 10,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                searchPlaceholder: 'Suche',
                search: ""
            },
            responsive: {
                details: {
                    type: 'column',
                    target: 1
                }
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['search'],
                bottomEnd: ['info', 'paging']
            },
            initComplete: function () {
                $('#dt-search-1').appendTo("#cardHeader2");
            }
        });

        $("button[value='changeLieferant']").click(function () {
            document.getElementById("addLieferant").style.display = "none";
            document.getElementById("saveLieferant").style.display = "inline";
            let $tr = $(this).closest('tr');
            let rowData = tableLieferantenUnternehmen.row($tr).data();
            $("#lieferantID").val(rowData[0]);
            $("#firma").val(rowData[2]);
            $("#lieferantTel").val(rowData[3]);
            $("#lieferantAdresse").val(rowData[4]);
            $("#lieferantPLZ").val(rowData[5]);
            $("#lieferantOrt").val(rowData[6]);
            $("#lieferantLand").val(rowData[7]);
        });

        $("#addLieferantButton").click(function () {
            $("#lieferantID").val("");
            $("#firma").val("");
            $("#lieferantTel").val("");
            $("#lieferantAdresse").val("");
            $("#lieferantPLZ").val("");
            $("#lieferantOrt").val("");
            $("#lieferantLand").val("");
            document.getElementById("saveLieferant").style.display = "none";
            document.getElementById("addLieferant").style.display = "inline";
        });

        $("#saveLieferant").click(function () {
            let lieferantID = $("#lieferantID").val();
            let firma = $("#firma").val();
            // console.log("FIRMA: ", firma);
            let lieferantTel = $("#lieferantTel").val();
            let lieferantAdresse = $("#lieferantAdresse").val();
            let lieferantPLZ = $("#lieferantPLZ").val();
            let lieferantOrt = $("#lieferantOrt").val();
            let lieferantLand = $("#lieferantLand").val();
            if (firma && lieferantTel && lieferantAdresse && lieferantPLZ && lieferantOrt && lieferantLand) {
                $('#changeLieferantModal').modal('hide');
                $.ajax({
                    url: "addFirma.php",
                    data: {
                        "lieferantID": lieferantID,
                        "firma": firma,
                        "lieferantTel": lieferantTel,
                        "lieferantAdresse": lieferantAdresse,
                        "lieferantPLZ": lieferantPLZ,
                        "lieferantOrt": lieferantOrt,
                        "lieferantLand": lieferantLand
                    },
                    type: "POST",
                    success: function (data) {
                        alert(data);
                        location.reload(); // or refresh only the table if you prefer
                    }
                });
            } else {
                alert("Bitte alle Felder ausfüllen!");
            }
        });

        // Select2 initialisieren nach Modal-Show
        $('#addContactModal').on('shown.bs.modal', function () {
            $('.select2').select2({
                dropdownCssClass: 'select2-dropdown-long',
                width: '100%',
                dropdownParent: $('#addContactModal'),
                placeholder: $(this).data('placeholder') || 'Auswählen...',
                allowClear: true
            });
        });

        // Neue Abteilung speichern
        $('#saveNewAbteilung').click(function () {
            var newAbteilungName = $('#newAbteilungName').val().trim();
            if (newAbteilungName === '') {
                alert('Bitte geben Sie einen Abteilungsnamen ein.');
                return;
            }
            if (!confirm("Haben sie genau geprüft, ob es diese Abteilung schon gibt?")) {
                return;
            }
            $.post('save_abteilung.php', {
                "abteilung": newAbteilungName
            }, function (response) {
                if (response.success) {
                    // Neue Option zum Select hinzufügen
                    $('#abteilung').append(
                        $('<option></option>')
                            .attr('value', response.id)
                            .text(newAbteilungName)
                    );

                    // Select2 aktualisieren
                    $('#abteilung').trigger('change.select2');

                    // Modal schließen und Felder zurücksetzen
                    $('#addAbteilungModal').modal('hide');
                    $('#newAbteilungName').val('');

                    alert('Abteilung erfolgreich hinzugefügt!');
                } else {
                    alert('Fehler beim Speichern: ' + (response.error || 'Unbekannter Fehler'));
                }
            }, 'json').fail(function () {
                alert('Verbindungsfehler. Bitte versuchen Sie es erneut.');
            });
        });

        // Modal zurücksetzen beim Schließen
        $('#addAbteilungModal').on('hidden.bs.modal', function () {
            $('#newAbteilungName').val('');
        });


    });
</script>
</html>
