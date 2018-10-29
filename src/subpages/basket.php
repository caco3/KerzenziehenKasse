<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

?>

<!-- <script src="<? echo("$root/"); ?>/framework/functions.js"></script> -->
<script src="<? echo("$root/"); ?>/framework/basket.js"></script>



<?
    $bookingId = getDbBookingId();
    if($bookingId == "new" ) { // basket filled with articles for a new booking
        echo("<h2>Warenkorb</h2>");
        echo("<table id=basketTableNew>");
    }
    else { // basket loaded to edit an already completed booking
        echo("<h2 id=editBookingTitle>Warenkorb (Bearbeitung einer bestehenden Buchung)!</h2>");
        echo("<table id=basketTableEdit>");
    }
    
    echo("<tr><th colspan=2>Artikel</th><th>Menge</th><th>Preis</th><th></th></tr>\n");
    $basket = getDbBasket();
    
/*    echo("<pre>");
    print_r($basket);
    echo("</pre>");  */  
    
    foreach($basket as $basketEntry) {      
        $basketEntryId = $basketEntry['basketEntryId'];
        $articleId = $basketEntry['articleId'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $image1 = $basketEntry['image1'];
        $image2 = $basketEntry['image2'];
        $image3 = $basketEntry['image3'];
        
        $removeButton = showRemoveFromBasketButton($basketEntryId);
        
        if($articleId != 'custom') { // normal entry
            list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
            /* Note: input fields typenumber do not allow setSelection
            *       So we use a type=text field and restrict the characters, see $(".basketQuantityInput").keydown()          */
            $quantityField = "<input type=text class=basketQuantityInput id=basketEntryId_" . $basketEntryId . "_quantity value=$quantity> $unit";
            $priceField = "CHF <input class=basketMoneyInput type=text id=basketEntryId_" . $basketEntryId . "_price value=" . number_format($price, 2) . " readonly>";            
            $textField = "$name";
        }
        else { // custom entry
            $quantityField = "<input type=text class=basketQuantityInput id=basketEntryId_" . $basketEntryId . "_quantity value=$quantity readonly> Stk.";
            $textField = $basketEntry['text'];
            $priceField = "CHF <input type=text class=basketMoneyInput id=basketEntryId_" . $basketEntryId . "_price value=" . $price . ">";
        }
        
        
        echo("<tr>
            <td>");
        echo("<span class=tooltip><img class=articleImage src=images/$image1><span><img src=images/$image1></span></span>");
        if( $image2 != "") {
            echo("<span class=tooltip><img class=articleImage src=images/$image2><span><img src=images/$image2></span></span>");
        }
        if( $image3 != "") {
            echo("<span class=tooltip><img class=articleImage src=images/$image3><span><img src=images/$image3></span></span>");
        }
        
        echo("</td><td>$textField</td>
            <td class=quantityCell>$quantityField</td>
            <td class=moneyCell>$priceField</td>
            <td>$removeButton</td>
            </tr>\n");  
    }
    
    
    // Spende
    echo("<tr>
            <td colspan=3>Spende</td>
            <td class=moneyCell>CHF <input type=text class=basketMoneyInput id=basketDonationMoney value=" . getDbDonation() . "></td>
            <td></td>
        </tr>\n");    
    
    
    // Total
    echo("<tr>
            <td colspan=2 class=bold class=basketTotalCell>Total</td>
            <td class=basketTotalRoundedCell><p class=basketTotalRoundedLabel>gerunded</p></td>
            <td class=moneyCell><b>CHF <input type=text class=basketMoneyInput id=basketTotalMoney value=" . getDbTotal() . "><p id=basketTotalMoneyRounded>CHF " . roundMoney(getDbTotal()) . "</p></td>
            <td></td>
        </tr>\n"); 
    
    
    echo("</table>");




?>
