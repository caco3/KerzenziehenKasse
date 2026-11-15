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

    <!-- Screensaver (same as on the customer screen, but input-driven) -->
    <div id="screensaver"><img id=screensaverImg src="<? echo("$root"); ?>/images/Logo-Kirche-Neuwies.png" width=200px></div>

    <style>
        #screensaver {
            position: fixed; /* Sit on top of the page content */
            display: none; /* Hidden by default */
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: rgba(0,0,0, 1); /* Black background */
            z-index: 9999; /* Above all page content, overlays and the numpad */
        }

        #screensaverImg {
            position: fixed;
            top: 0;
            left: 0;
        }
    </style>

    <script>
        var screensaverIdleTimeout = 60 * 60 * 1000; // 60min idle -> screensaver
        var screensaverMoveInterval = 5 * 1000; // 5s in ms
        var screensaverIdleTimer = null;
        var moveScreensaverTimerHandle = null;
        var screensaverDismissedAt = 0; // timestamp of last dismissal (for click swallowing)

        /**
         * Shows the screensaver overlay and starts moving the logo.
         */
        function showScreensaver() {
            moveScreensaverImg();
            document.getElementById("screensaver").style.display = "block";
            moveScreensaverTimerHandle = setInterval(moveScreensaverImg, screensaverMoveInterval);
        }

        /**
         * Hides the screensaver overlay and stops moving the logo.
         */
        function hideScreensaver() {
            var screensaver = document.getElementById("screensaver");
            if (window.getComputedStyle(screensaver).display !== "none") {
                // The input that dismissed the screensaver must not also trigger
                // actions underneath (e.g. put an article into the basket).
                screensaverDismissedAt = Date.now();
            }
            screensaver.style.display = "none";
            if (moveScreensaverTimerHandle != null) {
                clearInterval(moveScreensaverTimerHandle);
                moveScreensaverTimerHandle = null;
            }
        }

        /**
         * Moves the screensaver logo to a new random position.
         */
        function moveScreensaverImg() {
            var img = document.getElementById("screensaverImg");
            var maxX = Math.max(0, window.innerWidth - img.offsetWidth);
            var maxY = Math.max(0, window.innerHeight - img.offsetHeight);
            img.style.left = Math.floor(Math.random() * (maxX + 1)) + "px";
            img.style.top = Math.floor(Math.random() * (maxY + 1)) + "px";
        }

        /**
         * Dismisses the screensaver (if shown) and restarts the idle timer.
         *
         * @param {Event} [e] - The input event that counts as activity.
         */
        function resetScreensaverTimer(e) {
            if (e && e.type === "touchstart" && e.cancelable &&
                window.getComputedStyle(document.getElementById("screensaver")).display !== "none") {
                e.preventDefault(); // suppress the emulated click of the dismissing tap
            }
            hideScreensaver();
            clearTimeout(screensaverIdleTimer);
            screensaverIdleTimer = setTimeout(showScreensaver, screensaverIdleTimeout);
        }

        // Any user input counts as activity; "keydown" also covers the barcode scanner
        ["mousemove", "mousedown", "keydown"].forEach(function(evt) {
            document.addEventListener(evt, resetScreensaverTimer, true);
        });
        // touchstart needs a non-passive listener so preventDefault() can suppress
        // the emulated click of the tap that dismisses the screensaver
        document.addEventListener("touchstart", resetScreensaverTimer, { capture: true, passive: false });

        // Swallow the first click after the screensaver was dismissed so the
        // dismissing tap/click/keypress cannot reach the UI underneath
        document.addEventListener("click", function(e) {
            if (Date.now() - screensaverDismissedAt < 500) {
                e.stopPropagation();
                e.preventDefault();
            }
        }, true);

        resetScreensaverTimer(); // Start the idle timer on page load
    </script>

<!-- <div class="rightSideDiv topAlignDiv"> -->

<!-- </div> -->

<?
	
}

include "$root/framework/footer.php"; 
?>
    

<!-- </div> -->
