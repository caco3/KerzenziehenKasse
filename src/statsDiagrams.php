<? 

$root=".";
include "$root/framework/header.php";

/* Returns the total grouped per day for each day in the given year listed in the DB */
function getStatsPerDay($year) {
    $data = array();
    $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
	
		// echo("$date<br>\n");
        $donations = 0;
        $total = 0;    
        $food = 0;                
        $articles = array();
        
        $bookingIds = getBookingIdsOfDate($date, false);        
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
			//echo("<pre>");
			//print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles 
				//echo("$articleId, " . ($articleId * 10));
				//print_r($articles);
				//print_r($articles[$articleId]);
				if (is_array($articles[$articleId]) and !array_key_exists('quantity', $articles[$articleId])) {
					$articles[$articleId]['quantity'] = 0;
				}
				$articles[$articleId]['quantity'] += $article['quantity'];
                $articles[$articleId]['price'] = $article['price']; // not summed up since it is per 1 pc.
				
				if ($articleId == 200) { // Food
					//print_r($article);
					$food += $article['quantity']; // equals the costs on food
				}
            }
            $donations += $booking['donation'];
            $total += $booking['total'];

        }   
        
//         $total += $donations;
        $data[$date]['donations'] = $donations;
        $data[$date]['total'] = $total;
        $data[$date]['food'] = $food;
		
		//echo("donations, total, food: $donations, $total, $food<br>\n");
    }
    
    ksort($data);
	
	//print_r($data);
    return $data;
}


function showDiagram($name, $data) {
?>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart', 'bar']});
		google.charts.setOnLoadCallback(drawChartTotalPerDay_<? echo($name); ?>);
		
		

		function drawChartTotalPerDay_<? echo($name); ?>() {
			var data = google.visualization.arrayToDataTable([
<?
				echo("['Tag', ");
				
				$yearsCovered = date("Y") - 2018 + 1; //count($data[0]); // get the number of data columns
				for($i = $yearsCovered; $i > 0; $i--) {
					$year = date("Y") - $i + 1; 
					echo("'$year', ");
				}            
				echo("],\n");
				
				
				foreach($data as $day => $dataOfDay) {
					//echo("$day\n");
					/* Ignore all empty days from the array */
					if(count($dataOfDay) == 0) { // ignore empty days
						continue;
					}


					// X-Axis
	//                 echo("[\"");  
	//                 foreach($dataOfDay as $year => $data2) {
	//                     echo($data2['formatedDate'] . ",\\n");
	//                 }
	//                 echo("\", ");  
					
	//                 echo("['" . $dataOfDay[$year - $yearsCovered + 1]['formatedDate'] . "', ");

					if ($dataOfDay['formatedDate'] == "") {
						continue;
					}
					echo("['" . $dataOfDay['formatedDate'] . "', ");
					
					
					// dataOfDay
					for($i = $yearsCovered; $i > 0; $i--) { // for each year
						$year = date("Y") - $i + 1; 					
						if (is_array($dataOfDay['year']) and array_key_exists($year, $dataOfDay['year'])) {
							echo($dataOfDay['year'][$year]['total'] . ", ");
						}
						else {
							echo("0, ");
						}

					}                              
					echo("],\n");
				}
?>
			]);

			var options = { 
				backgroundColor: 'transparent',
				chartArea: {
					top: 20,
					bottom: 40,
					left: 100,
					right: 120,
				}, 
				hAxis: {
					//slantedText:true, 
					//slantedTextAngle:90,
					textStyle: {
						fontSize: 18
					},
				},
				
				vAxis: {
					title: "Umsatz in CHF",
					textStyle: {
						fontSize: 18
					},
				}
			};
			
			var formatter = new google.visualization.NumberFormat({decimalSymbol: '.',groupingSymbol: "'", prefix: 'CHF '});
			for (var i = 1; i <= <? echo($yearsCovered); ?>; i++) {
				formatter.format(data, i);
			}
			
			var chartTotalPerDay_<? echo($name); ?> = new google.visualization.ColumnChart(document.getElementById('dayTotal_<? echo($name); ?>'));

			chartTotalPerDay_<? echo($name); ?>.draw(data, options);
		}
	</script>

	<div id="dayTotal_<? echo($name); ?>" style="width: 1600px; height: 600px; background-image: url(images/chart-bg.png)"></div> 
	<!--  The background image got generated with `various/chart-bg-generator.py` -->

<?
}



$statsPerDay = array();
for ($i = 0; $i <= (date("Y") - 2018 + 1); $i++) {
	$year = date("Y") - $i; // iterate through the last years (since 2018)
	$stats = getStatsPerDay($year);
	if (count($stats) == 0) { // no stats for this year => skip
		continue;
	}
	$statsPerDay[$year] = $stats;
}

$totalPerDayAndYear = array(); // [day][year]
$totalWaxPerDayAndYear = array(); // [day][year]
$totalFoodPerDayAndYear = array(); // [day][year]

/* Create one index per day for 30 days.
 * If a day stays empty, it will get ignored in the plot */
for ($i = 0; $i <= 30; $i++) { // for each day add a placeholder index
	$totalPerDayAndYear[$i] = array('donations' => 0, 'total' => 0);
	$totalWaxPerDayAndYear[$i] = array('donations' => 0, 'total' => 0);
	$totalFoodPerDayAndYear[$i] = array('donations' => 0, 'total' => 0);
}
	
for ($i = 0; $i <= 10; $i++) { // for each year
	$year = date("Y") - $i; 
	$dayIndex = 0;
	foreach($statsPerDay[$year] as $date => $data) { // for each day
		if ($dayIndex == 0) {
			$firstDay = $date; 
			$zeroOffset = date("z", strtotime($date));
		}
		$offset = date("z", strtotime($date)) - $zeroOffset;
		$dayIndex++;
		
		$totalPerDayAndYear[$offset]['year'][$year]['total'] = $data['total']; 
		$totalPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
		
		$totalWaxPerDayAndYear[$offset]['year'][$year]['total'] = $data['total'] - $data['food']; // subtract food again as we only want to see the wax part
		$totalWaxPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalWaxPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
		
		$totalFoodPerDayAndYear[$offset]['year'][$year]['total'] = $data['food']; // We only want to see the food part
		$totalFoodPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalFoodPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
	}
} 

?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div id="body">
<a name="PerDayAndYear"></a><h1>Umsatz pro Tag und Jahr</h1> 
<p>Hinweise: Für 2018 ist der Gastronomie-Anteil <span style="color:red;">nicht</span> enthalten! 2020 konnte das Kerzenziehen wegen COVID-19 nicht öffentlich durchgeführt werden.<br>&nbsp;</p>


<p><a href="?nocss", target="_self">Ohne Hintergrundbild anzeigen</a><br>&nbsp;</p>


<h2>Wachs + Gastronomie</h2> 
<? showDiagram("Common", $totalPerDayAndYear); ?>  

<h2>Wachs</h2>
<? showDiagram("Wax", $totalWaxPerDayAndYear); ?> 

<h2>Gastronomie</h2>
<? showDiagram("Food", $totalFoodPerDayAndYear); ?> 

<?
include "$root/framework/footer.php"; 
?>
    
