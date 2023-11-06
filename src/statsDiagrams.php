<? 

$root=".";
include "$root/framework/header.php";

/* Returns the total grouped per day for each day in the given year listed in the DB */
function getStatsPerDay($year) {
    $data = array();
    $bookingDates = getBookingDatesOfYear($year);
    foreach($bookingDates as $date) {  // a day 
		//echo("<pre>");	
		//echo("$date<br>\n");
        $donations = 0;
        $total = 0;    
        $food = 0;      
        $school = 0;    
		$beeWax = 0;
		$parafinWax = 0;		
        $articles = array();
        
        $bookingIds = getBookingIdsOfDate($date, false);        
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
 	    	//echo("<pre>"); print_r($booking); echo("</pre>");
            foreach ($booking['articles'] as $articleId => $article) { // articles
				//echo("$articleId:\n");
			//	print_r($articles);
			//	print_r($articles[$articleId]);
				if (is_array($articles[$articleId]) and !array_key_exists('quantity', $articles[$articleId])) {
					$articles[$articleId]['quantity'] = 0;
				}
				$articles[$articleId]['quantity'] += $article['quantity'];
                $articles[$articleId]['price'] = $article['price']; // not summed up since it is per 1 pc.
				
				if ($articleId == 200) { // Food
					//print_r($article);
					$food += $article['quantity']; // equals the costs on food
				}
				elseif($articleId == 1) { // Parafin
					$parafinWax += $article["quantity"];
				}
				elseif($articleId == 2) { // Bee Wax
					$beeWax += $article["quantity"];
				}
				else { // Guss
					if ($article["waxType"] == "parafin") {
						$parafinWax += $article["waxAmount"] * $article["quantity"];					
					}
					else { // bee wax
						$beeWax += $article["waxAmount"] * $article["quantity"];					
					}
				}

				//echo("Parafin: $parafinWax, Bee: $beeWax\n");
				
// 				if ($articleId == 200) { // School
// 					//print_r($article);
// 					$school += $article['total'];
// 				}

				 
				//echo("--------------------\n");
            }
            $donations += $booking['donation'];
            $total += $booking['total'];
			if ($booking['school']) {
				$school += $booking['total']; // Count school bookings total additionally
			}
	    
			//echo("#############################\n");
        }   
        
//         $total += $donations;
        $data[$date]['donations'] = $donations;
        $data[$date]['total'] = $total;
        $data[$date]['food'] = $food;
        $data[$date]['school'] = $school;
        $data[$date]['parafinWax'] = $parafinWax;
        $data[$date]['beeWax'] = $beeWax;
		
// 		echo("donations, total, food, school: $donations, $total, $food, $school<br>\n");
// 		if ($school > 0) {
// 	    	echo("SCHOOL: $school<br>");
// 		}
    }
    
    ksort($data);
	
	//print_r($data);
    return $data;
}


function showDiagram($name, $yAxisName, $data, $nameLowerPart, $nameUpperPart, $widthAdjustment, $paddingLeft, $prefix, $suffix, $fractionDigits) {
    
//     print_r($data);
?>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart', 'bar']});
		google.charts.setOnLoadCallback(drawChartTotalPerDay_<? echo($name); ?>);
				
		function drawChartTotalPerDay_<? echo($name); ?>() {
			var data = google.visualization.arrayToDataTable([
<?
				echo("['', ");
				
				$yearsCovered = date("Y") - 2018 + 1; //count($data[0]); // get the number of data columns
				$years = array();
				for($i = $yearsCovered; $i > 0; $i--) {
					$year = date("Y") - $i + 1; 
					
					if ($year == 2020) { // Do not show 2020
						continue;
					}					
					array_push($years, $year);
				}				
				
				foreach($years as $year) {
					echo("'$year$nameLowerPart', ");
					echo("'$year$nameUpperPart', ");
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
					
					foreach($years as $year) {
						if (is_array($dataOfDay['year']) and array_key_exists($year, $dataOfDay['year'])) {
							if (($dataOfDay['year'][$year]['lowerPart'] + $dataOfDay['year'][$year]['upperPart']) > $maxValue) {
							    $maxValue = $dataOfDay['year'][$year]['lowerPart'] + $dataOfDay['year'][$year]['upperPart'];
							}
							
							echo(number_format($dataOfDay['year'][$year]['lowerPart'], 1, ".", "") . ", ");
							echo(number_format($dataOfDay['year'][$year]['upperPart'], 1, ".", "") . ", ");
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
				//colors:['#27A4AE', '#612C85', '#DA557B', '#F48F5F', '#EFC956', '#92C44A'],
				// https://www.heavy.ai/blog/12-color-palettes-for-telling-better-stories-with-your-data
				colors:["#b30000", "#7c1158", "#4421af", "#1a53ff", "#0d88e6", "#00b7c7", "#5ad45a", "#8be04e", "#ebdc78"], // River Nights
				
				isStacked: true,
				
				series: {
				    <? 
				    
				    for($i = 0; $i < count($years); $i++) {
						echo (($i * 2) . ": { targetAxisIndex: $i, labelInLegend: " . $years[$i]. " },\n");
						echo (($i * 2 + 1) . ": { targetAxisIndex: $i , visibleInLegend: false },\n");		
				    }
				    ?>
				    
				},
				
				height: 572,
			    //width: 1680,
				width: <? echo(1710 + $widthAdjustment); ?>,
				
				chartArea:{
				    left:300,
				    top:10,
// 				    width:'80%',
				    width:'10%',
				    backgroundColor: 'transparent',
				},
// 				theme: 'material',
				
// 				legend:
// 				{
// // 				    position: 'top',
// 				    titleTextStyle: { color: 'black' },
// 				},
				
				
// 				chartArea.left: 100,
				
//     chartArea:{left:10,top:20,width:"100%",height:"100%"},
				
				hAxis: {
					//slantedText:true, 
					//slantedTextAngle:90,
					textStyle: { fontSize: 20 },
					titleTextStyle: { italic: false, color: 'black' },
				},
				
				vAxis: {
					title: "<? echo($yAxisName); ?>",
// 					textPosition: 'none',
					textStyle: { fontSize: 20 },
					titleTextStyle: { fontSize: 26, italic: false, bold: true, color: 'black' },
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
			
			var formatter = new google.visualization.NumberFormat({decimalSymbol: '.', fractionDigits: <? echo($fractionDigits); ?>, groupingSymbol: "'", prefix: '<? echo($prefix); ?> ', suffix: ' <? echo($suffix); ?>'});
			for (var i = 1; i <= <? echo(($yearsCovered-1)*2); ?>; i++) {
				formatter.format(data, i);
			}
			
// 			var chartTotalPerDay_<? echo($name); ?> = new google.visualization.ColumnChart(document.getElementById('dayTotal_<? echo($name); ?>'));

// 			chartTotalPerDay_<? echo($name); ?>.draw(data, options);
			
			
			
			// Instantiate and draw our chart, passing in some options.
			var chartTotalPerDay_<? echo($name); ?> = new google.charts.Bar(document.getElementById('dayTotal_<? echo($name); ?>'));
			chartTotalPerDay_<? echo($name); ?>.draw(data, google.charts.Bar.convertOptions(options));
		}
	</script>

	<div id="dayTotal_<? echo($name); ?>" style="border: 0px solid black; width: 1600px; height: 600px; padding-left: <? echo($paddingLeft); ?>px; background-image: url(images/chart-bg.png); background-repeat: no-repeat; background-attachment: relative; background-position: -2px -20px;"></div> <p><br></p>
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
$totalWaxPerDayAndYearInKg = array(); // [day][year]

/* Create one index per day for 30 days.
 * If a day stays empty, it will get ignored in the plot */
for ($i = 0; $i <= 30; $i++) { // for each day add a placeholder index
	$totalPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalWaxPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalFoodPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalWaxPerDayAndYearInKg[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
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
		//$totalPerDayAndYear[$offset]['year'][$year]['total'] = $data['total']; 
		$totalPerDayAndYear[$offset]['year'][$year]['lowerPart'] = $data['total'] - $data['school']; 
		$totalPerDayAndYear[$offset]['year'][$year]['upperPart'] = $data['school']; 
		$totalPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
		
		/* Wax and food in CHF */
		//$totalWaxPerDayAndYear[$offset]['year'][$year]['total'] = $data['total'] - $data['food']; // subtract food again as we only want to see the wax part
		$totalWaxPerDayAndYear[$offset]['year'][$year]['lowerPart'] = $data['total'] - $data['food'] - $data['school']; 
		$totalWaxPerDayAndYear[$offset]['year'][$year]['upperPart'] = $data['school']; 
		$totalWaxPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalWaxPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
		
		/* Food only in CHF */
		$totalFoodPerDayAndYear[$offset]['year'][$year]['lowerPart'] = $data['food']; // We only want to see the food part
		$totalFoodPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
		$totalFoodPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
		
		/* Wax only in kg */
		//$totalWaxPerDayAndYearInKg[$offset]['year'][$year]['total'] = $data['total']; 
		$totalWaxPerDayAndYearInKg[$offset]['year'][$year]['lowerPart'] = $data['parafinWax'] / 1000; 
		$totalWaxPerDayAndYearInKg[$offset]['year'][$year]['upperPart'] = $data['beeWax'] / 1000; 
		$totalWaxPerDayAndYearInKg[$offset]['year'][$year]['date'] = $date; 
		$totalWaxPerDayAndYearInKg[$offset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];
		
		//echo("<pre>");
		//print_r($data);
	}
} 

?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div id="body">
<a name="PerDayAndYear"></a><h1>Umsatz und Wachs pro Tag und Jahr</h1> 

<h3>Übersicht</h3>
<ul>
    <li><a href=#Wax+Gastro_Currency>Umsatz gesamt (Wachs + Gastronomie)</a><br><br></li>
    <li><a href=#Wax_Currency>Umsatz Wachs</a><br><br></li>
    <li><a href=#Gastro_Currency>Umsatz Gastronomie</a><br><br></li>
    <li><a href=#Wax_amount>Wachsmenge</a><br><br></li>
</ul>


<!-- <p><a href="?nocss" target="_self">Ohne Hintergrundbild anzeigen</a><br>&nbsp;</p> -->

<hr>

<a name=Wax+Gastro_Currency></a><h2>Umsatz gesamt (Wachs + Gastronomie) <span style="font-size: 70%">(Dunkle Farbe = Öffentlich, helle Farben = Schule, 2018 ohne Gastronomie)</span></h2>
<? showDiagram("Common", "Umsatz in CHF", $totalPerDayAndYear, ": Öffentlich", ": Schule", 0, 0, "CHF", "", 2); ?>  
<hr>

<a name=Wax_Currency></a><h2>Umsatz Wachs <span style="font-size: 70%">(Dunkle Farbe = Öffentlich, helle Farben = Schule)</span></h2>
<? showDiagram("Wax", "Umsatz in CHF", $totalWaxPerDayAndYear, ": Öffentlich", ": Schule", 0, 0, "CHF", "", 2); ?> 
<hr>

<a name=Gastro_Currency></a><h2>Umsatz Gastronomie <span style="font-size: 70%">(2018 fehlt)</span></h2>
<? showDiagram("Food", "Umsatz in CHF", $totalFoodPerDayAndYear, "", "", 0, 0, "CHF ", "", 2); ?> 
<hr>

<a name=Wax_amount></a><h2>Wachsmenge <span style="font-size: 70%">(Dunkle Farbe = Parafin, helle Farben = Bienenwachs)</span></h2>
<? showDiagram("WaxAmount", "Wachsmenge in kg", $totalWaxPerDayAndYearInKg, ": Parafinwachs", ": Bienenwachs", -20, 20, "", "kg", 1); ?> 

<hr>
<h3>Hinweise</h3>
<ul>
    <li>2020 konnte das Kerzenziehen wegen COVID-19 nicht öffentlich durchgeführt werden.</li>
</ul>

<?
include "$root/framework/footer.php"; 
?>
    
