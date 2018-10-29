<pre><? 
$root="..";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

if($_GET['id']) {
    $date = $_GET['id'];
}
else {
    die("Missing id!");
}

$file = "Kerzenziehen-Export - " . date("Y-m-d__H-i-s") . ".csv";

$content = "";

$timestamp = strtotime($date);
$formatedDate = $germanDayOfWeek[date("N", $timestamp)] . ", " . date("d. ", $timestamp) . $germanMonth[date("m", $timestamp) - 1] . date(". Y", $timestamp);

// Create list of all available products, so all exports have the same order
$summary = array();

$products = getDbProducts("wachs");
// print_r($products);
foreach($products as $product) {
    $summary[$product['articleId']]['text'] = $product['name'];
}

$products = getDbProducts("guss");
foreach($products as $product) {
    $summary[$product['articleId']]['text'] = $product['name'];
}

// print_r($summary);


$bookingIds = getBookingIdsOfDate($date, false);
foreach($bookingIds as $bookingId) { // a booking
    $booking = getBooking($bookingId);
    foreach ($booking['articles'] as $articleId => $article) { // articles
        print_r($article);
        $summary[$articleId]['text'] = $article['text'];
        $summary[$articleId]['quantity'] += $article['quantity'];
        $summary[$articleId]['price'] += $article['price'];
        $summary[$articleId]['unit'] = $article['unit'];
    }
}
 
// print_r($summary);
?>

<?
$sales = 0;
foreach($summary as $article) {
    $sales += $article['price'];
}

$content .= "$formatedDate (Total: CHF $sales)\n\n";
$content .= "Artikel;Menge;Einheit;Betrag [CHF]\n";

foreach($summary as $articleId => $article) {
    if (is_numeric($articleId)) { 
        $custom = "";
    }
    else {
        $custom = ""; 
    }

    $content .= $custom . $article['text'] . ";" . number_format($article['quantity'], 0, ".", "'") . ";" . $article['unit'] . ";" . roundMoney($article['price']) . "\n";
}



/*
header("Content-Disposition: attachment; filename=\"$file\"");
header("Content-Type: application/text");
header("Content-Length: " . strlen($content));*/
   
echo($content);

exit(); 

?>
