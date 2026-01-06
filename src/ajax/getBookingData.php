<?php
$root="..";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config.php");
require_once("$root/config/config_generic.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");

db_connect();

header('Content-Type: application/json');

// Get booking ID from request
if(isset($_GET['id'])) {
    $bookingId = intval($_GET['id']);
} else {
    echo json_encode(['error' => 'Booking ID not provided']);
    exit();
}

// Get booking data
$booking = getBooking($bookingId);

if(!$booking) {
    echo json_encode(['error' => 'Booking not found']);
    exit();
}

// Extract extra data if available
$teacher = "";
$class_name = "";
if (!empty($booking['extra'])) {
    $extraData = @unserialize($booking['extra']);
    if (is_array($extraData)) {
        $teacher = $extraData['leiter'] ?? "";
        $class_name = $extraData['schulklasse'] ?? "";
    }
}

// Map payment method
$payment_type = "bar"; // default
if ($booking['paymentMethod'] == 'twint') {
    $payment_type = "Twint";
} elseif ($booking['paymentMethod'] == 'invoice') {
    $payment_type = "EZS";
}

// Prepare response data
$response = [
    'booking_id' => $bookingId,
    'value' => floatval($booking['total']),
    'teacher' => $teacher,
    'class' => $class_name,
    'payment_type' => $payment_type
];

echo json_encode($response);
?>
