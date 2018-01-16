<? 
    
include "../config/config.php";
include "../framework/functions.php";
include "../framework/db.php";


db_connect();
?>


<script src="framework/functions.js"></script>
<script src="framework/article_subpage.js"></script>


<h2>Artikel</h2>

<table id=dippArticlesTable>
<tr><th colspan=2>Wachs</th><th>Preis</th><th>Gewicht</th><th></th></tr>

<? 
$lines = getDbProducts("wachs");

foreach($lines as $line) {
    if($line['unit'] == "g") {
        $price = "CHF " . number_format($line['pricePerQuantity'] * 100, 2) . "/100g";
        
        /* Note: input fields typenumber do not allow setSelection
         *       So we use a type=text field and restrict the characters, see $(".adjustQuantityInput").keydown()          */
        $weight = "<input type=text class=articleQuantityInput id=quantity" . $line['articleId'] . " value=0> g";
    }
    else if($line['unit'] == "Stk.") {  
        $price = "CHF " . number_format($line['pricePerQuantity'], 2) . "/Stk.";
        $weight = "<input type=hidden value=1 class=weightInput id=quantity" . $line['articleId'] . ">";
    }
    else {
        $price = "CHF " . number_format($line['pricePerQuantity'], 2);
        $weight = "<input type=hidden value=1 id=quantity" . $line['articleId'] . ">";
    }
    
    $button = addButton($line['articleId']);
            
    echo("<tr>
            <td><span class=tooltip><img class=articleImage src=images/" . $line['image'] . "><span><img src=images/" . $line['image'] . "></span></span></td>
            <td>" . $line['name'] . "</td>
            <td class=moneyCell>$price</td>
            <td>$weight</td>
            <td>$button</td>
        </tr>\n");    
}

?>
</table>

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

<table id=manualArticleTable>
<tr><th>Freie Eingabe</th><th>Preis</th><th></th></tr>
<tr>
    <td><input type=text id=manualArticleDescriptionInput placeholder="Manuelle Eingabe eines Artikels"></input></td>
    <td>CHF <input type=text class=articleMoneyInput id=quantity0 maxlength=6 placeholder="0.00"></input></td>
    <td><? echo(addButton('0')); ?></td>
</tr>
</tr>
</table>



