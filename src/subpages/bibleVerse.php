<?
include "../config/config.php";
include "../framework/functions.php";
include "../framework/db.php";

db_connect();

$verse = getBibleVerse();

?>

<!-- <h2>Bibelvers</h2> -->

<!--<table id=bibleVerseTable>
<tr>
    <td style="word-wrap:break-word; word-break: break-all; max-width: 200px;"><p id=bibleVerseText><? echo($verse['verse']); ?></p></td>
    <td><p id=bibleVerseReference><? echo($verse['ref']); ?></p></td>
</tr>
</table>-->



<div id=bibleVerseContentDiv>
    <p id=bibleVerseText><? echo($verse['verse']); ?></p>
    <p id=bibleVerseReference><? echo($verse['ref']); ?></p>
</div>
