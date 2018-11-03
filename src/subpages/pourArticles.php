<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();
?>

<!-- <script src="<? echo("$root/"); ?>/framework/functions.js"></script> -->
<script src="<? echo("$root/"); ?>/framework/articles.js"></script>

<h2>Artikel zum Giessen</h2>

<table id=pourArticlesTable>
<tr><th colspan=2>Form</th><th>Preis</th><th></th></tr>
<?
$lines = getDbProducts("guss", "name");

foreach($lines as $line) {
    if($line['unit'] == "g") {
        $price = "CHF " . number_format($line['pricePerQuantity'] * 100, 2, ".", "") . "/100g";
        $weight = "<input type=hidden value=1 id=quantity_" . $line['articleId'] . ">";
    }
    else if($line['unit'] == "Stk.") {  
        $price = "CHF " . number_format($line['pricePerQuantity'], 2, ".", "") . "/Stk.";
        $weight = "<input type=hidden value=1 id=quantity_" . $line['articleId'] . ">";
    }
    else {
        $price = "CHF " . number_format($line['pricePerQuantity'], 2, ".", "");
        $weight = "<input type=hidden value=1 id=quantity_" . $line['articleId'] . ">";
    }
    
    $button = addButton($line['articleId']);
    
    $image1 = $line['image1'];
    $image2 = $line['image2'];
    $image3 = $line['image3'];
            
    echo("<tr><td>");
    echo("<span class=tooltip><img class=articleImage src=images/$image1><span><img src=images/$image1></span></span>");
    if( $image2 != "") {
        echo("<span class=tooltip><img class=articleImage src=images/$image2><span><img src=images/$image2></span></span>");
    }
    if( $image3 != "") {
        echo("<span class=tooltip><img class=articleImage src=images/$image3><span><img src=images/$image3></span></span>");
    }
    echo("</td>
            <td class=articleNameCell>" . $line['name'] . "</td>
            <td class=moneyCell>$price $weight</td>
            <td>$button</td>
        </tr>");    
}
?>

</table>

<script>
    $(document).ready(function(){
        console.log("Pour Articles Page loaded");  
    });
</script>
