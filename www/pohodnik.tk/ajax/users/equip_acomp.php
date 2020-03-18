<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$add_q = "";
if(isset($_GET['flag']) && in_array($_GET['flag'], array('is_group','is_musthave'))){
	$add_q = " AND `".$_GET['flag']."`=1 ";
}

$term = $mysqli->real_escape_string(trim($_GET['term']));
$res = array();
$q = $mysqli->query("SELECT `id`, `name`, `weight`, `value`, `is_musthave`, `is_group` FROM `user_equip` WHERE `name` LIKE('%{$term}%') {$add_q} LIMIT 10");
while($r = $q->fetch_assoc()){$res[] = $r; }
die(json_encode($res));