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

// print_r($_POST);

// sleep(3); //test high latency

if (isset($_POST['id']) AND (isset($_POST['quantity']) OR isset($_POST['price'])) AND isset($_POST['text'])) {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $text = $_POST['text'];
    $price = $_POST['price'];

//     echo($id . ", is_numeric(): " . is_numeric($id) . "<br>");
//     echo($quantity . ", is_numeric(): " . is_numeric($quantity) . "<br>");

    if ((is_numeric($id)) OR ($id == 'custom')){ // valid ID
        if((is_numeric($quantity)) OR ($id == 'custom' AND $text != "" AND $price != "")) { //its a pour or dip article OR a custom article with a price and text
        
        
            if($id != "custom"){ // normal article
                list($name, $pricePerQuantity, $unit, $image) = getDbArticleData($id);
                $price = $quantity * $pricePerQuantity;
            }
            else{ // custom article
                $quantity = 1;                
            }        
        
            if(addToBasket($id, $quantity, $price, $text) == true){
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
