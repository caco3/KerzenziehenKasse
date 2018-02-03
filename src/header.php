<?
require_once('framework/credentials_check.php');

if($autorization == CREDENTIALS_VALID){ //credentials are valid
    // nothing to do, continue page load
}
elseif($autorization == INVALID_CREDENTIALS){ //credentials are invalid
    // redirect to login page
    echo("<head><meta http-equiv=refresh content=\"0; url=login.php\"/></head>"); //redirect to login.php
    die("Access restricted, you get redirected to login.php!");    
}
elseif($autorization == MISSING_CREDENTIALS){ //credentials are missing
    // redirect to login page
    echo("<head><meta http-equiv=refresh content=\"0; url=login.php\"/></head>"); //redirect to login.php
    die("Access restricted, you get redirected to login.php!");    
} 



require_once("config/config.php");
require_once("framework/functions.php");
require_once("framework/db.php");




db_connect();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="favicon.ico">
    <title>Kerzenziehen</title>    
    <link rel="stylesheet" href="style.css">  
    <script src="framework/jquery.min.js"></script>
    <script src="framework/jquery-ui.min.js"></script>
    <script src="framework/functions.js"></script>
</head>


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


<body>



    <div style="clear:both;">
        <div id=logo>
            <h1><img src="images/candle.png" width=30px> Kerzenziehen &ndash; <img src="images/logo_small.png"></h1>
        </div>
        <div id=clock><p id=clockText></p></div>
    </div>
    <hr>
    <div class="modal"></div>
    <div class="fullPageOverlay">
        <div class="fullPageOverlayContent">
            Laden...
        </div>
    </div>
    
    
    
