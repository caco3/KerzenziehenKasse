<? 
$root=".";
include "$root/framework/header.php";
?>
    <div id="body">
      <h1>Hilfe</h1>
      <h2>FAQ</h2>
      <ul>
        
        <li><p class=question><b>Frage:</b> Was bedeutet die kleine Stopuhr <img src=images/timer/5.png> oben in der Navigationsbar?</p>
            <p class=answer><b>Antwort:</b> Wenn im Warenkorb in einem Testfeld eine Eingabe gemacht wird (Menge- oder Preisänderung), wird einen Moment gewartet, bevor der Warenkorb neu berechnet wird.</p></li>
                
        <li><p class=question><b>Frage:</b> Wieso kann man nur die Buchungen vom aktuellen Tag nachbearbeiten?</p>
            <p class=answer><b>Antwort:</b> Die Tageskasse von den vergangenen Tagen wurden bereits abgerechnet ünd können nicht mehr verändert werden.</p></li>
        
        <li><p class=question><b>Frage:</b> Wohin kann ich Feedback und Verbesserungsvorschläge senden?</p>
            <p class=answer><b>Antwort:</b> Siehe Support unten.</p></li>
        
        <li><p class=question><b>Frage:</b> Wieso zeigt Word eine Fehlermeldung, wenn ich den Beleg öffne?</p>
            <p class=answer><b>Antwort:</b> Word unterstützt leider die aktuelle Version des Opendocument Formats (*.odt) nicht vollständig. Die Fehlermeldung kann jedoch ignoriert werden.<br>Alternativ kann <a href=https://de.libreoffice.org/ target="_blank">Libreoffice</a> verwendet werden (Gratis Alternative für Microsoft Office).</p></li>
            
        <li><p class=question><b>Frage:</b> Wie können die Preise angepasst werden?</p>
            <p class=answer><b>Antwort:</b> Die Artikel und Preise können über <a href="https://admin.hostpoint.ch/phpmyadmin2/sql.php?db=chrisc22_kerzenziehen&table=articles&server=467&pos=0" target="_blank">phpMyAdmin</a> angepasst werden.</p></li>
            
        <li><p class=question><b>Frage:</b> Gibt es einen Backup?</p>
            <p class=answer><b>Antwort:</b> Es wird täglich ein Backup der Datenbank gemacht. Die Daten werden abgelegt unter ~/backup/kerzenziehen.</p></li>
            
        <li><p class=question><b>Frage:</b> Wo finde ich den Quellcode dieser Buchungssoftware?</p>
            <p class=answer><b>Antwort:</b> Siehe <a href="https://github.com/caco3/KerzenziehenKasse" target="_blank">https://github.com/caco3/KerzenziehenKasse</a>.</p></li>
      
      </ul>
      <h2>Support</h2>
      <p>George Ruinelli<br>
        <a href="mailto:george@ruinelli.ch">george@ruinelli.ch</a><br>
        Mobile: 076/418'22'61
      </p>
      <h2>Copyright</h2>
      <p>&copy; 2018 by George Ruinelli</p>
    </div>

<?
include "$root/framework/footer.php"; 
?>
    
