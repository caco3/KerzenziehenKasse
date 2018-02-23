-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: mysql19j06.db.hostpoint.internal
-- Erstellungszeit: 24. Feb 2018 um 00:25
-- Server-Version: 10.1.26-MariaDB
-- PHP-Version: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `chrisc22_kerzenziehen`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `articles`
--

CREATE TABLE `articles` (
  `articleId` varchar(10) NOT NULL,
  `typ` enum('wachs','guss','custom','') NOT NULL,
  `name` text,
  `pricePerQuantity` decimal(8,3) DEFAULT NULL,
  `unit` text NOT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `articles`
--

INSERT INTO `articles` (`articleId`, `typ`, `name`, `pricePerQuantity`, `unit`, `image`) VALUES
('1', 'wachs', 'Parafin-Kerzen', '0.027', 'g', 'colors.png'),
('11', 'guss', 'Flamme / Zwölfstern', '7.000', 'Stk.', 'zwoelfstern.png'),
('12', 'guss', 'Pentakegel / Kugel gross /<br>Pyramide', '8.000', 'Stk.', 'pyramide.png'),
('15', 'guss', 'Sternkegel', '8.500', 'Stk.', 'sternkegel.png'),
('16', 'guss', 'Tanne', '9.000', 'Stk.', 'tanne.png'),
('17', 'guss', 'Quader hoch', '10.000', 'Stk.', 'quader_gross.png'),
('18', 'guss', 'Quader breit', '11.000', 'Stk.', 'quader_breit.png'),
('19', 'guss', 'Bienenkorb', '12.000', 'Stk.', 'bienenkorb.png'),
('2', 'wachs', 'Bienenwachs-Kerzen', '0.037', 'g', 'bee.png'),
('3', 'wachs', 'Effektwachs', '1.000', 'Stk.', 'effektwachs.png'),
('4', 'guss', 'Tropfen', '2.000', 'Stk.', 'drop.png'),
('5', 'guss', 'Kegel', '3.500', 'Stk.', 'kegel.png'),
('6', 'guss', 'Kugel', '4.000', 'Stk.', 'kugel.png'),
('7', 'guss', 'Pentakegel', '5.500', 'Stk.', 'pentakegel.png'),
('8', 'guss', 'Rhombenkonus / Sechsstern', '6.500', 'Stk.', 'sechsstern.png'),
('custom', 'custom', 'Sonstiges', '0.000', 'Stk.', 'transparent.png');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `basket`
--

CREATE TABLE `basket` (
  `basket_id` int(11) NOT NULL,
  `article_id` text COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL,
  `custom` tinyint(1) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `basket_various`
--

CREATE TABLE `basket_various` (
  `donation` float NOT NULL,
  `total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `basket_various`
--

INSERT INTO `basket_various` (`donation`, `total`) VALUES
(0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bible_verses`
--

CREATE TABLE `bible_verses` (
  `ref` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `verse` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `bible_verses`
--

INSERT INTO `bible_verses` (`ref`, `verse`) VALUES
('1 Johannes 2:17', 'Und die Welt vergeht mit ihrer Lust; wer aber den Willen Gottes tut, der bleibt in Ewigkeit.'),
('1 Johannes 5:11', 'Und das ist das Zeugnis, dass uns Gott das ewige Leben gegeben hat, und dieses Leben ist in seinem Sohn.'),
('1 Johannes 5:13', 'Das habe ich euch geschrieben, damit ihr wisst, dass ihr das ewige Leben habt, euch, die ihr glaubt an den Namen des Sohnes Gottes.'),
('1 Johannes 5:20', 'Wir wissen aber, dass der Sohn Gottes gekommen ist und uns Einsicht gegeben hat, damit wir den Wahrhaftigen erkennen. Und wir sind in dem Wahrhaftigen, in seinem Sohn Jesus Christus. Dieser ist der wahrhaftige Gott und das ewige Leben.'),
('1 Korinther 15:58', 'Darum, meine lieben Brüder und Schwestern, seid fest und unerschütterlich und nehmt immer zu in dem Werk des Herrn, denn ihr wisst, dass eure Arbeit nicht vergeblich ist in dem Herrn.'),
('1 Korinther 16:13', 'Wachet, steht im Glauben, seid mutig und seid stark!'),
('1 Korinther 6:12', 'Alles ist mir erlaubt, aber nicht alles dient zum Guten. Alles ist mir erlaubt, aber nichts soll Macht haben über mich.'),
('1 Korinther 9:25', 'Jeder aber, der kämpft, enthält sich aller Dinge; jene nun, damit sie einen vergänglichen Kranz empfangen, wir aber einen unvergänglichen.'),
('1 Petrus 2:16', 'Als Freie und nicht als hättet ihr die Freiheit zum Deckmantel der Bosheit, sondern als Knechte Gottes.'),
('1 Petrus 5:10', 'Der Gott aller Gnade aber, der euch berufen hat zu seiner ewigen Herrlichkeit in Christus, der wird euch, die ihr eine kleine Zeit leidet, aufrichten, stärken, kräftigen, gründen.'),
('1 Thessalonicher 5:11', 'Darum tröstet euch untereinander und einer erbaue den andern, wie ihr auch tut.'),
('1 Timotheus 1:16', 'Aber darum ist mir Barmherzigkeit widerfahren, dass Christus Jesus an mir als Erstem alle Geduld erweise, zum Vorbild denen, die an ihn glauben sollten zum ewigen Leben.'),
('1 Timotheus 6:12', 'Kämpfe den guten Kampf des Glaubens; ergreife das ewige Leben, wozu du berufen bist und bekannt hast das gute Bekenntnis vor vielen Zeugen.'),
('2 Korinther 1:3-4', 'Gelobt sei Gott, der Vater unseres Herrn Jesus Christus, der Vater der Barmherzigkeit und Gott allen Trostes, der uns tröstet in aller unserer Trübsal, damit wir auch trösten können, die in allerlei Trübsal sind, mit dem Trost, mit dem wir selber getröstet werden von Gott.'),
('2 Korinther 1:5', 'Denn wie die Leiden Christi reichlich über uns kommen, so werden wir auch reichlich getröstet durch Christus.'),
('2 Korinther 3:17', 'Der Herr ist der Geist; wo aber der Geist des Herrn ist, da ist Freiheit.'),
('2 Korinther 4:16', 'Darum werden wir nicht müde; sondern wenn auch unser äußerer Mensch verfällt, so wird doch der innere von Tag zu Tag erneuert.'),
('2 Korinther 4:17', 'Denn unsre Bedrängnis, die zeitlich und leicht ist, schafft eine ewige und über alle Maßen gewichtige Herrlichkeit.'),
('2 Korinther 4:18', 'Uns, die wir nicht sehen auf das Sichtbare, sondern auf das Unsichtbare. Denn was sichtbar ist, das ist zeitlich; was aber unsichtbar ist, das ist ewig.'),
('2 Korinther 8:12', 'Denn wenn der gute Wille da ist, so ist jeder willkommen nach dem, was er hat, nicht nach dem, was er nicht hat.'),
('2 Timotheus 2:11', 'Das ist gewisslich wahr: Sind wir mit gestorben, so werden wir mit leben.'),
('5 Mose 31:8', 'Der HERR aber, der selber vor euch hergeht, der wird mit dir sein und wird die Hand nicht abtun und dich nicht verlassen. Fürchte dich nicht und erschrick nicht!'),
('Apostelgeschichte 13:38-39', 'So sei euch nun kundgetan, ihr Männer, liebe Brüder, dass euch durch ihn Vergebung der Sünden verkündigt wird; und in all dem, worin ihr durch das Gesetz des Mose nicht gerecht werden konntet, ist der gerecht gemacht, der an ihn glaubt.'),
('Galater 5:1', 'Zur Freiheit hat uns Christus befreit! So steht nun fest und lasst euch nicht wieder das Joch der Knechtschaft auflegen!'),
('Galater 5:13', 'Ihr aber, Brüder und Schwestern, seid zur Freiheit berufen. Allein seht zu, dass ihr durch die Freiheit nicht dem Fleisch Raum gebt, sondern durch die Liebe diene einer dem andern.'),
('Galater 6:8', 'Wer auf sein Fleisch sät, der wird von dem Fleisch das Verderben ernten; wer aber auf den Geist sät, der wird von dem Geist das ewige Leben ernten.'),
('Hebräer 10:24-25', 'Und lasst uns aufeinander achthaben und einander anspornen zur Liebe und zu guten Werken und nicht verlassen unsre Versammlung, wie einige zu tun pflegen, sondern einander ermahnen, und das umso mehr, als ihr seht, dass sich der Tag naht.'),
('Hebräer 5:9', 'Und da er vollendet war, ist er für alle, die ihm gehorsam sind, der Urheber der ewigen Seligkeit geworden.'),
('Hebräer 7:25', 'Daher kann er auch für immer selig machen, die durch ihn zu Gott kommen; denn er lebt für immer und bittet für sie.'),
('Hesekiel 18:32', 'Denn ich habe kein Gefallen am Tod dessen, der sterben müsste, spricht Gott der HERR. Darum bekehrt euch, so werdet ihr leben.'),
('Jakobus 1:25', 'Wer aber sich vertieft in das vollkommene Gesetz der Freiheit und dabei beharrt und ist nicht ein vergesslicher Hörer, sondern ein Täter, der wird selig sein in seinem Tun.'),
('Jesaja 40:31', 'Aber die auf den HERRN harren, kriegen neue Kraft, dass sie auffahren mit Flügeln wie Adler, dass sie laufen und nicht matt werden, dass sie wandeln und nicht müde werden.'),
('Jesaja 43:2', 'Wenn du durch Wasser gehst, will ich bei dir sein, und wenn du durch Ströme gehst, sollen sie dich nicht ersäufen. Wenn du ins Feuer gehst, wirst du nicht brennen, und die Flamme wird dich nicht versengen.'),
('Jesaja 43:4', 'Weil du teuer bist in meinen Augen und herrlich und weil ich dich lieb habe, gebe ich Menschen an deiner statt und Völker für dein Leben.'),
('Jesaja 51:12', 'Ich, ich bin euer Tröster! Wer bist du denn, dass du dich vor Menschen fürchtest, die doch sterben, und vor Menschenkindern, die wie Gras vergehen.'),
('Jesaja 61:1', 'Der Geist Gottes des Herrn ist auf mir, weil der Herr mich gesalbt hat. Er hat mich gesandt, den Elenden gute Botschaft zu bringen, die zerbrochenen Herzen zu verbinden, zu verkündigen den Gefangenen die Freiheit, den Gebundenen, dass sie frei und ledig sein sollen.'),
('Jesaja 7:14', 'Darum wird euch der Herr selbst ein Zeichen geben: Siehe, eine Jungfrau ist schwanger und wird einen Sohn gebären, den wird sie nennen Immanuel.'),
('Johannes 10:28-30', 'Und ich gebe ihnen das ewige Leben, und sie werden nimmermehr umkommen, und niemand wird sie aus meiner Hand reißen. Mein Vater, der mir sie gegeben hat, ist größer als alles, und niemand kann sie aus des Vaters Hand reißen. Ich und der Vater sind eins.'),
('Johannes 14:27', 'Frieden lasse ich euch, meinen Frieden gebe ich euch. Nicht gebe ich euch, wie die Welt gibt. Euer Herz erschrecke nicht und fürchte sich nicht.'),
('Johannes 16:33', 'Dies habe ich mit euch geredet, damit ihr in mir Frieden habt. In der Welt habt ihr Angst; aber seid getrost, ich habe die Welt überwunden.'),
('Johannes 17:3', 'Das ist aber das ewige Leben, dass sie dich, der du allein wahrer Gott bist, und den du gesandt hast, Jesus Christus, erkennen.'),
('Johannes 3:16', 'Denn also hat Gott die Welt geliebt, dass er seinen eingeborenen Sohn gab, auf dass alle, die an ihn glauben, nicht verloren werden, sondern das ewige Leben haben.'),
('Johannes 3:36', 'Wer an den Sohn glaubt, der hat das ewige Leben. Wer aber dem Sohn nicht gehorsam ist, der wird das Leben nicht sehen, sondern der Zorn Gottes bleibt über ihm.'),
('Johannes 4:14', 'Wer aber von dem Wasser trinkt, das ich ihm gebe, den wird in Ewigkeit nicht dürsten, sondern das Wasser, das ich ihm geben werde, das wird in ihm eine Quelle des Wassers werden, das in das ewige Leben quillt.'),
('Johannes 6:27', 'Müht euch nicht um Speise, die vergänglich ist, sondern um Speise, die da bleibt zum ewigen Leben. Dies wird euch der Menschensohn geben; denn auf ihm ist das Siegel Gottes des Vaters.'),
('Johannes 8:31-32', 'Da sprach nun Jesus zu den Juden, die an ihn glaubten: Wenn ihr bleiben werdet an meinem Wort, so seid ihr wahrhaftig meine Jünger und werdet die Wahrheit erkennen, und die Wahrheit wird euch frei machen.'),
('Johannes 8:36', 'Wenn euch nun der Sohn frei macht, so seid ihr wirklich frei.'),
('Josua 1:9', 'Habe ich dir nicht geboten: Sei getrost und unverzagt? Lass dir nicht grauen und entsetze dich nicht; denn der Herr, dein Gott, ist mit dir in allem, was du tun wirst.'),
('Lukas 10:20', 'Doch darüber freut euch nicht, dass euch die Geister untertan sind. Freut euch aber, dass eure Namen im Himmel geschrieben sind.'),
('Lukas 12:6-7', 'Verkauft man nicht fünf Sperlinge für zwei Groschen? Dennoch ist vor Gott nicht einer von ihnen vergessen. Auch sind die Haare auf eurem Haupt alle gezählt. Fürchtet euch nicht! Ihr seid kostbarer als viele Sperlinge.'),
('Lukas 2:11', 'Denn euch ist heute der Heiland geboren, welcher ist Christus, der Herr, in der Stadt Davids.'),
('Lukas 2:14', 'Ehre sei Gott in der Höhe und Friede auf Erden bei den Menschen seines Wohlgefallens.'),
('Lukas 2:20', 'Und die Hirten kehrten wieder um, priesen und lobten Gott für alles, was sie gehört und gesehen hatten, wie denn zu ihnen gesagt war.'),
('Lukas 2:4-5', 'Da machte sich auf auch Josef aus Galiläa, aus der Stadt Nazareth, in das judäische Land zur Stadt Davids, die da heißt Bethlehem, darum dass er von dem Hause und Geschlechte Davids war, auf dass er sich schätzen ließe mit Maria, seinem vertrauten Weibe; die war schwanger.'),
('Lukas 2:6-7', 'Und als sie daselbst waren, kam die Zeit, dass sie gebären sollte. Und sie gebar ihren ersten Sohn und wickelte ihn in Windeln und legte ihn in eine Krippe; denn sie hatten sonst keinen Raum in der Herberge.'),
('Lukas 4:18', 'Der Geist des Herrn ist auf mir, weil er mich gesalbt hat und gesandt, zu verkündigen das Evangelium den Armen, zu predigen den Gefangenen, dass sie frei sein sollen, und den Blinden, dass sie sehen sollen, und die Zerschlagenen zu entlassen in die Freiheit.'),
('Markus 10:29-30', 'Jesus sprach: Wahrlich, ich sage euch: Es ist niemand, der Haus oder Brüder oder Schwestern oder Mutter oder Vater oder Kinder oder Äcker verlässt um meinetwillen und um des Evangeliums willen, der nicht hundertfach empfange: jetzt in dieser Zeit Häuser und Brüder und Schwestern und Mütter und Kinder und Äcker mitten unter Verfolgungen – und in der kommenden Welt das ewige Leben.'),
('Matthäus 1:20', 'Als er noch so dachte, siehe, da erschien ihm ein Engel des Herrn im Traum und sprach: Josef, du Sohn Davids, fürchte dich nicht, Maria, deine Frau, zu dir zu nehmen; denn was sie empfangen hat, das ist von dem Heiligen Geist.'),
('Matthäus 1:21', 'Und sie wird einen Sohn gebären, dem sollst du den Namen Jesus geben, denn er wird sein Volk retten von ihren Sünden.'),
('Matthäus 1:22-23', 'Das ist aber alles geschehen, auf dass erfüllt würde, was der Herr durch den Propheten gesagt hat, der da spricht: »Siehe, eine Jungfrau wird schwanger sein und einen Sohn gebären, und sie werden ihm den Namen Immanuel geben«, das heißt übersetzt: Gott mit uns.'),
('Matthäus 10:39', 'Wer sein Leben findet, der wird\'s verlieren; und wer sein Leben verliert um meinetwillen, der wird\'s finden.'),
('Matthäus 11:28', 'Kommt her zu mir, alle, die ihr mühselig und beladen seid; ich will euch erquicken.'),
('Matthäus 7:13-14', 'Geht hinein durch die enge Pforte. Denn die Pforte ist weit und der Weg ist breit, der zur Verdammnis führt, und viele sind\'s, die auf ihm hineingehen. Wie eng ist die Pforte und wie schmal der Weg, der zum Leben führt, und wenige sind\'s, die ihn finden!'),
('Offenbarung 1:8', 'Ich bin das A und das O, spricht Gott der Herr, der da ist und der da war und der da kommt, der Allmächtige.'),
('Offenbarung 21:3-4', 'Und ich hörte eine große Stimme von dem Thron her, die sprach: Siehe da, die Hütte Gottes bei den Menschen! Und er wird bei ihnen wohnen, und sie werden seine Völker sein, und er selbst, Gott mit ihnen, wird ihr Gott sein; und Gott wird abwischen alle Tränen von ihren Augen, und der Tod wird nicht mehr sein, noch Leid noch Geschrei noch Schmerz wird mehr sein; denn das Erste ist vergangen.'),
('Offenbarung 7:16-17', 'Sie werden nicht mehr hungern noch dürsten; es wird auch nicht auf ihnen lasten die Sonne oder irgendeine Hitze; denn das Lamm mitten auf dem Thron wird sie weiden und leiten zu den Quellen lebendigen Wassers, und Gott wird abwischen alle Tränen von ihren Augen.'),
('Psalm 119:45', 'Und ich wandle in weitem Raum; denn ich suche deine Befehle.'),
('Psalm 121:1-2', 'Ich hebe meine Augen auf zu den Bergen. Woher kommt mir Hilfe? Meine Hilfe kommt vom HERRN, der Himmel und Erde gemacht hat.'),
('Psalm 139:23-24', 'Erforsche mich, Gott, und erkenne mein Herz; prüfe mich und erkenne, wie ich\'s meine. Und sieh, ob ich auf bösem Wege bin, und leite mich auf ewigem Wege.'),
('Psalm 145:1', 'Ich will dich erheben, mein Gott, du König, und deinen Namen loben immer und ewiglich.'),
('Psalm 23:4', 'Und ob ich schon wanderte im finstern Tal, fürchte ich kein Unglück; denn du bist bei mir, dein Stecken und Stab trösten mich.'),
('Psalm 31:25', 'Seid getrost und unverzagt alle, die ihr des HERRN harret!'),
('Psalm 32:8', 'Ich will dich unterweisen und dir den Weg zeigen, den du gehen sollst; ich will dich mit meinen Augen leiten.'),
('Psalm 34:20', 'Der Gerechte muss viel leiden, aber aus alledem hilft ihm der HERR.'),
('Psalm 37:28', 'Denn der HERR hat das Recht lieb und verlässt seine Heiligen nicht. Ewiglich werden sie bewahrt, aber das Geschlecht der Frevler wird ausgerottet.'),
('Psalm 68:7', 'Ein Gott, der die Einsamen nach Hause bringt, der die Gefangenen herausführt, dass es ihnen wohlgehe; aber die Abtrünnigen bleiben in dürrem Lande.'),
('Psalm 79:9', 'Hilf du uns, Gott, unser Helfer, um deines Namens Ehre willen! Errette uns und vergib uns unsre Sünden um deines Namens willen!'),
('Psalm 90:17', 'Und der Herr, unser Gott, sei uns freundlich und fördere das Werk unsrer Hände bei uns. Ja, das Werk unsrer Hände wollest du fördern!'),
('Psalm 97:10', 'Die ihr den HERRN liebet, hasset das Arge! Der Herr bewahrt die Seelen seiner Heiligen; aus der Hand der Frevler wird er sie erretten.'),
('Römer 15:2', 'Ein jeder von uns lebe so, dass er seinem Nächsten gefalle zum Guten und zur Erbauung.'),
('Römer 15:5', 'Der Gott aber der Geduld und des Trostes gebe euch, dass ihr einträchtig gesinnt seid untereinander, wie es Christus Jesus entspricht.'),
('Römer 5:21', 'Damit, wie die Sünde geherrscht hat durch den Tod, so auch die Gnade herrsche durch die Gerechtigkeit zum ewigen Leben durch Jesus Christus, unsern Herrn.'),
('Römer 6:15', 'Wie nun? Sollen wir sündigen, weil wir nicht unter dem Gesetz, sondern unter der Gnade sind? Das sei ferne!'),
('Römer 6:22', 'Nun aber, da ihr von der Sünde frei und Gottes Knechte geworden seid, habt ihr darin eure Frucht, dass ihr heilig werdet; das Ende aber ist das ewige Leben.'),
('Römer 6:23', 'Denn der Sünde Sold ist der Tod; die Gabe Gottes aber ist das ewige Leben in Christus Jesus, unserm Herrn.'),
('Römer 8:1-2', 'So gibt es nun keine Verdammnis für die, die in Christus Jesus sind. Denn das Gesetz des Geistes, der lebendig macht in Christus Jesus, hat dich frei gemacht von dem Gesetz der Sünde und des Todes.'),
('Römer 8:18', 'Denn ich bin überzeugt, dass dieser Zeit Leiden nicht ins Gewicht fallen gegenüber der Herrlichkeit, die an uns offenbart werden soll.'),
('Römer 8:31', 'Was wollen wir nun hierzu sagen? Ist Gott für uns, wer kann wider uns sein?'),
('Sprüche 14:23', 'Wo man arbeitet, da ist Gewinn; wo man aber nur mit Worten umgeht, da ist Mangel.'),
('Sprüche 19:16', 'Wer das Gebot bewahrt, der bewahrt sein Leben; wer aber auf seinen Weg nicht achtet, wird sterben.'),
('Sprüche 8:35', 'Wer mich findet, der findet das Leben und erlangt Wohlgefallen vom HERRN.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bookings`
--

CREATE TABLE `bookings` (
  `bookingId` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `total` decimal(8,3) NOT NULL,
  `donation` decimal(8,3) NOT NULL,
  `booking` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`articleId`),
  ADD UNIQUE KEY `articleId_2` (`articleId`),
  ADD KEY `articleId` (`articleId`);

--
-- Indizes für die Tabelle `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`basket_id`),
  ADD UNIQUE KEY `basket_id` (`basket_id`),
  ADD KEY `basket_id_2` (`basket_id`);

--
-- Indizes für die Tabelle `bible_verses`
--
ALTER TABLE `bible_verses`
  ADD PRIMARY KEY (`ref`),
  ADD KEY `ref` (`ref`),
  ADD KEY `ref_2` (`ref`);

--
-- Indizes für die Tabelle `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`bookingId`),
  ADD UNIQUE KEY `bookingId` (`bookingId`),
  ADD KEY `bookingId_2` (`bookingId`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `basket`
--
ALTER TABLE `basket`
  MODIFY `basket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `bookings`
--
ALTER TABLE `bookings`
  MODIFY `bookingId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
