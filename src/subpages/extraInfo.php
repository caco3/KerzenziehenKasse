<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

$bookingId = getDbBookingId();
?>
<div style="text-align: left; padding: 5px; cursor: pointer;" onclick="editExtraDataClicked();" id="extraInfoLine">
	<small><em>Extra-Daten: <span id="extraSummaryText">Drücken zum Bearbeiten</span></em></small>
	<img src="images/receipt.png" height=30px style="float: right; margin-left: 10px;">
</div>
