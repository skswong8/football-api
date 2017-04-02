<?php
require_once 'vendor/autoload.php';
Unirest\Request::verifyPeer(false);

// These code snippets use an open-source library.
$standings = Unirest\Request::get("https://myanmarunicorn-bhawlone-v1.p.mashape.com/competitions/36/standings",
  array(
    "X-Mashape-Key" => "091ZETXuQVmsh6KXaNX13JTRfXEKp1YmrqCjsnxT4UwBk3L7Ud",
    "Accept" => "application/json"
  )
);

// These code snippets use an open-source library.
$topscorers = Unirest\Request::get("https://myanmarunicorn-bhawlone-v1.p.mashape.com/competitions/36/topscorers",
  array(
    "X-Mashape-Key" => "091ZETXuQVmsh6KXaNX13JTRfXEKp1YmrqCjsnxT4UwBk3L7Ud",
    "Accept" => "application/json"
  )
);
?>
<!doctype html>
<html>
	<head>
		<title>#</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src="node_modules/chart.js/dist/Chart.js"></script>
	</head>
	<body>
		<div class="container">
			<header>
				<h1>Football API</h1>
			</header>

			<table>
				<thead>
					<tr>
						<th class="table-position">Position</th>
						<th class="table-team">Team</th>
						<th class="table-p">P</th>
						<th class="table-w">W</th>
						<th class="table-d">D</th>
						<th class="table-l">L</th>
						<th class="table-f">F</th>
						<th class="table-a">A</th>
						<th class="table-gd">GD</th>
						<th class="table-pts">Pts</th>
					</tr>
				</thead>
				<tbody>
				<?php

				foreach ($standings->body->data->standings as $arr) {
					$position = $arr->position;
					$name = $arr->team->name;
					$played = $arr->played;
					$wins = $arr->wins;
					$draws = $arr->draws;
					$losses = $arr->losses;
					$goalsFor = $arr->goalsFor;
					$goalsAgainst = $arr->goalsAgainst;
					$points = $arr->points;
					// output html
					$html = '<tr>';
						$html .= '<td class="row-position">'.$position.'</td>';
						$html .= '<td class="row-name">'.$name.'</td>';
						$html .= '<td class="row-p">'.$played.'</td>';
						$html .= '<td class="row-w">'.$wins.'</td>';
						$html .= '<td class="row-d">'.$draws.'</td>';
						$html .= '<td class="row-l">'.$losses.'</td>';
						$html .= '<td class="row-f">'.$goalsFor.'</td>';
						$html .= '<td class="row-a">'.$goalsAgainst.'</td>';
						$html .= '<td class="row-gd">'.($goalsFor - $goalsAgainst).'</td>';
						$html .= '<td class="row-pts">'.$points.'</td>';
					$html .= '</tr>';
					echo $html;
				}
				?>
				</tbody>
			</table>

			<section id="charts">
				<div class="chart">
					<canvas id="myChart" width="400" height="400"></canvas>
				</div>
				<div class="chart">
					<canvas id="myChart2" width="400" height="400"></canvas>
				</div>
			</section>

			<script>

				// get array of top scorers
				var scorersArray = <?php echo json_encode($topscorers->body->data); ?>;

				// return the names of the scorers
				var playerNames = $.map( scorersArray, function(value) {
				   return value.player.name;
				});

				// get home goals from the scorersArray
				var homeGoals = $.map( scorersArray, function(value) {
				   return value.homeGoals;
				});
				
				// get away goals from the scorersArray
				var awayGoals = $.map( scorersArray, function(value) {
				   return value.awayGoals;
				});

				var totalGoals = $.map( scorersArray, function(value) {
				   return value.awayGoals + value.homeGoals;
				});

				var firstHomeGoals = scorersArray[0].homeGoals;
				var firstAwayGoals = scorersArray[0].awayGoals;
				var firstString = '[' +firstHomeGoals+ ', ' +firstAwayGoals+ ']';
				firstJson = JSON.parse(firstString); //an array [1,2]

				var ctx2 = document.getElementById("myChart2");
				var data2 = {
				    labels: [
				        "Home",
				        "Away"
				    ],
				    datasets: [
				        {
				            data: firstJson,
				            backgroundColor: [
				                "#FF6384",
				                "#36A2EB"
				            ],
				            hoverBackgroundColor: [
				                "#FF6384",
				                "#36A2EB"
				            ]
				        }]
				};
				// And for a doughnut chart
				var myDoughnutChart = new Chart(ctx2, {
				    type: 'doughnut',
				    data: data2,
				    options: {
				    	cutoutPercentage:70,
				    }
				});

			var data = {
			    labels: playerNames,
			    datasets: [
			        {
			            label: "Total goals",
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
			            borderWidth: 1,
			            data: totalGoals,
			        }
			    ]
			};

			var ctx = document.getElementById("myChart");
			var myBarChart = new Chart(ctx, {
			    type: 'bar',
			    data: data,
			    options: {
			        scales: {
			            yAxes: [{
			                ticks: {
			                    beginAtZero:true
			                }
			            }]
			        }
			    }
			});

			// on click get player index (name)
			document.getElementById("myChart").onclick = function(evt){
	            var activePoints = myBarChart.getElementsAtEvent(evt); // return ChartElement array
	            var firstPoint = activePoints[0];
	            var playerName = myBarChart.data.labels[firstPoint._index]; // returns player name as string
	            var value = myBarChart.data.datasets[firstPoint._datasetIndex].data[firstPoint._index]; // returns value (total goals) as int

	            // find player data in scorersArray from playerName
	            var playerData = $.grep(scorersArray, function(value){
	            	return value.player.name == playerName; // returns player data object
	            });

	            var goalTypes = $.map(playerData, function(player) {
				    return player.homeGoals + ", " + player.awayGoals; // returns array of homeGoals and awayGoals
				});

	            var goalTypesString = '[' +goalTypes+ ']'; // save homeGoals and awayGoals as string to feed into pie data
	            goalTypesJson = JSON.parse(goalTypesString); //an array [1,2]


	            var playerGoals = {
				    labels: [
				        "Home",
				        "Away"
				    ],
				    datasets: [
				        {
				            data: goalTypesJson,
				            backgroundColor: [
				                "#FF6384",
				                "#36A2EB"
				            ],
				            hoverBackgroundColor: [
				                "#FF6384",
				                "#36A2EB"
				            ]
				        }]
				};

	            myDoughnutChart.config.data = playerGoals;
	            myDoughnutChart.update();

	        };

			
			</script>
			

		</div>
	</body>
</html>