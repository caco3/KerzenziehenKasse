<?

include "../config/config.php";
include "../framework/functions.php";
include "../framework/db.php";

db_connect();

$basketId = "";
$quantity = "";
$success = false;

if (isset($_POST['basketId']) AND isset($_POST['free']) AND isset($_POST['quantity']) AND isset($_POST['price'])) {
    $basketId = $_POST['basketId'];
    $free = $_POST['free'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    if (!is_numeric($free) OR !is_numeric($quantity) OR !is_numeric($price)){ // one of free, quantity or price is not a number
        $errorText = "Invalid parameters (not numbers)!";
    }
    else if (is_numeric($basketId)){ // Id is a number => must be an article
        if($free != 0){ //manual article
            if(updateArticlePriceInBasket($basketId, $price) == true){
                $total = calculateBasketTotal(true);    
                updateTotalInBasket($total);
                //todo validate
                
                $articleId = getArticleIdInBasket($basketId);
                list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
                                                    
                // List all fields that changed  
                $response_array['updatedFields']['total'] = $total;
                        
                $success = true;
            }
            else {
                $errorText = "SQL transaction failed (article)!";
            }
        }
        else{ // normal article        
            if(updateArticleQuantityInBasket($basketId, $quantity) == true){
                $total = calculateBasketTotal(true);    
                updateTotalInBasket($total);
                //todo validate
                
                $articleId = getArticleIdInBasket($basketId);
                list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
                                                    
                // List all fields that changed  
                $response_array['updatedFields']['article'][$basketId]['price'] = $quantity * $pricePerQuantity;
                $response_array['updatedFields']['total'] = $total;
                        
                $success = true;
            }
            else {
                $errorText = "SQL transaction failed (article)!";
            }
        }
    
    
    
    
    
        
    }
    else if(($basketId == "basketDonationMoney")) { // id is donation, update donation
        if(updateDonationInBasket($price) == true){   
            $total = calculateBasketTotal(true);    
            updateTotalInBasket($total);
            //todo validate
                        
            // List all fields that changed
            $response_array['updatedFields']['total'] = $total;
                        
            $success = true;
        }
        else {
            $errorText = "SQL transaction failed (donation)!";
        }    
    }
    else if(($basketId == "basketTotalMoney") ) { // id is total, update total
        if(updateTotalInBasket($price) == true){ 
            $TotalWithoutDonation = calculateBasketTotal(false);
            $donation = $price - $TotalWithoutDonation;
            if($donation < 0) {
                $donation = 0;
                updateTotalInBasket($TotalWithoutDonation); 
                $total = getDbTotal();
                $response_array['updatedFields']['total'] = $total;
            }
            updateDonationInBasket($donation) == true;
            // todo validate
                        
            // List all fields that changed
            $response_array['updatedFields']['donation'] = $donation;
            
            $success = true;
        }
        else {
            $errorText = "SQL transaction failed (total)!";
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
    $response_array['response']['Text'] = "Updated ID $basketId in basket.";
       
}
else {
    $response_array['response']['success'] = 'false'; 
    $response_array['response']['Text'] = $errorText; 
    
    // temporary
//     sql_transaction_logger("--" . $errorText);
//     $vars = print_r($_POST, true);
//     sql_transaction_logger("--" . $vars);
//     sql_transaction_logger("-- id: $basketId, quantity: $quantity");
    
}






// https://www.w3schools.com/xml/ajax_xmlhttprequest_response.asp
$response_array['readyState'] = '4'; 
$response_array['status'] = '200'; 


// $response_array['data']['id'] = $id;  
// $response_array['data']['quantity'] = $quantity; 


// file_put_contents("basket.txt", "$id: $quantity<br>\n", FILE_APPEND | LOCK_EX);

echo json_encode($response_array);

?>
