<? 
$root=".";
include "$root/framework/header.php";

$bookingDatesOfCurrentYear = getBookingDatesOfCurrentYear();
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
        echo("<a name=$date></a><h2>". exportCsvButton($date) . "$formatedDate</h2>");
        $articles = array();
        $donations = 0;
        $bookingIds = getBookingIdsOfDate($date, false);
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
//             echo("<pre>");
//             print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles
                if($article['type'] == "normal") { // normal article   
                    $id = $articleId;
                }
                else { // custom article       
                    $id = $article['text'];
                }
                
                $articles[$id]['text'] = $article['text'];
                $articles[$id]['quantity'] += $article['quantity'];
                $articles[$id]['price'] = $article['price']; // not summed up since it is per 1 pc.
                $articles[$id]['unit'] = $article['unit'];
                $articles[$id]['type'] = $article['type'];
            }
            $donations += $booking['donation'];
        }
        
        ksort($articles, SORT_STRING);    
//         echo("<pre>");
//         print_r($articles);
?>

<?
        $sales = 0;
        foreach($articles as $article) {
            $sales += $article['quantity'] * $article['price'];
        }
        $sales += $donations;

        echo("<p><br>Tages-Umsatz: CHF ". roundMoney($sales) . "<br><br></p>\n");
?>
        <table id=bookingsTable>
        <tr><th>Artikel</th><th>Menge</th><th>Betrag</th></tr>
<?

        foreach($articles as $articleId => $article) {
            if ($article['type'] == "custom") { 
                $custom = "*) ";
            }
            else {
                $custom = ""; 
            }
        
            echo("<tr><td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
        }
        
        echo("<tr><td>Spenden</td><td></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
?>
        </table>
      <p><br></p>
        <hr>
<?
    }
?>
    
    
    
      
      
<a name=year></a><h1><? echo(exportCsvButton('year')); ?>Auswertung ganzes Jahr</h1>
<?
    $articles = array();
    $donations = 0;
    
    $bookingDatesOfCurrentYear = getBookingDatesOfCurrentYear();
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $bookingIds = getBookingIdsOfDate($date, false);
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
//             print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles
                if($article['type'] == "normal") { // normal article   
                    $id = $articleId;
                }
                else { // custom article       
                    $id = $article['text'];
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
    
    ksort($articles, SORT_STRING);    
//     print_r($articles);
?>
  
<?
    $sales = 0;
    foreach($articles as $article) {
        $sales += $article['quantity'] * $article['price'];
    }
    $sales += $donations;
        
    echo("<p></p><h3>Jahres-Umsatz: CHF ". roundMoney($sales) . "</h3><p></p>\n");
?>


    <table id=bookingsTable>
    <tr><th>Artikel</th><th>Menge</th><th>Betrag</th></tr>
<?

    foreach($articles as $articleId => $article) {
        if ($article['type'] == "custom") { 
            $custom = "*) ";
        }
        else {
            $custom = ""; 
        }
    
        echo("<tr><td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['quantity'] * $article['price']) . "</td></tr>\n");
    }
    
    echo("<tr><td>Spenden</td><td></td><td>CHF " . roundMoney($donations) . "</td></tr>\n");
?>
    </table>
      
        
    </div>
    <p><br></p>
    <hr>
    <p>*) Freie Eingabe eines Artikels<br><br></p>


<?
include "$root/framework/footer.php"; 
?>
    
