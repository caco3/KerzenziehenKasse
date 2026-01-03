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
    $metaData = getMetaFromBasket();
    if ($metaData === null) {
        throw new Exception("Failed to fetch meta data");
    }
    $response['success'] = true;
    $response['data'] = $metaData;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
