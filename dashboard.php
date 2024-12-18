<?php
include '_utils.php';
init_page_serversides();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>RB-Dashboard</title>
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

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>


    <!--
   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
   <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.js"></script>
    -->

    <style>
        #body, html, .container-fluid {
            height: 100%;
            font-size: 13px;
        }

        .card {
            overflow: hidden;
        }

        .card-body .rotate {
            z-index: 8;
            float: right;
            height: 100%;
        }

        .card-body .rotate i {
            color: rgba(20, 20, 20, 0.15);
            position: absolute;
            left: 0;
            left: auto;
            right: 10px;
            bottom: 0;
            display: block;
            -webkit-transform: rotate(-44deg);
            -moz-transform: rotate(-44deg);
            -o-transform: rotate(-44deg);
            -ms-transform: rotate(-44deg);
            transform: rotate(-44deg);
        }

    </style>
</head>

<body>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="card text-dark h-100">
                <div class="card-header bg-light"><h3>Anzahl meiner aktiven Projekte</h3></div>
                <div class="card-body text-dark">
                    <div class="rotate">
                        <i class="fa fa-list fa-4x"></i>
                    </div>
                    <h1 class="display-4" id="numberOfActiveProjects"></h1>
                </div>
            </div>
        </div>
    </div>
    <!--
    <div class="row mt-3">
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="card text-white bg-warning h-100">
                <div class="card-header"><h3>Offene Vermerke in meinen Projekten</h3></div>      
                <div class="card-body">                            
                    <div class="rotate">
                        <i class="fa fa-question fa-4x"></i>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    -->
    <div class="row mt-3">
        <div class="col-xl-6 col-sm-6 py-2">
            <div class="card text-dark h-100">
                <div class="card-header bg-light "><h3>Offene Protokollpunkte</h3></div>
                <div class="card-body">
                    <canvas id="chart-openVermerkeInActiveProjects"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-sm-6 py-2">
            <div class="card text-dark h-100">
                <div class="card-header bg-light "><h3>Offene Verfahren nach Status</h3></div>
                <div class="card-body">
                    <canvas id="chart-openTendersWithStatusInActiveProjects"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
<script>

    $(document).ready(function () {
        $.ajax({
            url: "getNumberOfActiveProjects.php",
            type: "GET",
            success: function (data) {
                $("#numberOfActiveProjects").html(data);
            }
        });


        $.ajax({
            url: "getChartDataActiveOpenVermerke.php",
            type: "GET",
            success: function (data) {
                console.log(data);
                var projects = [];
                var openVermerke = [];
                var openVermerkeOverdue = [];

                for (var i in data) {

                    projects[i] = data[i].Interne_Nr + " " + data[i].Projektname;
                    openVermerke[i] = data[i].VermerkeOffen;
                    openVermerkeOverdue[i] = data[i].VermerkeUeberf;
                }
                var chartConfig = {
                    type: 'bar',
                    data: {
                        datasets: [{
                            data: openVermerkeOverdue,
                            backgroundColor: 'rgba(217, 83, 79, 1)',
                            label: 'Überfällig'
                        },
                            {
                                data: openVermerke,
                                backgroundColor: 'rgba(240, 173, 78, 1)',
                                label: 'Offen'
                            }],
                        labels: projects
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: false,
                            text: 'Chart.js Bar Chart'
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                        scales: {
                            xAxes: [{
                                stacked: true,
                                ticks: {
                                    fontSize: 10,
                                    maxRotation: 90,
                                    minRotation: 80
                                }
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        }
                    }
                };
                var ctx = document.getElementById('chart-openVermerkeInActiveProjects').getContext('2d');
                var chart = new Chart(ctx, chartConfig);
            }
        });

        $.ajax({
            url: "getChartDataActiveOpenTendersWithStatus.php",
            type: "GET",
            success: function (data) {
                console.log(data);
                var counterTendersStatus0 = [];
                var counterTendersStatus2 = [];
                var projects = [];
                var currentProject = "";
                var projectCounter = 0;
                var statusBefore = 0;
                var counterStatus0 = 0;
                var counterStatus2 = 0;
                for (var i in data) {
                    //Neues Projekt in Daten
                    if (data[i].idTABELLE_Projekte !== currentProject) {
                        projects[projectCounter] = data[i].Projektname;
                        projectCounter++;

                        if (data[i].Status === "0" && statusBefore === 0) {
                            counterTendersStatus0[counterStatus0] = data[i].Counter;
                            counterStatus0++;
                            if (counterStatus2 > 0) {
                                counterTendersStatus2[counterStatus2] = 0;
                                counterStatus2++;
                            }
                        } else {
                            if (data[i].Status === "2" && statusBefore === 0) {
                                counterTendersStatus2[counterStatus2] = data[i].Counter;
                                counterStatus2++;
                                if (counterStatus0 > 0) {
                                    counterTendersStatus0[counterStatus0] = 0;
                                    counterStatus0++;
                                }
                            } else {
                                if (data[i].Status === "2" && statusBefore === 2) {
                                    counterTendersStatus0[counterStatus0] = 0;
                                    counterStatus0++;
                                    counterTendersStatus2[counterStatus2] = data[i].Counter;
                                    counterStatus2++;
                                } else {
                                    counterTendersStatus0[counterStatus0] = data[i].Counter;
                                    counterStatus0++;
                                }
                            }
                        }
                    } else {
                        if (data[i].Status === "0") {
                            counterTendersStatus0[counterStatus0] = data[i].Counter;
                            counterStatus0++;
                        } else {
                            counterTendersStatus2[counterStatus2] = data[i].Counter;
                            counterStatus2++;
                        }
                    }
                    statusBefore = parseInt(data[i].Status);
                    currentProject = data[i].idTABELLE_Projekte;
                }

                var chartConfig = {
                    type: 'bar',
                    data: {
                        datasets: [{
                            data: counterTendersStatus0,
                            backgroundColor: 'rgba(217, 83, 79, 1)',
                            label: 'Nicht abgeschlossen'
                        },
                            {
                                data: counterTendersStatus2,
                                backgroundColor: 'rgba(240, 173, 78, 1)',
                                label: 'Wartend'
                            }],
                        labels: projects
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: false,
                            text: 'Chart.js Bar Chart'
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                        scales: {
                            xAxes: [{
                                stacked: true,
                                ticks: {
                                    fontSize: 10,
                                    maxRotation: 90,
                                    minRotation: 80
                                }
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        }
                    }
                };
                var ctx = document.getElementById('chart-openTendersWithStatusInActiveProjects').getContext('2d');
                var chart = new Chart(ctx, chartConfig);
            }
        });
    });

</script>
</html> 
