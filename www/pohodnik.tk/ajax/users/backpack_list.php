<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$res = array();
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT `id`, `id_user`, `name`, `weight`, `value`,`photo` FROM `user_backpacks` WHERE `id_user`={$id_user} ORDER BY value");
 
if($q && $q->num_rows>0){
	while($r = $q->fetch_assoc()){
		$res[] = $r;
	}
}
die(json_encode($res));