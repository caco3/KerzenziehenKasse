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
    <script src="<? echo("$root"); ?>/framework/functions.js"></script>
    
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
                <h1><img src="images/candle.png" width=30px> Kerzenziehen &ndash; <a href="http://www.kirche-neuwies.ch" target="_blank"><img src="images/logo_small.png"></a></h1>
            </div>
            
            <div id=headerLinksDiv>
                <p id=headerLinksTexts> Navigation: 
                <? if(basename($_SERVER['PHP_SELF']) == "index.php") { ?>
                    <a class="headerLinks" href="bookings.php" target="_self">Buchungen</a>
                    | <a class="headerLinks" href="statsCurrentYear.php" target="_self">Auswertung (Pro Tag)</a>
                    | <a class="headerLinks" href="statsYears.php" target="_self">Auswertung (Pro Jahr)</a>
                    | <a class="headerLinks" href="statsDiagrams.php" target="_self">Auswertung (Diagramme)</a>
                    | <a class="headerLinks" href="admin.php" target="_self">Admin</a>
                    | <a class="headerLinks" href="help.php" target="_self">Hilfe</a>
                <? } 
                    elseif(basename($_SERVER['PHP_SELF']) == "bookings.php") { ?>
                    <a class="headerLinks" href="index.php" target="_self">Kasse</a>
                    | <a class="headerLinks" href="statsCurrentYear.php" target="_self">Auswertung (Pro Tag)</a>
                    | <a class="headerLinks" href="statsYears.php" target="_self">Auswertung (Pro Jahr)</a>
                    | <a class="headerLinks" href="statsDiagrams.php" target="_self">Auswertung (Diagramme)</a>
                    | <a class="headerLinks" href="admin.php" target="_self">Admin</a>
                    | <a class="headerLinks" href="help.php" target="_self">Hilfe</a>
                <? } 
                    elseif(basename($_SERVER['PHP_SELF']) == "statsCurrentYear.php") { ?>
                    <a class="headerLinks" href="index.php" target="_self">Kasse</a>
                    | <a class="headerLinks" href="bookings.php" target="_self">Buchungen</a>
                    | <a class="headerLinks" href="statsYears.php" target="_self">Auswertung (Pro Jahr)</a>
                    | <a class="headerLinks" href="statsDiagrams.php" target="_self">Auswertung (Diagramme)</a>
                    | <a class="headerLinks" href="admin.php" target="_self">Admin</a>
                    | <a class="headerLinks" href="help.php" target="_self">Hilfe</a>
                <? } 
                    elseif(basename($_SERVER['PHP_SELF']) == "statsYears.php") { ?>
                    <a class="headerLinks" href="index.php" target="_self">Kasse</a>
                    | <a class="headerLinks" href="bookings.php" target="_self">Buchungen</a>
                    | <a class="headerLinks" href="statsCurrentYear.php" target="_self">Auswertung (Pro Tag)</a>
                    | <a class="headerLinks" href="statsDiagrams.php" target="_self">Auswertung (Diagramme)</a>
                    | <a class="headerLinks" href="admin.php" target="_self">Admin</a>
                    | <a class="headerLinks" href="help.php" target="_self">Hilfe</a>
                <? } 
                    elseif(basename($_SERVER['PHP_SELF']) == "statsDiagrams.php") { ?>
                    <a class="headerLinks" href="index.php" target="_self">Kasse</a>
                    | <a class="headerLinks" href="bookings.php" target="_self">Buchungen</a>
                    | <a class="headerLinks" href="statsCurrentYear.php" target="_self">Auswertung (Pro Tag)</a>
                    | <a class="headerLinks" href="statsYears.php" target="_self">Auswertung (Pro Jahr)</a>
                    | <a class="headerLinks" href="admin.php" target="_self">Admin</a>
                    | <a class="headerLinks" href="help.php" target="_self">Hilfe</a>
                <? } 
                    elseif(basename($_SERVER['PHP_SELF']) == "admin.php") { ?>
                    <a class="headerLinks" href="index.php" target="_self">Kasse</a>
                    | <a class="headerLinks" href="bookings.php" target="_self">Buchungen</a>
                    | <a class="headerLinks" href="statsCurrentYear.php" target="_self">Auswertung (Pro Tag)</a>
                    | <a class="headerLinks" href="statsYears.php" target="_self">Auswertung (Pro Jahr)</a>
                    | <a class="headerLinks" href="statsDiagrams.php" target="_self">Auswertung (Diagramme)</a>
                    | <a class="headerLinks" href="help.php" target="_self">Hilfe</a>
                <? } 
                    elseif(basename($_SERVER['PHP_SELF']) == "help.php") { ?>
                    <a class="headerLinks" href="index.php" target="_self">Kasse</a>
                    | <a class="headerLinks" href="bookings.php" target="_self">Buchungen</a>
                    | <a class="headerLinks" href="statsCurrentYear.php" target="_self">Auswertung (Pro Tag)</a>
                    | <a class="headerLinks" href="statsYears.php" target="_self">Auswertung (Pro Jahr)</a>
                    | <a class="headerLinks" href="statsDiagrams.php" target="_self">Auswertung (Diagramme)</a>
                    | <a class="headerLinks" href="admin.php" target="_self">Admin</a>
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
    

