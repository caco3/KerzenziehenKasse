<h1><a href="admin.php">Zurück</a></h1>
<hr>
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
// foreach($articles as &$article) {
//     $article['pricePerQuantity'] = $article['pricePerQuantity'] * 100; // Adjust price since it is per 'g' but we want to show it per '100g'
// }


/* Normal Articles */
$articles = array_merge($articles, getDbProductsEx("guss", "name", "normal"));


/* Floating Articles */
$articles = array_merge($articles, getDbProductsEx("guss", "name", "floatingCandle"));

/* Premade Articles */
// $articles = array_merge($articles, getDbProductsEx("guss", "name", "premade"));
$articles = array_merge($articles, getDbProductsEx("special", "name", "premade"));

/* Effect Articles */
$articles = array_merge($articles, getDbProductsEx("wachs", "name", "effect"));


// print("<pre>");
// print_r($articles);


?>

<div id="scoped-content">
    <style type="text/css" scoped>
        table, th, td { border:1px solid black; border-collapse: collapse; padding: 5px;  } 		
    </style>

<h1>Preisliste (Stand <? echo(date("d.m.Y")); ?>)</h1>
<p>Alles auswählen und in den Texteditor deiner Wahl einfügen.</p>
	
<table>
<tr><th colspan=2>Artikel</th><th>Preis</th><th>Ungefähre Dauer</th></tr>
<?
foreach($articles as $article) {
    if (($article['type'] == "wachs") && ($article['subtype'] != "effect")) { // Ziehen (ohne Effektwachs)
        $article['pricePerQuantity'] = $article['pricePerQuantity'] * 100; // Adjust price since it is per 'g' but we want to show it per '100g'
		$duration = $article['duration'] . " h für &Oslash; 1 - 1.5 cm";
    }
	elseif (($article['type'] == "guss") && ($article['subtype'] != "preMade")) { // Giessen (ohne Rosenkerze)
		$duration = $article['duration'] . " h";
    }
	else {
		$duration = "-";
	}
	
	
	
	
    ?>
    <tr>
        <td><img src=images/articles/<? echo($article['image1']); ?> style="width: 50px; height: 50px;"></td>
        <td><? echo($article['name']); ?></td>
        <td>CHF <? echo(formatMoney($article['pricePerQuantity'])); ?>/<? echo($article['package']); ?></td>
        <td><? echo($duration); ?></td>
		
		
    </tr>
    <?
}

?>

</table>
</div>
