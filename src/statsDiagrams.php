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
				if (! array_key_exists($articleId, $articles))  {
					$articles[$articleId] = array();
				}
			//	echo("$articleId:\n");
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


function showDiagram($name, $yAxisName, $data, $nameLowerPart, $nameUpperPart, $widthAdjustment, $paddingLeft, $prefix, $suffix, $fractionDigits, $bgImage) {
    
//     print_r($data);
?>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart', 'bar']});
		google.charts.setOnLoadCallback(drawChartTotalPerDay_<? echo($name); ?>);
				
		function drawChartTotalPerDay_<? echo($name); ?>() {
			var data = google.visualization.arrayToDataTable([
<?
				echo("['', ");
				
				$yearsCovered = date("Y") - 2017 + 1; //count($data[0]); // get the number of data columns
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




					if ((!array_key_exists("formatedDate", $dataOfDay)) or ($dataOfDay['formatedDate'] == "")) {
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
				//colors:["#b30000", "#7c1158", "#4421af", "#1a53ff", "#0d88e6", "#00b7c7", "#5ad45a", "#8be04e", "#ebdc78"], // River Nights
				
				<?
				/* The current year shall have an outstanding color */
				$colors = ["#b30000", "#7c1158", "#4421af", "#1a53ff", "#00b7c7", "#00b7c7", "#5ad45a", "#8be04e", "#ebdc78"];
				$colors = ["#0a481b", "#590058", "#0b448c", "#781f07", "#424006", "#481d32", "#132945", "#4f260c"];
				$colors = array_slice($colors, 0, count($years) - 1); // cut off current and future years
				array_push($colors, "#00af00"); // insert outstanding color
				//array_push($colors, "#af0000"); // insert outstanding color
				echo("colors:[");
				foreach($colors as $color) { echo("\"$color\", "); }
				echo("],");				
				?>				
				
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
				width: <? echo(1735 + $widthAdjustment + 95); ?>, // Needed to change it from 35 to 60 in 2025 and to 95 in 2026
				
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
					gridlines: {
						color: 'transparent'
					}
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

	<div id="dayTotal_<? echo($name); ?>" style="border: 0px solid black; width: 1600px; height: 600px; padding-left: <? echo($paddingLeft); ?>px; background-image: url(images/<? echo($bgImage); ?>); background-repeat: no-repeat; background-attachment: relative; background-position: -2px -20px;"></div> <p><br></p>
	<!--  The background image got generated with `various/chart-bg-generator.py` -->

<?
}



$statsPerDay = array();
for ($i = 0; $i <= (date("Y") - 2017 + 1); $i++) {
	$year = date("Y") - $i; // iterate through the last years (since 2017)
	$stats = getStatsPerDay($year);
// 	echo("<pre>"); print_r($stats); echo("</pre>");
	if (count($stats) == 0) { // no stats for this year => skip
		continue;
	}
	$statsPerDay[$year] = $stats;
}

$totalPerDayAndYear = array(); // [day][year]
$totalPerDayAndYearSummed = array(); // [day][year]
$totalWaxPerDayAndYear = array(); // [day][year]
$totalFoodPerDayAndYear = array(); // [day][year]
$totalWaxPerDayAndYearInKg = array(); // [day][year]
$totalWaxPerDayAndYearInKgSummed = array(); // [day][year]

/* Create one index per day for 30 days.
 * If a day stays empty, it will get ignored in the plot */
for ($i = 0; $i <= 30; $i++) { // for each day add a placeholder index
	$totalPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalWaxPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalFoodPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalWaxPerDayAndYearInKg[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
	$totalWaxPerDayAndYearInKgSummed[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
}
	
for ($i = 0; $i <= 10; $i++) { // for each year
	$year = date("Y") - $i; 
	$dayIndex = 0;
	$totalSummed = 0;
	$beeWaxSummed = 0;
	$parafinWaxSummed = 0;
	//echo("<br>$year<br>");
	if (! array_key_exists($year, $statsPerDay))  {
		$statsPerDay[$year] = array();

	}
	foreach($statsPerDay[$year] as $date => $data) { // for each day
		if ($dayIndex == 0) {
			$firstDay = $date; 
			$zerodayOffset = date("z", strtotime($date));
		}
		$dayOffset = date("z", strtotime($date)) - $zerodayOffset;
		$dayIndex++;
		
		/* Wax and food in CHF */
		$totalWaxPerDayAndYear[$dayOffset]['year'][$year]['lowerPart'] = $data['total'] - $data['food'] - $data['school']; 
		$totalWaxPerDayAndYear[$dayOffset]['year'][$year]['upperPart'] = $data['school']; 
		$totalWaxPerDayAndYear[$dayOffset]['year'][$year]['date'] = $date; 
		$totalWaxPerDayAndYear[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];
		
		/* Wax and food in CHF, summed up */
		$totalSummed += $data['total'];
		//echo("$dayOffset: $totalSummed<br>");
		$totalPerDayAndYearSummed[$dayOffset]['year'][$year]['lowerPart'] = $totalSummed; 
		$totalPerDayAndYearSummed[$dayOffset]['year'][$year]['upperPart'] = 0;
		$totalPerDayAndYearSummed[$dayOffset]['year'][$year]['date'] = $date; 
		$totalPerDayAndYearSummed[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];

		/* Wax only in CHF */
		$totalPerDayAndYear[$dayOffset]['year'][$year]['lowerPart'] = $data['total'] - $data['school']; 
		$totalPerDayAndYear[$dayOffset]['year'][$year]['upperPart'] = $data['school']; 
		$totalPerDayAndYear[$dayOffset]['year'][$year]['date'] = $date; 
		$totalPerDayAndYear[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];  
		/* Food only in CHF */
		$totalFoodPerDayAndYear[$dayOffset]['year'][$year]['lowerPart'] = $data['food']; // We only want to see the food part
		$totalFoodPerDayAndYear[$dayOffset]['year'][$year]['upperPart'] = 0;
		$totalFoodPerDayAndYear[$dayOffset]['year'][$year]['date'] = $date; 
		$totalFoodPerDayAndYear[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
		
		/* Wax only in kg */
		$totalWaxPerDayAndYearInKg[$dayOffset]['year'][$year]['lowerPart'] = $data['parafinWax'] / 1000; 
		$totalWaxPerDayAndYearInKg[$dayOffset]['year'][$year]['upperPart'] = $data['beeWax'] / 1000; 
		$totalWaxPerDayAndYearInKg[$dayOffset]['year'][$year]['date'] = $date; 
		$totalWaxPerDayAndYearInKg[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];
		
		/* Wax only in kg summed up */
		$parafinWaxSummed += $data['parafinWax'] / 1000;
		$beeWaxSummed += $data['beeWax'] / 1000;
		$totalWaxPerDayAndYearInKgSummed[$dayOffset]['year'][$year]['lowerPart'] = $parafinWaxSummed; 
		$totalWaxPerDayAndYearInKgSummed[$dayOffset]['year'][$year]['upperPart'] = $beeWaxSummed; 
		$totalWaxPerDayAndYearInKgSummed[$dayOffset]['year'][$year]['date'] = $date; 
		$totalWaxPerDayAndYearInKgSummed[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];
	}
	
	//echo("<pre>"); print_r($totalWaxPerDayAndYearInKgSummed); echo("</pre>");
	

	if (! array_key_exists(0, $totalPerDayAndYearSummed)) { // No data for this year
		continue;
	}

	if (! array_key_exists("year", $totalPerDayAndYearSummed[0])) { // No data for this year
		continue;
	}

	if (! array_key_exists($year, $totalPerDayAndYearSummed[0]['year']))  {
		$totalPerDayAndYearSummed[0]['year'][$year] = array();
		$totalPerDayAndYearSummed[0]['year'][$year]['lowerPart'] = 0;
		$totalPerDayAndYearSummed[0]['year'][$year]['upperPart'] = 0;
	}

	if (! array_key_exists($year, $totalWaxPerDayAndYearInKgSummed[0]['year']))  {
		$totalWaxPerDayAndYearInKgSummed[0]['year'][$year] = array();
		$totalWaxPerDayAndYearInKgSummed[0]['year'][$year]['lowerPart'] = 0;
		$totalWaxPerDayAndYearInKgSummed[0]['year'][$year]['upperPart'] = 0;
	}

	/* Fill up empty days on the summed up data */
	for ($x = 1; $x < 15; $x++) {
		// echo("x: $x<br>");
		if (array_key_exists($x, $totalPerDayAndYearSummed))  {
			if (! array_key_exists($year, $totalPerDayAndYearSummed[$x]['year']))  {
				$totalPerDayAndYearSummed[$x]['year'][$year] = array();
				$totalPerDayAndYearSummed[$x]['year'][$year]['lowerPart'] = 0;
				$totalPerDayAndYearSummed[$x]['year'][$year]['upperPart'] = 0;
			}

			if ($totalPerDayAndYearSummed[$x]['year'][$year]['lowerPart'] == 0) {
				$totalPerDayAndYearSummed[$x]['year'][$year]['lowerPart'] = $totalPerDayAndYearSummed[$x - 1]['year'][$year]['lowerPart'];
			}

			if (! array_key_exists($year, $totalWaxPerDayAndYearInKgSummed[$x]['year']))  {
				$totalWaxPerDayAndYearInKgSummed[$x]['year'][$year] = array();
				$totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['lowerPart'] = 0;
				$totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['upperPart'] = 0;
			}

			if ($totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['lowerPart'] == 0) {
				$totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['lowerPart'] = $totalWaxPerDayAndYearInKgSummed[$x - 1]['year'][$year]['lowerPart'];
			}
			if ($totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['upperPart'] == 0) {
				$totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['upperPart'] = $totalWaxPerDayAndYearInKgSummed[$x - 1]['year'][$year]['upperPart'];
			}
		}
	}
	
	/* Remove future days on the summed data of the current year */
	if ($year == date("Y")) {
		for ($x = 14; $x > 0; $x--) {
			if (array_key_exists($x, $totalPerDayAndYearSummed))  {
				if ($totalWaxPerDayAndYearInKg[$x]['year'][$year]['lowerPart'] == 0) { // This day has no booking yet and is therefore most likely in the future
					$totalPerDayAndYearSummed[$x]['year'][$year]['lowerPart'] = 0;
					$totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['lowerPart'] = 0;
					$totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['upperPart'] = 0;
				}
			}
		}
	}
} 

?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div id="body">
<h1>Umsatz und Wachs pro Tag und Jahr</h1>
<div style="display: flex; align-items: flex-start; gap: 20px 60px; margin-bottom: 20px;">
	<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; backdrop-filter: blur(5px); flex: 0 0 auto;">
		<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 0.65); font-size: 16px; font-weight: 600;">Diagramme:</h4>
		<ul style="margin: 0; padding-left: 20px;">
			<li><a href=#Wax+Gastro_Currency>Umsatz pro Tag (Wachs + Gastronomie)</a><br><br></li>
			<li><a href=#Wax+Gastro_Currency_summed>Umsatz aufsummiert (Wachs + Gastronomie)</a><br><br></li>
			<li><a href=#Wax_Currency>Umsatz Wachs</a><br><br></li>
			<li><a href=#Gastro_Currency>Umsatz Gastronomie</a><br><br></li>
			<li><a href=#Wax_amount>Wachsmenge</a><br><br></li>
			<li><a href=#WaxAmountSummed>Wachsmenge aufsummiert</a></li>
		</ul>
	</div>
	
	<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; backdrop-filter: blur(5px); flex: 0 0 auto; margin-left: auto;">
		<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 0.65); font-size: 16px; font-weight: 600;">Hinweise:</h4>
		<ul style="margin: 0; padding-left: 20px; color: rgba(73, 80, 87, 0.65); font-size: 16px;">
			<li style="margin-bottom: 10px;">2020 konnte das Kerzenziehen wegen COVID-19 nicht öffentlich durchgeführt werden.</li>
			<li style="margin-bottom: 10px;">Wachspreise:
				<table style="margin-top: 8px; border-collapse: collapse; width: 100%; font-size: 15px;">
					<thead>
						<tr style="border-bottom: 1px solid rgba(73, 80, 87, 0.3);">
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 0.65); font-weight: 600;">Jahr</th>
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 0.65); font-weight: 600;">Bienenwachs</th>
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 0.65); font-weight: 600;">Parafinwachs</th>
						</tr>
					</thead>
					<tbody>
						<tr style="border-bottom: 1px solid rgba(73, 80, 87, 0.2);">
							<td style="padding: 1px 8px;">Ab 2023</td>
							<td style="padding: 1px 8px;">CHF 4.60</td>
							<td style="padding: 1px 8px;">CHF 3.60</td>
						</tr>
						<tr style="border-bottom: 1px solid rgba(73, 80, 87, 0.2);">
							<td style="padding: 1px 8px;">2022</td>
							<td style="padding: 1px 8px;">CHF 4.50</td>
							<td style="padding: 1px 8px;">CHF 3.50</td>
						</tr>
						<tr>
							<td style="padding: 1px 8px;">Bis 2021</td>
							<td style="padding: 1px 8px;">CHF 4.40</td>
							<td style="padding: 1px 8px;">CHF 3.30</td>
						</tr>
					</tbody>
				</table>
			</li>
		</ul>
	</div>
</div>


<!-- <p><a href="?nocss" target="_self">Ohne Hintergrundbild anzeigen</a><br>&nbsp;</p> -->

<hr>

<a name=Wax+Gastro_Currency></a><h2>Umsatz pro Tag (Wachs + Gastronomie) <span style="font-size: 70%"></span></h2>
<? showDiagram("Common", "Umsatz in CHF", $totalPerDayAndYear, ": Öffentlich", ": Schule/Geschlossene Gesellschaft/Private Gruppe", 0, 0, "CHF", "", 2, "chart-bg-public-school.png"); ?>  
<hr>

<a name=Wax+Gastro_Currency_summed></a><h2>Umsatz aufsummiert (Wachs + Gastronomie) <span style="font-size: 70%"></span></h2>
<? showDiagram("CommonSummed", "Umsatz aufsummiert in CHF", $totalPerDayAndYearSummed, "", "", -2, 2, "CHF", "", 2, "chart-bg.png"); ?>  
<hr>

<a name=Wax_Currency></a><h2>Umsatz Wachs <span style="font-size: 70%"></span></h2>
<? showDiagram("Wax", "Umsatz in CHF", $totalWaxPerDayAndYear, ": Öffentlich", ": Schule/Geschlossene Gesellschaft/Private Gruppe", 0, 0, "CHF", "", 2, "chart-bg-public-school.png"); ?> 
<hr>

<a name=Gastro_Currency></a><h2>Umsatz Gastronomie <span style="font-size: 70%"></span></h2>
<? showDiagram("Food", "Umsatz in CHF", $totalFoodPerDayAndYear, "", "", -8, 8, "CHF ", "", 2, "chart-bg.png"); ?> 
<hr>

<a name=Wax_amount></a><h2>Wachsmenge <span style="font-size: 70%"></span></h2>
<? showDiagram("WaxAmount", "Wachsmenge in kg", $totalWaxPerDayAndYearInKg, ": Parafinwachs", ": Bienenwachs", -20, 20, "", "kg", 1, "chart-bg-bee-parafin.png"); ?> 

<a name=WaxAmountSummed></a><h2>Wachsmenge aufsummiert<span style="font-size: 70%"></span></h2>
<? showDiagram("WaxAmountSummed", "Wachsmenge in kg", $totalWaxPerDayAndYearInKgSummed, ": Parafinwachs", ": Bienenwachs", -10, 10, "", "kg", 1, "chart-bg-bee-parafin.png"); ?> 

<hr>

<?
include "$root/framework/footer.php"; 
?>
    
