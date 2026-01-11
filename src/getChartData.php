<?php
header('Content-Type: application/json');

// Start timing for getChartData.php
$startTime = microtime(true);
error_log("getChartData.php starting execution...");

$root=".";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config_generic.php");
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

// Connect to database
db_connect();

// Include the data provider
include "$root/framework/statsDataProvider.php";

// Get the stats data
$statsData = getStatsData();

// Map chart types to column names
$chartConfig = [
    'totalPerDayAndYear' => [
        'lowerName' => 'Öffentlich',
        'upperName' => 'Schule',
        'prefix' => 'CHF',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg-public-school.png',
        'paddingLeft' => 0
    ],
    'totalPerDayAndYearSummed' => [
        'lowerName' => '',
        'upperName' => '',
        'prefix' => 'CHF',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg.png',
        'paddingLeft' => 5
    ],
    'totalWaxPerDayAndYear' => [
        'lowerName' => 'Öffentlich',
        'upperName' => 'Schule',
        'prefix' => 'CHF',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg-public-school.png',
        'paddingLeft' => 0
    ],
    'totalFoodPerDayAndYear' => [
        'lowerName' => '',
        'upperName' => '',
        'prefix' => 'CHF ',
        'suffix' => '',
        'fractionDigits' => 2,
        'bgImage' => 'chart-bg.png',
        'paddingLeft' => 7
    ],
    'totalWaxPerDayAndYearInKg' => [
        'lowerName' => 'Parafinwachs',
        'upperName' => 'Bienenwachs',
        'prefix' => '',
        'suffix' => 'kg',
        'fractionDigits' => 1,
        'bgImage' => 'chart-bg-bee-parafin.png',
        'paddingLeft' => 17
    ],
    'totalWaxPerDayAndYearInKgSummed' => [
        'lowerName' => 'Parafinwachs',
        'upperName' => 'Bienenwachs',
        'prefix' => '',
        'suffix' => 'kg',
        'fractionDigits' => 1,
        'bgImage' => 'chart-bg-bee-parafin.png',
        'paddingLeft' => 7
    ]
];

// Return all formatted data
$allFormattedData = [];

// Get all chart types and format their data
$allChartTypes = array_keys($chartConfig);
foreach ($allChartTypes as $type) {
    // Process this chart type
    $config = $chartConfig[$type];
    $data = $statsData[$type];
    
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
