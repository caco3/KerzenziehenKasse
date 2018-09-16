<? 
$root=".";
include "$root/framework/header.php";


// Today
$today = date("Y-m-d");
$todayDE = date("d. ") . $germanMonth[date("m") - 1] . date(". Y");

?>

    <script src="<? echo("$root/"); ?>/framework/bookings.js"></script>

    <div id="body">
      <h1>Buchungen Heute (<? echo($todayDE); ?>)</h1>
      <!--<p>Noch nicht implementiert</p>-->
      
      <table id=bookingsTable>
      <tr><th>Buchung</th><th>Zeit</th><th>Total</th><th>Spenden</th><th>Artikel</th><th></th></tr>
      <?
      
        $bookingIdsToday = getBookingIdsOfDate($today, false);
        arsort($bookingIdsToday); // sorting to show latest booking on top
        
//         echo("<pre>");
//         print_r($bookingIdsToday);
        
        foreach($bookingIdsToday as $bookingId) {
            $booking = getBooking($bookingId);
            $button = editButton($bookingId);
//             echo("<pre>");
//             print_r($booking);
            echo("<tr>");
            echo("<td>$bookingId</td>");
            echo("<td>" . $booking['time'] . "</td>");
            echo("<td>CHF " . number_format($booking['total'], 2) . "</td>");
            echo("<td>CHF " . number_format($booking['donation'], 2) . "</td>");
            
            echo("<td>");
            foreach($booking['articles'] as $article) {
                echo($article['quantity'] . " " . $article['unit'] . " " . $article['text'] . ", ");
            }
            
            echo("</td>");
            
            echo("<td>$button</td>");
            echo("</tr>\n");
        }        
      ?>
    </table>

    
    
    <p></p>
    <h1>Frühere Buchungen (nur aktuelles Jahr)</h1>
    <table id=bookingsTable>
    <tr><th>Buchung</th><th>Datum</th><th>Zeit</th><th>Total</th><th>Spenden</th><th>Artikel</th></tr>
    <?    
        $bookingIds = getBookingIdsOfDate($today, true);
        arsort($bookingIds); // sorting to show latest booking on top
        
        $previousFormatedDate = "";
        
//         echo("<pre>");
//         print_r($bookingIds);
        
        foreach($bookingIds as $bookingId) {
            $booking = getBooking($bookingId);
//             echo("<pre>");
//             print_r($booking); 
            $formatedDate = $germanDayOfWeek[strftime("%w", strtotime($booking['date']))] . ", " . 
                strftime("%d. ", strtotime($booking['date'])) . $germanMonth[strftime("%m", strtotime($booking['date'])) - 1] ;
            
            if( $formatedDate != $previousFormatedDate) {
//                 echo("<tr><td></td></tr><tr><td></td></tr>\n");
                $previousFormatedDate = $formatedDate;
            }
            else {            
                $formatedDate = "";
            }            

            echo("<tr>");
            echo("<td>$bookingId</td>");
            echo("<td><nobr>$formatedDate</nobr></td>");
            echo("<td>" . $booking['time'] . "</td>");
            echo("<td>CHF " . number_format($booking['total'], 2) . "</td>");
            echo("<td>CHF " . number_format($booking['donation'], 2) . "</td>");
            
            echo("<td>");
            foreach($booking['articles'] as $article) {
                echo($article['quantity'] . " " . $article['unit'] . " " . $article['text'] . ", ");
            }
            
            echo("</td>");            
            echo("</tr>\n");
        }    
    ?>
    </table>    
    
    
<?
include "$root/framework/footer.php"; 
?>
    
