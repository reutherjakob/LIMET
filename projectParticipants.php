<?php
include '_utils.php';
init_page_serversides();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>RB-Beteiligte</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>


    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

</head>
<body style="height:100%">
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">

    <div class="mt-4 card">
        <div class="card-header">Beteiligte Personen</div>
        <div class="card-body" id='personsInProject'></div>
    </div>
    <div class='mt-4 row'>
        <div class='col-sm-8'>
            <div class="mt-4 card">
                <div class="card-header">Nicht beteiligte Personen</div>
                <div class="card-body" id='personsNotInProject'></div>
            </div>
        </div>
        <div class='col-sm-4'>
            <div class="mt-4 card">
                <div class="card-header">Person zu Projekt hinzufügen</div>
                <div class="card-body" id='addPersonToProject'></div>
            </div>
        </div>
    </div>
</div>
</body>
<script>

    // Tabelle formatieren
    $(document).ready(function () {
        // Wenn Seite geladen, dann Inhalte dazu laden
        $.ajax({
            url: "getPersonsOfProject.php",
            type: "GET",
            success: function (data) {
                $("#personsInProject").html(data);
                $.ajax({
                    url: "getPersonsNotInProject.php",
                    type: "GET",
                    success: function (data) {
                        $("#personsNotInProject").html(data);
                        $.ajax({
                            url: "getPersonToProjectField.php",
                            type: "GET",
                            success: function (data) {
                                $("#addPersonToProject").html(data);
                                $.ajax({
                                    url: "getProjectNotices.php",
                                    type: "GET",
                                    success: function (data) {
                                        $("#projectNotices").html(data);
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });


    });


</script>

</html>
