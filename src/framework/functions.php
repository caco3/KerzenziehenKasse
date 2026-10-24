<? 

$germanDayOfWeek = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag");
$germanDayOfWeekShort = array("So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So");
$germanMonth = array("Jan", "Feb", "Mrz", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez");

/* Set the correct timezone */
date_default_timezone_set('Europe/Zurich');


/**
 * Returns the HTML for an add-to-basket button of an article.
 *
 * @param int|string $id - Article ID used as button id.
 * @return string HTML button element.
 */
function addButton($id) {
    return "<button type=button id=$id class=addToBasketButton></button> ";
}

/**
 * Returns the HTML for a remove-from-basket button of a basket entry.
 *
 * @param int|string $basketEntryId - Basket entry ID used as button id.
 * @return string HTML button element.
 */
function showRemoveFromBasketButton($basketEntryId) {
    return "<button type=button id=$basketEntryId class=removeFromBasketButton></button> ";
}

/**
 * Returns the HTML for the edit button of a booking (loads the booking
 * back into the basket).
 *
 * @param int|string $id - Booking ID used as button id.
 * @return string HTML button element.
 */
function editButton($id) {
    return "<button type=button id=$id class=editButton title=\"Diese Buchung zurück in den Warenkorb laden\"></button> ";
}

/**
 * Returns the HTML for the print-receipt button of a booking.
 *
 * @param int|string $id - Booking ID used as button id.
 * @return string HTML button element.
 */
function receiptButtonPrint($id) {
    return "<button type=button id=$id class=receiptButtonPrint title=\"Die Rechnung/Quittung für diese Buchung drucken\"></button> ";
}

/**
 * Returns the HTML for the view-receipt button of a booking.
 *
 * @param int|string $id - Booking ID used as button id.
 * @return string HTML button element.
 */
function receiptButtonView($id) {
    return "<button type=button id=$id class=receiptButtonView title=\"Die Rechnung/Quittung für diese Buchung ansehen\"></button> ";
}

/**
 * Returns the HTML for the school-flag toggle button of a booking.
 *
 * @param int|string $id - Booking ID used as button id (prefixed with 'school_').
 * @param bool $isActive - Whether the flag is currently set (adds 'active' class).
 * @return string HTML button element.
 */
function schoolFlagButton($id, $isActive) {
    $activeClass = $isActive ? ' active' : '';
    return "<button type=button id=school_$id class=\"schoolFlagButton$activeClass\" title=\"Schul-Markierung umschalten\"></button> ";
}

/**
 * Returns the HTML for the CSV export button of a booking.
 *
 * @param int|string $id - Booking ID used as button id.
 * @return string HTML button element.
 */
function exportCsvButton($id) {
    return "<button type=button id=$id class=exportCsvButton title=\"Als CSV exportieren\" onclick=\"location.href='subpages/exportCsv.php?id=$id'\"></button> ";
}


/**
 * Appends an SQL statement (whitespace-normalized) together with a
 * timestamp to the db_transaction_log.sql file.
 *
 * @param string $message - The SQL statement or log message.
 * @return void
 */
function sql_transaction_logger($message){
    $message = str_replace("\n", " ", $message);
    $message = preg_replace('!\s+!', ' ', $message);
    // TODO: replace absolute path
    file_put_contents(LOG_FOLDER . "/db_transaction_log.sql", "-- " . date(DATE_RFC2822) . "\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/db_transaction_log.sql", "$message\r\n", FILE_APPEND);
}



/**
 * Sums up the cost of the whole basket, optionally including donation.
 *
 * @param bool $includeDOnation - Whether to add the donation to the sum.
 * @return float|int The calculated basket total.
 */
function calculateBasketTotal($includeDOnation){
    $basket = getDbBasket();
    
    $sum = 0;
    foreach($basket as $basketEntry) {      
        $articleId = $basketEntry['articleId'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        list($name, $type, $pricePerQuantity, $unit, $image1, $image2, $image3, $waxAmount, $waxType) = getDbArticleData($articleId);
        
        if($articleId != 'custom'){ // normal article
            $sum += $quantity * $pricePerQuantity; 
        }
        else{ // custom article
            $sum += $price; 
        }
    }
    
    if($includeDOnation == true){
        $sum += getDbDonation();
    }
    
    return $sum;
}



/**
 * Rundet auf 5 Rappen (rounds to 5 Rappen / 0.05 CHF).
 *
 * @param float $num - The amount to round.
 * @return string The rounded amount, formatted with formatMoney().
 */
function roundMoney($num){
    $x = ($num * 1000) % 100;
    
    if($x < 25){
        $rounded = $num - $x/1000;
    }
    else if($x >= 75){
        $rounded = $num + (50-$x)/1000 + 0.05;
    }
    else {
        $rounded = $num + (50-$x)/1000;
    }
    
    return formatMoney($rounded);
}  



/**
 * Rundet auf 10 Rappen (rounds to 10 Rappen / 0.10 CHF).
 *
 * @param float $num - The amount to round.
 * @return string The rounded amount, formatted with formatMoney().
 */
function roundMoney10($num){
    $x = ($num * 1000) % 100;
    
    if($x < 50){
        $rounded = $num - $x/1000;
    }
    else {
        $rounded = $num + (100-$x)/1000;
    }
    
    return formatMoney($rounded);
}  


/**
 * Formats a price with two decimals and ' as thousands separator.
 *
 * @param float $price - The price to format.
 * @return string The formatted price.
 */
function formatMoney($price) {
    return number_format($price, 2, ".", "'");
}



/**
 * Formats a weight with one decimal and ' as thousands separator.
 *
 * @param float $weight - The weight to format.
 * @return string The formatted weight.
 */
function formatWeight($weight) {
    return number_format($weight, 1, ".", "'");
}




/**
 * Renders the basket summary as an HTML table (article, quantity, price).
 *
 * @return void
 */
function showSummary(){
    $summary = getBasketSummary(false, false);
    
//     echo("<pre>");
//     print_r($summary);
//     echo("</pre>");
?>

    <table id=summaryTable>
    <tr><th>Artikel</th><th>Menge</th><th>Preis</th></tr>
<?
        foreach($summary as $entry){
            echo("<tr>
                <td>" . $entry['name'] . "</td>
                <td>" . $entry['quantity'] . " " .  $entry['unit'] . "</td>
                <td>CHF " . number_format($entry['price'], 2, ".", "") . "</td>
            </tr>\n");
        
        }
        
//         echo("<tr>
//                 <td colspan=2 class=bold>Total</td>
//                 <td class=bold>CHF " . number_format(roundMoney($total), 2, ".", "") . "</td>
//             </tr>\n"); 
?>
    </table>

<?
    
    
}




/**
 * Returns the summarized content of the basket: identical articles are
 * merged, custom articles stay separate.
 *
 * @param bool $includeDonation - Whether to add a 'donation' entry.
 * @param bool $includeTotal - Whether to add a 'total' entry.
 * @return array Summary keyed by articleId (or 'custom_N', 'donation', 'total').
 */
function getBasketSummary($includeDonation, $includeTotal){
    $basket = getDbBasket();
    $summary = array();
    
    $customId = 0; 
        
    foreach($basket as $basketEntry) {      
        $basketEntryId = $basketEntry['basketEntryId'];
        $articleId = $basketEntry['articleId'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $text = $basketEntry['text'];
        list($name, $type, $pricePerQuantity, $unit, $image1, $image2, $image3, $waxAmount, $waxType) = getDbArticleData($articleId);
  
        if($articleId == 'custom'){ // custom article, they never get merged
            $summary['custom_' . $customId]['text'] = $text;  
            $summary['custom_' . $customId]['price'] = $price;             
            $customId++;
        }
        else { // normal article, add it to an existing identical article
            if(!array_key_exists($articleId, $summary)){ // Article shows up first time
                $summary[$articleId]['quantity'] = $quantity;
                $summary[$articleId]['price'] = $pricePerQuantity;
            }
            else { // article is already in the list, sum it up
                $summary[$articleId]['quantity'] += $quantity;
//                 $summary[$articleId]['price']  += $quantity * $pricePerQuantity;
            }
        }    
    } 
    
    ksort($summary);
    
    $total = getDbTotal();
    $donation = getDbDonation();
        
    if(($includeDonation == true) and ($donation > 0)){
//         $summary['donation']['name'] = "Spende";
//         $summary['donation']['quantity'] = "";
//         $summary['donation']['unit'] = "";
        $summary['donation']['price'] = $donation;
//         $summary['donation']['articleId'] = 'donation';
    }
    
    if($includeTotal == true) {
//         $summary['total']['name'] = "Total";
//         $summary['total']['quantity'] = "";
//         $summary['total']['unit'] = "";
        $summary['total']['price'] = $total;
//         $summary['total']['articleId'] = 'total';
    }
        
    return $summary;
}



/**
 * Returns the summarized content of a booking, with the stored article
 * list enriched by article data (name, unit, wax info).
 *
 * @param int|string $bookingId - ID of the booking.
 * @return array The booking array with an 'articles' element.
 */
function getBooking($bookingId){
    $booking = getDbBooking($bookingId);
// 	echo("<pre>"); print_r($booking); echo("</pre>");
	if (is_array($booking['articles'])) {
		$articleIds = array_keys($booking['articles']);
    }
	else {
		$articleIds = array();
	}
	
//     echo("<pre>"); print_r($articleIds); echo("</pre>");
    
    // $booking['articles'] = array();

    if (count($articleIds) > 0) {
        foreach ($articleIds as $articleId) {
            if (strpos("$articleId", 'custom') === 0) { // custom article
                $booking['articles'][$articleId]['quantity'] = 1;
                list($name, $type, $pricePerQuantity, $unit, $image1, $image2, $image3, $waxAmount, $waxType) = getDbArticleData('custom');
            }
            else { // normal article
                list($name, $type, $pricePerQuantity, $unit, $image1, $image2, $image3, $waxAmount, $waxType) = getDbArticleData($articleId);
                $booking['articles'][$articleId]['text'] = $name;
                $booking['articles'][$articleId]['waxAmount'] = $waxAmount;
                $booking['articles'][$articleId]['waxType'] = $waxType;
            }
            $booking['articles'][$articleId]['type'] = $type;
            $booking['articles'][$articleId]['unit'] = $unit;
        }
    }
    else {
        $booking['articles'] = array();
    }

//     echo("<pre>"); print_r($booking); echo("</pre>   ");
    
    return $booking;
}




/**
 * Copies all data of an existing booking (articles, donation, total,
 * extra data) into the basket for editing.
 *
 * @param int|string $bookingId - ID of the booking to copy.
 * @return bool True on success, false on any failure.
 */
function copyBookingToBasket($bookingId) {
    $data = getBooking($bookingId);
//     print_r($data);

    if (updateTotalInBasket($data['total']) != true) {
        return(false);
    }
    
    if (updateDonationInBasket($data['donation']) != true) {
        return(false);
    }
    
    foreach($data['articles' ] as $articleId => $article) {
//         echo("$articleId:\n");
//         print_r($article);

        if (strpos($articleId, "custom_") !== false) { // remove index as used in bookings serialized format
            $articleId = "custom";
        }

        if (addToBasket($articleId, $article['quantity'], $article['price'] * $article['quantity'], $article['text']) != true) {
            return(false);
        }
    }
    
    if (updateBookingIdInBasket($bookingId) != true) {
        return(false);
    }
    
    // Copy extra data from booking to basket
    if (isset($data['extra']) && !empty($data['extra'])) {
        if (updateExtraInBasket($data['extra']) != true) {
            return(false);
        }
    }
    
//     return(false);
    return(true);
}



/**
 * Appends the current basket content of a booking to its log file
 * (booking_<id>.log in LOG_FOLDER).
 *
 * @param int|string $bookingId - ID of the booking.
 * @return bool Always true.
 */
function writeBasketContentLog($bookingId) {
    $basket = getDbBasket();
     
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "------------------------------------------------------------------------------\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "Booking ID, Date\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "donation, total\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "articleId, quantity, pricePerQuantity, price, text\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "------------------------------------------------------------------------------\r\n", FILE_APPEND);
        
    $total = getDbTotal();
    $donation = getDbDonation();
    
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "$bookingId, " . date(DATE_RFC2822) . "\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "$donation, $total\r\n", FILE_APPEND);
    
    foreach($basket as $basketEntry) {      
        $articleId = $basketEntry['articleId'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $text = $basketEntry['text'];
        list($name, $type, $pricePerQuantity, $unit, $image1, $image2, $image3, $waxAmount, $waxType) = getDbArticleData($articleId);

        file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "$articleId, $quantity, $pricePerQuantity, $price, $text\r\n", FILE_APPEND);
    }
        
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "------------------------------------------------------------------------------\r\n\r\n\r\n", FILE_APPEND);
    return true;
}


/**
 * Appends a timestamped message to error.log in LOG_FOLDER.
 *
 * @param string $message - The message to log.
 * @return void
 */
function errorLog($message) {
    file_put_contents(LOG_FOLDER . "/error.log", date(DATE_RFC2822) . ", $message\r\n", FILE_APPEND);
}


?>
