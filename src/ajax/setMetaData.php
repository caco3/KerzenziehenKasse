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
    $metaJson = isset($parsed['meta']) ? $parsed['meta'] : null;
    if ($metaJson === null && isset($_POST['meta'])) {
        $metaJson = $_POST['meta'];
    }

    if ($metaJson !== null) {
        try {
            $metaArray = json_decode($metaJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON decode error: " . json_last_error_msg());
            }
            if (!is_array($metaArray)) {
                throw new Exception("Decoded meta is not an array");
            }

            $success = setMetaInBasket($metaArray);
            if ($success) {
                $response['success'] = true;
                $response['decoded'] = $metaArray;
            } else {
                throw new Exception("Failed to save meta data");
            }
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
