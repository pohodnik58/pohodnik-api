<?php 
$host = 'mysql';
$conn = mysqli_connect($host, 'poh', 'poh');
if (!$conn) {
    exit('Connection failed: '.mysqli_connect_error().PHP_EOL);
}
 
echo 'Successful database connection!'.PHP_EOL;
 ?>