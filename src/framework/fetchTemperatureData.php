<?php

require_once(__DIR__ . "/credentials_check.php");
require_once(__DIR__ . "/../config/config_generic.php");
require_once(__DIR__ . "/../config/config.php");
require_once(__DIR__ . "/functions.php");
require_once(__DIR__ . "/db.php");

/**
 * Downloads the hourly temperatures for the event location (Uster) from
 * the Open-Meteo archive for all event years and returns them per
 * event-day offset.
 *
 * @param int[]|null $onlyYears - Restrict the fetch to these years, null = all.
 * @return array [year][dayOffset][hour] => temperature in °C, empty on failure.
 */
function fetchTemperatureData($onlyYears = null) {
    global $db_link;
    db_connect();

    $result = mysqli_query($db_link, "SELECT YEAR(date) as year, MIN(date) as firstDay, MAX(date) as lastDay FROM bookings GROUP BY year");
    if (!$result) {
        return array();
    }

    $cache = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $year = (int)$row['year'];
        if ($onlyYears !== null && !in_array($year, $onlyYears)) {
            continue;
        }
        $firstDay = $row['firstDay'];
        $lastDay = $row['lastDay'];
        $zeroDay = date("z", strtotime($firstDay));

        $url = "https://archive-api.open-meteo.com/v1/archive?latitude=47.347&longitude=8.721" .
               "&start_date=$firstDay&end_date=$lastDay&hourly=temperature_2m&timezone=Europe%2FZurich";
        $json = @file_get_contents($url);
        if ($json === false) {
            continue;
        }
        $data = json_decode($json, true);
        $times = $data['hourly']['time'] ?? array();
        $temps = $data['hourly']['temperature_2m'] ?? array();

        foreach ($times as $i => $time) {
            $date = substr($time, 0, 10);
            $hour = intval(substr($time, 11, 2));
            $offset = date("z", strtotime($date)) - $zeroDay;
            $cache[$year][$offset][$hour] = $temps[$i];
        }
    }
    return $cache;
}

// Historical data committed to the repo: works even when Open-Meteo is offline
function getTemperatureStaticPath() {
    return __DIR__ . '/temperatureData.json';
}

// Freshly fetched data (e.g. the ongoing year); writable by www-data
function getTemperatureCachePath() {
    return dirname(__DIR__, 2) . '/log/temperatureData.json';
}

function readJsonFile($path) {
    if (!file_exists($path)) {
        return array();
    }
    return json_decode(file_get_contents($path), true) ?: array();
}

/**
 * Returns the temperatures per year/event-day offset/hour. Historical
 * years come from the committed temperatureData.json plus anything
 * already fetched into the log cache. Only the current year is
 * (re)fetched at runtime, at most once per 24h; on failure the stale
 * data is kept.
 *
 * @return array [year][dayOffset][hour] => temperature in °C.
 */
function getTemperatureData() {
    $data = array_replace_recursive(
        readJsonFile(getTemperatureStaticPath()),
        readJsonFile(getTemperatureCachePath())
    );

    $currentYear = (int)date('Y');
    $cacheFile = getTemperatureCachePath();
    $cacheIsFresh = file_exists($cacheFile)
        && time() - filemtime($cacheFile) < 24 * 3600
        && isset(readJsonFile($cacheFile)[$currentYear]);

    if (!$cacheIsFresh) {
        $newData = fetchTemperatureData([$currentYear]);
        if (count($newData) > 0) {
            $cache = array_replace_recursive(readJsonFile($cacheFile), $newData);
            file_put_contents($cacheFile, json_encode($cache));
            $data = array_replace_recursive($data, $newData);
        }
    }
    return $data;
}

// Run the fetch when this file is called directly: refreshes the log
// cache and, when writable, regenerates the committed static file.
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    $cache = fetchTemperatureData();
    if (count($cache) > 0) {
        file_put_contents(getTemperatureCachePath(), json_encode($cache));
        $staticFile = getTemperatureStaticPath();
        if (is_writable($staticFile) || is_writable(dirname($staticFile))) {
            file_put_contents($staticFile, json_encode($cache));
        }
        header('Content-Type: application/json');
        echo json_encode($cache);
    }
}
?>
