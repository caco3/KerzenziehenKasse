<? 
$root=".";
include "$root/framework/header.php";

if (!str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) { /* Hide on viewer */ ?>
    <div id="body">
      <h1>Administration</h1>

      <h2>Preisliste</h2>
      <p><a href=prices.php target="_self">Generator für die Preisliste</a>.<br><br></p>
      
      <h2>Artikel und Preise ändern</h2>
      <p><a href=../../phpmyadmin target=_blank>phpmyadmin</a>.<br><br></p>
      
      <h2>Buchungen exportieren</h2>
      <p>Der CSV-Export ist direkt über die Buchungs- und Statistik-Seiten möglich.<br><br></p>
      
      <!--<h2>Datenbank exportieren</h2>
      <p><a href=subpages/exportDb.php target="_self">Export im SQL-Format</a>.<br><br></p>-->
      
<!--       <iframe name="content" style="width: 1600px; height: 450px; background: white;"></iframe> -->
    </div>
 <? } else { ?>
	<br><br><b>Duhhh, Keine Ahnung, wie Du diese Seite gefunden hast.<br>Aber auf jeden Fall gibt es hier für dich nichts zu sehen :)</b>
<?
 }
include "$root/framework/footer.php"; 
?>
    
