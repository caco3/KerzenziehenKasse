<? 
$root=".";
require_once("$root/framework/credentials_check.php");
require_once("$root/config/config.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$file = "Kerzenziehen-Datenbank - " . date_format(date_create(),"Y-m-d__H-i-s") . ".sql";

$database = MYSQL_DATABASE;
$user = MYSQL_USER;
$pass = MYSQL_PASSWORD;
$host = MYSQL_HOST;


$cmd = "mysqldump --extended-insert=FALSE --complete-insert=TRUE --user=$user --password=$pass --host=$host $database";

exec("$cmd 2>&1", $arr);
// print_r($arr);


$content = "";
foreach($arr as $line) {
//     echo "$line\n";
    $content .= "$line\n";
}


header("Content-Disposition: attachment; filename=\"$file\"");
header("Content-Type: application/text");
header("Content-Length: " . strlen($content));
   
echo($content);

exit(); 

?>
