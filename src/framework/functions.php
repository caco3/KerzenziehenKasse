<? 


function addButton($id) {
    return "<button type=button id=$id class=addToBasketButton></button> ";
}

function showRemoveFromBasketButton($basketId) {
    return "<button type=button id=$basketId class=removeFromBasketButton></button> ";
}





function listProducts() {
?>
<!--     <h3>Ziehen</h3>         -->
<?
    listDipProducts();
?>


<?


?>
<!--     <h3>Giessen</h3> -->
<br>
<?
        listPourProducts();
?>


<?


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
        $articleId = $basketEntry['article_id'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
        
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




function roundMoney($num){
    $x = ($num * 1000) % 100;
    
    if($x < 25){
        return $num - $x/1000;
    }
    else if($x >= 75){
        return $num + (50-$x)/1000 + 0.05;
    }
    else {
        return $num + (50-$x)/1000;
    }
}  




/* Returns the summarized content of the basket */
function getBasketSummary($includeDonation, $includeTotal){
    $basket = getDbBasket();
    
    $summary = array();
    
    foreach($basket as $basketEntry) {      
        $basketId = $basketEntry['basket_id'];
        $articleId = $basketEntry['article_id'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $text = $basketEntry['text'];
        list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
        
        
//         $id = $articleId;
//         if($id == 'custom'){ // generate unique ID for this custom article (there is no consolidating)
//             $id = "custom_" . $customId;
//             $customId++;
//         }
        
        
        if(!array_key_exists($basketId, $summary)){ // new article
            if($articleId != 'custom'){ // normal article
                $summary[$basketId]['name'] = $name;
                $summary[$basketId]['quantity'] = 0;
                $summary[$basketId]['price'] = 0;              
            }
            else{ // custom article
                $summary[$basketId]['name'] = $text;
                $summary[$basketId]['price'] = $price; 
                $summary[$basketId]['quantity'] = 1;  
            }
            $summary[$basketId]['articleId'] = $articleId;
            $summary[$basketId]['unit'] = $unit;
        }
        
        // Sum identical articles up
        if($articleId != 'custom'){ // normal article
            $summary[$basketId]['price'] += $quantity * $pricePerQuantity; 
            $summary[$basketId]['quantity'] += $quantity;            
        }
        else{ // custom article
//             $summary[$basketId]['price'] = $price; 
//             $summary[$basketId]['quantity'] = 1;  
        }        
    } 
    
    ksort($summary);
    
    $total = getDbTotal();
    $donation = getDbDonation();
    
    
    if(($includeDonation == true) and ($donation > 0)){
        $summary['donation']['name'] = "Spende";
        $summary['donation']['quantity'] = "";
        $summary['donation']['unit'] = "";
        $summary['donation']['price'] = $donation;
        $summary['donation']['articleId'] = 'donation';
    }
    
    if($includeTotal == true) {
        $summary['total']['name'] = "Total";
        $summary['total']['quantity'] = "";
        $summary['total']['unit'] = "";
        $summary['total']['price'] = $total;
        $summary['total']['articleId'] = 'total';
    }
        
    return $summary;
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
                <td>CHF " . number_format($entry['price'], 2) . "</td>
            </tr>\n");
        
        }
        
//         echo("<tr>
//                 <td colspan=2 class=bold>Total</td>
//                 <td class=bold>CHF " . number_format(roundMoney($total), 2) . "</td>
//             </tr>\n"); 
?>
    </table>

<?
    
    
}



/* Returns the sumarized content of the last booking */
function getLastBookingSummary(){
    $customId = 0;
    
    $bookingId = bookingsGetLastId();
    $booking = getBooking($bookingId);
    
    echo("<pre>Bookings:\n");
    print_r($booking);
    
    $summary = array();
    
    foreach(array_keys($booking) as $bookingEntry) {  
        echo("$bookingEntry: " . $booking[$bookingEntry] . "\n");
        
        
        if($bookingEntry == "booking_id") {
        
        }
        elseif($bookingEntry == "total") {
        
        }
        elseif($bookingEntry == "donation") {
        
        }
        else { // article
            if($booking[$bookingEntry] != 0) { // the booking contains this article                
                if(strpos($bookingEntry, "custom_") === false) { // normal article
                    $id = $bookingEntry;
                    list($name, $pricePerQuantity, $unit) = getDbArticleData($id);
                    $summary[$basketId]['name'] = $name;
                    $summary[$basketId]['unit'] = $unit;
                }
                else { // custom article                    
                    $summary[$basketId]['name'] = $bookingEntry; // temporarly
                }
            }
        }    
    }
    
    print_r($summary);
    
    exit();
        
    $summary = array();
    
    foreach($basket as $basketEntry) {      
        $basketId = $basketEntry['basket_id'];
        $articleId = $basketEntry['article_id'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $text = $basketEntry['text'];
        list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
        
        
        $id = $articleId;
        if($id == 'custom'){ // custom article
            $id = "custom_" . $customId;
            $customId++;
        }
        
        
        if(!array_key_exists($id, $summary)){ // new article
            if($articleId != 'custom'){ // normal article
                $summary[$basketId]['name'] = $name;
                $summary[$basketId]['quantity'] = 0;
                $summary[$basketId]['articleId'] = $articleId;
                $summary[$basketId]['price'] = 0;          
            }
            else{ // custom article
                $summary[$basketId]['name'] = $text;
                $summary[$basketId]['price'] = $price; 
                $summary[$basketId]['quantity'] = 1; 
                $summary[$basketId]['articleId'] = $id;
            }
            $summary[$basketId]['unit'] = $unit;
        }
        
        // Sum identical articles up
        if($articleId != 'custom'){ // normal article
            $summary[$basketId]['price'] += $quantity * $pricePerQuantity; 
            $summary[$basketId]['quantity'] += $quantity;            
        }
        else{ // custom article
//             $summary[$basketId]['price'] = $price; 
//             $summary[$basketId]['quantity'] = 1;  
        }        
    } 
    
    ksort($summary);
    
    $total = getDbTotal();
    $donation = getDbDonation();
    
    
    if($donation > 0){
        $summary['donation']['name'] = "Spende";
        $summary['donation']['quantity'] = "";
        $summary['donation']['unit'] = "";
        $summary['donation']['price'] = $donation;
        $summary['donation']['articleId'] = 'donation';
    }
    
    if($includeTotal == true) {
        $summary['total']['name'] = "Total";
        $summary['total']['quantity'] = "";
        $summary['total']['unit'] = "";
        $summary['total']['price'] = $total;
        $summary['total']['articleId'] = 'total';
    }
    
    
    return $summary;
}




function writeBasketContentLog($bookingId) {
    $basket = getDbBasket();
    
    // TODO: replace absolute path    
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "Booking ID, Date\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "articleId, quantity, pricePerQuantity, price, text\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "donation, total\r\n", FILE_APPEND);
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "----------------------------\r\n", FILE_APPEND);
    
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "$bookingId, " . date(DATE_RFC2822) . "\r\n", FILE_APPEND);
    
    foreach($basket as $basketEntry) {      
        $articleId = $basketEntry['article_id'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $text = $basketEntry['text'];
        list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);

        file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "$articleId, $quantity, $pricePerQuantity, $price, $text\r\n", FILE_APPEND);
    }
        
    $total = getDbTotal();
    $donation = getDbDonation();
    
    file_put_contents(LOG_FOLDER . "/booking_$bookingId.log", "$donation, $total\r\n", FILE_APPEND);
    
    
    return true;
}


function errorLog($message) {
    file_put_contents(LOG_FOLDER . "/error.log", date(DATE_RFC2822) . ", $message\r\n", FILE_APPEND);
}


?>
