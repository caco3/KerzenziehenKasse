<?
// echo("<pre>");

$root="..";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config.php");
require_once("$root/config/config_generic.php");
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

if (strlen($id) == 4) { // year        
    $formatedDate = date("Y");
}
else { // a day
    $date = $id;
    $timestamp = strtotime($date);
    $formatedDate = $germanDayOfWeek[date("N", $timestamp)] . ", " . date("d. ", $timestamp) . $germanMonth[date("m", $timestamp) - 1] . date(". Y", $timestamp);
}


// Create list of all available products, so all exports have the same categories
$articles = array();

$products = getDbProducts("wachs", "articleId");
// print_r($products);
foreach($products as $product) {
    $articles[$product['articleId']]['text'] = strip_tags(html_entity_decode($product['name']));
//     $articles[$product['articleId']]['quantity'] = $product['quantity'];
    $articles[$product['articleId']]['unit'] = $product['unit'];
}

$products = getDbProducts("guss", "name");
foreach($products as $product) {
    $articles[$product['articleId']]['text'] = strip_tags(html_entity_decode($product['name']));
//     $articles[$product['articleId']]['quantity'] = $product['quantity'];
    $articles[$product['articleId']]['unit'] = $product['unit'];
}

// print_r($articles);


if ($id == "bookings") { // Bookings
// Booking ID, date, time, total, donation, paymentMethod, school, Parafin, bee, form 1, form 2, ...
//     echo("<pre>");
    
    $productList = [];
    
    // Header
    $content = "Buchungs-Nr,Datum,Zeit,Total [CHF],Spenden [CHF],Schule,Zahlungsart,";
    $products = getDbProducts("wachs", "articleId");
    foreach($products as $product) {
        $productList[$product['articleId']] = $product['name'];
        $content .= strip_tags(html_entity_decode($product['name'])) . " [" . $product['unit'] . "],";        
    }
    
    $products = getDbProducts("guss", "name");
    foreach($products as $product) {
        $productList[$product['articleId']] = $product['name'];
        $content .= strip_tags(html_entity_decode($product['name'])) . " [" . $product['unit'] . "],";
    }
    
    $products = getDbProducts("special", "name");
    foreach($products as $product) {
        $productList[$product['articleId']] = $product['name'];
        $content .= strip_tags(html_entity_decode($product['name'])) . " [" . $product['unit'] . "],";
    }
    
//     $products = getDbProducts("custom", "name");
//     foreach($products as $product) {
//         $productList[$product['articleId']] = $product['name'];
//         $content .= strip_tags(html_entity_decode($product['name'])) . ",";
//     }
    
    $content .= "\n";
  
    $bookingDatesOfCurrentYear = getBookingDatesOfYear(date("Y"));
    asort($bookingDatesOfCurrentYear);
    $donations = 0;
    $customIds = 0;
//     print_r($bookingDatesOfCurrentYear);
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $bookingIds = getBookingIdsOfDate($date, false);
//         print_r($bookingIds);
        foreach($bookingIds as $bookingId) { // a booking
//             echo("\n\n## booking $bookingId\n");
            $booking = getBooking($bookingId);
            
            if ($booking['total'] == 0) {  // Ignore empty bookings
                continue;
            }
            
//             echo($bookingId . "," . $booking['date'] ."," . $booking['time'] . "<br>");
            
            $content .= $bookingId . "," . $booking['date'] ."," . $booking['time'] ."," . $booking['total'] ."," . $booking['donation'] ."," . $booking['school'] ."," . $booking['paymentMethod'] .","; 
// echo("$bookingId<br>");
            foreach($productList as $productId => $productName) {
//                 echo($productId . ", " . gettype($booking['articles']));
                
                if (gettype($booking['articles']) == array() && array_key_exists($productId, $booking['articles'])) {
                    $content .= $booking['articles'][$productId]['quantity'];
                    
                }

                if (array_key_exists($productId, $booking['articles'])) {
                    $content .= $booking['articles'][$productId]["quantity"] . ",";  
                }
                else {
                    $content .= "0,"; 
                }

            }
            
            $content .= "\n";
        }
    }
//     echo($content);
}


else if (strlen($id) != 4) { // a day
    $bookingIds = getBookingIdsOfDate($date, false);
    $donations = 0;
    $customIds = 0;
    foreach($bookingIds as $bookingId) { // a booking
        $booking = getBooking($bookingId);
        foreach ($booking['articles'] as $articleId => $article) { // articles
    //         print_r($article);
//             if($article['type'] == "normal") { // normal article   
                $id = $articleId;
//             }
//             else { // custom article      
//                 $id = $article['text'] . "_$customIds";
//                 $customIds++;
//             }

            if (! array_key_exists($id, $articles)) {
                $articles[$id] = array();
                $articles[$id]['text'] = "";
                $articles[$id]['quantity'] = 0;
                $articles[$id]['price'] = 0;
                $articles[$id]['unit'] = 0;
                $articles[$id]['type'] = 0;
            }

            if (! array_key_exists("quantity", $articles[$id])) {
                $articles[$id]['quantity'] = 0;
            }

            $articles[$id]['text'] = strip_tags(html_entity_decode($article['text']));
            $articles[$id]['quantity'] += $article['quantity'];
            $articles[$id]['price'] = $article['price']; // not summed up since it is per 1 pc.
            $articles[$id]['unit'] = $article['unit'];
            $articles[$id]['type'] = $article['type'];
        }
        $donations += $booking['donation'];
    }
    
//     print_r($articles);

    $total = 0;
    foreach($articles as $key => $article) {
        if (array_key_exists('quantity', $article) and array_key_exists('price', $article)) {
            $total += $article['quantity'] * $article['price'];
        }
        else {
            $articles[$key]['quantity'] = 0;
            $articles[$key]['price'] = 0;
        }
    }

    $total += $donations;

    $content .= "Export für:;$formatedDate;Total [CHF]:;" . roundMoney10($total) . "\n\n";
    $content .= "Artikel;Menge;Einheit;Betrag [CHF]\n";

    foreach($articles as $articleId => $article) {
        if (array_key_exists('type', $article) and $article['type'] == "custom") {
            $custom = "*) ";
        }
        else {
            $custom = ""; 
        }

        $content .= $custom . $article['text'] . ";" . number_format($article['quantity'], 0, ".", "") . ";" . $article['unit'] . ";" . roundMoney($article['price'] * $article['quantity']) . "\n";
    }
    $content .= "Spenden;;;$donations";
}


else { // the whole year    
    $bookingDatesOfCurrentYear = getBookingDatesOfYear($id);
    $donations = 0;
    $customIds = 0;
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $bookingIds = getBookingIdsOfDate($date, false);
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
            foreach ($booking['articles'] as $articleId => $article) { // articles
                 //print_r($article);
//                 if($article['type'] == "normal") { // normal article   
                    $id = $articleId;
//                 }
//                 else { // custom article   
//                     $id = $article['text'] . "_$customIds";
//                     $customIds++;
//                 }

                if (! array_key_exists($id, $articles)) {
                    $articles[$id] = array();
                    $articles[$id]['text'] = "";
                    $articles[$id]['quantity'] = 0;
                    $articles[$id]['price'] = 0;
                    $articles[$id]['unit'] = 0;
                    $articles[$id]['type'] = 0;
                }

                if (! array_key_exists("quantity", $articles[$id])) {
                    $articles[$id]['quantity'] = 0;
                }
                        
                $articles[$id]['text'] = strip_tags(html_entity_decode($article['text']));
                $articles[$id]['quantity'] += $article['quantity'];
                $articles[$id]['price'] = $article['price']; // not summed up since it is per 1 pc.
                $articles[$id]['unit'] = $article['unit'];
                $articles[$id]['type'] = $article['type'];
            }
            $donations += $booking['donation'];
        }
    }
        
    // print_r($articles);

    $total = 0;
    foreach($articles as $key => $article) {
        if (array_key_exists('quantity', $article) and array_key_exists('price', $article)) {
            $total += $article['quantity'] * $article['price'];
        }
        else {
            $articles[$key]['quantity'] = 0;
            $articles[$key]['price'] = 0;
        }
    }
    $total += $donations;

    $content .= "Export für:;$formatedDate;Total [CHF]:;" . roundMoney10($total) . "\n\n";
    $content .= "Artikel;Menge;Einheit;Betrag [CHF]\n";

    foreach($articles as $articleId => $article) {
        if (array_key_exists('type', $article) and $article['type'] == "custom") {
            $custom = "*) ";
        }
        else {
            $custom = ""; 
        }

        $content .= $custom . $article['text'] . ";" . number_format($article['quantity'], 0, ".", "") . ";" . $article['unit'] . ";" . roundMoney($article['price'] * $article['quantity']) . "\n";
    }    
    $content .= "Spenden;;;$donations";
}

header("Content-Disposition: attachment; filename=\"$file\"");
header("Content-Type: application/text");
header("Content-Length: " . strlen($content));
   
echo($content);

exit(); 

?>
