<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id=intval($_POST['id']);
$id_user = $_COOKIE["user"];
$res = array();
if($mysqli->query("DELETE FROM user_equip_set_items WHERE id={$id}")){
	$res['success'] = true;
} else {
	$res['error'] = $mysqli->error;
}
die(json_encode($res));