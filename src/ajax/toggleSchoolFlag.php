<? 
$root="..";
require "$root/config/config.php";
require "$root/framework/db.php";

// Establish database connection
db_connect();

header('Content-Type: application/json');

// Get booking ID from request
$bookingId = $_POST['bookingId'] ?? '';

if (empty($bookingId)) {
    echo json_encode(['success' => false, 'error' => 'Booking ID missing']);
    exit;
}

// Validate booking ID
if (!is_numeric($bookingId)) {
    echo json_encode(['success' => false, 'error' => 'Invalid booking ID']);
    exit;
}

// Get current school flag status
$booking = getDbBooking($bookingId);
if (!$booking) {
    echo json_encode(['success' => false, 'error' => 'Booking not found']);
    exit;
}

// Toggle the school flag (0 to 1, 1 to 0)
$newSchoolFlag = $booking['school'] == 1 ? 0 : 1;

// Update the database
global $db_link;
$sql = "UPDATE `bookings` SET `school`='$newSchoolFlag' WHERE `bookingId`='$bookingId'";
$query_response = mysqli_query($db_link, $sql);

if (!$query_response) {
    echo json_encode(['success' => false, 'error' => 'Database update failed: ' . mysqli_error($db_link)]);
    exit;
}

echo json_encode([
    'success' => true, 
    'newSchoolFlag' => $newSchoolFlag,
    'message' => 'School flag ' . ($newSchoolFlag ? 'enabled' : 'disabled')
]);
?>
