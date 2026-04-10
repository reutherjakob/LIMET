
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


    <style>
        .select2-container {
            z-index: 1060 !important;
        }

        .select2-dropdown {
            z-index: 1061 !important;
        }

        .modal .select2-container {
            z-index: 1070 !important;
        }

        .modal .select2-dropdown {
            z-index: 1071 !important;
        }
    </style>
</head>

<?php
require_once "utils/_utils.php";
init_page_serversides("x");
?>


<body style="height:100%">
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            Lieferanten-Kontakt e
            <div class="d-flex align-items-center" id="cardHeader1">
                <input type='button' id='addContactModalButton' class='btn btn-success btn-sm me-1'
                       value='Lieferantenkontakt hinzufügen' data-bs-toggle='modal'
                       data-bs-target='#addContactModal'>
            </div>
        </div>
        <div class="card-body">
            <table id="tableLieferantenKontakte" class="table table-striped table-bordered table-sm" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th></th>
                    <th>Name</th>
                    <th>Vorname</th>
                    <th>Tel</th>
                    <th>Mail</th>
                    <th>Adresse</th>
                    <th>PLZ</th>
                    <th>Ort</th>
                    <th>Land</th>
                    <th>Lieferant</th>
                    <th>Abteilung</th>
                    <th>Gebiet</th>
                    <th></th>
                    <th>Lieferant ID</th>
                    <th>Abt. ID</th>
                </tr>
                </thead>
            </table>
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
                            Lieferanten Unternehmen hinzufügen <i class='far fa-plus-square'></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableLieferantenUnternehmen" class="table table-striped table-bordered nowrap table-sm">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th></th>
                            <th>Lieferant</th>
                            <th>Tel</th>
                            <th>Adresse</th>
                            <th>PLZ</th>
                            <th>Ort</th>
                            <th>Land</th>
                            <th></th>
                        </tr>
                        </thead>
                    </table>
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
    </div>
</div>



<?php
require_once "modal_LieferantenKontaktHinzufuegen.php";
require_once "modal_firmenkontakte_change_lieferant.php";
require_once "modal_firmenkontakte_visitenkarte.php";
?>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script charset="utf-8">
    var ansprechID;
    var tableLieferantenKontakte, tableLieferantenUnternehmen;

    $(document).ready(function () {


        tableLieferantenKontakte = $('#tableLieferantenKontakte').DataTable({
            ajax: {
                url: 'getLieferantenPersonen.php',
                type: 'GET',
                dataSrc:''
            },
            columns: [
                {data: 'idTABELLE_Ansprechpersonen'},
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<button type='button' id='${row.idTABELLE_Ansprechpersonen}'
                            class='btn btn-outline-dark btn-sm'
                            value='addressCard' data-bs-toggle='modal'
                            data-bs-target='#showAddressCard'>
                            <i class='far fa-address-card'></i></button>`;
                    }
                },
                {data: 'Name'},
                {data: 'Vorname'},
                {data: 'Tel'},
                {data: 'Mail'},
                {data: 'Adresse'},
                {data: 'PLZ'},
                {data: 'Ort'},
                {data: 'Land'},
                {data: 'Lieferant'},
                {data: 'Abteilung'},
                {data: 'Gebietsbereich'},
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<button type='button' id='${row.idTABELLE_Ansprechpersonen}'
                            class='btn btn-outline-dark btn-sm'
                            value='changeContact' data-bs-toggle='modal'
                            data-bs-target='#addContactModal'>
                            <i class='fa fa-pencil-alt'></i></button>`;
                    }
                },
                {data: 'idTABELLE_Lieferant'},
                {data: 'idtabelle_abteilung'}
            ],
            columnDefs: [
                {targets: [0, 14, 15], visible: false, searchable: false}
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
            buttons: ['copy', 'excel'],
            initComplete: function () {
                $('#dt-search-0').appendTo("#cardHeader1");
                $('.dt-buttons .btn').addClass("btn-sm me-1");
                tableLieferantenKontakte.buttons().container()
                    .removeClass("flex-wrap")
                    .prependTo("#cardHeader1");
            }
        });


        tableLieferantenUnternehmen = $('#tableLieferantenUnternehmen').DataTable({
            ajax: {
                url: 'getLieferantenUnternehmen.php',
                type: 'GET',
                dataSrc:''

            },
            columns: [
                { data: 'idTABELLE_Lieferant' },  // ID (hidden)
                {
                    className: 'control',
                    orderable: false,
                    render: function() {
                        return '';
                    }
                },
                { data: 'Lieferant' },
                { data: 'Tel' },
                { data: 'Anschrift' },
                { data: 'PLZ' },
                { data: 'Ort' },
                { data: 'Land' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `<button type='button' id='${row.idTABELLE_Lieferant}'
                            class='btn btn-outline-dark btn-sm'
                            value='changeLieferant'
                            data-bs-toggle='modal'
                            data-bs-target='#changeLieferantModal'>
                            <i class='fa fa-pencil-alt'></i></button>`;
                    }
                }
            ],
            columnDefs: [
                { targets: [0], visible: false, searchable: false }
            ],
            select: true,
            paging: true,
            searching: true,
            info: true,
            order: [[2, 'asc']],  // Sort by Lieferant name
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


        $('#tableLieferantenKontakte tbody').on('click', 'tr', function () {
            const rowData = tableLieferantenKontakte.row($(this)).data();
            ansprechID = rowData.idTABELLE_Ansprechpersonen;
            $("#lieferantenName").val(rowData.Name);
            $("#lieferantenVorname").val(rowData.Vorname);
            $("#lieferantenTel").val(rowData.Tel);
            $("#lieferantenEmail").val(rowData.Mail);
            $("#lieferantenAdresse").val(rowData.Adresse);
            $("#lieferantenPLZ").val(rowData.PLZ);
            $("#lieferantenOrt").val(rowData.Ort);
            $("#lieferantenLand").val(rowData.Land);
            $("#lieferant").val(rowData.idTABELLE_Lieferant);
            $("#abteilung").val(rowData.idtabelle_abteilung);
            $("#lieferantenGebiet").val(rowData.Gebietsbereich);

            $("#cardName").html(rowData.Name + " " + rowData.Vorname);
            $("#cardLieferant").html(rowData.Lieferant);
            $("#cardTel").html(rowData.Tel);
            $("#cardMail").html(rowData.Mail);
            $("#cardAddress").html(rowData.Adresse);
            $("#cardPlace").html(rowData.PLZ + ", " + rowData.Ort);
        });


        $('#tableLieferantenUnternehmen tbody').on('click', 'tr', function () {
            $.ajax({
                url: "getLieferantenUmsaetze.php",
                data: {"lieferantenID": tableLieferantenUnternehmen.row($(this)).data().idTABELLE_Lieferant},
                type: "POST",
                success: function (data) {
                    $("#lieferantenumsaetze").html(data);
                }
            });
        });


        $("#addLieferantenKontakt").click(function () {
            let form = document.querySelector('#addContactForm');
            if (!form.checkValidity()) {
                form.reportValidity();
            } else {
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
                    success: function () {
                        tableLieferantenKontakte.ajax.reload();
                        $('#addContactModal').modal('hide');

                    }
                });
            }
        });

        $("#saveLieferantenKontakt").click(function () {
            let form = document.querySelector('#addContactForm');
            if (!form.checkValidity()) {
                form.reportValidity();
            } else {
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
                    success: function () {
                        tableLieferantenKontakte.ajax.reload();
                        $('#addContactModal').modal('hide');
                    }
                });
            }
        });


        $("#addLieferant").click(function () {
            let form = document.querySelector('#changeLieferantForm');
            if (!form.checkValidity()) {
                form.reportValidity();
            } else {
                let firma = $("#firma").val();
                let lieferantTel = $("#lieferantTel").val();
                let lieferantAdresse = $("#lieferantAdresse").val();
                let lieferantPLZ = $("#lieferantPLZ").val();
                let lieferantOrt = $("#lieferantOrt").val();
                let lieferantLand = $("#lieferantLand").val();
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
                    success: function () {
                        tableLieferantenUnternehmen.ajax.reload();
                        $('#changeLieferantModal').modal('hide');
                    }
                });
            }
        });

        $("#saveLieferant").click(function () {
            let form = document.querySelector('#changeLieferantForm');
            if (!form.checkValidity()) {
                form.reportValidity();
            } else {
                let lieferantID = $("#lieferantID").val();
                let firma = $("#firma").val();
                let lieferantTel = $("#lieferantTel").val();
                let lieferantAdresse = $("#lieferantAdresse").val();
                let lieferantPLZ = $("#lieferantPLZ").val();
                let lieferantOrt = $("#lieferantOrt").val();
                let lieferantLand = $("#lieferantLand").val();

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
                    success: function () {
                        tableLieferantenUnternehmen.ajax.reload();
                        $('#changeLieferantModal').modal('hide');
                    }
                });
            }
        });


        $("#addContactModalButton").click(function () {
            document.getElementById("lieferantenName").value = "";
            document.getElementById("lieferantenVorname").value = "";
            document.getElementById("lieferantenTel").value = "";
            document.getElementById("lieferantenAdresse").value = "";
            document.getElementById("lieferantenPLZ").value = "";
            document.getElementById("lieferantenOrt").value = "";
            document.getElementById("lieferantenLand").value = "";
            document.getElementById("lieferantenEmail").value = "";
            document.getElementById("lieferantenGebiet").value = "";
            document.getElementById("saveLieferantenKontakt").style.display = "none";
            document.getElementById("addLieferantenKontakt").style.display = "inline";
            var selectedRow = tableLieferantenUnternehmen.row({selected: true});
            if (selectedRow.node()) {
                var rowData = selectedRow.data();
                $("#lieferant").val(rowData.idTABELLE_Lieferant);
                document.getElementById("lieferantenAdresse").value = rowData.Anschrift;
                document.getElementById("lieferantenPLZ").value = rowData.PLZ;
                document.getElementById("lieferantenOrt").value = rowData.Ort;
                document.getElementById("lieferantenLand").value = rowData.Land;
                document.getElementById("lieferantenTel").value = rowData.Tel;
            }
        });

        $(document).on('click', "button[value='changeContact']", function () {
            document.getElementById("addLieferantenKontakt").style.display = "none";
            document.getElementById("saveLieferantenKontakt").style.display = "inline";
        });


        $(document).on('click', "button[value='changeLieferant']", function () {
            document.getElementById("addLieferant").style.display = "none";
            document.getElementById("saveLieferant").style.display = "inline";
            let $tr = $(this).closest('tr');
            let rowData = tableLieferantenUnternehmen.row($tr).data();
            $("#lieferantID").val(rowData.idTABELLE_Lieferant);
            $("#firma").val(rowData.Lieferant);
            $("#lieferantTel").val(rowData.Tel);
            $("#lieferantAdresse").val(rowData.Anschrift);
            $("#lieferantPLZ").val(rowData.PLZ);
            $("#lieferantOrt").val(rowData.Ort);
            $("#lieferantLand").val(rowData.Land);
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


        $('#addContactModal').on('shown.bs.modal', function () {
            $('.select2').select2({
                dropdownCssClass: 'select2-dropdown-long',
                width: '100%',
                dropdownParent: $('#addContactModal'),
                placeholder: $(this).data('placeholder') || 'Auswählen...',
                allowClear: true
            });
        });

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
                    $('#abteilung').append(
                        $('<option></option>')
                            .attr('value', response.id)
                            .text(newAbteilungName)
                    );
                    $('#abteilung').trigger('change.select2');
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
        $('#addAbteilungModal').on('hidden.bs.modal', function () {
            $('#newAbteilungName').val('');
        });

    });

</script>
</body>
</html>
