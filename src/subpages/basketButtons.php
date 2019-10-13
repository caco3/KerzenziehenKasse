<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

?>

<!-- <button type=submit id=createReceiptButton class=createReceiptButton><img src="images/receipt.png" width=50px><br>Beleg</button>         -->


<table id=basketButtonsTable>
    <tr>
        <td >
            <img id=timerIcon src="images/timer/0.png" width=36px>
        </td>
        <td style="width: 50px">
            <button type=button id=cancelButton class=cancelButton><img src="images/clear_basket.png" width=50p><br>Abbrechen</button>
        </td>
        <td style="width: 50px">
            <?
            $bookingId = getDbBookingId();
            if($bookingId == "new" ) { // basket filled with articles for a new booking
            ?>
                <button type=button id=payButton class=payButton><img src="images/pay.png" width=50p><br>Bezahlt</button>
            <?
            }
            else { // basket loaded to edit an already completed booking
            ?>
                <button type=button id=updateButton class=updateButton><img src="images/update_basket.png" width=50p><br>Aktualisieren</button>
            <?
            }
            ?>
        </td>
    </tr>
</table>
   
