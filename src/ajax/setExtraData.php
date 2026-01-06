<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    parse_str($raw, $parsed);
    $extraJson = isset($parsed['extra']) ? $parsed['extra'] : null;
    if ($extraJson === null && isset($_POST['extra'])) {
        $extraJson = $_POST['extra'];
    }

    if ($extraJson !== null) {
        try {
            $extraArray = json_decode($extraJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON decode error: " . json_last_error_msg());
            }
            if (!is_array($extraArray)) {
                throw new Exception("Decoded extra is not an array");
            }

            $success = setExtraInBasket($extraArray);
            if ($success) {
                $response['success'] = true;
                $response['decoded'] = $extraArray;
            } else {
                throw new Exception("Failed to save extra data");
            }
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }
    } else {
        $response['error'] = "Missing extra parameter";
    }
} else {
    $response['error'] = "Invalid request method";
}

echo json_encode($response);
?>
