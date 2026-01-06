<? 
$root=".";
include "$root/framework/header.php";


// Today
$today = date("Y-m-d");
$todayDE = date("d. ") . $germanMonth[date("m") - 1] . date(". Y");
$todayDayOfWeek = $germanDayOfWeek[date("w")];

?>

    <script src="<? echo("$root/"); ?>/framework/bookings.js"></script>

    <!-- School Flag Confirmation Dialog -->
    <div id="schoolFlagDialog" class="printer-dialog" style="display: none;">
        <div class="printer-dialog-content">
            <div class="printer-dialog-header">
                <h3>Buchungen von Schulklassen</h3>
                <button type="button" class="printer-dialog-close" onclick="closeSchoolFlagDialog()">&times;</button>
            </div>
            <div class="printer-dialog-body">
                <p id="schoolFlagMessage">Möchten Sie die Schul-Markierung wirklich umschalten?</p>
            </div>
            <div class="printer-dialog-footer">
                <button type="button" class="cashButton" id="confirmSchoolFlagBtn" style="width: 150px; height: 60px; font-size: 20px; margin-right: 20px;" onclick="confirmSchoolFlag()">Umschalten</button>
                <button type="button" class="cancelButton" onclick="closeSchoolFlagDialog()" style="width: 150px; height: 60px; font-size: 20px;">Abbrechen</button>
            </div>
        </div>
    </div>

    <div id="body">
		<h1>Buchungen</h1>
		<div style="display: flex; align-items: flex-start; gap: 20px 60px; margin-bottom: 20px;">
			<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; backdrop-filter: blur(5px); flex: 0 0 auto;">
				<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 0.65); font-size: 16px; font-weight: 600;">Navigation:</h4>
				<ul style="margin: 0; padding-left: 20px;">
					<li><a href=bookings.php#today>Heute</a><br><br></li>
					<li><a href=bookings.php#year>Aktuelles Jahr</a><br><br></li>
					<li><a href=bookings_last_year.php>Vergangene Jahre</a></li>
				</ul>
			</div>
			
			<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; backdrop-filter: blur(5px); flex: 0 0 auto;">
				<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 0.65); font-size: 16px; font-weight: 600;">CSV Export:</h4>
				<div style="color: rgba(73, 80, 87, 0.65); font-weight: 600;"><? echo(exportCsvButton("bookings")); ?></div>
			</div>
			
			<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 25px; backdrop-filter: blur(5px); flex: 0 0 auto; margin-left: auto;">
				<h4 style="margin: 0 0 20px 0; color: rgba(73, 80, 87, 0.65); font-size: 18px; font-weight: 600;">Legende:</h4>
				<div style="display: flex; gap: 20px; font-size: 16px; align-items: center;">
					<div style="display: flex; align-items: center; gap: 8px;">
						<div style="width: 28px; height: 28px; background: url('images/edit.png') no-repeat center center; background-size: 20px 20px; background-color: rgba(176, 224, 230, 0.65); border: 1px solid rgba(135, 206, 235, 0.65); border-radius: 4px;"></div>
						<div style="color: rgba(73, 80, 87, 0.65); font-weight: 600;">Bearbeiten</div>
					</div>
					
					<div style="display: flex; align-items: center; gap: 8px;">
						<div style="width: 28px; height: 28px; background: url('images/receipt-view.png') no-repeat center center; background-size: 20px 20px; background-color: rgba(176, 224, 230, 0.65); border: 1px solid rgba(135, 206, 235, 0.65); border-radius: 4px;"></div>
						<div style="color: rgba(73, 80, 87, 0.65); font-weight: 600;">Quittung ansehen</div>
					</div>
					
					<div style="display: flex; align-items: center; gap: 8px;">
						<div style="width: 28px; height: 28px; background: url('images/receipt-print.png') no-repeat center center; background-size: 20px 20px; background-color: rgba(176, 224, 230, 0.65); border: 1px solid rgba(135, 206, 235, 0.65); border-radius: 4px;"></div>
						<div style="color: rgba(73, 80, 87, 0.65); font-weight: 600;">Quittung drucken</div>
					</div>
					
					<div style="display: flex; align-items: center; gap: 8px;">
						<div style="width: 28px; height: 28px; background: url('images/school.png') no-repeat center center; background-size: 20px 20px; background-color: rgba(176, 224, 230, 0.65); border: 1px solid rgba(135, 206, 235, 0.65); border-radius: 4px;"></div>
						<div style="color: rgba(73, 80, 87, 0.65); font-weight: 600;">Schul-Markierung</div>
					</div>
				</div>
			</div>
		</div>
	
      <h2><a name=today>Buchungen Heute (<? echo($todayDayOfWeek); ?>, <? echo($todayDE); ?>)</h2>
      <!--<p>Noch nicht implementiert</p>-->
      
      <table id=bookingsTable>
      <tr><th class=td_rightBorder>Buchung</th><th class=td_rightBorder>Zeit</th><th class=td_rightBorder>Total</th><th class=td_rightBorder>Spende</th><th class=td_rightBorder>Bezahlung</th><th class=td_rightBorder>Artikel</th><th class=td_rightBorder>Extra-Daten</th><th></th></tr>
      <?
      
        $bookingIdsToday = getBookingIdsOfDate($today, false);
        arsort($bookingIdsToday); // sorting to show latest booking on top
        
//         echo("<pre>"); print_r($bookingIdsToday); echo("</pre>");
        
        foreach($bookingIdsToday as $bookingId) {
            $booking = getBooking($bookingId);
//             echo("<pre>"); print_r($booking); echo("</pre>");
            $editButton = editButton($bookingId);
            $receiptButtonView = receiptButtonView($bookingId);
            $receiptButtonPrint = receiptButtonPrint($bookingId);
//             echo("<pre>"); print_r($booking); echo("</pre>");
            echo("<tr>");
            echo("<td class=td_rightBorder>$bookingId</td>");
            echo("<td class=\"td_nowrap td_rightBorder\">" . $booking['time'] . "</td>");
            echo("<td class=\"td_nowrap td_rightBorder\">CHF " . roundMoney10($booking['total']) . "</td>");
            echo("<td class=\"td_nowrap td_rightBorder\">CHF " . roundMoney($booking['donation']) . "</td>");
			if ($booking['paymentMethod'] == 'cash') {
				echo("<td class=\"td_nowrap td_rightBorder\" style=\"text-align: center; vertical-align: middle;\"><img src=\"images/bargeld.png\" height=40px></td>");
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
                echo("<span class=tooltip><img class=articleImage src=images/articles/$image><span><img src=images/articles/$image></span></span>");
                echo(" " . $article['quantity'] . " " . $article['unit'] . " " . $article['text'] . ", ");
            }
            
            echo("</td>");
            
            // Extra-Daten column
            echo("<td class=td_rightBorder>");
            if (!empty($booking['extra'])) {
                $extraData = @unserialize($booking['extra']);
                if (is_array($extraData)) {
                    $extraParts = [];
                    if (!empty($extraData['schulklasse'])) $extraParts[] = "Schulklasse: " . $extraData['schulklasse'];
                    if (!empty($extraData['leiter'])) $extraParts[] = "Leiter/in: " . $extraData['leiter'];
                    echo(implode("<br>", $extraParts));
                }
            }
            echo("</td>");
            if (str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) {
				// Do not show any buttons
				echo("<td></td><td></td><td></td>");
			}
			else {
				echo("<td class=td_rightBorder><div class='button-container'>");
				echo("$editButton");
                echo("$receiptButtonView");
                echo("$receiptButtonPrint");
                echo(schoolFlagButton($bookingId, $booking['school'] == 1));
				echo("</div></td>");
			}
            echo("</tr>\n");
        }        
      ?>
    </table>

    
    
    <p><br></p>
    <h2><a name=year>Weitere Buchungen des aktuellen Jahres</h2>
    <table id=bookingsTable>
    <tr><th class=td_rightBorder>Buchung</th><th>Datum</th><th class=td_rightBorder>Zeit</th><th class=td_rightBorder>Total</th><th class=td_rightBorder>Spende</th><th class=td_rightBorder>Bezahlung</th><th class=td_rightBorder>Artikel</th><th class=td_rightBorder>Extra-Daten</th><th></th></tr>
    <?    
        $datesWithBookings = getBookingDatesOfYear(date("Y"));
    
//         echo("<pre>"); print_r($datesWithBookings); echo("</pre>");
    
        foreach($datesWithBookings as $date) {
            // Skip today's date in the lower table
            if ($date == $today) {
                continue;
            }
            
            $bookingIds = getBookingIdsOfDate($date, false);
            arsort($bookingIds); // sorting to show latest date on top
            
//             echo("<pre>"); print_r($bookingIds); echo("</pre>");
        
            $previousFormatedDate = "";
            
//             echo("<pre>");
//             print_r($bookingIds);
            
            foreach($bookingIds as $bookingId) {
                $booking = getBooking($bookingId);
                $receiptButtonView = receiptButtonView($bookingId);
                $receiptButtonPrint = receiptButtonPrint($bookingId);
    //             echo("<pre>");
    //             print_r($booking); 
                $formatedDate = $germanDayOfWeek[strftime("%w", strtotime($booking['date']))] . ", " . 
                    strftime("%d. ", strtotime($booking['date'])) . $germanMonth[strftime("%m", strtotime($booking['date'])) - 1] . "." ;
                
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
					echo("<td class=\"td_nowrap td_rightBorder\" style=\"text-align: center; vertical-align: middle;\"><img src=\"images/bargeld.png\" height=40px></td>");
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
                    echo("<span class=tooltip><img class=articleImage src=images/articles/$image><span><img src=images/articles/$image></span></span>");
                    echo(" " . $article['quantity'] . " " . $article['unit'] . " " . $article['text'] . ", ");
                }
                
                echo("</td>");
                
                // Extra-Daten column
                echo("<td class=td_rightBorder>");
                if (!empty($booking['extra'])) {
                    $extraData = @unserialize($booking['extra']);
                    if (is_array($extraData)) {
                        $extraParts = [];
                        if (!empty($extraData['schulklasse'])) $extraParts[] = "Schulklasse: " . $extraData['schulklasse'];
                        if (!empty($extraData['leiter'])) $extraParts[] = "Leiter/in: " . $extraData['leiter'];
                        echo(implode("<br>", $extraParts));
                    }
                }
                echo("</td>");

				if (str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) {
					// Do not show any buttons
					echo("<td></td>");
				}
				else {
				echo("<td class=td_rightBorder><div class='button-container'>");
				echo("$receiptButtonView");
				echo("$receiptButtonPrint");
				echo(schoolFlagButton($bookingId, $booking['school'] == 1));
				echo("</div></td>");
			}
                echo("</tr>\n");

            }
        }
    ?>
    </table>    
    <p><br></p>
    
    
<?
include "$root/framework/footer.php"; 
?>
    

