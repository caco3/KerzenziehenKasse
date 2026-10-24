<?
//Make sure there are no characters before the opening PHP-Tag above. Else the Export will be mallformed, see http://stackoverflow.com/questions/29028190/unable-to-write-on-the-first-line-of-csv-using-php

$db_link = NULL;

/**
 * Establishes the MySQL connection using the MYSQL_* constants and stores
 * it in the global $db_link. Dies on connection failure.
 *
 * @return void
 */
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



/**
 * Returns all articles of a given type from the database, ordered by a
 * column. Missing 'quantity'/'price' keys are filled with 0.
 *
 * @param string $type - Article type filter (articles.type).
 * @param string $orderByColumn - Column name to order the result by.
 * @return array List of article rows as associative arrays.
 */
function getDbProducts($type, $orderByColumn) {
    global $db_link;
    
    $sql = "SELECT * FROM articles WHERE type = '$type' ORDER by `$orderByColumn` ASC";
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    while ($line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC))
    {
        $lines[] = $line;
    } 
    mysqli_free_result( $query_response );
    
    // Add missing "Quantity" values
    foreach($lines as $key => $data) {
        if (! array_key_exists("quantity", $data)) {
            $lines[$key]['quantity'] = 0;
        }
        if (! array_key_exists("price", $data)) {
            $lines[$key]['price'] = 0;
        }
    }

     //echo("<pre>");
     //print_r($lines);

    return $lines;
}


/**
 * Returns all articles of a given type and subtype from the database,
 * ordered by a column.
 *
 * @param string $type - Article type filter (articles.type).
 * @param string $orderByColumn - Column name to order the result by.
 * @param string $subType - Article subtype filter (articles.subtype).
 * @return array List of article rows as associative arrays.
 */
function getDbProductsEx($type, $orderByColumn, $subType) {
    global $db_link;
    
    $sql = "SELECT * FROM articles WHERE (type = '$type' AND subtype = '$subType') ORDER by `$orderByColumn` ASC";
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
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



/**
 * Returns the data of a single article.
 * Note: only provide real article IDs (but not custom ones).
 *
 * @param int|string $id - The articleId to look up.
 * @return array List with name, type, pricePerQuantity, unit, image1-3,
 *               waxAmount and waxType.
 */
function getDbArticleData($id){
    global $db_link;
    
    $sql = "SELECT * FROM articles WHERE articleId = '$id'";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    
    
    if($line['type'] == 'custom'){ // custom article
        $line['unit'] = "Stk."; // TODO: replace by unit in table
    }
    
//     echo("<pre>"); print_r($line); echo("</pre>");
    
    mysqli_free_result( $query_response );

    return [$line['name'], $line['type'], $line['pricePerQuantity'], $line['unit'], $line['image1'], $line['image2'], $line['image3'], $line['waxAmount'], $line['waxType']];
}



/**
 * Returns the donation amount currently stored in the basket.
 *
 * @return string|float The donation value from basket_various.
 */
function getDbDonation(){
    global $db_link;
    
    $sql = "SELECT donation FROM basket_various";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line['donation'];
}



/**
 * Returns the total amount currently stored in the basket.
 *
 * @return string|float The total value from basket_various.
 */
function getDbTotal(){
    global $db_link;
    
    $sql = "SELECT total FROM basket_various";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line['total'];
}


/**
 * Returns the booking ID currently associated with the basket
 * (used when editing an existing booking).
 *
 * @return string|int The bookingId from basket_various.
 */
function getDbBookingId(){
    global $db_link;
    
    $sql = "SELECT bookingId FROM basket_various";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line['bookingId'];
}



/**
 * Inserts a new entry into the basket table.
 *
 * @param int|string $id - ID of the article to add.
 * @param int|float|string $quantity - Quantity or weight.
 * @param float|string $price - Price of the entry.
 * @param string $text - Free text for the entry.
 * @return bool True on success, false on failure.
 */
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
        sql_transaction_logger("-- [ERROR] Failed to add article $id to basket: $sql");
        errorLog("SQL Error: Failed to add article $id to basket: $sql");
        return false;
    }
}



/**
 * Returns the articleId belonging to a basket entry.
 *
 * @param int|string $basketEntryId - ID of the basket entry.
 * @return int|string The articleId of the entry.
 */
function getArticleIdInBasket($basketEntryId) {
    global $db_link;

    $sql = "SELECT `articleId` FROM basket WHERE `basketEntryId`='$basketEntryId'"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line['articleId'];
}



/**
 * Updates the quantity of a basket entry.
 *
 * @param int|string $basketEntryId - ID of the basket entry.
 * @param int|float|string $quantity - New quantity.
 * @return bool True on success, false on failure.
 */
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
        sql_transaction_logger("-- [ERROR] Failed to update $id in basket: $sql");
        errorLog("SQL Error: Failed to update $id in basket: $sql");
        return false;
    }
}



/**
 * Updates the price of a basket entry.
 *
 * @param int|string $basketEntryId - ID of the basket entry.
 * @param float|string $price - New price.
 * @return bool True on success, false on failure.
 */
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
        sql_transaction_logger("-- [ERROR] Failed to update $id in basket: $sql");
        errorLog("SQL Error: Failed to update $id in basket: $sql");
        return false;
    }
}



/**
 * Updates the donation amount stored in basket_various.
 *
 * @param float|string $money - New donation amount.
 * @return bool True on success, false on failure.
 */
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
        sql_transaction_logger("-- [ERROR] Failed to update donation in basket: $sql");
        errorLog("SQL Error: Failed to update donation in basket: $sql");
        return false;
    }
}



/**
 * Updates the total amount stored in basket_various.
 *
 * @param float|string $money - New total amount.
 * @return bool True on success, false on failure.
 */
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
        sql_transaction_logger("-- [ERROR] Failed to update total in basket: $sql");
        errorLog("SQL Error: Failed to update total in basket: $sql");
        return false;
    }
}



/**
 * Stores a booking ID in basket_various (marks the basket as editing an
 * existing booking).
 *
 * @param int|string $bookingId - The booking ID to store.
 * @return bool True on success, false on failure.
 */
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
        sql_transaction_logger("-- [ERROR] Failed to update bookingId in basket: $sql");
        errorLog("SQL Error: Failed to update bookingId in basket: $sql");
        return false;
    }
}



/**
 * Deletes a single entry from the basket table.
 *
 * @param int|string $basketEntryId - ID of the basket entry to delete.
 * @return bool True on success, false on failure.
 */
function deleteFromBasket($basketEntryId) {
    global $db_link;

    $sql = "DELETE FROM `basket` WHERE `basketEntryId`='$basketEntryId'";

    
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
    }
    else { // fail
        sql_transaction_logger("-- [ERROR] Failed to delete article $basketEntryId from basket: $sql");
        errorLog("SQL Error: Failed to delete article $basketEntryId from basket: $sql");
        return false;
    }
}



/**
 * Returns the basket content, enriched with article data.
 * Without donation or total!
 *
 * @return array List of basket entries as associative arrays.
 */
function getDbBasket() {
    global $db_link;
    
    $sql = "SELECT * FROM basket";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
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




/**
 * Creates per-article columns (article_<id>_<column>) in the bookings
 * table if they do not exist yet.
 *
 * @param int|string $articleId - ID of the article the columns belong to.
 * @param string[] $columns - Column suffixes to create ('cost', 'quantity', 'text', ...).
 * @return bool True on success, false on failure.
 */
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




/**
 * Creates a new (empty) booking entry and sets its date/time to now.
 *
 * @return int|string The bookingId of the newly created booking.
 */
function bookingsCreateId() {
    global $db_link;
    
    // create entry
    $sql = "INSERT INTO bookings (`bookingId`, `date`, `time`, `booking`, `donation`, `total`, `extra`) values (null, '" . date("Y-m-d") . "', '" . date("H:i:s") . "', '', 0, 0, \"\")";
    // echo("SQL: $sql");
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }
    
    $bookingId = bookingsGetLastId();
    
    // update date/time with current date/time
    $sql = "UPDATE `bookings`
            SET `date`='" . date("Y-m-d") . "', `time`='" . date("H:i:s") . "'
            WHERE `bookingId`='$bookingId'"; 
    // echo("SQL: $sql");
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }
        
    return $bookingId;
}


/**
 * Returns the highest bookingId in the bookings table.
 *
 * @return int|string The last bookingId, or 0 if the table is empty.
 */
function bookingsGetLastId() {
    global $db_link;
    
    $sql = "SELECT `bookingId` FROM bookings ORDER BY `bookingId` DESC LIMIT 1"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );
    
    if($line == ""){ //empty table
        return 0;
    }
    
    return $line['bookingId'];
}




/**
 * Stores the serialized extra data (Schulklasse, Leiter/in) in
 * basket_various.
 *
 * @param string|null $extra - Serialized extra data, or null to clear it.
 * @return bool True on success, false on failure.
 */
function updateExtraInBasket($extra) {
    global $db_link;
    if ($extra === null) {
        $sql = "UPDATE `basket_various` SET `extra` = \"\"";
    } else {
        $extraEscaped = mysqli_real_escape_string($db_link, $extra);
        $sql = "UPDATE `basket_various` SET `extra` = '$extraEscaped'";
    }
    if(mysqli_query($db_link, $sql)){
        sql_transaction_logger($sql);
        return true;
    }
    else { // fail
        sql_transaction_logger("-- [ERROR] Failed to update extra in basket: $sql");
        errorLog("SQL Error: Failed to update extra in basket: $sql");
        return false;
    }
}

/**
 * Reads and decodes the extra data stored in basket_various. Tries
 * unserialize() first and falls back to JSON decoding.
 *
 * @return array The decoded extra data as associative array, empty when unset.
 */
function getExtraFromBasket() {
    global $db_link;
    $sql = "SELECT `extra` FROM `basket_various`";
    $query_response = mysqli_query($db_link, $sql);
    if (!$query_response) {
        return null;
    }
    $row = mysqli_fetch_assoc($query_response);
    mysqli_free_result($query_response);
    if (!empty($row['extra'])) {
        $raw = $row['extra'];
        errorLog("getExtraFromBasket raw extra: " . $raw);
        $extra = @unserialize($raw);
        if ($extra === false && $raw !== serialize(false)) {
            // unserialize failed and not because the value is boolean false
            errorLog("getExtraFromBasket unserialize failed, trying JSON decode");
            $extra = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                errorLog("getExtraFromBasket JSON decode failed: " . json_last_error_msg());
                return [];
            }
        }
        return is_array($extra) ? $extra : [];
    }
    return [];
}

/**
 * Serializes and stores an extra-data array in the basket.
 *
 * @param array $extraArray - Extra data (e.g. 'schulklasse', 'leiter').
 * @return bool True on success, false when the input is not an array or the update fails.
 */
function setExtraInBasket($extraArray) {
    global $db_link;
    if (!is_array($extraArray)) {
        return false;
    }
    $serialized = serialize($extraArray);
    return updateExtraInBasket($serialized);
}


/**
 * Writes the basket contents, donation, total and payment method into an
 * existing booking row.
 *
 * @param int|string $bookingId - ID of the booking to update.
 * @param string $serializedBasket - Serialized basket contents.
 * @param float|string $donation - Donation amount.
 * @param float|string $total - Total amount.
 * @param string $paymentMethod - Payment method ('cash', 'twint', 'invoice').
 * @param string|null $extra - Optional serialized extra data.
 * @return bool True on success, false on failure.
 */
function moveBasketToBooking($bookingId, $serializedBasket, $donation, $total, $paymentMethod, $extra = null) {
    global $db_link;

    // TODO sanetize

	//echo("moveBasketToBooking, $paymentMethod\n");
       
    $sql = "UPDATE `bookings`
            SET `booking`='$serializedBasket', `donation`='$donation', `total`='$total', `paymentMethod`='$paymentMethod'";
    if ($extra !== null) {
        $extraEscaped = mysqli_real_escape_string($db_link, $extra);
        $sql .= ", `extra`='$extraEscaped'";
    }
    $sql .= " WHERE `bookingId`='$bookingId'"; 
       
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




/**
 * Returns a single booking including its unserialized article list.
 *
 * @param int|string $bookingId - ID of the booking to load.
 * @return array Associative array with date, time, donation, total,
 *               paymentMethod, school, extra and 'articles'.
 */
function getDbBooking($bookingId) {
    global $db_link;
    
    $sql = "SELECT * FROM bookings WHERE bookingId = '$bookingId'"; 
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );
        
    $booking = array();
    
    $booking['date'] = $line['date'];
    $booking['time'] = $line['time'];
    $booking['donation'] = $line['donation'];
    $booking['total'] = $line['total'];
    $booking['paymentMethod'] = $line['paymentMethod'];
    $booking['school'] = $line['school'];
    $booking['extra'] = $line['extra'];

    $booking['articles'] = unserialize($line['booking']);

    
//     print_r($booking);    

    return $booking;
}


/**
 * Returns the booking IDs of one specific date, or of all dates except
 * the given one when inverted.
 *
 * @param string $date - Date in 'Y-m-d' format.
 * @param bool $invertDateFilter - False: bookings of that date; true: all other dates.
 * @return array List of bookingIds.
 */
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
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }
    
    $lines = array();
    while ($line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC))
    {
        $lines[] = $line['bookingId'];
    } 
    mysqli_free_result( $query_response );
    
    return $lines;
}


/**
 * Returns all dates with at least one booking on that day within a year.
 * On multiple bookings on a day, the day is only returned once.
 *
 * @param int|string $year - The year to query.
 * @return array List of dates ('Y-m-d'), newest first.
 */
function getBookingDatesOfYear($year) {
    global $db_link;
   
    $nextYear = $year + 1;
    
//     echo("$year - $nextYear");
    
    $sql = "SELECT date FROM `bookings` WHERE date between date('$year-01-01') and date('$nextYear-01-01') group by date order by date DESC"; 
    
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
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



/**
 * Empties the basket table (TRUNCATE).
 *
 * @return bool True on success, false on failure.
 */
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


/**
 * Checks whether the basket is completely empty: no entries, no donation
 * and no total stored.
 *
 * @return bool True when the basket is empty, false otherwise.
 */
function dbCheckBasketIsEmpty() {
    global $db_link;
       
    $sql = "SELECT COUNT(*) FROM basket"; 

    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
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



/**
 * Returns a random bible verse from the bible_verses table.
 *
 * @return array Associative array of the selected verse row.
 */
function getBibleVerse() {
    global $db_link;
    
    $sql = "SELECT * FROM bible_verses ORDER BY RAND() LIMIT 1";  
    $query_response = mysqli_query($db_link, $sql );
    if ( ! $query_response )
    {
      die(__FUNCTION__ . ' Invalid MySQL request: ' . mysqli_error($db_link) . "! Caused by query '" .$sql . "'");
    }

    $line = mysqli_fetch_array( $query_response, MYSQLI_ASSOC);
    mysqli_free_result( $query_response );

    return $line;
}

    
?>
