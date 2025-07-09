<?php
session_start();
if (!isset($_SESSION["username"])) {
    echo "Bitte erst <a href=\"index.php\">einloggen</a>";
    exit;
}

?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Vergabediagramme</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">

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

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

    <!--DATEPICKER -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
</head>
<body>
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="col-xxl-12 col-xxl-12">
        <div class="mt-4 card">
            <div class="card-header">
                <div class='form-group'>
                    <label for='selectTenderChart'></label><select class='form-control form-control-sm'
                                                                   id='selectTenderChart'>
                        <option value=0 selected>Diagramm wählen</option>
                        <option value="chartTenderSums.php">Vergabesumme nach Lieferant</option>
                        <option value="chartTenderProxVsRealTenderSum.php">Vergleich Schätzkosten zu Vergabesumme
                            Absolut
                        </option>
                    </select>
                </div>
            </div>
            <div class="card-body" id="tenderChart">
            </div>
        </div>
    </div>
</div>
</body>
<script>
    $('#selectTenderChart').change(function () {
        if ($('#selectTenderChart').val() !== '0') {
            $.ajax({
                url: $('#selectTenderChart').val(),
                type: "GET",
                success: function (data) {
                    $("#tenderChart").html(data);
                }
            });
        }
    });
    $.get("navbar4.html", function (data) {
        $("#limet-navbar").html(data);
        $(".navbar-nav").find("li:nth-child(3)").addClass("active");
        currentP = <?php     echo json_encode($_SESSION["projectName"]); ?>;
        $("#projectSelected").text(currentP);
    });


</script>

</html>
