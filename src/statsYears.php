<? 
// TODO Refactor this page

$root=".";
include "$root/framework/header.php";


/* Shows the summary stats of a year */
function showSummaryOfYear($year) {
        $articles = array();
        $donations = 0;		
        $total = 0;
		$cash = 0;
		$twint = 0;	
		$invoice = 0;             
        
        // Create list of all available products, so all days have the same order
        $products = getDbProducts("wachs", "articleId");
        // print_r($products);
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
            $articles[$product['articleId']]['image'] = $product['image1'];
        }

        $products = getDbProducts("guss", "name");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
            $articles[$product['articleId']]['image'] = $product['image1'];
        }

        $products = getDbProducts("special", "name");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
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
        <tr><th>Artikel</th><th class=td_rightBorder></th><th class=td_rightBorder>Menge</th><th class=td_rightBorder>Betrag</th></tr>
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

			
			if ($article['subtype'] == 'food') {
                echo("<td class=td_rightBorder>" . $custom . $article['text'] . "</td><td class=td_rightBorder></td><td class=td_rightBorder>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
			}
			else { // normal
                echo("<td class=td_rightBorder>" . $custom . $article['text'] . "</td><td class=td_rightBorder>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td class=td_rightBorder>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
			}
			
			
			
        }
        
        echo("<tr><td colspan=2 class=td_rightBorder>Spenden</td><td class=td_rightBorder></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
        echo("<tr><td colspan=2 class=td_rightBorder><b>Total</b></td><td class=td_rightBorder></td><td><b>CHF " . roundMoney10($total) . "</b></td></tr>\n");
    ?>
        </table>
        <p><br>CSV Export: <? echo(exportCsvButton($year)); ?></p>
        </div>
    <?
}












?>
    <div id="body">
  
<a name=PerYear></a><h1>Umsatz pro Jahr</h1>
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
    
