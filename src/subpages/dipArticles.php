<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();
?>

<script src="framework/functions.js"></script>
<script src="framework/articles.js"></script>

<h2>Artikel zum Ziehen</h2>

<table id=dippArticlesTable>
<tr><th colspan=2>Wachs</th><th>Preis</th><th>Gewicht</th><th></th></tr>

<? 
$lines = getDbProducts("wachs");

foreach($lines as $line) {
    if($line['unit'] == "g") {
        $price = "CHF " . number_format($line['pricePerQuantity'] * 100, 2) . "/100g";
        
        /* Note: input fields typenumber do not allow setSelection
         *       So we use a type=text field and restrict the characters, see $(".adjustQuantityInput").keydown()          */
        $weight = "<input type=text class=articleQuantityInput id=quantity_" . $line['articleId'] . " value=0> g";
    }
    else if($line['unit'] == "Stk.") {  
        $price = "CHF " . number_format($line['pricePerQuantity'], 2) . "/Stk.";
        $weight = "<input type=hidden value=1 class=weightInput id=quantity_" . $line['articleId'] . ">";
    }
    else {
        $price = "CHF " . number_format($line['pricePerQuantity'], 2);
        $weight = "<input type=hidden value=1 id=quantity_" . $line['articleId'] . ">";
    }
    
    $button = addButton($line['articleId']);
            
    echo("<tr>
            <td><span class=tooltip><img class=articleImage src=images/" . $line['image'] . "><span class=tooltipContent><img src=images/" . $line['image'] . "></span></span></td>
            <td>" . $line['name'] . "</td>
            <td class=moneyCell>$price</td>
            <td>$weight</td>
            <td>$button</td>
        </tr>\n");    
}

?>
</table>


<script>
    $(document).ready(function(){
        console.log("Dip Articles Page loaded");  
    });
</script>
