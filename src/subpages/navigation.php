<p class=navigation><a class="headerLinks" href="bookings.php" target="_self">Buchungen</a></p>
<p class=navigation><a class="headerLinks" href="stats.php" target="_self">Auswertung</a></p>
<p class=navigation><a class="headerLinks" href="admin.php" target="_self">Administration</a></p>
<p class=navigation><a class="headerLinks" href="help.php" target="_self">Hilfe</a></p>
<?
  $root = "..";
  require_once("$root/config/config.php");
  if(isset($TEST_SYSTEM) && $TEST_SYSTEM) { ?>
 <!-->   <p class=navigation><a class="headerLinks" href="https://kerzenziehen.kirche-neuwies.ch/index.php" target="_self">Zur normalen Kasse wechseln</a></p> -->
<? } else { ?>
  <!--  <p class=navigation><a class="headerLinks" href="https://kerzenziehentest.kirche-neuwies.ch/index.php" target="_self">Zum Test-System wechseln</a></p> -->
<? } ?>
