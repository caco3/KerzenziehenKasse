<?
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="favicon.ico">
    <title>Kerzenziehen</title>    
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/style.css">  
    <script src="<? echo("$root"); ?>/framework/jquery.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/jquery-ui.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/functions.js"></script>

    <link href="<? echo("$root"); ?>/framework/jquery.firework.css" rel="stylesheet">
    <script src="<? echo("$root"); ?>/framework/jquery.firework.js"></script>


    <script>
    // global variables used in basket
    // var inputFieldActive = null;
    // var inputFieldSelection = null;

    $(document).ready(function(){    
        startClock();
        showArticles();
        showBasket();
        loadBibleVerse();
    });
    </script>

</head>

<body>

<div id="container">

   <div id="header">
        <div style="clear:both;">
            <div id=logo>
                <h1><img src="images/candle.png" width=30px> Kerzenziehen &ndash; <img src="images/logo_small.png"></h1>
            </div>
            
            <div id=headerLinksDiv>
                <p id=headerLinksTexts>
                <? if(basename($_SERVER['PHP_SELF']) == "index.php") { ?>
                    <a class="headerLinks" href="receipt.php" target="_blank">Beleg zu letzter Buchung</a> | 
                    <a class="headerLinks" href="stats.php" target="_blank">Auswertung</a>
                    | <a class="headerLinks" href="help.php" target="_blank">Hilfe</a>
                <? } 
                    elseif(basename($_SERVER['PHP_SELF']) == "stats.php") { ?>
                    <a class="headerLinks" href="index.php" target="_blank">Hauptseite</a>
                    | <a class="headerLinks" href="help.php" target="_blank">Hilfe</a>
                <? } 
                    elseif(basename($_SERVER['PHP_SELF']) == "help.php") { ?>
                    <a class="headerLinks" href="index.php" target="_blank">Hauptseite</a>
                <? } ?>
                </p>
            </div>
            <div id=clock>
                <p id=clockText></p>
            </div>
        </div>
        <hr>
        <div class="modal"></div>
<!--        <div class="fullPageOverlay">
            <div class="fullPageOverlayContent">
                Laden...
            </div>
        </div>-->
    </div>
    
    
