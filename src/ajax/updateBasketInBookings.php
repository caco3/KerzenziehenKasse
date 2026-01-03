<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();


$basketSummary = getBasketSummary(false, false);

// print_r($basketSummary);

$serializedBasket = serialize($basketSummary);

// echo($serializedBasket);
// print_r(unserialize($serializedBasket));

// exit();


$success = true;

$bookingId = getDbBookingId();
if($bookingId == "new" ) { // error, we expect a basket loaded from bookings
    $errorText = "Expected bookingId to be number, but is 'new'!";
    $success = false;
}
else { // ok, basket was loaded from bookings and can be updated in there
	$booking = getDbBooking($bookingId);
	
	$paymentMethod = $booking['paymentMethod']; // On update, we do not support changing the method, so fetch it from the booking

    $ret = moveBasketToBooking($bookingId, $serializedBasket, getDbDonation(), roundMoney10(getDbTotal()), $paymentMethod);
    if( $ret == false) {
        $errorText = "Failed to move basket to bookings (booking ID $bookingId, updating booking)!";
        $success = false;
    }

    sql_transaction_logger("-- Booking completed (ID: $bookingId)");
    sql_transaction_logger("-- ---------------------------------------------------------------------------");


    /* Write basket into bookings log */
    $ret = writeBasketContentLog($bookingId);
    if( $ret == false) {
        $errorText = "Failed to write basket Log (booking ID $bookingId, updating booking)!";
        $success = false;
    }    
    // }


    if($success == true) { // ok, whole basket transfered, empty basket
        $ret = emptyBasket();
        if( $ret == false) {
            $errorText = "Failed to empty basket (updating booking)!";
            $success = false;
        }
        updateDonationInBasket(0);
        updateTotalInBasket(0);
        updateBookingIdInBasket("new");
        updateMetaInBasket(null);
    }
}



if ( $success == true) {
    $response_array['response']['success'] = 'true'; 
    $response_array['response']['Text'] = "updated basket in bookings.";
    $response_array['response']['bookingId'] = $bookingId;
	$response_array['response']['paymentMethod'] = $paymentMethod; 
}
else {
    $response_array['response']['success'] = 'false'; 
    $response_array['response']['Text'] = $errorText; 
    
    errorLog(print_r($response_array, true));
}



// https://www.w3schools.com/xml/ajax_xmlhttprequest_response.asp
$response_array['readyState'] = '4'; 
$response_array['status'] = '200'; 


echo json_encode($response_array);

?>
