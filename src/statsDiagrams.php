<? 

$root=".";
include "$root/framework/header.php";
?>

<script type="text/javascript" src="<? echo("$root"); ?>/framework/google-charts-loader.js"></script>
<script>
// Load Google Charts once
google.charts.load('current', {'packages':['corechart', 'bar']});

// Wait for Google Charts to be ready
/**
 * Callback executed once Google Charts finished loading; sets the
 * googleChartsReady flag.
 */
google.charts.setOnLoadCallback(function() {
    // Set a flag to indicate Google Charts is ready
    window.googleChartsReady = true;
});

// Global data cache for client-side sharing
var allChartData = null;
var chartConfig = null;

// Load all chart data once
/**
 * Fetches all chart data and config from framework/getChartData.php once
 * and caches it in allChartData/chartConfig.
 *
 * @returns {Promise<Object>} Resolves with the chart data map.
 */
function loadAllChartData() {
    return new Promise(
        /**
         * Promise executor performing the fetch of the chart data.
         *
         * @param {Function} resolve - Promise resolve callback.
         * @param {Function} reject - Promise reject callback.
         */
        function(resolve, reject) {
        if (allChartData && chartConfig) {
            resolve(allChartData);
            return;
        }
        
        var startTime = performance.now();
        console.log('Starting to load chart data...');
        
        fetch('framework/getChartData.php')
            .then(
                /** @param {Response} response - Fetch response. @returns {Promise} Parsed JSON body. */
                response => response.json())
            .then(
                /**
                 * Stores the fetched config and data in the cache and
                 * resolves the outer promise.
                 *
                 * @param {Object} data - Response body with 'config' and 'data'.
                 */
                data => {
                var endTime = performance.now();
                var loadTime = endTime - startTime;
                console.log('Chart data loaded in ' + loadTime.toFixed(2) + ' ms');
                
                chartConfig = data.config;
                allChartData = data.data;
                resolve(allChartData);
            })
            .catch(
                /**
                 * Rejects the promise when fetching the chart data failed.
                 *
                 * @param {*} error - The fetch/parse error.
                 */
                error => {
                var endTime = performance.now();
                var loadTime = endTime - startTime;
                console.error('Error loading chart data after ' + loadTime.toFixed(2) + ' ms:', error);
                reject(error);
            });
    });
}

// Reusable chart function - now uses shared data
/**
 * Renders one chart (and its legend) once the shared chart data and
 * Google Charts are available.
 *
 * @param {string} chartId - DOM id of the chart container.
 * @param {string} legendId - DOM id of the legend container.
 * @param {string} dataId - Key of the data set inside the shared chart data.
 * @param {string} prefix - Value prefix for tooltips (e.g. 'CHF').
 * @param {string} suffix - Value suffix for tooltips (e.g. 'kg').
 * @param {number} fractionDigits - Number of fraction digits for tooltips.
 * @returns {Promise} Resolves when the chart has been drawn.
 */
function createChart(chartId, legendId, dataId, prefix, suffix, fractionDigits) {
    return new Promise(
        /**
         * Promise executor that loads the shared chart data and triggers
         * drawing when ready.
         *
         * @param {Function} resolve - Promise resolve callback.
         * @param {Function} reject - Promise reject callback.
         */
        function(resolve, reject) {
        loadAllChartData()
            .then(
                /**
                 * Draws the chart and legend for the requested data set once
                 * Google Charts is ready.
                 *
                 * @param {Object} allData - The shared chart data map.
                 */
                allData => {
                if (!allData[dataId]) {
                    reject('Data not found for data id: ' + dataId);
                    return;
                }
                
                var data = allData[dataId];
                var headers = data[0].slice(1);

                // Wait for Google Charts to be ready
                /**
                 * Polls until Google Charts is ready, then draws the chart
                 * and legend and resolves the promise.
                 */
                function waitForGoogleCharts() {
                    if (chartConfig[dataId] && chartConfig[dataId].chartType === 'heatmap') {
                        drawHeatmap(chartId, data, headers);
                        drawHeatmapLegend(legendId);
                        resolve();
                        return;
                    }
                    if (window.googleChartsReady && google.visualization) {
                        if (chartConfig[dataId] && chartConfig[dataId].chartType === 'scatter') {
                            var correlation = drawWaxTemperatureChart(chartId, data, allData['totalWaxPerDayAndYearInKg'], headers);
                            drawWaxTemperatureLegend(legendId, headers, correlation);
                        }
                        else if (chartConfig[dataId] && chartConfig[dataId].chartType === 'line') {
                            drawTemperatureChart(chartId, data, headers);
                            drawTemperatureLegend(legendId, headers);
                        }
                        else {
                            drawChart(chartId, data, headers, prefix, suffix, fractionDigits);
                            drawLegendChart(legendId, headers);
                        }
                        resolve();
                    } else {
                        setTimeout(waitForGoogleCharts, 100);
                    }
                }
                waitForGoogleCharts();
            })
            .catch(
                /**
                 * Shows an error notification and rejects the promise when
                 * the chart data could not be loaded.
                 *
                 * @param {*} error - The loading error.
                 */
                error => {
                console.error('Error loading chart data:', error);
                firework.launch('Fehler beim Laden der Chart-Daten: ' + error, 'error', 5000);
                reject(error);
            });
    });
}

/**
 * Draws a stacked bar chart with Google Charts into the given container.
 *
 * @param {string} chartId - DOM id of the chart container.
 * @param {Array[]} data - Chart data rows (first element is the day label).
 * @param {string[]} headers - Column headers (two per year: lower and upper part).
 * @param {string} prefix - Value prefix for tooltips (e.g. 'CHF').
 * @param {string} suffix - Value suffix for tooltips (e.g. 'kg').
 * @param {number} fractionDigits - Number of fraction digits for tooltips.
 */
function drawChart(chartId, data, headers, prefix, suffix, fractionDigits) {
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
            headerRow.push(headers[i]);
            headerRow.push(headers[i + 1]);
            
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

// Heatmap
/**
 * Maps a booking count to a discrete heat category color:
 * 0 = empty, 1-3 = green, 4-6 = yellow, 7-9 = orange, 10+ = red.
 *
 * @param {number} value - Number of bookings in the slot.
 * @returns {string} CSS color, 'transparent' for empty slots.
 */
function heatColor(value) {
    if (value <= 0) return 'transparent';
    if (value <= 3) return 'hsl(120, 90%, 42%)';
    if (value <= 6) return 'hsl(50, 90%, 45%)';
    if (value <= 9) return 'hsl(25, 90%, 45%)';
    return 'hsl(0, 90%, 45%)';
}

// Opening hours per weekday as [firstSlot, endSlot) in 15-minute slots
var openingHours = {
    'So': [48, 74], // 12:00 - 18:30
    'Mo': null,
    'Di': null,
    'Mi': [56, 74], // 14:00 - 18:30
    'Do': null,
    'Fr': [72, 88], // 18:00 - 22:00
    'Sa': [48, 74]  // 12:00 - 18:30
};
var lastDayHours = [48, 62]; // Last event day: 12:00 - 15:30
var slotMinutes = ['00', '15', '30', '45'];

/**
 * Returns the opening hours [firstSlot, endSlot) for a weekday, or null
 * when closed. The last event day (a Sunday) has shorter hours.
 *
 * @param {string} weekday - German weekday label ('So', 'Mo', ...).
 * @param {boolean} isLastDay - True for the last event day.
 * @returns {number[]|null} Slot range or null when closed.
 */
function getOpeningHours(weekday, isLastDay) {
    return (isLastDay && weekday === 'So') ? lastDayHours : openingHours[weekday];
}

function getSlotLabel(s) {
    return Math.floor(s / 4) + ':' + slotMinutes[s % 4];
}

/**
 * Draws an HTML heatmap table: 15-minute slots on the Y axis (10:00 on
 * top to 23:45 at the bottom), one opening-hours column plus one column
 * per year grouped per event day on the X axis (like the bar charts).
 * Cells are colored by category: green (1-3) to red (10+ bookings),
 * blue marks the opening hours.
 *
 * @param {string} chartId - DOM id of the chart container.
 * @param {Array[]} data - Row 0 = ['', year, ...]; then one row per day:
 *        [dayLabel, slotCounts[96] per year, ...].
 * @param {string[]} headers - Year labels (data[0].slice(1)).
 */
function drawHeatmap(chartId, data, headers) {
    var startTime = performance.now();
    console.log('Starting to render heatmap: ' + chartId);

    // Fixed display range 10:00 - 24:00 in 15-minute slots (40 = 10:00, 95 = 23:45)
    var minSlot = 40;
    var maxSlot = 95;

    var html = '<table class="heatmap-table"><tr><th class="heatmap-axis"></th>';
    for (var row = 1; row < data.length; row++) {
        html += '<th colspan="' + (headers.length + 1) + '" class="heatmap-day">' + data[row][0] + '</th>';
    }
    html += '</tr><tr><th class="heatmap-axis"></th>';
    for (var row = 1; row < data.length; row++) {
        html += '<th class="heatmap-year heatmap-group-start" title="Öffnungszeiten"><div>Öffn.</div></th>';
        for (var i = 0; i < headers.length; i++) {
            html += '<th class="heatmap-year"><div>' + headers[i] + '</div></th>';
        }
    }
    html += '</tr>';

    for (var s = minSlot; s <= maxSlot; s++) {
        var slotLabel = getSlotLabel(s);
        var hourStart = (s % 4 === 0);
        html += '<tr><th class="heatmap-axis' + (hourStart ? ' heatmap-hour-start' : '') + '">' + slotLabel + '</th>';
        for (var row = 1; row < data.length; row++) {
            var weekday = data[row][0];
            var hours = getOpeningHours(weekday, row === data.length - 1);
            if (hours && s === hours[0]) {
                var openText = 'Öffnungszeiten: ' + getSlotLabel(hours[0]) + ' - ' + getSlotLabel(hours[1]);
                html += '<td class="heatmap-cell heatmap-group-start heatmap-open' + (hourStart ? ' heatmap-hour-start' : '') +
                    '" rowspan="' + (hours[1] - hours[0]) + '" title="' + weekday + ' - ' + openText + '"><div>Geöffnet</div></td>';
            } else if (!hours || s < hours[0] || s >= hours[1]) {
                html += '<td class="heatmap-cell heatmap-group-start' + (hourStart ? ' heatmap-hour-start' : '') +
                    '" style="background-color: transparent;" title="' + weekday + ' - geschlossen"></td>';
            }
            for (var i = 0; i < headers.length; i++) {
                var slotCounts = data[row][i + 1] || [];
                var value = slotCounts[s] || 0;
                var bookingsLabel = value + ' Buchung' + (value === 1 ? '' : 'en');
                html += '<td class="heatmap-cell' + (hourStart ? ' heatmap-hour-start' : '') +
                    '" style="background-color: ' + heatColor(value) + ';" title="' +
                    headers[i] + ', ' + data[row][0] + ', ' + slotLabel + ' - ' + bookingsLabel + '"></td>';
            }
        }
        html += '</tr>';
    }
    html += '</table>';

    document.getElementById(chartId).innerHTML = html;

    var endTime = performance.now();
    console.log('Heatmap ' + chartId + ' rendered in ' + (endTime - startTime).toFixed(2) + ' ms');
}

/**
 * Renders the heatmap legend (one colored box per booking-count
 * category) into the given element.
 *
 * @param {string} legendId - DOM id of the legend container.
 */
function drawHeatmapLegend(legendId) {
    var categories = [
        { color: heatColor(2), border: '1px solid #ccc', label: '1-3 Buchungen' },
        { color: heatColor(5), border: '1px solid #ccc', label: '4-6 Buchungen' },
        { color: heatColor(8), border: '1px solid #ccc', label: '7-9 Buchungen' },
        { color: heatColor(10), border: '1px solid #ccc', label: '10+ Buchungen' }
    ];

    var legendHTML = '<div style="display: flex; justify-content: center; align-items: center; gap: 20px; padding: 20px; margin-top: 0; padding-top: 0; background: transparent;">';
    for (var i = 0; i < categories.length; i++) {
        legendHTML += '<div style="display: flex; align-items: center; gap: 8px;">';
        legendHTML += '<div style="width: 20px; height: 20px; background-color: ' + categories[i].color + '; border: ' + categories[i].border + ';"></div>';
        legendHTML += '<span style="font-size: 14px; font-weight: bold;">' + categories[i].label + '</span>';
        legendHTML += '</div>';
    }
    legendHTML += '<span style="font-size: 13px; font-weight: bold; color: #555;">pro 15 Minuten</span>';
    legendHTML += '</div>';

    document.getElementById(legendId).innerHTML = legendHTML;
}

// Temperature line chart
/**
 * Draws a Google Charts line chart: X axis = event days (weekday labels
 * like the other charts), one line per year, Y = average temperature
 * during that day's opening hours.
 *
 * @param {string} chartId - DOM id of the chart container.
 * @param {Array[]} data - Row 0 = ['', year, ...]; then one row per day:
 *        [dayLabel, hourlyTemps[24] per year (or null), ...].
 * @param {string[]} headers - Year labels (data[0].slice(1)).
 */
function drawTemperatureChart(chartId, data, headers) {
    var startTime = performance.now();
    console.log('Starting to render temperature chart: ' + chartId);

    var chartData = [[''].concat(headers)];
    for (var row = 1; row < data.length; row++) {
        var weekday = data[row][0];
        var hours = getOpeningHours(weekday, row === data.length - 1);
        var dataRow = [weekday];
        for (var i = 0; i < headers.length; i++) {
            var temps = data[row][i + 1];
            if (!hours || !temps) {
                dataRow.push(null);
                continue;
            }
            var firstHour = Math.floor(hours[0] / 4);
            var lastHour = Math.floor((hours[1] - 1) / 4);
            var sum = 0;
            var count = 0;
            for (var h = firstHour; h <= lastHour; h++) {
                if (temps[h] !== null && temps[h] !== undefined) {
                    sum += temps[h];
                    count++;
                }
            }
            dataRow.push(count > 0 ? sum / count : null);
        }
        chartData.push(dataRow);
    }

    var dataTable = google.visualization.arrayToDataTable(chartData);

    // Same one-color-per-year palette as the other charts' legends
    var colors = [];
    for (var i = 0; i < headers.length; i++) {
        colors.push(seriesColors[i]);
    }

    var options = {
        backgroundColor: 'transparent',
        height: 460,
        width: 1730,
        chartArea: {
            left: 80,
            top: 20,
            width: '85%',
            height: '75%',
            backgroundColor: 'transparent',
        },
        hAxis: {
            textStyle: { fontSize: 16 },
            title: null,
        },
        vAxis: {
            title: '°C',
            titleTextStyle: { fontSize: 20, italic: false },
            textStyle: { fontSize: 16 },
        },
        interpolateNulls: true,
        colors: colors,
        pointSize: 5,
        lineWidth: 3,
        legend: {
            position: 'none'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById(chartId));
    chart.draw(dataTable, options);

    var loadingElement = document.getElementById('loading_' + chartId);
    if (loadingElement) {
        loadingElement.style.display = 'none';
    }

    var endTime = performance.now();
    console.log('Temperature chart ' + chartId + ' rendered in ' + (endTime - startTime).toFixed(2) + ' ms');
}

/**
 * Renders the temperature chart legend (one color box per year) into the
 * given element.
 *
 * @param {string} legendId - DOM id of the legend container.
 * @param {string[]} headers - Year labels.
 */
function drawTemperatureLegend(legendId, headers) {
    var legendHTML = '<div style="display: flex; justify-content: center; align-items: center; gap: 20px; padding: 20px; margin-top: 0; padding-top: 0; background: transparent;">';
    for (var i = 0; i < headers.length; i++) {
        legendHTML += '<div style="display: flex; align-items: center; gap: 8px;">';
        legendHTML += '<div style="width: 20px; height: 20px; background-color: ' + seriesColors[i] + '; border: 1px solid #ccc;"></div>';
        legendHTML += '<span style="font-size: 14px; font-weight: bold;">' + headers[i] + '</span>';
        legendHTML += '</div>';
    }
    legendHTML += '</div>';

    document.getElementById(legendId).innerHTML = legendHTML;
}

// Wax vs temperature scatter chart
/**
 * Draws a scatter chart correlating total wax amount per year with the
 * average temperature during the event's opening hours that year. One
 * point per year, plus a linear trendline.
 *
 * @param {string} chartId - DOM id of the chart container.
 * @param {Array[]} tempData - Row 0 = ['', year, ...]; then one row per
 *        day: [dayLabel, hourlyTemps[24] per year (or null), ...].
 * @param {Array[]} waxData - Row 0 = ['', 'year: lower', 'year: upper',
 *        ...]; then one row per day: [dayLabel, lowerKg, upperKg per year].
 * @param {string[]} headers - Year labels (tempData[0].slice(1)).
 * @returns {number|null} Pearson correlation coefficient of all points.
 */
function drawWaxTemperatureChart(chartId, tempData, waxData, headers) {
    var startTime = performance.now();
    console.log('Starting to render wax/temperature chart: ' + chartId);

    // Accumulate per year: mean temperature over all opening hours of the
    // event, and total wax amount over all event days.
    var tempSum = headers.map(function() { return 0; });
    var tempCount = headers.map(function() { return 0; });
    var waxSum = headers.map(function() { return 0; });
    for (var row = 1; row < tempData.length; row++) {
        var weekday = tempData[row][0];
        var hours = getOpeningHours(weekday, row === tempData.length - 1);
        if (!hours) {
            continue;
        }
        var firstHour = Math.floor(hours[0] / 4);
        var lastHour = Math.floor((hours[1] - 1) / 4);
        for (var i = 0; i < headers.length; i++) {
            var temps = tempData[row][i + 1];
            var waxRow = waxData[row] || [];
            waxSum[i] += (parseFloat(waxRow[i * 2 + 1]) || 0) + (parseFloat(waxRow[i * 2 + 2]) || 0);
            if (!temps) {
                continue;
            }
            for (var h = firstHour; h <= lastHour; h++) {
                if (temps[h] !== null && temps[h] !== undefined) {
                    tempSum[i] += temps[h];
                    tempCount[i]++;
                }
            }
        }
    }

    var points = [];
    for (var i = 0; i < headers.length; i++) {
        if (tempCount[i] === 0 || waxSum[i] <= 0) {
            continue;
        }
        var meanTemp = tempSum[i] / tempCount[i];
        points.push([
            meanTemp,
            waxSum[i],
            'point { fill-color: ' + seriesColors[i] + ' }',
            headers[i] + ': ' + meanTemp.toFixed(1) + ' °C, ' + waxSum[i].toFixed(1) + ' kg'
        ]);
    }

    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn('number', 'Temperatur');
    dataTable.addColumn('number', 'Wachsmenge');
    dataTable.addColumn({ type: 'string', role: 'style' });
    dataTable.addColumn({ type: 'string', role: 'tooltip' });
    dataTable.addRows(points);

    // Pearson correlation over all points
    var n = points.length;
    var correlation = null;
    if (n > 1) {
        var sumX = 0, sumY = 0, sumXY = 0, sumX2 = 0, sumY2 = 0;
        for (var i = 0; i < n; i++) {
            var x = points[i][0];
            var y = points[i][1];
            sumX += x; sumY += y; sumXY += x * y; sumX2 += x * x; sumY2 += y * y;
        }
        var denom = Math.sqrt((n * sumX2 - sumX * sumX) * (n * sumY2 - sumY * sumY));
        if (denom > 0) {
            correlation = (n * sumXY - sumX * sumY) / denom;
        }
    }

    var options = {
        backgroundColor: 'transparent',
        height: 460,
        width: 1730,
        chartArea: {
            left: 80,
            top: 20,
            width: '85%',
            height: '75%',
            backgroundColor: 'transparent',
        },
        hAxis: {
            title: 'Temperatur (°C)',
            titleTextStyle: { fontSize: 20, italic: false },
            textStyle: { fontSize: 16 },
        },
        vAxis: {
            title: 'Wachsmenge (kg)',
            titleTextStyle: { fontSize: 20, italic: false },
            textStyle: { fontSize: 16 },
            viewWindow: { min: 0 },
        },
        pointSize: 7,
        trendlines: {
            0: { type: 'linear', color: '#333', lineWidth: 2, opacity: 0.6 }
        },
        legend: {
            position: 'none'
        }
    };

    var chart = new google.visualization.ScatterChart(document.getElementById(chartId));
    chart.draw(dataTable, options);

    var loadingElement = document.getElementById('loading_' + chartId);
    if (loadingElement) {
        loadingElement.style.display = 'none';
    }

    var endTime = performance.now();
    console.log('Wax/temperature chart ' + chartId + ' rendered in ' + (endTime - startTime).toFixed(2) + ' ms');
    return correlation;
}

/**
 * Renders the wax/temperature chart legend (one color box per year plus
 * the correlation coefficient) into the given element.
 *
 * @param {string} legendId - DOM id of the legend container.
 * @param {string[]} headers - Year labels.
 * @param {number|null} correlation - Pearson r of all points.
 */
function drawWaxTemperatureLegend(legendId, headers, correlation) {
    var legendHTML = '<div style="display: flex; justify-content: center; align-items: center; gap: 20px; padding: 20px; margin-top: 0; padding-top: 0; background: transparent;">';
    for (var i = 0; i < headers.length; i++) {
        legendHTML += '<div style="display: flex; align-items: center; gap: 8px;">';
        legendHTML += '<div style="width: 20px; height: 20px; background-color: ' + seriesColors[i] + '; border: 1px solid #ccc;"></div>';
        legendHTML += '<span style="font-size: 14px; font-weight: bold;">' + headers[i] + '</span>';
        legendHTML += '</div>';
    }
    if (correlation !== null) {
        legendHTML += '<span style="font-size: 14px; font-weight: bold; margin-left: 20px;">Korrelation r = ' + correlation.toFixed(2) + '</span>';
    }
    legendHTML += '</div>';

    document.getElementById(legendId).innerHTML = legendHTML;
}

// HTML Legend
/**
 * Renders an HTML legend (one color box per year, plus a brightened box
 * for stacked second series) into the given element.
 *
 * @param {string} legendId - DOM id of the legend container.
 * @param {string[]} headers - Column headers (two per year).
 */
function drawLegendChart(legendId, headers) {
        var legendHTML = '<div style="display: flex; justify-content: center; align-items: center; gap: 20px; padding: 20px; margin-top: 0; padding-top: 0; background: transparent;">';
        
        for (var i = 0; i < headers.length; i += 2) {
            var yearName = headers[i].split(':')[0];
            var yearIndex = Math.floor(i / 2);
            var darkColor = seriesColors[yearIndex];
            var hasSecondSeries = headers[i].indexOf(':') !== -1;
            
            legendHTML += '<div style="display: flex; align-items: center; gap: 8px;">';
            legendHTML += '<div style="display: flex; gap: 2px;">';
            legendHTML += '<div style="width: 20px; height: 20px; background-color: ' + darkColor + '; border: 1px solid #ccc;"></div>';
            if (hasSecondSeries) {
                var lightColor = generateBrightColor(darkColor);
                legendHTML += '<div style="width: 20px; height: 20px; background-color: ' + lightColor + '; border: 1px solid #ccc;"></div>';
            }
            legendHTML += '</div>';
            legendHTML += '<span style="font-size: 14px; font-weight: bold;">' + yearName + '</span>';
            legendHTML += '</div>';
        }
        
        legendHTML += '</div>';
        
        document.getElementById(legendId).innerHTML = legendHTML;
    }

// Create container immediately (without data loading)
/**
 * Creates the DOM container (background image, chart div with loading
 * indicator and legend div) for a diagram.
 *
 * @param {string} dataId - Key of the data set / DOM id of the outer container.
 * @returns {Object|null} Info object with chartId, legendId, dataId, prefix,
 *          suffix and fractionDigits, or null on failure.
 */
function createDiagramContainer(dataId) {
    var config = chartConfig ? chartConfig[dataId] : null;
    if (!config) {
        console.error('Chart config not found for: ' + dataId);
        return null;
    }

    var chartId = 'chart_' + dataId;
    var legendId = 'legend_' + dataId;

    var container = document.getElementById(dataId);
    if (!container) {
        console.error('Container not found: ' + dataId);
        return null;
    }

    if (config.chartType === 'heatmap' || config.chartType === 'line' || config.chartType === 'scatter') {
        var heatmapHtml = '<div style="margin: 0; padding: 0;">';
        heatmapHtml += '<div id="' + chartId + '" style="position: relative; min-height: 150px; margin: 0; padding: 10px 0 0 0;">';
        heatmapHtml += '<div id="loading_' + chartId + '" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255, 255, 255, 0.9); padding: 20px; border-radius: 8px; text-align: center; font-size: 16px; font-weight: bold; color: #333; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">Lade Diagramm...</div>';
        heatmapHtml += '</div>';
        heatmapHtml += '</div>';
        heatmapHtml += '<div id="' + legendId + '" style="max-width: 1400px; margin: 0; margin-top: 10px; height: 60px;"></div>';

        container.innerHTML = heatmapHtml;

        return { chartId: chartId, legendId: legendId, dataId: dataId, chartType: config.chartType };
    }

    // Show chart container with background image and loading indicator immediately
    // Adjust padding-left to the container to make sure all X axis tick labels have the same width (so it matches the background image position)
    // And compensate the width with it
    var legendOverlay = config.legendImage
        ? '<img src="images/' + config.legendImage + '" style="position: absolute; top: 50px; left: 150px; z-index: 1; pointer-events: none;" alt="">'
        : '';
    // Wrapper width = 46px bg offset + 1684px bg image width; overflow-x hidden hides the
    // transparent tail of the Google chart that extends beyond the last band.
    var chartHtml = '<div style="width: 1730px; height: 470px; margin: 0; padding: 0; overflow-x: hidden; background-image: url(images/' + config.bgImage + '); background-repeat: no-repeat; background-position: 46px -140px; position: relative;">';
    chartHtml += legendOverlay;
    chartHtml += '<div id="' + chartId + '" style="position: relative; z-index: 2; width: ' + (1822 - config.paddingLeft) + 'px; margin: 0; padding: 0 0 0 ' + config.paddingLeft + 'px; height: 100%;">';
    chartHtml += '<div id="loading_' + chartId + '" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255, 255, 255, 0.9); padding: 20px; border-radius: 8px; text-align: center; font-size: 16px; font-weight: bold; color: #333; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">Lade Diagramm...</div>';
    chartHtml += '</div>';
    chartHtml += '</div>';
    chartHtml += '<div id="' + legendId + '" style="max-width: 1400px; margin: 0; margin-top: 10px; height: 100px;"></div>';
    
    container.innerHTML = chartHtml;
    
    return { chartId: chartId, legendId: legendId, dataId: dataId, prefix: config.prefix, suffix: config.suffix, fractionDigits: config.fractionDigits };
}

// Load data into existing container
/**
 * Loads data into an existing diagram container by rendering the chart.
 *
 * @param {Object} containerInfo - Info object as returned by createDiagramContainer().
 * @returns {Promise} Resolves when the chart has been drawn.
 */
function loadDiagramData(containerInfo) {
    if (!containerInfo) return Promise.reject('Invalid container info');
    
    // Start loading data in background using shared data
    return createChart(containerInfo.chartId, containerInfo.legendId, containerInfo.dataId, containerInfo.prefix, containerInfo.suffix, containerInfo.fractionDigits);
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
/**
 * Generates a brightened variant of a hex color (used for the second,
 * stacked series in the legend).
 *
 * @param {string} hexColor - Color in '#rrggbb' format.
 * @returns {string} The brightened color in '#rrggbb' format.
 */
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
    
    /* The page itself never scrolls sideways - instead each diagram container
       scrolls the fixed-width chart horizontally when the viewport is too narrow. */
    #body > div[id] {
        overflow-x: auto;
    }
    
    /* Google Charts specific overrides */
    .google-visualization-chart {
        margin: 0 !important;
        padding: 0 !important;
        margin-left: 5px !important; /* Force left margin */
    }
    
    /* Google Charts sometimes reserves too little space for the Y axis labels (e.g. "16K"), let them overflow into the padding */
    [id^="chart_"] svg {
        overflow: visible;
    }

    /* Hide chart overflow */
    #chart {
        overflow-x: hidden !important;
        margin-left: 5px !important; /* Force left margin */
    }

    /* Heatmap table */
    .heatmap-table {
        border-collapse: collapse;
    }
    .heatmap-table th {
        font-weight: normal;
        padding: 0 2px;
    }
    .heatmap-table .heatmap-day {
        font-size: 13px;
        border-left: 2px solid #999;
        border-right: 2px solid #999;
        border-bottom: 1px solid #999;
        text-align: center;
    }
    .heatmap-table .heatmap-year {
        height: 52px;
        vertical-align: bottom;
        text-align: center;
        padding: 0 !important;
    }
    .heatmap-table .heatmap-year div {
        writing-mode: vertical-rl;
        font-size: 9px;
        height: 48px;
        margin: 0 auto;
    }
    .heatmap-table .heatmap-axis {
        font-size: 11px;
        text-align: right;
        padding: 0 5px 0 0 !important;
        white-space: nowrap;
        width: 42px;
    }
    .heatmap-table .heatmap-cell {
        width: 11px;
        min-width: 11px;
        max-width: 11px;
        height: 12px;
        padding: 0;
    }
    .heatmap-table .heatmap-group-start {
        border-left: 2px solid #999;
    }
    .heatmap-table .heatmap-hour-start {
        border-top: 1px solid #ccc;
    }
    .heatmap-table .heatmap-open {
        background-color: hsl(210, 90%, 45%);
        text-align: center;
        vertical-align: middle;
    }
    .heatmap-table .heatmap-open div {
        writing-mode: vertical-rl;
        font-size: 10px;
        font-weight: bold;
        color: #fff;
        letter-spacing: 1px;
        margin: 0 auto;
    }
</style>

<div id="body" style="margin: 0; padding: 0;">
<h1 style="margin: 0; padding: 0;">Umsatz und Wachs pro Tag und Jahr</h1>
<div style="display: flex; align-items: flex-start; gap: 20px 60px; margin-bottom: 20px;">
	<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; backdrop-filter: blur(5px); flex: 0 0 auto;">
		<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 1); font-size: 16px; font-weight: 600;">Übersicht:</h4>
		<ul style="margin: 0; padding-left: 20px;">
			<li><a href=#Wax+Gastro_Currency>Umsatz pro Tag (Wachs, zugekaufte Artikel + Gastronomie)</a><br><br></li>
			<li><a href=#Wax+Gastro_Currency_summed>Umsatz aufsummiert (Wachs, zugekaufte Artikel + Gastronomie)</a><br><br></li>
			<li><a href=#Wax_Currency>Umsatz Wachs + zugekaufte Artikel</a><br><br></li>
			<li><a href=#Wax_Currency_summed>Umsatz Wachs + zugekaufte Artikel aufsummiert</a><br><br></li>
			<li><a href=#Gastro_Currency>Umsatz Gastronomie</a><br><br></li>
			<li><a href=#Gastro_Currency_summed>Umsatz Gastronomie aufsummiert</a><br><br></li>
			<li><a href=#Wax_amount>Wachsmenge</a><br><br></li>
			<li><a href=#WaxAmountSummed>Wachsmenge aufsummiert</a><br><br></li>
			<li><a href=#bookings>Buchungen pro Tag</a><br><br></li>
			<li><a href=#bookings_summed>Buchungen aufsummiert</a><br><br></li>
			<li><a href=#bookings_heatmap>Buchungen pro 15 Minuten (nur Wachs &amp; Artikel)</a><br><br></li>
			<li><a href=#temperature>Durchschnittliche Temperatur während Öffnungszeiten</a><br><br></li>
			<li><a href=#wax_temperature>Wachsmenge vs. Temperatur</a></li>
		</ul>
	</div>
	
	<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; backdrop-filter: blur(5px); flex: 0 0 auto; margin-left: auto;">
		<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 1); font-size: 16px; font-weight: 600;">Hinweise:</h4>
		<ul style="margin: 0; padding-left: 20px; color: rgba(73, 80, 87, 1); font-size: 16px;">
			<li style="margin-bottom: 10px;">2020 konnte das Kerzenziehen wegen COVID-19 nicht öffentlich durchgeführt werden.</li>
			<li style="margin-bottom: 10px;">Wachspreise:
				<table style="margin-top: 8px; border-collapse: collapse; width: 100%; font-size: 15px;">
					<thead>
						<tr style="border-bottom: 1px solid rgba(73, 80, 87, 0.3);">
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 1); font-weight: 600;">Jahr</th>
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 1); font-weight: 600;">Bienenwachs</th>
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 1); font-weight: 600;">Parafinwachs</th>
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
            <li><a href="statsDiagrams-old.php">Alte Diagram-Seite</a></li>
		</ul>
	</div>
</div>
<hr>

<a name=Wax+Gastro_Currency></a><h2>Umsatz pro Tag (Wachs, zugekaufte Artikel  + Gastronomie, in CHF)</span></h2><div id="totalPerDayAndYear"></div><hr>
<a name=Wax+Gastro_Currency_summed></a><h2>Umsatz aufsummiert (Wachs, zugekaufte Artikel + Gastronomie, in CHF)</span></h2><div id="totalPerDayAndYearSummed"></div><hr>
<a name=Wax_Currency></a><h2>Umsatz Wachs + zugekaufte Artikel (in CHF)</span></h2><div id="totalWaxPerDayAndYear"></div><hr>
<a name=Wax_Currency_summed></a><h2>Umsatz Wachs + zugekaufte Artikel aufsummiert (in CHF)</span></h2><div id="totalWaxPerDayAndYearSummed"></div><hr>
<a name=Gastro_Currency></a><h2>Umsatz Gastronomie (in CHF)</span></h2><div id="totalFoodPerDayAndYear"></div><hr>
<a name=Gastro_Currency_summed></a><h2>Umsatz Gastronomie aufsummiert (in CHF)</span></h2><div id="totalFoodPerDayAndYearSummed"></div><hr>
<a name=Wax_amount></a><h2>Wachsmenge (in kg)</span></h2><div id="totalWaxPerDayAndYearInKg"></div><hr>
<a name=WaxAmountSummed></a><h2>Wachsmenge aufsummiert (in kg)</span></h2><div id="totalWaxPerDayAndYearInKgSummed"></div><hr>
<a name=bookings></a><h2>Buchungen pro Tag</h2><div id="bookingsPerDayAndYear"></div><hr>
<a name=bookings_summed></a><h2>Buchungen aufsummiert</h2><div id="bookingsPerDayAndYearSummed"></div><hr>
<a name=bookings_heatmap></a><h2>Buchungen pro 15 Minuten (nur Wachs &amp; Artikel, ohne Gastronomie/Spenden)</h2><div id="bookingsPerHour"></div><hr>
<a name=temperature></a><h2>Durchschnittliche Temperatur während Öffnungszeiten</h2><div id="temperaturePerDayAndYear"></div><hr>
<a name=wax_temperature></a><h2>Wachsmenge vs. Temperatur pro Jahr</h2><div id="waxVsTemperature"></div>

<script>
// Create all containers immediately, then load data once and render all charts
/**
 * Loads the shared chart data, creates all ten diagram containers and
 * renders all charts in parallel.
 */
function loadAllDiagrams() {
    var totalStartTime = performance.now();
    console.log('=== DIAGRAM LOADING START ===');
    console.log('Starting to load all diagrams...');

    // Load config+data first, then create containers and render charts
    loadAllChartData()
        .then(
            /**
             * Creates all diagram containers and starts rendering all
             * charts in parallel once the shared data is available.
             */
            function() {
            // Create all containers first (immediate display)
            var commonContainer = createDiagramContainer("totalPerDayAndYear");
            var commonSummedContainer = createDiagramContainer("totalPerDayAndYearSummed");
            var waxContainer = createDiagramContainer("totalWaxPerDayAndYear");
            var waxSummedContainer = createDiagramContainer("totalWaxPerDayAndYearSummed");
            var foodContainer = createDiagramContainer("totalFoodPerDayAndYear");
            var foodSummedContainer = createDiagramContainer("totalFoodPerDayAndYearSummed");
            var waxAmountContainer = createDiagramContainer("totalWaxPerDayAndYearInKg");
            var waxAmountSummedContainer = createDiagramContainer("totalWaxPerDayAndYearInKgSummed");
            var bookingsContainer = createDiagramContainer("bookingsPerDayAndYear");
            var bookingsSummedContainer = createDiagramContainer("bookingsPerDayAndYearSummed");
            var bookingsHeatmapContainer = createDiagramContainer("bookingsPerHour");
            var temperatureContainer = createDiagramContainer("temperaturePerDayAndYear");
            var waxTemperatureContainer = createDiagramContainer("waxVsTemperature");

            var containers = [commonContainer, commonSummedContainer, waxContainer, waxSummedContainer, foodContainer, foodSummedContainer, waxAmountContainer, waxAmountSummedContainer, bookingsContainer, bookingsSummedContainer, bookingsHeatmapContainer, temperatureContainer, waxTemperatureContainer];

            var dataLoadTime = performance.now() - totalStartTime;
            console.log('✓ Data loading completed in ' + dataLoadTime.toFixed(2) + ' ms, starting chart rendering...');

            // Render all charts simultaneously
            var promises = containers.map(
                /**
                 * Starts loading/rendering a single diagram container.
                 *
                 * @param {Object} container - Info object from createDiagramContainer().
                 * @returns {Promise} The rendering promise of the diagram.
                 */
                function(container) {
                return loadDiagramData(container);
            });

            Promise.all(promises)
                .then(
                    /** Logs the total rendering time once all charts finished. */
                    function() {
                    var totalTime = performance.now() - totalStartTime;
                    console.log('✓ All charts rendered successfully');
                    console.log('=== DIAGRAM LOADING COMPLETE ===');
                    console.log('Total time: ' + totalTime.toFixed(2) + ' ms');
                    console.log('Charts rendered: ' + containers.length);
                })
                .catch(
                    /**
                     * Shows an error notification when chart rendering failed.
                     *
                     * @param {*} error - The rendering error.
                     */
                    function(error) {
                    var totalTime = performance.now() - totalStartTime;
                    console.error('✗ Error rendering diagrams after ' + totalTime.toFixed(2) + ' ms:', error);
                    firework.launch('Fehler beim Rendern der Diagramme: ' + error, 'error', 5000);
                });
        })
        .catch(
            /**
             * Shows an error notification when loading the diagram data failed.
             *
             * @param {*} error - The loading error.
             */
            function(error) {
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
    
