<?php
include_once("../../blocks/db.php"); //подключение к БД
include_once("../../blocks/global.php"); //подключение к БД

$id= intval($_GET['id']);

$q = $mysqli->query("SELECT * FROM `positions` WHERE id={$id}");
$r = $q -> fetch_assoc();

die(out($r));