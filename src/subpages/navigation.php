<p class=navigation><a class="headerLinks" href="bookings.php" target="_self"><img src=images/bookings.png height=30px> Buchungen</a></p>
<p class=navigation><a class="headerLinks" href="statsCurrentYear.php" target="_self"><img src=images/day.png height=30px> Auswertung (Pro Tag)</a></p>
<p class=navigation><a class="headerLinks" href="statsYears.php" target="_self"><img src=images/year.png height=30px> Auswertung (Pro Jahr)</a></p>
<p class=navigation><a class="headerLinks" href="statsDiagrams.php" target="_self"><img src=images/chart.png height=30px> Auswertung (Diagramme)</a></p>
<? if (!str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) { /* Hide on viewer */ ?>
    <p class=navigation><a class="headerLinks" href="admin.php" target="_self"><img src=images/gear.png height=30px> Administration</a></p>
    <p class=navigation><a class="headerLinks" href="help.php" target="_self"><img src=images/help.png height=30px> Hilfe</a></p>
<? } ?>
<?
  $root = "..";
  require_once("$root/config/config.php");
  if(isset($TEST_SYSTEM) && $TEST_SYSTEM) { ?>
 <!-->   <p class=navigation><a class="headerLinks" href="https://kerzenziehen.kirche-neuwies.ch/index.php" target="_self">Zur normalen Kasse wechseln</a></p> -->
<? } else { ?>
  <!--  <p class=navigation><a class="headerLinks" href="https://kerzenziehentest.kirche-neuwies.ch/index.php" target="_self">Zum Test-System wechseln</a></p> -->
<? } ?>
