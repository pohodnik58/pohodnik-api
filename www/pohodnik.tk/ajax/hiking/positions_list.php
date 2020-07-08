<?php
include_once("../../blocks/db.php"); //подключение к БД
include_once("../../blocks/global.php"); //подключение к БД
$result = array();

$q = $mysqli->query("SELECT * FROM `positions`");
while($r = $q -> fetch_assoc()) {
	$result[] = $r;
}

die(out($result));