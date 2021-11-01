<? 
$root=".";
include "$root/framework/header.php";
?>
    <div id="body">
      <h1>Administration</h1>

      <h2>Preisliste</h2>
      <p><a href=prices.php target="_self">Generator für die Preisliste</a>.<br><br></p>
      
      <h2>Artikel und Preise ändern</h2>
      <p><a href=../../phpmyadmin target=_blank>phpmyadmin</a>.<br><br></p>
      
      <h2>Buchungen exportieren</h2>
      <p>Das ist direkt über die <a href=stats.php target="_self">Statistik-Seite</a> möglich.<br><br></p>
      
      <!--<h2>Datenbank exportieren</h2>
      <p><a href=subpages/exportDb.php target="_self">Export im SQL-Format</a>.<br><br></p>-->
      
<!--       <iframe name="content" style="width: 1600px; height: 450px; background: white;"></iframe> -->
    </div>




<?
include "$root/framework/footer.php"; 
?>
    
