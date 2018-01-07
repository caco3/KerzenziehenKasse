<?
//Make sure there are no characters before the opening PHP-Tag above. Else the Export will be mallformed, see http://stackoverflow.com/questions/29028190/unable-to-write-on-the-first-line-of-csv-using-php

$db_link = NULL;

function db_connect(){
    global $db_link;
    
    $db_link = mysqli_connect (
        MYSQL_HOST, 
        MYSQL_USER, 
        MYSQL_PASSWORD, 
        MYSQL_DATABASE
    );

    // Check connection
    if (mysqli_connect_errno()){
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }
}



function getDbProducts($type) {
    global $db_link;
    
    $sql = "SELECT * FROM tbl_produkt_stamm WHERE typ = '$type' ORDER by `produkt_stamm_id` ASC";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    while ($line = mysqli_fetch_array( $query_response, MYSQL_ASSOC))
    {
        $lines[] = $line;
    } 
    mysqli_free_result( $query_response );
    
//     echo("<pre>");
//     print_r($lines);

    return $lines;
}



function getDbArticleData($id){
    global $db_link;
    
    $sql = "SELECT * FROM tbl_produkt_stamm WHERE produkt_stamm_id = '$id'";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    mysqli_free_result( $query_response );

    return [$line['name'], $line['betrag'], $line['einheit']];
}



function getDbDonation(){
    global $db_link;
    
    $sql = "SELECT donation FROM tbl_basket_various";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    mysqli_free_result( $query_response );

    return $line['donation'];
}



function getDbTotal(){
    global $db_link;
    
    $sql = "SELECT total FROM tbl_basket_various";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    mysqli_free_result( $query_response );

    return $line['total'];
}



function addToBasket($id, $quantity, $price, $free, $text) {
    global $db_link;

    // TODO sanetize
       
    $sql = "
        INSERT INTO `tbl_basket`
        (`article_id`, `quantity`, `price`, `free`, `text`) 
        VALUES
        ('$id', '$quantity',  '$price', '$free', '$text')
    ";
    
        
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
    }
    else { // fail
//         echo('Invalid MySQL request: ' . mysqli_error($db_link) . "<br>");
        sql_transaction_logger("-- [ERROR] Failed to add article $id to basket: $sql");
        return false;
    }
}



function getArticleIdInBasket($basketId) {
    global $db_link;

    $sql = "SELECT `article_id` FROM tbl_basket WHERE `basket_id`='$basketId'"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    mysqli_free_result( $query_response );

    return $line['article_id'];
}



function updateArticleQuantityInBasket($basketId, $quantity) {
    global $db_link;

    $sql = "UPDATE `tbl_basket` SET `quantity`='$quantity' WHERE `basket_id`='$basketId'"; 
    
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
        
//         if(calculateBasketTotal() == true){
//             return true;
//         }
//         else{
//             return false;
//         }
    }
    else { // fail
//         echo('Invalid MySQL request: ' . mysqli_error($db_link) . "<br>");
        sql_transaction_logger("-- [ERROR] Failed to update $id in basket: $sql");
        return false;
    }
}



function updateArticlePriceInBasket($basketId, $price) {
    global $db_link;

    $sql = "UPDATE `tbl_basket` SET `price`='$price' WHERE `basket_id`='$basketId'"; 
    
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
        
//         if(calculateBasketTotal() == true){
//             return true;
//         }
//         else{
//             return false;
//         }
    }
    else { // fail
//         echo('Invalid MySQL request: ' . mysqli_error($db_link) . "<br>");
        sql_transaction_logger("-- [ERROR] Failed to update $id in basket: $sql");
        return false;
    }
}



function updateDonationInBasket($money) {
    global $db_link;

    $sql = "UPDATE `tbl_basket_various` SET `donation`='$money' "; 
    
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;

//         if(calculateBasketTotal() == true){
//             return true;
//         }
//         else{
//             return false;
//         }
    }
    else { // fail
//         echo('Invalid MySQL request: ' . mysqli_error($db_link) . "<br>");
        sql_transaction_logger("-- [ERROR] Failed to update donation in basket: $sql");
        return false;
    }
}



function updateTotalInBasket($money) {
    global $db_link;

    $sql = "UPDATE `tbl_basket_various` SET `total`='$money' "; 
    
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;

//         if(calculateBasketDonation() == true){
//             return true;
//         }
//         else{
//             return false;
//         }
    }
    else { // fail
//         echo('Invalid MySQL request: ' . mysqli_error($db_link) . "<br>");
        sql_transaction_logger("-- [ERROR] Failed to update total in basket: $sql");
        return false;
    }
}



function deleteFromBasket($basketId) {
    global $db_link;

    $sql = "DELETE FROM `tbl_basket` WHERE `basket_id`='$basketId'";

    
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
    }
    else { // fail
//         echo('Invalid MySQL request: ' . mysqli_error($db_link) . "<br>");
        sql_transaction_logger("-- [ERROR] Failed to delete article $basketId from basket: $sql");
        return false;
    }
}



/* Returns the basket content
 * Without donation or total!
 */
function getDbBasket() {
    global $db_link;
    
    $sql = "SELECT * FROM tbl_basket";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

//     echo("<pre>");
    $lines = array();
    while ($line = mysqli_fetch_array( $query_response, MYSQL_ASSOC))
    {
        list($line['name'], $line['pricePerQuantity'], $line['unit']) = getDbArticleData($line['article_id']);
        
        if($line['free'] == 0){ //normal article
            $line['price'] = $line['quantity'] * $line['pricePerQuantity'];
        }

        $lines[] = $line;
//         print_r($line);

    } 
    mysqli_free_result( $query_response );
    
//     echo("<pre>");
//     print_r($lines);

    return $lines;
}





function getBibleVerse() {
    global $db_link;
    
    $sql = "SELECT * FROM tbl_bible_verses ORDER BY RAND() LIMIT 1";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    mysqli_free_result( $query_response );

    return $line;
}

    
?>
