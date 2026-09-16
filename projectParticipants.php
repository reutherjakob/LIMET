<?php
// 25 FX
require_once 'utils/_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Beteiligte</title>
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

</head>
<body style="height:100%">
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="mt-2 card">
        <div class="card-header">
            <div class="row">
                <div class="col-8">Beteiligte Personen</div>
                <div class="col-4" id="BeteiligtePersonenCardHeader"></div>
            </div>
        </div>
        <div class="card-body" id='personsInProject'></div>
    </div>
    <div class='mt-2 row'>
        <div class='col-xxl-8'>
            <div class="mt-2 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">Nicht beteiligte Personen</div>
                        <div class="col-4" id="NichtBeteiligtePersonenCardHeader"></div>
                    </div>
                </div>

                <div class="card-body" id='personsNotInProject'></div>
            </div>
        </div>
        <div class='col-xxl-4'>
            <div class="mt-2 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">Person zu Projekt hinzufügen</div>
                        <div class="col-4 d-flex justify-content-end">
                            <button class="btn" title="Reset" id="resetAddPerson"><i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body" id='addPersonToProject'></div>
            </div>
        </div>
    </div>
</div>
</body>


<?php require_once "modal_firmenkontakte_visitenkarte.php"; ?>

<script src="utils/_utils.js"></script>
<script charset="utf-8">


    $(document).ready(function () {
        $.ajax({
            url: "getPersonsOfProject.php",
            type: "POST",
            success: function (data) {
                $("#personsInProject").html(data);
                $.ajax({
                    url: "getPersonsNotInProject.php",
                    type: "POST",
                    success: function (data) {
                        $("#personsNotInProject").html(data);
                        $.ajax({
                            url: "getPersonToProjectField.php",
                            type: "POST",
                            success: function (data) {
                                $("#addPersonToProject").html(data);
                            }
                        });
                    }
                });
            }
        });

        $(document).on('click', "button[value='addressCard']", function () {
            const b = $(this);
            $("#cardName").html(b.data('name'));
            $("#cardLieferant").html(b.data('lieferant'));
            $("#cardTel").html(b.data('tel'));
            $("#cardMail").html(b.data('mail'));
            $("#cardAddress").html(b.data('adresse'));
            $("#cardPlace").html(b.data('plz') + ", " + b.data('ort'));
        });

        $("#resetAddPerson").click(function () {
            $.ajax({
                url: "getPersonToProjectField.php",
                type: "POST",
                success: function (data) {
                    $("#addPersonToProject").html(data);
                }
            });
        });
    });

</script>
</html>
