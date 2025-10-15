<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

?>

<script>
$.ajaxPrefilter('script', function(options) {
    options.cache = true;
});

</script>

<!-- <script src="framework/functions.js"></script> -->
<script src="framework/basket.js"></script>



<?
    $bookingId = getDbBookingId();
    if($bookingId == "new" ) { // basket filled with articles for a new booking
        echo("<h2>Warenkorb</h2>");
        echo("<table id=basketTableNew>");
    }
    else { // basket loaded to edit an already completed booking
        echo("<h2 id=editBookingTitle>Korrektur von Buchung $bookingId</h2>");
        echo("<table id=basketTableEdit>");
    }
    
    echo("<tr><th colspan=2>Artikel</th><th>Menge</th><th>Preis</th><th></th></tr>\n");
    $basket = getDbBasket();
    
/*    echo("<pre>");
    print_r($basket);
    echo("</pre>"); */   
    
    foreach($basket as $basketEntry) {      
        $basketEntryId = $basketEntry['basketEntryId'];
        $articleId = $basketEntry['articleId'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $image1 = $basketEntry['image1'];
        $image2 = $basketEntry['image2'];
        $image3 = $basketEntry['image3'];
        
        $removeButton = showRemoveFromBasketButton($basketEntryId);
        
//         if($articleId != 'custom') { // normal entry
            list($name, $type, $pricePerQuantity, $unit) = getDbArticleData($articleId);
            /* Note: input fields typenumber do not allow setSelection
            *       So we use a type=text field and restrict the characters, see $(".basketQuantityInput").keydown()          */
			
			     
			$cents = true;
				
				
            if ($unit == "g") {
                $prefix = "";
                $suffix = " g";      
				//$cents = false;
            }            
            else if($unit == "Stk.") {
                $prefix = "";
                $suffix = " Stk. ";      
				//$cents = false;            
            }            
            else {
                $prefix = "CHF ";
                $suffix = "";        
				//$cents = true;
            }
            
            $header = "<h2><img class=articleImageNumpadHeader src=images/articles/$image1> $name</h2>";
            $quantityField = "<input type=text class=basketQuantityInput id=basketEntryId_" . $basketEntryId . "_quantity value=$quantity 
            onclick=\"show_easy_numpad($basketEntryId, 'basketQuantity', this.value, '$header', $cents, '$prefix', '$suffix')\"> $unit";
            $priceField = "CHF <input class=basketMoneyInput type=text id=basketEntryId_" . $basketEntryId . "_price value=" . number_format($pricePerQuantity * $quantity, 2, ".", "") . " readonly disabled=disabled>";            
            $textField = "$name";
//         }
//         else { // custom entry
//             $quantityField = "<input type=text class=basketQuantityInput id=basketEntryId_" . $basketEntryId . "_quantity value=$quantity readonly disabled=disabled> Stk.";
//             $textField = $basketEntry['text'];
//             $priceField = "CHF <input type=text class=basketMoneyInput id=basketEntryId_" . $basketEntryId . "_price value=" . $price . ">";
//         }
        
        
        echo("<tr>
            <td>");
        echo("<span class=tooltip><img class=articleImageNumpadHeader src=images/articles/$image1><span><img src=images/articles/$image1></span></span>");
        if( $image2 != "") {
            echo("<span class=tooltip><img class=articleImageNumpadHeader src=images/articles/$image2><span><img src=images/articles/$image2></span></span>");
        }
        if( $image3 != "") {
            echo("<span class=tooltip><img class=articleImageNumpadHeader src=images/articles/$image3><span><img src=images/articles/$image3></span></span>");
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
            <td class=moneyCell colspan=2>CHF <input type=text class=basketMoneyInput id=basketDonationMoney value=" . getDbDonation() . "
            onclick=\"show_easy_numpad('basketDonationMoney', 'basketDonation', this.value, '<h2>Spende</h2>', true, 'CHF ', '')\"></td>
        </tr>\n");    
    
    
    // Total
    echo("<tr>
            <td colspan=3 class=bold class=basketTotalCell>Total</td>
            <td class=moneyCell colspan=2><b>CHF <input type=text class=basketMoneyInput id=basketTotalMoney value=" . number_format(getDbTotal(), 2, ".", "") . "
             onclick=\"show_easy_numpad('basketTotalMoney', 'basketTotal', this.value, '<h2>Total</h2>', true, 'CHF ', '')\"></td>
        </tr>\n"); 
        
    echo("<tr>
            <td colspan=5 style=\"height: 0px\"></td>
        </tr>\n"); 
        
    echo("<tr>
            <td colspan=3 class=bold class=basketTotalCell><b>Auf 10 Rappen gerundet</b></td>
            <td class=moneyCell colspan=2><b><p id=basketTotalMoneyRounded>CHF " . roundMoney10(getDbTotal()) . "</p></td>
        </tr>\n"); 
    
    
    echo("</table>");




?>
