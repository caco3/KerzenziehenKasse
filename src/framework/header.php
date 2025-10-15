<?


require_once("$root/framework/credentials_check.php");

require_once("$root/config/config_generic.php");
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();
?>

<!DOCTYPE html>
<html lang="de">
<head>

<meta name="viewport" content="initial-scale=0.5">


<? if(isset($TEST_SYSTEM) && $TEST_SYSTEM) { ?>
    <title>Kerzenziehen TEST-SYSTEM</title>
<? } else { ?>
    <title>Kerzenziehen</title>
<? } ?>

    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="favicon.ico">
    
<? 
    /* For Screenshotting the diagramms easier, we can hide the background Image.
       For simplicity, we disable the whole CSS */
   
   
   
    if (!isset($_GET['nocss'])) {
       if(isset($TEST_SYSTEM) && $TEST_SYSTEM) { ?>
        <link rel="stylesheet" href="<? echo("$root"); ?>/framework/style_testsystem.css"> 
    <? } else { ?>
        <link rel="stylesheet" href="<? echo("$root"); ?>/framework/style.css"> 
    <? 
       } 
   }
?> 


    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/buttons.css">  
    <script src="<? echo("$root"); ?>/framework/jquery.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/jquery-ui.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/functions.js"></script>

    <link href="<? echo("$root"); ?>/framework/jquery.firework.css" rel="stylesheet">
    <script src="<? echo("$root"); ?>/framework/jquery.firework.js"></script>
    <script src="<? echo("$root"); ?>/framework/browser_detect.js"></script>
    
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/easy-numpad.css">
    <script src="<? echo("$root"); ?>/framework/easy-numpad.js"></script>
    <script src="<? echo("$root"); ?>/framework/scale.js"></script>
    
<!--    <script src="<? echo("$root/"); ?>/framework/articles.js"></script>
    <script src="<? echo("$root/"); ?>/framework/basket.js"></script>-->




    <script>
    // global variables used in basket
    // var inputFieldActive = null;
    // var inputFieldSelection = null;


    $(document).ready(function(){
        startClock();
        showArticles();
        showBasket();
        loadBibleVerse();
        loadNavigation();
        
//         console.log("Webbrowser: " + BrowserDetect.browser);
//         if (BrowserDetect.browser != "Firefox") {
//             firework.launch("Dieser Webbrowser (" + BrowserDetect.browser + ") wird nicht unterstützt! Bitte verwende Firefox!", 'error', 9999999000);
//         }

        <? if(isset($TEST_SYSTEM) && $TEST_SYSTEM and (basename($_SERVER['PHP_SELF']) == "index.php")) { ?>
            firework.launch("Du verwendest das Test-System! Damit kannst spielen und testen. Die Eingaben haben keinen Einfluss auf die richtige Kasse!", 'error', 9999999000);
        <? } ?>

    });
    </script>

</head>

<? if(isset($TEST_SYSTEM) && $TEST_SYSTEM) { ?>
    <body id=test>
<? } else { ?>
    <body id=live>
<? } ?>

<div id="container">

   <div id="header">
   
<?
// If this variable is set (in config.php), a separate database and files/folders will be used!
if(isset($TEST_SYSTEM) && $TEST_SYSTEM) {
    echo("<h1 style=\"color: red;\">TEST-SYSTEM (Separate Datenbank)!!!</h1>\n");
}  

if (!(basename($_SERVER['PHP_SELF']) == "index.php")) {
?>
        <div style="clear:both;">
            <div id=logo>
                <h1><img src="images/candle.png" width=30px> Kerzenziehen &ndash; <img src="images/logo_small.png"></h1>
            </div>
            
            <div>
                <p id=headerLinksTexts> 
                <? if(basename($_SERVER['PHP_SELF']) != "index.php") { ?>
                    <? if (!str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) { /* Hide on viewer */ ?>
                        <a class="headerLinks" href="index.php" target="_self"><img src=images/Shopping-basket-icon.png height=30px> Kasse</a> | 
                    <?} ?>
                <? }

				?>

                    <a class="headerLinks" href="bookings.php" target="_self"><img src=images/bookings.png height=30px> Buchungen</a> |
					<b>Auswertung:</b>
                    <a class="headerLinks" href="statsCurrentYear.php" target="_self"><img src=images/day.png height=30px style="margin-left: 5px; margin-right: 5px"></a> 
                    <a class="headerLinks" href="statsYears.php" target="_self"><img src=images/year.png height=30px style="margin-left: 5px; margin-right: 5px"></a> 
                    <a class="headerLinks" href="statsDiagrams.php" target="_self"><img src=images/chart.png height=30px style="margin-left: 5px; margin-right: 5px"></a> | 
                    <? if (!str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) { /* Hide on viewer */ ?> 
						<a class="headerLinks" href="admin.php" target="_self"><img src=images/gear.png height=30px> Admin</a> | 
                        <a class="headerLinks" href="help.php" target="_self"><img src=images/help.png height=30px> Hilfe</a> | 
					<? } ?>

				<?
                if(basename($_SERVER['PHP_SELF']) != "bookings.php") { ?> 
                <? } 
                if(basename($_SERVER['PHP_SELF']) != "statsCurrentYear.php") { ?> 
                <? } 
                if(basename($_SERVER['PHP_SELF']) != "statsYears.php") { ?> 
                <? } 
                if(basename($_SERVER['PHP_SELF']) != "statsDiagrams.php") { ?>
                <? } 
                if(basename($_SERVER['PHP_SELF']) != "admin.php") { ?>
                    <? if (!str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) { /* Hide on viewer */ ?> 
                    <?} ?>
                <? } 
                if(basename($_SERVER['PHP_SELF']) != "help.php") { ?>
                    <? if (!str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) { /* Hide on viewer */ ?>
                    <?} ?>
                <? } ?>
<!--                     | <img id=timerIcon src="images/timer/0.png" width=18px> -->
                </p>
            </div>
            <div id=clock>
                <p id=clockText></p>
            </div>
        </div>
        <hr>
        <? } ?>
  
        <div class="modal"></div>
    </div>
    

