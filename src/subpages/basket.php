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


<h2>Warenkorb</h2>

<?
echo("<table id=basketTable>");
    echo("<tr><th colspan=2>Artikel</th><th>Menge</th><th>Preis</th><th></th></tr>\n");
    $basket = getDbBasket();
    
/*    echo("<pre>");
    print_r($basket);
    echo("</pre>");  */  
    
    foreach($basket as $basketEntry) {      
        $basketEntryId = $basketEntry['basket_id'];
        $articleId = $basketEntry['article_id'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $image = $basketEntry['image'];
        
        $removeButton = showRemoveFromBasketButton($basketEntryId);
        
        if($articleId != 'custom') { // normal entry
            list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
            /* Note: input fields typenumber do not allow setSelection
            *       So we use a type=text field and restrict the characters, see $(".basketQuantityInput").keydown()          */
            $quantityField = "<input type=text class=basketQuantityInput id=basketId_" . $basketEntryId . "_quantity value=$quantity> $unit";
            $priceField = "CHF <input class=basketMoneyInput type=text id=basketId_" . $basketEntryId . "_price value=" . number_format($price, 2) . " readonly>";            
            $textField = "$name";
        }
        else { // custom entry
            $quantityField = "<input type=text class=basketQuantityInput id=basketId_" . $basketEntryId . "_quantity value=$quantity readonly> Stk.";
            $textField = $basketEntry['text'];
            $priceField = "CHF <input type=text class=basketMoneyInput id=basketId_" . $basketEntryId . "_price value=" . $price . ">";
        }
        
        
        echo("<tr>
            <td><span class=tooltip><img class=articleImage src=images/$image><span><img src=images/$image></span></td>
            <td>$textField</td>
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
