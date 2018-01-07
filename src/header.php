<?
include "config/config.php";
include "framework/functions.php";
include "framework/db.php";

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

</body>
