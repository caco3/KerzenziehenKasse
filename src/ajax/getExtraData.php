<?
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

header('Content-Type: application/json');

$response = ['success' => false, 'data' => null];

try {
    $extraData = getExtraFromBasket();
    if ($extraData === null) {
        throw new Exception("Failed to fetch extra data");
    }
    $response['success'] = true;
    $response['data'] = $extraData;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
