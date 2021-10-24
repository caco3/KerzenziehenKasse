<? 
$root=".";
include "$root/framework/header.php";


// Today
$today = date("Y-m-d");
$todayDE = date("d. ") . $germanMonth[date("m") - 1] . date(". Y");

?>

    <script src="<? echo("$root/"); ?>/framework/bookings.js"></script>

    <div id="body">
      <h2>Buchungen Heute (<? echo($todayDE); ?>)</h2>
      <!--<p>Noch nicht implementiert</p>-->
      
      <table id=bookingsTable>
      <tr><th>Buchung</th><th>Zeit</th><th>Total</th><th>Spende</th><th>Artikel</th><th></th><th></th></tr>
      <?
      
        $bookingIdsToday = getBookingIdsOfDate($today, false);
        arsort($bookingIdsToday); // sorting to show latest booking on top
        
//         echo("<pre>");
//         print_r($bookingIdsToday);
        
        foreach($bookingIdsToday as $bookingId) {
            $booking = getBooking($bookingId);
            $editButton = editButton($bookingId);
            $receiptButton = receiptButton($bookingId);
//             echo("<pre>");
//             print_r($booking);
            echo("<tr>");
            echo("<td>$bookingId</td>");
            echo("<td class=td_nowrap>" . $booking['time'] . "</td>");
            echo("<td class=td_nowrap>CHF " . roundMoney10($booking['total']) . "</td>");
            echo("<td class=td_nowrap>CHF " . roundMoney($booking['donation']) . "</td>");
            
            echo("<td>");
            foreach($booking['articles'] as $articleId => $article) {
                list($name, $type, $pricePerQuantity, $unit, $image) = getDbArticleData($articleId);
                echo("<span class=tooltip><img class=articleImage src=images/articles/$image><span><img src=images/articles/$image></span></span>");
                echo(" " . $article['quantity'] . " " . $article['unit'] . " " . $article['text'] . ", ");
            }
            
            echo("</td>");
            
            echo("<td>$editButton</td>");
            echo("<td>$receiptButton</td>");
            echo("</tr>\n");
        }        
      ?>
    </table>

    
    
    <p><br></p>
    <h2>Frühere Buchungen (nur aktuelles Jahr)</h2>
    <table id=bookingsTable>
    <tr><th>Buchung</th><th>Datum</th><th>Zeit</th><th>Total</th><th>Spende</th><th>Artikel</th><th></th></tr>
    <?    
        $datesWithBookings = getBookingDatesOfYear(date("Y"));
    
//         echo("<pre>");
//         print_r($datesWithBookings);
    
        foreach($datesWithBookings as $date) {
            $bookingIds = getBookingIdsOfDate($date, false);
            arsort($datesWithBookings); // sorting to show latest date on top
            
            $previousFormatedDate = "";
            
//             echo("<pre>");
//             print_r($bookingIds);
            
            foreach($bookingIds as $bookingId) {
                $booking = getBooking($bookingId);
                $receiptButton = receiptButton($bookingId);
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
                echo("<td class=td_nowrap>$formatedDate</td>");
                echo("<td class=td_nowrap>" . $booking['time'] . "</td>");
                echo("<td class=td_nowrap>CHF " . roundMoney10($booking['total']) . "</td>");
                echo("<td class=td_nowrap>CHF " . roundMoney($booking['donation']) . "</td>");
                
                echo("<td>");
                foreach($booking['articles'] as $articleId => $article) {
                    list($name, $type, $pricePerQuantity, $unit, $image) = getDbArticleData($articleId);
                    echo("<span class=tooltip><img class=articleImage src=images/articles/$image><span><img src=images/articles/$image></span></span>");
                    echo(" " . $article['quantity'] . " " . $article['unit'] . " " . $article['text'] . ", ");
                }
                
                echo("</td>");
                echo("<td>$receiptButton</td>");            
                echo("</tr>\n");

            }
        }
    ?>
    </table>    
    
    
<?
include "$root/framework/footer.php"; 
?>
    
