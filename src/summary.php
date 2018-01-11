<? 
    include "header.php";
    
    // todo get next free recipe id
    $id = 99;
?>
<h2>Zusammenfassung</h2>


    <div id=leftSideDiv>
        <?
            showSummary();
        ?>

    </div>
    <div  id=rightSideDiv>
        <div id=leftSideDiv>
            <form method="get" target="_blank" action="receipt.php">
                <input type=hidden name="id" value="<? echo($id); ?>">
                <button type=submit id=createReceiptButton class=createReceiptButton><img src="images/receipt.png" width=50px><br>Beleg</button>
                
            </form>
            <p><br></p>
            <form method="get" target="_self" action="index.php">
                <button type=submit id=summaryBackButton class=summaryBackButton><img src="images/back.png" width=50px><br>Zurück</button>
            </form>
        </div>
        <div id=rightSideDiv>
            <form method="get" target="_self" action="index2xxx.php">
                <button type=button id=finishButton class=finishButton><img src="images/pay.png" width=50px><br>Bezahlt</button>    
            </form>
        </div>
    </div>



<div id=bottomDiv></div>

<? 
    include "footer.php";
?>
