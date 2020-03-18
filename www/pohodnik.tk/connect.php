<?php 

$host = getenv('MYSQL_HOST');
$user = getenv('MYSQL_USER');
$psw = getenv('MYSQL_PASSWORD');
$db = getenv('MYSQL_DATABASE');

$host = 'mysql';
$conn = mysqli_connect($host, $user, $psw, $db);
if (!$conn) {
    exit('Connection failed: '.mysqli_connect_error().PHP_EOL);
}
 
echo "Successful database connection {$db}!".PHP_EOL;
 ?>