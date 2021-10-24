<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();
?>

<script src="framework/articles.js"></script>


<h2 id=articlesDivTitle>Artikel</h2>



<?


function showButton($line, $buttonStyle) {
//  [articleId] => 20 [typ] => guss [subtype] => normal [name] => Arrangement [pricePerQuantity] => 16.000 [unit] => Stk. [image1] => arrangement.png [image2] => [image3] => 
// $line['name'], $line['image1'], $line['articleId'], $line['unit']
    if($line['unit'] == "g") {
//         $price = "CHF " . number_format($line['pricePerQuantity'] * 100, 2, ".", "") . "/100g";
        $weight = "<input type=hidden value=1 id=quantity_" . $line['articleId'] . ">";
    }
    else if($line['unit'] == "Stk.") {  
//         $price = "CHF " . number_format($line['pricePerQuantity'], 2, ".", "") . "/Stk.";
        $weight = "<input type=hidden value=1 id=quantity_" . $line['articleId'] . ">";
    }
    else {
//         $price = "CHF " . number_format($line['pricePerQuantity'], 2, ".", "");
        $weight = "<input type=hidden value=1 id=quantity_" . $line['articleId'] . ">";
    }
    $price = formatMoney($line['pricePerQuantity']);




    ?>
        <div class="articleButton <? echo("$buttonStyle"); ?>" id=<? echo($line['articleId']); ?> onclick="addArticleToBasket(this.id)">
        <div class=articlePackageDiv><? echo($line['package']); ?></div>
<!--             <p><? echo("<span class=tooltip><img class=articleImage src=images/articles/".$line['image1']."><span><img src=images/articles/".$line['image1']."></span></span>"); ?></p> -->
            <? echo("<img class=articleImageLarge src=images/articles/".$line['image1'].">"); ?>
        <div class=articlePriceDiv><? echo($price); ?></div>
        
            <p><? echo($line['name']); ?></p>
        </div>
    <? 
}





function showDippingButton($line, $buttonStyle) {
    $price = $line['pricePerQuantity'] * 100; // Adjust price since it is per 'g' but we want to show it per '100g'
    $price = formatMoney($price);
    
    $header = "<h2><img class=articleImageNumpadHeader src=images/articles/".$line['image1']."> " . $line['name'] . "</h2>";
    ?>
        <div class="dippingArticleButton <? echo("$buttonStyle"); ?>" id=<? echo($line['articleId']); ?> 
            onclick="show_easy_numpad(this.id, 'articleQuantity', 0, '<? echo($header); ?>', false, '', ' g')">
        <div class=articlePackageDiv><? echo($line['package']); ?></div>
<!--             <p><? echo("<span class=tooltip><img class=articleImage src=images/articles/".$line['image1']."><span><img src=images/articles/".$line['image1']."></span></span>"); ?></p> -->
            <p><? echo("<img class=articleImageLarge src=images/articles/".$line['image1'].">"); ?></p>
        <div class=articlePriceDiv><? echo($price); ?></div>
        
            <p><? echo($line['name']); ?></p>
        </div>
    <? 
}


/* Dipping Articles */
if (in_array('normal', $articlesToShow['wachs'])) {
	$lines = getDbProductsEx("wachs", "name", "normal");
	foreach($lines as $line) {     
	//     print_r($line);
		showDippingButton($line, "dippingArticleButton");
	} 
}


/* Normal Articles */
if (in_array('normal', $articlesToShow['guss'])) {
	$lines = getDbProductsEx("guss", "name", "normal");
	foreach($lines as $line) {     
	//     print_r($line);
		showButton($line, "normalArticleButton");
	}
}

/* Floating Articles */
if (in_array('floatingCandle', $articlesToShow['guss'])) {
	$lines = getDbProductsEx("guss", "name", "floatingCandle");
	foreach($lines as $line) {     
		showButton($line, "floatingArticleButton");
	}
}

/* Premade Articles */
if (in_array('premade', $articlesToShow['guss'])) {
	$lines = getDbProductsEx("guss", "name", "premade");
	foreach($lines as $line) {     
		showButton($line, "preMadeArticleButton");
	} 
}

/* Effect Articles */
if (in_array('effect', $articlesToShow['wachs'])) {
	$lines = getDbProductsEx("wachs", "name", "effect");
	foreach($lines as $line) {     
		showButton($line, "effectArticleButton");
	} 
}



?>





<script>
    $(document).ready(function(){
        console.log("Articles Page loaded");  
    });
</script>
