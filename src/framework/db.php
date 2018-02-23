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
    
    $sql = "SELECT * FROM articles WHERE typ = '$type' ORDER by `articleId` ASC";  
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



/* return article data
 * Note: only provide real article IDs (but not custom ones)
 */
function getDbArticleData($id){
    global $db_link;
    
    $sql = "SELECT * FROM articles WHERE articleId = '$id'";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    
    
    if($line['articleId'] == 'custom'){ // custom article
        $line['unit'] = "Stk."; // TODO: replace by unit in table
    }
    
//     echo("<pre>");
//     print_r($line);
//     echo("</pre>");
    
    mysqli_free_result( $query_response );

    return [$line['name'], $line['pricePerQuantity'], $line['unit'], $line['image']];
}



function getDbDonation(){
    global $db_link;
    
    $sql = "SELECT donation FROM basket_various";  
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
    
    $sql = "SELECT total FROM basket_various";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    mysqli_free_result( $query_response );

    return $line['total'];
}



function addToBasket($id, $quantity, $price, $text) {
    global $db_link;

    // TODO sanetize
       
    $sql = "INSERT INTO `basket`
        (`article_id`, `quantity`, `price`, `text`) 
        VALUES
        ('$id', '$quantity',  '$price', '$text')
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

    $sql = "SELECT `article_id` FROM basket WHERE `basket_id`='$basketId'"; 
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

    $sql = "UPDATE `basket` SET `quantity`='$quantity' WHERE `basket_id`='$basketId'"; 
    
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

    $sql = "UPDATE `basket` SET `price`='$price' WHERE `basket_id`='$basketId'"; 
    
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

    $sql = "UPDATE `basket_various` SET `donation`='$money' "; 
    
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

    $sql = "UPDATE `basket_various` SET `total`='$money' "; 
    
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

    $sql = "DELETE FROM `basket` WHERE `basket_id`='$basketId'";

    
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
    
    $sql = "SELECT * FROM basket";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

//     echo("<pre>");
    $lines = array();
    while ($line = mysqli_fetch_array( $query_response, MYSQL_ASSOC))
    {
        list($line['name'], $line['pricePerQuantity'], $line['unit'], $line['image']) = getDbArticleData($line['article_id']);
        
        if($line['custom'] == 'custom'){ //normal article
            $line['price'] = $line['quantity'] * $line['pricePerQuantity'];
        }
        else{ // custom article
            $line['unit'] = "Stk."; // TODO replace by unit from table
        }

        $lines[] = $line;
//         print_r($line);

    } 
    mysqli_free_result( $query_response );
    
//     echo("<pre>");
//     print_r($lines);

    return $lines;
}




function bookingsCreateArticleColumns($articleId, $columns) {
    global $db_link;
    
    foreach ($columns as $column) {
        $columnName = "article_" . $articleId . "_" . $column;
        
        switch($column) {
            case "cost":
                $columnsType = "decimal(8,3)";
                break;
            case "quantity":
                $columnsType = "int(11)";
                break;
            case "text":
                $columnsType = "text";
                break;
            default:
                $columnsType = "text";
                break;
        }
                
        // create column if it doesn't exist yet
        $sql = "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS `$columnName` $columnsType NOT NULL";
        
        if(mysqli_query($db_link, $sql)){
            sql_transaction_logger($sql);
        }
        else { // fail
            sql_transaction_logger("-- [ERROR] Failed to create column 'article_" . $articleId . "_quantity' in table booking: $sql");
            return false;
        }
    }
    
    return true;
}




function bookingsCreateId() {
    global $db_link;
    
    $sql = "INSERT INTO bookings (`bookingId`) values (null)"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }
    
    return bookingsGetLastId();
}


function bookingsGetLastId() {
    global $db_link;
    
    $sql = "SELECT `bookingId` FROM bookings ORDER BY `bookingId` DESC LIMIT 1"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    mysqli_free_result( $query_response );
    
    if($line == ""){ //empty table
        return 0;
    }
    
    return $line['bookingId'];
}




function moveBasketToBooking($bookingId, $serializedBasket, $donation, $total) {
    global $db_link;

    // TODO sanetize
       
    $sql = "UPDATE `bookings`
            SET `date`='" . date("Y-m-d") . "', `time`='" . date("H:i:s") . "',
            `booking`='$serializedBasket',
            `donation`='$donation', `total`='$total'
            WHERE `bookingId`='$bookingId'"; 
    
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
    }
    else { // fail
        sql_transaction_logger("-- [ERROR] Failed to add donation and total to bookings: $sql");
        return false;
    }
}




function getDbBooking($bookingId) {
    global $db_link;
    
    $sql = "SELECT * FROM bookings WHERE bookingId = '$bookingId'"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQL_ASSOC);
    mysqli_free_result( $query_response );
        
    $booking = array();
    
    $booking['date'] = $line['date'];
    $booking['time'] = $line['time'];
    $booking['donation'] = $line['donation'];
    $booking['total'] = $line['total'];

    $booking['articles'] = unserialize($line['booking']);

    
//     print_r($booking);    

    return $booking;
}


function getBookingIdsOfDate($date) {
   global $db_link;
    
    $sql = "SELECT bookingId FROM bookings WHERE date = '$date'"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }
    
    $lines = array();
    while ($line = mysqli_fetch_array( $query_response, MYSQL_ASSOC))
    {
        $lines[] = $line['bookingId'];
    } 
    mysqli_free_result( $query_response );
    
    return $lines;
}



function emptyBasket() {
    global $db_link;
       
    $sql = "TRUNCATE TABLE basket"; 
        
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
    }
    else { // fail
        sql_transaction_logger("-- [ERROR] Failed to drop basket: $sql");
        return false;
    }
}



function getBibleVerse() {
    global $db_link;
    
    $sql = "SELECT * FROM bible_verses ORDER BY RAND() LIMIT 1";  
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
