<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();
?>

<script src="framework/functions.js"></script>
<script src="framework/article_subpage.js"></script>

<h2>Artikel zum Giessen</h2>

<table id=pourArticlesTable>
<tr><th colspan=2>Form</th><th>Preis</th><th></th></tr>
<?
$lines = getDbProducts("guss");

foreach($lines as $line) {
    if($line['unit'] == "g") {
        $price = "CHF " . number_format($line['pricePerQuantity'] * 100, 2) . "/100g";
        $weight = "<input type=hidden value=1 id=quantity" . $line['articleId'] . ">";
    }
    else if($line['unit'] == "Stk.") {  
        $price = "CHF " . number_format($line['pricePerQuantity'], 2) . "/Stk.";
        $weight = "<input type=hidden value=1 id=quantity" . $line['articleId'] . ">";
    }
    else {
        $price = "CHF " . number_format($line['pricePerQuantity'], 2);
        $weight = "<input type=hidden value=1 id=quantity" . $line['articleId'] . ">";
    }
    
    $button = addButton($line['articleId']);
            
    echo("<tr>
            <td><span class=tooltip><img class=articleImage src=images/" . $line['image'] . "><span><img src=images/" . $line['image'] . "></span></span></td>
            <td>" . $line['name'] . "</td>
            <td class=moneyCell>$price $weight</td>
            <td>$button</td>
        </tr>");    
}
?>

</table>

<script>
    $(document).ready(function(){
        console.log("Pour Articles loaded");  
    });
</script>
