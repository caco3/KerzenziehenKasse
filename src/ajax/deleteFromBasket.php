<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

$basketId = "";
$success = false;

// print_r($_POST);

if (isset($_POST['basketId'])) {
    $basketId = $_POST['basketId'];


    if (is_numeric($basketId)){ // ok
        if(deleteFromBasket($basketId) == true){
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
    $response_array['response']['Text'] = "Deleted $basketId from basket.";
}
else {
    $response_array['response']['success'] = 'false'; 
    $response_array['response']['Text'] = $errorText; 
    
    // temporary
//     sql_transaction_logger("--" . $errorText);
//     $vars = print_r($_POST, true);
//     sql_transaction_logger("--" . $vars);
//     sql_transaction_logger("-- id: basketId, quantity: $quantity");
    
}






// https://www.w3schools.com/xml/ajax_xmlhttprequest_response.asp
$response_array['readyState'] = '4'; 
$response_array['status'] = '200'; 


// $response_array['data']['id'] = $id;  
// $response_array['data']['quantity'] = $quantity; 


// file_put_contents("basket.txt", "$id: $quantity<br>\n", FILE_APPEND | LOCK_EX);

echo json_encode($response_array);

?>
