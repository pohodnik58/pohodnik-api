<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$tWH = isset($_GET['type'])?" AND type=".intval($_GET['type']):"";
$id_user = $_COOKIE["user"];
$z = "SELECT `id`, `id_user`, `name`, `comment`, `id_product`, `type` FROM `user_allergies` WHERE id_user={$id_user} {$tWH}";

$q = $mysqli->query($z);
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
$res = array();
while($r = $q->fetch_assoc()){
	$res[] = $r;
}

die(json_encode($res));