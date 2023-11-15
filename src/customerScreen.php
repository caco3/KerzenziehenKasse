<? 
$root=".";
// include "$root/framework/header.php";
// 
// 
// // Today
// $today = date("Y-m-d");
// $todayDE = date("d. ") . $germanMonth[date("m") - 1] . date(". Y");
// 
// include "$root/framework/footer.php"; 
?>


<!DOCTYPE html>
<html lang="de">
<head>

<!--     <meta http-equiv="refresh" content="1" /> -->
    <meta name="viewport" content="initial-scale=1.2">
    <title>Kerzenziehen</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="favicon.ico">
        
    <script src="<? echo("$root"); ?>/framework/jquery.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/jquery-ui.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/functions.js"></script>

    <link href="<? echo("$root"); ?>/framework/jquery.firework.css" rel="stylesheet">
    <script src="<? echo("$root"); ?>/framework/jquery.firework.js"></script>
    <script src="<? echo("$root"); ?>/framework/browser_detect.js"></script>
    
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/easy-numpad.css">
    <script src="<? echo("$root"); ?>/framework/easy-numpad.js"></script>
    <script src="<? echo("$root"); ?>/framework/functions.js"></script>
    
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/style.css"> 

    <script>
        $(document).ready(function(){
             startClock();
        });
    </script>

</head>

<body id=live>
<div id="container">
   <div id="header">
        <div style="clear:both;">
            <div id=logo>
                <h1><img src="images/candle.png" width=30px> Kerzenziehen &ndash; <a href="http://www.kirche-neuwies.ch" target="_blank"><img src="images/logo_small.png"></a></h1>
            </div>
<!--            <div id=clock>
                <p id=clockText></p>
            </div>-->
        </div>
        <hr>
        
      <h2>Warenkorb</h2>
      
      <table id=bookingsTable>
      <tr><th class=td_rightBorder colspan=2>Artikel</th><th>Preis</th></tr>
        
      <tr><td><img src=images/articles_small/colors.png height=40px></td>       <td class=td_rightBorder>273 g Parafin­wachs</td>       <td>CHF 10.23</td></tr>
      <tr><td><img src=images/articles_small/food.png height=40px></td>         <td class=td_rightBorder>273 g Food &amp; Drinks</td>  <td>CHF 23.50</td></tr>
      <tr><td><img src=images/articles_small/pentakegel.png height=40px></td>   <td class=td_rightBorder>1 Pentakegel gross</td>       <td>CHF 34.00</td></tr>
      <tr><td><img src=images/articles_small/bee.png height=40px></td>          <td class=td_rightBorder>652 g Bienen­wachs</td>        <td>CHF 52.13</td></tr>     
        
        
      </table>
        
      <h3>Total: CHF <span id=total>0.00</span></h3>
    </div>
</body>


