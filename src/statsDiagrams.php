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
        $school = 0;               
        $articles = array();
        
        $bookingIds = getBookingIdsOfDate($date, false);        
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
// 	    echo("<pre>"); print_r($booking); echo("</pre>");
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
				
// 				if ($articleId == 200) { // School
// 					//print_r($article);
// 					$school += $article['total'];
// 				}
            }
            $donations += $booking['donation'];
            $total += $booking['total'];
	    if ($booking['school']) {
		$school += $booking['total']; // Count school bookings total additionally
	    }
	    
        }   
        
//         $total += $donations;
        $data[$date]['donations'] = $donations;
        $data[$date]['total'] = $total;
        $data[$date]['food'] = $food;
        $data[$date]['school'] = $school;
		
// 	echo("donations, total, food, school: $donations, $total, $food, $school<br>\n");
// 	if ($school > 0) {
// 	    echo("SCHOOL: $school<br>");
// 	}
    }
    
    ksort($data);
	
	//print_r($data);
    return $data;
}


function showDiagram($name, $yAxisName, $data) {
    
//     print_r($data);
?>
	<script type="text/javascript">
		google.load('visualization', '1.1', { 'packages': ['bar'] });
		google.charts.setOnLoadCallback(drawChartTotalPerDay_<? echo($name); ?>);
		
		function drawChartTotalPerDay_<? echo($name); ?>() {
			var data = google.visualization.arrayToDataTable([
<?
				echo("['', ");
				
				$yearsCovered = date("Y") - 2018 + 1; //count($data[0]); // get the number of data columns
				for($i = $yearsCovered; $i > 0; $i--) {
					$year = date("Y") - $i + 1; 
					echo("'$year', ");
					echo("'$year (Schule)', ");
				}            
				echo("],\n");
				
				
				$maxValue = 0;
				
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
							if ($dataOfDay['year'][$year]['total'] > $maxValue) {
							    $maxValue = $dataOfDay['year'][$year]['total'];
							}
							
							echo(($dataOfDay['year'][$year]['total'] - $dataOfDay['year'][$year]['school']) . ", ");
							echo($dataOfDay['year'][$year]['school'] . ", ");
// 							echo("100" . ", "); // xxx
							
						}
						else {
							echo("0, ");
							echo("0, ");
						}

					}                              
					echo("],\n");
				}
?>
			]);

			var options = { 
				backgroundColor: 'transparent',
				
				isStacked: true,
				
				series: {
				    <? 
				    
				    for($series = 0; $series < $yearsCovered; $series++) {
					echo (($series * 2) . ": { targetAxisIndex: $series },\n");
					echo (($series * 2 + 1) . ": { targetAxisIndex: $series },\n");
				    }
				    ?>
				    
				},
				
				
				height: 600,
				width: 1800,
				
				chartArea: {
// 					top: 1000,
// 					bottom: 40,
// 					left: 100,
// 					right: 120,
					backgroundColor: 'transparent',
				}, 
				
// 				chartArea.top: 0,
				
				hAxis: {
					//slantedText:true, 
					//slantedTextAngle:90,
					textStyle: { fontSize: 20 },
					titleTextStyle: { italic: false },
// 					viewWindow: {
// 					    min: 0,
// 					    max: 300, // Needed to make all series scaled the same
// 					},
				},
				
				vAxis: {
					title: "<? echo($yAxisName); ?>",
// 					textPosition: 'none',
					textStyle: { fontSize: 20 },
					titleTextStyle: { fontSize: 26, italic: false, bold: true },
					viewWindow: {
					    min: 0,
					    max: <? echo($maxValue * 1.1); ?>, // Needed to make all series scaled the same
					},
				},
				
				bar: { groupWidth: '80%' },
// 				 dataOpacity: 0.7,
				
				
				// Hide all except first Y axis
				vAxes: {
				    <?
					for($series = 1; $series < $yearsCovered; $series++) {
					    echo("$series: { gridlines: { count:0}, ticks: [0], textStyle: {fontSize: 0 } }, ");
					}
				    ?>
				},
			};
			
			var formatter = new google.visualization.NumberFormat({decimalSymbol: '.',groupingSymbol: "'", prefix: 'CHF '});
			for (var i = 1; i <= <? echo($yearsCovered); ?>; i++) {
				formatter.format(data, i);
			}
			
// 			var chartTotalPerDay_<? echo($name); ?> = new google.visualization.ColumnChart(document.getElementById('dayTotal_<? echo($name); ?>'));

// 			chartTotalPerDay_<? echo($name); ?>.draw(data, options);
			
			
			
			// Instantiate and draw our chart, passing in some options.
			var chartTotalPerDay_<? echo($name); ?> = new google.charts.Bar(document.getElementById('dayTotal_<? echo($name); ?>'));
			chartTotalPerDay_<? echo($name); ?>.draw(data, google.charts.Bar.convertOptions(options));
		}
	</script>

	<div id="dayTotal_<? echo($name); ?>" style="width: 1600px; height: 600px; background-image: url(images/chart-bg.png); background-repeat: no-repeat; background-attachment: relative; background-position: -2px 8px;"></div> <p><br></p>
<!-- <div id="dayTotal_<? echo($name); ?>" style="width: 1600px; height: 600px; background-image: url(images/chart-bg.png);"></div> <p><br></p> -->
	<!--  The background image got generated with `various/chart-bg-generator.py` -->

<?
}



$statsPerDay = array();
for ($i = 0; $i <= (date("Y") - 2018 + 1); $i++) {
	$year = date("Y") - $i; // iterate through the last years (since 2018)
	$stats = getStatsPerDay($year);
// 	echo("<pre>"); print_r($stats); echo("</pre>");
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
	$totalPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalWaxPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalFoodPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
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
		
		/* Wax only in CHF */
		$totalPerDayAndYear[$offset]['year'][$year]['total'] = $data['total']; 
		$totalPerDayAndYear[$offset]['year'][$year]['school'] = $data['school']; 
		$totalPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
		
		/* Wax and food in CHF */
		$totalWaxPerDayAndYear[$offset]['year'][$year]['total'] = $data['total'] - $data['food']; // subtract food again as we only want to see the wax part
		$totalWaxPerDayAndYear[$offset]['year'][$year]['school'] = $data['school']; 
		$totalWaxPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalWaxPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
		
		/* Food only in CHF */
		$totalFoodPerDayAndYear[$offset]['year'][$year]['total'] = $data['food']; // We only want to see the food part
		$totalFoodPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalFoodPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
	}
} 

?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div id="body">
<a name="PerDayAndYear"></a><h1>Umsatz und Wachs pro Tag und Jahr</h1> 

<h3>Inhaltsverzeichnis</h3>
<ul>
    <li><a href=#Wax+Gastro_Currency>Wachs + Gastronomie</a><br><br></li>
    <li><a href=#Wax_Currency>Nur Wachs</a><br><br></li>
    <li><a href=#Gastro_Currency>Nur Gastronomie</a><br><br></li>
</ul>

<h3>Hinweise</h3>
<ul>
    <li>Für 2018 ist der Gastronomie-Anteil <span style="color:red;">nicht</span> enthalten!</li>
    <li>2020 konnte das Kerzenziehen wegen COVID-19 nicht öffentlich durchgeführt werden.</li>
</ul>


<p><a href="?nocss" target="_self">Ohne Hintergrundbild anzeigen</a><br>&nbsp;</p>

<hr>

<a name=Wax+Gastro_Currency></a><h2>Wachs + Gastronomie</h2>
<? showDiagram("Common", "Umsatz in CHF", $totalPerDayAndYear); ?>  
<hr>

<a name=Wax_Currency></a><h2>Wachs</h2>
<? showDiagram("Wax", "Umsatz in CHF", $totalWaxPerDayAndYear); ?> 
<hr>

<a name=Gastro_Currency></a><h2>Gastronomie</h2>
<? showDiagram("Food", "Umsatz in CHF", $totalFoodPerDayAndYear); ?> 

<?
include "$root/framework/footer.php"; 
?>
    
