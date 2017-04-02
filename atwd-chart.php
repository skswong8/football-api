<?php
$get_region = $_GET['region'];

$xml=simplexml_load_file("http://www.cems.uwe.ac.uk/~s9-wong/atwd/crimes/6-2013/$get_region/xml");
$doc = simplexml_load_file("../crimes/output.xml");

$area_id = $xml->xpath('/response/crimes/region/area/@id');
$total = $xml->xpath('/response/crimes/region/area/@total');

#total for each area
$total_array = array();
foreach ($total as $key=>$value){
	$total_array[$key] = $value['total'];
}

$result = array();
foreach($total_array as $inner) {
    $result[] = current($inner);   
}

#areas
$area_array = array();
foreach ($area_id as $areakey=>$areavalue){
	$area_array[$areakey] = $areavalue['id'];
}

$area_result = array();
foreach($area_array as $areainner) {
	$area_result[] = current($areainner);	
}
?>
<!doctype html>
<html>
<head>
	<title>ATWD Assignment - Samuel Wong</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="Chart.js"></script>
	<link href="../style.css" rel="stylesheet" type="text/css">
	<link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="container">
		<header>
			<h2>ATWD Assignment<br>
			Samuel Wong - 10006880</h2>
		</header>
		<aside>
			<nav>
				<?php 
				#array of regions and output for navigation
				$region_links = array('north_east','north_west','yorkshire_and_humber','east_midlands','west_midlands','east_of_england','london','south_east','south_west','wales','british_transport_police','action_fraud');
				$region_url = $_GET['region'];
				$regions = $xml->region;
				
				$region_title = ucwords(str_replace('_', ' ', $region_url));
				
				echo "<ul>\n";
					foreach($region_links as $region) {
						$region_name = str_replace('_', ' ', ucwords($region));
						echo "<li><a href='" . $region . "' >" . $region_name . "</a></li>\n";
					}
				echo "</ul>\n";
				
				#calculator
				$region_total = 0;
				$results = $doc->xpath('/crimes/region[@id="'.$region_title.'"]/area/total_recorded_crime/including_fraud');
				foreach($results as $sum) {
					$region_total+= $sum;
				}
				?>
			</nav>

			<!--key-->
			<div class="key">
				<h4>Key:</h4>
				<?php
				$colour_array = array('#2980b9', '#c0392b', '#af8a53', '#95a5a6', '#2ecc71', '#34495e');
				for ($i=0; $i < count($area_result); $i++){
					?>
					<span style="background-color: <?php echo $colour_array[$i];?>;"><?php echo $area_result[$i];?></span>
					<?php
				}
				?>
			</div>
		</aside>

		<section class="charts">

		<h2><?php echo $region_title; ?></h2>

		<canvas id="myChart" width="550" height="550"></canvas>
		<canvas id="myDoughnut" width="550" height="550"></canvas>
		<div class="total">
		<h3>Total:</h3>
		<p><?php echo $region_total ?></p>
		</div>
		</section>
		<script>

		var data = {
			labels : ["Areas"],
			datasets : [
				
				<?php
				for ($i=0; $i < count($result); $i++){
				?>
				{
					fillColor : "<?php echo $colour_array[$i];?>",
					strokeColor : "white",
					data : [<?php echo $result[$i];?>, 0]
				},
				<?php
				}
				?>
			]
		}

		var datatwo = [

				<?php
				for ($i=0; $i < count($result); $i++){
				?>
				{
					value : <?php echo $result[$i];?>,
					color : "<?php echo $colour_array[$i];?>"
				},
				<?php
				}
				?>
		]

		var ctx = document.getElementById("myChart").getContext("2d");
		new Chart(ctx).Bar(data);

		var ctx = document.getElementById("myDoughnut").getContext("2d");
		new Chart(ctx).Doughnut(datatwo);
		</script>
	</div>
</body>
</html>