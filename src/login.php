<?
$root=".";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config.php");
require_once("$root/config/config_generic.php");
/* We do not include the header here on purpose! */

    
// Enable to generate password hash
//     echo password_hash("xxx", PASSWORD_DEFAULT)."\n";
    
    
?>

<!DOCTYPE html>
<html lang="de">
<head>

<? if(isset($TEST_SYSTEM) && $TEST_SYSTEM) { ?>
    <title>Kerzenziehen TEST-SYSTEM</title>
<? } else { ?>
    <title>Kerzenziehen</title>
<? } ?>

    <meta charset="UTF-8">
    <http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="favicon.ico">
    
<? if(isset($TEST_SYSTEM) && $TEST_SYSTEM) { ?>
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/style_testsystem.css"> 
<? } else { ?>
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/style.css"> 
<? } ?>
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/buttons.css">  
    
    
    <script src="<? echo("$root"); ?>/framework/jquery.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/jquery-ui.min.js"></script>
    
    <link href="<? echo("$root"); ?>/framework/jquery.firework.css" rel="stylesheet">
    <script src="<? echo("$root"); ?>/framework/jquery.firework.js"></script>
</head>


<? if(isset($TEST_SYSTEM) && $TEST_SYSTEM) { ?>
    <body id=test>
<? } else { ?>
    <body id=live>
<? } ?>



    <div style="clear:both;">
        <?
            // If this variable is set (in config.php), a separate database and files/folders will be used!
            if(isset($testystem)) {
                echo("<h1 style=\"color: red;\">TEST-SYSTEM (Separate Datenbank)!!!</h1>\n");
            }
        ?>
        <div id=logo>
            <h1><img src="images/candle.png" width=30px> Kerzenziehen &ndash; <img src="images/logo_small.png"></h1>
        </div>
        <div id=clock><p id=clockText></p></div>
    </div>
    <hr>
    <div class="modal"></div>
<!--    <div class="fullPageOverlay">
        <div class="fullPageOverlayContent">
            Laden...
        </div>
    </div>-->
    
    

    <div class=contentLogin>
        <!-- content start --> 
<!--         <br><br> -->
<!--         Bitte Passwort eingeben: -->

<!--         <br><br> -->

<?
//     echo("autorization: $autorization<br><br>"); 
    if($autorization == CREDENTIALS_VALID){ //credentials are valid, redirect to index.php (default page)
?>
<!--         <div class="login_info login_ok">Das Passwort ist korrekt,<br>Du wirst gleich weitergeleitet...</div>   -->
        <script>
            firework.launch("Das Passwort ist korrekt,<br>Du wirst gleich weitergeleitet...", 'success', 5000);
        </script>        
<?
        exit();
    }
    elseif($autorization == MISSING_CREDENTIALS){ //no password or ID given  
?>
<!--         <div class="login_info">Das Passwort wird benötigt!</div> -->
        <script>
            firework.launch("Das Passwort wird benötigt!", 'error', 5000);
        </script>        
<?
    }
    elseif(($autorization == INVALID_CREDENTIALS)){ //password is wrong or ID is wrong
?>
<!--         <div class="login_info login_error">Das Passwort ist falsch!</div> -->
        <script>
            firework.launch("Das Passwort ist falsch!", 'error', 5000);
        </script>
<?
    }
    elseif(($autorization == "")){ //password is wrong or ID not yet given
        // nothing to do
    }
    elseif(($autorization == "INITIAL_PAGE_LOAD")){ // initial page load
        // nothing to do
    }
    else{ //should never occure
?>
<!--         <div class="login_info login_error">Unbekannter Fehler!</div> -->
        <script>
            firework.launch("Unbekannter Fehler!", 'error', 5000);
        </script>        
<?
    }            
?>



    <h2 style="margin-bottom: 5px;"><br>Anmeldung</h2>


    <form action="login.php" method="post">
    
    <label>Passwort:</label> 
    <input id="password" name="password" placeholder="**********" type="password"> 
    
    <input name="submit" type="submit" value="Login">
    </form>
    
    <p></br></p>
    <h2>Hinweis</h2>
    <p>Das Passwort erhältst Du vom Tagesleiter!</p>

        <!-- content End -->
    </div>
    <? include "$root/framework/footer.php" ?>

</body>

    
