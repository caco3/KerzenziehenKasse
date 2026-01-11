<?

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
			//	$colors = ["#b30000", "#7c1158", "#4421af", "#1a53ff", "#00b7c7", "#00b7c7", "#5ad45a", "#8be04e", "#ebdc78"];
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

?>
