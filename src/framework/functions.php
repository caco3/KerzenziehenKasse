<? 

$germanDayOfWeek = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag");
$germanDayOfWeekShort = array("So", "Mo", "Di", "Mi", "Do", "Fr", "Sa", "So");
$germanMonth = array("Jan", "Feb", "Mrz", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez");

/* Set the correct timezone */
date_default_timezone_set('Europe/Zurich');


function addButton($id) {
    return "<button type=button id=$id class=addToBasketButton></button> ";
}

function showRemoveFromBasketButton($basketEntryId) {
    return "<button type=button id=$basketEntryId class=removeFromBasketButton></button> ";
}

function editButton($id) {
    return "<button type=button id=$id class=editButton title=\"Diese Buchung zurück in den Warenkorb laden\"></button> ";
}

function receiptButtonPrint($id) {
    return "<button type=button id=$id class=receiptButtonPrint title=\"Die Rechnung/Quittung für diese Buchung drucken\"></button> ";
}

function receiptButtonView($id) {
    return "<button type=button id=$id class=receiptButtonView title=\"Die Rechnung/Quittung für diese Buchung ansehen\"></button> ";
}

function schoolFlagButton($id, $isActive) {
    $activeClass = $isActive ? ' active' : '';
    return "<button type=button id=school_$id class=\"schoolFlagButton$activeClass\" title=\"Schul-Markierung umschalten\"></button> ";
}

function exportCsvButton($id) {
    return "<button type=button id=$id class=exportCsvButton title=\"Als CSV exportieren\" onclick=\"location.href='subpages/exportCsv.php?id=$id'\"></button> ";
}


function sql_transaction_logger($message){
    $message = str_replace("\n", " ", $message);
    $message = preg_replace('!\s+!', ' ', $message);
    // TODO: replace absolute path
    file_put_contents(LOG_FOLDER . "/db_transaction_log.sql", "-- " . date(DATE_RFC2822) . "\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/db_transaction_log.sql", "$message\r\n", FILE_APPEND);
}



/* Sums up the cost of the whole basket optionally including donation
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



/* Rundet auf 5 Rappen */
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



/* Rundet auf 10 Rappen */
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


function formatMoney($price) {
    return number_format($price, 2, ".", "'");
}



function formatWeight($weight) {
    return number_format($weight, 1, ".", "'");
}




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




/* Returns the summarized content of the basket */
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



/* Returns the summarized content of a booking */
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


function errorLog($message) {
    file_put_contents(LOG_FOLDER . "/error.log", date(DATE_RFC2822) . ", $message\r\n", FILE_APPEND);
}


?>
