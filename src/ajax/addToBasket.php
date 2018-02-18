<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

$id = "";
$quantity = "";
$success = false;


// sleep(3); //test 

if (isset($_POST['id']) AND (isset($_POST['quantity']) OR isset($_POST['price'])) AND isset($_POST['free']) AND isset($_POST['text'])) {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $text = $_POST['text'];
    $price = $_POST['price'];
    $free = $_POST['free'];

//     echo($id . ", is_numeric(): " . is_numeric($id) . "<br>");
//     echo($quantity . ", is_numeric(): " . is_numeric($quantity) . "<br>");

    if (is_numeric($id)){ // ok
        if((is_numeric($quantity)) OR ($id == 0 AND $text != "" AND $price != "")) { //its a pour or dip article OR a manual article with a price and text
        
        
            if($free != "true"){ // normal article
                $free = 0;
            }
            else{ // manual article
                $free = 1;
                $quantity = 1;
                
            }        
        
            if(addToBasket($id, $quantity, $price, $free, $text) == true){
                $total = calculateBasketTotal(true);    
                updateTotalInBasket($total);
                //todo validate
                $success = true;
            }
            else {
                $errorText = "SQL transaction failed!";
            }
        }
//         else if(is_numeric($quantity)) { // pour or dip
//         
//         }
        else {
            $errorText = "Manual article without a text!";
        }
    }
    else { // invalid data
        $errorText = "Invalid parameters!";
    }
}
else { //parameters not set
    $errorText = "Missing parameters!";
}


if ( $success == true) {
    $response_array['response']['success'] = 'true'; 
    $response_array['response']['Text'] = "Added $quantity of ID $id to basket.";
}
else {
    $response_array['response']['success'] = 'false'; 
    $response_array['response']['Text'] = $errorText; 
    
    // temporary
//     sql_transaction_logger("--" . $errorText);
//     $vars = print_r($_POST, true);
//     sql_transaction_logger("--" . $vars);
//     sql_transaction_logger("-- id: $id, quantity: $quantity");
    
}






// https://www.w3schools.com/xml/ajax_xmlhttprequest_response.asp
$response_array['readyState'] = '4'; 
$response_array['status'] = '200'; 


// $response_array['data']['id'] = $id;  
// $response_array['data']['quantity'] = $quantity; 


// file_put_contents("basket.txt", "$id: $quantity<br>\n", FILE_APPEND | LOCK_EX);

echo json_encode($response_array);

?>
