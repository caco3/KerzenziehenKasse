<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

$basketEntryId = "";
$success = false;

// print_r($_POST);

if (isset($_POST['basketEntryId'])) {
    $basketEntryId = $_POST['basketEntryId'];


    if (is_numeric($basketEntryId)){ // ok
        if(deleteFromBasket($basketEntryId) == true){
            $total = calculateBasketTotal(true);    
            updateTotalInBasket($total);
            //todo validate
            $success = true;
        }
        else {
            $errorText = "SQL transaction failed!";
        }
    }
    else { // invalid data
        $errorText = "Invalid parameter!";
    }
}
else { //parameters not set
    $errorText = "Missing parameter!";
}


if ( $success == true) {
    $response_array['response']['success'] = 'true'; 
    $response_array['response']['Text'] = "Deleted $basketEntryId from basket.";
}
else {
    $response_array['response']['success'] = 'false'; 
    $response_array['response']['Text'] = $errorText; 
    
    errorLog(print_r($response_array, true));    
}






// https://www.w3schools.com/xml/ajax_xmlhttprequest_response.asp
$response_array['readyState'] = '4'; 
$response_array['status'] = '200'; 


// $response_array['data']['id'] = $id;  
// $response_array['data']['quantity'] = $quantity; 


// file_put_contents("basket.txt", "$id: $quantity<br>\n", FILE_APPEND | LOCK_EX);

echo json_encode($response_array);

?>
