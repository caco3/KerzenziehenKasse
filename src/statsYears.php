<? 
// TODO Refactor this page

$root=".";
include "$root/framework/header.php";

function appendTrendIndicator($display, $currentValue, &$previousValue) {
    if ($previousValue === null) {
        $previousValue = $currentValue;
        return $display;
    }

    if ($display === "-") {
        $previousValue = $currentValue;
        return "-";
    }

    $percentage = 0;
    $trendSymbol = "";
    $trendColor = "";
    $bgColor = "";
    
    if ($currentValue > $previousValue) {
        if ($previousValue == 0) {
            $percentage = 100; // Treat as 100% increase when going from 0
        } else {
            $percentage = (($currentValue - $previousValue) / $previousValue) * 100;
        }
        $trendSymbol = "↗";
        $trendColor = "#198754";
        $bgColor = "rgba(25, 135, 84, 0.1)";
    }
    else if ($currentValue < $previousValue) {
        if ($previousValue == 0) {
            $percentage = 0; // No percentage when going from 0 to lower (shouldn't happen)
        } else {
            $percentage = (($currentValue - $previousValue) / $previousValue) * 100;
        }
        $trendSymbol = "↘";
        $trendColor = "#dc3545";
        $bgColor = "rgba(220, 53, 69, 0.1)";
    }
    else {
        $trendSymbol = "→";
        $trendColor = "#000";
        $bgColor = "rgba(0, 0, 0, 0.05)";
    }

    $previousValue = $currentValue;
    
    // Format percentage display
    $percentageText = "";
    if ($percentage > 0 && $percentage < 1) {
        $percentageText = "<span style=\"font-size: 0.8em; color: $trendColor;\">+" . number_format($percentage, 1) . "%</span>";
    } else if ($percentage >= 1) {
        $percentageText = "<span style=\"font-size: 0.8em; color: $trendColor;\">+" . number_format($percentage, 0) . "%</span>";
    } else if ($percentage > 0) {
        $percentageText = "<span style=\"font-size: 0.8em; color: $trendColor;\">+" . number_format($percentage, 0) . "%</span>";
    } else if ($percentage < 0) {
        $percentageText = "<span style=\"font-size: 0.8em; color: $trendColor;\">" . number_format($percentage, 0) . "%</span>";
    }
    
    return "<span style=\"display: inline-block; padding: 2px 6px; border-radius: 3px; background-color: $bgColor;\">" .
           $display .
           ($percentageText ? " $percentageText" : "") . 
           "</span>";
}

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

    $sortedArticles = array();
    foreach($allProducts as $product) {
        $articleId = $product['articleId'];
        if (isset($allArticles[$articleId])) {
            $sortedArticles[$articleId] = $allArticles[$articleId];
        }
    }
    foreach($allArticles as $articleId => $article) {
        if (!isset($sortedArticles[$articleId])) {
            $sortedArticles[$articleId] = $article;
        }
    }
    
    ?>
    <p><br></p>
    <h1>Umsatz pro Jahr</h1>
    <table id=bookingsTable style="white-space: nowrap;">
    <colgroup>
        <col>
        <col>
        <col>
        <?php foreach($years as $year): ?>
            <col>
        <?php endforeach; ?>
    </colgroup>
    <tr>
        <th>Artikel</th>
        <th class=td_rightBorder></th>
        <th class=td_rightBorder>Einheit</th>
        <?php foreach($years as $year): ?>
            <th class=td_rightBorder><?php echo $year; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php
    $foodRows = array();
    
    // Display each article across all years
    foreach($sortedArticles as $articleId => $article) {
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
        
        if ($article['subtype'] == 'food') {
            ob_start();
        }
        
        echo("<tr>");
        echo("<td style=\"padding-top: 4px; padding-bottom: 0px;\"><span class=tooltip><img class=articleImage src=images/articles/". $article['image'] . "><span><img src=images/articles/". $article['image'] . "></span></span></td>");
        
        $displayText = $article['text'];
        if ($article['type'] == "custom") {
            $displayText = "*) " . $displayText;
        }
        if ($article['unit'] == "g") {
            $displayText .= " (ohne Gussformen)";
        }
        
        echo("<td class=td_rightBorder>" . $displayText . "</td>");
        
        if ($article['subtype'] == 'food') {
            echo("<td class=td_rightBorder>CHF</td>");
        } else {
            $unit = $article['unit'];
            if ($article['unit'] == "g") {
                $unit = "kg";
            }
            echo("<td class=td_rightBorder>$unit</td>");
        }
        
        // Display data for each year
        $prevTrendValue = null;
        foreach($years as $year) {
            if (isset($yearsData[$year]['articles'][$articleId])) {
                $yearArticle = $yearsData[$year]['articles'][$articleId];
                
                // Check if this article has data for this year
                if (($yearArticle['quantity'] ?? 0) > 0 || ($yearArticle['price'] ?? 0) > 0) {
                    $amount = $yearArticle['quantity'] * $yearArticle['price'];
                    
                    if ($article['subtype'] == 'food') {
                        $numericValue = round($amount, 0);
                        $displayValue = ($numericValue == 0) ? "-" : number_format($numericValue, 0, ".", "'");
                        echo("<td class=td_rightBorder>" . appendTrendIndicator($displayValue, $numericValue, $prevTrendValue) . "</td>");
                    } else {
                        $quantity = $yearArticle['quantity'];
                        if ($article['unit'] == "g") {
                            $numericValue = round($quantity / 1000, 0);
                        } else {
                            $numericValue = round($quantity, 0);
                        }
                        $displayValue = ($numericValue == 0) ? "-" : number_format($numericValue, 0, ".", "'");
                        echo("<td class=td_rightBorder>" . appendTrendIndicator($displayValue, $numericValue, $prevTrendValue) . "</td>");
                    }
                } else {
                    echo("<td class=td_rightBorder>" . appendTrendIndicator("-", 0, $prevTrendValue) . "</td>");
                }
            } else {
                echo("<td class=td_rightBorder>" . appendTrendIndicator("-", 0, $prevTrendValue) . "</td>");
            }
        }
        
        echo("</tr>\n");
        
        if ($article['subtype'] == 'food') {
            $foodRows[] = ob_get_clean();
            continue;
        }
    }
    
    foreach($foodRows as $rowHtml) {
        echo($rowHtml);
    }
    
    // Donations row
    echo("<tr class=tr_bottomBorder>");
    echo("<td style=\"padding-top: 4px; padding-bottom: 0px;\"><span class=tooltip><img class=articleImage src=images/heart.png><span><img src=images/heart.png></span></span></td>");
    echo("<td class=td_rightBorder>Spenden</td>");
    echo("<td class=td_rightBorder>CHF</td>");
    $prevTrendValue = null;
    foreach($years as $year) {
        $donationsRounded = round($yearsData[$year]['donations'], 0);
        if ($donationsRounded == 0) {
            echo("<td class=td_rightBorder>" . appendTrendIndicator("-", 0, $prevTrendValue) . "</td>");
        } else {
            $formatted = number_format($donationsRounded, 0, ".", "'");
            echo("<td class=td_rightBorder>" . appendTrendIndicator($formatted, $donationsRounded, $prevTrendValue) . "</td>");
        }
    }
    echo("</tr>\n");
    
    // Total row
    echo("<tr>");
    echo("<td><b>CHF</b></td>");
    echo("<td class=td_rightBorder><b>Umsatz Total</b></td>");
    echo("<td class=td_rightBorder><b>CHF</b></td>");
    $prevTrendValue = null;
    foreach($years as $year) {
        $totalRounded = round($yearsData[$year]['total'], 0);
        if ($totalRounded == 0) {
            echo("<td class=td_rightBorder><b>" . appendTrendIndicator("-", 0, $prevTrendValue) . "</b></td>");
        } else {
            $formatted = number_format($totalRounded, 0, ".", "'");
            echo("<td class=td_rightBorder><b>" . appendTrendIndicator($formatted, $totalRounded, $prevTrendValue) . "</b></td>");
        }
    }
    echo("</tr>\n");
    
    // Wax totals rows
    echo("<tr>");
    echo("<td style=\"padding-top: 4px; padding-bottom: 0px;\"><span class=tooltip><img class=articleImage src=images/articles/colors.png><span><img src=images/articles/colors.png></span></span></td>");
    echo("<td class=td_rightBorder><b>Parafinwachs</b></td>");
    echo("<td class=td_rightBorder><b>kg</b></td>");
    $prevTrendValue = null;
    foreach($years as $year) {
        $waxParafinRounded = round($yearsData[$year]['waxAmountParafin']/1000, 0);
        if ($waxParafinRounded == 0) {
            echo("<td class=td_rightBorder><b>" . appendTrendIndicator("-", 0, $prevTrendValue) . "</b></td>");
        } else {
            $formatted = number_format($waxParafinRounded, 0, ".", "'");
            echo("<td class=td_rightBorder><b>" . appendTrendIndicator($formatted, $waxParafinRounded, $prevTrendValue) . "</b></td>");
        }
    }
    echo("</tr>\n");

    echo("<tr>");
    echo("<td style=\"padding-top: 4px; padding-bottom: 0px;\"><span class=tooltip><img class=articleImage src=images/articles/bee.png><span><img src=images/articles/bee.png></span></span></td>");
    echo("<td class=td_rightBorder><b>Bienenwachs</b></td>");
    echo("<td class=td_rightBorder><b>kg</b></td>");
    $prevTrendValue = null;
    foreach($years as $year) {
        $waxBeeRounded = round($yearsData[$year]['waxAmountBee']/1000, 0);
        if ($waxBeeRounded == 0) {
            echo("<td class=td_rightBorder><b>" . appendTrendIndicator("-", 0, $prevTrendValue) . "</b></td>");
        } else {
            $formatted = number_format($waxBeeRounded, 0, ".", "'");
            echo("<td class=td_rightBorder><b>" . appendTrendIndicator($formatted, $waxBeeRounded, $prevTrendValue) . "</b></td>");
        }
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
