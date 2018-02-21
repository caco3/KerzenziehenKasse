<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

$id = "";
$quantity = "";





$summary = getBasketSummary(false, false);

// echo("<pre>");
// print_r($summary);
// echo("</pre>");


$success = true;

// In the bookings table, create two columns (quantity, cost) for each used article ID in this basket
foreach($summary as $entry){
//     echo "id: " . $entry['articleId'] . "\n";
    if($entry['articleId'] == 'custom') { // custom article
        $ret = bookingsCreateArticleColumns($entry['articleId'], array("cost", "text")); 
    }
    else { // normal article
        $ret = bookingsCreateArticleColumns($entry['articleId'], array("cost", "quantity")); 
    }
        
        
    if($ret == false) {
        $errorText = "Failed to create columns in table booking!";
        $success = false;
        break;
    }
}


if($success == true) { // ok, all columns exists
    $bookingId = bookingsCreateId();

//     echo "bookingId: $bookingId\n";

    // Add all articles to bookings
    foreach($summary as $entry){    
        if($entry['articleId'] == 'custom') { // custom article
            $ret = bookingsAddBasketCustomArticle($bookingId, $entry['articleId'], $entry['price'], $entry['name']);
        }
        else { // normal article
            $ret = bookingsAddBasketArticle($bookingId, $entry['articleId'], $entry['price'], $entry['quantity']);
        }        
        
        if( $ret == false) {
            $errorText = "Failed to add article " . $entry['articleId'] ." to table booking for booking ID $bookingId!";
            $success = false;
            break;
        }
    }
    
    $ret = bookingsAddBasketDonationAndTotal($bookingId, getDbDonation(), getDbTotal());
    if( $ret == false) {
        $errorText = "Failed to add donation + total to table booking for booking ID $bookingId!";
        $success = false;
    }
    
    
    
    sql_transaction_logger("-- Booking completed (ID: $bookingId)");
    sql_transaction_logger("-- ---------------------------------------------------------------------------");
    
    
    /* Write basket into bookings log */
    $ret = writeBasketContentLog($bookingId);
    if( $ret == false) {
        $errorText = "Failed to write basket Log (booking ID $bookingId)!";
        $success = false;
    }    
}


if($success == true) { // ok, whole basket transfered, empty basket
    $ret = emptyBasket();
    if( $ret == false) {
        $errorText = "Failed to empty basket!";
        $success = false;
    }
    updateDonationInBasket(0);
    updateTotalInBasket(0);
}




if ( $success == true) {
    $response_array['response']['success'] = 'true'; 
    $response_array['response']['Text'] = "moved basket to bookings.";
    $response_array['response']['bookingId'] = $bookingId;
}
else {
    $response_array['response']['success'] = 'false'; 
    $response_array['response']['Text'] = $errorText; 
    
    errorLog(print_r($response_array, true));
}






// https://www.w3schools.com/xml/ajax_xmlhttprequest_response.asp
$response_array['readyState'] = '4'; 
$response_array['status'] = '200'; 


// file_put_contents("basket.txt", "$id: $quantity<br>\n", FILE_APPEND | LOCK_EX);

echo json_encode($response_array);

?>
