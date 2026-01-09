<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output

// Capture any warnings/errors
ob_start();

$root=".";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config_generic.php");
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

// Connect to database
db_connect();

// Include the data provider
include "statsDataProvider.php";

// Get the stats data
$statsData = getStatsData();

// Get the chart type from the URL parameter
$chartType = $_GET['type'] ?? '';

// Map chart types to column names
$chartConfig = [
    'totalPerDayAndYear' => [
        'lowerName' => ': Öffentlich',
        'upperName' => ': Schule/Geschlossene Gesellschaft/Private Gruppe'
    ],
    'totalPerDayAndYearSummed' => [
        'lowerName' => ': Öffentlich',
        'upperName' => ': Schule/Geschlossene Gesellschaft/Private Gruppe'
    ],
    'totalWaxPerDayAndYear' => [
        'lowerName' => ': Öffentlich',
        'upperName' => ': Schule/Geschlossene Gesellschaft/Private Gruppe'
    ],
    'totalFoodPerDayAndYear' => [
        'lowerName' => ': Öffentlich',
        'upperName' => ': Schule/Geschlossene Gesellschaft/Private Gruppe'
    ],
    'totalWaxPerDayAndYearInKg' => [
        'lowerName' => ': Parafinwachs',
        'upperName' => ': Bienenwachs'
    ],
    'totalWaxPerDayAndYearInKgSummed' => [
        'lowerName' => ': Parafinwachs',
        'upperName' => ': Bienenwachs'
    ]
];

// Special case: return all formatted data at once
if ($chartType === 'all') {
    $allFormattedData = [];
    
    // Get all chart types and format their data
    $allChartTypes = array_keys($chartConfig);
    foreach ($allChartTypes as $type) {
        // Temporarily set chart type for processing
        $tempChartType = $chartType;
        $chartType = $type;
        
        // Process this chart type
        $config = $chartConfig[$chartType];
        $data = $statsData[$chartType];
        
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
            $headers[] = $year . $config['lowerName'];
            $headers[] = $year . $config['upperName'];
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
        
        // Restore original chart type
        $chartType = $tempChartType;
    }
    
    // Clear any output buffering and output clean JSON
    ob_end_clean();
    echo json_encode($allFormattedData);
    exit;
}

// Check if chart type exists
if (!isset($chartConfig[$chartType])) {
    echo json_encode(['error' => 'Invalid chart type: ' . $chartType]);
    exit;
}

$config = $chartConfig[$chartType];
$data = $statsData[$chartType];

// Format the data for Google Charts using the same logic as statsDiagramsFunctions.php
$formattedData = [];

// Create headers using the same logic as the original showDiagram function
$headers = [''];

$yearsCovered = date("Y") - 2017 + 1;
$years = array();
for($i = $yearsCovered; $i > 0; $i--) {
    $year = date("Y") - $i + 1; 
    
    if ($year == 2020) { // Do not show 2020
        continue;
    }					
    array_push($years, $year);
}				

foreach($years as $year) {
    $headers[] = $year . $config['lowerName'];
    $headers[] = $year . $config['upperName'];
}

$formattedData[] = $headers;

// Add data rows using the same logic as the original showDiagram function
foreach($data as $day => $dataOfDay) {
    // Ignore all empty days from the array
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

/*
// Testing - use hardcoded data for now
$formattedData = [
    ['', '2017: Öffentlich', '2017: Schule/Geschlossene Gesellschaft/Private Gruppe', '2018: Öffentlich', '2018: Schule/Geschlossene Gesellschaft/Private Gruppe', '2019: Öffentlich', '2019: Schule/Geschlossene Gesellschaft/Private Gruppe', '2021: Öffentlich', '2021: Schule/Geschlossene Gesellschaft/Private Gruppe', '2022: Öffentlich', '2022: Schule/Geschlossene Gesellschaft/Private Gruppe', '2023: Öffentlich', '2023: Schule/Geschlossene Gesellschaft/Private Gruppe', '2024: Öffentlich', '2024: Schule/Geschlossene Gesellschaft/Private Gruppe', '2025: Öffentlich', '2025: Schule/Geschlossene Gesellschaft/Private Gruppe', '2026: Öffentlich', '2026: Schule/Geschlossene Gesellschaft/Private Gruppe'],
    ['So', 900.0, 400.0, 2000.0, 400.0, 1920.3, 500.0, 1382.5, 500.0, 1914.2, 500.0, 2026.5, 0.0, 1568.4, 0.0, 1630.2, 0.0, 2009.0, 1700.5],
    ['Mo', 0, 0, 0.0, 100.7, 0, 0, 113.9, 100.0, 0.0, 317.2, 0.0, 150.0, 5.0, 452.8, 0.0, 150.0, 50.5, 72.0],
    ['Di', 0.0, 100.0, 0, 0, 0.0, 148.2, 48.1, 125.5, 0.0, 150.0, 0.0, 300.0, 0.0, 300.0, 54.0, 420.1, 26.5, 57.5],
    ['Mi', 873.8, 0.0, 777.3, 0.0, 769.1, 100.0, 826.3, 120.5, 915.5, 0.0, 1039.0, 150.0, 931.7, 150.0, 1297.4, 150.0, 23.0, 61.5],
    ['Do', 0.0, 217.0, 0.0, 135.8, 86.1, 0.0, 0.0, 106.3, 0.0, 300.0, 3.7, 316.3, 0.0, 333.3, 0.0, 336.7, 0, 0],
    ['Fr', 487.0, 0.0, 322.8, 0.0, 882.1, 217.3, 508.4, 332.1, 986.6, 782.3, 572.6, 150.0, 1048.5, 185.8, 945.0, 300.0, 0, 0],
    ['Sa', 1086.6, 0.0, 1140.5, 0.0, 1718.2, 0.0, 1886.3, 0.0, 2732.2, 0.0, 1909.0, 0.0, 1938.8, 0.0, 2058.3, 0.0, 0, 0],
    ['So', 1560.6, 0.0, 1517.9, 0.0, 3058.7, 0.0, 2507.2, 0.0, 3350.8, 0.0, 2883.3, 0.0, 3514.9, 0.0, 2927.6, 0.0, 0, 0],
    ['Mo', 0, 0, 8.9, 226.5, 0, 0, 126.1, 274.0, 0.0, 322.5, 0.0, 300.0, 0.0, 470.0, 64.5, 360.5, 0, 0],
    ['Di', 0.0, 142.5, 0.0, 125.3, 0, 0, 0.0, 203.0, 11.0, 300.0, 0.0, 300.0, 0.0, 301.7, 0.0, 307.5, 0, 0],
    ['Mi', 916.4, 0.0, 896.0, 581.7, 1239.3, 0.0, 1218.7, 150.3, 1556.9, 150.0, 1129.5, 150.0, 1721.1, 218.6, 1498.1, 150.0, 0, 0],
    ['Do', 241.5, 0.0, 0.0, 158.6, 0, 0, 2.0, 243.6, 0.0, 300.0, 127.0, 200.0, 0.0, 379.6, 3.0, 300.0, 0, 0],
    ['Fr', 765.5, 0.0, 883.2, 0.0, 883.1, 0.0, 1317.9, 229.7, 1705.4, 406.9, 1186.3, 371.5, 1424.6, 150.0, 1388.8, 250.0, 3000, 0],
    ['Sa', 1394.6, 0.0, 1852.3, 0.0, 2313.1, 0.0, 2583.8, 0.0, 3871.1, 0.0, 2830.4, 0.0, 3092.0, 0.0, 2848.6, 0.0, 2000, 0],
    ['So', 1275.8, 0.0, 1594.3, 0.0, 1909.8, 105.0, 1724.3, 0.0, 2604.4, 0.0, 2751.9, 0.0, 2729.6, 0.0, 3179.1, 0.0, 1000, 500]
];*/



// Clear any output buffering and output clean JSON
ob_end_clean();
echo json_encode($formattedData);
?>
