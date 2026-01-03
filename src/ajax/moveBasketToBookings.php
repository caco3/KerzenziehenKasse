<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();


if (isset($_POST['paymentMethod']) and ($_POST['paymentMethod'] == 'cash' or $_POST['paymentMethod'] == 'twint' or $_POST['paymentMethod'] == 'invoice')) {
	$paymentMethod = $_POST['paymentMethod'];

	$response_array['response']['paymentMethod'] = $paymentMethod; 
}
else {
	$errorText = "Invalid parameters!";
	$response_array['response']['success'] = 'false'; 
    $response_array['response']['Text'] = $errorText; 
    
    errorLog(print_r($response_array, true));
	
	// https://www.w3schools.com/xml/ajax_xmlhttprequest_response.asp
	$response_array['readyState'] = '4'; 
	$response_array['status'] = '200'; 

	echo json_encode($response_array);
	exit();
}


$basketSummary = getBasketSummary(false, false);

// print_r($basketSummary);

$serializedBasket = serialize($basketSummary);

// echo($serializedBasket);
// print_r(unserialize($serializedBasket));

// exit();


$success = true;

$bookingId = bookingsCreateId();

// Read meta from basket_various
$meta = getMetaFromBasket();
if ($meta === null) {
    $meta = null; // ensure null on error
} else {
    $meta = serialize($meta); // ensure serialized for DB
}

//echo("moveBasketToBookings.php, $paymentMethod\n");

$ret = moveBasketToBooking($bookingId, $serializedBasket, getDbDonation(), roundMoney10(getDbTotal()), $paymentMethod, $meta);
if( $ret == false) {
    $errorText = "Failed to move basket to bookings (booking ID $bookingId)!";
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
// }


if($success == true) { // ok, whole basket transfered, empty basket
    $ret = emptyBasket();
    if( $ret == false) {
        $errorText = "Failed to empty basket!";
        $success = false;
    }
    updateDonationInBasket(0);
    updateTotalInBasket(0);
    updateBookingIdInBasket("new");
    updateMetaInBasket(null);
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


echo json_encode($response_array);

?>
