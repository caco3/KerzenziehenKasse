<?php

$root="..";
define('DIR_CLASSES', "$root/framework/qrBill/");


use Sprain\SwissQrBill as QrBill;

require $root . '/framework/qrBill/vendor/autoload.php';


function generateQrCode($bookingId, $amount) {
    global $root;

    $qrBill = QrBill\QrBill::create();

    // Add creditor information
    // Who will receive the payment and to which bank account?
    $qrBill->setCreditor(
        QrBill\DataGroup\Element\CombinedAddress::create(
            'Viva Kirche Schweiz',
            'Kirche Neuwies Uster',
            '4126 Bettingen',
            'CH'
        ));

    $qrBill->setCreditorInformation(
        QrBill\DataGroup\Element\CreditorInformation::create(
            'CH0506888016123250007' // This is a classic iban. QR-IBANs will not be valid in this minmal setup.
        ));

    // Add payment amount information
    // The currency must be defined.
    $qrBill->setPaymentAmountInformation(
        QrBill\DataGroup\Element\PaymentAmountInformation::create(
            'CHF', $amount
        ));

    // Add payment reference
    // Explicitly define that no reference number will be used by setting TYPE_NON.
    $qrBill->setPaymentReference(
        QrBill\DataGroup\Element\PaymentReference::create(
            QrBill\DataGroup\Element\PaymentReference::TYPE_NON
        ));


    // Optionally, add some human-readable information about what the bill is for.
    $qrBill->setAdditionalInformation(
        QrBill\DataGroup\Element\AdditionalInformation::create(
            "Kerzenziehen $bookingId")
    );


    $qrBill->getQrCode()->writeFile("/tmp/qrBill_$bookingId.png");

}

?>
