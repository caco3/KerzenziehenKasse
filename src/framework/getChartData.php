<?php
header('Content-Type: application/json');

// Start timing for getChartData.php
$startTime = microtime(true);
error_log("getChartData.php starting execution...");

$root="..";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config_generic.php");
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

// Connect to database
db_connect();

// Include the data provider
include "$root/framework/statsDataProvider.php";
include_once __DIR__ . "/fetchTemperatureData.php";

// Get the stats data
$statsData = getStatsData();
$temperatureData = getTemperatureData();

// Map chart types to column names
$chartConfig = [
    'totalPerDayAndYear' => [
        'lowerName' => 'Öffentlich',
        'upperName' => 'Schule/Gruppe',
        'prefix' => 'CHF',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg.png',
        'legendImage' => 'legende-schule-oeffentlich.png',
        'paddingLeft' => 0
    ],
    'totalPerDayAndYearSummed' => [
        'lowerName' => 'Öffentlich',
        'upperName' => 'Schule/Gruppe',
        'prefix' => 'CHF',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg.png',
        'legendImage' => 'legende-schule-oeffentlich.png',
        'paddingLeft' => 5
    ],
    'totalWaxPerDayAndYear' => [
        'lowerName' => 'Öffentlich',
        'upperName' => 'Schule/Gruppe',
        'prefix' => 'CHF',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg.png',
        'legendImage' => 'legende-schule-oeffentlich.png',
        'paddingLeft' => 0
    ],
    'totalWaxPerDayAndYearSummed' => [
        'lowerName' => 'Öffentlich',
        'upperName' => 'Schule/Gruppe',
        'prefix' => 'CHF',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg.png',
        'legendImage' => 'legende-schule-oeffentlich.png',
        'paddingLeft' => 15
    ],
    'totalFoodPerDayAndYear' => [
        'lowerName' => 'Öffentlich',
        'upperName' => 'Schule/Gruppe',
        'prefix' => 'CHF ',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg.png',
        'legendImage' => 'legende-schule-oeffentlich.png',
        'paddingLeft' => 7
    ],
    'totalFoodPerDayAndYearSummed' => [
        'lowerName' => 'Öffentlich',
        'upperName' => 'Schule/Gruppe',
        'prefix' => 'CHF ',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg.png',
        'legendImage' => 'legende-schule-oeffentlich.png',
        'paddingLeft' => 1
    ],
    'totalWaxPerDayAndYearInKg' => [
        'lowerName' => 'Parafinwachs',
        'upperName' => 'Bienenwachs',
        'prefix' => '',
        'suffix' => 'kg',
        'fractionDigits' => 1,
        'bgImage' => 'chart-bg.png',
        'legendImage' => 'legende-bienenwachs-parafin.png',
        'paddingLeft' => 17
    ],
    'totalWaxPerDayAndYearInKgSummed' => [
        'lowerName' => 'Parafinwachs',
        'upperName' => 'Bienenwachs',
        'prefix' => '',
        'suffix' => 'kg',
        'fractionDigits' => 1,
        'bgImage' => 'chart-bg.png',
        'legendImage' => 'legende-bienenwachs-parafin.png',
        'paddingLeft' => 7
    ],
    'bookingsPerDayAndYear' => [
        'lowerName' => '',
        'upperName' => '',
        'prefix' => '',
        'suffix' => '',
        'fractionDigits' => 0,
        'bgImage' => 'chart-bg.png',
        'paddingLeft' => 10
    ],
    'bookingsPerDayAndYearSummed' => [
        'lowerName' => '',
        'upperName' => '',
        'prefix' => '',
        'suffix' => '',
        'fractionDigits' => 0,
        'bgImage' => 'chart-bg.png',
        'paddingLeft' => 10
    ],
    'bookingsPerHour' => [
        'chartType' => 'heatmap',
        'lowerName' => '',
        'upperName' => '',
        'prefix' => '',
        'suffix' => '',
        'fractionDigits' => 0,
        'bgImage' => '',
        'paddingLeft' => 0
    ],
    'temperaturePerDayAndYear' => [
        'chartType' => 'line',
        'lowerName' => '',
        'upperName' => '',
        'prefix' => '',
        'suffix' => '°C',
        'fractionDigits' => 1,
        'bgImage' => '',
        'paddingLeft' => 0
    ],
    'waxVsTemperature' => [
        'chartType' => 'scatter',
        'lowerName' => '',
        'upperName' => '',
        'prefix' => '',
        'suffix' => '°C',
        'fractionDigits' => 1,
        'bgImage' => '',
        'paddingLeft' => 0
    ]
];

// Return all formatted data
$allFormattedData = [];

// Get all chart types and format their data
$allChartTypes = array_keys($chartConfig);
foreach ($allChartTypes as $type) {
    // Process this chart type
    $config = $chartConfig[$type];
    $data = $statsData[$type] ?? null;
    
    // Format the data for this chart type
    $formattedData = [];
    
    // Create headers
    $headers = [''];
    $yearsCovered = date("Y") - 2017 + 1;
    $years = array();
    for($i = $yearsCovered; $i > 0; $i--) {
        $year = date("Y") - $i + 1; 
        
        if ($year == 2020) {
            continue;
        }					
        array_push($years, $year);
    }				

    if (($config['chartType'] ?? '') === 'heatmap') {
        // 2017 only has one aggregated booking per day at 00:00, exclude it
        $heatmapYears = array();
        foreach($years as $year) {
            if ($year == 2017) {
                continue;
            }
            $heatmapYears[] = $year;
        }

        foreach($heatmapYears as $year) {
            $headers[] = (string)$year;
        }
        $formattedData[] = $headers;

        foreach($data as $day => $dataOfDay) {
            if ((!array_key_exists("formatedDate", $dataOfDay)) or ($dataOfDay['formatedDate'] == "")) {
                continue;
            }

            $row = [$dataOfDay['formatedDate']];
            foreach($heatmapYears as $year) {
                $row[] = $dataOfDay['year'][$year] ?? array_fill(0, 96, 0);
            }
            $formattedData[] = $row;
        }

        $allFormattedData[$type] = $formattedData;
        continue;
    }

    if (in_array($config['chartType'] ?? '', ['line', 'scatter'])) {
        foreach($years as $year) {
            $headers[] = (string)$year;
        }
        $formattedData[] = $headers;

        // Reuse the bookingsPerHour day labels; one 24h temperature array per year
        foreach($statsData['bookingsPerHour'] as $day => $dataOfDay) {
            if ((!array_key_exists("formatedDate", $dataOfDay)) or ($dataOfDay['formatedDate'] == "")) {
                continue;
            }

            $row = [$dataOfDay['formatedDate']];
            foreach($years as $year) {
                $row[] = $temperatureData[$year][$day] ?? null;
            }
            $formattedData[] = $row;
        }

        $allFormattedData[$type] = $formattedData;
        continue;
    }

    foreach($years as $year) {
        $lowerName = isset($config['lowerName']) ? trim($config['lowerName']) : '';
        $lowerName = trim(ltrim($lowerName, ':'));
        $upperName = isset($config['upperName']) ? trim($config['upperName']) : '';
        $upperName = trim(ltrim($upperName, ':'));

        $headers[] = ($lowerName !== '') ? ($year . ': ' . $lowerName) : (string)$year;
        $headers[] = ($upperName !== '') ? ($year . ': ' . $upperName) : (string)$year;
    }

    $formattedData[] = $headers;

    // Add data rows
    foreach($data as $day => $dataOfDay) {
        if(count($dataOfDay) == 0) {
            continue;
        }

        if ((!array_key_exists("formatedDate", $dataOfDay)) or ($dataOfDay['formatedDate'] == "")) {
            continue;
        }
        
        $row = [$dataOfDay['formatedDate']];
        
        foreach($years as $year) {
            if (is_array($dataOfDay['year']) and array_key_exists($year, $dataOfDay['year'])) {
                $row[] = $dataOfDay['year'][$year]['lowerPart'];
                $row[] = $dataOfDay['year'][$year]['upperPart'];
            }
            else {
                $row[] = 0;
                $row[] = 0;
            }
        }
        
        $formattedData[] = $row;
    }
    
    $allFormattedData[$type] = $formattedData;
}

// Calculate and log total execution time
$endTime = microtime(true);
$totalTime = ($endTime - $startTime) * 1000;
error_log("getChartData.php completed in " . number_format($totalTime, 2) . " ms");

echo json_encode([
    'config' => $chartConfig,
    'data' => $allFormattedData
]);
exit;
?>
