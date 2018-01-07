<?
include "../config/config.php";
include "../framework/functions.php";
include "../framework/db.php";

db_connect();

$verse = getBibleVerse();

?>

<p id=bibleVerseText>
    <? echo($verse['verse']); ?>
</p>
<p id=bibleVerseReference>
    <? echo($verse['ref']); ?>
</p>

