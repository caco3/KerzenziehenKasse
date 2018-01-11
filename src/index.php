<? 
    include "header.php";
?>


<div id=leftSideDiv>
    <div id=articlesDiv></div>
</div>
<div id=rightSideDiv>
    <div id=basketDiv></div>
    <div id=basketCompleteDiv>
        <p></p>
        <form method="get" target="_self" action="summary.php">
            <button type=submit id=createReceipt class=createReceiptButton><img src="images/pay.png" width=50px><br>Weiter</button>
        </form>
    </div>
    <div id=bibleVerseDiv></div>
</div>


<div id=bottomDiv></div>

<? 
    include "footer.php";
?>
