<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawChartTotalsPerDay);

    function drawChartTotalsPerDay() {
        var data = google.visualization.arrayToDataTable([
<?
            echo("['Tag', ");
            
            $yearsCovered = count($totalPerDayAndYear[0]); // get the number of data columns
                for($i = $yearsCovered; $i > 0; $i--) {
                $year = date("Y") - $i + 1; 
                echo("'$year', ");
            }            
            echo("],\n");
            
            
            foreach($totalPerDayAndYear as $day => $data) {
                // X-Axis
//                 echo("[\"");  
//                 foreach($data as $year => $data2) {
//                     echo($data2['formatedDate'] . ",\\n");
//                 }
//                 echo("\", ");  
                
                echo("['" . $data[$year - $yearsCovered + 1]['formatedDate'] . "', ");
                
                
                // Data
                for($i = $yearsCovered; $i > 0; $i--) {
                    $year = date("Y") - $i + 1; 
                    if (array_key_exists($year, $data)) {
                        echo($data[$year]['total'] . ", ");
                    }
                    else {
                        echo("'', ");
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
                left: 100,
                height: '70%' 
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

        var chartTotalsPerDay = new google.visualization.ColumnChart(document.getElementById('dayTotals'));

        chartTotalsPerDay.draw(data, options);
    }
</script>

<div id="dayTotals" style="width: 900px; height: 600px;"></div>
