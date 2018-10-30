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
        $bookingIds = getBookingIdsOfDate($date, false);
        foreach($bookingIds as $bookingId) { // a booking
            $booking = getBooking($bookingId);
            foreach ($booking['articles'] as $articleId => $article) { // articles
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
        
        ksort($articles, SORT_STRING);    
//         echo("<pre>");
//         print_r($articles);
?>

<?
        $sales = 0;
        foreach($articles as $article) {
            $sales += $article['price'];
        }

        echo("<h3> Tages-Umsatz: CHF ". roundMoney($sales) . "</h3><p></p>\n");
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
        
            echo("<tr><td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['price']) . "</td></tr>\n");
        }
?>
        </table>
      <p><br></p>
<?
    }
?>
    
    
    
      
      
<a name=year></a><h1><? echo(exportCsvButton('year')); ?>Auswertung ganzes Jahr</h1>
<?
    $articles = array();
    
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
                $articles[$id]['price'] += $article['price'];
                $articles[$id]['unit'] = $article['unit'];
                $articles[$id]['type'] = $article['type'];
            }
        }
    }
    
    ksort($articles, SORT_STRING);    
//     print_r($articles);
?>
  
<?
    $sales = 0;
    foreach($articles as $article) {
        $sales += $article['price'];
    }

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
    
        echo("<tr><td>" . $custom . $article['text'] . "</td><td>" . number_format($article['quantity'], 0, ".", "'") . " " . $article['unit'] . "</td><td>CHF " . roundMoney($article['price']) . "</td></tr>\n");
    }
?>
        </table>
      
        
    </div>

    <p>*) Freie Eingabe eines Artikels</p>


<?
include "$root/framework/footer.php"; 
?>
    
