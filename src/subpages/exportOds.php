<?
// Generates an ODS (OpenDocument Spreadsheet) export of all bookings of the
// current year which were paid with the given payment method.
// ODS is a zip archive; the "mimetype" file must be stored first, uncompressed.

$root="..";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config.php");
require_once("$root/config/config_generic.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

$year = date("Y");

$method = $_GET['method'] ?? 'invoice';
$methodNames = [
    'invoice' => 'Rechnungen',
    'twint'   => 'Twint',
];
if (!array_key_exists($method, $methodNames)) {
    die("Invalid payment method!");
}
$methodName = $methodNames[$method];
$includeExtra = ($method == 'invoice'); // group/lead columns only for invoices

// Collect all matching bookings of the current year
$filteredBookings = [];
$totalSum = 0;

$bookingDatesOfCurrentYear = getBookingDatesOfYear($year);
asort($bookingDatesOfCurrentYear);
foreach($bookingDatesOfCurrentYear as $date) {
    $bookingIds = getBookingIdsOfDate($date, false);
    foreach($bookingIds as $bookingId) {
        $booking = getBooking($bookingId);

        if ($booking['total'] == 0 || $booking['paymentMethod'] != $method) {
            continue;
        }

        $group = "";
        $lead = "";
        $extraData = @unserialize($booking['extra']);
        if (is_array($extraData)) {
            if (!empty($extraData['group'])) $group = $extraData['group'];
            if (!empty($extraData['lead']))  $lead  = $extraData['lead'];
        }

        $booking['bookingId'] = $bookingId;
        $booking['group'] = $group;
        $booking['lead'] = $lead;
        $filteredBookings[] = $booking;
        $totalSum += $booking['total'];
    }
}

// --- Build content.xml ---

function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function textCell($text, $style = "") {
    $styleAttr = $style ? " table:style-name=\"$style\"" : "";
    return "<table:table-cell$styleAttr office:value-type=\"string\"><text:p>" . esc($text) . "</text:p></table:table-cell>";
}

function numberCell($value, $style = "") {
    $styleAttr = $style ? " table:style-name=\"$style\"" : "";
    $rounded = roundTo10Rappen($value);
    return "<table:table-cell$styleAttr office:value-type=\"float\" office:value=\"$rounded\"><text:p>" . esc(prettyMoneyFormat($value)) . "</text:p></table:table-cell>";
}

$columnCount = $includeExtra ? 8 : 5;

$rows = "";

// Total of all matching bookings, above the list
$rows .= "<table:table-row>" . str_repeat("<table:table-cell/>", $columnCount - 2) . textCell("Total aller Buchungen via $methodName [CHF]:", "boldRight") . numberCell($totalSum, "bold") . "</table:table-row>";
$rows .= "<table:table-row>" . str_repeat("<table:table-cell/>", $columnCount) . "</table:table-row>";

// Header
$rows .= "<table:table-row>"
    . textCell("Buchungs-Nr", "bold")
    . textCell("Datum", "bold")
    . textCell("Zeit", "bold")
    . textCell("Total [CHF]", "bold")
    . textCell("Spenden [CHF]", "bold");
if ($includeExtra) {
    $rows .= textCell("Schule/Gruppe", "bold")
        . textCell("Gruppe", "bold")
        . textCell("Leiter/in", "bold");
}
$rows .= "</table:table-row>";

// Data rows
foreach($filteredBookings as $booking) {
    $isSchool = $booking['school'] == 1;
    $dateFormatted = date("d.m.Y", strtotime($booking['date']));
    $rows .= "<table:table-row>"
        . numberCell($booking['bookingId'])
        . textCell($dateFormatted)
        . textCell($booking['time'])
        . numberCell($booking["total"])
        . numberCell($booking["donation"]);
    if ($includeExtra) {
        $rows .= textCell($isSchool ? "Ja" : "")
            . textCell($booking['group'])
            . textCell($booking['lead']);
    }
    $rows .= "</table:table-row>";
}

$contentXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" office:version="1.2">
<office:automatic-styles>
<style:style style:name="bold" style:family="table-cell"><style:text-properties fo:font-weight="bold"/></style:style>
<style:style style:name="boldRight" style:family="table-cell"><style:text-properties fo:font-weight="bold"/><style:paragraph-properties fo:text-align="end"/></style:style>
</office:automatic-styles>
<office:body><office:spreadsheet>
<table:table table:name="$methodName $year">
$rows
</table:table>
</office:spreadsheet></office:body>
</office:document-content>
XML;

$manifestXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.2">
<manifest:file-entry manifest:full-path="/" manifest:media-type="application/vnd.oasis.opendocument.spreadsheet"/>
<manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>
</manifest:manifest>
XML;

// --- Write the ODS zip ---
$tmpFile = tempnam(sys_get_temp_dir(), "ods");
$zip = new ZipArchive();
$zip->open($tmpFile, ZipArchive::OVERWRITE);

// mimetype must be the first entry and stored uncompressed
$zip->addFromString("mimetype", "application/vnd.oasis.opendocument.spreadsheet");
$zip->setCompressionName("mimetype", ZipArchive::CM_STORE);

$zip->addFromString("META-INF/manifest.xml", $manifestXml);
$zip->addFromString("content.xml", $contentXml);
$zip->close();

$file = "Kerzenziehen-" . $methodName . "-" . $year . ".ods";

header("Content-Type: application/vnd.oasis.opendocument.spreadsheet");
header("Content-Disposition: attachment; filename=\"$file\"");
header("Content-Length: " . filesize($tmpFile));
readfile($tmpFile);
unlink($tmpFile);

exit();

?>
