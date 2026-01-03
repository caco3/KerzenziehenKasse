<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

$success = true;
$errorText = "";

// empty basket
$ret = emptyBasket();
if( $ret == false) {
    $errorText = "Failed to empty basket!";
    $success = false;
}

if ( $success == true) {
    updateDonationInBasket(0);
    updateTotalInBasket(0);
    updateBookingIdInBasket("new");
    
    // Clear meta data from basket_various
    if (!updateMetaInBasket(null)) {
        errorLog("Failed to clear basket_various.meta");
    }
}


if ( $success == true) {
    $response_array['response']['success'] = 'true'; 
    $response_array['response']['Text'] = "emptied basket.";
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
