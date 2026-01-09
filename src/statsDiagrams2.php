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

// Reusable chart function
function createChart(chartId, legendId, dataUrl, chartTitle) {
    var data = [];
    var headers = [];
    
    // Load chart data via AJAX
    console.log("Loading " + dataUrl);
    fetch(dataUrl)
        .then(response => response.json())
        .then(chartData => {
            data = chartData;
            headers = data[0].slice(1);
            
            // Wait for Google Charts to be ready
            function waitForGoogleCharts() {
                if (window.googleChartsReady && google.visualization) {
                    drawChart(chartId, data, headers, chartTitle);
                    drawLegendChart(legendId, headers);
                } else {
                    setTimeout(waitForGoogleCharts, 100);
                }
            }
            waitForGoogleCharts();
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
            firework.launch('Fehler beim Laden der Chart-Daten: ' + error, 'error', 5000);
        });

    function drawChart(chartId, data, headers, chartTitle) {
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
        
        // Calculate max value for scaling
        var maxValue = 0;
        for (var i = 1; i < chartData.length; i++) {
            for (var j = 1; j < chartData[i].length; j++) {
                if (chartData[i][j] > maxValue) maxValue = chartData[i][j];
            }
        }
        console.log("maxValue:", maxValue);
        
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

        var chart = new google.charts.Bar(document.getElementById(chartId));
        chart.draw(chartDataForGoogle, google.charts.Bar.convertOptions(options));
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
}

// Load diagram function
function loadDiagram(name, yTitle, dataId, nameLowerPart, nameUpperPart, widthAdjustment, paddingLeft, prefix, suffix, fractionDigits, bgImage) {
    // Create chart container
    var chartId = 'chart_' + name;
    var legendId = 'legend_' + name;
    
    var container = document.getElementById(dataId);
    if (!container) {
        console.error('Container not found: ' + dataId);
        return;
    }
    
    var chartHtml = '<div id="' + chartId + '" style="width: 1999px; margin: 0; padding: 0; height: 470px; background-image: url(images/' + bgImage + '); background-repeat: no-repeat; background-attachment: relative; background-position: -2px -20px;"></div>';
    chartHtml += '<div id="' + legendId + '" style="max-width: 1400px; margin: 0; height: 100px;"></div>';
    
    container.innerHTML = chartHtml;
    
    // Create the chart using consolidated data file
    console.log("dataId:", dataId);
    createChart(chartId, legendId, 'getChartData.php?type=' + dataId, yTitle);
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

<a name=Wax+Gastro_Currency></a><h2>Umsatz pro Tag (Wachs + Gastronomie)</span></h2><div id="totalPerDayAndYear"></div>
<hr>
<a name=Wax+Gastro_Currency_summed></a><h2>Umsatz aufsummiert (Wachs + Gastronomie)</span></h2><div id="totalPerDayAndYearSummed"></div>
<hr>
<a name=Wax_Currency></a><h2>Umsatz Wachs</span></h2><div id="totalWaxPerDayAndYear"></div>
<hr>
<a name=Gastro_Currency></a><h2>Umsatz Gastronomie</span></h2><div id="totalFoodPerDayAndYear"></div>
<hr>
<a name=Wax_amount></a><h2>Wachsmenge</span></h2><div id="totalWaxPerDayAndYearInKg"></div>
<hr>
<a name=WaxAmountSummed></a><h2>Wachsmenge aufsummiert<span style="font-size: 70%"></span></h2><div id="totalWaxPerDayAndYearInKgSummed"></div>


<script>
loadDiagram("Common", "Umsatz in CHF", "totalPerDayAndYear", ": Öffentlich", ": Schule/Geschlossene Gesellschaft/Private Gruppe", 0, 0, "CHF", "", 2, "chart-bg-public-school.png");
/*
loadDiagram("CommonSummed", "Umsatz aufsummiert in CHF", "totalPerDayAndYearSummed", "", "", -2, 2, "CHF", "", 2, "chart-bg.png");
loadDiagram("Wax", "Umsatz in CHF", "totalWaxPerDayAndYear", ": Öffentlich", ": Schule/Geschlossene Gesellschaft/Private Gruppe", 0, 0, "CHF", "", 2, "chart-bg-public-school.png");
loadDiagram("Food", "Umsatz in CHF", "totalFoodPerDayAndYear", "", "", -8, 8, "CHF ", "", 2, "chart-bg.png");
loadDiagram("WaxAmount", "Wachsmenge in kg", "totalWaxPerDayAndYearInKg", ": Parafinwachs", ": Bienenwachs", -20, 20, "", "kg", 1, "chart-bg-bee-parafin.png");
loadDiagram("WaxAmountSummed", "Wachsmenge in kg", "totalWaxPerDayAndYearInKgSummed", ": Parafinwachs", ": Bienenwachs", -10, 10, "", "kg", 1, "chart-bg-bee-parafin.png"); 
*/
</script>




<?
include "$root/framework/footer.php"; 
?>
    
