<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

?>

<!-- <button type=submit id=createReceiptButton class=createReceiptButton><img src="images/receipt.png" width=50px><br>Beleg</button>         -->

<button type=button id=cancelButton class=cancelButton><img src="images/clear_basket.png" width=50p>Abbrechen</button>

<?
$bookingId = getDbBookingId();
if($bookingId == "new" ) { // basket filled with articles for a new booking
?>
    <button type=button id=payButton class=payButton disabled><img src="images/pay.png" width=50p>Bezahlt</button>
<?
}
else { // basket loaded to edit an already completed booking
?>
    <button type=button id=updateButton class=updateButton><img src="images/update_basket.png" width=50p>Aktualisieren</button>
<?
}
?>
   
