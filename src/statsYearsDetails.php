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
        $waxAmountBee = 0;
        $waxAmountParafin = 0;          
        
        // Create list of all available products, so all days have the same order
        $products = getDbProducts("wachs", "articleId");
        // echo("<pre>"); print_r($products);
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['waxType'] = $product['waxType'];
            $articles[$product['articleId']]['waxAmount'] = $product['waxAmount'];
        }

        $products = getDbProducts("guss", "name");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['waxType'] = $product['waxType'];
            $articles[$product['articleId']]['waxAmount'] = $product['waxAmount'];
        }

        $products = getDbProducts("special", "name");
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['type'] = $product['type'];
            $articles[$product['articleId']]['subtype'] = $product['subtype'];
            $articles[$product['articleId']]['image'] = $product['image1'];
            $articles[$product['articleId']]['waxType'] = $product['waxType'];
            $articles[$product['articleId']]['waxAmount'] = $product['waxAmount'];
            $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
        }
        
        $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
        $customIds = 0;
        foreach($bookingDatesOfCurrentYear as $date) {  // a day
            $bookingIds = getBookingIdsOfDate($date, false);
            foreach($bookingIds as $bookingId) { // a booking
                $booking = getBooking($bookingId);
                foreach ($booking['articles'] as $articleId => $article) { // articles
            if(!isset($article['type']) || $article['type'] != "custom") { // normal article   
                $id = $articleId;
            }
            else { // custom article      
                $id = $article['text'] . "_$customIds";
                $customIds++;
            }
                    
                    // echo("<pre>"); print_r($article);
                    
                    $articles[$id]['text'] = $article['text'];
                    $articles[$id]['quantity'] += $article['quantity'];
                    $articles[$id]['price'] = $article['price']; // not summed up since it is per 1 pc.
                    $articles[$id]['unit'] = $article['unit'];
                    $articles[$id]['type'] = $article['type'];
//                     $articles[$id]['waxType'] = $article['waxType'];
//                     $articles[$id]['waxAmount'] = $article['waxAmount'];
                }

                $donations += $booking['donation'];
            }
            
        }
        
        // Add missing price field (missing on articles which have quantity=0)
        foreach($articles as $key => $data) {
            if (! array_key_exists("price", $data)) {
                $articles[$key]['price'] = 0;
            }
        }

        // echo("<pre>"); print_r($articles); echo("</pre>");

        foreach($articles as $article) {
            $total += $article['quantity'] * $article['price'];
        }
        $total += $donations;
        
        if ($total == 0) { // no stats for this year => return
            return;
        }
        
        
        // Sum up wax amount
//         echo("<pre>"); print_r($articles); echo("</pre>");
        foreach($articles as $articleId => $article) {
//             echo("__" . $articles[$articleId]['waxType'] . " " . $articles[$articleId]['type'] . " " . $articles[$articleId]['quantity'] . " " . $articles[$articleId]['waxAmount'] . "<br>");
            
            if ($articles[$articleId]['type'] == "guss") { // Gegossen, sum all up
                if ($articles[$articleId]['waxType'] == "bee") {
                    $waxAmountBee += $articles[$articleId]['quantity'] * $articles[$articleId]['waxAmount'];
                }
                else if ($articles[$articleId]['waxType'] == "parafin") {
                    $waxAmountParafin += $articles[$articleId]['quantity'] * $articles[$articleId]['waxAmount'];
                }
            }
            else if ($articles[$articleId]['type'] == "wachs") { // Gezogen
                if ($articles[$articleId]['waxType'] == "bee") {
                    $waxAmountBee += $articles[$articleId]['quantity'];
                }
                else if ($articles[$articleId]['waxType'] == "parafin") {
                    $waxAmountParafin += $articles[$articleId]['quantity'];
                }
            }
        }

        
    ?>
        <p><br></p>
        <a name=year_<? echo($year); ?>_summary></a><h2><? echo($year); ?><? echo(exportCsvButton($year)); ?></h2>
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
				$quantity = number_format($article['quantity'], 0, ".", "'");
				$unit = $article['unit'];
				if ($article['unit'] == "g") {
					$quantity = number_format($article['quantity'] / 1000, 1, ".", "'");
					$unit = "kg";
					$article['text'] .= " (ohne Gussformen)";
				}
				echo("<td class=td_rightBorder>" . $custom . $article['text'] . "</td><td class=td_rightBorder>$quantity $unit</td><td class=td_rightBorder>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
			}
			
			
			
        }
        
        /* Spenden */
        echo("<tr class=tr_bottomBorder><td colspan=2 class=td_rightBorder>Spenden</td><td class=td_rightBorder></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
        
        /* Total CHF */
        echo("<tr><td colspan=2 class=td_rightBorder><b>Total</b></td><td class=td_rightBorder></td><td><b>CHF " . roundMoney10($total) . "</b></td></tr>\n");
        
        /* Total Wachs */
        echo("<tr><td colspan=2 class=td_rightBorder></td><td class=td_rightBorder></td><td class=td_rightBorder><b>
        <img src=images/articles/colors.png height=25px> Parafinwachs: " . formatWeight($waxAmountParafin/1000) . " kg, <img src=images/articles/bee.png height=25px> Bienenwachs: " . formatWeight($waxAmountBee/1000) . " kg</b></td></tr>\n");
    ?>
        </table>
        </div>
    <?
}

?>
    <div id="body" class="statsYears">
<a name=PerYear></a><h1>Umsatz pro Jahr</h1>
		<div style="display: flex; align-items: flex-start; gap: 20px 60px; margin-bottom: 20px;">
			<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; backdrop-filter: blur(5px); flex: 0 0 auto;">
				<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 0.65); font-size: 16px;">Ansicht:</h4>
				<ul style="margin: 0; padding-left: 20px;">
					<li><a href="statsYears.php" style="color: #007bff;">Kompaktansicht</a><br><br></li>
					<li><a href="statsYearsDetails.php" style="color: #6c757d;">Separate Tabelle pro Jahr</a></li>
				</ul>
			</div>
		</div>
  
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
