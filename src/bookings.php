<? 
$root=".";
include "$root/framework/header.php";


// Today
$today = date("Y-m-d");
$todayDE = date("d.m.Y");

?>
    <div id="body">
      <h1>Buchungen Heute (<? echo($todayDE); ?>)</h1>
      <!--<p>Noch nicht implementiert</p>-->
      
      <table id=bookingsTable>
      <tr><th>Buchung</th><th>Zeit</th><th>Total</th><th>Spenden</th><th>Artikel</th><th></th></tr>
      <?
      
        $bookingIdsToday = getBookingIdsOfDate($today, false);
//         arsort($bookingIdsToday); // sorting to show latest booking on top
        
//         echo("<pre>");
//         print_r($bookingIdsToday);
        
        foreach($bookingIdsToday as $bookingId) {
            $booking = getBooking($bookingId);
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
            
            echo("<td>" . "edit" . "</td>");
            echo("</tr>\n");
        }        
      ?>
    </table>

    
    
    <p></p>
    <h1>Frühere Buchungen (nur aktuelles Jahr)</h1>
    <table id=bookingsTable>
    <tr><th>Buchung</th><th>Datum</th><th>Zeit</th><th>Total</th><th>Spenden</th><th>Artikel</th></tr>
    <?    
        $bookingIdsToday = getBookingIdsOfDate($today, true);
//         arsort($bookingIdsToday); // sorting to show latest booking on top
        
        $previousFormatedDate = "";
        
//         echo("<pre>");
//         print_r($bookingIdsToday);
        
        foreach($bookingIdsToday as $bookingId) {
            $booking = getBooking($bookingId);
//             echo("<pre>");
//             print_r($booking);
            $formatedDate = strftime("%A, %e %B", strtotime($booking['date']));
            
            if( $formatedDate != $previousFormatedDate) {
//                 echo("<tr><td></td></tr><tr><td></td></tr>\n");
                $previousFormatedDate = $formatedDate;
            }
            else {            
                $formatedDate = "";
            }
            

            echo("<tr>");
            echo("<td>$bookingId</td>");
            echo("<td>$formatedDate</td>");
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
    
