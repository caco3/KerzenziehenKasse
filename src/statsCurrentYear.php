<? 
// TODO Refactor this page

$root=".";
include "$root/framework/header.php";

/* Returns the total grouped per day for each day in the given year listed in the DB */
function getStatsPerDay($year) {
    global $db_link;
    
    // Single optimized query to get all data for the year
    $nextYear = $year + 1;
    $query = "SELECT b.bookingId, b.date, b.donation, b.total, b.paymentMethod, b.booking as serialized_articles
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
        }
        
        // Process booking data exactly like original
        $booking = array();
        $booking['donation'] = floatval($row['donation']);
        $booking['total'] = floatval($row['total']);
        $booking['articles'] = unserialize($row['serialized_articles']);
        
        // Process articles exactly like original logic
        $food = 0;
        
        foreach ($booking['articles'] as $articleId => $article) {
            if ($articleId == 200) { // Food
                $food += $article['quantity'];
            }
        }
        
        // Accumulate data exactly like original
        $data[$date]['donations'] += $booking['donation'];
        $data[$date]['total'] += $booking['total'];
        $data[$date]['food'] += $food;
    }
    
    ksort($data);
    return $data;
}

function showDetailsPerDayAndYear($year) {
    global $germanDayOfWeek, $germanDayOfWeekShort, $germanMonth;

    // Get all products once (3 queries instead of per date)
    $productsWachs = getDbProducts("wachs", "name");
    $productsGuss = getDbProducts("guss", "name");
    $productsSpecial = getDbProducts("special", "name");
    
    // Combine all products into single array
    $allProducts = array_merge($productsWachs, $productsGuss, $productsSpecial);
    
    // Get unique dates first (still much better than N+1)
    $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
    
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $donations = 0;
        $total = 0;
        $cash = 0;
        $twint = 0;	
        $invoice = 0;		
        $waxAmountBee = 0;
        $waxAmountParafin = 0;
        $articles = array();
        
        // Initialize articles with all products (but only once per date)
        foreach($allProducts as $product) {
            $articles[$product['articleId']] = array(
                'text' => $product['name'],
                'quantity' => 0,
                'unit' => $product['unit'],
                'type' => $product['type'],
                'subtype' => $product['subtype'],
                'image' => $product['image1'],
                'waxType' => $product['waxType'],
                'waxAmount' => $product['waxAmount'],
                'pricePerQuantity' => $product['pricePerQuantity']
            );
        }
        
        // Get bookings for this date (still much better than individual booking queries)
        $bookingIds = getBookingIdsOfDate($date, false);
        
        foreach($bookingIds as $bookingId) { // for each booking            
            $booking = getBooking($bookingId);
            foreach ($booking['articles'] as $articleId => $article) { // articles      
                $articles[$articleId]['quantity'] += $article['quantity'];
                $articles[$articleId]['price'] = $article['price']; // not summed up since it is per 1 pc.
            }

            $donations += $booking['donation'];
            $total += $booking['total'];
            
            if ($booking['paymentMethod'] == 'cash') {
                $cash += $booking['total'];
            }
            else if ($booking['paymentMethod'] == 'twint') {
                $twint += $booking['total'];
            }
            else { // invoice
                $invoice += $booking['total'];
            }
        }   
        
        // Sum up wax amount (original logic)
        foreach($articles as $articleId => $article) {
            if ($article['type'] == "guss") { // Gegossen, sum all up
                if ($article['waxType'] == "bee") {
                    $waxAmountBee += $article['quantity'] * $article['waxAmount'];
                }
                else if ($article['waxType'] == "parafin") {
                    $waxAmountParafin += $article['quantity'] * $article['waxAmount'];
                }
            }
            else if ($article['type'] == "wachs") { // Gezogen
                if ($article['waxType'] == "bee") {
                    $waxAmountBee += $article['quantity'];
                }
                else if ($article['waxType'] == "parafin") {
                    $waxAmountParafin += $article['quantity'];
                }
            }
        }
        
        $formatedDate = $germanDayOfWeek[strftime("%w", strtotime($date))] .
                    strftime(", %e. ", strtotime($date)) . $germanMonth[strftime("%m", strtotime($date)) - 1] .
                    ". " . strftime("%Y", strtotime($date)); 
        ?>
        <p><br></p>
        <a name="<? echo($date); ?>"></a><h2><? echo($formatedDate); ?> <? echo(exportCsvButton($date)); ?></h2>
        <table id=bookingsTable>
        <tr><th>Artikel</th><th class=td_rightBorder></th><th class=td_rightBorder>Menge</th><th class=td_rightBorder>Betrag</th></tr>
    <?
        foreach($articles as $articleId => $article) {
            if ($article['quantity'] == 0) { // no sales for this article, ignore it 
                continue;
            }

            if (! array_key_exists("price", $article)) { // Add missing (neutral) value
                $article['price'] = $article['quantity'] * $article['pricePerQuantity'];
            }
        
            echo("<tr>");
            echo("<td><span class=tooltip><img class=articleImage src=images/articles/". $article['image'] . "><span><img src=images/articles/". $article['image'] . "></span></span></td>");
			
			if ($article['subtype'] == 'food') {
				echo("<td class=td_rightBorder>" . $article['text'] . "</td><td class=td_rightBorder></td><td class=td_rightBorder>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
			}
			else { // normal                            
                                $quantity = number_format($article['quantity'], 0, ".", "'");
                                $unit = $article['unit'];
                                if ($article['unit'] == "g") {
                                    $quantity = number_format($article['quantity'] / 1000, 1, ".", "'");
                                    $unit = "kg";
                                }
				echo("<td class=td_rightBorder>" . $article['text'] . "</td><td class=td_rightBorder>$quantity $unit</td><td class=td_rightBorder>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
			}
        }
        
        /* Spenden */
        echo("<tr class=tr_bottomBorder><td colspan=2 class=td_rightBorder>Spenden</td><td class=td_rightBorder></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
        
        /* Total CHF */
        echo("<tr><td colspan=2 class=td_rightBorder><b>Total</b></td><td class=td_rightBorder></td><td><b>CHF " . roundMoney10($total) . " (<img src=\"images/bargeld.png\" height=25px> CHF " . roundMoney10($cash) . ", <img src=\"images/twint-icon.png\" height=25px> CHF " . roundMoney10($twint) . ", <img src=\"images/invoice.png\" height=25px> CHF " . roundMoney10($invoice) .")</b></td></tr>\n");
        
        /* Total Wachs */
        echo("<tr><td colspan=2 class=td_rightBorder></td><td class=td_rightBorder></td><td class=td_rightBorder><b>
        <img src=images/articles/colors.png height=25px> Parafinwachs: " . formatWeight($waxAmountParafin/1000) . " kg, <img src=images/articles/bee.png height=25px> Bienenwachs: " . formatWeight($waxAmountBee/1000) . " kg</b></td></tr>\n");
    ?>
        </table>
        <?
    }
}

?>
<div id="body" class="statsCurrentYear">  
<a name="PerDay"></a><h1>Umsatz pro Tag (aktuelles Jahr)</h1>
<?
    $year = date("Y"); 
    showDetailsPerDayAndYear($year);
?>
  
  
  
  
  
  
  
  
<?
include "$root/framework/footer.php"; 
?>
    
