<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawChartTotalsPerDay);

    function drawChartTotalsPerDay() {
        var data = google.visualization.arrayToDataTable([
            ['Tag', 'Total'],
<?
            foreach($statsPerDay as $date => $total) {
                $timestamp = strtotime($date);
                $formatedDate = $germanDayOfWeek[date("N", $timestamp)] . ",\\n" . date("d. ", $timestamp) . $germanMonth[date("m", $timestamp) - 1] . date(". Y", $timestamp);
        
                echo("['" . $formatedDate . "', " . $total . "],\n");    
            }
?> 
        ]);

        var options = { 
            title: 'Umsatz pro Tag',
            titleTextStyle: { fontSize: 18 },
            backgroundColor: 'transparent',
            chartArea: {'width': '80%', 'height': '80%'}, 
//             is3D:true
        };

        var chartTotalsPerDay = new google.visualization.ColumnChart(document.getElementById('dayTotals'));

        chartTotalsPerDay.draw(data, options);
    }
</script>

<div id="dayTotals" style="width: 900px; height: 600px;"></div>
