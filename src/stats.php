<? 
// TODO Refactor this page

$root=".";
include "$root/framework/header.php";

/* Returns the total grouped per day for each day in the given year listed in the DB */
function getStatsPerDay($year) {
    $data = array();
    $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
	
		// echo("$date<br>\n");
        $donations = 0;
        $total = 0;    
        $food = 0;                
        $articles = array();
        
        $bookingIds = getBookingIdsOfDate($date, false);        
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
			//echo("<pre>");
			//print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles 
				//echo("$articleId, " . ($articleId * 10));
				//print_r($articles);
				//print_r($articles[$articleId]);
				if (is_array($articles[$articleId]) and !array_key_exists('quantity', $articles[$articleId])) {
					$articles[$articleId]['quantity'] = 0;
				}
				$articles[$articleId]['quantity'] += $article['quantity'];
                $articles[$articleId]['price'] = $article['price']; // not summed up since it is per 1 pc.
				
				if ($articleId == 200) { // Food
					//print_r($article);
					$food += $article['quantity']; // equals the costs on food
				}
            }
            $donations += $booking['donation'];
            $total += $booking['total'];

        }   
        
//         $total += $donations;
        $data[$date]['donations'] = $donations;
        $data[$date]['total'] = $total;
        $data[$date]['food'] = $food;
		
		//echo("donations, total, food: $donations, $total, $food<br>\n");
    }
	
	/*if (count($data) == 0) { // No data for this year, add placeholder data
        $data[$date]['donations'] = 0;
        $data[$date]['total'] = 0;
	}*/
    
    ksort($data);
	
	//print_r($data);
    return $data;
}






function showDetailsPerDayAndYear($year) {
    global $germanDayOfWeek, $germanMonth;

    $data = array();
    $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $donations = 0;
        $total = 0;
		$cash = 0;
		$twint = 0;		
        $articles = array();
        
        $bookingIds = getBookingIdsOfDate($date, false);
        
        // Create list of all available products, so all days have the same order
        $products = getDbProducts("wachs", "name");
        // print_r($products);
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
        }

        $products = getDbProducts("guss", "name");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
        }

        $products = getDbProducts("special", "name");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
        }
        
//         echo("<pre>");
//         print_r($articles);
        
        foreach($bookingIds as $bookingId) { // for each booking            
            $booking = getBooking($bookingId);
//             echo("<pre>");
//             print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles                
//                 $articles[$articleId]['text'] = $article['text'];
                $articles[$articleId]['quantity'] += $article['quantity'];
                $articles[$articleId]['price'] = $article['price']; // not summed up since it is per 1 pc.
//                 $articles[$articleId]['unit'] = $article['unit'];
//                 $articles[$articleId]['type'] = $article['type'];
            }
            $donations += $booking['donation'];
            $total += $booking['total'];
			
			if ($booking['twint'] == 1) {
				$twint += $booking['total'];
			}
			else {
				$cash += $booking['total'];
			}
            
//             foreach($articles as $article) {
//                 $total += $article['quantity'] * $article['price'];
// //                 echo("<pre>");
// //                 print_r($article);
//             }
        }   

        
//         $total += $donations;
        $data[$date]['donations'] = $donations;
        $data[$date]['total'] = $total;
        $data[$date]['articles'] = $articles;
        
        $formatedDate = $germanDayOfWeek[strftime("%w", strtotime($date))] .
                    strftime(", %e. ", strtotime($date)) . $germanMonth[strftime("%m", strtotime($date)) - 1] .
                    ". " . strftime("%Y", strtotime($date)); 
        ?>
        
        <a name="<? echo($date); ?>"></a><h2><? echo($formatedDate); ?></h2>
        <table id=bookingsTable>
        <tr><th>Artikel</th><th></th><th>Menge</th><th>Betrag</th></tr>
    <?
	
        foreach($articles as $articleId => $article) {
            if ($article['quantity'] == 0) { // no sales for this article, ignore it 
                continue;
            }
                
            if ($article['type'] == "custom") { 
                $custom = "*) ";
                $article['image'] = $customImage;
            }
            else {
                $custom = ""; 
            }
        
            echo("<tr>");
            echo("<td><span class=tooltip><img class=articleImage src=images/articles/". $article['image'] . "><span><img src=images/articles/". $article['image'] . "></span></span></td>");
            echo("<td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
			
			
			
        }
        
        echo("<tr><td colspan=2>Spenden</td><td></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
        echo("<tr><td colspan=2><b>Total</b></td><td></td><td><b>CHF " . roundMoney10($total) . " (<img src=\"images/cash.png\" height=25px> CHF " . roundMoney10($cash) . ", <img src=\"images/twint-icon.png\" height=25px> CHF " . roundMoney10($twint) .")</b></td></tr>\n");
    ?>
        </table>
        <p><br>CSV Export: <? echo(exportCsvButton($date)); ?></p>
        <?
    }
}





/* Shows the summary stats of a year */
function showSummaryOfYear($year) {
        $articles = array();
        $donations = 0;
             
        
        // Create list of all available products, so all days have the same order
        $products = getDbProducts("wachs", "articleId");
        // print_r($products);
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['image'] = $product['image1'];
        }

        $products = getDbProducts("guss", "name");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['image'] = $product['image1'];
        }

        $products = getDbProducts("special", "name");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
        }
        
                
        $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
        $customIds = 0;
        foreach($bookingDatesOfCurrentYear as $date) {  // a day
            $bookingIds = getBookingIdsOfDate($date, false);
            foreach($bookingIds as $bookingId) { // a booking
                $booking = getBooking($bookingId);
    //             echo("<pre>");
    //             print_r($booking);
                foreach ($booking['articles'] as $articleId => $article) { // articles
                    if($article['type'] != "custom") { // normal article   
                        $id = $articleId;
                    }
                    else { // custom article      
                        $id = $article['text'] . "_$customIds";
                        $customIds++;
                    }
                    
                    $articles[$id]['text'] = $article['text'];
                    $articles[$id]['quantity'] += $article['quantity'];
                    $articles[$id]['price'] = $article['price']; // not summed up since it is per 1 pc.
                    $articles[$id]['unit'] = $article['unit'];
                    $articles[$id]['type'] = $article['type'];
                }
                $donations += $booking['donation'];
            }
        }
        
    //     print_r($articles);

        $total = 0;
        foreach($articles as $article) {
            $total += $article['quantity'] * $article['price'];
        }
        $total += $donations;
        
        if ($total == 0) { // no stats for this year => return
            return;
        }
        
    ?>
        <a name=year_<? echo($year); ?>_summary></a><h2><? echo($year); ?></h2>
        <table id=bookingsTable>
        <tr><th>Artikel</th><th></th><th>Menge</th><th>Betrag</th></tr>
    <?

        foreach($articles as $articleId => $article) {
            if ($article['quantity'] == 0) { // no sales for this article, ignore it 
                continue;
            }
                
            if ($article['type'] == "custom") { 
                $custom = "*) ";
                $article['image'] = $customImage;
            }
            else {
                $custom = ""; 
            }
        
            echo("<tr>");
            echo("<td><span class=tooltip><img class=articleImage src=images/articles/". $article['image'] . "><span><img src=images/articles/". $article['image'] . "></span></span></td>");
            echo("<td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
        }
        
        echo("<tr><td colspan=2>Spenden</td><td></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
        echo("<tr><td colspan=2><b>Total</b></td><td></td><td><b>CHF " . roundMoney10($total) . "</b></td></tr>\n");
    ?>
        </table>
        <p><br>CSV Export: <? echo(exportCsvButton($year)); ?></p>
        </div>
    <?
}












?>
    <div id="body">
     <h1>Übersicht</h1>
    <ul>
	<li><a href=#PerDayAndYear>Umsatz pro Tag und Jahr</a></li>
	<li><a href=#PerDay>Umsatz pro Tag (aktuelles Jahr)</a></li>
	<ul>
<?
	$year = date("Y");
	$bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $timestamp = strtotime($date);
        $formatedDate = $germanDayOfWeek[date("N", $timestamp)] . ", " . date("d. ", $timestamp) . $germanMonth[date("m", $timestamp) - 1] . date(". Y", $timestamp);
        echo("<li><a href=#$date>$formatedDate</a><br></li>\n");
    }

?>
    </ul>
    <li><a href=#PerYear>Zusammenfassung pro Jahr</a></li>
  </ul> 
  
  
  
  
  
  
<a name="PerDayAndYear"></a><h1>Umsatz pro Tag und Jahr</h1> 
<h2>Wachs + Gastronomie</h2> 
<p>Hinweise:<br>
 - Der Gastronomie-Anteil ist erst seit 2021 enthalten!<br>&nbsp;</p>
<?    
    $statsPerDay = array();
    for ($i = 0; $i <= 10; $i++) {
        $year = date("Y") - $i; // iterate through the last 10 years
        $stats = getStatsPerDay($year);
        if (count($stats) == 0) { // no stats for this year => skip
            continue;
        }
        $statsPerDay[$year] = $stats;
    }
    
    $totalPerDayAndYear = array(); // [day][year]
    
    /* Create one index per day for 30 days.
     * If a day stays empty, it will get ignored in the plot */
    for ($i = 0; $i <= 30; $i++) { // for each day add a placeholder index
        $totalPerDayAndYear[$i] = array('donations' => 0, 'total' => 0);
    }
        
    for ($i = 0; $i <= 10; $i++) { // for each year
        $year = date("Y") - $i; 
        $dayIndex = 0;
        foreach($statsPerDay[$year] as $date => $data) { // for each day
            if ($dayIndex == 0) {
                $firstDay = $date; 
                $zeroOffset = date("z", strtotime($date));
            }
            $offset = date("z", strtotime($date)) - $zeroOffset;
            $dayIndex++;
            
            $totalPerDayAndYear[$offset]['year'][$year]['total'] = $data['total']; 
            $totalPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
            $totalPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeek[strftime("%w", strtotime($date))]; 
        }
    }    
	
    include "$root/subpages/totalsChartYear.php"; 
?>  


<h2>Nur Wachs (ohne Gastronomie)</h2>
<?    
    $statsPerDay = array();
    for ($i = 0; $i <= 10; $i++) {
        $year = date("Y") - $i; // iterate through the last 10 years
        $stats = getStatsPerDay($year);
        if (count($stats) == 0) { // no stats for this year => skip
            continue;
        }
        $statsPerDay[$year] = $stats;
    }
    
    $totalWaxPerDayAndYear = array(); // [day][year]
    
    /* Create one index per day for 30 days.
     * If a day stays empty, it will get ignored in the plot */
    for ($i = 0; $i <= 30; $i++) { // for each day add a placeholder index
        $totalWaxPerDayAndYear[$i] = array('donations' => 0, 'total' => 0);
    }
        
    for ($i = 0; $i <= 10; $i++) { // for each year
        $year = date("Y") - $i; 
        $dayIndex = 0;
        foreach($statsPerDay[$year] as $date => $data) { // for each day
			//echo("$date:\n");
			//print_r($data);
            if ($dayIndex == 0) {
                $firstDay = $date; 
                $zeroOffset = date("z", strtotime($date));
            }
            $offset = date("z", strtotime($date)) - $zeroOffset;
            $dayIndex++;
            
			//print_r($data['total']);
            $totalWaxPerDayAndYear[$offset]['year'][$year]['total'] = $data['total'] - $data['food']; // subtract food again as we only want to see the wax part
            $totalWaxPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
            $totalWaxPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeek[strftime("%w", strtotime($date))]; 
        }
    }    
	
    include "$root/subpages/totalWaxChartYear.php"; 
?>  

  
  
  
  
  
  
<a name="PerDay"></a><h1>Umsatz pro Tag (aktuelles Jahr)</h1>
<?
    $year = date("Y"); 
    showDetailsPerDayAndYear($year);
?>
  
  
  
  
  
  
  
  
  
  
  
<a name=PerYear></a><h1>Zusammenfassung pro Jahr</h1>
<?
    // Show yearly summary

    for ($i = 0; $i <= 10; $i++) {
        $year = date("Y") - $i; 
        showSummaryOfYear($year);
    }
?>


<?
include "$root/framework/footer.php"; 
?>
    
