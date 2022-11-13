<?
//Make sure there are no characters before the opening PHP-Tag above. Else the Export will be mallformed, see http://stackoverflow.com/questions/29028190/unable-to-write-on-the-first-line-of-csv-using-php

$db_link = NULL;

function db_connect(){
    global $db_link;
    
    $db_link = mysqli_connect (
        MYSQL_HOST, 
        MYSQL_USER, 
        MYSQL_PASSWORD, 
        MYSQL_DATABASE,
        MYSQL_PORT
    );

    // Check connection
    if (mysqli_connect_errno()){
        echo(MYSQL_HOST . ", " . MYSQL_USER . ", " . MYSQL_PASSWORD . ", " . MYSQL_DATABASE . "<br>\n");
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }
}



function getDbProducts($type, $orderByColumn) {
    global $db_link;
    
    $sql = "SELECT * FROM articles WHERE typ = '$type' ORDER by `$orderByColumn` ASC";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    while ($line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC))
    {
        $lines[] = $line;
    } 
    mysqli_free_result( $query_response );
    
//     echo("<pre>");
//     print_r($lines);

    return $lines;
}


function getDbProductsEx($type, $orderByColumn, $subType) {
    global $db_link;
    
    $sql = "SELECT * FROM articles WHERE (typ = '$type' AND subtype = '$subType') ORDER by `$orderByColumn` ASC";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    while ($line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC))
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

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    
    
    if($line['typ'] == 'custom'){ // custom article
        $line['unit'] = "Stk."; // TODO: replace by unit in table
    }
    
//     echo("<pre>"); print_r($line); echo("</pre>");
    
    mysqli_free_result( $query_response );

    return [$line['name'], $line['typ'], $line['pricePerQuantity'], $line['unit'], $line['image1'], $line['image2'], $line['image3'], $line['waxAmount'], $line['waxType']];
}



function getDbDonation(){
    global $db_link;
    
    $sql = "SELECT donation FROM basket_various";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
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

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line['total'];
}


function getDbBookingId(){
    global $db_link;
    
    $sql = "SELECT bookingId FROM basket_various";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line['bookingId'];
}



function addToBasket($id, $quantity, $price, $text) {
    global $db_link;

    // TODO sanetize
       
    $sql = "INSERT INTO `basket`
        (`articleId`, `quantity`, `price`, `text`) 
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
        errorLog("SQL Error: Failed to add article $id to basket: $sql");
        return false;
    }
}



function getArticleIdInBasket($basketEntryId) {
    global $db_link;

    $sql = "SELECT `articleId` FROM basket WHERE `basketEntryId`='$basketEntryId'"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line['articleId'];
}



function updateArticleQuantityInBasket($basketEntryId, $quantity) {
    global $db_link;

    $sql = "UPDATE `basket` SET `quantity`='$quantity' WHERE `basketEntryId`='$basketEntryId'"; 
    
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
        errorLog("SQL Error: Failed to update $id in basket: $sql");
        return false;
    }
}



function updateArticlePriceInBasket($basketEntryId, $price) {
    global $db_link;

    $sql = "UPDATE `basket` SET `price`='$price' WHERE `basketEntryId`='$basketEntryId'"; 
    
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
        errorLog("SQL Error: Failed to update $id in basket: $sql");
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
        errorLog("SQL Error: Failed to update donation in basket: $sql");
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
        errorLog("SQL Error: Failed to update total in basket: $sql");
        return false;
    }
}



function updateBookingIdInBasket($bookingId) {
    global $db_link;

    $sql = "UPDATE `basket_various` SET `bookingId`='$bookingId' "; 
    
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
        sql_transaction_logger("-- [ERROR] Failed to update bookingId in basket: $sql");
        errorLog("SQL Error: Failed to update bookingId in basket: $sql");
        return false;
    }
}



function deleteFromBasket($basketEntryId) {
    global $db_link;

    $sql = "DELETE FROM `basket` WHERE `basketEntryId`='$basketEntryId'";

    
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
    }
    else { // fail
//         echo('Invalid MySQL request: ' . mysqli_error($db_link) . "<br>");
        sql_transaction_logger("-- [ERROR] Failed to delete article $basketEntryId from basket: $sql");
        errorLog("SQL Error: Failed to delete article $basketEntryId from basket: $sql");
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
    while ($line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC))
    {
        list($line['name'], $line['type'], $line['pricePerQuantity'], $line['unit'], $line['image1'], $line['image2'], $line['image3']) = getDbArticleData($line['articleId']);
        
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
            errorLog("SQL Error: Failed to create column 'article_" . $articleId . "_quantity' in table booking: $sql");
            return false;
        }
    }
    
    return true;
}




function bookingsCreateId() {
    global $db_link;
    
    // create entry
    $sql = "INSERT INTO bookings (`bookingId`) values (null)"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }
    
    $bookingId = bookingsGetLastId();
    
    // update date/time with current date/time
    $sql = "UPDATE `bookings`
            SET `date`='" . date("Y-m-d") . "', `time`='" . date("H:i:s") . "'
            WHERE `bookingId`='$bookingId'"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }
        
    return $bookingId;
}


function bookingsGetLastId() {
    global $db_link;
    
    $sql = "SELECT `bookingId` FROM bookings ORDER BY `bookingId` DESC LIMIT 1"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );
    
    if($line == ""){ //empty table
        return 0;
    }
    
    return $line['bookingId'];
}




function moveBasketToBooking($bookingId, $serializedBasket, $donation, $total, $paymentMethod) {
    global $db_link;

    // TODO sanetize

	//echo("moveBasketToBooking, $paymentMethod\n");
       
    $sql = "UPDATE `bookings`
            SET `booking`='$serializedBasket', `donation`='$donation', `total`='$total', `paymentMethod`='$paymentMethod'
            WHERE `bookingId`='$bookingId'"; 
       
	if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
    }
    else { // fail
        sql_transaction_logger("-- [ERROR] Failed to add donation and total to bookings: $sql");
        errorLog("SQL Error: Failed to add donation and total to bookings: $sql");
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

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );
        
    $booking = array();
    
    $booking['date'] = $line['date'];
    $booking['time'] = $line['time'];
    $booking['donation'] = $line['donation'];
    $booking['total'] = $line['total'];
    $booking['paymentMethod'] = $line['paymentMethod'];

    $booking['articles'] = unserialize($line['booking']);

    
//     print_r($booking);    

    return $booking;
}


function getBookingIdsOfDate($date, $invertDateFilter) {
    global $db_link;
    
    if( $invertDateFilter == false) { // return bookings of set date
        $sql = "SELECT bookingId FROM bookings WHERE date = '$date' ORDER BY 'bookingId' ASC"; 
    }
    else {// return bookings of all but set date
        $sql = "SELECT bookingId FROM bookings WHERE date != '$date' ORDER BY 'bookingId' ASC"; 
    }
    
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }
    
    $lines = array();
    while ($line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC))
    {
        $lines[] = $line['bookingId'];
    } 
    mysqli_free_result( $query_response );
    
    return $lines;
}


/* Returns all dates with at least one booking on that day
 * On multiple bookings on a day, the day is only returned once
 */
function getBookingDatesOfYear($year) {
    global $db_link;
   
    $nextYear = $year + 1;
    
//     echo("$year - $nextYear");
    
    $sql = "SELECT date FROM `bookings` WHERE date between date('$year-01-01') and date('$nextYear-01-01') group by date order by date DESC"; 
    
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }
    
    $lines = array();
    while ($line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC))
    {
        $lines[] = $line['date'];
//         echo($line['date'] . "<br>");
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
        errorLog("SQL Error: Failed to drop basket: $sql");
        return false;
    }
}


function dbCheckBasketIsEmpty() {
    global $db_link;
       
    $sql = "SELECT COUNT(*) FROM basket"; 

    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );
        
//     print_r( $line);

    if($line['COUNT(*)'] != 0) { // no articles in basket
        return false;
    }
    
    if (getDbDonation() != 0) { // no donation in basket
        return false;
    }
    
    if (getDbTotal() != 0) { // no total in basket
        return false;
    }
    
    return true;
}



function getBibleVerse() {
    global $db_link;
    
    $sql = "SELECT * FROM bible_verses ORDER BY RAND() LIMIT 1";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die('Invalid MySQL request: ' . mysqli_error($db_link));
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line;
}

    
?>
