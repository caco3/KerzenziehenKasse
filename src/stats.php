<? 
// TODO Refactor this page

$root=".";
include "$root/framework/header.php";

$year = date("Y");
$bookingDatesOfCurrentYear = getBookingDatesOfYear($year);

// get image for custom articles
$products = getDbProducts("custom", "articleId");
// print_r($products);
$customImage = $products[0]['image1'];

$statsPerDay = array();


/* Returns the total gruped per day for each day in the given year listed in the DB */
function getStatsPerDay($year) {
    $data = array();
    $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $donations = 0;
        $total = 0;                
        $articles = array();
        
        $bookingIds = getBookingIdsOfDate($date, false);
        
//         // Create list of all available products, so all days have the same order
//         $products = getDbProducts("wachs", "articleId");
//         // print_r($products);
//         foreach($products as $product) {
//             $articles[$product['articleId']]['text'] = $product['name'];
// //             $articles[$product['articleId']]['quantity'] = $product['quantity'];
// //             $articles[$product['articleId']]['unit'] = $product['unit'];
// //             $articles[$product['articleId']]['image'] = $product['image1'];
//             $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
//         }
// 
//         $products = getDbProducts("guss", "name");
//         foreach($products as $product) {
//             $articles[$product['articleId']]['text'] = $product['name'];
// //             $articles[$product['articleId']]['quantity'] = $product['quantity'];
// //             $articles[$product['articleId']]['unit'] = $product['unit'];
// //             $articles[$product['articleId']]['image'] = $product['image1'];
//             $articles[$product['articleId']]['pricePerQuantity'] = $product['pricePerQuantity'];
//         }
        
//         echo("<pre>");
//         print_r($articles);
        
        
        foreach($bookingIds as $bookingId) { // a booking
            
            
            $booking = getBooking($bookingId);
//             echo("<pre>");
//             print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles                
//                 $articles[$articleId]['text'] = $article['text'];
                $articles[$articleId]['quantity'] += $article['quantity'];
                $articles[$articleId]['price'] = $article['price']; // not summed up since it is per 1 pc.
//                 $articles[$articleId]['unit'] = $article['unit'];
//                 $articles[$articleId]['type'] = $article['type'];
            }
            $donations += $booking['donation'];
            $total += $booking['total'];
            
//             foreach($articles as $article) {
//                 $total += $article['quantity'] * $article['price'];
// //                 echo("<pre>");
// //                 print_r($article);
//             }
        }   

        
//         $total += $donations;
        $data[$date]['donations'] = $donations;
        $data[$date]['total'] = $total;
//         $data[$date]['articles'] = $articles;
    }
    
//     echo("<pre>");
//     print_r($data);
    ksort($data);
    return $data;
}






/* Shows the summary stats of a year */
function showSummaryOfYear($year) {
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
        
                
        $bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
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
        
        if ($total == 0) { // no stats for this year => return
            return;
        }
        
    ?>
        <a name=year></a><h1>Zusammenfassung Jahr <? echo($year); ?></h1>
        <table>
        <tr  style="vertical-align: top; padding: 0px;">
        <td>

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
        <p><br>CSV Export: <? echo(exportCsvButton('year')); ?></p>
        </div>
    </td>
    <td>
    <? 
    getStatsPerDay($year);
    
    
        include "$root/subpages/articlesChartYear.php"; ?>
    </td>
    <td>
    <? 
//         ksort($statsPerDay);
//         include "$root/subpages/totalsChartYear.php"; 
    ?>
        </td>
    </tr>
    </table>

    <?
}












?>
    <div id="body">
<!--     <h1>Übersicht</h1> -->
<!--     <ul> -->
<?
//     foreach($bookingDatesOfCurrentYear as $date) {  // a day
//         $timestamp = strtotime($date);
//         $formatedDate = $germanDayOfWeek[date("N", $timestamp)] . ", " . date("d. ", $timestamp) . $germanMonth[date("m", $timestamp) - 1] . date(". Y", $timestamp);
//         echo("<li><a href=#$date>$formatedDate</a><br></li>\n");
//     }

?>
<!--     <li><a href=#year>Ganzes Jahr (<? echo($year); ?>)</a></li> -->
<!--     <li><a href=#lastyear>Ganzes Jahr (<? echo($year - 1); ?>)</a></li> -->
<!--   </ul> -->
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  

  
  
  
  
<h1>Umsatz pro Tag und Jahr</h1> 
<?    
    $statsPerDay = array();
    for ($i = 0; $i <= 10; $i++) {
        $year = date("Y") - $i; 
        $stats = getStatsPerDay($year);
        if (count($stats) == 0) { // no stats for this year => return
            break;
        }
        $statsPerDay[$year] = $stats;
    }
    
    $totalPerDayAndYear = array(); // [day][year]
        
    for ($i = 0; $i <= 10; $i++) { // for each year
        $year = date("Y") - $i; 
        $dayIndex = 0;
        foreach($statsPerDay[$year] as $date => $data) { // for each day
            if ($dayIndex == 0) {
                $firstDay = $date; 
                $zeroOffset = date("z", strtotime($date));
            }
            $offset = date("z", strtotime($date)) - $zeroOffset;
            $dayIndex++;
            
//             $totalPerDayAndYear[$offset][$year]['total'] = $data['total']; 
//             $totalPerDayAndYear[$offset][$year]['date'] = $date; 
            $totalPerDayAndYear[$offset]['year'][$year]['total'] = $data['total']; 
            $totalPerDayAndYear[$offset]['year'][$year]['date'] = $date; 
            
//             $totalPerDayAndYear[$offset][$year]['formatedDate'] = $germanDayOfWeek[strftime("%w", strtotime($date))] .
//                     strftime(", %e. ", strtotime($date)) . $germanMonth[strftime("%m", strtotime($date)) - 1] . ". " . strftime("%Y", strtotime($date)); 

//             $totalPerDayAndYear[$offset][$year]['formatedDate'] = $germanDayOfWeek[strftime("%w", strtotime($date))]; 
            $totalPerDayAndYear[$offset]['formatedDate'] = $germanDayOfWeek[strftime("%w", strtotime($date))]; 
        }
    }    
    
/*    echo("<pre>");
    print_r($totalPerDayAndYear); */  
    include "$root/subpages/totalsChartYear.php"; 
?>  


  
<?
    // Show yearly summary
    for ($i = 0; $i <= 10; $i++) {
        $year = date("Y") - $i; 
        showSummaryOfYear($year);
    }
?>


<?
include "$root/framework/footer.php"; 
?>
    
