<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

?>

<!-- <button type=submit id=createreceiptButtonView class=createreceiptButtonView><img src="images/receipt.png" width=50px><br>Beleg</button>         -->


<table id=basketButtonsTable>
    <tr>
        <td>
            <button type=button id=cancelButton class=cancelButton onclick="cancelClicked();"><img src="images/clear_basket.png" height=45px><br>Abbrechen</button>
        </td>
		<? 
			$bookingId = getDbBookingId();
			if($bookingId == "new" ) { // basket filled with articles for a new booking
		?>
			<td>
					<button type=button id=cashButton class=cashButton onclick="cashClicked();"><img src="images/bargeld.png" height=45px><br>Bar bezahlt</button>
			</td>
			<td>
					<button type=button id=twintButton class=twintButton onclick="twintClicked();"><img src="images/twint.png" height=40px><br>Mit Twint bez.</button>
			</td>
			<td>
					<button type=button id=invoiceButton class=invoiceButton onclick="invoiceClicked();"><img src="images/invoice.png" height=40px><br>Rechnung</button>
			</td>
			
		<? } else { /* update existing booking */ ?>
			<td>
				<button type=button id=updateButton class=updateButton style="vertical-align: middle;" onclick="updateClicked();"><img src="images/update_basket.png" height=45px><br>Aktualisieren</button>

				<!--&nbsp;&nbsp;&nbsp;Zahlungsart:-->
				<?
				$booking = getDbBooking($bookingId);
				$paymentMethod = $booking['paymentMethod'] ?? '';
				?>
				<input type=hidden id=originalPaymentMethod value="<? echo($paymentMethod); ?>">
				<input type=hidden id=paymentBookingId value="<? echo($bookingId); ?>">
				<div style="display: inline-block; text-align: left; vertical-align: middle; margin-left: 100px">
					<label style="display: block; margin: 20px 0;"><input type=radio name=paymentMethodRadio value=cash onchange="paymentMethodChanged(this)" <? if ($paymentMethod == 'cash') echo 'checked'; ?>> <img src="images/bargeld.png" height=40px style="vertical-align: middle;"> Bar</label>
					<label style="display: block; margin: 20px 0;"><input type=radio name=paymentMethodRadio value=twint onchange="paymentMethodChanged(this)" <? if ($paymentMethod == 'twint') echo 'checked'; ?>> <img src="images/twint.png" height=30px style="vertical-align: middle;"> Twint</label>
					<label style="display: block; margin: 20px 0;"><input type=radio name=paymentMethodRadio value=invoice onchange="paymentMethodChanged(this)" <? if ($paymentMethod == 'invoice') echo 'checked'; ?>> <img src="images/invoice.png" height=30px style="vertical-align: middle;"> Rechnung</label>
				</div>
			</td>
		<? } ?>
    </tr>
	</table>

	<!-- Payment Method Confirmation Dialog -->
	<div id="paymentMethodDialog" class="printer-dialog" style="display: none;">
		<div class="printer-dialog-content">
			<div class="printer-dialog-header">
				<h3>Zahlungsart ändern</h3>
				<button type="button" class="printer-dialog-close" onclick="closePaymentMethodDialog()">&times;</button>
			</div>
			<div class="printer-dialog-body">
				<p id="paymentMethodMessage" style="text-align: center;">Soll die Zahlungsart wirklich geändert werden?</p>
			</div>
			<div class="printer-dialog-footer">
				<button type="button" class="cashButton" id="confirmPaymentMethodBtn" style="width: 150px; height: 60px; font-size: 20px; margin-right: 20px;" onclick="confirmPaymentMethod()">Ändern</button>
				<button type="button" class="cancelButton" onclick="closePaymentMethodDialog()" style="width: 150px; height: 60px; font-size: 20px;">Abbrechen</button>
			</div>
		</div>
	</div>
   
