<? 

$root=".";
include "$root/framework/header.php";
?>

<script type="text/javascript" src="<? echo("$root"); ?>/framework/google-charts-loader.js"></script>
<script>
// Load Google Charts once
google.charts.load('current', {'packages':['corechart', 'bar']});

// Wait for Google Charts to be ready
google.charts.setOnLoadCallback(function() {
    // Set a flag to indicate Google Charts is ready
    window.googleChartsReady = true;
});

// Global data cache for client-side sharing
var allChartData = null;

// Load all chart data once
function loadAllChartData() {
    return new Promise(function(resolve, reject) {
        if (allChartData) {
            resolve(allChartData);
            return;
        }
        
        var startTime = performance.now();
        console.log('Starting to load chart data...');
        
        fetch('getChartData.php')
            .then(response => response.json())
            .then(data => {
                var endTime = performance.now();
                var loadTime = endTime - startTime;
                console.log('Chart data loaded in ' + loadTime.toFixed(2) + ' ms');
                
                allChartData = data;
                resolve(allChartData);
            })
            .catch(error => {
                var endTime = performance.now();
                var loadTime = endTime - startTime;
                console.error('Error loading chart data after ' + loadTime.toFixed(2) + ' ms:', error);
                reject(error);
            });
    });
}

// Reusable chart function - now uses shared data
function createChart(chartId, legendId, chartType, chartTitle, prefix, suffix, fractionDigits) {
    return new Promise(function(resolve, reject) {
        loadAllChartData()
            .then(allData => {
                if (!allData[chartType]) {
                    reject('Data not found for chart type: ' + chartType);
                    return;
                }
                
                var data = allData[chartType];
                var headers = data[0].slice(1);
                
                // Wait for Google Charts to be ready
                function waitForGoogleCharts() {
                    if (window.googleChartsReady && google.visualization) {
                        drawChart(chartId, data, headers, chartTitle, prefix, suffix, fractionDigits);
                        drawLegendChart(legendId, headers);
                        resolve();
                    } else {
                        setTimeout(waitForGoogleCharts, 100);
                    }
                }
                waitForGoogleCharts();
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                firework.launch('Fehler beim Laden der Chart-Daten: ' + error, 'error', 5000);
                reject(error);
            });
    });
}

function drawChart(chartId, data, headers, chartTitle, prefix, suffix, fractionDigits) {
    var startTime = performance.now();
    console.log('Starting to render chart: ' + chartId);
    
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
            googleColors.push(seriesColors[yearCounter * 2]);
            googleColors.push(seriesColors[yearCounter * 2 + 1]);
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
        
        // Calculate max value for scaling - use sum of stacked values
        var maxValue = 0;
        for (var i = 1; i < chartData.length; i++) {
            for (var j = 1; j < chartData[i].length; j += 2) {
                var stackedValue = (parseFloat(chartData[i][j]) || 0) + (parseFloat(chartData[i][j + 1]) || 0);
                if (stackedValue > maxValue) maxValue = stackedValue;
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
           // width: '120vw',
            
            chartArea: {
                left: 120,
                top: 10,
                width: '10%',
                height: '80%',
                backgroundColor: 'transparent',
            },
            
            hAxis: {
                textStyle: { fontSize: 20 },
                title: null,
            },
            
            vAxis: {
                viewWindow: {
                    min: 0,
                    max: maxValue * 1.02,
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

        var chart = new google.charts.Bar(document.getElementById(chartId));
        chart.draw(chartDataForGoogle, google.charts.Bar.convertOptions(options));
        
        // Apply number formatting for tooltips like the original implementation
        var formatter = new google.visualization.NumberFormat({
            decimalSymbol: '.', 
            fractionDigits: fractionDigits || 2, 
            groupingSymbol: "'", 
            prefix: (prefix || '') + ' ', 
            suffix: ' ' + (suffix || '')
        });
        for (var i = 1; i < chartDataForGoogle.getNumberOfColumns(); i++) {
            formatter.format(chartDataForGoogle, i);
        }
        
        // Hide loading indicator
        var loadingElement = document.getElementById('loading_' + chartId);
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
        
        var endTime = performance.now();
        var renderTime = endTime - startTime;
        console.log('Chart ' + chartId + ' rendered in ' + renderTime.toFixed(2) + ' ms');
    }

// HTML Legend
function drawLegendChart(legendId, headers) {
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
        
        document.getElementById(legendId).innerHTML = legendHTML;
    }

// Create container immediately (without data loading)
function createDiagramContainer(name, yTitle, dataId, paddingLeft, prefix, suffix, fractionDigits, bgImage) {
    var chartId = 'chart_' + name;
    var legendId = 'legend_' + name;
    
    var container = document.getElementById(dataId);
    if (!container) {
        console.error('Container not found: ' + dataId);
        return null;
    }
    
    // Show chart container with background image and loading indicator immediately
    // Adjust padding-left to the container to make sure all X axis tick labels have the same width (so it matches the background image position)
    // And compensate the width with it
    var chartHtml = '<div id="' + chartId + '" style="width: ' + (1994 - paddingLeft) + 'px; margin: 0; padding: 0 0 0 ' + paddingLeft + 'px; height: 470px; background-image: url(images/' + bgImage + '); background-repeat: no-repeat; background-attachment: relative; background-position: -9px -20px; position: relative;">';
    chartHtml += '<div id="loading_' + chartId + '" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255, 255, 255, 0.9); padding: 20px; border-radius: 8px; text-align: center; font-size: 16px; font-weight: bold; color: #333; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">Lade Diagramm...</div>';
    chartHtml += '</div>';
    chartHtml += '<div id="' + legendId + '" style="max-width: 1400px; margin: 0; height: 100px;"></div>';
    
    container.innerHTML = chartHtml;
    
    return { chartId: chartId, legendId: legendId, dataId: dataId, yTitle: yTitle, prefix: prefix, suffix: suffix, fractionDigits: fractionDigits };
}

// Load data into existing container
function loadDiagramData(containerInfo) {
    if (!containerInfo) return Promise.reject('Invalid container info');
    
    // Start loading data in background using shared data
    return createChart(containerInfo.chartId, containerInfo.legendId, containerInfo.dataId, containerInfo.yTitle, containerInfo.prefix, containerInfo.suffix, containerInfo.fractionDigits);
}

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
       /* margin: 0 !important;*/
        /*margin: 0 0 0 5px !important;*/
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

<div id="body" style="margin: 0; padding: 0;">
<h1 style="margin: 0; padding: 0;">Umsatz und Wachs pro Tag und Jahr</h1>
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
<hr>

<hr>
<a name=Wax+Gastro_Currency></a><h2>Umsatz pro Tag (Wachs + Gastronomie)</span></h2><div id="totalPerDayAndYear"></div><hr>
<a name=Wax+Gastro_Currency_summed></a><h2>Umsatz aufsummiert (Wachs + Gastronomie)</span></h2><div id="totalPerDayAndYearSummed"></div><hr>
<a name=Wax_Currency></a><h2>Umsatz Wachs</span></h2><div id="totalWaxPerDayAndYear"></div><hr>
<a name=Gastro_Currency></a><h2>Umsatz Gastronomie</span></h2><div id="totalFoodPerDayAndYear"></div><hr>
<a name=Wax_amount></a><h2>Wachsmenge in kg</span></h2><div id="totalWaxPerDayAndYearInKg"></div><hr>
<a name=WaxAmountSummed></a><h2>Wachsmenge aufsummiert in kg</span></h2><div id="totalWaxPerDayAndYearInKgSummed"></div>

<script>
// Create all containers immediately, then load data once and render all charts
function loadAllDiagrams() {
    var totalStartTime = performance.now();
    console.log('=== DIAGRAM LOADING START ===');
    console.log('Starting to load all diagrams...');
    
    // Create all containers first (immediate display)
    var commonContainer = createDiagramContainer("Common", "Umsatz in CHF", "totalPerDayAndYear",  6, "CHF", "", 2, "chart-bg-public-school.png");
    var commonSummedContainer = createDiagramContainer("CommonSummed", "Umsatz aufsummiert in CHF", "totalPerDayAndYearSummed",  10, "CHF", "", 2, "chart-bg.png");
    var waxContainer = createDiagramContainer("Wax", "Umsatz in CHF", "totalWaxPerDayAndYear", 6, "CHF", "", 2, "chart-bg-public-school.png");
    var foodContainer = createDiagramContainer("Food", "Umsatz in CHF", "totalFoodPerDayAndYear", 10, "CHF ", "", 2, "chart-bg.png");
    var waxAmountContainer = createDiagramContainer("WaxAmount", "Wachsmenge in kg", "totalWaxPerDayAndYearInKg", 20, "", "kg", 1, "chart-bg-bee-parafin.png");
    var waxAmountSummedContainer = createDiagramContainer("WaxAmountSummed", "Wachsmenge in kg", "totalWaxPerDayAndYearInKgSummed", 10, "", "kg", 1, "chart-bg-bee-parafin.png");
    
    var containers = [commonContainer, commonSummedContainer, waxContainer, foodContainer, waxAmountContainer, waxAmountSummedContainer];
    
    // Load all data once, then render all charts in parallel
    loadAllChartData()
        .then(function() {
            var dataLoadTime = performance.now() - totalStartTime;
            console.log('✓ Data loading completed in ' + dataLoadTime.toFixed(2) + ' ms, starting chart rendering...');
            
            // Render all charts simultaneously
            var promises = containers.map(function(container) {
                return loadDiagramData(container);
            });

            Promise.all(promises)
                .then(function() {
                    var totalTime = performance.now() - totalStartTime;
                    console.log('✓ All charts rendered successfully');
                    console.log('=== DIAGRAM LOADING COMPLETE ===');
                    console.log('Total time: ' + totalTime.toFixed(2) + ' ms');
                    console.log('Charts rendered: ' + containers.length);
                })
                .catch(function(error) {
                    var totalTime = performance.now() - totalStartTime;
                    console.error('✗ Error rendering diagrams after ' + totalTime.toFixed(2) + ' ms:', error);
                    firework.launch('Fehler beim Rendern der Diagramme: ' + error, 'error', 5000);
                });
        })
        .catch(function(error) {
            var totalTime = performance.now() - totalStartTime;
            console.error('✗ Error loading diagrams after ' + totalTime.toFixed(2) + ' ms:', error);
            firework.launch('Fehler beim Laden der Diagramme: ' + error, 'error', 5000);
        });
}

// Start loading when page is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAllDiagrams);
} else {
    loadAllDiagrams();
}
</script>

<?
include "$root/framework/footer.php"; 
?>
    
