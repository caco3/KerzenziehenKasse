# Templates

`bill.odt` und `receipt.odt` in diesem Ordner sind die **live Templates**: docker-compose
mountet dieses Verzeichnis in den `receipt-generator`-Container (`/templates`).

- `bill.odt` — Rechnung (Zahlungsart `EZS`), enthält den `qr-bill`-Frame für die QR-Rechnung
- `receipt.odt` — Quittung für Bar- und Twint-Zahlungen

Der Python-Service wählt das Template anhand der Zahlungsart, öffnet das ODT als ZIP,
ersetzt Platzhalter in `content.xml` per String-Replace und tauscht bei Rechnungen
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
