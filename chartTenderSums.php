<?php
require_once 'utils/_utils.php';
check_login();

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
            method: 'POST',
            success: function (data) {
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


</script>
</body>
</html>
