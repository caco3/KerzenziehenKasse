<? 
// TODO Refactor this page

$root=".";
include "$root/framework/header.php";

/* Shows the summary stats of all years in a single table */
function showAllYearsSummary() {
    global $customImage;
    
    // Get all products once for consistency
    $productsWachs = getDbProducts("wachs", "articleId");
    $productsGuss = getDbProducts("guss", "name");
    $productsSpecial = getDbProducts("special", "name");
    
    // Combine all products
    $allProducts = array_merge($productsWachs, $productsGuss, $productsSpecial);
    
    // Initialize data structure for all years
    $yearsData = array();
    $allArticles = array();
    
    // Process each year using original slow approach
    for ($i = 0; $i <= 10; $i++) {
        $year = date("Y") - $i;
        
        $articles = array();
        $donations = 0;		
        $total = 0;
        $waxAmountBee = 0;
        $waxAmountParafin = 0;
        $customIds = 0;
        
        // Initialize articles with all products
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
                'pricePerQuantity' => $product['pricePerQuantity'],
                'price' => 0
            );
        }
        
        // Use original slow approach - get dates, then bookings, then process
        $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
        foreach($bookingDatesOfCurrentYear as $date) {
            $bookingIds = getBookingIdsOfDate($date, false);
            foreach($bookingIds as $bookingId) {
                $booking = getBooking($bookingId);
                foreach ($booking['articles'] as $articleId => $article) {
                    if(!isset($article['type']) || $article['type'] != "custom") {
                        $id = $articleId;
                    }
                    else {
                        $id = $article['text'] . "_$customIds";
                        $customIds++;
                        
                        // Initialize custom article if not exists
                        if (!isset($articles[$id])) {
                            $articles[$id] = array(
                                'text' => $article['text'],
                                'quantity' => 0,
                                'unit' => $article['unit'] ?? '',
                                'type' => $article['type'],
                                'subtype' => $article['subtype'] ?? '',
                                'image' => $customImage,
                                'price' => 0
                            );
                        }
                    }
                    
                    if (isset($articles[$id])) {
                        $articles[$id]['quantity'] += $article['quantity'] ?? 0;
                        $articles[$id]['price'] = $article['price'] ?? 0;
                    }
                }
                $donations += $booking['donation'];
            }
        }
        
        // Calculate totals and wax amounts
        foreach($articles as $articleId => $article) {
            $amount = $article['quantity'] * $article['price'];
            $total += $amount;
            
            // Add safety checks for wax calculations
            if (isset($article['type']) && $article['type'] == "guss") {
                if (isset($article['waxType']) && $article['waxType'] == "bee") {
                    $waxAmountBee += $article['quantity'] * ($article['waxAmount'] ?? 0);
                }
                else if (isset($article['waxType']) && $article['waxType'] == "parafin") {
                    $waxAmountParafin += $article['quantity'] * ($article['waxAmount'] ?? 0);
                }
            }
            else if (isset($article['type']) && $article['type'] == "wachs") {
                if (isset($article['waxType']) && $article['waxType'] == "bee") {
                    $waxAmountBee += $article['quantity'];
                }
                else if (isset($article['waxType']) && $article['waxType'] == "parafin") {
                    $waxAmountParafin += $article['quantity'];
                }
            }
        }
        
        $total += $donations;
        
        // Skip years with no data
        if ($total == 0) {
            continue;
        }
        
        // Store year data
        $yearsData[$year] = array(
            'articles' => $articles,
            'donations' => $donations,
            'total' => $total,
            'waxAmountBee' => $waxAmountBee,
            'waxAmountParafin' => $waxAmountParafin
        );
        
        // Collect all unique articles across all years
        foreach($articles as $articleId => $article) {
            // Collect articles that have actual data (quantity > 0 or price > 0)
            if (($article['quantity'] ?? 0) > 0 || ($article['price'] ?? 0) > 0) {
                $allArticles[$articleId] = $article;
            }
        }
    }
    
    if (empty($yearsData)) {
        echo "<p>Keine Daten verfügbar.</p>";
        return;
    }
    
    // Sort years in ascending order
    ksort($yearsData);
    $years = array_keys($yearsData);
    
    ?>
    <p><br></p>
    <h1>Umsatz pro Jahr</h1>
    <table id=bookingsTable>
    <tr>
        <th>Artikel</th>
        <th class=td_rightBorder></th>
        <th class=td_rightBorder></th>
        <?php foreach($years as $year): ?>
            <th class=td_rightBorder><?php echo $year; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php
    
    // Display each article across all years
    foreach($allArticles as $articleId => $article) {
        $hasData = false;
        
        // Check if this article has any data in any year
        foreach($years as $year) {
            if (isset($yearsData[$year]['articles'][$articleId])) {
                $yearArticle = $yearsData[$year]['articles'][$articleId];
                // Check if this article has quantity > 0 OR price > 0 (for food items)
                if (($yearArticle['quantity'] ?? 0) > 0 || ($yearArticle['price'] ?? 0) > 0) {
                    $hasData = true;
                    break;
                }
            }
        }
        
        if (!$hasData) {
            continue;
        }
        
        echo("<tr>");
        echo("<td><span class=tooltip><img class=articleImage src=images/articles/". $article['image'] . "><span><img src=images/articles/". $article['image'] . "></span></span></td>");
        
        $displayText = $article['text'];
        if ($article['type'] == "custom") {
            $displayText = "*) " . $displayText;
        }
        if ($article['unit'] == "g") {
            $displayText .= " (ohne Gussformen)";
        }
        
        echo("<td class=td_rightBorder>" . $displayText . "</td>");
        
        if ($article['subtype'] == 'food') {
            echo("<td class=td_rightBorder></td>");
        } else {
            $unit = $article['unit'];
            if ($article['unit'] == "g") {
                $unit = "kg";
            }
            echo("<td class=td_rightBorder>$unit</td>");
        }
        
        // Display data for each year
        foreach($years as $year) {
            if (isset($yearsData[$year]['articles'][$articleId])) {
                $yearArticle = $yearsData[$year]['articles'][$articleId];
                
                // Check if this article has data for this year
                if (($yearArticle['quantity'] ?? 0) > 0 || ($yearArticle['price'] ?? 0) > 0) {
                    $amount = $yearArticle['quantity'] * $yearArticle['price'];
                    
                    if ($article['subtype'] == 'food') {
                        echo("<td class=td_rightBorder>CHF " . roundMoney($amount) . "</td>");
                    } else {
                        $quantity = $yearArticle['quantity'];
                        if ($article['unit'] == "g") {
                            $quantity = number_format($quantity / 1000, 1, ".", "'");
                        } else {
                            $quantity = number_format($quantity, 0, ".", "'");
                        }
                        echo("<td class=td_rightBorder>$quantity</td>");
                    }
                } else {
                    echo("<td class=td_rightBorder>-</td>");
                }
            } else {
                echo("<td class=td_rightBorder>-</td>");
            }
        }
        
        echo("</tr>\n");
    }
    
    // Donations row
    echo("<tr class=tr_bottomBorder>");
    echo("<td colspan=3 class=td_rightBorder>Spenden</td>");
    foreach($years as $year) {
        echo("<td class=td_rightBorder>CHF " . roundMoney($yearsData[$year]['donations']) . "</td>");
    }
    echo("</tr>\n");
    
    // Total row
    echo("<tr>");
    echo("<td colspan=2 class=td_rightBorder><b>Total</b></td><td class=td_rightBorder></td>");
    foreach($years as $year) {
        echo("<td class=td_rightBorder><b>CHF " . roundMoney10($yearsData[$year]['total']) . "</b></td>");
    }
    echo("</tr>\n");
    
    // Wax totals row
    echo("<tr>");
    echo("<td colspan=3 class=td_rightBorder><b>Wachs (Total)</b></td>");
    foreach($years as $year) {
        $waxText = "<img src=images/articles/colors.png height=25px> " . 
                   formatWeight($yearsData[$year]['waxAmountParafin']/1000) . " kg, " .
                   "<img src=images/articles/bee.png height=25px> " . 
                   formatWeight($yearsData[$year]['waxAmountBee']/1000) . " kg";
        echo("<td class=td_rightBorder><b>$waxText</b></td>");
    }
    echo("</tr>\n");
    
    ?>
    </table>
    <?php
}

?>
    <div id="body" class="statsYears">
		<div style="display: flex; align-items: flex-start; gap: 20px 60px; margin-bottom: 20px;">
			<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; backdrop-filter: blur(5px); flex: 0 0 auto;">
				<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 0.65); font-size: 16px;">Ansicht:</h4>
				<ul style="margin: 0; padding-left: 20px;">
					<li><a href="statsYears.php" style="color: #007bff;">Kompaktansicht</a><br><br></li>
					<li><a href="statsYearsDetails.php" style="color: #6c757d;">Separate Tabelle pro Jahr</a></li>
				</ul>
			</div>
		</div>
    
    <?php
    showAllYearsSummary();
    ?>
    <p><br></p>
    </div>

<?
include "$root/framework/footer.php"; 
?>
