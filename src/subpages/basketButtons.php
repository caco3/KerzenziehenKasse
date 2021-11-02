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
        <td>
            <button type=button id=cancelButton class=cancelButton onclick="cancelClicked();"><img src="images/clear_basket.png" height=45px><br>Abbrechen</button>
        </td>
        <td>
            <?
            $bookingId = getDbBookingId();
            if($bookingId == "new" ) { // basket filled with articles for a new booking
            ?>
                <button type=button id=cashButton class=cashButton onclick="cashClicked();"><img src="images/cash.png" height=45px><br>Bar bezahlt</button>
            <?
            }
            else { // basket loaded to edit an already completed booking
            ?>
                <button type=button id=updateButton class=updateButton onclick="updateClicked();"><img src="images/update_basket.png" height=45px><br>Aktualisieren</button>
            <?
            }
            ?>
        </td>
        <td>
            <?
            $bookingId = getDbBookingId();
            if($bookingId == "new" ) { // basket filled with articles for a new booking
            ?>
                <button type=button id=twintButton class=twintButton onclick="twintClicked();"><img src="images/twint.png" height=40px><br>Mit Twint bez.</button>
            <?
            }
            ?>
        </td>
    </tr>
</table>
   
