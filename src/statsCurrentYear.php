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
    global $germanDayOfWeek, $germanDayOfWeekShort, $germanMonth;

    $data = array();
    $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $donations = 0;
        $total = 0;
		$cash = 0;
		$twint = 0;	
		$invoice = 0;		
        $articles = array();
        
        $bookingIds = getBookingIdsOfDate($date, false);
        
        // Create list of all available products, so all days have the same order
        $products = getDbProducts("wachs", "name");
        // print_r($products);
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
        }

        $products = getDbProducts("guss", "name");
		//echo("<pre>products\n");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
        }

        $products = getDbProducts("special", "name");
        //print_r($products);
		foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
        }
        
//         echo("<pre>");
         //print_r($articles);
        
        foreach($bookingIds as $bookingId) { // for each booking            
            $booking = getBooking($bookingId);
//             echo("<pre>");
//             print_r($booking);
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
        <tr><th>Artikel</th><th class=td_rightBorder></th><th class=td_rightBorder>Menge</th><th class=td_rightBorder>Betrag</th></tr>
    <?
	
        foreach($articles as $articleId => $article) {
            if ($article['quantity'] == 0) { // no sales for this article, ignore it 
                continue;
            }
        
            echo("<tr>");
            echo("<td><span class=tooltip><img class=articleImage src=images/articles/". $article['image'] . "><span><img src=images/articles/". $article['image'] . "></span></span></td>");
			
			if ($article['subtype'] == 'food') {
				echo("<td class=td_rightBorder>" . $custom . $article['text'] . "</td><td class=td_rightBorder></td><td class=td_rightBorder>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
			}
			else { // normal
				echo("<td class=td_rightBorder>" . $custom . $article['text'] . "</td><td class=td_rightBorder>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td class=td_rightBorder>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
			}
        }
        
        echo("<tr><td colspan=2 class=td_rightBorder>Spenden</td><td class=td_rightBorder></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
        echo("<tr><td colspan=2 class=td_rightBorder><b>Total</b></td><td class=td_rightBorder></td><td><b>CHF " . roundMoney10($total) . " (<img src=\"images/cash.png\" height=25px> CHF " . roundMoney10($cash) . ", <img src=\"images/twint-icon.png\" height=25px> CHF " . roundMoney10($twint) . ", <img src=\"images/invoice.png\" height=25px> CHF " . roundMoney10($invoice) .")</b></td></tr>\n");
    ?>
        </table>
        <p><br>CSV Export: <? echo(exportCsvButton($date)); ?></p>
        <?
    }
}






?>
<div id="body">  
<a name="PerDay"></a><h1>Umsatz pro Tag (aktuelles Jahr)</h1>
<?
    $year = date("Y"); 
    showDetailsPerDayAndYear($year);
?>
  
  
  
  
  
  
  
  
<?
include "$root/framework/footer.php"; 
?>
    