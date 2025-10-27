# Templates
Die Templates müssen zwingend als ODT 1.0 gespeichert wwerden. Neuere Formate werden durch das verwendete ODTPHP-Framework korrupiert!

TODO: Altes Libreoffice finden, welches noch in ODT 1.0 speichert (7.6.7.2 ist zu neu).

## Neue Felder hinzufügen
Neue Felder können nur indirekt via LibreOffice hinzugefügt werden!
Vorgehen:

1. Feld in LibreOffice hinzufügen
2. Datei umbenennen: odt -> zip
3. Datei content.xml in Editor öffnen.
4. Feld suchen und Formatierungsteil entfernen, z.b: "{</text:span><text:span text:style-name="T20">qrBill</text:span><text:span text:style-name="T18">}" => "{qrBill}"
