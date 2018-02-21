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

<h2>Artikel zur freien Eingabe</h2>

<table id=customArticleTable>
<tr><th>Freie Eingabe</th><th>Preis</th><th></th></tr>
<tr>
    <td><input type=text id=customArticleDescriptionInput placeholder="Freie Eingabe eines Artikels"></input></td>
    <td>CHF <input type=text class=articleMoneyInput id=quantity_custom maxlength=6 placeholder="0.00"></input></td>
    <td><? echo(addButton('custom')); ?></td>
</tr>
</table>


<script>
    $(document).ready(function(){
        console.log("Custom Article Page loaded");  
    });
</script>
