<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_set=intval($_POST['id_set']);
$id_equip=intval($_POST['id_equip']);
$id_user = $_COOKIE["user"];
$res = array();
if($mysqli->query("INSERT INTO user_equip_set_items SET id_set={$id_set}, id_equip={$id_equip}")){
	$res['success'] = true;
	$res['id'] = $mysqli->insert_id;
	$mysqli->query("UPDATE user_equip_sets SET date_update=NOW() WHERE id={$id_equip}");
} else {
	$res['error'] = $mysqli->error;
}
die(json_encode($res));