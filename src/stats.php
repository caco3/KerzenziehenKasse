<? 
$root=".";
include "$root/framework/header.php";

$bookingDatesOfCurrentYear = getBookingDatesOfCurrentYear();

// get image for custom articles
$products = getDbProducts("custom", "articleId");
// print_r($products);
$customImage = $products[0]['image1'];

$statsPerDay = array();

?>
    <div id="body">
    <h1>Übersicht</h1>
    <ul>
<?
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $timestamp = strtotime($date);
        $formatedDate = $germanDayOfWeek[date("N", $timestamp)] . ", " . date("d. ", $timestamp) . $germanMonth[date("m", $timestamp) - 1] . date(". Y", $timestamp);
        echo("<li><a href=#$date>$formatedDate</a><br></li>\n");
    }

?>
    <li><a href=#year>Ganzes Jahr</a></li>
  </ul>
  
  
  
    
<h1>Auswertung pro Tag (aktuelles Jahr)</h1> 
<?
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $timestamp = strtotime($date);
        $formatedDate = $germanDayOfWeek[date("N", $timestamp)] . ", " . date("d. ", $timestamp) . $germanMonth[date("m", $timestamp) - 1] . date(". Y", $timestamp);
        echo("<a name=$date></a><h2>$formatedDate</h2>");
        $articles = array();
                
        // Create list of all available products, so all days have the same order
        $products = getDbProducts("wachs", "articleId");
//         print_r($products);
        foreach($products as $product) {
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['image'] = $product['image1'];
        }

        $products = getDbProducts("guss", "name");
        foreach($products as $product) {
//             echo("<pre>");
//             print_r($product);
            $articles[$product['articleId']]['text'] = $product['name'];
            $articles[$product['articleId']]['quantity'] = $product['quantity'];
            $articles[$product['articleId']]['unit'] = $product['unit'];
            $articles[$product['articleId']]['image'] = $product['image1'];
        }
        
        $donations = 0;
        $bookingIds = getBookingIdsOfDate($date, false);
        $customIds = 0;
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
//             echo("<pre>");
//             print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles
                if($article['type'] != "custom") { // normal article   
                    $id = $articleId;
                }
                else { // custom article       
                    $id = $article['text'] . "_$customIds";
                    $customIds++;
                }
                
                $articles[$id]['text'] = $article['text'];
                $articles[$id]['quantity'] += $article['quantity'];
                $articles[$id]['price'] = $article['price']; // not summed up since it is per 1 pc.
                $articles[$id]['unit'] = $article['unit'];
                $articles[$id]['type'] = $article['type'];
            }
            $donations += $booking['donation'];
        }   
//         echo("<pre>");
//         print_r($articles);
?>

<?
        $total = 0;
        foreach($articles as $article) {
            $total += $article['quantity'] * $article['price'];
        }
        $total += $donations;
?>
        <table id=bookingsTable>
        <tr><th>Artikel</th><th></th><th>Menge</th><th>Betrag</th></tr>
<?

        foreach($articles as $articleId => $article) {
            if ($article['quantity'] == 0) { // no sales for this article, ignore it 
                continue;
            }
            
            if ($article['type'] == "custom") { 
                $custom = "*) ";
                $article['image'] = $customImage;
            }
            else {
                $custom = ""; 
            }
        
            echo("<tr>");
            echo("<td><span class=tooltip><img class=articleImage src=images/articles/". $article['image'] . "><span><img src=images/articles/". $article['image'] . "></span></span></td>");
            echo("<td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
        }
        
        echo("<tr><td colspan=2>Spenden</td><td></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
        echo("<tr><td colspan=2><b>Total</b></td><td></td><td><b>CHF " . roundMoney10($total) . "</b></td></tr>\n");
?>
        </table>
        <p><br>CSV Export: <? echo(exportCsvButton($date)); ?> | *) Freie Eingabe eines Artikels</p>
      <p><br></p>
        <hr>
<?
    
        $statsPerDay[$date] = roundMoney10($total);
    }
?>
    
    
    
      
      
<a name=year></a><h1>Auswertung ganzes Jahr</h1>
<table>
<tr  style="vertical-align: top; padding: 0px;">
<td>
<?
    $articles = array();
    $donations = 0;
    
    // Create list of all available products, so all days have the same order
    $products = getDbProducts("wachs", "articleId");
    // print_r($products);
    foreach($products as $product) {
        $articles[$product['articleId']]['text'] = $product['name'];
        $articles[$product['articleId']]['quantity'] = $product['quantity'];
        $articles[$product['articleId']]['unit'] = $product['unit'];
        $articles[$product['articleId']]['image'] = $product['image1'];
    }

    $products = getDbProducts("guss", "name");
    foreach($products as $product) {
        $articles[$product['articleId']]['text'] = $product['name'];
        $articles[$product['articleId']]['quantity'] = $product['quantity'];
        $articles[$product['articleId']]['unit'] = $product['unit'];
        $articles[$product['articleId']]['image'] = $product['image1'];
    }
    
    $bookingDatesOfCurrentYear = getBookingDatesOfCurrentYear();
    $customIds = 0;
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $bookingIds = getBookingIdsOfDate($date, false);
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
//             echo("<pre>");
//             print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles
                if($article['type'] != "custom") { // normal article   
                    $id = $articleId;
                }
                else { // custom article      
                    $id = $article['text'] . "_$customIds";
                    $customIds++;
                }
                
                $articles[$id]['text'] = $article['text'];
                $articles[$id]['quantity'] += $article['quantity'];
                $articles[$id]['price'] = $article['price']; // not summed up since it is per 1 pc.
                $articles[$id]['unit'] = $article['unit'];
                $articles[$id]['type'] = $article['type'];
            }
            $donations += $booking['donation'];
        }
    }
       
//     print_r($articles);
?>
  
<?
    $total = 0;
    foreach($articles as $article) {
        $total += $article['quantity'] * $article['price'];
    }
    $total += $donations;
?>


    <table id=bookingsTable>
    <tr><th>Artikel</th><th></th><th>Menge</th><th>Betrag</th></tr>
<?

    foreach($articles as $articleId => $article) {
        if ($article['quantity'] == 0) { // no sales for this article, ignore it 
            continue;
        }
            
        if ($article['type'] == "custom") { 
            $custom = "*) ";
            $article['image'] = $customImage;
        }
        else {
            $custom = ""; 
        }
    
        echo("<tr>");
        echo("<td><span class=tooltip><img class=articleImage src=images/articles/". $article['image'] . "><span><img src=images/articles/". $article['image'] . "></span></span></td>");
        echo("<td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
    }
    
    echo("<tr><td colspan=2>Spenden</td><td></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
    echo("<tr><td colspan=2><b>Total</b></td><td></td><td><b>CHF " . roundMoney10($total) . "</b></td></tr>\n");
?>
    </table>
    <p><br>CSV Export: <? echo(exportCsvButton('year')); ?> | *) Freie Eingabe eines Artikels</p>
    </div>
</td>
<td>
<? include "$root/subpages/articlesChartYear.php"; ?><br>
<? 
    ksort($statsPerDay);
    include "$root/subpages/totalsChartYear.php"; 
?>
    </td>
</tr>
</table>
<?
include "$root/framework/footer.php"; 
?>
    
