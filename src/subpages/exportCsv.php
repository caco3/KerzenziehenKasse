<?
// echo("<pre>");

$root="..";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

if($_GET['id']) {
    $id = $_GET['id'];
}
else {
    die("Missing id!");
}

$file = "Kerzenziehen-Export - " . date("Y-m-d__H-i-s") . ".csv";

$content = "";

if ($id == 'year') {        
    $formatedDate = date("Y");
}
else { // a day
    $date = $id;
    $timestamp = strtotime($date);
    $formatedDate = $germanDayOfWeek[date("N", $timestamp)] . ", " . date("d. ", $timestamp) . $germanMonth[date("m", $timestamp) - 1] . date(". Y", $timestamp);
}


// Create list of all available products, so all exports have the same order
$articles = array();

$products = getDbProducts("wachs");
// print_r($products);
foreach($products as $product) {
    $articles[$product['articleId']]['text'] = $product['name'];
}

$products = getDbProducts("guss");
foreach($products as $product) {
    $articles[$product['articleId']]['text'] = $product['name'];
}

// print_r($articles);


if ($id != 'year') { // a day
    $bookingIds = getBookingIdsOfDate($date, false);
    foreach($bookingIds as $bookingId) { // a booking
        $booking = getBooking($bookingId);
        foreach ($booking['articles'] as $articleId => $article) { // articles
    //         print_r($article);
            if($article['type'] == "normal") { // normal article   
                $id = $articleId;
            }
            else { // custom article       
                $id = $article['text'];
            }
                    
            $articles[$id]['text'] = $article['text'];
            $articles[$id]['quantity'] += $article['quantity'];
            $articles[$id]['price'] += $article['price'];
            $articles[$id]['unit'] = $article['unit'];
            $articles[$id]['type'] = $article['type'];
        }
    }
    
    // print_r($articles);

    $sales = 0;
    foreach($articles as $article) {
        $sales += $article['price'];
    }

    $content .= "Export für:;$formatedDate;Total [CHF]:;" . roundMoney($sales) . "\n\n";
    $content .= "Artikel;Menge;Einheit;Betrag [CHF]\n";

    foreach($articles as $articleId => $article) {
        if ($article['type'] == "custom") { 
            $custom = "*) ";
        }
        else {
            $custom = ""; 
        }

        $content .= $custom . $article['text'] . ";" . number_format($article['quantity'], 0, ".", "'") . ";" . $article['unit'] . ";" . roundMoney($article['price']) . "\n";
    }
}
else { // the whole year    
    $bookingDatesOfCurrentYear = getBookingDatesOfCurrentYear();
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $bookingIds = getBookingIdsOfDate($date, false);
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
            foreach ($booking['articles'] as $articleId => $article) { // articles
        //         print_r($article);
                if($article['type'] == "normal") { // normal article   
                    $id = $articleId;
                }
                else { // custom article       
                    $id = $article['text'];
                }
                        
                $articles[$id]['text'] = $article['text'];
                $articles[$id]['quantity'] += $article['quantity'];
                $articles[$id]['price'] += $article['price'];
                $articles[$id]['unit'] = $article['unit'];
                $articles[$id]['type'] = $article['type'];
            }
        }
    }
        
    // print_r($articles);

    $sales = 0;
    foreach($articles as $article) {
        $sales += $article['price'];
    }

    $content .= "Export für:;$formatedDate;Total [CHF]:;" . roundMoney($sales) . "\n\n";
    $content .= "Artikel;Menge;Einheit;Betrag [CHF]\n";

    foreach($articles as $articleId => $article) {
        if ($article['type'] == "custom") { 
            $custom = "*) ";
        }
        else {
            $custom = ""; 
        }

        $content .= $custom . $article['text'] . ";" . number_format($article['quantity'], 0, ".", "'") . ";" . $article['unit'] . ";" . roundMoney($article['price']) . "\n";
    }
}

$content .= "\n\n*) Freie Eingabe eines Artikels\n";



header("Content-Disposition: attachment; filename=\"$file\"");
header("Content-Type: application/text");
header("Content-Length: " . strlen($content));
   
echo($content);

exit(); 

?>