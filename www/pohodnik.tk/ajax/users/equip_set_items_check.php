<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id=intval($_POST['id']);
$value=intval($_POST['value']);
$id_user = $_COOKIE["user"];
$res = array();

if($mysqli->query("UPDATE user_equip_set_items SET is_check={$value} WHERE id={$id}")){
	$res['success'] = true;
} else {
	$res['error'] = $mysqli->error;
}
die(json_encode($res));