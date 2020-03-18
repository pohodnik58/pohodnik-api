<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$id = intval($_GET['id']);
$res = array();
$q = $mysqli->query("SELECT 
	user_equip_set_items.id AS iid,	user_equip_set_items.is_check,
	user_equip.id,
	user_equip.id_user, 
	user_equip.name, 
	user_equip.weight, 
	user_equip.value,
	user_equip.is_musthave,
	user_equip.is_group
FROM user_equip_set_items 
LEFT JOIN user_equip ON(user_equip_set_items.id_equip = user_equip.id)
WHERE user_equip_set_items.id_set={$id}");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$res[] = $r;
}
die(json_encode($res));