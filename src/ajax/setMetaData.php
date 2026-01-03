<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

header('Content-Type: application/json');

$response = ['success' => false];

// Log raw POST for debugging
error_log("setMetaData raw POST: " . file_get_contents('php://input'));
error_log("setMetaData _POST: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    parse_str($raw, $parsed);
    $meta = isset($parsed['meta']) ? $parsed['meta'] : null;
    if ($meta === null && isset($_POST['meta'])) {
        $meta = $_POST['meta'];
    }

    if ($meta !== null) {
        try {
            // Expect JSON string; decode to array; then serialize for DB
            $decoded = json_decode($meta, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON decode error: " . json_last_error_msg());
            }
            if (!is_array($decoded)) {
                throw new Exception("Decoded meta is not an array");
            }

            $serialized = serialize($decoded);

            $sql = "UPDATE basket_various SET meta = ? LIMIT 1";
            $stmt = mysqli_prepare($db_link, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($db_link));
            }
            mysqli_stmt_bind_param($stmt, "s", $serialized);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);

            $response['success'] = true;
            $response['decoded'] = $decoded;
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }
    } else {
        $response['error'] = "Missing meta parameter";
    }
} else {
    $response['error'] = "Invalid request method";
}

echo json_encode($response);
?>
