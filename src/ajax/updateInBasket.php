<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

$basketEntryId = "";
$quantity = "";
$success = false;

// print_r($_POST);

if (isset($_POST['basketEntryId']) AND isset($_POST['quantity']) AND isset($_POST['price'])) {
    $basketEntryId = $_POST['basketEntryId'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // TODO only check/use price in case its a custom article or donation or total
    if (!is_numeric($quantity) OR !is_numeric($price)){ // quantity or price is not a number
        $errorText = "Invalid parameters (not numbers)!";
    }
    else if (is_numeric($basketEntryId)){ // Id is a number => must be an article (normal or custom)    
        $articleId = getArticleIdInBasket($basketEntryId);    
        if($articleId == 'custom'){ //custom article
            if(updateArticlePriceInBasket($basketEntryId, $price) == true){
                $total = calculateBasketTotal(true);    
                updateTotalInBasket($total);
                //todo validate
                
                list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
                                                    
                // List all fields that changed  
                $response_array['updatedFields']['total'] = $total;
                $response_array['updatedFields']['totalRounded'] = roundMoney10($total);
                        
                $success = true;
            }
            else {
                $errorText = "SQL transaction failed (article)!";
            }
        }
        else{ // normal article        
            if(updateArticleQuantityInBasket($basketEntryId, $quantity) == true){
                $total = calculateBasketTotal(true);    
                updateTotalInBasket($total);
                //todo validate
                
                list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
                                                    
                // List all fields that changed  
                $response_array['updatedFields']['article'][$basketEntryId]['price'] = $quantity * $pricePerQuantity;
                $response_array['updatedFields']['total'] = $total;
                $response_array['updatedFields']['totalRounded'] = roundMoney10($total);
                        
                $success = true;
            }
            else {
                $errorText = "SQL transaction failed (article)!";
            }
        } 
    }
    else if(($basketEntryId == "basketDonationMoney")) { // id is donation, update donation
        if(updateDonationInBasket($price) == true){   
            $total = calculateBasketTotal(true);    
            updateTotalInBasket($total);
            //todo validate
                        
            // List all fields that changed
            $response_array['updatedFields']['total'] = $total;
            $response_array['updatedFields']['totalRounded'] = roundMoney10($total);
                        
            $success = true;
        }
        else {
            $errorText = "SQL transaction failed (donation)!";
        }    
    }
    else if(($basketEntryId == "basketTotalMoney") ) { // id is total, update total
        if(updateTotalInBasket($price) == true){ 
            $TotalWithoutDonation = calculateBasketTotal(false);
            $donation = $price - $TotalWithoutDonation;
            if($donation < 0) { // donation is negative! This means that the newly entered total is below the cost of all articles!
                $donation = 0;
                updateTotalInBasket($TotalWithoutDonation); 
                $total = getDbTotal();
                $response_array['updatedFields']['total'] = $total;
                $response_array['corrections']['action'] = 'uprounded';
                $response_array['corrections']['Text'] = 'total rounded up to minimum!';
            }
            else {
                $total = getDbTotal();
            }
            updateDonationInBasket($donation) == true;
            // todo validate
                        
            // List all fields that changed
            $response_array['updatedFields']['donation'] = $donation;
            $response_array['updatedFields']['totalRounded'] = roundMoney10($total);
            
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
    $response_array['response']['Text'] = "Updated ID $basketEntryId in basket.";
       
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
