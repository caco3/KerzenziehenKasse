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
      <tr><th class=td_rightBorder>Buchung</th><th class=td_rightBorder>Zeit</th><th class=td_rightBorder>Total</th><th class=td_rightBorder>Spende</th><th class=td_rightBorder>Bezahlung</th><th class=td_rightBorder>Artikel</th><th></th><th></th><th></th></tr>
      <?
      
        $bookingIdsToday = getBookingIdsOfDate($today, false);
        arsort($bookingIdsToday); // sorting to show latest booking on top
        
//         echo("<pre>"); print_r($bookingIdsToday); echo("</pre>");
        
        foreach($bookingIdsToday as $bookingId) {
            $booking = getBooking($bookingId);
//             echo("<pre>"); print_r($booking); echo("</pre>");
            $editButton = editButton($bookingId);
            $receiptButton = receiptButton($bookingId);
//             echo("<pre>"); print_r($booking); echo("</pre>");
            echo("<tr>");
            echo("<td class=td_rightBorder>$bookingId</td>");
            echo("<td class=\"td_nowrap td_rightBorder\">" . $booking['time'] . "</td>");
            echo("<td class=\"td_nowrap td_rightBorder\">CHF " . roundMoney10($booking['total']) . "</td>");
            echo("<td class=\"td_nowrap td_rightBorder\">CHF " . roundMoney($booking['donation']) . "</td>");
			if ($booking['paymentMethod'] == 'cash') {
				echo("<td class=\"td_nowrap td_rightBorder\" style=\"text-align: center; vertical-align: middle;\"><img src=\"images/cash.png\" height=40px></td>");
			}
			else if ($booking['paymentMethod'] == 'twint') {
				echo("<td class=\"td_nowrap td_rightBorder\" style=\"text-align: center; vertical-align: middle;\"><img src=\"images/twint.png\" height=30px></td>");
			}
			else { // invoice
				echo("<td class=\"td_nowrap td_rightBorder\" style=\"text-align: center; vertical-align: middle;\"><img src=\"images/invoice.png\" height=40px></td>");
			}
            
            echo("<td class=td_rightBorder>");
            foreach($booking['articles'] as $articleId => $article) {
                list($name, $type, $pricePerQuantity, $unit, $image) = getDbArticleData($articleId);
                echo("<span class=tooltip><img class=articleImage src=images/articles_small/$image><span><img src=images/articles_small/$image></span></span>");
                echo(" " . $article['quantity'] . " " . $article['unit'] . " " . $article['text'] . ", ");
            }
            
            echo("</td>");
            if (str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) {
				// Do not show any buttons
				echo("<td></td><td></td><td></td>");
			}
			else {
				echo("<td>$editButton</td>");
				echo("<td>$receiptButton</td>");
				if ($booking['school'] == 1) {
					echo("<td><img src=\"images/school.png\" width=50px></td>");
				}
				else {
					echo("<td></td>");            
				}
			}
            echo("</tr>\n");
        }        
      ?>
    </table>

    
    
    <p><br></p>
    <h2>Alle Buchungen des aktuelles Jahres</h2>
    <table id=bookingsTable>
    <tr><th class=td_rightBorder>Buchung</th><th>Datum</th><th class=td_rightBorder>Zeit</th><th class=td_rightBorder>Total</th><th class=td_rightBorder>Spende</th><th class=td_rightBorder>Bezahlung</th><th class=td_rightBorder>Artikel</th><th></th><th></th></tr>
    <?    
        $datesWithBookings = getBookingDatesOfYear(date("Y"));
    
//         echo("<pre>"); print_r($datesWithBookings); echo("</pre>");
    
        foreach($datesWithBookings as $date) {
            $bookingIds = getBookingIdsOfDate($date, false);
            arsort($bookingIds); // sorting to show latest date on top
            
//             echo("<pre>"); print_r($bookingIds); echo("</pre>");
        
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
                
//                 if( $formatedDate != $previousFormatedDate) {
    //                 echo("<tr><td></td></tr><tr><td></td></tr>\n");
                $previousFormatedDate = $formatedDate;
                /*}
                else {            
                    $formatedDate = "";
                } */           

                echo("<tr>");
                echo("<td class=td_rightBorder>$bookingId</td>");
                echo("<td class=td_nowrap>$formatedDate</td>");
                echo("<td class=\"td_nowrap td_rightBorder\">" . $booking['time'] . "</td>");
                echo("<td class=\"td_nowrap td_rightBorder\">CHF " . roundMoney10($booking['total']) . "</td>");
                echo("<td class=\"td_nowrap td_rightBorder\">CHF " . roundMoney($booking['donation']) . "</td>");
				if ($booking['paymentMethod'] == 'cash') {
					echo("<td class=\"td_nowrap td_rightBorder\" style=\"text-align: center; vertical-align: middle;\"><img src=\"images/cash.png\" height=40px></td>");
				}
				else if ($booking['paymentMethod'] == 'twint') {
					echo("<td class=\"td_nowrap td_rightBorder\" style=\"text-align: center; vertical-align: middle;\"><img src=\"images/twint.png\" height=30px></td>");
				}
				else { // invoice
					echo("<td class=\"td_nowrap td_rightBorder\" style=\"text-align: center; vertical-align: middle;\"><img src=\"images/invoice.png\" height=40px></td>");
				}
				
                echo("<td class=td_rightBorder>");
                foreach($booking['articles'] as $articleId => $article) {
                    list($name, $type, $pricePerQuantity, $unit, $image) = getDbArticleData($articleId);
                    echo("<span class=tooltip><img class=articleImage src=images/articles_small/$image><span><img src=images/articles_small/$image></span></span>");
                    echo(" " . $article['quantity'] . " " . $article['unit'] . " " . $article['text'] . ", ");
                }
                
                echo("</td>");

				if (str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) {
					// Do not show any buttons
					echo("<td></td><td></td>");
				}
				else {
					echo("<td>$receiptButton</td>"); 
					if ($booking['school'] == 1) {
						echo("<td><img src=\"images/school.png\" width=50px></td>");
					}
					else {
						echo("<td></td>");            
					}
				}					
                echo("</tr>\n");

            }
        }
    ?>
    </table>    
    <p><br>CSV Export: <? echo(exportCsvButton("bookings")); ?></p>
    
    
<?
include "$root/framework/footer.php"; 
?>
    
