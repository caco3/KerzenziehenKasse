<? 
$root=".";
include "$root/framework/header.php";

if (!str_contains($_SERVER["SCRIPT_FILENAME"], "viewer")) { /* Hide on viewer */ ?>
    <div id="body">
      <h1>Administration</h1>

      <h2>Drucker</h2>
      <p><a href="subpages/printerApi.php" target="_self">API für Beleg-Drucker</a><br><br></p>
      <p><a href="#" onclick="fetch('http://localhost:8086/printer-settings'); return false;">Druckereinstellungen</a></p>
      <p><br></p>

      <h2>Preisliste</h2>
      <p><a href=prices.php target="_self">Generator für die Preisliste</a></p>
      <p><br></p>

      <h2>Datenbank</h2>
      <p><a href="subpages/phpmyadmin.php" target="_self">phpMyAdmin</a></p>
      <p><br></p>

      <h2>Buchungen exportieren</h2>
      <ul>
        <li>Der CSV-Export ist direkt über die Buchungs- und Statistik-Seiten möglich.<br><br></li>
        <li><a href=subpages/exportOds.php target="_self">Alle Rechnungen des aktuellen Jahres</a><br><br></li>
        <li><a href="subpages/exportOds.php?method=twint" target="_self">Alle Twint-Buchungen des aktuellen Jahres</a></li>
      </ul>
      <p></p>

      <h2>Monitoring / APIs</h2>
      <p>REST-Schnittstellen (via kdotool-webserver):</p>
      <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <tr><th>Endpoint</th><th>Beschreibung</th><th>Zugriff</th></tr>
        <tr><td><a href="/battery" target="_blank">/battery</a></td>
            <td>Akkustand als JSON:<br><code>{"level":75,"status":"Not charging","charging":false,"ac_connected":true}</code></td>
            <td>lokal, LAN, <b>Internet — ohne Login!</b><br><a href="https://neuwies.ddnss.ch/battery" target="_blank"><code>https://neuwies.ddnss.ch/battery</code></a></td></tr>
        <tr><td><a href="/memory" target="_blank">/memory</a></td>
            <td>RAM- und Swap-Auslastung als JSON:<br><code>{"mem_total_mb":7591,"mem_available_mb":1700,"mem_used_percent":77,<br>"swap_total_mb":2048,"swap_used_mb":370,"swap_used_percent":18}</code></td>
            <td>lokal, LAN, <b>Internet — ohne Login!</b><br><a href="https://neuwies.ddnss.ch/memory" target="_blank"><code>https://neuwies.ddnss.ch/memory</code></a></td></tr>

      </table>
      
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
    
