<? 
$root=".";
include "$root/framework/header.php";

$date = date("Y-m-d");
$dateDE = date("d.m.Y");

?>
    <div id="body">
      <h1>Buchungen Heute (<? echo($dateDE); ?>)</h1>
      <!--<p>Noch nicht implementiert</p>-->
      
      <table id=bookingsTable>
      <tr><th>Buchung</th><th>Zeit</th><th>Total</th><th>Spenden</th><th>Artikel</th><th></th></tr>
      <?
      
        $bookingIdsToday = getBookingIdsOfDate($date);
        arsort($bookingIdsToday); // sorting to show latest booking on top
        
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




<?
include "$root/framework/footer.php"; 
?>
    
