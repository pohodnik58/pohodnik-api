<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$res = array();
$id_user = $_COOKIE["user"];
$id = intval($_GET['id']);
$q = $mysqli->query("SELECT `id`, `id_user`, `name`, `weight`, `value`, `photo` FROM `user_backpacks` WHERE `id_user`={$id_user} AND id={$id} LIMIT 1");
die(json_encode($q->fetch_assoc()));