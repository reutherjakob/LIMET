<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
</head>

<body>
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }

?>

<canvas id="myChart" ></canvas>
                
</div>
</body>
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

</html>
