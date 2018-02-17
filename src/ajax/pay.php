<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

$id = "";
$quantity = "";





$summary = getSummary();

// echo("<pre>");
// print_r($summary);
// echo("</pre>");


$success = true;

// Make sure there is a column for each article ID
foreach($summary as $entry){
//     echo "id: " . $entry['articleId'] . "\n";
    $ret = bookingsCreateColumns($entry['articleId']);
    if($ret == false) {
        $errorText = "Failed to create column in table booking!";
        $success = false;
        break;
    }
}


if($success == true) { // ok, all columns exists
    $bookingId = bookingsGetNextFreeId();

//     echo "bookingId: $bookingId\n";

    // Add all articles to bookings
    foreach($summary as $entry){
        $ret = bookingsAddBasket($bookingId, $entry['articleId'], $entry['price']);
        if( $ret == false) {
            $errorText = "Failed to add article " . $entry['articleId'] ." to table booking for booking ID $bookingId!";
            $success = false;
            break;
        }
    }
}


if($success == true) { // ok, whole basket transfered, empty basket
//     $ret = emptyBasket();
//     if( $ret == false) {
//         $errorText = "Failed to empty basket!";
//         $success = false;
//     }
//     updateDonationInBasket(0);
//     updateTotalInBasket(0);
}



if ( $success == true) {
    $response_array['response']['success'] = 'true'; 
    $response_array['response']['Text'] = "moved basket to bookings.";
}
else {
    $response_array['response']['success'] = 'false'; 
    $response_array['response']['Text'] = $errorText; 
}






// https://www.w3schools.com/xml/ajax_xmlhttprequest_response.asp
$response_array['readyState'] = '4'; 
$response_array['status'] = '200'; 


// file_put_contents("basket.txt", "$id: $quantity<br>\n", FILE_APPEND | LOCK_EX);

echo json_encode($response_array);

?>
