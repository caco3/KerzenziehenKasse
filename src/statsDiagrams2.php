<? 

$root=".";
include "$root/framework/header.php";
?>

<script type="text/javascript" src="<? echo("$root"); ?>/framework/google-charts-loader.js"></script>
<script>
var data = [];



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
    var headers = [];
    
    google.charts.load('current', {'packages':['corechart', 'bar']});

    // Load chart data via AJAX
    fetch('getChartData.php')
        .then(response => response.json())
        .then(chartData => {
            data = chartData;
            headers = data[0].slice(1);
            google.charts.setOnLoadCallback(function() {
                drawChart();
                drawLegendChart();
            });
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
            firework.launch('Fehler beim Laden der Chart-Daten: ' + error, 'error', 5000);
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








<div id="body" style="margin: 0; padding: 0;">
<h1 style="margin: 0; padding: 0;">Umsatz und Wachs pro Tag und Jahr</h1>


<a name=Wax+Gastro_Currency></a><h2>Umsatz pro Tag (Wachs + Gastronomie)</span></h2>
<div id="chart" style="width: 1999px; margin: 0; padding: 0; height: 470px; background-image: url(images/chart-bg-public-school.png); background-repeat: no-repeat; background-attachment: relative; background-position: -2px -20px;"></div>
<div id="legendChart" style="max-width: 1400px; margin: 0; height: 100px;"></div>








<?
include "$root/framework/footer.php"; 
?>
    
