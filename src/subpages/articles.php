<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();
?>

<script src="<? echo("$root/"); ?>/framework/articles.js"></script>






<?


function showButton($title, $image, $id, $style) {
    ?>
        <div class="articleButton <? echo($style); ?>">
<!--             <p><? echo("<span class=tooltip><img class=articleImage src=images/articles/$image><span><img src=images/articles/$image></span></span>"); ?></p> -->
            <p><? echo("<img class=articleImage src=images/articles/$image>"); ?></p>
            <p><? echo($title); ?></p>
    <!--         <p class=moneyCellSmall><? echo($price); ?></p> -->
        </div>
    <? 
}



/* Normal Articles */
$lines = getDbProductsEx("guss", "name", "normal");
foreach($lines as $line) {     
    showButton($line['name'], $line['image1'], line['articleId'], "normalArticleButton");
} 

/* Floating Articles */
$lines = getDbProductsEx("guss", "name", "floatingCandle");
foreach($lines as $line) {     
    showButton($line['name'], $line['image1'], line['articleId'], "floatingArticleButton");
}

/* Premade Articles */
$lines = getDbProductsEx("guss", "name", "premade");
foreach($lines as $line) {     
    showButton($line['name'], $line['image1'], line['articleId'], "preMadeArticleButton");
} 

/* Effect Articles */
$lines = getDbProductsEx("wachs", "name", "effect");
foreach($lines as $line) {     
    showButton($line['name'], $line['image1'], line['articleId'], "effectArticleButton");
} 




?>





<script>
    $(document).ready(function(){
        console.log("Articles Page loaded");  
    });
</script>
