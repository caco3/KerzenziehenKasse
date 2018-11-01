<?php

$root="..";
require_once("$root/framework/credentials_check.php");
// 
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();


require_once("$root/framework/odtphp/src/Odf.php");

require_once("$root/framework/odtphp/src/Exceptions/OdfException.php");
require_once("$root/framework/odtphp/src/Exceptions/PhpZipProxyException.php");
require_once("$root/framework/odtphp/src/Exceptions/PhpZipProxyException.php");
require_once("$root/framework/odtphp/src/Exceptions/SegmentException.php");
require_once("$root/framework/odtphp/src/Segment.php");
require_once("$root/framework/odtphp/src/SegmentIterator.php");
require_once("$root/framework/odtphp/lib/pclzip.lib.php");
require_once("$root/framework/odtphp/src/Zip/ZipInterface.php");
require_once("$root/framework/odtphp/src/Zip/PclZipProxy.php");
require_once("$root/framework/odtphp/src/Zip/PhpZipProxy.php");

use Odtphp\Odf;


// if(isset($_GET['id'])){
//     $bookingId = $_GET['id'];
// }
// else{
//     $bookingId = 0;
// }
// 
// if($bookingId == ""){
//     $bookingId = 0;
// }

// $bookingId = bookingsGetLastId();


// print_r($_GET);
$bookingId = $_GET['id'];

// echo("booking ID: $bookingId");

$booking = getBooking($bookingId);

// echo("Last booking ID: $bookingId");
// echo("<pre>");
// print_r($booking);
// echo("</pre>");
// 
// exit();

// $donation = "CHF " . number_format(roundMoney(getDbTotal()), 2);
// $total = "CHF " . number_format(roundMoney(getDbTotal()), 2);


$odf = new Odf("$root/templates/receipt.odt");

$year = date("Y");
$date = date("d.m.Y");
$time = date("H:i:s");

$odf->setVars('year', $year);
$odf->setVars('date', $date);
$odf->setVars('time', $time);
$odf->setVars('bookingId', "$bookingId");
$odf->setVars('priceTotal', "CHF " . number_format($booking['total'], 2));




$articlesList = $odf->setSegment('articles');

foreach($booking['articles'] as $article) { // Add all articles
    $articlesList->articleTitle(strip_tags($article['text']));
    $articlesList->articleDetails($article['quantity'] . " " . $article['unit']);
    $articlesList->articleCost("CHF " . number_format($article['quantity'] * $article['price'], 2));
    $articlesList->merge();
}

if ($booking['donation'] != 0) { // Add donation to list
    $articlesList->articleTitle("Spende");
    $articlesList->articleDetails("");
    $articlesList->articleCost("CHF " . number_format($booking['donation'], 2));
    $articlesList->merge();
}


$odf->mergeSegment($articlesList);


// We export the file
$odf->exportAsAttachedFile("Kerzenziehen $year - Buchung $bookingId - $date.odt");
 
?>
