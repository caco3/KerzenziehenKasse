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
    $sql = "SELECT meta FROM basket_various LIMIT 1";
    $query_response = mysqli_query($db_link, $sql);
    if (!$query_response) {
        throw new Exception("DB query failed: " . mysqli_error($db_link));
    }
    $row = mysqli_fetch_assoc($query_response);
    mysqli_free_result($query_response);

    $metaData = [];
    if (!empty($row['meta'])) {
        $metaData = unserialize($row['meta']);
        if (!is_array($metaData)) {
            $metaData = [];
        }
    }

    $response['success'] = true;
    $response['data'] = $metaData;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
