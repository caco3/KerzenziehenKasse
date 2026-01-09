<? 

$root=".";
include "$root/framework/header.php";
/*include "statsDataProvider.php";
include "statsDiagramsFunctions.php";



$statsData = getStatsData();
$totalPerDayAndYear = $statsData['totalPerDayAndYear'];
$totalPerDayAndYearSummed = $statsData['totalPerDayAndYearSummed'];
$totalWaxPerDayAndYear = $statsData['totalWaxPerDayAndYear'];
$totalFoodPerDayAndYear = $statsData['totalFoodPerDayAndYear'];
$totalWaxPerDayAndYearInKg = $statsData['totalWaxPerDayAndYearInKg'];
$totalWaxPerDayAndYearInKgSummed = $statsData['totalWaxPerDayAndYearInKgSummed']; */

?>

<script>
var data = [
    ['', '2017: Öffentlich', '2017: Schule/Geschlossene Gesellschaft/Private Gruppe', '2018: Öffentlich', '2018: Schule/Geschlossene Gesellschaft/Private Gruppe', '2019: Öffentlich', '2019: Schule/Geschlossene Gesellschaft/Private Gruppe', '2021: Öffentlich', '2021: Schule/Geschlossene Gesellschaft/Private Gruppe', '2022: Öffentlich', '2022: Schule/Geschlossene Gesellschaft/Private Gruppe', '2023: Öffentlich', '2023: Schule/Geschlossene Gesellschaft/Private Gruppe', '2024: Öffentlich', '2024: Schule/Geschlossene Gesellschaft/Private Gruppe', '2025: Öffentlich', '2025: Schule/Geschlossene Gesellschaft/Private Gruppe', '2026: Öffentlich', '2026: Schule/Geschlossene Gesellschaft/Private Gruppe'],
    ['So', 900.0, 400.0, 2000.0, 400.0, 1920.3, 500.0, 1382.5, 500.0, 1914.2, 500.0, 2026.5, 0.0, 1568.4, 0.0, 1630.2, 0.0, 2009.0, 1700.5],
    ['Mo', 100, 0, 0.0, 100.7, 0, 0, 113.9, 100.0, 0.0, 317.2, 0.0, 150.0, 5.0, 452.8, 0.0, 150.0, 50.5, 1072.0],
    ['Di', 1000.0, 100.0, 0, 0, 0.0, 148.2, 48.1, 125.5, 0.0, 150.0, 0.0, 300.0, 0.0, 300.0, 54.0, 420.1, 26.5, 57.5],
    ['Mi', 873.8, 0.0, 777.3, 0.0, 769.1, 100.0, 826.3, 120.5, 915.5, 0.0, 1039.0, 150.0, 931.7, 150.0, 1297.4, 150.0, 23.0, 1061.5],
    ['Do', 1000.0, 217.0, 0.0, 135.8, 86.1, 0.0, 0.0, 106.3, 0.0, 300.0, 3.7, 316.3, 0.0, 333.3, 0.0, 336.7, 0, 1000],
    ['Fr', 487.0, 0.0, 322.8, 0.0, 882.1, 217.3, 508.4, 332.1, 986.6, 782.3, 572.6, 150.0, 1048.5, 185.8, 945.0, 300.0, 0, 1000],
    ['Sa', 1086.6, 0.0, 1140.5, 0.0, 1718.2, 0.0, 1886.3, 0.0, 2732.2, 0.0, 1909.0, 0.0, 1938.8, 0.0, 2058.3, 0.0, 0, 1000],
    ['So', 1560.6, 0.0, 1517.9, 0.0, 3058.7, 0.0, 2507.2, 0.0, 3350.8, 0.0, 2883.3, 0.0, 3514.9, 0.0, 2927.6, 0.0, 0, 1000],
    ['Mo', 1000, 0, 8.9, 226.5, 0, 0, 126.1, 274.0, 0.0, 322.5, 0.0, 300.0, 0.0, 470.0, 64.5, 360.5, 0, 1000],
    ['Di', 1000.0, 142.5, 0.0, 125.3, 0, 0, 0.0, 203.0, 11.0, 300.0, 0.0, 300.0, 0.0, 301.7, 0.0, 307.5, 0, 1000],
    ['Mi', 916.4, 0.0, 896.0, 581.7, 1239.3, 0.0, 1218.7, 150.3, 1556.9, 150.0, 1129.5, 150.0, 1721.1, 218.6, 1498.1, 150.0, 0, 1000],
    ['Do', 241.5, 0.0, 0.0, 158.6, 0, 0, 2.0, 243.6, 0.0, 300.0, 127.0, 200.0, 0.0, 379.6, 3.0, 300.0, 0, 1000],
    ['Fr', 765.5, 0.0, 883.2, 0.0, 883.1, 0.0, 1317.9, 229.7, 1705.4, 406.9, 1186.3, 371.5, 1424.6, 150.0, 1388.8, 250.0, 3000, 1000],
    ['Sa', 1394.6, 0.0, 1852.3, 0.0, 2313.1, 0.0, 2583.8, 0.0, 3871.1, 0.0, 2830.4, 0.0, 3092.0, 0.0, 2848.6, 0.0, 2000, 1000],
    ['So', 1275.8, 0.0, 1594.3, 0.0, 1909.8, 105.0, 1724.3, 0.0, 2604.4, 0.0, 2751.9, 0.0, 2729.6, 0.0, 3179.1, 0.0, 1000, 500]
];



// Hardcoded colors for each series (18 colors for 9 years × 2 series)
var seriesColors = [
    "#0a481b", // year 1
    "#590058", // year 2
    "#0b448c", // year 3
    "#781f07", // year 4
    "#424006", // year 5
    "#481d32", // year 6
    "#132945", // year 7
    "#69625e", // year 8
    "#831843", // year 9
    "#b4748e", // year 10
    "#0a481b", // year 11
    "#590058", // year 12
    "#0b448c", // year 13
    "#781f07", // year 14
    "#424006", // year 15
    "#481d32", // year 16
    "#132945", // year 17
    "#69625e", // year 18
    "#831843", // year 19
    "#b4748e"  // year 20
];

// Function to generate bright colors from dark colors
// This is used for the legend chart.
// The colors do not fully match what the chart above does, but it is close enough.
function generateBrightColor(hexColor) {
    // Remove # and convert to RGB
    var r = parseInt(hexColor.substr(1, 2), 16);
    var g = parseInt(hexColor.substr(3, 2), 16);
    var b = parseInt(hexColor.substr(5, 2), 16);
    
    // Increase brightness by adding 40% of the difference to 255
    r = Math.min(255, r + Math.floor((255 - r) * 0.4));
    g = Math.min(255, g + Math.floor((255 - g) * 0.4));
    b = Math.min(255, b + Math.floor((255 - b) * 0.4));
    
    // Convert back to hex
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}

// Set current year to outstanding lighter green
var currentYear = new Date().getFullYear();
for (var yearIndex = 0; yearIndex < 10; yearIndex++) {
    var yearValue = 2017 + yearIndex;    
    if (yearValue === currentYear - 5) {
        seriesColors[yearIndex * 2] = '#00af00';
    }
}

</script>


<div id="body" style="margin: 0; padding: 0;">
<h1 style="margin: 0; padding: 0;">Umsatz und Wachs pro Tag und Jahr</h1>

<!-- Google Charts Bar Chart -->
<div id="chart" style="width: 1999px; margin: 0; padding: 0; height: 470px; background-image: url(images/chart-bg-public-school.png); background-repeat: no-repeat; background-attachment: relative; background-position: -2px -20px;"></div>

<!-- Google Charts Legend (HTML) -->
<div id="legendChart" style="max-width: 1400px; margin: 0; height: 100px;"></div>

<style>
    /* Override Google Charts default padding */
    #chart > div {
        padding: 0 !important;
        margin: 0 !important;
        margin-left: 5px !important; /* Force left margin */
    }
    
    /* Remove any body margins */
    body {
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden !important; /* Hide horizontal overflow */
    }
    
    /* Full viewport width */
    html, body {
        width: 100vw !important;
        overflow-x: hidden !important;
    }
    
    /* Google Charts specific overrides */
    .google-visualization-chart {
        margin: 0 !important;
        padding: 0 !important;
        margin-left: 5px !important; /* Force left margin */
    }
    
    /* Hide chart overflow */
    #chart {
        overflow-x: hidden !important;
        margin-left: 5px !important; /* Force left margin */
    }
</style>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    // Extract headers for series names
    var headers = data[0].slice(1);
    
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(function() {
        drawChart();
        drawLegendChart();
    });

    function drawChart() {
        // Prepare data for Google Charts - each year with 2 stacked values
        var chartData = [];
        
        // Add header row
        var headerRow = [''];
        var years = [];
        var googleColors = [];
        var yearCounter = 0;
        for (var i = 0; i < headers.length; i += 2) {
            var yearName = headers[i].split(':')[0];
            years.push(yearName);
            headerRow.push(yearName + ' - Öffentlich');
            headerRow.push(yearName + ' - Schule');
            
            // Use hardcoded colors for each series
            googleColors.push(seriesColors[yearCounter * 2]); // Color for this year
            googleColors.push(seriesColors[yearCounter * 2 + 1]); // Color for next year (why does it need to be done this way? No idea, but it works)
            yearCounter++;
        }
        chartData.push(headerRow);
        
        // Add data rows - keep the two values separate for stacking
        for (var row = 1; row < data.length; row++) {
            var dataRow = [data[row][0]];
            for (var i = 0; i < headers.length; i += 2) {
                var publicValue = data[row][i + 1] || 0;
                var schoolValue = data[row][i + 2] || 0;
                dataRow.push(publicValue);
                dataRow.push(schoolValue);
            }
            chartData.push(dataRow);
        }
        
        var chartDataForGoogle = google.visualization.arrayToDataTable(chartData);
        
        // Calculate max value for scaling
        var maxValue = 0;
        for (var i = 1; i < chartData.length; i++) {
            for (var j = 1; j < chartData[i].length; j++) {
                if (chartData[i][j] > maxValue) maxValue = chartData[i][j];
            }
        }
        
        // Generate series configuration - each year gets 2 series (stacked)
        var series = {};
        for (var i = 0; i < years.length; i++) {
            series[i * 2] = { targetAxisIndex: i, labelInLegend: years[i] };
            series[i * 2 + 1] = { targetAxisIndex: i, visibleInLegend: false };
        }
        
        // Generate vAxes configuration - hide all but first axis
        var vAxes = {};
        vAxes[0] = { 
            gridlines: { count: 0 }, 
            ticks: [0], 
            textStyle: { fontSize: 18 }
        };
        for (var i = 1; i < years.length; i++) {
            vAxes[i] = { gridlines: { count: 0 }, ticks: [0], textStyle: { fontSize: 0 } };
        }
        
        var options = {
            backgroundColor: 'transparent',
            isStacked: true,
            height: 460,
            width: '120vw',
            
            chartArea: {
                left: 120,
                top: 10,
                width: '100%',
                height: '80%',
                backgroundColor: 'transparent',
            },
            
            hAxis: {
                textStyle: { fontSize: 20 },
                title: null,
            },
            
            vAxis: {
                textStyle: { fontSize: 14 },
                viewWindow: {
                    min: 0,
                    max: maxValue * 1.1,
                },
                gridlines: {
                    color: 'transparent'
                }
            },
            
            bar: { groupWidth: '85%' },
            
            series: series,
            vAxes: vAxes,
            
            colors: googleColors,
            
            legend: {
                position: 'none'
            }
        };

        var chart = new google.charts.Bar(document.getElementById('chart'));
        chart.draw(chartDataForGoogle, google.charts.Bar.convertOptions(options));
    }
    
    // HTML Legend
    function drawLegendChart() {
        var legendHTML = '<div style="display: flex; justify-content: center; align-items: center; gap: 20px; padding: 20px; margin-top: 0; padding-top: 0; background: transparent;">';
        
        for (var i = 0; i < headers.length; i += 2) {
            var yearName = headers[i].split(':')[0];
            var yearIndex = Math.floor(i / 2);
            var darkColor = seriesColors[yearIndex];
            var lightColor = generateBrightColor(darkColor);
            
            legendHTML += '<div style="display: flex; align-items: center; gap: 8px;">';
            legendHTML += '<div style="display: flex; gap: 2px;">';
            legendHTML += '<div style="width: 20px; height: 20px; background-color: ' + darkColor + '; border: 1px solid #ccc;"></div>';
            legendHTML += '<div style="width: 20px; height: 20px; background-color: ' + lightColor + '; border: 1px solid #ccc;"></div>';
            legendHTML += '</div>';
            legendHTML += '<span style="font-size: 14px; font-weight: bold;">' + yearName + '</span>';
            legendHTML += '</div>';
        }
        
        legendHTML += '</div>';
        
        document.getElementById('legendChart').innerHTML = legendHTML;
    }
</script>




<?
include "$root/framework/footer.php"; 
?>
    
