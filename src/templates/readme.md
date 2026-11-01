# Templates

`receipt.odt` in diesem Ordner ist das **live Template**: docker-compose mountet
dieses Verzeichnis in den `receipt-generator`-Container (`/templates`). Änderungen
greifen sofort — kein Neustart, kein Rebuild nötig.

Der Python-Service öffnet das ODT als ZIP, ersetzt Platzhalter in `content.xml` per String-Replace und tauscht
die QR-Rechnung als SVG aus.

## Neue Felder hinzufügen

Platzhalter müssen in `content.xml` als **ungeteilter** String stehen
(`{feldname}`), sonst findet das String-Replace sie nicht. LibreOffice teilt
Felder aber oft auf mehrere `text:span`s auf — deshalb:

1. Feld in LibreOffice hinzufügen
2. Datei umbenennen: odt -> zip
3. `content.xml` in Editor öffnen.
4. Feld suchen und Formatierungsteil entfernen, z.B.:
   "{</text:span><text:span text:style-name="T20">qrBill</text:span><text:span text:style-name="T18">}" => "{qrBill}"
