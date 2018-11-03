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

<h2>Artikel zur freien Eingabe</h2>
<?

$lines = getDbProducts("custom", "name");
?>

<table id=customArticleTable>
<tr><th colspan=2>Freie Eingabe</th><th>Preis</th><th></th></tr>
<tr>
    <td><span class=tooltip><img class=articleImage src=images/<? echo($lines[0]['image1']); ?>><span class=tooltipContent><img src=images/<? echo($lines[0]['image1']); ?>></span></span></td>
    <td><input type=text id=customArticleDescriptionInput placeholder="Freie Eingabe eines Artikels"></input></td>
    <td class=moneyCell>CHF <input type=text class=articleMoneyInput id=quantity_custom maxlength=6 placeholder="0.00"></input></td>
    <td><? echo(addButton('custom')); ?></td>
</tr>
</table>


<script>
    $(document).ready(function(){
        console.log("Custom Article Page loaded");  
    });
</script>
