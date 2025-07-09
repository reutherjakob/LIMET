<?php
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
check_login();
// TODO get this to work
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title></title>
</head>

<body>
<canvas id="myChart" ></canvas>

<script>
    
    $(document).ready(function(){
	$.ajax({
		url: "getChartTenderProxVsRealTenderSums.php",
		method: "GET",
		success: function(data) {
			console.log(data);
			var tenderLot = [];
			var delta = [];
                        

			for(var i in data) {
				tenderLot.push(data[i].LosNr_Extern);
				delta.push(data[i].delta);
			}

			var chartdata = {
				labels: tenderLot,
				datasets : [
					{
						label: "Schätzkosten-Vergabekosten",
						backgroundColor: 'rgba(75, 192, 192, 0.2)',
						borderColor: 'rgba(75, 192, 192, 1)',
						hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
						hoverBorderColor: 'rgba(200, 200, 200, 1)',
						data: delta,
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
                                                beginAtZero:true
                                            }
                                        }]
                                    }
                                }
			});
		},
		error: function(data) {
			console.log(data);
		}
	});
    });

</script>
</body>
</html>
