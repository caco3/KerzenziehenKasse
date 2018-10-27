<? 
$root=".";
include "$root/framework/header.php";

// Today
$today = date("Y-m-d");
$todayDE = date("d. ") . $germanMonth[date("m") - 1] . date(". Y");

?>
    <div id="body">
      <h1>Auswertung (<? echo($todayDE); ?> )</h1>   
<!--       <pre> -->
      
      
      
<?
    $summary = array();
    $bookingIds = getBookingIdsOfDate($today, false);
    foreach($bookingIds as $bookingId) { // a booking
        $booking = getBooking($bookingId);
        foreach ($booking['articles'] as $articleId => $article) { // articles
            $summary[$articleId]['text'] = $article['text'];
            $summary[$articleId]['quantity'] += $article['quantity'];
            $summary[$articleId]['price'] += $article['price'];
            $summary[$articleId]['unit'] = $article['unit'];
        }
    }
    
    ksort($summary, SORT_STRING);    
//     print_r($summary);
?>

<?
    $sales = 0;
    foreach($summary as $article) {
        $sales += $article['price'];
    }

    echo("<h2>Tages-Umsatz: CHF ". roundMoney($sales) . "</h2><p></p>\n");
?>


      <table id=bookingsTable>
      <tr><th>Artikel</th><th>Menge</th><th>Betrag</th></tr>
<?

    foreach($summary as $articleId => $article) {
        if (is_numeric($articleId)) { 
            $custom = "";
        }
        else {
            $custom = "*) "; 
        }
    
        echo("<tr><td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['price']) . "</td></tr>\n");
    }
?>
        </table>
         
      
      
      <p></p>
      
      
      <h1>Auswertung (Aktuelles Jahr)</h1>
<?
    $summary = array();
    
    $bookingDatesOfCurrentYear = getBookingDatesOfCurrentYear();
    foreach($bookingDatesOfCurrentYear as $date) {  // a day
        $bookingIds = getBookingIdsOfDate($date, false);
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
//             print_r($booking);
            foreach ($booking['articles'] as $articleId => $article) { // articles
                $summary[$articleId]['text'] = $article['text'];
                $summary[$articleId]['quantity'] += $article['quantity'];
                $summary[$articleId]['price'] += $article['price'];
                $summary[$articleId]['unit'] = $article['unit'];
            }
        }
    }
    
    ksort($summary, SORT_STRING);    
//     print_r($summary);
?>
  
<?
    $sales = 0;
    foreach($summary as $article) {
        $sales += $article['price'];
    }

    echo("<p></p><h2>Jahres-Umsatz: CHF ". roundMoney($sales) . "</h2><p></p>\n");
?>


      <table id=bookingsTable>
      <tr><th>Artikel</th><th>Menge</th><th>Betrag</th></tr>
<?

    foreach($summary as $articleId => $article) {
        if (is_numeric($articleId)) { 
            $custom = "";
        }
        else {
            $custom = "*) "; 
        }
    
        echo("<tr><td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['price']) . "</td></tr>\n");
    }
?>
        </table>
      
        
    </div>

    *) Freier Eingabe


<?
include "$root/framework/footer.php"; 
?>
    
