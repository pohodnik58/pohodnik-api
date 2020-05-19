<?php 
$host = getenv('MYSQL_HOST');
$user = getenv('MYSQL_USER');
$psw = getenv('MYSQL_PASSWORD');
$db = getenv('MYSQL_DATABASE');

$host = empty($host) ? 'localhost' : $host;
$user = empty($user) ? 'thdwdvqs_rukz' : $user;
$psw = empty($psw)? 'rukzrukz' : $psw;
$db = empty($db) ? 'thdwdvqs_rukz' : $db;

$conn = mysqli_connect($host, $user, $psw, $db);
if (!$conn) {	
    exit('Connection failed: '.mysqli_connect_error().PHP_EOL."h={$host}, u={$user}, p={$psw}, db={$db}");
}
 
echo "Successful database connection {$db}!".PHP_EOL;
 ?>