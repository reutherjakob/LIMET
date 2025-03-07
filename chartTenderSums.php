<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();
// TODO get this to work
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
</head>

<body>
<canvas id="myChart"></canvas>

<script>
    $(document).ready(function () {
        $.ajax({
            url: "getChartTenderSums.php",
            method: "GET",
            success: function (data) {
                console.log(data);
                var tender = [];
                var sum = [];

                for (var i in data) {
                    tender.push(data[i].Lieferant);
                    sum.push(data[i].SummevonVergabesumme);
                }

                var chartdata = {
                    labels: tender,
                    datasets: [
                        {
                            label: "Vergabesumme",
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                            hoverBorderColor: 'rgba(200, 200, 200, 1)',
                            data: sum,
                            borderWidth: 2
                        }
                    ]
                };

                var ctx = $("#myChart");

                var barGraph = new Chart(ctx, {
                    type: 'bar',
                    data: chartdata,
                    options: {
                        responsive: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            },
            error: function (data) {
                console.log(data);
            }
        });
    });
    /*        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });**/

</script>
</body>
</html>
