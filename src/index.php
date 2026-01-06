<? 
$root=".";
include "$root/framework/header.php";

if (str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) { // Viewer
?>
<h1>Kerzenziehen-Kasse (Viewer)</h1>
<div id=navigationDiv style="width:400px"></div>
<?
}
else { // Normal
?>

    <div id="body">
        <div id=clock><p><span id=clockText></span> <button type=button id=minimizeButton class=minimizeButton onclick="minimizeClicked();"><img src=<? echo("$root"); ?>/images/minimize.png height=28px></button></p></div>
        <table id=mainPageTable>
            <tr style="vertical-align: top; padding: 0px;">
                <td>
                    <div class=leftSideDiv>
<!--                             <div id=dipArticlesDiv style="width:408px;"></div> -->
                                            
<!--                         <div id=customArticleDiv></div> -->
<!--                        <span id=pourArticlesFloatingCandlesDiv></span>
                        <span id=pourArticlesNormalDiv></span>
                        <span id=pourArticlesPreMadeDiv></span>-->
                        
                        <div id=articlesDiv></div>

                    </div>
                </td>
                <td>
                    <div class=verticalSpacer></div>
                </td>
<!--                <td>
                    <div class=midddleDiv>
                        <div id=pourArticlesNormalDiv></div>
                    </div>
                </td>
                <td>
                    <div class=verticalSpacer></div>
                </td>-->
                <td>
                    <div class=rightSideDiv>
                        <div id=basketDiv></div>
                        <div id=basketButtonsDiv></div>
                        <div id=extraInfoDiv></div>
                        <!--<div id=bibleVerseDiv></div>-->
                        <div id=navigationDiv></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>



<!-- <div class="rightSideDiv topAlignDiv"> -->

<!-- </div> -->

<?
	
}

include "$root/framework/footer.php"; 
?>
    

<!-- </div> -->
