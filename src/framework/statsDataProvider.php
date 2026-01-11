<? 

$root=".";

// Make sure the german day names are available
global $germanDayOfWeekShort;

/* Returns the total grouped per day for each day in the given year listed in the DB */
function getStatsPerDay($year) {
    global $db_link;
    
    // Single optimized query to get all data for the year
    $nextYear = $year + 1;
    $query = "SELECT b.bookingId, b.date, b.donation, b.total, b.school, b.booking as serialized_articles
              FROM bookings b
              WHERE b.date >= '$year-01-01' AND b.date < '$nextYear-01-01'
              ORDER BY b.date, b.bookingId";
    
    $result = mysqli_query($db_link, $query);
    if (!$result) {
        return array();
    }
    
    // Process all data in memory - replicate original structure exactly
    $data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $date = $row['date'];
        
        // Initialize date data if not exists (matches original structure)
        if (!isset($data[$date])) {
            $data[$date]['donations'] = 0;
            $data[$date]['total'] = 0;
            $data[$date]['food'] = 0;
            $data[$date]['school'] = 0;
            $data[$date]['parafinWax'] = 0;
            $data[$date]['beeWax'] = 0;
        }
        
        // Process booking data exactly like original
        $booking = array();
        $booking['donation'] = floatval($row['donation']);
        $booking['total'] = floatval($row['total']);
        $booking['school'] = $row['school'] == 1;
        $booking['articles'] = unserialize($row['serialized_articles']);
        
        // Skip if unserialize failed
        if ($booking['articles'] === false) {
            continue;
        }
        
        // Process articles exactly like original logic
        $food = 0;
        $beeWax = 0;
        $parafinWax = 0;
        
        foreach ($booking['articles'] as $articleId => $article) {
            if ($articleId == 200) { // Food
                $food += $article['quantity'];
            }
            elseif($articleId == 1) { // Parafin
                $parafinWax += $article["quantity"];
            }
            elseif($articleId == 2) { // Bee Wax
                $beeWax += $article["quantity"];
            }
            else { // Guss
                if (($article["waxType"] ?? "") == "parafin") {
                    $parafinWax += ($article["waxAmount"] ?? 0) * $article["quantity"];					
                }
                else { // bee wax
                    $beeWax += ($article["waxAmount"] ?? 0) * $article["quantity"];					
                }
            }
        }
        
        // Accumulate data exactly like original
        $data[$date]['donations'] += $booking['donation'];
        $data[$date]['total'] += $booking['total'];
        $data[$date]['food'] += $food;
        $data[$date]['parafinWax'] += $parafinWax;
        $data[$date]['beeWax'] += $beeWax;
        
        if ($booking['school']) {
            $data[$date]['school'] += $booking['total'];
        }
    }
    
    ksort($data);
    return $data;
}

function getStatsData() {
    global $germanDayOfWeekShort;
    
    $statsPerDay = array();
    for ($i = 0; $i <= (date("Y") - 2017 + 1); $i++) {
        $year = date("Y") - $i; // iterate through the last years (since 2017)
        $stats = getStatsPerDay($year);
        if (count($stats) == 0) { // no stats for this year => skip
            continue;
        }
        $statsPerDay[$year] = $stats;
    }

    $totalPerDayAndYear = array(); // [day][year]
    $totalPerDayAndYearSummed = array(); // [day][year]
    $totalWaxPerDayAndYear = array(); // [day][year]
    $totalFoodPerDayAndYear = array(); // [day][year]
    $totalWaxPerDayAndYearInKg = array(); // [day][year]
    $totalWaxPerDayAndYearInKgSummed = array(); // [day][year]

    /* Create one index per day for 30 days.
     * If a day stays empty, it will get ignored in the plot */
    for ($i = 0; $i <= 30; $i++) { // for each day add a placeholder index
        $totalPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
        $totalWaxPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
        $totalFoodPerDayAndYear[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
        $totalWaxPerDayAndYearInKg[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
        $totalWaxPerDayAndYearInKgSummed[$i] = array('donations' => 0, 'total' => 0, 'school' => 0);
    }
        
    for ($i = 0; $i <= 10; $i++) { // for each year
        $year = date("Y") - $i; 
        $dayIndex = 0;
        $totalSummed = 0;
        $beeWaxSummed = 0;
        $parafinWaxSummed = 0;
        
        if (! array_key_exists($year, $statsPerDay))  {
            $statsPerDay[$year] = array();
        }
        foreach($statsPerDay[$year] as $date => $data) { // for each day
            if ($dayIndex == 0) {
                $firstDay = $date; 
                $zerodayOffset = date("z", strtotime($date));
            }
            $dayOffset = date("z", strtotime($date)) - $zerodayOffset;
            $dayIndex++;
            
            /* Wax and food in CHF */
            $totalWaxPerDayAndYear[$dayOffset]['year'][$year]['lowerPart'] = $data['total'] - $data['food'] - $data['school']; 
            $totalWaxPerDayAndYear[$dayOffset]['year'][$year]['upperPart'] = $data['school']; 
            $totalWaxPerDayAndYear[$dayOffset]['year'][$year]['date'] = $date; 
            $totalWaxPerDayAndYear[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];
            
            /* Wax and food in CHF, summed up */
            $totalSummed += $data['total'];
            $totalPerDayAndYearSummed[$dayOffset]['year'][$year]['lowerPart'] = $totalSummed; 
            $totalPerDayAndYearSummed[$dayOffset]['year'][$year]['upperPart'] = 0;
            $totalPerDayAndYearSummed[$dayOffset]['year'][$year]['date'] = $date; 
            $totalPerDayAndYearSummed[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];

            /* Wax only in CHF */
            $totalPerDayAndYear[$dayOffset]['year'][$year]['lowerPart'] = $data['total'] - $data['school']; 
            $totalPerDayAndYear[$dayOffset]['year'][$year]['upperPart'] = $data['school']; 
            $totalPerDayAndYear[$dayOffset]['year'][$year]['date'] = $date; 
            $totalPerDayAndYear[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];  
            /* Food only in CHF */
            $totalFoodPerDayAndYear[$dayOffset]['year'][$year]['lowerPart'] = $data['food']; // We only want to see the food part
            $totalFoodPerDayAndYear[$dayOffset]['year'][$year]['upperPart'] = 0;
            $totalFoodPerDayAndYear[$dayOffset]['year'][$year]['date'] = $date; 
            $totalFoodPerDayAndYear[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))]; 
            
            /* Wax only in kg */
            $totalWaxPerDayAndYearInKg[$dayOffset]['year'][$year]['lowerPart'] = $data['parafinWax'] / 1000; 
            $totalWaxPerDayAndYearInKg[$dayOffset]['year'][$year]['upperPart'] = $data['beeWax'] / 1000; 
            $totalWaxPerDayAndYearInKg[$dayOffset]['year'][$year]['date'] = $date; 
            $totalWaxPerDayAndYearInKg[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];
            
            /* Wax only in kg summed up */
            $parafinWaxSummed += $data['parafinWax'] / 1000;
            $beeWaxSummed += $data['beeWax'] / 1000;
            $totalWaxPerDayAndYearInKgSummed[$dayOffset]['year'][$year]['lowerPart'] = $parafinWaxSummed; 
            $totalWaxPerDayAndYearInKgSummed[$dayOffset]['year'][$year]['upperPart'] = $beeWaxSummed; 
            $totalWaxPerDayAndYearInKgSummed[$dayOffset]['year'][$year]['date'] = $date; 
            $totalWaxPerDayAndYearInKgSummed[$dayOffset]['formatedDate'] = $germanDayOfWeekShort[strftime("%w", strtotime($date))];
        }
        

        if (! array_key_exists(0, $totalPerDayAndYearSummed)) { // No data for this year
            continue;
        }

        if (! array_key_exists("year", $totalPerDayAndYearSummed[0])) { // No data for this year
            continue;
        }

        if (! array_key_exists($year, $totalPerDayAndYearSummed[0]['year']))  {
            $totalPerDayAndYearSummed[0]['year'][$year] = array();
            $totalPerDayAndYearSummed[0]['year'][$year]['lowerPart'] = 0;
            $totalPerDayAndYearSummed[0]['year'][$year]['upperPart'] = 0;
        }

        if (! array_key_exists($year, $totalWaxPerDayAndYearInKgSummed[0]['year']))  {
            $totalWaxPerDayAndYearInKgSummed[0]['year'][$year] = array();
            $totalWaxPerDayAndYearInKgSummed[0]['year'][$year]['lowerPart'] = 0;
            $totalWaxPerDayAndYearInKgSummed[0]['year'][$year]['upperPart'] = 0;
        }

        /* Fill up empty days on the summed up data */
        for ($x = 1; $x < 15; $x++) {
            if (array_key_exists($x, $totalPerDayAndYearSummed))  {
                if (! array_key_exists($year, $totalPerDayAndYearSummed[$x]['year']))  {
                    $totalPerDayAndYearSummed[$x]['year'][$year] = array();
                    $totalPerDayAndYearSummed[$x]['year'][$year]['lowerPart'] = 0;
                    $totalPerDayAndYearSummed[$x]['year'][$year]['upperPart'] = 0;
                }

                if ($totalPerDayAndYearSummed[$x]['year'][$year]['lowerPart'] == 0) {
                    $totalPerDayAndYearSummed[$x]['year'][$year]['lowerPart'] = $totalPerDayAndYearSummed[$x - 1]['year'][$year]['lowerPart'];
                }

                if (! array_key_exists($year, $totalWaxPerDayAndYearInKgSummed[$x]['year'] ?? array()))  {
                    $totalWaxPerDayAndYearInKgSummed[$x]['year'][$year] = array();
                    $totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['lowerPart'] = 0;
                    $totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['upperPart'] = 0;
                }

                if (($totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['lowerPart'] ?? 0) == 0) {
                    $totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['lowerPart'] = $totalWaxPerDayAndYearInKgSummed[$x - 1]['year'][$year]['lowerPart'] ?? 0;
                }
                if (($totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['upperPart'] ?? 0) == 0) {
                    $totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['upperPart'] = $totalWaxPerDayAndYearInKgSummed[$x - 1]['year'][$year]['upperPart'] ?? 0;
                }
            }
        }
        
        /* Remove future days on the summed data of the current year */
        if ($year == date("Y")) {
            for ($x = 14; $x > 0; $x--) {
                if (array_key_exists($x, $totalPerDayAndYearSummed))  {
                    if ($totalWaxPerDayAndYearInKg[$x]['year'][$year]['lowerPart'] == 0) { // This day has no booking yet and is therefore most likely in the future
                        $totalPerDayAndYearSummed[$x]['year'][$year]['lowerPart'] = 0;
                        $totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['lowerPart'] = 0;
                        $totalWaxPerDayAndYearInKgSummed[$x]['year'][$year]['upperPart'] = 0;
                    }
                }
            }
        }
    } 

    return array(
        'totalPerDayAndYear' => $totalPerDayAndYear,
        'totalPerDayAndYearSummed' => $totalPerDayAndYearSummed,
        'totalWaxPerDayAndYear' => $totalWaxPerDayAndYear,
        'totalFoodPerDayAndYear' => $totalFoodPerDayAndYear,
        'totalWaxPerDayAndYearInKg' => $totalWaxPerDayAndYearInKg,
        'totalWaxPerDayAndYearInKgSummed' => $totalWaxPerDayAndYearInKgSummed
    );
}

?>
