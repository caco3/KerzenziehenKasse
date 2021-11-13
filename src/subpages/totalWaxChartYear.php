<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawChartTotalWaxPerDay);

    function drawChartTotalWaxPerDay() {
        var data = google.visualization.arrayToDataTable([
<?
            echo("['Tag', ");
            
            $yearsCovered = count($totalWaxPerDayAndYear[0]); // get the number of data columns
			for($i = $yearsCovered; $i > 0; $i--) {
				$year = date("Y") - $i + 1; 
				echo("'$year', ");
			}            
            echo("],\n");
            
            
            foreach($totalWaxPerDayAndYear as $day => $data) {
				//echo("$day\n");
                /* Ignore all empty days from the array */
                if(count($data) == 0) { // ignore empty days
                    continue;
                }


                // X-Axis
//                 echo("[\"");  
//                 foreach($data as $year => $data2) {
//                     echo($data2['formatedDate'] . ",\\n");
//                 }
//                 echo("\", ");  
                
//                 echo("['" . $data[$year - $yearsCovered + 1]['formatedDate'] . "', ");

				if ($data['formatedDate'] == "") {
					continue;
				}
                echo("['" . $data['formatedDate'] . "', ");
                
                
                // Data
                for($i = $yearsCovered; $i > 0; $i--) { // for each year
                    $year = date("Y") - $i + 1; 					
                    if (is_array($data['year']) and array_key_exists($year, $data['year'])) {
                        echo($data['year'][$year]['total'] . ", ");
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
                slantedText:true, 
                slantedTextAngle:90,
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

        var chartTotalWaxPerDay = new google.visualization.ColumnChart(document.getElementById('dayTotalWax'));

        chartTotalWaxPerDay.draw(data, options);
    }
</script>

<div id="dayTotalWax" style="width: 1600px; height: 600px; background-image: url(images/chart-bg.png)"></div> 
