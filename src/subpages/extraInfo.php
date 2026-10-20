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
	<img src="images/school.png" height=50px style="margin-right: 10px; margin-top: 5px; margin-bottom:0">
	<small style="position: relative; top: -18px;"><em> <span id="extraSummaryText">Angabe von Schulklasse, Leiter/in</span></em></small>
	<button type=button class=editButton title="Editieren" style="float:right"></button>
</div>
