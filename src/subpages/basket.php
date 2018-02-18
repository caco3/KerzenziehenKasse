<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

?>

<script src="framework/functions.js"></script>
<script src="framework/basket_subpage.js"></script>


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
        $freeEntry = $basketEntry['free'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $image = $basketEntry['image'];
        
        $deleteButton = showDeleteButton($basketEntryId);
        
        if($freeEntry == 0) { // normal entry
            list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
            /* Note: input fields typenumber do not allow setSelection
            *       So we use a type=text field and restrict the characters, see $(".basketQuantityInput").keydown()          */
            $quantityField = "<input type=text class=basketQuantityInput id=basketId_" . $basketEntryId . "_quantity value=$quantity> $unit";
            $priceField = "CHF <input class=basketMoneyInput type=text id=basketId_" . $basketEntryId . "_price value=" . number_format($price, 2) . " readonly>";            
            $textField = "$name";
        }
        else { // manual entry
            $quantityField = "<input type=text class=basketQuantityInput id=basketId_" . $basketEntryId . "_quantity value=$quantity readonly> Stk.";
            $textField = $basketEntry['text'];
            $priceField = "CHF <input type=text class=basketMoneyInput id=basketId_" . $basketEntryId . "_price value=" . $price . ">";
        }
        
        
        echo("<tr>
            <td><span class=tooltip><img class=articleImage src=images/$image><span><img src=images/$image></span></td>
            <td>$textField</td>
            <td class=quantityCell>$quantityField</td>
            <td class=moneyCell>$priceField</td>
            <td>$deleteButton
                <input type=hidden id=basketId_" . $basketEntryId . "_free  value=$freeEntry> </td>
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
            <td colspan=3 class=bold>Total</td>
            <td class=moneyCell class=bold>CHF <input type=text class=basketMoneyInput id=basketTotalMoney value=" . roundMoney(getDbTotal()) . "></td>
            <td></td>
        </tr>\n");  
    
    
    
    echo("</table>");




?>
