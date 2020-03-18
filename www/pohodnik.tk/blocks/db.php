<?php
$host = getenv('MYSQL_HOST');
$user = getenv('MYSQL_USER');
$psw = getenv('MYSQL_PASSWORD');
$db = getenv('MYSQL_DATABASE');


$mysqli = new mysqli($host, $user, $psw, $db);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
		$mysqli -> query("SET CHARACTER SET `utf8`");
		$mysqli -> query("SET NAMES SET `utf8`");
		$mysqli -> query("SET SESSION collation_connection = 'utf8_general_ci'");
		$mysqli -> query("SET GLOBAL time_zone ='MSK'");
ini_set('date.timezone', 'Europe/Moscow');
date_default_timezone_set( 'Europe/Moscow' );
$time_offset = 0;//-14400;
?>