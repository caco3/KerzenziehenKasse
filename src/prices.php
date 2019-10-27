<? 

$root=".";
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");


db_connect();

// print("<pre>");

$articles = array();

/* Dipping Articles */
$articles = array_merge($articles, getDbProductsEx("wachs", "name", "normal"));
foreach($articles as &$article) {
    $article['pricePerQuantity'] = $article['pricePerQuantity'] * 100; // Adjust price since it is per 'g' but we want to show it per '100g'
}


/* Normal Articles */
$articles = array_merge($articles, getDbProductsEx("guss", "name", "normal"));


/* Floating Articles */
$articles = array_merge($articles, getDbProductsEx("guss", "name", "floatingCandle"));

/* Premade Articles */
$articles = array_merge($articles, getDbProductsEx("guss", "name", "premade"));

/* Effect Articles */
$articles = array_merge($articles, getDbProductsEx("wachs", "name", "effect"));


// print("<pre>");
// print_r($articles);


?>

<h1>Preislisten-Generator (Stand <? echo(date("d.m.Y")); ?>)</h1>
<p>Alles auswählen und in den Texteditor deiner Wahl einfügen.</p>

<table>

<?
foreach($articles as $article) {
    ?>
    <tr>
        <td><img src=images/articles/<? echo($article['image1']); ?> style="width: 50px; height: 50px;"></td>
        <td><? echo($article['name']); ?></td>
        <td>CHF <? echo(formatMoney($article['pricePerQuantity'])); ?>/<? echo($article['package']); ?></td>
    </tr>
    <?
}

?>

</table>
