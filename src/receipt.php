<?php

$root=".";
require_once("$root/framework/credentials_check.php");
// 
require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();


require_once('framework/odtphp/src/Odf.php');

require_once('framework/odtphp/src/Exceptions/OdfException.php');
require_once('framework/odtphp/src/Exceptions/PhpZipProxyException.php');
require_once('framework/odtphp/src/Exceptions/PhpZipProxyException.php');
require_once('framework/odtphp/src/Exceptions/SegmentException.php');
require_once('framework/odtphp/src/Segment.php');
require_once('framework/odtphp/src/SegmentIterator.php');
require_once('framework/odtphp/lib/pclzip.lib.php');
require_once('framework/odtphp/src/Zip/ZipInterface.php');
require_once('framework/odtphp/src/Zip/PclZipProxy.php');
require_once('framework/odtphp/src/Zip/PhpZipProxy.php');

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

$bookingId = bookingsGetLastId();


$price = "CHF " . number_format(roundMoney(getDbTotal()), 2);


$odf = new Odf("templates/receipt.odt");

$year = date("Y");
$date = date("d.m.Y");

$odf->setVars('year', $year);
$odf->setVars('date', $date);
$odf->setVars('bookingId', "$bookingId");
$odf->setVars('priceTotal', "$price");




$articlesList = $odf->setSegment('articles');

$data = getLastBookingSummary();

echo("<pre>");
print_r($data);

foreach($data as $article) {
//     print_r($article);

//     echo($article['name'] . ", " . "CHF " . number_format($article['price'], 2) . "<br>\n");
    
    $articlesList->articleTitle(strip_tags($article['name']));
    $articlesList->articleDetails($article['quantity'] . " " . $article['unit']);
    $articlesList->articleCost("CHF " . number_format($article['price'], 2));
    $articlesList->merge();
}
$odf->mergeSegment($articlesList);




// We export the file
// $odf->exportAsAttachedFile("Kerzenziehen $year - Buchung $bookingId - $date.odt");
 
?>
