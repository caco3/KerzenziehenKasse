<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

$success = false;
$errorText = "";


if (isset($_POST['bookingId'])) {
    $bookingId = $_POST['bookingId'];    
    if (dbCheckBasketIsEmpty() == true) {
        $response_array['response']['empty'] = "true";
        $success = copyBookingToBasket($bookingId);
    }
    else {
        $response_array['response']['empty'] = "false";
        $errorText = "Basket not empty!";
    }
}
else { //parameters not set
    $errorText = "Missing parameter!";
}


if ( $success == true) {
    $response_array['response']['success'] = 'true'; 
    $response_array['response']['Text'] = "copied booking $bookingId to basket.";
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
